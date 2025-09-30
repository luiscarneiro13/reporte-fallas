<template>
  <div id="modalAddModelVehicle" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar modelo de Vehículo</h5>
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input
            type="text"
            class="form-control"
            name="name"
            id="name"
            placeholder="Ej.: Ford Explorer 2003"
            v-model="name"
          />
          <small @if="err" class="text-danger">{{ err }}</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" @click="addModelVehicle">
            Agregar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  props: ["branch_id"],
  data() {
    return {
      name: "",
      err: null,
    };
  },

  methods: {
    addModelVehicle() {
      axios
        .post("/api/admin-sucursal/modelovehiculos/store", {
          name: this.name,
          branch_id: window.branchId,
        })
        .then((response) => {
          const { data, success } = response.data;
          if (success) {
            this.addOptions(data);
            this.err = null;
            this.name = "";
            $("#modalAddModelVehicle").modal("hide");
          } else {
            this.err = data;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    addOptions(data) {
      const newOption = document.createElement("option");
      newOption.value = data.id.toString();
      newOption.text = data.name;
      const selectElement = document.querySelector("#modelVehicles");
      selectElement.appendChild(newOption);
      newOption.selected = true;
    },
  },
};
</script>

