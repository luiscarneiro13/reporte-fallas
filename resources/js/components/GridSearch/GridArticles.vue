<template>
  <div class="pl-2 pr-2 pb-3" style="height: 300px; overflow-y: scroll">
    <table
      class="table-bordered table-hover dataTable no-footer"
      style="width: 100%"
    >
      <thead>
        <tr class="bg-dark">
          <th widht="25%" class="pl-1">Nombre</th>
          <th widht="25%" class="pl-1">Description</th>
          <th widht="25%" class="pl-1">Marca</th>
          <th widht="25%" class="pl-1">Tipo</th>
          <th widht="25%" class="pl-1">Precio</th>
          <th widht="25%" class="pl-1">Disponible</th>
          <th widht="25%" class="pl-1">Marcas</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(article, index) in items"
          :key="index"
          :class="{ active: index === activeRowIndex }"
          style="cursor: pointer"
          @dblclick="handleDoubleClick(index)"
        >
          <td>{{ article.name }}</td>
          <td>{{ article.description }}</td>
          <td>{{ article.brand }}</td>
          <td>{{ article.type }}</td>
          <td>Bs. {{ article.price * averageRate }}</td>
          <td>{{ article.available_qty }}</td>
          <td>
            <div v-if="article.model_vehicles.length">
              <a @click="viewInfo(article.model_vehicles)" href="#">Ver info</a>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
import { roundUp } from "../functions.js";
import Swal from "sweetalert2";

export default {
  name: "GridArticles",
  props: ["items"],
  data() {
    return {
      activeRowIndex: -1, // Teclas arriba y abajo
      averageRate: null,
      rate: window.rate,
    };
  },
  beforeUnmount() {
    // INICIO Teclas arriba y abajo
    document.removeEventListener("keydown", this.handleKeyDown);
    // FIN Teclas arriba y abajo
  },
  mounted() {
    this.averageRate = window.averageRate;
    // INICIO Teclas arriba y abajo
    document.addEventListener("keydown", this.handleKeyDown);
    // FIN Teclas arriba y abajo
  },
  methods: {
    showAlert(text) {
      Swal.fire({
        title: "Este producto aplica a:",
        text,
        confirmButtonText: "Aceptar",
      });
    },
    viewInfo(modelVehicles) {
      const models = modelVehicles.map((item) => item.name).join(", ");
      this.showAlert(models);
      //   alert(models);
    },

    handleDoubleClick(index) {
      this.selectRow(index);
    },

    selectRow(index) {
      const itemSelected = this.items[index];
      if (itemSelected && itemSelected.id) {
        //Aquí ya se selecciona, se envía el dato y se debe cerrar la pantalla
        itemSelected.qty = 1;
        itemSelected.sub_total = roundUp(
          itemSelected.price * itemSelected.qty,
          2
        );
        console.log("articleSelected", itemSelected);
        this.$emit("articleSelected", itemSelected);
      }
    },

    // INICIO Teclas arriba y abajo
    handleKeyDown(event) {
      if (event.key === "ArrowUp") {
        this.moveFocusUp();
      } else if (event.key === "ArrowDown") {
        this.moveFocusDown();
      } else if (event.key === "Enter") {
        this.selectRow(this.activeRowIndex);
      }
    },
    moveFocusUp() {
      if (this.activeRowIndex > 0) {
        this.activeRowIndex--;
      }
    },
    moveFocusDown() {
      if (this.activeRowIndex < this.items.length - 1) {
        this.activeRowIndex++;
      }
    },
    // FIN Teclas arriba y abajo
  },
};
</script>
