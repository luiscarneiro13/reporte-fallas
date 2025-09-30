<template>
  <div>
    <Modal
      mRef="refModalSearchCustomer"
      mId="modalSearchCustomer"
      @closeModal="hideModalCustomer"
    >
      <template v-slot:title> Seleccionar Cliente </template>
      <template v-slot:buttonRight>
        <a @click="showAddCustomer" href="#" class="small-box-footer">
          &nbsp;<i class="fas fa-plus-circle"> </i> Nuevo Cliente
        </a>
      </template>
      <template v-slot:body>
        <div>
          <div class="card">
            <div class="card-body">
              <Search
                sRef="refQuerySearchCustomer"
                sId="querySearchCustomer"
                @debounceSearch="debounceSearchCustomer"
                :focus="handleFocusSearch"
              />
            </div>
            <GridCustomers
              v-if="show"
              :items="customers"
              @customerSelected="customerSelected"
            />
          </div>
        </div>
      </template>
    </Modal>
    <Modal mRef="refModalAddCustomer" mId="modalAddCustomer" hideHeader="true">
      <template v-slot:title> Agregar Cliente </template>
      <template v-slot:body>
        <AddCustomer
          @customerSelected="customerSelected"
          @closeModal="hideModalAddCustomer"
        />
      </template>
    </Modal>
  </div>
</template>
<script>
import { debounce } from "lodash";
import Modal from "./GridSearch/Modal.vue";
import Search from "./GridSearch/Search.vue";
import GridCustomers from "./GridSearch/GridCustomers.vue";
import * as api from "./Api/CustomersApi.js";
import AddCustomer from "./addCustomer.vue";

export default {
  components: { Modal, Search, GridCustomers, AddCustomer },
  props: ["show"],
  data() {
    return {
      customers: window.customers,
      querySearchCustomer: "",
      branch_id: window.branchId,
      activeRowIndex: -1, // Teclas arriba y abajo
      customerSelectTemp: {},
      handleFocusSearch: false,
    };
  },
  watch: {
    //INICIO Modal search customer
    show() {
      if (this.show) {
        this.querySearchCustomer = "";
        this.customers = window.customers;
        this.handleFocusSearch = true;
        $("#modalSearchCustomer").modal("show");
      } else {
        this.handleFocusSearch = false;
        $("#modalSearchCustomer").modal("hide");
      }
      //FIN Modal search customer
    },
  },
  methods: {
    customerSelected(event) {
        console.log(event)
      this.$emit("customerSelected", event);
    },

    hideModalCustomer() {
      this.$emit("hideModalCustomer");
    },

    hideModalAddCustomer() {
      $("#modalAddCustomer").modal("hide");
      $("#modalSearchCustomer").modal("show");
    },

    debounceSearchCustomer: debounce(async function (event) {
      const { data, success } = await api.index(event);
      this.customers = data;
    }, 200), // 200 es el tiempo de espera en milisegundos

    showAddCustomer() {
      $("#modalAddCustomer .modal-dialog")
        .removeClass("modal-xl")
        .addClass("modal-md");
      $("#modalAddCustomer").modal("show");
      $("#modalSearchCustomer").modal("hide");
    },

    addOptions(data) {
      const newOption = document.createElement("option");
      newOption.value = data.id.toString();
      newOption.text = data.name;
      const selectElement = document.querySelector("#customer_id");
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
