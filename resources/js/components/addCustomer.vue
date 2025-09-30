<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p class="text-bold mb-3">Agregar Cliente</p>
            </div>
            <div class="col-md-6"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12 mb-2">
              <input
                ref="inputCedula"
                type="text"
                class="form-control"
                name="cedula"
                id="cedula"
                placeholder="Cedula o rif"
                v-model="cedula"
                @keyup.enter="focusNextField('inputName')"
              />
              <ErrorValidation v-if="err" :err="err" item="cedula" />
            </div>
            <div class="col-md-12 mb-2">
              <input
                ref="inputName"
                type="text"
                class="form-control"
                name="name"
                id="name"
                placeholder="Nombre y Apellido o Razón social"
                v-model="name"
                @keyup.enter="focusNextField('inputPhone')"
              />
              <ErrorValidation v-if="err" :err="err" item="name" />
            </div>
            <div class="col-md-12 mb-2">
              <input
                ref="inputAddress"
                type="text"
                class="form-control"
                name="address"
                id="address"
                placeholder="Dirección"
                v-model="address"
                @keyup.enter="focusNextField('inputAddress')"
              />
              <ErrorValidation v-if="err" :err="err" item="address" />
            </div>
            <div class="col-md-12 mb-2">
              <input
                ref="inputPhone"
                type="text"
                class="form-control"
                name="phone"
                id="phone"
                placeholder="Teléfono"
                v-model="phone"
                @keyup.enter="focusNextField('inputEmail')"
              />
              <ErrorValidation v-if="err" :err="err" item="phone" />
            </div>
            <div class="col-md-12 mb-2">
              <input
                ref="inputEmail"
                type="text"
                class="form-control"
                name="email"
                id="email"
                placeholder="Correo electrónico"
                v-model="email"
                @keyup.enter="focusNextField('buttonAddCustomer')"
              />
              <ErrorValidation v-if="err" :err="err" item="email" />
            </div>
            <div class="col-md-12">
              <div class="row mt-3">
                <a
                  ref="buttonAddCustomer"
                  href="#"
                  class="btn-sm mr-3 btn-primary"
                  icon="fas fa-lg fa-save"
                  @click="buttonSubmitAddCustomer"
                >
                  <i class="fas fa-lg fa-save"></i> Guardar</a
                >
                <a
                  href="#"
                  class="btn-sm mr-3 btn-default"
                  icon="fas fa-lg fa-save"
                  @click="hideAddCustomer"
                >
                  Cancelar
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import ErrorValidation from "./errorValidation.vue";
import * as api from "./Api/CustomersApi";

export default {
  name: "AddCustomer",
  components: { ErrorValidation },
  data() {
    return {
      name: "",
      phone: "",
      address: "CABIMAS",
      email: "",
      err: null,
      cedula: "",
    };
  },
  watch: {
    //INICIO Modal
    show() {
      if (this.show) {
        $("#modalAddCustomer").modal("show");
      } else {
        $("#modalAddCustomer").modal("hide");
      }
      //FIN Modal
    },
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
      //   this.$refs.closeButton.click();

      const insert = {
        name: this.name,
        phone: this.phone,
        address: this.address,
        email: this.email,
        cedula: this.cedula ?? null,
        branch_id: window.branchId,
      };

      const { data, success } = await api.store(insert);
      console.log("Success: ", success);
      if (success) {
        this.$emit("customerSelected", data);
        location.reload();
      } else {
        this.err = data;
      }
    },
  },
};
</script>
