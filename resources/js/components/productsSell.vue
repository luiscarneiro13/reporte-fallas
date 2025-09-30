<template>
  <div style="margin-top: -35px">
    <div class="card">
      <div class="card-header">
        <div class="row mb-3">
          <div class="col-md-6">
            <div>
              <label for="customer_id">Cliente</label>
              <a href="#" data-toggle="modal" class="small-box-footer" @click="handleAddCustomer">
                &nbsp;&nbsp;&nbsp;
                <i class="fas fa-search"></i>
              </a>
            </div>
            <div>
              <span v-if="customerSelected">
                {{ customerSelected.full_name }}
              </span>
            </div>
          </div>

          <div class="col-md-5">
            <div class="row">
              <div class="col-md-4 col-sm-12 text-right">Total a pagar en $:</div>
              <div class="col-md-8 col-sm-12 font-weight-bold">
                {{ subTotalSell }}
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 col-sm-12 text-right">Total a pagar en Bs:</div>
              <div class="col-md-8 col-sm-12 font-weight-bold">
                {{ subTotalSellBs }}
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 col-sm-12 text-right">Total productos:</div>
              <div class="col-md-8 col-sm-12 font-weight-bold">
                {{ totalItems }}
              </div>
            </div>
          </div>
          <div class="col-md-1">
            <button @click="handleAddMethodPayment" class="mt-1 btn-sm float-right btn-primary">Finalizar</button>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <a @click="service = 0" class="col-md-6 text-dark" style="cursor: pointer">
                <input type="radio" name="service" id="" value="0" v-model="service" />
                Productos
              </a>
              <!-- Descomentar esto para que funcione la parte de servicios -->
              <a @click="service = 1" class="col-md-6 text-dark" style="cursor: pointer">
                <input type="radio" name="service" id="" value="1" v-model="service" />
                Servicios
              </a>

              <!-- Comentar esto para que funcione la parte de servicios -->
              <!-- <a class="col-md-6 text-dark" style="cursor: pointer">
                <input disabled type="radio" name="service" />
                Servicios
              </a> -->
            </div>
          </div>
          <div class="col-md-6"></div>
        </div>
      </div>
    </div>
    <div class="card" v-if="showArticles">
      <div class="card-header">
        <div class="row">
          <div class="col-md-12">
            <div class="mb-2">
              <label for="brand_id">Artículos</label>
              <a href="#" data-toggle="modal" class="small-box-footer" @click="handleAddArticle">
                &nbsp;&nbsp;&nbsp;
                <i class="fas fa-search"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="row" style="width: 100%">
          <table width="100%" class="table table-sm table-responsive full-width-table">
            <thead class="thead-dark">
              <tr>
                <th style="width: 5%" class="text-center">#</th>
                <th>Artículo</th>
                <th>Descripción</th>
                <th>Marca</th>
                <th>Disp.</th>
                <th class="text-center" style="width: 7%">Cant.</th>
                <th class="text-center" style="width: 7%">P.U Bs</th>
                <th class="text-center" style="width: 7%">Sub Total</th>
                <th class="text-center" style="width: 7%"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(article, index) in selectedAllArticles" :key="article">
                <td>{{ index + 1 }}</td>
                <td>{{ article.name }}</td>
                <td>{{ article.type }}</td>
                <td>{{ article.brand }}</td>
                <td class="text-success font-weight-bolder">
                  {{ article.available_qty }}
                </td>
                <td class="text-center" style="width: 5%">
                  <input
                    type="text"
                    name="price"
                    :id="'article-' + index + '-' + article.id"
                    size="1"
                    :value="article.qty"
                    @keyup="changeQty($event, index, article)"
                    @blur="handleBlur($event, 'article-' + index + '-' + article.id)"
                  />
                </td>
                <td class="text-center" style="width: 5%">
                  {{ article.price * averageRate }}
                </td>
                <td class="text-center" style="width: 5%">
                  {{ article.sub_total * averageRate }}
                </td>
                <td class="text-center">
                  <a href="#" @click="deleteArticle(article)"><i class="fa fa-trash text-danger">&nbsp;</i></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="card" v-else>
      <div class="card-header">
        <div class="row">
          <div class="col-md-12">
            <div class="mb-2">
              <label for="brand_id">Servicios</label>
              <a href="#" data-toggle="modal" class="small-box-footer" @click="handleAddService">
                &nbsp;&nbsp;&nbsp;
                <i class="fas fa-search"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="row">
          <table class="table table-sm table-responsive" width="100%">
            <thead class="thead-dark">
              <tr>
                <th style="width: 5%" class="text-center">#</th>
                <th>Servicio</th>
                <th class="text-center" style="width: 7%">P.U Bs</th>
                <th class="text-center" style="width: 7%">Sub Total</th>
                <th class="text-center" style="width: 7%"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(service, index) in selectedAllServices" :key="service">
                <td>{{ index + 1 }}</td>
                <td>{{ service.name }}</td>
                <td class="text-center" style="width: 5%">
                  {{ service.price * averageRate }}
                </td>
                <td class="text-center" style="width: 5%">
                  {{ service.sub_total * averageRate }}
                </td>
                <td class="text-center">
                  <a href="#" @click="deleteService(service)"><i class="fa fa-trash text-danger">&nbsp;</i></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div>
    <searchCustomerVue @customerSelected="mountCustomer" :show="showSearchCustomer" @hideModalCustomer="hideModalCustomer" />
  </div>
  <searchArticleVue @articleSelected="mountArticle" :show="showSearchArticle" @hideModalArticle="hideModalArticle" />
  <searchServiceVue @serviceSelected="mountService" :show="showSearchService" @hideModalService="hideModalService" />
  <selectMethodPaymentVue
    :show="showSelectMethodPayment"
    :subTotalSell="subTotalSell"
    :subTotalSellBs="subTotalSellBs"
    :totalItems="totalItems"
    @paymentMethodSelected="mountPaymentMethod"
    :service="service"
    :customerSelected="customerSelected"
    :selectedAllArticles="selectedAllArticles"
    :selectedAllServices="selectedAllServices"
    @hideModalMethodPayment="hideModalMethodPayment"
    @windowReload="windowReload"
  />
</template>
<script>
import { roundUp } from "./functions";

import searchCustomerVue from "./searchCustomer.vue";
import searchArticleVue from "./searchArticle.vue";
import searchServiceVue from "./searchService.vue";
import selectMethodPaymentVue from "./selectMethodPayment.vue";
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";

function parseJson(data, defaultValue) {
  try {
    return JSON.parse(data);
  } catch (e) {
    return defaultValue;
  }
}

export default {
  components: {
    searchCustomerVue,
    VueSelect,
    searchArticleVue,
    selectMethodPaymentVue,
    searchServiceVue,
  },
  data() {
    return {
      dailyRate: window.dailyRate,
      averageRate: window.averageRate,
      tax: window.tax,
      name: "",
      customers: window.customers,
      articles: window.articles,
      services: window.services,
      selectedCustomer: null,
      selectedArticle: null,
      selectedAllArticles: parseJson(sessionStorage.getItem("articlesSelected"), null),
      selectedServices: null,
      selectedAllServices: parseJson(sessionStorage.getItem("servicesSelected"), null),
      err: null,
      showSearchCustomer: false,
      showSearchArticle: false,
      showSearchService: false,
      showSelectMethodPayment: false,
      customerSelected: parseJson(sessionStorage.getItem("customerSelected"), null),
      i: 0,
      service: 0,
      showArticles: true,
      showServices: false,
      showMethodPayment: false,
      subTotalSellBs: 0,
      subTotalSell: 0,
      totalItems: 0,
    };
  },
  mounted() {
    // this.resetSession();
    this.calculateSubTotalArticles();
  },
  watch: {
    service() {
      if (this.service == 1) {
        this.calculateSubTotalServices();
      } else {
        this.calculateSubTotalArticles();
      }
    },
  },
  computed: {
    showMountSearchCustomer() {
      return this.showSearchCustomer;
    },
  },
  methods: {
    resetSession() {
      sessionStorage.setItem("customerSelected", "");
      sessionStorage.setItem("articlesSelected", "");
      sessionStorage.setItem("servicesSelected", "");
    },
    calculateSubTotalServices() {
      this.showServices = true;
      this.showArticles = false;
      if (this.selectedAllServices && this.selectedAllServices.length) {
        const subTotalBs = this.selectedAllServices.reduce((acum, objeto) => acum + objeto.sub_total * this.averageRate, 0);
        this.subTotalSell = roundUp(subTotalBs / this.dailyRate, 2);
        this.subTotalSellBs = roundUp(subTotalBs, 2);
      } else {
        this.subTotalSell = 0;
        this.subTotalSellBs = 0;
      }
      this.totalItems = this.selectedAllServices ? this.selectedAllServices.length : 0;
    },
    calculateSubTotalArticles() {
      let totalSell = 0;
      let subTotalBs = 0;
      let totalItems = 0;
      this.showServices = false;
      this.showArticles = true;
      if (this.selectedAllArticles && this.selectedAllArticles.length) {
        subTotalBs = this.selectedAllArticles.reduce((acum, objeto) => acum + objeto.sub_total * this.averageRate, 0);
        totalSell = roundUp(subTotalBs / this.dailyRate, 2);
        totalItems = roundUp(this.selectedAllArticles.reduce((acum, objeto) => acum + objeto.qty, 0));
      } else {
        totalSell = 0;
        totalItems = 0;
      }
      this.totalItems = totalItems;
      this.subTotalSell = totalSell;
      this.subTotalSellBs = subTotalBs;
    },

    windowReload() {
      window.location.reload();
    },

    //Modal Method Payments
    // ******************************
    hideModalMethodPayment() {
      this.showSelectMethodPayment = false;
    },
    handleAddMethodPayment() {
      if (this.customerSelected && this.customerSelected.id) {
        if (this.selectedAllArticles && this.selectedAllArticles.length) {
          this.showSelectMethodPayment = true;
        } else if (this.selectedAllServices && this.selectedAllServices.length) {
          this.showSelectMethodPayment = true;
        } else {
          Swal.fire({
            title: "Agregue productos o servicios",
            icon: "error",
            // timer: 2000, // La alerta se cerrará automáticamente después de 3 segundos
            showConfirmButton: true,
          });
        }
      } else {
        Swal.fire({
          title: "Seleccione un Cliente",
          icon: "error",
          // timer: 2000, // La alerta se cerrará automáticamente después de 3 segundos
          showConfirmButton: true,
        });
      }
    },
    // ******************************

    handleBlur(event, reference) {
      if (!event.target.value.length) {
        $("#" + reference).focus();
      }
    },
    focus(reference) {
      this.$nextTick(() => {
        this.$refs[reference].focus();
      });
    },
    changeQty(event, index, currentArticle) {
      const inputValue = event.target.value;
      const lastChar = inputValue.slice(-1);

      if (isNaN(lastChar) && lastChar !== ".") {
        event.target.value = inputValue.slice(0, -1);
      } else {
        if (event.target.value.length) {
          let newQty = event.target.value;

          if (parseInt(newQty) > parseInt(currentArticle.available_qty)) {
            newQty = currentArticle.available_qty;
            Swal.fire({
              title: `Solo hay ${currentArticle.available_qty} disponibles`,
              //   text: "Cantidad no disponible",
              icon: "error",
              //   timer: 3000, // La alerta se cerrará automáticamente después de 3 segundos
              showConfirmButton: true,
            });
          } else {
            newQty = event.target.value;
          }
          const article = this.selectedAllArticles[index];
          const subTotal = newQty * Number(article.price);
          this.selectedAllArticles[index].qty = Number(newQty);
          this.selectedAllArticles[index].sub_total = roundUp(subTotal);
        } else {
          this.selectedAllArticles[index].qty = "";
          this.selectedAllArticles[index].sub_total = "";
        }
        this.calculateSubTotalArticles();
        sessionStorage.setItem("articlesSelected", JSON.stringify(this.selectedAllArticles));
      }
    },
    deleteArticle(article) {
      const newData = this.selectedAllArticles.filter((objeto) => objeto.id !== article.id);

      this.totalItems = this.totalItems - article.qty;
      this.selectedAllArticles = newData;
      let newSelectedArticles = JSON.stringify(newData);
      sessionStorage.setItem("articlesSelected", newSelectedArticles);
      this.calculateSubTotalArticles();
    },

    deleteService(service) {
      const newData = this.selectedAllServices.filter((objeto) => objeto.id !== service.id);

      this.totalItems = this.totalItems - service.qty;
      this.selectedAllServices = newData;
      let newSelectedServices = JSON.stringify(newData);
      sessionStorage.setItem("servicesSelected", newSelectedServices);
      this.calculateSubTotalServices();
    },

    //Modal Customers
    // ******************************
    hideModalCustomer() {
      this.showSearchCustomer = false;
    },

    handleAddCustomer() {
      this.showSearchCustomer = true;
    },
    // ******************************

    //Modal Articles
    // ******************************
    hideModalArticle() {
      this.showSearchArticle = false;
    },

    handleAddArticle() {
      $("#querySearchArticle").focus();
      this.showSearchArticle = true;
    },
    // ******************************

    //Modal Services
    // ******************************
    hideModalService() {
      this.showSearchService = false;
    },

    handleAddService() {
      //   $("#querySearchService").focus();
      this.showSearchService = true;
    },
    // ******************************

    mountArticles(data) {
      this.articles = data;
    },

    mountArticle(data) {
      let newData = [];

      if (this.selectedAllArticles && this.selectedAllArticles.length) {
        const index = this.selectedAllArticles.findIndex((item) => item.id === data.id);

        if (index == -1) {
          newData = this.selectedAllArticles;
          newData.push(data);
        } else {
          this.sumAddSubTotal(index);
          newData = this.selectedAllArticles;
        }
      } else {
        newData.push(data);
      }

      this.totalItems += data.qty;

      this.selectedAllArticles = newData;
      sessionStorage.setItem("articlesSelected", JSON.stringify(newData));
      this.calculateSubTotalArticles();
      this.hideModalArticle();
    },

    mountServices(data) {
      this.services = data;
    },

    mountService(data) {
      let newData = [];

      if (this.selectedAllServices && this.selectedAllServices.length) {
        const index = this.selectedAllServices.findIndex((item) => item.id === data.id);

        if (index == -1) {
          newData = this.selectedAllServices;
          newData.push(data);
        } else {
          this.sumAddSubTotal(index);
          newData = this.selectedAllServices;
        }
      } else {
        newData.push(data);
      }

      this.totalItems += data.qty;

      this.selectedAllServices = newData;
      let newSelectedServices = JSON.stringify(newData);
      sessionStorage.setItem("servicesSelected", newSelectedServices);
      this.calculateSubTotalServices();
      this.hideModalService();
    },

    sumAddSubTotal(index) {
      this.selectedAllArticles[index].qty += 1;
      const price = this.selectedAllArticles[index].price;
      const qty = this.selectedAllArticles[index].qty;
      this.selectedAllArticles[index].sub_total = roundUp(price * qty);
    },

    mountCustomer(data) {
      this.customerSelected = data;
      sessionStorage.setItem("customerSelected", JSON.stringify(data));
      this.hideModalCustomer();
    },
  },
};
</script>
