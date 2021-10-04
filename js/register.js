const MyEvent = {
    data() {
        return {
            email: '',
            return_code: '',
            next_btn_able: false,
            user_code: '',
            out_date_time: 15,
            email: '',
            password: '',
            password_comfirm: '',
            phone_num: '',
            pet_name: '',
        }
    },
    methods: {
        connect_php(email) {
            if (email.length <= 32) {
                var base = new Base64();
                jQuery.ajax({
                    type: "POST",
                    url: "yanzhengma\\mail_ini.php",
                    dataType: 'json',
                    async: false,
                    data: {
                        'Email': base.encode(email),
                        'my_email': 'MzE0NDc5NDExMkBxcS5jb20=', //暂时采用base64加密
                    },
                    success: function(data) {
                        if (data['error'] == '') {
                            return_data = base.decode(data['code']);
                            // MyEvent.data.return_code = return_data;
                            alert('已经成功向' + email + '发送验证码，请查收！');
                            // console.log(MyEvent.data.return_code);
                            // console.log(MyEvent.data.user_code);
                        } else {
                            alert('邮箱输入有误！请重新输入');
                            return_data = base.decode(data['error']);
                        }
                    }
                });
                this.return_code = return_data;
                // console.log(this.return_code);
                let _this = this;
                setTimeout(function() {
                    // console.log('wuwu');
                    _this.jishi();
                }, 300000);
            } else {
                alert('输入邮箱太长或者有不合法字符！请重新输入')
            }
        },
        yanzheng() {
            if (this.user_code.length != 0 && this.user_code == this.return_code) {
                alert('邮箱验证成功！请继续输入内容');
                this.next_btn_able = true;
                // console.log(this.return_code);
                // console.log(this.user_code);
            } else if (this.user_code.length == 0) {
                alert('邮箱验证码为空！请输入验证码');
            } else {
                alert('邮箱验证码错误！请重新输入验证码');
                // this.return_code = '';
                // console.log(this.return_code);
                // console.log(this.user_code);
            }
        },
        jishi() {
            // console.log(this.return_code);
            // console.log('haha');
            this.return_code = '';
            // console.log(this.return_code);
        },
        return_to() {
            this.next_btn_able = false;
            this.return_code = '';
        },
        my_submit(email, pet_name, password, password_comfirm, phone_num, next_btn_able) {
            if (!next_btn_able) {
                alert('未输入正确验证码！');
            } else if (email.length == 0) {
                alert('邮箱不得为空！');
            } else if (pet_name.length >= 32) {
                alert('昵称不能超过32位！请重新输入')
            } else if (pet_name.length == 0) {
                alert('昵称不能为空！')
            } else if (password != password_comfirm) {
                alert('两次输入密码不相同！');
            } else if (email.length >= 32) {
                alert("邮箱不能超过32位！请重新输入");
            } else if (password.length >= 32) {
                alert('密码过长！请重新输入');
            } else if (!this.next_btn_able) {
                alert('验证码不正确！');
            } else if (password.length < 6) {
                alert('密码不能小于6位！')
            } else {
                var base = new Base64();
                jQuery.ajax({
                    type: "POST",
                    url: "houduan\\register.php",
                    dataType: 'json',
                    async: false,
                    data: {
                        'email': base.encode(email),
                        'pet_name': base.encode(pet_name),
                        'password': base.encode(password),
                        'phone_num': base.encode(phone_num),
                    },
                    success: function(data) {
                        if (data['error'] == '') {
                            // alert(data['msg']);
                            alert(base.decode(data['msg']));
                            if (base.decode(data['msg']) == '注册成功！') {
                                window.location.href = 'log.html';
                            }
                        } else {
                            return_data = base.decode(data['error']);
                        }
                    }
                });
            }
        }
    },
}

const Event = Vue.createApp(MyEvent)
Event.mount('#yanzheng')


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