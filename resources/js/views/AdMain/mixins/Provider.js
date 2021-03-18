export default {
  provide() {
    return {
      scatForm: this,
      requestAction: this.requestAction,
    }
  },
  computed: {
    formItemDisabled: function () {
      return this.requestAction === "show";
    },
    formTitle: function () {
      if (this.requestAction === 'show') {
        return '查看广告';
      } else if (this.requestAction === 'edit') {
        return '编辑广告';
      } else if (this.requestAction === 'create') {
        return '创建广告'
      } else {
        return '';
      }
    }
  }
};