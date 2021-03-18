<template>
<div>
  <el-form
    v-loading="loading"
    :model="form"
    :rules="rules"
    size="mini"
    ref="form"
    label-width="100px">
    <el-form-item label="广告位名称" prop="name">
      <el-input v-model="form.name"></el-input>
    </el-form-item>
    
    <el-row>
      <el-col :span="12">
        <el-form-item label="所属媒体" prop="media_id">
          <el-select v-model="form.media_id" filterable :disabled="itemId>0">
            <el-option
              v-for="item in mediaList"
              :key="item.value"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="结算方式" prop="settlement">
          <el-select v-model="form.settlement" multiple clearable>
            <el-option
              v-for="item in settlementList"
              :key="item.value"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
    </el-row>
    <el-row>
      <el-col :span="12">
        <el-form-item label="推广方式" prop="promotion_mode" v-if="form.media_id==7">
          <el-select v-model="form.promotion_mode" clearable :disabled="itemId>0" >
            <el-option
              v-for="item in promotionModeList"
              :key="item.value"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="备注">
          <el-input v-model="form.remark"></el-input>
        </el-form-item>
      </el-col>
    </el-row>
    

    <el-form-item label="素材样式">
      <el-row v-for="(item, key) in form.stylesShow" :key="key">
        <el-col>
          <el-tag type="success" :hit="false" size="small">{{item.style_name}}</el-tag>
          <el-button type="text" class="el-icon-document"  @click="handleStyleShow(key, item)"></el-button>
          <el-button type="text" class="el-icon-edit-outline" @click="handleStyleEdit(key, item)"></el-button>
          <el-button type="text" class="el-icon-delete" @click="handleStyleDelete(key)"></el-button>
        </el-col>
      </el-row>
      <el-button @click="createStyle()">创建样式</el-button>
    </el-form-item>

    <el-form-item>
      <el-button @click="cancelForm">取消</el-button>
      <el-button type="primary" :disabled="submitDisabled" @click="submitForm">确认</el-button>
    </el-form-item>
  </el-form>

  <!--  创建样式弹框 -->
  <FormAdSpaceStyle
    v-if="isDisplayStyle"
    :isDisplay="isDisplayStyle"
    :curKey="curStyleKey"
    :curStyle="curStyleJson"
    :openType="openType"
    @transferStyleJson="transferStyleJson">
  </FormAdSpaceStyle>

</div>
</template>

<script>
import { editItem, storeItem, updateItem } from "@/js/api/adSpace";
import { renewObject } from "@/js/utils";
import { getBasicData } from "@/js/api/common";

import FormAdSpaceStyle from "./components/FormAdSpaceStyle";

export default {
  name: "gmp-ad-space-form",
  components: { FormAdSpaceStyle },
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    requestAction: {
      type: [String],
      required: true
    },
    mediaId: {
      default: null
    },
    itemId: {
      default: null
    }
  },
  data: function() {
    return {
      loading: true,
      isDisplayStyle: false,
      curStyleKey: null,
      curStyleJson: "",
      openType: "",

      settlementList: [],
      mediaList: [],
      promotionModeList:[
        {label:'打开页面',value:'open_page'},
        {label:'APP下载',value:'dl_app'}
      ],

      submitDisabled: false,
      initForm: {
        name: "",
        media_id: "",
        settlement: [],
        remark: "",
        promotion_mode:'',
        stylesShow: []
      },
      form: {},
      rules: {
        media_id: [{ required: true, message: "请选择媒体", trigger: "blur" }],
        name: [
          { required: true, message: "请输入广告位名称", trigger: "blur" }
        ],
        settlement: [
          { required: true, message: "请选择结算方式", trigger: "blur" }
        ],
        promotion_mode: [
          { required: true, message: "请选择推广方式", trigger: "blur" }
        ]
      }
    };
  },
  methods: {
    transferStyleJson: function(action, key, value) {
      if (action === "confirm") {
        // 检查名称是否为空
        if (!value.style_name) {
          this.$message("样式名称必填");
          return;
        }
        // 检查文字大小上下限
        let text_info = value.style_info.text;
        let flag = 0;
        text_info.forEach((item, key) => {
          if (
            isNaN(item.min_length) ||
            isNaN(item.max_length) ||
            item.min_length < 0 ||
            item.max_length < 0
          ) {
            this.$message("文字个数上下限必须为数字且大于零");
            flag = 1;
            return;
          }
          if (parseInt(item.min_length) > parseInt(item.max_length)) {
            this.$message("文字个数的上限必须大于下限");
            flag = 2;
            return;
          }
        });
        if (flag > 0) {
          return;
        }

        if (!value.enumerated_value) {
          value.enumerated_value = "";
        }

        if (key != null && key >= 0) {
          this.form.stylesShow.splice(key, 1, value);
        } else if (value.style_id && value.style_id > 0) {
          // 编辑 如果是编辑样式 删除原有样式 有id
          this.form.stylesShow.forEach((item, key) => {
            if (item.style_id == value.style_id) {
              this.form.stylesShow.splice(key, 1, value);
            }
          });
        } else {
          // 新增 回填名称到样式列表
          this.form.stylesShow.push(value);
        }
      }
      this.isDisplayStyle = false;
    },
    handleStyleEdit: function(key, item) {
      this.curStyleKey = key;
      this.curStyleJson = item;
      this.openType = "edit";
      this.isDisplayStyle = true;
    },
    handleStyleShow: function(key, item) {
      this.curStyleJson = item;
      this.openType = "show";
      this.isDisplayStyle = true;
    },
    handleStyleDelete: function(key) {
      this.$confirm("确定删除该样式吗?")
        .then(_ => {
          this.form.stylesShow.splice(key, 1);
        })
        .catch(_ => {});
    },
    reloadItem() {
      this.loading = true;
      this.$set(this.$data, "form", renewObject(this.initForm));
      // 加载编辑数据
      if (this.itemId) {
        editItem(this.itemId).then(response => {
          let it = response.data.it;
          it.settlement = it.settlement.split(",").filter(item => {
            return item.length > 0;
          });
          it.stylesShow = it.stylesShow.map(item => {
            try {
              item.style_info = JSON.parse(item.style_info);
            } catch (error) {
              item.style_info = {
                text: [],
                img: [],
                video: []
              };
            }
            return item;
          });
          this.form = Object.assign({}, this.form, it);
          this.loading = false;
        });
      } else {
        this.loading = false;
      }
    },
    cancelForm() {
      this.$emit("update:visible", false);
    },
    submitForm(btn) {
      this.$refs.form.validate(valid => {
        if (valid) {
          
          // 检查结算方式是否属于媒体
          let settlementArr = [];let flag=false;
          this.settlementList.map(function(n){
            settlementArr.push(n.value);
          });
          this.form.settlement.map(function(item){
            if(!settlementArr.includes(item)){
              flag=true;
              return;
            }
          });
          if(flag){
            this.$message('请重新选择结算方式');
            return;
          }
          // 提示不能修改所属媒体 推广方式
          if(this.requestAction === "create"){
            this.$confirm(
          "所属媒体和推广方式保存后不可修改，请仔细核实"
            )
              .then(_ => {
                let form = renewObject(this.form);
                this.submitDisabled = true;
                (this.requestAction === "edit"
                ? updateItem(this.itemId, this.form)
                : storeItem(this.form)
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
                setTimeout(function() {
                  location.reload();
                }, 2000);
              }
              this.submitDisabled = false;
            })
            .catch(error => {
              console.log("error", error);
              this.submitDisabled = false;
            });
              })
              .catch(_ => {});
          }else if(this.requestAction === "edit"){
            let form = renewObject(this.form);
            this.submitDisabled = true;
            (this.requestAction === "edit"
            ? updateItem(this.itemId, this.form)
            : storeItem(this.form)
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
                setTimeout(function() {
                  location.reload();
                }, 2000);
              }
              this.submitDisabled = false;
            })
            .catch(error => {
              console.log("error", error);
              this.submitDisabled = false;
            });
          }
          
            
          
          
        } else {
          this.$message({
            message: "请检查表单是否填写完整",
            center: true
          });
        }
      });
    },
    handlerItemClose: function() {
      this.dialogItemVisible = false;
    },
    createStyle() {
      this.curStyleKey = null;
      this.curStyleJson = {
        style_info: {
          text: [],
          img: [],
          video: []
        }
      };
      this.isDisplayStyle = true;
    }
  },
  watch: {
    visible: {
      handler: function(val) {
        if (val) {
          this.reloadItem();
        } else {
          this.$set(this.$data, "form", renewObject(this.initForm));
        }
      }
    },
    "form.media_id":function(val){
      if(val){
        getBasicData({
          mediaDetail: {media_id:val},
        })
          .then(response => {
            if(response.data.mediaDetail.settlement!=null){
              let settlementArr =  response.data.mediaDetail.settlement.split(',');
              // settlementArr.filter(function(n){return n.length>0});
              // this.form.settlement =settlementArr;
              var tempArr=[];
              settlementArr.map(function(n){
                if(n.length>0){
                  tempArr.push({"label":n,"value":n});
                }
              });

              this.settlementList = tempArr;
            }else{
              //  this.form.settlement =[];
              this.settlementList = [];
            }

          })
          .catch(error => {
            console.log(error);
          });
      }
    }
  },
  computed: {},
  created() {
    this.reloadItem();
    // 媒体跳转创建
    if (this.mediaId) {
      this.form.media_id = this.mediaId;
    }
    getBasicData({
      settlementList: {"media_id":this.mediaId},
      mediaList: {}
    })
      .then(response => {
        this.mediaList = response.data.mediaList;
        this.settlementList = response.data.settlementList;
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>
