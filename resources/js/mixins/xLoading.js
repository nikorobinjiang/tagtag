export default {
  props: {
    xLoading: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    xloading: {
      get: function () {
        return this.xLoading;
      },
      set: function (val, oldVal) {
        if (val !== oldVal) {
          this.$emit("update:xLoading", val);
        }
      }
    }
  },
};