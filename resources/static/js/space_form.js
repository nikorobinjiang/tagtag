$(function () {

  /*$('body>#modal-create-style').remove()
   _tmp = $('#modal-create-style-raw').html();
   $('#modal-create-style-raw').remove();*/

  //            $('body').append('<div class="am-modal am-modal-no-btn" tabindex="-1" id="modal-create-style">' + _tmp + '</div>');


  // 是否需要视频封面选择
  $(document).off('click', '.toggle-v-cover-btn');
  $(document).off('click', '.delete-style-item');
  $(document).off('click', '.view-style-item');

  $(document).on('click', '.toggle-v-cover-btn', function () {

    var _this = $(this).find('.icon-toggle');
    if (_this.hasClass('am-icon-toggle-on')) {
      _this.addClass('am-icon-toggle-off').removeClass('am-icon-toggle-on');

      _this.closest('.video-cover-outer').find('.video-cover-box').hide().find('.video-has-cover').val('');

    } else if (_this.hasClass('am-icon-toggle-off')) {
      _this.removeClass('am-icon-toggle-off').addClass('am-icon-toggle-on');
      _this.closest('.video-cover-outer').find('.video-cover-box').show().find('.video-has-cover').val(1);

    }

  });

  //            选择媒体 关联结算方式
  var media_select = $('#media_select');
  media_select.on('change', function () {

    var settlement_select = $('#settlement_select');
    settlement_select.html('');

    var set_str = $(this).find('option:selected').attr('data-settlement');
    var set_arr = set_str.split(',');
    var _selected = settlement_select.data('value').split(',');
    var _html = '';
    $.each(set_arr, function (idx, val) {
      _html += '<option value=' + val + ((_selected.indexOf(val) !== -1) ? ' selected' : '') + ' >' + val + '</option>';
    });
    settlement_select.html(_html);
  });
  media_select.trigger('change');

  //广告位里新建样式模态框打开清空上次数据
  $(document).on('click', '#create-style-btn', function () {
    $('#style-name').val('');
    $('#enumerated_value').val('');
    $('#style-id').val('');
    $('#all-need-style-content').html('');
    $('#modal-create-style').show().css('top', 0);
  });

  //模态框保存
  $(document).on('click', '#layersavebtn-inner', function () {

    //必填项
    var stylename = $('#style-name').val();
    if (stylename == '') {
      layer.msg('必填项不能为空');
      return;
    }

    var enumerated_value = $('#enumerated_value').val();

    var styleid = $('#style-id').val();


    var need_json = {};
    var need_text_json = [];
    var need_img_json = [];
    var need_video_json = [];

    var _all = $('#all-need-style-content');

    //必填check
    var check_input = _all.find(':text');
    /*$.each(check_input,function(){
     if($(this).val()==''){
     layer.msg('必填项不能为空');exit;
     }
     });*/


    var all_text = _all.find('.input-need-text');
    var all_img = _all.find('.input-need-img');
    var all_video = _all.find('.input-need-video');

    $.each(all_text, function (i) {

      var _this = $(this);
      var tname = _this.find('.text-name').val();
      var tmax = _this.find('.text-max-length').val();


      var item_text_json = {};
      item_text_json.name = tname;
      item_text_json.max_length = tmax;

      need_text_json.push(item_text_json);
    });

    $.each(all_img, function (i) {
      var _this = $(this);
      var item_img_json = {};
      item_img_json.name = _this.find('.img-name').val();
      item_img_json.format = _this.find('.img-format').val();
      item_img_json.max = _this.find('.img-max').val();
      item_img_json.width = _this.find('.img-width').val();
      item_img_json.height = _this.find('.img-height').val();
      item_img_json.size_unit = _this.find('.img-size-unit').val();
      //item_img_json.amount = _this.find('.img-amount').val();

      need_img_json.push(item_img_json);
    });

    $.each(all_video, function (i) {
      var _this = $(this);
      var item_video_json = {};
      item_video_json.name = _this.find('.video-name').val();
      item_video_json.format = _this.find('.video-format').val();
      item_video_json.max = _this.find('.video-max').val();
      item_video_json.width = _this.find('.video-width').val();
      item_video_json.height = _this.find('.video-height').val();
      item_video_json.size_unit = _this.find('.video-size-unit').val();

      //cover
      item_video_json.has_cover = _this.find('.video-has-cover').val();
      item_video_json.cover_name = _this.find('.video-cover-name').val();
      item_video_json.cover_format = _this.find('.video-cover-format').val();
      item_video_json.cover_max = _this.find('.video-cover-max').val();
      item_video_json.cover_width = _this.find('.video-cover-width').val();
      item_video_json.cover_height = _this.find('.video-cover-height').val();
      item_video_json.cover_size_unit = _this.find('.video-cover-size-unit').val();

      need_video_json.push(item_video_json);
    });

    need_json.text = need_text_json;
    need_json.img = need_img_json;
    need_json.video = need_video_json;
    //console.log(need_json);

    var need_josn_include_name = {};
    need_josn_include_name.style_id = styleid;
    need_josn_include_name.style_name = stylename;
    need_josn_include_name.style_info = need_json;
    need_josn_include_name.enumerated_value = enumerated_value;

    //回填到样式简介列表
    var _summary_html = '<div class="item-style-need" id="label-style-' + styleid + '"><span class="am-badge am-badge-warning view-style-item" style="cursor: pointer;">' + stylename + '</span>' +
      '<span class="cus-icon-view view-style-item" title="样式详情"></span>' +
      '<input type="hidden" value="' + stylename + '">' +
      '<input type="hidden" class="style-need-json" name="data[style_need_json][]" value=\'' + JSON.stringify(need_josn_include_name) +
      '\'><span class="am-icon-edit edit-style-item"></span><span class="am-icon-trash delete-style-item"></span></div>';

    var style_summary = $('#style-summary-box');
    style_summary.append(_summary_html);

    // 修改 将原来的删除
    style_summary.find('.delete_item').remove();
    //        $('#style-summary-box').append(_summary_html);

    //close modal
    $modal = $(this).attr('data-title');
    //                $('#modal-'+$modal).modal('close');
    $('#modal-' + $modal).hide();
  });

  //删除样式
  $(document).on('click', '.delete-style-item', function () {

    var _this = $(this);
    app.confirm({
      content: '确定删除样式吗?',
      onConfirm: function () {
        _this.closest('.item-style-need').remove();
      }
    });
  });

  //查看样式详情 回填json解析的数据
  $(document).on('click', '.view-style-item', function () {
    var _self = $(this);

    var _all_need_style_content = $('#all-need-style-content-view');

    _all_need_style_content.html('');

    var _style_need_json = $(this).closest('.item-style-need').find('.style-need-json').val();
    _style_need_json = JSON.parse(_style_need_json);

    var _style_id = _style_need_json.style_id;
    var _style_name = _style_need_json.style_name;
    var _style_info = _style_need_json.style_info;
    var _enumerated_value = _style_need_json.enumerated_value;

    $('#style-name-view').html(_style_name);
    $('#enumerated_value-view').html(_enumerated_value);
    $.each(_style_info.text, function () {


      var _this = $(this);
      //用jquery获取模板
      var _html = $('#cont-need-text-view').html();
      //预编译模板
      var template = Handlebars.compile(_html);
      //匹配json内容
      var html = template(_this[0]);
      // 输入模板
      _all_need_style_content.append(html);
    });
    $.each(_style_info.img, function () {

      var _this = $(this);

      //用jquery获取模板
      var _html = $('#cont-need-img-view').html();
      //预编译模板
      var template = Handlebars.compile(_html);
      //匹配json内容
      var html = template(_this[0]);
      // 输入模板
      _all_need_style_content.append(html);
    });
    $.each(_style_info.video, function () {

      var _this = $(this);

      //用jquery获取模板
      var _html = $('#cont-need-video-view').html();
      //预编译模板
      var template = Handlebars.compile(_html);
      //匹配json内容
      var html = template(_this[0]);
      // 输入模板
      _all_need_style_content.append(html);
    });

    //                手动弹框
    $('#modal-view-style').show().css('top', 0);
  });

  //修改样式 回填json解析的数据
  $(document).on('click', '#style-summary-box .edit-style-item', function () {

    var _self = $(this);

    var _all_need_style_content = $('#all-need-style-content');

    _all_need_style_content.html('');

    var _style_need_json = $(this).closest('.item-style-need').find('.style-need-json').val();
    _style_need_json = JSON.parse(_style_need_json);

    var _style_id = _style_need_json.style_id;
    var _style_name = _style_need_json.style_name;
    var _style_info = _style_need_json.style_info;
    var _enumerated_value = _style_need_json.enumerated_value;

    $('#style-name').val(_style_name);
    $('#style-id').val(_style_id);
    $('#enumerated_value').val(_enumerated_value);

    $.each(_style_info.text, function () {


      var _this = $(this);
      //用jquery获取模板
      var _html = $('#cont-need-text').html();
      //预编译模板
      var template = Handlebars.compile(_html);
      //匹配json内容
      var html = template(_this[0]);
      // 输入模板
      _all_need_style_content.append(html);
    });
    $.each(_style_info.img, function () {

      var _this = $(this);

      //用jquery获取模板
      var _html = $('#cont-need-img').html();
      //预编译模板
      var template = Handlebars.compile(_html);
      //匹配json内容
      var html = template(_this[0]);
      // 输入模板
      _all_need_style_content.append(html);
    });
    $.each(_style_info.video, function () {

      var _this = $(this);

      //用jquery获取模板
      var _html = $('#cont-need-video').html();
      //预编译模板
      var template = Handlebars.compile(_html);
      //匹配json内容
      var html = template(_this[0]);
      // 输入模板
      _all_need_style_content.append(html);
    });

    //                下拉框手动选中当前
    var _format1 = $('.video-cover-format');
    $.each(_format1, function () {
      $(this).val($(this).attr('data-value'));
    });
    var _format2 = $('.img-format');
    $.each(_format2, function () {
      $(this).val($(this).attr('data-value'));
    });


    //修改标记 修改完之后删除原来的
    $('#style-summary-box').find('.item-style-need').removeClass('delete_item');
    _self.closest('.item-style-need').addClass('delete_item');

    //                手动弹框
    $('#modal-create-style').show().css('top', 0);
  });

  //模态框关闭
  $(document).on('click', '#layercancelbtn-inner', function () {

    $modal = $(this).attr('data-title');

    //                $('#modal-'+$modal).modal('close');
    $('#modal-' + $modal).hide().css('top', '-1000px');

    //        将删除标记去掉
    $('#style-summary-box').find('.delete_item').removeClass('delete_item');

  });
  // 内部弹框关闭当前弹窗
  $('body').on('click', '.gm-cancel-inner', function () {

    $(this).closest('.am-modal').hide().css('top', '-1000px');

    //        将删除标记去掉
    $('#style-summary-box').find('.delete_item').removeClass('delete_item');

  });

  //添加素材要求
  $(document).on('click', '.add-need-btn', function () {

    var type = '#cont-' + $(this).attr('data-type');

    //用jquery获取模板
    var _html = $(type).html();
    //预编译模板
    var template = Handlebars.compile(_html);
    //匹配json内容
    var html = template({});
    // 输入模板
    $('#all-need-style-content').append(html);

    /* //用jquery获取模板
     var tpl   =  $("#tpl").html();

     //预编译模板
     var template = Handlebars.compile(tpl);
     //模拟json数据
     var context = { name: "zhaoshuai", content: "learn Handlebars"};
     //匹配json内容
     var html = template(context);
     //输入模板
     $('#ttt').html(html);*/
  });

  //            删除素材要求
  $(document).on('click', '.am-icon-minus-circle', function () {

    $(this).closest('.am-form-group').remove();
  });

});