import {
  destroyItem
} from "@/js/api/advertising";

export default {
  methods: {
    handleDelete(index, row) {
      this.$confirm("确定删除该广告吗？删除成功后，可从回收站恢复该广告。")
        .then(_ => {
          destroyItem(row.ad_id)
            .then(response => {
              if (response.data.result == "error") {
                this.$message.warning(response.data.message);
              } else {
                this.$message(response.data.message);
                this.reloadList();
              }
            })
            .catch(error => {
              console.log("error", error);
            });
        })
        .catch(_ => {});
    },
    handleRestore(index, row) {
      this.$confirm("确定恢复该广告吗？恢复成功后将移动到对应状态的页签下。")
        .then(_ => {
          destroyItem(row.ad_id, {
              _action: 'restore'
            })
            .then(response => {
              if (response.data.result == "error") {
                this.$message.warning(response.data.message);
              } else {
                this.$message(response.data.message);
                this.reloadList();
              }
            })
            .catch(error => {
              console.log("error", error);
            });
        })
        .catch(_ => {});
    },
  }
};