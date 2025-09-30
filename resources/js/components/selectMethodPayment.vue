<template>
  <div>
    <Modal
      mRef="refmodalMethodPayment"
      mId="modalMethodPayment"
      @closeModal="hideModalMethodPayment"
    >
      <template v-slot:title>
        {{ this.printing ? "Imprimiendo" : "Seleccionar método de pago" }}
      </template>
      <template v-slot:body>
        <div
          v-if="printing"
          class="d-flex align-items-center justify-content-center"
        >
          <div class="spinner"></div>
        </div>
        <div v-else id="sectionMethodPayment">
          <div class="card">
            <div class="card-body">
              <!-- <div class="row">
                <div
                  v-for="method in methodPayments"
                  :key="method"
                  class="col-md-4"
                >
                  <label>
                    <input
                      type="radio"
                      v-model="method_payment_id"
                      id="`${method.id}_radio`"
                      :value="method.id"
                    />
                    {{ method.name }}
                  </label>
                </div>
              </div>
              <hr /> -->
              <div class="row">
                {{
                  customerSelected
                    ? "Cliente: " + customerSelected.full_name
                    : ""
                }}
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="row font-weight-bold">
                    <span>
                      {{ labelSell }}
                    </span>
                  </div>

                  <div v-if="subTotalSell" class="row">
                    Total a pagar:
                    <span class="font-weight-bold"
                      >&nbsp;${{ subTotalSell }}</span
                    >
                  </div>
                  <div v-if="subTotalSellBs" class="row">
                    Total a pagar:
                    <span class="font-weight-bold">
                      &nbsp;Bs {{ subTotalSellBs }}
                    </span>
                  </div>
                </div>
              </div>
              <div v-if="subTotalSell" class="row pt-3 float-right">
                <div>
                  <button
                    @click="apiPost('notaDespacho')"
                    class="mt-1 btn-sm btn-default mr-3"
                  >
                    <i class="fas fa-file-alt">&nbsp;</i>
                    Nota de Despacho
                  </button>
                </div>
                <div>
                  <!-- <button
                    @click="apiPost('factura')"
                    class="mt-1 btn-sm float-right btn-primary"
                  >
                    <i class="fas fa-file-invoice-dollar">&nbsp;</i>
                    Factura
                  </button> -->
                </div>
              </div>
              <!-- <div v-else>
                <div class="row">No hay items seleccionados</div>
              </div> -->
              <!-- Service: {{ service }} -->
            </div>
          </div>
        </div>
        <!-- {{ selectedAllArticles }} -->
      </template>
    </Modal>
  </div>
</template>
<script>
import ErrorValidation from "./errorValidation.vue";
import Modal from "./GridSearch/Modal.vue";
import Search from "./GridSearch/Search.vue";
import GridArticles from "./GridSearch/GridArticles.vue";

export default {
  components: { ErrorValidation, Modal, Search, GridArticles },
  props: [
    "show",
    "subTotalSell",
    "subTotalSellBs",
    "totalItems",
    "service",
    "customerSelected",
    "selectedAllArticles",
    "selectedAllServices",
  ],
  data() {
    return {
      name: "",
      phone: "",
      email: "",
      err: null,
      cedula: "",
      articlesSelected: "",
      servicesSelected: "",
      methodPayments: window.methodPayments,
      queryMethodPayment: "",
      branch_id: window.branchId,
      articleSelectTemp: {},
    //   method_payment_id: 0,
      showContent: false,
      labelSell: "",
      printing: false,
      dailyRate: window.dailyRate,
      averageRate: window.averageRate,
    };
  },
  created() {
    // if (this.methodPayments && this.methodPayments.length) {
    //   this.methodPayments.map((item) => {
    //     if (item.name == "EFECTIVO") {
    //       this.method_payment_id = item.id;
    //     }

    //     if (item.name == "PUNTO") {
    //       this.method_payment_id = item.id;
    //     }
    //   });
    //   this.changeLabel();
    // }
    // if (this.customerSelected) {
    //   this.invoice.name = this.customerSelected.name;
    //   this.invoice.rif = this.customerSelected.cedula;
    //   this.invoice.address = this.customerSelected.address;
    // }
  },
  watch: {
    //INICIO Modal
    service() {
      this.changeLabel();
    },
    show() {
      if (this.show) {
        this.queryMethodPayment = "";
        this.methodPayments = window.methodPayments;
        $("#modalMethodPayment .modal-dialog")
          .removeClass("modal-xl")
          .addClass("modal-md");

        $("#modalMethodPayment").modal("show");
      } else {
        $("#modalMethodPayment").modal("hide");
      }
      //FIN Modal
    },
  },
  methods: {
    async apiPost(invoiceMethod) {
      this.printing = true;
      const invoice = {
        name: this.customerSelected.name,
        rif: this.customerSelected.cedula,
        address: this.customerSelected.address,
      };
      const params = {
        customer_id: this.customerSelected.id,
        // method_payment_id: this.method_payment_id,
        branch_id: window.branchId,
        rate: dailyRate,
        average_rate: window.averageRate,
        tax: window.tax,
        total: this.subTotalSell,
        total_bs: this.subTotalSellBs,
        service: this.service,
        customerSelected: this.customerSelected,
        selectedAllArticles: this.selectedAllArticles,
        selectedAllServices: this.selectedAllServices,
        totalItems: this.totalItems,
        invoiceMethod,
        invoice,
      };
      axios
        .post("/api/vender", params)
        .then((response) => {
          const { data, success } = response.data;
          console.log("Respuesta del endpoint api/vender: ", data);
          if (success) {
            const url = window.impresora;
            console.log("Lo que regresa el endpoint imporesora: ", data);
            axios.post(url, { data: data }).then((resp) => {});
          } else {
            this.err = data;
          }

          setTimeout(() => {
            this.cleanSession();
            this.$emit("hideModalMethodPayment", false);
            this.$emit("windowReload");
          }, 2000);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    cleanSession() {
      sessionStorage.setItem("customerSelected", "");
      sessionStorage.setItem("articlesSelected", "");
      sessionStorage.setItem("servicesSelected", "");
    },
    changeLabel() {
      if (this.service) {
        this.labelSell = "Pagar servicios";
      } else {
        this.labelSell = "Pagar productos";
      }
    },
    articleSelected(event) {
      this.$emit("articleSelected", event);
    },
    hideModalMethodPayment() {
      this.$emit("hideModalMethodPayment");
    },
    addOptions(data) {
      const newOption = document.createElement("option");
      newOption.value = data.id.toString();
      newOption.text = data.name;
      const selectElement = document.querySelector("#article_id");
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
.spinner {
  border: 5px solid #f2f2f2;
  border-bottom: 5px solid #000000;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spinner 2s linear infinite;
}

@keyframes spinner {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>
