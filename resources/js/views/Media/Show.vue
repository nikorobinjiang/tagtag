<template>
    <el-form
    v-loading="loading"
    :model="form"
    :rules="rules"
    size="mini"
    ref="form"
    label-width="100px">
      <el-row>
        <el-col :span=12>
            <el-form-item label="媒体名称" prop="media_name">
        <el-input v-model="form.media_name" :disabled="true"></el-input>
        </el-form-item>
        </el-col>
        <el-col :span=12>
             <el-form-item label="后台地址" prop="manage_url">
        <el-input v-model="form.manage_url" :disabled="true"></el-input>
        </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span=12>
            <el-form-item label="key" prop="media_key">
        <el-input v-model="form.media_key" :disabled="true"></el-input>
        </el-form-item>
        </el-col>
        <el-col :span=12>
        <el-form-item label="结算方式" prop="settlement">
          <el-tag v-for="item in form.settlement" :key="item" size="mini" class="mr8">{{item}}</el-tag>
        </el-form-item>
        </el-col>
      </el-row>
      <el-row>
          <el-col>
            <el-tabs v-model="activeName" @tab-click="handleClick">
              <el-tab-pane label="广告位" name="adpos">
                  <Adposition :media-id="this.itemId"></Adposition>
              </el-tab-pane>
              <el-tab-pane label="媒体账号" name="promote">
                  <Promote :media-id="this.itemId"></Promote>
              </el-tab-pane>
                
            </el-tabs>
          </el-col>
      </el-row>
    </el-form>
</template>
<script>
import { renewObject } from "@/js/utils";
import { editItem } from "@/js/api/media";
import  Adposition  from "./components/Adposition";
import  Promote  from "./components/Promote";


export default {
    name:'DetailForm',
    components:{Adposition,Promote},
    props:{
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
    data:function(){
      return {
        loading:false,
        activeName:'adpos',
        initForm: {
          media_name: "",
          manage_url: "",
          settlement: [],
          media_key: ""
        },
        form: {},
        
        rules: {
            media_name: [
            { required: true, message: "请输入媒体名称", trigger: "blur" }
            ]
        }
      }
    },
    methods:{
       handleClick(tab, event) {
        // console.log(tab, event);
      },
      reloadList(page) {
        this.loading = true;
        if (page === undefined) {
            page = this.list.current_page;
        }
      fetchList(
        Object.assign({}, this.search, {
          page: page
        })
      )
        .then(response => {
          this.list = response.data.list;
          if (response.data.ids) {
            this.ids = response.data.ids;
          }
          if (response.data.excel_url) {
            this.excel_url = response.data.excel_url;
          }
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
      },
      handlePagerSizeChange(val) {
        console.log(`每页 ${val} 条`);
      }
    },
    watch:{
      itemId: {
        handler: function() {
            this.loading = true;
            this.$set(this.$data, "form", renewObject(this.initForm));
            // 加载编辑数据
            if (this.itemId) {
            editItem(this.itemId).then(response => {
                let it = response.data.it;
                
                this.form = Object.assign({}, this.form, it);
                this.loading = false;
            });
            } else {
                this.loading = false;
            }
        },
        immediate: true
      }
    },
    computed:{},
    created(){
    },
    mounted(){
      this.$set(this.$data, "form", renewObject(this.initForm));

    }
}
</script>
<style>
.right{float:right;margin-right: 10%}
.mr8{margin-right: 8px}
</style>
