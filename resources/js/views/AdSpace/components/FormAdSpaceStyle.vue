<template>
 <el-dialog
  :visible.sync="InnerDialogItemVisible"
  :title="innerDialogTitle"
  :modal="false"
  :curStyle="this.curStyle"
  :before-close="handleClose"
  :openType="openType">
  <el-form
    size="mini"
    ref="form"
    label-width="100px">
    <el-form-item label="样式名称" prop="style_name">
      <el-input v-model="form.style_name" :disabled="openType=='show'"></el-input>
    </el-form-item>
  
    <el-form-item label="枚举值" prop="enumerated_value" >
      <el-input v-model="form.enumerated_value" :disabled="openType=='show'"></el-input>
    </el-form-item>

    <el-form-item label="素材要求" >
      <el-button type="text" size="medium" @click="add_style_require('img')"  :disabled="openType=='show'">
        <i class="el-icon-circle-plus" ></i>图片要求
      </el-button>
      <el-button type="text" size="medium" @click="add_style_require('video')"  :disabled="openType=='show'">
        <i class="el-icon-circle-plus"></i>视频要求
      </el-button>
      <el-button type="text" size="medium" @click="add_style_require('text')"  :disabled="openType=='show'">
        <i class="el-icon-circle-plus"></i>文字要求
      </el-button>
    </el-form-item>

    <el-form-item label="文字要求" prop="style_info.text" 
    v-for="(item,key) in form.style_info.text"
        :key="key">
      <el-row
        >
        <el-col :span="10">
          <el-form-item label="名称">
            <el-input v-model="item.name" :disabled="openType=='show'"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="5">
          <el-form-item label="字数下限">
            <el-input v-model="item.min_length" :disabled="openType=='show'" type="number"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="5">
          <el-form-item label="字数上限">
            <el-input v-model="item.max_length" :disabled="openType=='show'"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
    </el-form-item>

    <el-form-item label="图片要求" prop="style_info.img" v-for="(item,key) in form.style_info.img"
        :key="key">
      <el-row
        >
        <el-col :span="10">
          <el-form-item label="名称">
            <el-input v-model="item.name" :disabled="openType=='show'"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="10"></el-col>
        <el-col :span="10">
          <el-form-item label="图片格式">
            <el-select v-model="item.format" :disabled="openType=='show'">
            <el-option v-for="(item) in img_format_list"
            :lable="item"
            :key="item"
            :title="item"
              :value="item"
            ></el-option>
            </el-select>
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <el-form-item label="图片大小上限">
            <el-row>
              <el-col :span="16"><el-input v-model="item.max" :disabled="openType=='show'"></el-input></el-col>
              <el-col :span="6">
                <el-select v-model="item.size_unit"  :disabled="openType=='show'">
                <el-option v-for="(item,key) in img_unit_size_list"
                :lable="item.label"
                :key="key"
                :title="item.value"
                  :value="item.value"
                ></el-option>
              </el-select>
              </el-col>
            </el-row>
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <el-form-item label="长(px)">
            <el-input v-model="item.width" :disabled="openType=='show'"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <el-form-item label="宽(px)">
            <el-input v-model="item.height" :disabled="openType=='show'"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
    </el-form-item>

    <el-form-item label="视频要求" prop="style_info.video" v-for="(item,key) in form.style_info.video"
        :key="key">
      <el-row
        >
        <el-col :span="10">
          <el-form-item label="名称">
            <el-input v-model="item.name" :disabled="openType=='show'"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <el-form-item label="视频格式">
            <el-select v-model="item.format" :disabled="openType=='show'">
            <el-option v-for="(item) in video_format_list"
            :lable="item"
            :key="item"
            :title="item"
              :value="item"
            ></el-option>
            </el-select>
          </el-form-item>  
        </el-col>
        <el-col :span="10">
          <el-form-item label="视频大小上限">
            <el-row>
              <el-col :span="16"><el-input v-model="item.max" :disabled="openType=='show'"></el-input></el-col>
              <el-col :span="6">
                <el-select v-model="item.size_unit" :disabled="openType=='show'">
                <el-option v-for="(item,key) in video_unit_size_list"
                :lable="item.label"
                :key="key"
                :title="item.value"
                  :value="item.value"
                ></el-option>
              </el-select>
              </el-col>
            </el-row>
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <el-form-item label="长(px)">
            <el-input v-model="item.width" :disabled="openType=='show'"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <el-form-item label="宽(px)">
            <el-input v-model="item.height" :disabled="openType=='show'"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="20">
          <span>视频封面</span>
          <el-switch
            v-model="item.has_cover"
            active-color="#13ce66"
            inactive-color="#C0C4CC" :disabled="openType=='show'">
          </el-switch>
        </el-col>
        <div v-if="item.has_cover==1">
          <el-col :span="10">
            <el-form-item label="封面名称">
            <el-input v-model="item.cover_name" :disabled="openType=='show'"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="10">
            <el-form-item label="封面图片格式">
                <el-select v-model="item.cover_format" :disabled="openType=='show'">
              <el-option v-for="(item) in img_format_list"
              :lable="item"
              :key="item"
              :title="item"
                :value="item"
              ></el-option>
            </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="10">
            <el-form-item label="封面大小上限">
              <el-row>
                <el-col :span="16"><el-input v-model="item.cover_max" :disabled="openType=='show'"></el-input></el-col>
                <el-col :span="6">
                  <el-select v-model="item.cover_size_unit" >
                  <el-option v-for="(item,key) in img_unit_size_list"
                  :lable="item.label"
                  :key="key"
                  :title="item.value"
                    :value="item.value"
                  ></el-option>
                </el-select>
                </el-col>
              </el-row>
            </el-form-item>
          </el-col>
          <el-col :span="10"></el-col>
          <el-col :span="10">
            <el-form-item label="长(px)">
              <el-input v-model="item.cover_width" :disabled="openType=='show'"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="10">
            <el-form-item label="宽(px)">
              <el-input v-model="item.cover_height" :disabled="openType=='show'"></el-input>
            </el-form-item>
          </el-col>
        </div>
      </el-row>
    </el-form-item>

    <el-form-item>
      <el-button @click="handleClose('cancel')">取消</el-button>
      <el-button type="primary" @click="handleClose('confirm')" :disabled="openType=='show'">确认</el-button>
    </el-form-item>
  </el-form>
</el-dialog>
</template>
<script>
export default {
  name: "form-ad-space-style",
  props: {
    innerDialogTitle: "创建样式",
    openType:'',
    curKey: {},
    curStyle: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      InnerDialogItemVisible: true,

      img_format_list: ["PNG", "JPG", "GIF"],
      video_format_list: ["MP4"],
      img_unit_size_list: [
        {
          label: "KB",
          value: "KB"
        }
      ],
      video_unit_size_list: [
        {
          label: "MB",
          value: "MB"
        }
      ],

      form: {
        style_name: "",
        enumerated_value: "",
        style_id: null,
        style_info: {
          text: [],
          img: [],
          video: []
        },
        show:1
      },
      rules: {
        
      }
    };
  },
  methods: {
    transferStyleJson: function(action) {
      
      this.$emit(
        "transferStyleJson",
        action, // cancel | confirm
        this.curKey,
        this.form
      );
    },
    handleClose(action) {
     
      this.transferStyleJson(action);
    },
    add_style_require: function(type) {
      if (type == "img") {
        this.form.style_info.img.push({
          name: "",
          format: "",
          max: "",
          height: "",
          size_unit: "KB",
          width: ""
        });
      } else if (type == "video") {
        this.form.style_info.video.push({
          name: "",
          format: "",
          max: "",
          height: "",
          width: "",
          size_unit: "MB",
          has_cover: 0,
          cover_name: "",
          cover_format: "",
          cover_max: "",
          cover_height: "",
          cover_size_unit: "KB",
          cover_width: ""
        });
      } else if (type == "text") {
        this.form.style_info.text.push({
          name: "",
          max_length: "",
          min_length: 0
        });
      }
    }
  },
  mounted() {
    if (this.curStyle) {
      this.form.style_name = this.curStyle.style_name;
      this.form.style_id = this.curStyle.style_id;
      this.form.enumerated_value = this.curStyle.enumerated_value;
      this.form.style_info = this.curStyle.style_info;
    }
  }
};
</script>

