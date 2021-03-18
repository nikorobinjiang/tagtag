<template>
    <el-row type="flex" justify="center">
        <el-col class="mt50 width350">
            <el-form :model="form" :action="form_action" aria-label="登录">
                <div class="row">
                    <label for="email" class="col-sm-4 col-form-label text-md-right">手机号/电子邮箱</label>

                    <div class="col-md-6">
                        <!-- <input id="email" type="email" class="" name="email" :value="old_email" required autofocus> -->
                        <el-input
                            placeholder="请输入邮箱"
                            v-model="form.email">
                            <i slot="prefix" class="el-input__icon el-icon-user"></i>
                        </el-input>
                        <span v-if="error_info && error_info.email" class="invalid-feedback" role="alert">
                            <strong>error_info.email</strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">密码</label>
                    <div class="col-md-6">
                        <!-- <input id="password" type="password" class="" name="password" required> -->
                        <el-input
                            show-password
                            v-model="form.password">
                            <i slot="prefix" class="el-input__icon el-icon-lock"></i>
                        </el-input>
                        <span v-if="error_info && error_info.password" class="invalid-feedback" role="alert">
                            <strong>{{ error_info.password }}</strong>
                        </span>
                    </div>
                </div>


                <el-form-item>
                    <x-button @click="submitForm">登录</x-button>
                    <a class="btn btn-link" :href="pw_request">
                        忘记密码?
                    </a>
                </el-form-item>
            </el-form>
        </el-col>
        
    </el-row>
    
</template>

<script>
export default {
    name: 'auth-form',
    components: {},
    props: {
        error_info: {
            type: Object,
            default: null
        },
        old_email: {
            type: String,
            default: null
        },
        form_action: {
            type: String,
            default: null
        },
        pw_request: {
            default: ''
        }

    },
    data: function() {
        return {
            form: {
                email: '',
                password:''
            }
        };
    },
    methods: {
        submitForm(btn) {
            // this.$refs.form.validate(valid => {
            //     if (valid) {
            //     this.submitDisabled = true;
            //     (this.requestAction === "edit"
            //         ? updateItem(this.itemId, omit(this.form, ["annex_files"]))
            //         : storeItem(omit(this.form, ["annex_files"]))
            //     )
            //         .then(response => {
            //         if (response.data.result == "error") {
            //             this.$alert(response.data.message, "", {
            //             confirmButtonText: "确定"
            //             });
            //         } else {
            //             this.$message("保存成功");

            //             this.$emit("submitSucc");
            //             this.$emit("update:visible", false);
            //         }
            //         this.submitDisabled = false;
            //         })
            //         .catch(error => {
            //         console.log("error", error);
            //         this.submitDisabled = false;
            //         });
            //     } else {
            //     this.$message({
            //         message: "请检查表单是否填写完整",
            //         type: "warning",
            //         center: true
            //     });
            //     }
            // });
        }
    },
    created() {
        console.log(this.error_info);
        this.form.email = this.old_email;
    }
}
</script>
<style scoped>
.mt50{margin-top: 100px;}
.width350{width: 350px;}
</style>
    