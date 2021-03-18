<template>
<div>
<el-tree
  :data="data2"
  show-checkbox
  :default-expand-all=false
  node-key="id"
  ref="tree"
  highlight-current
  :props="defaultProps">
</el-tree>
</div>
</template>
<script>
export default {
  props: {
    title: {
      type: String,
      default: ""
    },
    list: {
      type: Object,
      required: true
    }
  },
  methods: {
    getCheckedNodes() {
      console.log(this.$refs.tree.getCheckedNodes());
    },
    getCheckedKeys() {
      console.log(this.$refs.tree.getCheckedKeys());
    },
    setCheckedNodes() {
      this.$refs.tree.setCheckedNodes([
        {
          id: 5,
          label: "二级 2-1"
        },
        {
          id: 9,
          label: "三级 1-1-1"
        }
      ]);
    },
    setCheckedKeys() {
      this.$refs.tree.setCheckedKeys([3]);
    },
    resetChecked() {
      this.$refs.tree.setCheckedKeys([]);
    }
  },

  data() {
    //生成数据
    var arr = [] ;
    for(var x in this.list){
      if(this.list[x].level==1){
        var obj={};
        obj.id=this.list[x].id;
        obj.label=this.list[x].name;
        var children=[];
        var children_arr = this.list[x].children;
        for(var y in children_arr){
          var obj_child={};
          console.log("y:"+children_arr[y]);
          obj_child.id = this.list[children_arr[y]].id;
          obj_child.label = this.list[children_arr[y]].name;
          children.push(obj_child);
        }
        obj.children=children;
        arr.push(obj);
      }
    }
    console.log("hello");
    return {
      data2: [
        {
          id: 1,
          label: "全部",
          children: arr
        }
      ],
      defaultProps: {
        children: "children",
        label: "label"
      }
    };
  }
};
</script>