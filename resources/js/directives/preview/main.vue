<template>
  <transition name="fade">
    <div class="gmvd-preview-wrapper" v-if="options.show" @click="leave" @touchmove.prevent>
      <div class="gmvd-preview-loading" v-show="options.loading"><div></div></div>
      <div class="gmvd-preview-content"
        v-if="previewCur.src">
        <div
          class="gmvd-preview-item"
          :style="!options.loading ? getCurStyel(previewCur) : {}"
          :ref="'gpi_' + previewCur.index"
          @mouseover="handleMouseOverObj(true)"
          @mouseout="handleMouseOverObj(false)"
          @wheel.prevent="handleImgWheel"
          @mousedown.prevent="handleImgDrag">
          <img
            class="gmvd-preview-obj"
            :ref="'gpi_obj_' + previewCur.index"
            v-if="previewCur.file_type === 'image'"
            v-show="!options.loading"
            :src="previewCur.src"
            :alt="previewCur.title">
          <video
            class="gmvd-preview-obj"
            :ref="'gpi_obj_' + previewCur.index"
            v-else-if="previewCur.file_type === 'video'"
            v-show="!options.loading"
            :poster="previewCur.src"
            :alt="previewCur.title"
            controls>
            <source :src="previewCur.file_url" type="video/mp4">
            Your browser does not support the video tag.
          </video>
        </div>
      </div>
      <div class="gmvd-preview-ui">
        <div class="gmvd-preview-nav-left" v-if="options.isHorizontalNavEnable" v-show="!options.loading">
          <span class="gmvd-preview-nav-arrow" @click="preAction" ></span>
        </div>
        <div class="gmvd-preview-nav-right" v-if="options.isHorizontalNavEnable" v-show="!options.loading">
          <span class="gmvd-preview-nav-arrow" @click="nextAction"></span>
        </div>
        <div
          class="gmvd-preview-caption"
          v-if="options.isTitleEnable && options.current.title && mouseOverObj"
          v-show="!options.loading">
          {{options.current.title}}
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { renewObject } from "@/js/utils";

// 鼠标动作
// @wheel.prevent="handleImgWheel"
// @mousedown.prevent="handleImgDrag"
// @dblclick="handleImgSizeReset"
export default {
  name: "gmvd-preview",
  data: function() {
    return {
      previewCur: {
        naturalHeight: 0,
        naturalWidth: 0,
        scaled: false,
        scale: 0,
        offseted: false,
        offsetLeft: 0,
        offsetTop: 0,
        style: {}
      },
      mouseOverObj: false,
      options: {
        isTitleEnable: true,
        isHorizontalNavEnable: false,
        show: false,
        loading: true,
        current: {
          title: "",
          src: ""
        },
        list: []
      }
    };
  },
  methods: {
    handleImgWheel(ev) {
      this.previewCur.scaled = true;
      if (ev.wheelDelta) {
        this.previewCur.scale += ev.wheelDelta / 12;
      } else if (ev.deltaY) {
        this.previewCur.scale += ev.deltaY;
      }
      if (this.previewCur.scale <= 0) {
        this.previewCur.scale = 1;
      }
    },
    handleImgDrag(ev) {
      this.previewCur.offseted = true;
      ev.preventDefault();
      let target = ev.target;
      let disX = ev.clientX - target.offsetLeft - this.previewCur.offsetLeft;
      let disY = ev.clientY - target.offsetTop - this.previewCur.offsetTop;
      document.onmousemove = evSub => {
        target.style.left = evSub.clientX - disX + "px";
        target.style.top = evSub.clientY - disY + "px";
        this.previewCur.offsetLeft = evSub.clientX - disX;
        this.previewCur.offsetTop = evSub.clientY - disY;
      };
      document.onmouseup = function() {
        document.onmousemove = null;
        document.onmouseup = null;
      };
    },
    handleImgSizeReset(ev) {
      ev.preventDefault();
      let target = ev.target;
      target.style.zoom = "100%";
    },
    handleMouseOverObj(val) {
      this.mouseOverObj = val;
    },
    leave(e) {
      if (this.options.show && !this.mouseOverObj) {
        this.close();
      }
    },
    close() {
      this.options.show = false;
    },
    preAction() {
      this.options.loading = true;
      let index = this.options.list.indexOf(this.options.current);
      if (index === 0) {
        this.options.loading = false;
        return;
      }
      index--;
      this.options.current = this.options.list[index];
      const img = new window.Image();
      img.src = this.options.current.src;
      img.onload = function() {
        setTimeout(function() {
          this.options.loading = false;
        }, 500);
      };
    },
    nextAction() {
      this.options.loading = true;
      let index = this.options.list.indexOf(this.options.current);
      if (index === this.options.list.length - 1) {
        this.options.loading = false;
        return;
      }
      index++;
      this.options.current = this.options.list[index];
      const img = new window.Image();
      img.src = this.options.current.src;
      img.onload = function() {
        setTimeout(function() {
          this.options.loading = false;
        }, 500);
      };
    },
    getCurStyel(item) {
      if (item.scaled === false || item.offseted === false) {
        let el = this.$refs["gpi_" + item.index];
        let elObj = this.$refs["gpi_obj_" + item.index];

        if (el && elObj) {
          if (item.file_type === "image") {
            this.previewCur.naturalHeight = elObj.naturalHeight;
            this.previewCur.naturalWidth = elObj.naturalWidth;
          } else if (item.file_type === "video") {
            this.previewCur.naturalHeight = elObj.videoHeight;
            this.previewCur.naturalWidth = elObj.videoWidth;
          }

          let objHeight = this.previewCur.naturalHeight;
          let objWidth = this.previewCur.naturalWidth;
          let docHeight = document.documentElement.clientHeight;
          let docWidth = document.documentElement.clientWidth;

          // 初始缩放大小
          if (item.scaled === false) {
            let scaleHeight = objHeight / docHeight;
            let scaleWidth = objWidth / docWidth;
            if (scaleHeight > 1) {
              this.previewCur.scale = (1 / scaleHeight) * 100;
            }
            if (scaleWidth > 1) {
              this.previewCur.scale = (1 / scaleWidth) * 100;
            }
          }

          // 初始位置偏移
          if (item.offseted === false) {
            let scale = this.previewCur.scale / 100;
            let offsetTopRes = 0;
            if (objHeight > docHeight) {
              let offsetHeight = (objHeight - objHeight * scale) / 2;
              // let offsetWidth = (objWidth - objWidth * scale) / 2;

              offsetTopRes = -offsetHeight;
              // this.previewCur.offsetLeft = -offsetWidth;
            } else {
              let offsetHeight = (docHeight - objHeight * scale) / 2;
              // let offsetWidth = (docWidth - objWidth * scale) / 2;

              offsetTopRes = offsetHeight;
              // this.previewCur.offsetLeft = offsetWidth;
            }

            this.previewCur.offsetTop =
              offsetTopRes > docHeight * 0.4 ? 50 : offsetTopRes;
          }
        }
      }

      let scale = this.previewCur.scale / 100;
      let offsetLeft = this.previewCur.offsetLeft + "px";
      let offsetTop = this.previewCur.offsetTop + "px";

      return {
        transform: `translate3d(${offsetLeft}, ${offsetTop}, 0px) scale(${scale})`,
        "-ms-transform": `translate3d(${offsetLeft}, ${offsetTop}, 0px) scale(${scale})`,
        "-moz-transform": `translate3d(${offsetLeft}, ${offsetTop}, 0px) scale(${scale})`,
        "-webkit-transform": `translate3d(${offsetLeft}, ${offsetTop}, 0px) scale(${scale})`,
        "-o-transform": `translate3d(${offsetLeft}, ${offsetTop}, 0px) scale(${scale})`
      };
    },
    getFileType(item) {
      let res = "unknown";
      if (item.file_type) {
        return item.file_type;
      }
      let previewUrl = item.file_url ? item.file_url : item.src;
      if (previewUrl) {
        let index1 = previewUrl.lastIndexOf(".");
        let index2 = previewUrl.length;
        let suffix = previewUrl
          .substring(index1 + 1, index2)
          .toLocaleLowerCase(); //后缀名
        if (suffix === "mp4") {
          res = "video";
        } else if (["jpg", "jpeg", "png", "gif"].indexOf(suffix) !== -1) {
          res = "image";
        }
      }
      return res;
    },
    onLoadObj() {
      console.log("onload");
    }
  },
  watch: {
    "options.current": function() {
      this.options.loading = true;
      let extInfo = {};
      if (this.options.current) {
        extInfo = {
          naturalHeight: 0,
          naturalWidth: 0,
          scaled: false,
          scale: 100,
          offseted: false,
          offsetLeft: 0,
          offsetTop: 0,
          file_type: this.getFileType(this.options.current)
        };
      }
      this.$set(
        this.$data,
        "previewCur",
        Object.assign(renewObject(this.options.current), extInfo)
      );
      this.options.loading = false;
    }
  },
  computed: {},
  created: function() {}
};
</script>

<style lang="scss" scoped>
@import "./main.scss";
</style>