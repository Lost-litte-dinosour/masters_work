<?php
//[*邮件发送逻辑处理过程*系统核心配置文件*]
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//生成6位随机验证码
function codestr()
{
    $arr = array_merge(range('a', 'b'), range('A', 'B'), range('0', '9'));
    shuffle($arr);
    $arr = array_flip($arr);
    $arr = array_rand($arr, 6);
    $res = '';
    foreach ($arr as $v) {
        $res .= $v;
    }
    return $res;
}

$Email = base64_decode($_POST['Email']);
$my_email = base64_decode($_POST['my_email']);

//调用PHPMailer组件，此处是你自己的目录，需要改写。
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$mail = new PHPMailer(true);       // Passing `true` enables exceptions
try {
    $my_email = '3144794112@qq.com';
    //服务器配置
    $mail->CharSet = "UTF-8";                     //设定邮件编码
    $mail->SMTPDebug = 0;                        // 调试模式输出
    $mail->isSMTP();                             // 使用SMTP
    $mail->Host = '  smtp.qq.com';            // SMTP服务器
    $mail->SMTPAuth = true;                      // 允许 SMTP 认证
    $mail->Username = $my_email;              // SMTP 用户名  即邮箱的用户名
    $mail->Password = 'kjdkzpbibspmdffh';        // SMTP 密码  部分邮箱是授权码(例如163邮箱)
    $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
    $mail->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

    $mail->setFrom($my_email, '迷失的蓝色小恐龙');  //发件人（以QQ邮箱为例）

    $temp_name = substr($Email, 0, strpos($Email, '@'));
    $mail->addAddress($Email, $temp_name);  // 收件人（$Email可以为变量传值，也可为固定值）
    //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
    $mail->addReplyTo($my_email, 'info'); //回复的时候回复给哪个邮箱 建议和发件人一致
    //$mail->addCC('cc@example.com');                    //抄送
    //$mail->addBCC('bcc@example.com');                    //密送

    //发送附件
    // $mail->addAttachment('../xy.zip');         // 添加附件
    // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名

    $yanzhen = codestr();  //此处为调用随机验证码函数（按照自己实际函数名改写）

    //Content
    $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
    $mail->Subject = '迎新系统注册身份登录验证';
    $mail->Body = '<h1>欢迎注册迎新系统</h1><h3>您的身份验证码是：<span>' . $yanzhen . '</span></h3>' . '<br>' . '<h4>有效时间为10分钟，如果不是本人操作，请忽略</h4>' . '<br>' . '本邮件是自动发送，请勿回复谢谢！' . '<br>' . '有问题请加QQ 3144794112' . '<br>' . date('Y-m-d H:i:s');
    $mail->AltBody = '欢迎使用迎新系统注册,您的身份验证码是：' . $yanzhen . date('Y-m-d H:i:s');


    $mail->send();
    $array = array(
        "status" => "200",
        "reason" => "Return Tips",
        "code" => base64_encode($yanzhen),
        "error" => "",
    );
    echo json_encode($array);
} catch (Exception $e) {
    $array = array(
        "status" => "200",
        "reason" => "Return Tips",
        "code" => '',
        "error" => $mail->ErrorInfo,
    );
    echo json_encode($array);
}

?>