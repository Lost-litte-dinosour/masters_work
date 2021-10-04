const MyEvent = {
    data() {
        return {
            name_num: '',
            image_num: 1,
            image: [],
            index: '',
            choice: [],
            checkedValue: '',
        }
    },
    methods: {
        get_msg(image_num, name_num) {
            // console.log(image_num);
            // console.log(name_num);
            if (0 < name_num && name_num < 7) {
                var that = this;
                jQuery.ajax({
                    type: "GET",
                    url: "houduan/check.php?image_num=" + image_num + '&name_num=' + name_num,
                    dataType: 'json',
                    // methood: 'GET',
                    async: false,
                    success: function(data) {
                        // console.log(image_num);
                        // console.log(name_num);
                        var base = new Base64();
                        that.image = [];
                        that.choice = [];
                        if (data['error'] === '0' && data['msg'] == '') {
                            // alert(data['data']['image']);
                            var i;
                            // console.log(data['data']['image'][i]);
                            that.image.push(base.decode(data['data']['image'])); //采用了base64加密
                            // console.log(base.decode(data['data']['image']));
                            // console.log(data['data']['index']);
                            that.index = base.decode(data['data']['index']);
                            for (i = 0; i < data['data']['choice'].length; i++) {
                                that.choice.push(base.decode(data['data']['choice'][i]));
                            }
                            // console.log(data['data']['choice']);
                        } else {
                            alert('出错啦！请重新输入');
                        }
                    }
                });
                // console.log(this.index);
                // console.log(this.name);
                // console.log(this.image);
            } else {
                alert('名字数量要在1-6之间！请重新输入');
            }
        },
        my_submit() {
            var flag = 0;
            var that = this;
            var base = new Base64();
            jQuery.ajax({
                type: "POST",
                url: "houduan\\check_right.php",
                dataType: 'json',
                async: false,
                data: {
                    image: that.image,
                    index: that.index,
                    choice: that.checkedValue,
                },
                success: function(data) {
                    if (data['error'] === '0' && data['msg'] == '') {
                        // console.log(data['data']);
                        alert(base.decode(data['data']));
                        // that.methods.get_msg(that.image_num, that.name_num);
                        if (base.decode(data['data']) == '判断正确！') {
                            that.flag = 1;
                            // console.log(that.flag);
                        }
                    } else {
                        alert('出错啦！请重新输入');
                    }
                    // console.log(data);
                }
            });
            // console.log(this.flag);
            if (this.flag == 1) {
                // this.$options.methods.get_msg(this.image_num, this.name_num);
                var base = new Base64();
                if (0 < this.name_num && this.name_num < 7) {
                    var that = this;
                    jQuery.ajax({
                        type: "GET",
                        url: "houduan/check.php?image_num=" + this.image_num + '&name_num=' + this.name_num,
                        dataType: 'json',
                        // methood: 'GET',
                        async: false,
                        success: function(data) {
                            // console.log(image_num);
                            // console.log(name_num);
                            that.image = [];
                            that.choice = [];
                            if (data['error'] === '0' && data['msg'] == '') {
                                // alert(data['data']['image']);
                                var i;
                                // console.log(data['data']['image'][i]);
                                that.image.push(base.decode(data['data']['image']));
                                // console.log(data['data']['index']);
                                that.index = base.decode(data['data']['index']);
                                for (i = 0; i < data['data']['choice'].length; i++) {
                                    that.choice.push(base.decode(data['data']['choice'][i]));
                                }
                                // console.log(data['data']['choice']);
                            } else {
                                alert('出错啦！请重新输入');
                            }
                        }
                    });
                    // console.log(this.index);
                    // console.log(this.name);
                    // console.log(this.image);
                    this.flag = 0;
                    this.checkedValue = '';
                } else {
                    alert('名字数量要在1-6之间！请重新输入')
                }
            }
            // console.log(this.name);
            // console.log(that.checkedValue);
        },
        my_delete() {
            var base = new Base64();

            jQuery.ajax({
                type: "GET",
                url: "houduan\\delete.php",
                dataType: 'json',
                async: false,
                success: function(data) {
                    alert(base.decode(data['msg']));
                }
            });

            // alert('注销成功！');
            window.location.href = 'log.html';
        },

    },
    beforeCreate() {
        var base = new Base64();
        var that = this;
        jQuery.ajax({ // 第一次让php文件设置cookie，不获取数据
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
                    that.profile = base.decode(data['data']['profile']);
                    that.visible = base.decode(data['data']['visible']);
                    that.cookie_set = true;
                }
            }
        });
    },
}

const Event = Vue.createApp(MyEvent)
Event.mount('#big')



//base64加密解密函数

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