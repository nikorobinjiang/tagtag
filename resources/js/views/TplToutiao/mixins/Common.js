export default {
  computed: {
    formTitle: function () {
      if (this.requestAction === 'show') {
        return '查看';
      } else if (this.requestAction === 'edit') {
        return '编辑';
      } else if (this.requestAction === 'create') {
        return '创建'
      } else {
        return '';
      }
    }
  }
};