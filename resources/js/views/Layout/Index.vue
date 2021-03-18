<template>
  <el-container>
    <el-header height="50px">
      <el-row>
        <el-col class="header-left" :span="12">
          <strong>{{ appName }}</strong>
          <small>后台管理模板</small>
        </el-col>
        <el-col class="header-right" :offset="6" :span="6">
          <el-dropdown>
            <span class="el-dropdown-link">
              <svg-icon icon-class="people"/>
              {{ username }}
            </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item><a href="/logout">退出</a></el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
          <label @click="toggleScreenfull"><svg-icon icon-class="fullscreen"/>全屏</label>
        </el-col>
      </el-row>
    </el-header>
    <el-container>
      <el-container>
        <el-main>
          <slot></slot>
        </el-main>
        <el-footer height="50px">© 2018 {{ appName}}.</el-footer>
      </el-container>
    </el-container>
  </el-container>
</template>

<script>
import screenfull from "screenfull";


export default {
  name: "layout",
  props: {
    appName: {},
    username: {}
  },
  provide() {
    return {
      scatLayout: this,
      appName: this.appName,
      username: this.username
    };
  },
  components: {
    LayoutSidebar
  },
  data() {
    return {};
  },
  methods: {
    toggleScreenfull: function() {
      screenfull.toggle();
    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
a {
  &:link,
  &:visited {
    color: #000000;
    text-decoration: none; /*超链接无下划线*/
  }
  &:hover {
    color: #000000;
    text-decoration: underline; /*鼠标放上去有下划线*/
  }
}
.el-header {
  background-color: #0e90d2;
  color: #eeeeee;
  line-height: 50px;
  .header-right {
    text-align: right;
  }
}
.el-aside {
  color: #eeeeee;
}

.el-dropdown {
  color: #eeeeee;
  font-size: 14px;
}
</style>