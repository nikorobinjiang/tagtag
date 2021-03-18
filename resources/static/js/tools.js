if (typeof String.prototype.startsWith != 'function') {
    String.prototype.startsWith = function (prefix) {
        return this.slice(0, prefix.length) === prefix;
    };
}

if (typeof String.prototype.endsWith != 'function') {
    String.prototype.endsWith = function (suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
}

Array.prototype.indexOf = function (val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == val) return i;
    }
    return -1;
};

var ArrayRemove = function (arr, val) {
    var index = arr.indexOf(val);
    if (index > -1) {
        arr.splice(index, 1);
    }
};

var tools = {};

// ajax 加载页
tools.loadPage = function (_url, nodeId, extData) {
    // 一部加载页面对分页按钮处理 ,id 带有 #
    var _load_page = function (__url) {
        var _query_data = tools.searchToObject();
        if (extData) {
            _query_data = Object.assign(_query_data, extData);
        }
        $.ajax({
            url: __url,
            type: 'GET',
            data: _query_data,
            dataType: 'html'
        }).done(function (data) {
            // console.log("success");
            $(nodeId).html(data);
            $(nodeId + " .am-pagination a").each(function (index, el) {
                var elhref = $(el).attr('href');
                if (elhref == '#') {
                    elhref = __url;
                    $(el).attr('data-href', __url);
                } else {
                    $(el).attr('data-href', elhref);
                }
                $(el).attr('href', 'javascript:void(0)');
            });
        }).fail(function () {
            // console.log("error");
        }).always(function () {
            // console.log("complete");
        });
    };
    _load_page(_url);
    $(nodeId).delegate('a[data-href]', 'click', function () {
        _load_page($(this).attr('data-href'));
    });
};

tools.searchToObject = function () {
    var pairs = window.location.search.substring(1).split("&"),
        obj = {},
        pair,
        i;

    for (i in pairs) {
        if (pairs[i] === "" || typeof (pairs[i]) !== 'string') {
            continue;
        }
        pair = pairs[i].split("=");
        key = decodeURIComponent(pair[0]);
        val = decodeURIComponent(pair[1]);
        if (key.endsWith('[]')) {
            if (obj[key] === undefined) {
                obj[key] = [];
            }
            obj[key].push(val);
        } else {
            obj[key] = val;
        }
    }
    return obj;
};

tools.urlBuild = function (param, key, encode) {
    if (param == null) return '';
    var paramStr = '';
    var t = typeof (param);
    if (t == 'string' || t == 'number' || t == 'boolean') {
        paramStr += '&' + key + '=' + ((encode == null || encode) ? encodeURIComponent(param) : param);
    } else {
        for (var i in param) {
            var k = key == null ? i : key + (param instanceof Array ? '[' + i + ']' : '.' + i);
            paramStr += this.urlBuild(param[i], k, encode);
        }
    }
    return paramStr;
};

// 多图上传 传入空白 div 标签 id 和 data，没有 data 不用传
tools.multiImage = function (holder, urls, data) {
    var _input_ids = holder + '_ids';
    var _reloader = holder + '-reload';
    var _destroyer = holder + '-destory';
    var _listor = holder + '_list';
    var _urls = Object.assign({
        remvoe_check: '/advertising/helper/annex_remove_check',
        upload: '/upload'
    }, urls);

    var _upload_htmle_box = '<div class="layui-upload">' +
        '<button type="button" class="am-btn am-btn-sm tool-upload-select"' + '>选择多文件</button>' +
        $("#material_annex_data").html() +
        '<button type="button" class="layui-btn tool-upload-submit">开始上传</button>' +
        '</div>';
    $("#material_annex_data").remove();
    $('#' + holder).html(_upload_htmle_box);

    layui.use('upload', function () {
        var $ = layui.jquery;
        var upload = layui.upload;

        // 多文件列表示例
        var uploadListView = $('#' + _listor);
        // 编辑时配置
        uploadListView.on('click', '.' + _destroyer, function (e) {
            console.log('---')
            e.preventDefault();
            var _btn = $(this);
            var _id = _btn.data('id');
            var _tr = _btn.parents('tr');

            var _type = _btn.attr('data-name');
            if(!_type){
                _type='素材';
            }
            // 删除图片
            layer.open({
                type: 1,
                title: false // 不显示标题栏
                    ,
                closeBtn: false,
                area: '300px;',
                shade: 0.8,
                id: 'LAY_layuipro' // 设定一个id，防止重复弹出
                    ,
                btn: ['确定', '取消'],
                btnAlign: 'c',
                moveType: 1 // 拖拽模式，0或者1
                    ,
                content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">' +
                    '你确定要删除该'+_type+'？' +
                    '</div>',
                yes: function (index, layero) {
                    $.ajax({
                        url: _urls.remvoe_check,
                        type: 'GET',
                        data: {
                            'id': _id,
                            'type': 'material_annex'
                        },
                        dataType: 'json'
                    }).done(function (data) {
                        if (data.result == 'success') {
                            _tr.remove();
                            var ids = [];
                            if ($('#' + _input_ids).val().length > 0) {
                                ids = $('#' + _input_ids).val().split(',');
                            }
                            ArrayRemove(ids, _id);
                            $('#' + _input_ids).val(ids.join(','));
                        } else {
                            layer.msg(data.message);
                        }
                    }).fail(function () {
                        console.log("error");
                    }).always(function () {
                        console.log("complete");
                    });
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });

        // 上传时配置 多图上传
        var uploadListIns = upload.render({
            elem: $('#' + holder + ' .tool-upload-select'),
            url: _urls.upload,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            accept: 'file',
            multiple: true,
            auto: false,
            bindAction: $('#' + holder + ' .tool-upload-submit'),
            choose: function (obj) {
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列

                //读取本地文件
                obj.preview(function (index, file, result) {
                    var tr = $(['', '<tr id="upload-' + index + '">', '<td>', '<img src="' + result + '" alt="' + file.name + '">', '</td>', '<td>' + file.type.split('/').pop() + '</td>', '<td> - </td>', '<td>' + (file.size / 1014).toFixed(1) + 'kb</td>', '<td>等待上传</td>', '<td>', '<button class="layui-btn layui-btn-mini ' + _reloader + ' layui-hide">重传</button>', '<button class="layui-btn layui-btn-mini layui-btn-danger ' + _destroyer + '">删除</button>', '</td>', '</tr>'].join(''));

                    // 单个重传
                    tr.find('.' + _reloader).on('click', function () {
                        obj.upload(index, file);
                    });

                    // 上传时删除
                    tr.find('.' + _destroyer).on('click', function () {
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    uploadListView.append(tr);
                });
            },
            done: function (res, index, upload) {
                if (res.success) { //上传成功
                    var tr = uploadListView.find('tr#upload-' + index)
                    var tds = tr.children(); // 当前 td item
                    tds.eq(2).html(res.item.file_height + 'x' + res.item.file_width);
                    tds.eq(4).html('<span style="color: #5FB878;">上传成功</span>');
                    tds.eq(5).html('');
                    // tds.eq(5).find('.layui-btn-danger').addClass('material-annex-destory'); //删除操作 
                    var ids = [];
                    if ($('#' + _input_ids).val().length > 0) {
                        ids = $('#' + _input_ids).val().split(',');
                    }
                    ids.push(res.id);
                    $('#' + _input_ids).val(ids.join(','));
                    return delete this.files[index]; //删除文件队列已经上传成功的文件
                }
                this.error(index, upload);
            },
            error: function (index, upload) {
                var tr = uploadListView.find('tr#upload-' + index),
                    tds = tr.children();
                tds.eq(4).html('<span style="color: #FF5722;">上传失败</span>');
                tds.eq(5).find('.' + _reloader).removeClass('layui-hide'); //显示重传
            }
        });
    });

}