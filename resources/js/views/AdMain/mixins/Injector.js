export default {
  inject: {
    scatForm: {
      default: () => null
    },
    requestAction: {
      default: () => ''
    },
  },
  computed: {
    formItemDisabled: function () {
      if (this.scatForm) {
        return this.scatForm.requestAction === "show";
      } else {
        console.warn('can not find scat form.');
        return false;
      }
    }
  }
};