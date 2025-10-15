<template>
  <div class="row">
    <input
      :ref="sRef"
      :id="sId"
      v-model="querySearch"
      type="text"
      class="form-control"
      @input="debounceInput"
      placeholder="Buscar por nombre"
    />
  </div>
</template>
<script>
export default {
  name: "Search",
  props: ["sRef", "sId", "focus"],
  data() {
    return {
      querySearch: "",
      nro: 0,
    };
  },
  mounted() {
    this.querySearch = "";
    this.debounceInput();
  },
  watch: {
    focus() {
      this.$nextTick(() => {
        const reference = this.sRef;
        this.$refs[reference].focus();
      });
    },
  },
  methods: {
    debounceInput() {
      this.$emit("debounceSearch", this.querySearch);
    },
  },
};
</script>
