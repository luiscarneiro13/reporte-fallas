<template>
  <div class="pl-2 pr-2 pb-3" style="height: 300px; overflow-y: scroll">
    <table
      class="table-bordered table-hover dataTable no-footer"
      style="width: 100%"
    >
      <thead>
        <tr class="bg-dark">
          <th widht="25%" class="pl-1">Nombre</th>
          <th widht="25%" class="pl-1">Precio</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(service, index) in items"
          :key="index"
          :class="{ active: index === activeRowIndex }"
          style="cursor: pointer"
          @dblclick="handleDoubleClick(index)"
        >
          <td>{{ service.name }}</td>
          <td>Bs. {{ service.price * averageRate }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
import { roundUp } from "../functions.js";

export default {
  name: "GridServices",
  props: ["items"],
  data() {
    return {
      activeRowIndex: -1, // Teclas arriba y abajo
      averageRate: window.averageRate,
    };
  },
  beforeUnmount() {
    // INICIO Teclas arriba y abajo
    document.removeEventListener("keydown", this.handleKeyDown);
    // FIN Teclas arriba y abajo
  },
  mounted() {
    // INICIO Teclas arriba y abajo
    document.addEventListener("keydown", this.handleKeyDown);
    // FIN Teclas arriba y abajo
  },
  methods: {
    handleDoubleClick(index) {
      this.selectRow(index);
    },

    selectRow(index) {
      const itemSelected = this.items[index];
      if (itemSelected && itemSelected.id) {
      console.log("LUIS: ",itemSelected)
        //Aquí ya se selecciona, se envía el dato y se debe cerrar la pantalla
        itemSelected.qty = 1;
        itemSelected.sub_total = roundUp(
          itemSelected.price * itemSelected.qty,
          2
        );
        this.$emit("serviceSelected", itemSelected);
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
