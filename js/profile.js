const MyEvent = {
    data() {
        return {
            // imgsrc: "./image/user_image/moren.png",
            // checkedValue: "未选择",
            // cookie_set: false,
        }
    },
    computed: {

    },
    methods: {
        my_delete() {
            var base = new Base64();

            jQuery.ajax({
                type: "GET",
                url: "houduan\\delete.php",
                dataType: 'json',
                async: false,
                success: function(data) {
                    // alert('ahha');
                    console.log(base.decode(data['msg']));
                    alert(base.decode(data['msg']));
                }
            });

            // alert('注销成功！');
            window.location.href = 'log.html';
        },
        upTx() {
            if (document.getElementById("txUrl").files.length > 0) {
                var formData = new FormData();
                var file = document.getElementById("txUrl").files[0];
                var beat64Url;
                // console.log(file);

                //只支持jpg、jpeg、png、bmp、gif的图片格式，且大小不能超过1MB
                if ((file.type == 'image/jpeg' || file.type == 'image/png' || file.type == 'image/bmp' || file.type == 'image/jpg' || file.type == 'image/gif') && file.size < 1048577) {
                    formData.append('user_image', file);
                    var _this = this;
                    jQuery.ajax({
                        type: "POST",
                        url: "houduan\\profile.php",
                        contentType: false,
                        // 告诉jQuery不要去设置Content-Type请求头
                        processData: false,
                        // 告诉jQuery不要去处理发送的数据
                        dataType: 'json',
                        async: false,
                        data: formData,
                        success: function(data) {
                            // alert(data['data']);
                            if (data['data'] == '上传成功！(如果刷新后未改变头像请按shift+F5刷新缓存)' || data['data'] == '上传失败' || data['data'] == '上传类型不正确或者超出大小(最大为1MB),请重新上传！') { //防止XSS攻击
                                alert(data['data']); //本来这里是要通过if判断来防止XSS攻击的，但是不知道为什么一直刷新不出来，所以只能先alert了
                                // console.log(data); //data能返回结果中的数据
                                if (data['data'] == '上传成功！(如果刷新后未改变头像请按shift+F5刷新缓存)') {
                                    // alert();
                                    location.reload();
                                }
                            } else if (data['data'] == '您的Cookie信息已经被更改，请重新登录！' || data['data'] == '您未登录！') {
                                window.location.href = 'log.html';
                            }
                        }
                    });
                    // axios({
                    //         url: "houduan/profile.php",
                    //         method: "POST",
                    //         data: formData
                    //     })
                    //     .then(res => {

                    //     })
                } else if (file.size > 1048577) {
                    alert('只能上传小于1MB的图片！');
                } else {
                    alert('只支持上传格式为jpg、jpeg、png、bmp、gif的图片！');
                }
            } else {
                alert('并未选择文件！');
            }
        },
        f_visible() {
            var base = new Base64();
            var that = this;
            jQuery.ajax({
                type: "GET",
                url: "houduan/visible.php?visible=" + that.checkedValue,
                dataType: 'json',
                async: false,
                success: function(data) {
                    alert(base.decode(data['msg']));
                }
            });
        }
    },
    beforeCreate() {
        var base = new Base64();
        var that = this;
        jQuery.ajax({ // 第一次让php文件设置Cookie，不获取数据(这个bug找了好久，哭了)
            type: "GET",
            url: "houduan/index.php",
            dataType: 'json',
            async: false,
        });
        jQuery.ajax({
            type: "GET",
            url: "houduan/index.php",
            dataType: 'json',
            async: false,
            success: function(data) {
                if (base.decode(data['msg']) == '您的Cookie信息已经被更改，请重新登录！') {
                    alert(base.decode(data['msg']));
                    window.location.href = 'log.html';
                } else if (base.decode(data['msg']) == '您未登录！') {
                    alert(base.decode(data['msg']));
                    that.cookie_set = false;
                } else if (data['data']['pet_name'] != null && data['data']['profile'] != null && data['data']['visible'] != null) {
                    that.user = base.decode(data['data']['pet_name']);
                    that.imgsrc = base.decode(data['data']['profile']);
                    that.checkedValue = base.decode(data['data']['visible']);
                    that.cookie_set = true;
                }
            },
        });
        console.log(this.cookie_set);
    },
}

const Event = Vue.createApp(MyEvent)
Event.mount('#profile')

function Base64() {

    // private property  
    _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

    // public method for encoding  
    this.encode = function(input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;
        input = _utf8_encode(input);
        while (i < input.length) {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);
            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;
            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }
            output = output +
                _keyStr.charAt(enc1) + _keyStr.charAt(enc2) +
                _keyStr.charAt(enc3) + _keyStr.charAt(enc4);
        }
        return output;
    }

    // public method for decoding  
    this.decode = function(input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        while (i < input.length) {
            enc1 = _keyStr.indexOf(input.charAt(i++));
            enc2 = _keyStr.indexOf(input.charAt(i++));
            enc3 = _keyStr.indexOf(input.charAt(i++));
            enc4 = _keyStr.indexOf(input.charAt(i++));
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
            output = output + String.fromCharCode(chr1);
            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
        }
        output = _utf8_decode(output);
        return output;
    }

    // private method for UTF-8 encoding  
    _utf8_encode = function(string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";
        for (var n = 0; n < string.length; n++) {
            var c = string.charCodeAt(n);
            if (c < 128) {
                utftext += String.fromCharCode(c);
            } else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            } else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }
        return utftext;
    }

    // private method for UTF-8 decoding  
    _utf8_decode = function(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;
        while (i < utftext.length) {
            c = utftext.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            } else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            } else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    }
}