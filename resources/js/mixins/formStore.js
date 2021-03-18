import {
  renewObject
} from "@/js/utils";

export default {
  props: {
    formName: {
      type: [String],
      required: true
    },
    requestAction: {
      type: [String],
      required: true
    },
    visible: {
      type: Boolean,
      default: false
    },
    itemId: {
      default: null
    }
  },
  data: function () {
    return {
      loading: false, // 当前 form 加载状态
      submitDisabled: false, // 禁止提交按钮
    };
  },
  computed: {
    formTitle: function () {
      let title = '';
      if (this.requestAction === 'show') {
        title = '查看';
      } else if (this.requestAction === 'edit') {
        title = '编辑';
      } else if (this.requestAction === 'create') {
        title = '创建';
      }
      return title + this.formName;
    }
  },
  methods: {
    // 取消 form
    cancelForm() {
      this.$emit("update:visible", false);
    },
    // 提交 form
    submitForm() {
      this.$refs.scatForm.validate(valid => {
        if (valid) {
          this.submitDisabled = true;
          (this.requestAction === "edit" ?
            this.updateItem(this.itemId, this.form) :
            this.storeItem(this.form)
          )
          .then(response => {
              if (response.data.result == "error") {
                this.$alert(response.data.message, "", {
                  confirmButtonText: "确定"
                });
              } else {
                this.$alert("保存成功", "", {
                  confirmButtonText: "确定",
                  callback: _ => {
                    location.reload();
                  }
                });
                setTimeout(function () {
                  location.reload();
                }, 2000);
              }
              this.submitDisabled = false;
            })
            .catch(error => {
              console.log("error", error);
              this.submitDisabled = false;
            });
        } else {
          this.$message({
            message: "请检查表单是否填写完整",
            center: true
          });
        }
      });
    },
  },
  watch: {
    itemId: {
      handler: function () {
        this.loading = true;
        this.$store.dispatch(this.state.getActionName("clear"));
        // 加载编辑数据
        if (this.itemId) {
          this.editItem(this.itemId).then(response => {
            this.$store.dispatch(this.state.getActionName("assign_form"), response.data.it);
            this.loading = false;
          });
        } else {
          this.loading = false;
        }
      },
      immediate: true
    }
  },
  created() {
    this.$store.dispatch(this.state.getActionName("clear"));
  }
};