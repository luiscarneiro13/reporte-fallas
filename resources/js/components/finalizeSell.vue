<template>
  <div>
    <div class="row">
      <div class="col-md-6">
        <h5>
          <button v-if="!(sale.paid == 0 && cancel_sale == 0)" type="button" class="btn btn-sm btn-default mr-2" @click="reprint">
            <span v-if="isLoadingReprint" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span v-if="!isLoadingReprint"><i class="fas fa-print"></i></span>
          </button>
          {{ labelTitle }}
          {{ sale.uuid }}
        </h5>
      </div>
      <div class="col-md-6">
        <button v-if="sale.paid == 0 && !sale.cancel_sale" type="button" class="btn btn-sm btn-primary float-right" @click="openModalFinalize">Finalizar venta</button>
      </div>
    </div>
    <div id="modalFinalizeSell" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Pagar Bs.
              <span class="font-weight-bold">
                {{ sale.total_bs }}
              </span>
              &nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp; Pagar
              <span class="font-weight-bold">$ {{ sale.total_dolares }}</span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row mb-2">
              <div class="col-12">
                <label for="method_payment_id">Método de pago</label>
                <select @change="checkPaymentMethod" v-model="method_payment_id" id="mySelect" class="form-control" :disabled="isLoading ? true : false">
                  <option v-for="(option, index) in methodPayments" :key="index" :value="option.id">
                    {{ option.name }}
                  </option>
                </select>
              </div>
            </div>
            <div v-if="method_payment_id == 9" class="row mb-2 mt-4 ml-4">
              <div v-for="(item, index) in paymentMixedLabel" :key="index" class="col-12">
                <div class="row">
                  <div class="col-6">
                    <label>{{ item.name }}</label>
                  </div>
                  <div class="col-6">
                    <input size="5" :placeholder="item.currency" class="ml-1" type="text" v-model="paymentMixed[item.slug]" />
                    {{ item.currency }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-primary" @click="finalizeSell" :disabled="isLoading">
              <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              <span v-if="!isLoading">Finalizar venta</span>
            </button>
          </div>
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
      method_payment_id: window.sale.method_payment_id ?? null,
      methodPayments: window.methodPayments,
      sale: window.sale,
      labelTitle: "Nro. Factura",
      isLoading: false,
      invoice: false,
      type: null,
      back_url: window.back_url,
      isLoadingReprint: false,
      paymentMixedLabel: [],
      paymentMixed: {
        bolivares_efectivo: "",
        dolares_efectivo: "",
        pago_movil: "",
        biopago: "",
        punto_venta_venezuela: "",
        punto_venta_banesco: "",
      },
    };
  },
  mounted() {
    // Inicializa method_payment_id con el id del primer elemento del array
    if (this.methodPayments.length) {
      this.method_payment_id = this.methodPayments[0].id;

      this.paymentMixedLabel = this.methodPayments.filter((item) => item.name != "MIXTO");
    }
    if (this.sale.delivery_notes && this.sale.delivery_notes.length) {
      if (this.sale.paid == 0) {
        //Nota de despacho
        this.labelTitle = "Nro. de Nota de Despacho:";
      } else {
        //Nota de entrega
        this.labelTitle = "Nro. de Entrega: ";
        this.type = "";
      }
      this.type = "notaEntrega";
    }

    if (this.sale.invoices && this.sale.invoices.length) {
      this.labelTitle = "Nro. de Factura: ";
      this.type = "invoice";
    }
  },
  methods: {
    openModalFinalize() {
      $("#modalFinalizeSell").modal("show");
    },
    async reprint() {
      this.isLoadingReprint = true;
      try {
        // Lógica para finalizar la venta
        await this.apiReprint();
      } catch (error) {
        console.error(error);
      } finally {
        // this.isLoading = false;
      }
    },
    async apiReprint() {
      const url = window.impresora;
      axios.post(url, { data: this.sale }).then((resp) => {});
      setTimeout(() => {
        window.location.reload();
      }, 2000);
    },
    async finalizeSell() {
      this.isLoading = true;
      try {
        // Lógica para finalizar la venta
        await this.apiPost();
      } catch (error) {
        console.error(error);
      } finally {
        // this.isLoading = false;
      }
    },
    async apiPost() {
      const params = {
        method_payment_id: this.method_payment_id,
        paid: 1,
      };

      for (let key in this.paymentMixed) {
        if (this.paymentMixed[key]) {
          params.paymentMixed = this.paymentMixed;
        }
      }

      axios
        .post("/api/finalizar/" + this.sale.id, params)
        .then((response) => {
          const { data, success } = response.data;
          if (success) {
            const url = window.impresora;
            axios.post(url, { data: data }).then((resp) => {});
          } else {
            this.err = data;
          }

          setTimeout(() => {
            window.location.reload();
          }, 2000);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    checkPaymentMethod() {
      if (this.method_payment_id != 9) {
        this.paymentMixed = {
          bolivares_efectivo: "",
          dolares_efectivo: "",
          pago_movil: "",
          biopago: "",
          punto_venta_venezuela: "",
          punto_venta_banesco: "",
        };
      }
    },
  },
};
</script>
