import {
  getBasicData
} from "@/js/api/common";

export default {
  computed: {
    xStore() {
      return this.$store.state.xStore;
    }
  },
  methods: {
    xStoreLoadExtraConfig() {
      if (!this.xStore.hasLoad) {
        getBasicData({
          adSiteList: {},
          mediaList: '',
          gameGroupList: '',
          gameList: '',
          designerList: {
            format: ["1#", "0#", "search"]
          },
          lpDesignerList: '',
          promoteList: '',
          agentList: '',
          settlementList: '',
          gameTypeH5: '',
          gameTypeWXMP: '',
        })
          .then(response => {
            let data = response.data;
            if (data.adSiteList) {
              this.xStore.data.adSiteList = data.adSiteList;
            }
            if (data.mediaList) {
              this.xStore.data.mediaList = data.mediaList;
            }
            if (data.gameGroupList) {
              this.xStore.data.gameGroupList = data.gameGroupList;
            }
            if (data.gameList) {
              this.xStore.data.gameList = data.gameList;
            }
            if (data.designerList) {
              this.xStore.data.designerList = data.designerList;
            }
            if (data.lpDesignerList) {
              this.xStore.data.lpDesignerList = data.lpDesignerList;
            }
            if (data.promoteList) {
              this.xStore.data.promoteList = data.promoteList;
            }
            if (data.agentList) {
              this.xStore.data.agentList = data.agentList;
            }
            if (data.settlementList) {
              this.xStore.data.settlementList = data.settlementList;
            }
            if (data.gameTypeH5) {
              this.xStore.data.gameTypeH5 = data.gameTypeH5;
            }
            if (data.gameTypeWXMP) {
              this.xStore.data.gameTypeWXMP = data.gameTypeWXMP;
            }
            this.xStore.hasLoad = true;
          })
          .catch(error => {
            console.log(error);
          });
      }
    }
  }
};