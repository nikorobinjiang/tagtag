<template>
    <el-form
            v-loading="loading"
            :model="form"
            :rules="rules"
            ref="DownSiteForm"
            size="mini"
            label-width="100px"
    >
        <el-form-item label="反劫持域名" prop="url">
            <el-input v-model="form.url"></el-input>

        </el-form-item>
        <el-form-item>
            <el-button @click="cancelForm">取消</el-button>
            <el-button type="primary" :disabled="submitDisabled" @click="submitForm">确认</el-button>
        </el-form-item>
    </el-form>

</template>
<script>
import { editDown, storeDown, updateDown } from "@/js/api/domain";
import { renewObject } from "@/js/utils";
import { getBasicData } from "@/js/api/common";

export default {
  name: "down-site-domain-form",
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    requestAction: {
      type: [String],
      required: true
    },
    itemId: {
      default: null
    }
  },
  data: function() {
    return {
      loading: false,
      submitDisabled: false,
      form: { url: "" },
      rules: {
        url: [{ required: true, message: "请输入域名", trigger: "blur" }]
      }
    };
  },
  methods: {
    cancelForm() {
      this.$emit("update:visible", false);
    },
    submitForm(btn) {
      this.$refs.DownSiteForm.validate(valid => {
        if (valid) {
          this.submitDisabled = true;
          (this.requestAction === "edit"
            ? updateDown(this.itemId, this.form)
            : storeDown(this.form)
          )
            .then(response => {
              if (response.data.result == "error") {
                this.$alert(response.data.message, "", {
                  confirmButtonText: "确定"
                });
              } else {
                this.$message("保存成功");
                 
                this.$emit("submitSucc");
                this.$emit("update:visible", false);
              }
              this.submitDisabled = false;
            })
            .catch(error => {
              console.log("error", error);
              this.submitDisabled = false;
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    }
  },
  watch: {
    visible: {
      handler: function() {
        this.loading = true;
        this.$set(this.$data, "form", renewObject(this.initForm));
        // 加载编辑数据
        if (this.itemId) {
          editDown(this.itemId).then(response => {
            this.form = Object.assign({}, this.form, response.data.it);
            this.loading = false;
          });
        } else {
          this.loading = false;
        }
      },
      immediate: true
    }
  },
  computed: {},
  mounted() {}
};
</script>