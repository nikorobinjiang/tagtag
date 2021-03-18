<template>
  <span class="el-table cell">
    <div class="content" @click="($event) => handleSortToggle($event)">
      <slot></slot>
    </div>
    <span class="caret-wrapper">
      <i
        class="sort-caret"
        :class="ascending"
        @click="($event) => handleSortClick($event, 'asc')"
      ></i>
      <i
        class="sort-caret"
        :class="descending"
        @click="($event) => handleSortClick($event, 'desc')"
      ></i>
    </span>
  </span>
</template>

<script>
const sortables = ['', 'asc', 'desc'];

export default {
  name: "TableHeadSortable",
  components: {},
  props: {
    value: {
      type: String,
      required: false
    }
  },
  methods: {
    emitInput(val) {
      this.$emit("beforeInput")
      this.$emit("input", val);
    },
    handleSortToggle(event) {
      let sort_idx = sortables.indexOf(this.value);
      if (sort_idx === -1) {
        sort_idx = 0
      }
      this.emitInput(sortables[(sort_idx + 1) % sortables.length])
    },
    handleSortClick(event, order) {
      this.emitInput(order)
    }
  },
  computed: {
    ascending: function() {
      return ["ascending", this.value==="asc" ? "hl-ascending" : ""];
    },
    descending: function() {
      return ["descending", this.value==="desc" ? "hl-descending" : ""];
    }
  }
};
</script>

<style lang="scss" scoped>
.cell {
  .content {
    display: inline;
  }
  .hl-ascending {
    border-bottom-color: #409eff;
  }
  .hl-descending {
    border-top-color: #409eff;
  }
}
.caret-wrapper .sort-caret {
  left: -4px;
}
</style>
