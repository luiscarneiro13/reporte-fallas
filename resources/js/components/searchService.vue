<template>
  <div>
    <Modal
      mRef="refmodalSearchService"
      mId="modalSearchService"
      @closeModal="hideModalService"
    >
      <template v-slot:title> Seleccionar Servicio </template>
      <template v-slot:body>
        <div id="sectionSearchService">
          <div class="card">
            <div class="card-body">
              <Search
                sRef="refQuerySearchService"
                sId="querySearchService"
                @debounceSearch="debounceSearchService"
                :focus="handleFocusSearch"
              />
            </div>
            <GridServices
              v-if="show"
              :items="services"
              @serviceSelected="serviceSelected"
            />
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>
<script>
import axios from "axios";
import ErrorValidation from "./errorValidation.vue";
import { debounce } from "lodash";
import Modal from "./GridSearch/Modal.vue";
import Search from "./GridSearch/Search.vue";
import GridServices from "./GridSearch/GridServices.vue";

export default {
  components: { ErrorValidation, Modal, Search, GridServices },
  props: ["show"],
  data() {
    return {
      name: "",
      phone: "",
      email: "",
      err: null,
      cedula: "",
      services: window.services,
      querySearchService: "",
      branch_id: window.branchId,
      serviceSelectTemp: {},
      handleFocusSearch: false,
    };
  },
  watch: {
    //INICIO Modal
    show() {
      if (this.show) {
        this.querySearchService = "";
        this.services = window.services;
        this.handleFocusSearch = true;
        $("#modalSearchService").modal("show");
      } else {
        this.handleFocusSearch = false;
        $("#modalSearchService").modal("hide");
      }
      //FIN Modal
    },
  },
  methods: {
    serviceSelected(event) {
      this.$emit("serviceSelected", event);
    },

    hideModalService() {
      this.$emit("hideModalService");
    },

    debounceSearchService: debounce(function (event) {
      axios
        .get(
          "/api/admin-sucursal/servicios/index?query=" +
            event +
            "&branch_id=" +
            this.branch_id
        )
        .then((response) => {
          const { data, success } = response.data;
          if (success) {
            this.services = data;
          } else {
            this.err = data;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    }, 200), // 200 es el tiempo de espera en milisegundos

    addOptions(data) {
      const newOption = document.createElement("option");
      newOption.value = data.id.toString();
      newOption.text = data.name;
      const selectElement = document.querySelector("#service_id");
      selectElement.appendChild(newOption);
      newOption.selected = true;
    },
  },
};
</script>
<style>
.active {
  background-color: #007bff;
  color: white;
}
</style>
