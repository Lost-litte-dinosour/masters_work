const MyEvent = {
    data() {
        return {
            email: '',
            password: '',
            password_flag: true,
        }
    },
    computed: {
        my_type() {
            if (this.password_flag) {
                return "password";
            } else {
                return "text";
            }
        },
        my_image() {
            if (this.password_flag) {
                return "image/eye1.png";
            } else {
                return "image/eye0.png";
            }
        }
    },
    methods: {
        password_change() {
            this.password_flag = !this.password_flag;
        },
        log(email, password) {
            console.log(email)
            if (email.length == 0) {
                alert('邮箱不得为空！');
            } else if (password.length < 6) {
                alert('密码不能小于6位！');
            } else if (email.length > 32) {
                alert('邮箱不能超过32位！请重新输入');
            } else {
                jQuery.ajax({
                    type: "POST",
                    url: "houduan\\log.php",
                    dataType: 'json',
                    async: false,
                    data: {
                        'email': email,
                        'password': password,
                    },
                    success: function(data) {
                        if (data['error'] == '') {
                            alert(data['msg']);
                            if (data['msg'] == '登录成功！') {
                                window.location.href = 'index.html';
                            }
                        } else {
                            alert(data['error']);
                        }
                    }
                });
            }
        }
    },
}

const Event = Vue.createApp(MyEvent)
Event.mount('#denglu')