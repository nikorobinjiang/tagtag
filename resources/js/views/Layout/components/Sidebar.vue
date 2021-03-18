<template>
  <el-aside width="200px" style="background-color: rgb(238, 241, 246)">
    <el-menu :default-openeds="menuOpens">
      <template v-for="(item, key) in menuList">
        <el-menu-item v-if="!item.submenu" :key="key" :index="item.name" @click="redirect_to(item.path)">
          <template slot="title">
            <svg-icon :icon-class="item.meta.icon?item.meta.icon:'file'" class="svg-icon"/>
            {{ item.meta.title }}
          </template>
        </el-menu-item>
        <el-submenu v-else-if="item.submenu" :key="key" :index="item.name">
          <template slot="title">
            <svg-icon :icon-class="item.meta.icon?item.meta.icon:'file'" class="svg-icon"/>
            {{ item.meta.title }}
          </template>
          <el-menu-item
            v-for="(itemSub, keySub) in item.submenu"
            :key="key + '-' + keySub"
            :index="key + '-' + keySub" @click="redirect_to(itemSub.path)">
            <template slot="title" >
              <svg-icon :icon-class="itemSub.meta.icon?itemSub.meta.icon:'file'" class="svg-icon"/>
              {{ itemSub.meta.title }}
            </template>
          </el-menu-item>
        </el-submenu>
      </template>
    </el-menu>
  </el-aside>
</template>

<script>
export default {
  data() {
    return {
      menuList: [
        { path: "/", name: "home", meta: { title: "首页", icon: "home" } },
        {
          name: "material",
          meta: { icon: "gift", title: "游戏素材" },
          submenu: [
            {
              path: "/material/picture",
              meta: { title: "图片素材", icon: "file-image" }
            },
            {
              path: "/material/video",
              meta: { title: "视频素材", icon: "video" }
            },
            {
              path: "/material/landing_page",
              meta: { title: "落地页素材", icon: "eye" }
            },
            {
              path: "/material/material_report",
              meta: { title: "素材报表", icon: "rise" }
            }
          ]
        },
        {
          name: "media",
          meta: { icon: "earth", title: "投放媒体" },
          submenu: [
            { path: "/media/media", meta: { title: "媒体管理" } },
            { path: "/media/account", meta: { title: "媒体账号管理" } }
          ]
        },
        {
          name: "advertising",
          meta: { icon: "golden-fill", title: "广告管理" },
          submenu: [
            {
              path: "/advertising/ad_space",
              meta: { title: "广告位管理", icon: "calendar" }
            },
            {
              path: "/advertising/advertising",
              meta: { title: "广告管理", icon: "tags" }
            },
            {
              path: "/advertising/realtime_data",
              meta: { title: "广告实时数据", icon: "fire" }
            }
          ]
        },
        {
          name: "agent",
          meta: { icon: "team", title: "合作代理商" },
          submenu: [{ path: "/agent/agent", meta: { title: "代理商管理" } }]
        },
        {
          name: "domain",
          meta: { icon: "rocket", title: "域名管理" },
          submenu: [{ path: "/domain/domain", meta: { title: "域名列表" } }]
        },
        {
          name: "tpl",
          meta: { icon: "snippets", title: "广告模板" },
          submenu: [
            { path: "/tpl/toutiao", meta: { title: "今日头条" } },
            { path: "/tpl/uchc_tpl", meta: { title: "UC头条" } },
            { path: "/tpl/uchcv2_tpl", meta: { title: "UC头条 2.0" } }
          ]
        },
        {
          name: "logout",
          path: "/logout",
          meta: { icon: "logout", title: "注销" }
        }
      ]
    };
  },
  methods: {
    redirect_to(url) {
      window.location.href = url;
    }
  },
  computed: {
    menuOpens: function() {
      let pathSplits = window.location.pathname.split("/");
      if (pathSplits.length > 1) {
        return [pathSplits[1]];
      } else {
        return [];
      }
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
.svg-icon {
  font-size: 18px;
}
</style>
