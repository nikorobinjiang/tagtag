<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>游戏素材</el-breadcrumb-item>
          <el-breadcrumb-item>图片素材</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    
    <el-row class="row-search">
      <el-form :inline="true" ref="" :model="search" label-width="80px" size="mini">
        <el-form-item label="游戏名称">
          <el-select v-model="search.game_id" clearable filterable placeholder="请选择游戏名称">
            <el-option
              v-for="(item, key) in gameList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="设计师">
          <el-select v-model="search.designer_id" clearable filterable placeholder="请选择设计师">
            <el-option
              v-for="(item, key) in designerList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="素材名称">
            <el-input v-model="search.search_name"></el-input>
        </el-form-item>
        <el-form-item label="标签">
            <el-input v-model="search.search_tags"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="reloadList()">查询</el-button>
        </el-form-item>
      </el-form>
    </el-row>
    <el-row class="row-actions">
      <el-col>
        <el-button type="primary" size="mini" @click="handlerItem('create')">新增图片素材</el-button>
      </el-col>
    </el-row>
    <el-row v-loading="loading">
    <el-row :gutter="20">
      <el-col :span="6" style=" margin-bottom: 1.5rem;" v-for="(item,key) in list.data" :key="key" >
          <scat-card size="small" :file-name="item.name" :info="['图片数量:'+(item.annex_pictures?item.annex_pictures.length:0),'游戏:'+item.game_name,'设计师:'+item.designer_name,'上传时间:'+item.add_time]">
              <template slot="operateIcons">
                  <el-button class="el-icon-edit-outline" type="text" size="medium" @click="handlerItem('edit',item)"></el-button>
                  
                  <el-button class="el-icon-download" type="text" size="medium" @click="handlerItem('download',item)"></el-button>
                  
                  <el-button class="el-icon-delete" type="text" size="medium" @click="handleItemDelete(item.material_id)"></el-button>
              </template>
              <template slot="content">
                <el-carousel :autoplay="false" arrow="always" height="13rem" style="width:100%" indicator-position="none">
                  <el-carousel-item v-for="picture_item in item.annex_pictures" :key="picture_item.annex_id">
                    <img :src="picture_item.file_url" :alt="picture_item.file_name" v-preview="{file_type: 'image'}"/>
                  </el-carousel-item>
                </el-carousel>
              </template>
          </scat-card>
      </el-col>
    </el-row>
      <el-row>
        <el-pagination
          @size-change="handlePagerSizeChange"
          @current-change="reloadList"
          :current-page.sync="list.current_page"
          :page-sizes="[8]"
          :page-size="list.per_page"
          layout="total, sizes, prev, pager, next, jumper"
          :total="list.total">
        </el-pagination>
      </el-row>
    </el-row>

    <!-- 创建、修改 -->
    <el-dialog
      :visible.sync="dialogItemVisible"
      :title="dialogItemTitle"
      :before-close="handlerItemClose">
      <Form
        :visible.sync="dialogItemVisible"
        :requestAction="dialogItemRequestAction"
        :itemId="dialogItemId"
        @submitSucc="reloadList()">
      </Form>
    </el-dialog>

  </div>
</template>

<script>
import { getBasicData } from "@/js/api/common";
import {
  createItem,
  fetchList,
  destroyItem,
  downloadItem
} from "@/js/api/materialPicture";
import Form from "./Form";
import ScatCard from "@/js/components/ScatCard/index";

export default {
  mixins: [],
  name: "gmp-material-picture-index",
  components: { Form, ScatCard },
  props: {},
  data: function() {
    return {
      // 创建修改框
      dialogItemTitle: "",
      dialogItemRequestAction: "",
      dialogItemId: null,
      dialogItemVisible: false,

      loading: true,
      urlCreate: createItem(),
      dataColsBoxVisible: false,
      gameList: [],
      designerList: [],
      search: {
        game_id: null,
        designer_id: null,
        search_name: null,
        search_tags: null
      },
      list: {
        current_page: 1,
        data: [],
        per_page: 8,
        total: 0
      }
    };
  },
  methods: {
    // 创建修改框
    handlerItem: function(action, item) {
      this.dialogItemRequestAction = action;
      if (action === "create") {
        this.dialogItemTitle = "创建图片素材";
        this.dialogItemId = null;
        this.dialogItemVisible = true;
      } else if (action === "edit") {
        this.dialogItemTitle = "编辑图片素材";
        this.dialogItemId = item.material_id;
        this.dialogItemVisible = true;
      } else if (action === "download") {
        // 下载素材
        let _data = new Array();
        item.annex_pictures.map(pic_item => {
          let pic_data_item = {};
          pic_data_item.id = pic_item.annex_id;
          pic_data_item.url = pic_item.file_url;
          _data.push(pic_data_item);
        });
        let senddata = {};
        senddata.data = _data;
        senddata.m_name = item.name;
        downloadItem(senddata)
          .then(response => {
            if (response.data.result == "error") {
              this.$alert(response.data.message, "", {
                confirmButtonText: "确定"
              });
            } else {
              location.href = response.data.download_url;
            }
          })
          .catch(error => {
            console.log("error", error);
          });
      }
    },
    handlerItemClose: function() {
      this.dialogItemVisible = false;
    },
    // 删除素材
    handleItemDelete(id) {
      this.$confirm("确定删除该组素材吗?")
        .then(_ => {
          destroyItem(id)
            .then(response => {
              if (response.data.result == "error") {
                this.$alert(response.data.message, "", {
                  confirmButtonText: "确定"
                });
              } else {
                this.$alert("素材删除成功", "", {
                  confirmButtonText: "确定",
                  callback: _ => {
                    this.reloadList();
                  }
                });
                setTimeout(function() {
                  this.reloadList();
                }, 2000);
              }
            })
            .catch(error => {
              console.log("error", error);
            });
        })
        .catch(_ => {});
    },
    // 加载数据
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
  watch: {},
  computed: {},
  created() {
    getBasicData({
      gameList: "",
      designerList: {
        format: ["1#", "0#", "search"]
      }
    })
      .then(response => {
        this.gameList = response.data.gameList;
        this.designerList = response.data.designerList;
        this.reloadList();
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>

<style lang="scss" scoped>
.row-item {
  margin-bottom: 1.5rem;
}
.el-carousel {
  width: 100%;
}
</style>