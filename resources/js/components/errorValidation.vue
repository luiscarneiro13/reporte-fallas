<template>
  <div v-if="err">
    <small v-if="err[item]" class="text-danger">{{ err[item][0] }}</small>
  </div>
</template>
<script>
export default {
  props: ["err", "item"],
};
</script>
