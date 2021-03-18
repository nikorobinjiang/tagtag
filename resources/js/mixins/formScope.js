export default {
  props: {
    formScope: {
      type: [String],
      default: ''
    },
  },
  methods: {
    getFormScopedField(field) {
      return [this.formScope, field].filter(item => {
        return item.length > 0;
      }).join('.');
    }
  }
};