
$(function () {
    $("input[type='checkbox'], input[type='radio']").uCheck();

    layui.use('layer', function () {
        var layer = layui.layer;
        $(document).on('click', '.layopenbtn', function () {

            $("#advertising-nav").collapse('open');
            $("#material-nav").collapse('close');
            $("#media-nav").collapse('close');
            $("#agent-nav").collapse('close');
            $("#domain-nav").collapse('close');

            var _url = $(this).attr('data-url');
            var _title = $(this).attr('data-title');
            layer.open({
                type: 2,
                title: _title,
                area: ['50%','500px'],
                content: _url,
                shadeClose: false
            });
        });

        //进入页面是否弹出创建框
        var open_create = $('#open_create').val();
        if(open_create){
            //$('.createbtn').trigger('click');
            var cbtn = $('.createbtn');
            var _url = cbtn.attr('data-url') + '?open_create_media_id='+open_create;
            var _title =  cbtn.attr('data-title');
            app.popup({url:_url,title:_title});

        //    更新data-reload-type
            $('.gm-post').attr('data-reload-type',1);
        }
        //是否弹出编辑框
        var open_edit = $('#open_edit');
        var _open_edit = open_edit.val();
        if(_open_edit){
            //$('#tr-'+_open_edit).find('.gm-loadpage').trigger('click');
            var _url = open_edit.attr('data-url') + '?edit_type=2';
            var _title = open_edit.attr('data-title');
            app.popup({url:_url,title:_title});
        }


        // 列页删除条目
        $(document).on('click', '.layer_btn_destroy', function () {
            var _btn = $(this);
            var _action = _btn.data('url');
            var _type = _btn.data('name');
            if(!_type){
                _type='素材';
            }
            layer.open({
                type: 1
                , title: false //不显示标题栏
                , closeBtn: false
                , area: '300px;'
                , shade: 0.8
                , id: 'LAY_layuipro' //设定一个id，防止重复弹出
                , btn: ['确定', '取消']
                , btnAlign: 'c'
                , moveType: 1 //拖拽模式，0或者1
                , content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">你确定要删除该'+_type+'？</div>'
                , yes: function (index, layero) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: _action,
                        method: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': $('input[name=_token]').val()
                        },
                        success: function (data) {
                            if (data.result == 'success') {
                                layer.msg(data.message, function () {
                                    window.parent.location.reload();
                                });
                            } else {
                                console.log(data);
                                layer.msg(data.message);
                            }
                        },
                        error: function (data, textStatus, errorThrown) {
                            layer.msg(data.message);
                        }
                    });
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });

        });
    });

    $('#layersavebtnmulti').on('click', function () {

        var _form = $(this).closest('form');

        var _url = $(this).attr('data-url');

        var _formdata = _form.serialize();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: _url,
            data: _formdata,
            method: 'POST',
            success: function (data) {
                if (data.result == 'success') {

                    layer.msg(data.message, function () {
                        window.location.reload();
                        window.parent.location.reload();

                    });

                } else {
                    layer.msg(data.message);

                }

            }
        });
    });

    //广告位页面媒体和结算选项联动
    $('#adpos_media_select').on('change',function(){

        var media_settlement = $('#adpos_media_select').find('option:selected').attr('data-settlement');

        var set_select = $('#adpos_settlement_select');
        var arr = media_settlement.split(',');
        var _html = '';
        $.each(arr, function (index,value) {

            set_select.find('option').remove();
            _html += '<option value="'+value+'">'+value+'</option>';

            set_select.append(_html);
        });
    });


    //tab切换
    $(document).on('click', '.cus-tabs-change', function () {
        var _this = $(this);
        _this.addClass('am-active').siblings('li').removeClass('am-active');

        // content 切换
        var _cur = _this.attr('data-title');
        $('.nor_box').hide();

        $('.' + _cur).show();

    });

    // 视频列表
    $(document).on('mouseenter', '.video-item-outer',function () {
        $(this).find('.video-head-icons').slideDown(100);
    }).on('mouseleave','.video-item-outer', function () {
        $(this).find('.video-head-icons').slideUp(100);
    });


});
