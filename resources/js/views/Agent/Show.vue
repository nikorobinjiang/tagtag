<template>
  <el-table
    v-loading="loading"
    :data="itData"
    style="width: 100%">
    <el-table-column
      prop="label"
      label="信息"
      width="180">
    </el-table-column>
    <el-table-column
      label="内容">
      <template slot-scope="scope">
        <template v-if="scope.row.label === '落地页公司'">
            <el-col v-for="(item, key) in scope.row.value" :key="key">
              {{ item.name }} - {{ item.addr }} - {{ item.tel }}
            </el-col>
        </template>
        <template v-else-if="scope.row.label === '负责媒体'">
          {{ itMedias }}
        </template>
        <template v-else-if="scope.row.label === '负责游戏'">
          {{ itGames }}
        </template>
        <template v-else-if="scope.row.label === '查看数据项'">
          {{ itDataItems }}
        </template>
        <template v-else>{{ scope.row.value }}</template>
      </template>
    </el-table-column>
  </el-table>
</template>

<script>
import { getBasicData } from "@/js/api/common";
import { editItem, storeItem, updateItem } from "@/js/api/agent";
import { renewObject } from "@/js/utils";

export default {
  name: "gmp-agent-main-show",
  components: {},
  props: {
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
      loading: true,
      settlementList: [],
      mediaList: [],
      gameList: [],
      agentDataFieldList: [],
      submitDisabled: false,
      formInit: {
        agent_name: "",
        linkman: "",
        linkman_phone: "",
        email: "",
        companys: [],
        account: "",
        password: "",
        media_ids: [],
        game_ids: [],
        promote_ids: [],
        can_create_ad: 0,
        data_items: []
      },
      form: {}
    };
  },
  methods: {},
  watch: {
    itemId: {
      handler: function() {
        this.loading = true;
        this.$set(this.$data, "form", renewObject(this.formInit));
        // 加载编辑数据
        if (this.itemId) {
          editItem(this.itemId).then(response => {
            let it = response.data.it;
            this.can_create_ad_temp = it.can_create_ad;
            it.media_ids = it.media_ids.map(item => {
              if (item != "") {
                return parseInt(item);
              }
            });
            it.game_ids = it.game_ids.map(item => {
              if (item != "") {
                return parseInt(item);
              }
            });
            it.promote_ids = it.promote_ids.map(item => {
              if (item != "") {
                return parseInt(item);
              }
            });
            this.$set(this.$data, "form", it);
            this.loading = false;
          });
        } else {
          this.loading = false;
        }
      },
      immediate: true
    }
  },
  computed: {
    itData: function() {
      return [
        {
          label: "代理商名称",
          value: this.form.agent_name
        },
        {
          label: "联系人",
          value: this.form.linkman
        },
        {
          label: "联系电话",
          value: this.form.linkman_phone
        },
        {
          label: "邮箱地址",
          value: this.form.email
        },
        {
          label: "落地页公司",
          value: this.form.companys
        },
        {
          label: "代理商账号",
          value: this.form.account
        },
        {
          label: "代理商密码",
          value: this.form.password
        },
        {
          label: "负责媒体",
          value: this.form.media_ids
        },
        {
          label: "负责游戏",
          value: this.form.game_ids
        },
        {
          label: "创建广告",
          value: this.form.can_create_ad ? "是" : "否"
        },
        {
          label: "查看数据项",
          value: this.form.data_items
        }
      ];
    },
    itMedias: function() {
      let res = this.form.media_ids.map(item => {
        let label = "";
        this.mediaList.forEach(media => {
          if (media.value === item) {
            label = media.label;
          }
        });
        return label;
      });
      return res.join("，");
    },
    itGames: function() {
      let res = this.form.game_ids.map(item => {
        let label = "";
        this.gameList.forEach(game => {
          if (game.value === item) {
            label = game.label;
          }
        });
        return label;
      });
      return res.join("，");
    },
    itDataItems: function() {
      let res = this.form.data_items
        .map(item => {
          let label = "";
          this.agentDataFieldList.forEach(field => {
            if (field.value === item) {
              label = field.label;
            }
          });
          return label;
        })
        .filter(item => {
          return item.length > 0;
        });
      return res.join("，");
    }
  },
  created() {
    this.$set(this.$data, "form", renewObject(this.formInit));
    getBasicData({
      settlementList: "",
      agentDataFieldList: "",
      mediaList: "",
      gameList: ""
    })
      .then(response => {
        this.settlementList = response.data.settlementList;
        this.agentDataFieldList = response.data.agentDataFieldList;
        this.mediaList = response.data.mediaList;
        this.gameList = response.data.gameList;
        this.loading = false;
      })
      .catch(() => {
        this.loading = false;
      });
  }
};
</script>
