<template>
  <div
    :ref="mRef"
    :id="mId"
    class="modal"
    show="true"
    tabindex="-1"
    role="dialog"
    data-backdrop="static"
  >
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="row mb-3" v-if="!hideHeader">
            <div class="col-md-8">
              <h5 class="pl-2">
                <slot name="title"></slot>
              </h5>
            </div>
            <div class="col-md-3 text-right">
              <slot name="buttonRight"></slot>
            </div>
            <div class="col-md-1">
              <button
                ref="closeButton"
                type="button"
                class="close"
                @click="closeModal"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
          <slot name="body"></slot>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "Modal",
  props: ["mRef", "mId", "hideHeader"],
  methods: {
    closeModal() {
      this.$emit("closeModal");
    },
  },
};
</script>
