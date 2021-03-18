<template>
<div class="scat-upload">
  <el-row class="row-tools" type="flex" justify="end" v-if="false">
    <el-button type="button" @click.prevent="isOption = !isOption">
      <i class="el-icon-setting"></i>
      Options
    </el-button>
  </el-row>

  <div v-show="$refs.scatUpload && $refs.scatUpload.dropActive" class="drop-active">
		<h3>Drop files to upload</h3>
  </div>

  <el-row class="row-upload-actions" v-if="(multiple || (value.length === 0))" v-show="!isOption">
    <el-col :span="24" class="upload-actions">
      <el-button type="primary" v-show="false">
        <FileUpload
          class="input-upload-main"
          :post-action="postAction"
          :chunk-enabled="chunkEnabled"
          :chunk="{
            headers: headers,
            action: chunkAction,
            minSize: chunkMinSize * 1048576,
            maxActive: chunkMaxActive,
            maxRetries: chunkMaxRetries,
            finishBody: data
          }"
          :extensions="extensions"
          :accept="accept"
          :multiple="multiple"
          :directory="directory"
          :size="size || 0"
          :thread="thread < 1 ? 1 : (thread > 5 ? 5 : thread)"
          :headers="headers"
          :data="data"
          :drop="drop"
          :drop-directory="dropDirectory"
          :add-index="addIndex"
          v-model="files"
          @input-filter="inputFilter"
          @input-file="inputFile"
          ref="scatUpload">
          <i class="el-icon-plus"></i>上传{{ typeInfo.name }}
          <template v-if="false">
            <el-dropdown-item><label :for="name">Add files</label></el-dropdown-item>
            <el-dropdown-item><label @click="onAddFolader">Add folder</label></el-dropdown-item>
            <el-dropdown-item><label @click.prevent="addData.show = true">Add data</label></el-dropdown-item>
          </template>
        </FileUpload>
      </el-button>
      <div v-if="!isShow" style="display:inline;">
        <el-button type="primary" @click.prevent="onAddFiles">
        <i class="el-icon-plus"></i>
        上传{{ typeInfo.name }}
        </el-button>
        <el-button type="success" v-if="!$refs.scatUpload || !$refs.scatUpload.active" @click.prevent="$refs.scatUpload.active = true">
          <i class="el-icon-upload"></i>
          开始上传
        </el-button>
        <el-button type="danger" v-else @click.prevent="$refs.scatUpload.active = false">
          <i class="el-icon-close"></i>
          停止上传
        </el-button>
      </div>
      
      <div class="batch-tools">
        <slot name="tools" v-bind="{selected: selected}"></slot>
      </div>
    </el-col>
    <el-col :span="12" class="upload-footer" v-if="false">
      Drop: {{$refs.scatUpload ? $refs.scatUpload.drop : false}},
      Active: {{$refs.scatUpload ? $refs.scatUpload.active : false}},
      Uploaded: {{$refs.scatUpload ? $refs.scatUpload.uploaded : true}},
      Drop active: {{$refs.scatUpload ? $refs.scatUpload.dropActive : false}}
    </el-col>
  </el-row>

  <el-row class="row-upload-content" v-show="!isOption">
    <el-table
      :data="files"
      tooltip-effect="dark"
      style="width: 100%"
      @selection-change="handleSelectionChange">
      <el-table-column
        v-if="false"
        label="#">
        <template slot-scope="scope">
          {{ scope.$index }}
        </template>
      </el-table-column>

      <el-table-column
        v-if="hasSelect"
        type="selection"
        width="55">
      </el-table-column>

      <el-table-column
        :label="typeInfo.name">
        <template slot-scope="scope">
          <div class="preview">
            <img
              v-if="['image', 'zip'].indexOf(type) !== -1"
              :src="getPreview(scope.row)"
              :title="getFilename(scope.row)"
              v-preview="{file_type: 'image'}" />
            <video
              v-else-if="type === 'video'"
              :poster="getPreview(scope.row)"
              :src="getFile(scope.row)"
              :title="getFilename(scope.row)"
              v-preview="{file_url: getFile(scope.row), file_type: 'video'}" />
            <span v-else>No Image</span>
          </div>
        </template>
      </el-table-column>

      <el-table-column
        label="文件名">
        <template slot-scope="scope">
          <div class="filename">
            <template v-if="getFilename(scope.row)">{{ getFilename(scope.row) }}</template>
          </div>
          <div class="progress" v-if="scope.row.id && (scope.row.active || scope.row.progress !== '0.00')">
            <el-progress :percentage="parseInt(scope.row.progress)"></el-progress>
          </div>
        </template>
      </el-table-column>

      <el-table-column
        label="类型">
        <template slot-scope="scope">
          {{ getFileType(scope.row) }}
        </template>
      </el-table-column>

      <el-table-column
        v-if="type==='video'"
        label="时长">
        <template slot-scope="scope">
          {{ getVideoTime(scope.row) }}
        </template>
      </el-table-column>

      <el-table-column
        v-if="['image', 'video'].indexOf(type) !== -1"
        label="尺寸">
        <template slot-scope="scope">
          {{ getResolution(scope.row) }}
        </template>
      </el-table-column>

      <el-table-column
        label="大小">
        <template slot-scope="scope">
          <template v-if="scope.row.size">{{ scope.row.size | formatSize }}</template>
          <template v-else-if="scope.row.file_size">{{ scope.row.file_size | formatSize  }}</template>
        </template>
      </el-table-column>

      <el-table-column
        v-if="false"
        label="上传速度">
        <template slot-scope="scope">
          {{ scope.row.speed | formatSize }}
        </template>
      </el-table-column>

      <el-table-column
        label="状态">
        <template slot-scope="scope">
          <label v-if="scope.row.error">
            {{ getErrorMsg(scope.row.error) }}
          </label>
          <label v-else-if="scope.row.success">
            上传成功
          </label>
          <label v-else-if="scope.row.active">
            {{ scope.row.active ? "开始上传" : "停止上传" }}
          </label>
          <label v-else>
            -
          </label>
        </template>
      </el-table-column>

      <el-table-column
        label="操作"
        width="160px">
        <template slot-scope="scope">
          <el-row>
            <el-col :span="12">
              <el-button type="primary" v-download="{url:getDownloadUrl(scope.row)}">下载</el-button>
            </el-col>
            <el-col :span="12">
              <el-button type="danger" @click.prevent="handleRemove(scope.row)" v-if="!isShow">删除</el-button>
            </el-col>
          </el-row>

          <el-dropdown v-if="false" trigger="click" size="mini" type="primary">
            <span class="el-dropdown-link">
              <el-button type="primary" @click.prevent="handleRemove(scope.row)">操作<i class="el-icon-arrow-down el-icon--right"></i></el-button>
            </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item v-if="false" :disabled="scope.row.active || scope.row.success || scope.row.error === 'compressing'">
                <label
                  @click.prevent="scope.row.active || scope.row.success || scope.row.error === 'compressing' ? false :  onEditFileShow(scope.row)">
                  编辑
                </label>
              </el-dropdown-item>
              <el-dropdown-item v-else-if="scope.row.id" :disabled="!scope.row.active">
                <label
                  @click.prevent="scope.row.active ? $refs.scatUpload.update(scope.row, {error: 'cancel'}) : false">
                  取消
                </label>
              </el-dropdown-item>

              <el-dropdown-item v-if="scope.row.active">
                <label @click.prevent="$refs.scatUpload.update(scope.row, {active: false})">
                  中止
                </label>
              </el-dropdown-item>
              <el-dropdown-item v-else-if="scope.row.error && scope.row.error !== 'compressing' && $refs.scatUpload.features.html5">
                <label @click.prevent="$refs.scatUpload.update(scope.row, {active: true, error: '', progress: '0.00'})">
                  重新上传
                </label>
              </el-dropdown-item>
              <el-dropdown-item v-else-if="scope.row.id" :disabled="scope.row.success || scope.row.error === 'compressing'">
                <label
                  @click.prevent="scope.row.success || scope.row.error === 'compressing' ? false : $refs.scatUpload.update(scope.row, {active: true})">
                  上传
                </label>
              </el-dropdown-item>

              <el-dropdown-item divided>
                <label @click.prevent="handleRemove(scope.row)">
                  删除
                </label>
              </el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
    </el-table>
  </el-row>

  <el-row class="row-upload-option" v-show="isOption">
    <div class="form-group">
      <label for="accept">Accept:</label>
      <input type="text" id="accept" class="form-control" v-model="accept">
      <small class="form-text text-muted">Allow upload mime type</small>
    </div>
    <div class="form-group">
      <label for="extensions">Extensions:</label>
      <input type="text" id="extensions" class="form-control" v-model="extensions">
      <small class="form-text text-muted">Allow upload file extension</small>
    </div>
    <div class="form-group">
      <label>PUT Upload:</label>
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="put-action" id="put-action" value="" v-model="putAction"> Off
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="put-action" id="put-action" value="/upload" v-model="putAction"> On
        </label>
      </div>
      <small class="form-text text-muted">After the shutdown, use the POST method to upload</small>
    </div>
    <div class="form-group">
      <label for="thread">Thread:</label>
      <input type="number" max="5" min="1" id="thread" class="form-control" v-model.number="thread">
      <small class="form-text text-muted">Also upload the number of files at the same time (number of threads)</small>
    </div>
    <div class="form-group">
      <label for="size">Max size:</label>
      <input type="number" min="0" id="size" class="form-control" v-model.number="size">
    </div>
    <div class="form-group">
      <label for="minSize">Min size:</label>
      <input type="number" min="0" id="minSize" class="form-control" v-model.number="minSize">
    </div>
    <div class="form-group">
      <label for="autoCompress">Automatically compress:</label>
      <input type="number" min="0" id="autoCompress" class="form-control" v-model.number="autoCompress">
      <small class="form-text text-muted" v-if="autoCompress > 0">More than {{autoCompress | formatSize}} files are automatically compressed</small>
      <small class="form-text text-muted" v-else>Set up automatic compression</small>
    </div>

    <div class="form-group">
      <div class="form-check">
        <label class="form-check-label">
          <input type="checkbox" id="add-index" class="form-check-input" v-model="addIndex"> Start position to add
        </label>
      </div>
      <small class="form-text text-muted">Add a file list to start the location to add</small>
    </div>

    <div class="form-group">
      <div class="form-check">
        <label class="form-check-label">
          <input type="checkbox" id="drop" class="form-check-input" v-model="drop"> Drop
        </label>
      </div>
      <small class="form-text text-muted">Drag and drop upload</small>
    </div>
    <div class="form-group">
      <div class="form-check">
        <label class="form-check-label">
          <input type="checkbox" id="drop-directory" class="form-check-input" v-model="dropDirectory"> Drop directory
        </label>
      </div>
      <small class="form-text text-muted">Not checked, filter the dragged folder</small>
    </div>
    <div class="form-group">
      <div class="form-check">
        <label class="form-check-label">
          <input type="checkbox" id="upload-auto" class="form-check-input" v-model="uploadAuto"> Auto start
        </label>
      </div>
      <small class="form-text text-muted">Automatically activate upload</small>
    </div>
    <div class="form-group">
      <button type="button" class="btn btn-primary btn-lg btn-block" @click.prevent="isOption = !isOption">Confirm</button>
    </div>
  </el-row>

  <el-dialog
    class="row-upload-add-data"
    title="Add data"
    :visible.sync="addData.show"
    width="50%"
    :before-close="handleClose">
    <form @submit.prevent="onAddData">
      <div class="modal-body">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" class="form-control" required id="name"  placeholder="Please enter a file name" v-model="addData.name">
          <small class="form-text text-muted">Such as <code>filename.txt</code></small>
        </div>
        <div class="form-group">
          <label for="type">Type:</label>
          <input type="text" class="form-control" required id="type"  placeholder="Please enter the MIME type" v-model="addData.type">
          <small class="form-text text-muted">Such as <code>text/plain</code></small>
        </div>
        <div class="form-group">
          <label for="content">Content:</label>
          <textarea class="form-control" required id="content" rows="3" placeholder="Please enter the file contents" v-model="addData.content"></textarea>
        </div>
      </div>
    </form>
    <span slot="footer" class="dialog-footer">
      <el-button @click="addData.show = false">取 消</el-button>
      <el-button type="primary" @click="addData.show = false">保 存</el-button>
    </span>
  </el-dialog>

  <el-dialog
    class="row-upload-edit-file"
    title="Edit file"
    :visible.sync="editFile.show"
    width="50%"
    :before-close="handleClose">
    <form @submit.prevent="onEditorFile">
      <div class="modal-body">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" class="form-control" required id="name"  placeholder="Please enter a file name" v-model="editFile.name">
        </div>
        <div class="form-group" v-if="editFile.show && editFile.blob && editFile.type && editFile.type.substr(0, 6) === 'image/'">
          <label>Image: </label>
          <div class="edit-image">
            <img :src="editFile.blob" ref="editImage" />
          </div>

          <div class="edit-image-tool">
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-primary" @click="editFile.cropper.rotate(-90)" title="cropper.rotate(-90)"><i class="fa fa-undo" aria-hidden="true"></i></button>
              <button type="button" class="btn btn-primary" @click="editFile.cropper.rotate(90)"  title="cropper.rotate(90)"><i class="fa fa-repeat" aria-hidden="true"></i></button>
            </div>
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-primary" @click="editFile.cropper.crop()" title="cropper.crop()"><i class="fa fa-check" aria-hidden="true"></i></button>
              <button type="button" class="btn btn-primary" @click="editFile.cropper.clear()" title="cropper.clear()"><i class="fa fa-remove" aria-hidden="true"></i></button>
            </div>
          </div>
        </div>
      </div>
    </form>
    <span slot="footer" class="dialog-footer">
      <el-button @click="editFile.show = false">取 消</el-button>
      <el-button type="primary" @click="editFile.show = false">保 存</el-button>
    </span>
  </el-dialog>
</div>
</template>

<script>
import { Message } from "element-ui";

import { getUploadUrl } from "@/js/api/common";
import { getHeaders } from "@/js/utils/request";

import Cropper from "cropperjs";
import ImageCompressor from "image-compressor.js";
import FileUpload from "vue-upload-component";

const filesGroup = {
  image: {
    name: "图片",
    list: [
      { accept: "image/png", extension: "png" },
      { accept: "image/jpeg", extension: "jpeg" },
      { accept: "image/jpeg", extension: "jpg" },
      { accept: "image/gif", extension: "gif" },
      { accept: "image/webp", extension: "webp" }
    ]
  },
  video: { name: "视频", list: [{ accept: "video/mp4", extension: "mp4" }] },
  zip: {
    name: "文件",
    list: [{ accept: "application/x-zip-compressed", extension: "zip" }]
  }
};

const uploadErrors = {
  size: "文件大小至少为1KB",
  extension: "文件后缀不符合要求",
  timeout: "上传超时",
  abort: "上传取消",
  network: "网络异常",
  server: "服务器异常",
  denied: "上传被禁止"
};

const getTypeInfo = type => {
  let accepts = [];
  let extensions = [];
  let typeInfo = filesGroup[type];

  if (typeInfo) {
    typeInfo.list.forEach(item => {
      accepts.push(item.accept);
      extensions.push(item.extension);
    });
  }

  return {
    name: typeInfo ? typeInfo.name : "文件",
    accept: accepts.join(","),
    extensions: extensions
  };
};

export default {
  name: "input-upload",
  components: {
    FileUpload
  },
  props: {
    value: {
      type: Array,
      required: true
    },
    chunkEnabled: {
      type: Boolean,
      default: true
    },
    data: {
      type: Object,
      default: _ => {},
      intro: "POST 请求: 附加请求的 body, PUT 请求: 附加请求的 query"
    },
    type: {
      type: [String, Array],
      default: "image"
    },
    multiple: {
      type: Boolean,
      default: true,
      intro: "是否允许选择多个文件, 如果为 false 则只能上传一个文件"
    },
    handleCheckCanRemove: {
      type: Function,
      required: true
    },
    isShow: {
      type: Boolean,
      default: false
    },
    hasSelect: {
      type: Boolean,
      default: false
    }
  },
  data() {
    let typeInfo = getTypeInfo(this.type);
    return {
      typeInfo,
      files: this.value === undefined || this.value === null ? [] : this.value,

      // 1MB by default
      chunkMinSize: 1,
      chunkMaxActive: 3,
      chunkMaxRetries: 5,
      // 文件类型限制
      accept: typeInfo.accept,
      extensions: typeInfo.extensions,
      // 文件大小限制
      minSize: 1024,
      size: 1024 * 1024 * 100,

      // 是否是上传文件夹
      directory: false,
      drop: true,
      dropDirectory: true,
      addIndex: false,
      thread: 3,
      name: "file_data",
      chunkAction: getUploadUrl("chunk"),
      postAction: getUploadUrl(),
      putAction: getUploadUrl(),
      headers: getHeaders(),
      autoCompress: 1024 * 1024,
      uploadAuto: false,
      isOption: false,
      addData: {
        show: false,
        name: "",
        type: "",
        content: ""
      },
      editFile: {
        show: false,
        name: ""
      },
      selected: []
    };
  },
  watch: {
    "editFile.show"(newValue, oldValue) {
      // 关闭了 自动删除 error
      if (!newValue && oldValue) {
        this.$refs.scatUpload.update(this.editFile.id, {
          error: this.editFile.error || ""
        });
      }
      if (newValue) {
        this.$nextTick(function() {
          if (!this.$refs.editImage) {
            return;
          }
          let cropper = new Cropper(this.$refs.editImage, {
            autoCrop: false
          });
          this.editFile = {
            ...this.editFile,
            cropper
          };
        });
      }
    },
    "addData.show"(show) {
      if (show) {
        this.addData.name = "";
        this.addData.type = "";
        this.addData.content = "";
      }
    },
    value: {
      handler: function(val, oldVal) {
        if (val && oldVal && val.length !== oldVal.length) {
          this.files = val === undefined || val === null ? [] : val;
        }
      },
      immediate: true,
      deep: true
    },
    files: {
      handler: function(val) {
        if (this.$refs.scatUpload && this.$refs.scatUpload.uploaded) {
          this.emitValue();
        } else if (!this.$refs.scatUpload && val.length === 0) {
          this.emitValue();
        }
      }
    }
  },
  methods: {
    inputFilter(newFile, oldFile, prevent) {
      if (newFile && !oldFile) {
        // Before adding a file
        // 添加文件前
        // Filter system files or hide files
        // 过滤系统文件 和隐藏文件
        if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
          return prevent();
        }
        // Filter php html js file
        // 过滤 php html js 文件
        if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
          return prevent();
        }
        // Automatic compression
        // 自动压缩
        if (
          newFile.file &&
          newFile.type.substr(0, 6) === "image/" &&
          this.autoCompress > 0 &&
          this.autoCompress < newFile.size
        ) {
          newFile.error = "compressing";
          const imageCompressor = new ImageCompressor(null, {
            convertSize: Infinity,
            maxWidth: 512,
            maxHeight: 512
          });
          imageCompressor
            .compress(newFile.file)
            .then(file => {
              this.$refs.scatUpload.update(newFile, {
                error: "",
                file,
                size: file.size,
                type: file.type
              });
            })
            .catch(err => {
              this.$refs.scatUpload.update(newFile, {
                error: err.message || "compress"
              });
            });
        }
      }
      if (newFile && (!oldFile || newFile.file !== oldFile.file)) {
        // Create a blob field
        // 创建 blob 字段
        newFile.blob = "";
        let URL = window.URL || window.webkitURL;
        if (URL && URL.createObjectURL) {
          newFile.blob = URL.createObjectURL(newFile.file);
        }
        // Thumbnails
        // 缩略图
        newFile.thumb = "";
        if (newFile.blob && newFile.type.substr(0, 6) === "image/") {
          newFile.thumb = newFile.blob;
        }
      }
    },
    // add, update, remove File Event
    inputFile(newFile, oldFile) {
      if (newFile && oldFile) {
        // update
        if (newFile.active && !oldFile.active) {
          // beforeSend
          // min size
          if (
            newFile.size >= 0 &&
            this.minSize > 0 &&
            newFile.size < this.minSize
          ) {
            this.$refs.scatUpload.update(newFile, { error: "size" });
          }
        }
        if (newFile.progress !== oldFile.progress) {
          // progress
        }
        if (newFile.error && !oldFile.error) {
          // error
        }
        if (newFile.success && !oldFile.success) {
          // success
        }
      }
      if (!newFile && oldFile) {
        // remove
        if (oldFile.success && oldFile.response.id) {
          // $.ajax({
          //   type: 'DELETE',
          //   url: '/upload/delete?id=' + oldFile.response.id,
          // })
        }
      }
      // Automatically activate upload
      if (
        Boolean(newFile) !== Boolean(oldFile) ||
        oldFile.error !== newFile.error
      ) {
        if (this.uploadAuto && !this.$refs.scatUpload.active) {
          this.$refs.scatUpload.active = true;
        }
      }
    },
    alert(message) {
      alert(message);
    },
    onEditFileShow(file) {
      this.editFile = { ...file, show: true };
      this.$refs.scatUpload.update(file, { error: "edit" });
    },
    onEditorFile() {
      if (!this.$refs.scatUpload.features.html5) {
        this.alert("Your browser does not support");
        this.editFile.show = false;
        return;
      }
      let data = {
        name: this.editFile.name
      };
      if (this.editFile.cropper) {
        let binStr = atob(
          this.editFile.cropper
            .getCroppedCanvas()
            .toDataURL(this.editFile.type)
            .split(",")[1]
        );
        let arr = new Uint8Array(binStr.length);
        for (let i = 0; i < binStr.length; i++) {
          arr[i] = binStr.charCodeAt(i);
        }
        data.file = new File([arr], data.name, { type: this.editFile.type });
        data.size = data.file.size;
      }
      this.$refs.scatUpload.update(this.editFile.id, data);
      this.editFile.error = "";
      this.editFile.show = false;
    },
    // add files
    onAddFiles() {
      let input = this.$refs.scatUpload.$el.querySelector("input");
      input.onclick = null;
      input.click();
    },
    // add folader
    onAddFolader() {
      if (!this.$refs.scatUpload.features.directory) {
        this.alert("Your browser does not support");
        return;
      }
      let input = this.$refs.scatUpload.$el.querySelector("input");
      input.directory = true;
      input.webkitdirectory = true;
      this.directory = true;
      input.onclick = null;
      input.click();
      input.onclick = e => {
        this.directory = false;
        input.directory = false;
        input.webkitdirectory = false;
      };
    },
    onAddData() {
      this.addData.show = false;
      if (!this.$refs.scatUpload.features.html5) {
        this.alert("Your browser does not support");
        return;
      }
      let file = new window.File([this.addData.content], this.addData.name, {
        type: this.addData.type
      });
      this.$refs.scatUpload.add(file);
    },
    getResolution(file) {
      if (file.id && file.response && file.response.item) {
        return [
          file.response.item.file_width,
          file.response.item.file_height
        ].join(" x ");
      } else if (file.annex_id) {
        return [file.file_width, file.file_height].join(" x ");
      } else {
        return "-";
      }
    },
    getErrorMsg(error) {
      return uploadErrors[error] ? uploadErrors[error] : "未知状态";
    },
    getPreview(file) {
      let res = require("@/js/assets/images/common-msg.png");
      if (this.type === "image") {
        if (file.id) {
          res = file.thumb;
        } else {
          res = file.preview;
        }
      } else if (this.type === "video") {
        if (file.id) {
          res = "";
        } else {
          res = file.preview;
        }
      } else if (this.type === "zip") {
        if (file.response && file.response.item) {
          res = file.response.item.preview;
        } else if (file.preview) {
          res = file.preview;
        }
      } else {
        res = file.thumb;
      }
      return res;
    },
    getFile(file) {
      if (this.type === "image") {
        if (file.id) {
          return file.thumb;
        } else {
          return file.file_url;
        }
      } else if (this.type === "video") {
        if (file.id) {
          return file.blob;
        } else {
          return file.file_url;
        }
      } else {
        return file.thumb;
      }
    },
    getDownloadUrl(file) {
      if (file.id) {
        return file.blob;
      } else if (file.annex_id) {
        return file.file_url;
      }
    },
    getFilename(file) {
      let res = null;
      if (file.name) {
        res = file.name;
      } else if (file.file_name) {
        res = file.file_name;
      }
      return res;
    },
    getFileType(file) {
      let res = "-";
      if (file.id) {
        res = file.type
          .split("/")
          .filter(item => {
            return item.length > 0;
          })
          .pop();
      } else if (file.annex_id) {
        res = file.file_type;
      }
      if (res === "x-zip-compressed") {
        res = "zip";
      }
      return res;
    },
    getVideoTime(file) {
      if (
        file.id &&
        file.response &&
        file.response.item &&
        file.response.item.video_time
      ) {
        return file.response.item.video_time + "s";
      } else if (file.annex_id) {
        return file.video_time + "s";
      } else {
        return "-";
      }
    },
    handleRemove(file) {
      if (file.id) {
        this.$refs.scatUpload.remove(file);
      } else if (file.annex_id) {
        this.handleCheckCanRemove(file.annex_id).then(res => {
          if (res) {
            let fileIdx = null;
            this.files.forEach((item, idx) => {
              if (item.annex_id && item.annex_id === file.annex_id) {
                fileIdx = idx;
              }
            });
            if (fileIdx !== "null") {
              this.files.splice(fileIdx, 1);
            }
          }
        });
      }
    },
    handleClose() {
      console.log("Dialog Closed!");
    },
    reloadValue() {
      console.log("reloadValue");
      this.files =
        this.value === undefined || this.value === null ? [] : this.value;
    },
    emitValue() {
      const res = this.files
        .map(item => {
          if (item.annex_id) {
            return item;
          } else if (item.response) {
            if (item.response.success) {
              return item.response.item;
            } else {
              Message.warning(
                item.response.error ? item.response.error : "未知错误"
              );
              return null;
            }
          } else {
            return null;
          }
        })
        .filter(item => {
          return item !== null;
        });
      this.$emit("input", res);
    },
    handleSelectionChange(val) {
      this.selected = val;
    }
  }
};
</script>

<style lang="scss">
@import "./index.scss";
</style>
