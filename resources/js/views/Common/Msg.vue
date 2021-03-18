<template>
  <div class="errPage-container">
    <el-row>
      <el-col>
        <h1 class="text-jumbo text-ginormous">{{ msg }}</h1>
        <ul class="list-unstyled">
          <li class="link-type">
            <a href="/">回首页</a>
          </li>
          <li class="link-type">
            <a @click="gotoPrev" href="#">返回上一页</a>
          </li>
        </ul>
      </el-col>
    </el-row>
  </div>
</template>

<script>

export default {
  name: "common-msg",
  props: {
    content: {}
  },
  data() {
    return {
      dialogVisible: false
    };
  },
  methods: {
    gotoPrev: function() {
      history.go(-1);
    }
  },
  computed: {
    msg: function() {
      if (typeof(this.content) === 'string') {
        return this.content;
      } else {
        return this.content.msg ? this.content.msg : "";
      }
    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.errPage-container {
  width: 800px;
  margin: 100px auto;
  .pan-back-btn {
    background: #008489;
    color: #fff;
    border: none !important;
  }
  .pan-gif {
    margin: 0 auto;
    display: block;
  }
  .pan-img {
    display: block;
    margin: 0 auto;
    width: 100%;
  }
  .text-jumbo {
    font-size: 60px;
    font-weight: 700;
    color: #484848;
  }
  .list-unstyled {
    font-size: 14px;
    li {
      padding-bottom: 5px;
    }
    a {
      color: #008489;
      text-decoration: none;
      &:hover {
        text-decoration: underline;
      }
    }
  }
}
</style>
