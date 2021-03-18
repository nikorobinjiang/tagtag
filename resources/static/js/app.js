var app = {
  loading: function (options) {
    options = options || {};
    options.title = options.title || '正在载入...';

    var html = [];
    html.push('<div class="am-modal am-modal-loading am-modal-no-btn" tabindex="-1" id="loading-modal">');
    html.push('<div class="am-modal-dialog">');
    html.push('<div class="am-modal-hd">' + options.title + '</div>');
    html.push('<div class="am-modal-bd">');
    html.push('<span class="am-icon-spinner am-icon-spin"></span>');
    html.push('</div>');
    html.push('</div>');
    html.push('</div>');

    return $(html.join('')).appendTo('body').modal()
      .on('closed.modal.amui', function () {
        $(this).remove();
      });
  },
  popup: function (options) {
    options = options || {};
    options.title = options.title || '';
    options.ele = options.ele || Date.parse(new Date()); //'popup-modal';
    options.url = options.url || '';
    options.onClose = options.onClose || function () {};
    var $loading = app.loading();
    var height = Math.ceil($(document).height() * 0.8);
    if (options.title == '创建广告') {
      height  = '500';
    }
    var width = Math.ceil($(document).width() * 0.8);
    if ($(document.body).width() < 850) width = 850;
    if (width > 1000) width = 1000;

    var html = [];
    html.push('<div class="am-modal am-modal-no-btn popup-modal" style="display:none" tabindex="-1" id="' + options.ele + '">');
    html.push('<div class="am-modal-dialog" style="height: ' + height + 'px;">');
    html.push('<div class="am-modal-hd">');
    html.push('<span>' + options.title + '</span><a href="javascript: void(0)" class="am-close am-close-spin gm-cancel">&times;</a>');
    html.push('</div>');
    html.push('<div class="am-modal-bd am-scrollable-vertical" style="resize:none;height:' + (height - 50) + 'px"></div>');
    html.push('</div>');
    html.push('</div>');

    $modal = $(html.join(''));
    $modal.appendTo('body')
    $modal.find('.am-modal-bd').load(options.url, function () {
      setTimeout(function () {
        $loading.modal('close');
      }, 1000);

      $('[data-am-selected]').selected();
      $modal.modal({
          closeViaDimmer: false,
          dimmer: true,
          width: width,
          height: height
        })
        .on('closed.modal.amui', function () {
          $(this).remove();
          options.onClose();
        });
    })
  },
  alert: function (options) {
    options = options || {};
    options.title = options.title || '提示';
    options.content = options.content || '提示内容';
    options.onConfirm = options.onConfirm || function () {};

    var html = [];
    html.push('<div class="am-modal am-modal-alert" tabindex="-1">');
    html.push('<div class="am-modal-dialog">');
    html.push('<div class="am-modal-hd">' + options.title + '</div>');
    html.push('<div class="am-modal-bd">' + options.content + '</div>');
    html.push('<div class="am-modal-footer"><span class="am-modal-btn">确定</span></div>');
    html.push('</div>');
    html.push('</div>');

    return $(html.join(''))
      .appendTo('body')
      .modal({
        closeViaDimmer: false
      })
      .on('closed.modal.amui', function () {
        options.onConfirm();
        $(this).remove();
      });
  },
  msg: function (options) {
    options = options || {};
    options.content = options.content || '提示内容';
    options.callback = options.callback || function () {};

    var html = [];
    html.push('<div class="am-modal am-modal-no-btn" tabindex="-1">');
    html.push('<div class="am-modal-dialog">');
    html.push('<div class="am-modal-bd">' + options.content + '</div>');
    html.push('</div></div>');

    $modal = $(html.join(''));
    $modal.appendTo('body')
      .modal({
        closeViaDimmer: false
      })
      .on('closed.modal.amui', function () {
        options.callback();
        $(this).remove();
      });

    setTimeout(function () {
      $modal.modal('close');
    }, 2500);
  },
  confirm: function (options) {
    options = options || {};
    options.title = options.title || '提示';
    options.content = options.content || '提示内容';
    options.onConfirm = options.onConfirm || function () {};
    options.onCancel = options.onCancel || function () {};

    var html = [];
    html.push('<div class="am-modal am-modal-confirm" tabindex="-1">');
    html.push('<div class="am-modal-dialog">');
    html.push('<div class="am-modal-hd">' + options.title + '</div>');
    html.push('<div class="am-modal-bd">' + options.content + '</div>');
    html.push('<div class="am-modal-footer">');
    html.push('<span class="am-modal-btn" data-am-modal-cancel>取消</span>');
    html.push('<span class="am-modal-btn" data-am-modal-confirm>确定</span>');
    html.push('</div>');
    html.push('</div>');
    html.push('</div>');

    return $(html.join('')).appendTo('body').modal({
      closeViaDimmer: false,
      relatedTarget: this,
      onConfirm: function () {
        options.onConfirm();
      },
      onCancel: function () {
        options.onCancel();
      }
    }).on('closed.modal.amui', function () {
      $(this).remove();
    });
  }
};
(function ($) {
  'use strict';
  // 侧边栏
  $(function () {
    var store = $.AMUI.store;
    if (store.enabled) {
      var collapsed = store.get('collapsed');
      if (typeof (collapsed) == 'undefined') {
        collapsed = '';
      }
      $('.am-collapse').on('open.collapse.amui', function () { //open方法被调用时立即触发
        var collapsed_id = $(this).attr('id');
        collapsed = collapsed + ',' + collapsed_id + ',', '';
        store.set('collapsed', collapsed);
      }).on('close.collapse.amui', function () { //close方法调用时立即触发
        var collapsed_id = $(this).attr('id');
        collapsed = collapsed.replace(new RegExp(',' + collapsed_id + ',', 'g'), '');
        store.set('collapsed', collapsed);
      });

      $('.admin-sidebar-list').find('.am-collapse').each(function (key, item) {
        var collapsed_id = $(item).attr('id');
        if (collapsed.indexOf(',' + collapsed_id + ',') > -1) {
          $(item).collapse('open');
        }
      });
    }
  });
  // 全屏
  $(function () {
    var $fullText = $('.admin-fullText');
    $('#admin-fullscreen').on('click', function () {
      $.AMUI.fullscreen.toggle();
    });
    $(document).on($.AMUI.fullscreen.raw.fullscreenchange, function () {
      $fullText.text($.AMUI.fullscreen.isFullscreen ? '退出全屏' : '开启全屏');
    });
  });
  // 自定义事件
  $(function () {
    // 加载页面
    $(document).on('click', '.gm-loadpage', function () {
      var $this = $(this);

      if ($this.hasClass('am-disabled')) return;
      $this.addClass('am-disabled');

      var $url = $(this).data('url');
      var $title = $(this).data('title');
      app.popup({
        url: $url,
        title: $title
      });

      $this.removeClass('am-disabled');
    });
    // post 提交表单
    $('body').on('click', '.gm-post', function () {
      var $this = $(this);

      if ($this.hasClass('am-disabled')) return;
      $this.addClass('am-disabled');

      var $form = $this.parents('form');
      var $data = $form.find('input,select,textarea').serialize();
      $.post($form.attr('action'), $data, '', 'JSON').success(function (ret) {
        if (ret.result == 'success') {
          $('.am-modal').modal('close');
          app.msg({
            content: '保存成功~',
            callback: function () {
              var load_type = $this.attr('data-reload-type');
              var edit_type = $this.attr('data-edit-type');
              if (load_type || edit_type) {
                var _href = window.location.href;
                var _arr = _href.split('?');
                _href = _arr[0];

                location.href = _href;
              } else {
                location.href = location.href;
                location.reload();
              }
            }
          });
        } else {
          app.alert({
            content: ret.message,
            onConfirm: function () {
              $this.removeClass('am-disabled');
            }
          });
        }
      }).error(function (ret) {
        app.alert({
          content: '系统错误',
          onConfirm: function () {
            $this.removeClass('am-disabled');
          }
        });
      });
      return false;
    });
    // 关闭当前弹窗
    $('body').on('click', '.gm-cancel', function () {
      //$(this).closest('.am-modal').modal('close');
      var _this = $(this);
      _this.closest('.am-modal').modal('close');

      //跳转过来的弹框 刷新去掉参数
      var load_type = _this.attr('data-reload-type');
      var edit_type = _this.attr('data-edit-type');
      if (load_type || edit_type) {
        var _href = window.location.href;
        var _arr = _href.split('?');
        _href = _arr[0];

        location.href = _href;
      }
      //_this.closest('.am-modal').remove();
      //$('.am-dimmer.am-active').hide();
    });

    // amazeui modal close bug fix
    $(document).on('click', '[gm-data-am-modal-close]', function (e) {
      e.preventDefault();
      var modal_now = $(this).parents('.am-modal:first');
      modal_now.modal();
    });

    $('body').on('click', '.gm-img-preview', function () {
      var _this = $(this),
        photos = {
          "data": [{
            "alt": "",
            "pid": 109,
            "src": "",
            "thumb": ""
          }]
        };
      photos.data[0].alt = _this.attr('alt');
      photos.data[0].src = _this.attr('src');
      if (!photos.data[0].src) return false;
      layer.photos({
        photos: photos,
        anim: 5
      });
    });

    $('body').on('click', '.gm-table-checkbox-all', function () {
      var _checked = $(this).is(':checked');
      var _table = $(this).closest('table');
      if (_checked) {
        _table.find('.gm-table-checkbox').prop("checked", true);;
      } else {
        _table.find('.gm-table-checkbox').prop("checked", false);;
      }
    });

    // 点击复制
    var seed = 0;
    $('body').on('click', '.clipboard[data-clipboard-text]', function (e) {
      var _this = this;
      var cbCLass = 'clipboard_app_' + seed++;
      _this.classList.add(cbCLass);

      var clipboard = new ClipboardJS('.' + cbCLass);
      clipboard.on('success', function (e) {
        e.clearSelection();
        // 释放内存
        clipboard.destroy();
        _this.classList.remove(cbCLass);
        layer.msg('复制成功');
      });

      var evt = document.createEvent("HTMLEvents");
      evt.initEvent("click", false, false);
      _this.dispatchEvent(evt);
      if (!_this.has_clipboarded) {
        _this.has_clipboarded = true;
        _this.click();
      }
    });
  });
})(jQuery);