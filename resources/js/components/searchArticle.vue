<template>
  <div>
    <Modal
      mRef="refmodalSearchArticle"
      mId="modalSearchArticle"
      @closeModal="hideModalArticle"
    >
      <template v-slot:title> Seleccionar Artículo / Producto </template>
      <template v-slot:body>
        <div id="sectionSearchArticle">
          <div class="card">
            <div class="card-body">
              <Search
                sRef="refQuerySearchArticle"
                sId="querySearchArticle"
                @debounceSearch="debounceSearchArticle"
                :focus="handleFocusSearch"
              />
            </div>
            <GridArticles
              v-if="show"
              :items="articles"
              @articleSelected="articleSelected"
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
import GridArticles from "./GridSearch/GridArticles.vue";

export default {
  components: { ErrorValidation, Modal, Search, GridArticles },
  props: ["show"],
  data() {
    return {
      name: "",
      phone: "",
      email: "",
      err: null,
      cedula: "",
      articles: window.articles,
      querySearchArticle: "",
      branch_id: window.branchId,
      articleSelectTemp: {},
      handleFocusSearch: false,
    };
  },
  watch: {
    //INICIO Modal
    show() {
      if (this.show) {
        this.querySearchArticle = "";
        this.articles = window.articles;
        this.handleFocusSearch = true;
        $("#modalSearchArticle").modal("show");
      } else {
        this.handleFocusSearch = false;
        $("#modalSearchArticle").modal("hide");
      }
      //FIN Modal
    },
  },
  methods: {
    articleSelected(event) {
      this.$emit("articleSelected", event);
    },

    hideModalArticle() {
      this.$emit("hideModalArticle");
    },

    debounceSearchArticle: debounce(function (event) {
      axios
        .get(
          "/api/admin-sucursal/productos/index?query=" +
            event +
            "&branch_id=" +
            this.branch_id
        )
        .then((response) => {
          const { data, success } = response.data;
          if (success) {
            this.articles = data;
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
</style>
