<template>
  <div class="pl-2 pr-2 pb-3" style="height: 300px; overflow-y: scroll">
    <table
      class="table-bordered table-hover dataTable no-footer"
      style="width: 100%"
    >
      <thead>
        <tr class="bg-dark">
          <th widht="25%" class="pl-1">Cédula</th>
          <th widht="25%" class="pl-1">Nombre y Apellido</th>
          <th widht="25%" class="pl-1">Teléfono</th>
          <th widht="25%" class="pl-1">Dirección</th>
          <th widht="25%" class="pl-1">Email</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(customer, index) in items"
          :key="index"
          :class="{ active: index === activeRowIndex }"
          style="cursor: pointer"
          @dblclick="handleDoubleClick(index)"
        >
          <td>{{ customer.cedula }}</td>
          <td>{{ customer.name }}</td>
          <td>{{ customer.phone }}</td>
          <td>{{ customer.address }}</td>
          <td>{{ customer.email }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
export default {
  name: "GridCustomers",
  props: ["items"],
  data() {
    return {
      activeRowIndex: -1, // Teclas arriba y abajo
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
      if (this.items.length) {
        const itemSelected = this.items[index];
        if (itemSelected && itemSelected.id) {
          this.$emit("customerSelected", itemSelected);
        }
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
