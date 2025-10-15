<template>
  <div id="modalAddCustomer" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-12 mb-2">
              <label for="name">Nombre</label>
              <input ref="inputName" type="text" class="form-control" name="name" id="name" v-model="name" @keyup.enter="focusNextField('inputName')" />
              <ErrorValidation v-if="err" :err="err" item="name" />
            </div>

            <div class="col-md-12 mb-2">
              <label for="email">Email</label>
              <input ref="inputEmail" type="text" class="form-control" name="email" id="email" v-model="email" @keyup.enter="focusNextField('inputEmail')" />
              <ErrorValidation v-if="err" :err="err" item="email" />
            </div>

            <div class="col-md-12 mb-2">
              <label for="phone">Teléfono</label>
              <input ref="inputPhone" type="text" class="form-control" name="phone" id="phone" v-model="phone" @keyup.enter="focusNextField('inputPhone')" />
              <ErrorValidation v-if="err" :err="err" item="phone" />
            </div>

            <div class="col-md-12 mb-2">
              <label for="address">Dirección</label>
              <input ref="inputAddress" type="text" class="form-control" name="address" id="address" v-model="address" @keyup.enter="focusNextField('inputAddress')" />
              <ErrorValidation v-if="err" :err="err" item="address" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" :disabled="isSubmitting" class="btn btn-primary" @click="buttonSubmitAddCustomer">
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
  name: "AddCustomer",
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
    hideAddCustomer() {
      this.$emit("closeModal");
    },

    focusNextField(nextField) {
      if (nextField) {
        this.$refs[nextField].focus();
      }
    },

    async buttonSubmitAddCustomer() {
      this.isSubmitting = true;

      const insert = {
        name: this.name,
        email: this.email,
        phone: this.phone,
        address: this.address,
        branch_id: window.branchId,
      };

      const url = "/api/v1/admin/clientes/store";

      const { data, success } = await api.store(url, insert);

      if (success) {
        this.err = null;
        this.name = "";
        this.email = "";
        this.phone = "";
        this.address = "";

        $("#modalAddCustomer").trigger("customerAdded", [data]);
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

