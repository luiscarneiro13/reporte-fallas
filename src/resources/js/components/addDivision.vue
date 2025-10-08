<template>
  <div id="modalAddDivision" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar división</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-12 mb-2">
              <label for="name">Nombre de la división</label>
              <input ref="inputName" type="text" class="form-control" name="name" id="name" v-model="name" @keyup.enter="focusNextField('inputName')" />
              <ErrorValidation v-if="err" :err="err" item="name" />
            </div>

            <div class="col-md-12 mb-2">
              <label for="description">Descripción</label>
              <input ref="inputDescription" type="text" class="form-control" name="description" id="description" v-model="description" @keyup.enter="focusNextField('inputDescription')" />
              <ErrorValidation v-if="err" :err="err" item="description" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" :disabled="isSubmitting" class="btn btn-primary" @click="buttonSubmitAddDivision">
            {{ isSubmitting ? "Agregando..." : "Agregar" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ErrorValidation from "./errorValidation.vue";
import * as api from "./Api/GlobalApi";

export default {
  name: "AddDivision",
  components: { ErrorValidation },
  props: ["branch_id"],
  data() {
    return {
      name: "",
      email: "",
      phone: "",
      address: "",
      err: null,
      isSubmitting: false,
    };
  },

  methods: {
    hideAddDivision() {
      this.$emit("closeModal");
    },

    focusNextField(nextField) {
      if (nextField) {
        this.$refs[nextField].focus();
      }
    },

    async buttonSubmitAddDivision() {
      this.isSubmitting = true;

      const insert = {
        name: this.name,
        description: this.description,
        branch_id: window.branchId,
      };

      const url = "/api/v1/admin/divisiones/store";

      const { data, success } = await api.store(url, insert);

      if (success) {
        this.err = null;
        this.name = "";
        this.description = "";

        $("#modalAddDivision").trigger("divisionAdded", [data]);
      } else {
        this.err = data;
      }

      this.isSubmitting = false;
    },

    // addOptions(data) {
    //   const newOption = document.createElement("option");
    //   newOption.value = data.id.toString();
    //   newOption.text = data.name;
    //   const selectElement = document.querySelector("#brand_id");
    //   selectElement.appendChild(newOption);
    //   newOption.selected = true;
    // },
  },
};
</script>

