<template>
  <div class="scat-card">
    <el-row>
      <el-col>
        <el-card :body-style="{ padding: '4px' }">
          <div class="top-nav">
            <!-- 自定义页眉  是否自带选框（单选框 复选框 无） -->
            <el-radio v-if="canSelect == 'radio'"></el-radio>
            <el-checkbox  v-if="canSelect == 'checkbox'"></el-checkbox>
            <!-- 是否操作icon -->
            <div class="button">
              <slot name="operateIcons"></slot>
            </div>
          </div>

          <div class="content-file" :class='"content-"+size'>
            <!-- 自定义内容 图片或者视频 -->
            <slot name="content"></slot>
          </div>

          <div style="padding: 14px;">
            <span>{{ fileName }}</span>
            <div class="bottom clearfix"  v-if="info.length>0">
                <!-- 自定义页脚 -->
                <div v-for="(item,key) in info" :key="key">
                  <el-tooltip placement="top" :content="item">
                  <span class="info" >
                    {{item}}
                  </span>
                  </el-tooltip>
                </div>
                
                <slot name="footer"></slot>
                
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>
<script>
export default {
  name: "scat-card",
  props: {
    size: {
      type: String,
      required: false,
      default: "large"
    },
    // null radio checkbox
    canSelect: {
      type: String,
      required: false,
      default: null
    },
    fileName: {
      type: String,
      required: false,
      default: null
    },
    info: {
      type: Array,
      required: false,
      default: []
    }
  }
};
</script>

<style lang="scss">
.scat-card {
  .content-large {
    width: 100%;
    height: 22rem;
  }
  .content-small {
    width: 100%;
    height: 14rem;
  }
  .content-file {
    img {
      max-width: 100%;
      max-height: 95%;
      display: block;
      margin: 0 auto;
    }
  }
  .info {
    font-size: 13px;
    color: #999;
    float: left;
    display: block;
    width: 48%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: 10px;
  }
  .top-nav {
    display: block;
    height: 1.5rem;
    padding: 0 4px 4px 4px;
  }
  .bottom {
    margin-top: 10px;
    line-height: 14px;
  }

  .button {
    padding: 0;
    float: right;
  }

  .image {
    width: 100%;
    display: block;
  }

  .clearfix:before,
  .clearfix:after {
    display: table;
    content: "";
  }

  .clearfix:after {
    clear: both;
  }
}
</style>
