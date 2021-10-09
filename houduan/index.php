<?php
session_start();
$link = mysqli_connect('localhost', 'root', '1234') or die("数据库连接失败！");

//如果是直接进入index.html,没有设置session且之前的cookie没有过期，则通过cookie设置好session。   安全隐患：如果恶意用户改了cookie   解决方法：设置密码和用户名一起，进行验证
//如果有设置Session且没有设置cookie或者设置了cookie并且和session中的不一样，那么需要通过session去设置cookie以方便下次直接登录
//Cookie以加密方式存储和传输，Session以明文存储

if (!(isset($_SESSION['password']) && isset($_SESSION['email'])) && isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
    $_SESSION['password'] = base64_decode($_COOKIE['password']);
//    $_SESSION['profile'] = base64_decode($_COOKIE['profile']);
//    $_SESSION['visible'] = base64_decode($_COOKIE['visible']);
    $_SESSION['email'] = base64_decode($_COOKIE['email']);
//    && (!(isset($_COOKIE['password']) && isset($_COOKIE['email'])) || (base64_decode($_COOKIE['password']) != $_SESSION['password'] && base64_decode($_COOKIE['email']) != $_SESSION['email']))
} elseif (isset($_SESSION['password']) && isset($_SESSION['email']) && $_SESSION['password']!='' && $_SESSION['password']!='') { //总之就是如果设置了session且不为空，cookie就要和session一样
    setcookie('password', base64_encode($_SESSION['password']), time() + 30 * 60); //采用base64加密存储
    setcookie('email', base64_encode($_SESSION['email']), time() + 30 * 60);
//    $temp = $_SESSION['pet_name'];
} else {
    $array = array(
        "error" => "0",
        "msg" => base64_encode('您未登录！'),
        "data" => array(),
        "redirect" => "",
    );
    echo json_encode($array);
}


if (isset($_COOKIE['password']) && isset($_COOKIE['email'])) {
    my_error($link, __LINE__, "use mydatabase");
    $res = my_error($link, __LINE__, 'select * from masters_work_user where email="' . filter(base64_decode($_COOKIE['email'])) . '";'); //先对邮箱进行SQL过滤，再拼接进SQL语句中查询
    $temp = mysqli_fetch_assoc($res);
//    echo json_encode($temp);
    if (@$temp['password'] == @base64_decode($_COOKIE['password'])) { //如果cookie中的信息验证成功，则进行查询和数据返回
        $array = array(
            "error" => "0",
            "msg" => "",
            "data" => array('pet_name' => base64_encode($temp['pet_name']), 'profile' => base64_encode($temp['profile']), 'visible' => base64_encode($temp['visible'])),  //采用base64加密传输(虽然没什么用)  //昵称、头像图片信息都要从重新查询的结果中来
            "redirect" => "",
        );
        echo json_encode($array);
    } else {
        $array = array(
            "error" => "0",
            "msg" => base64_encode('您的Cookie信息已经被更改，请重新登录！(大佬别搞了球球了)'),
            "data" => array(),
            "redirect" => "",
        );
        echo json_encode($array);
    }

}

function my_error($link, $my_line, $sql)
{
    $res = mysqli_query($link, $sql);
    if (!$res) {
        $array = array(
            "status" => "200",
            "reason" => "Return Tips",
            "msg" => base64_encode("出错啦!请重新输入"),
//            "msg" => "出错啦！行号为" . $my_line . "<br/>" . "SQL执行错误，错误编号为：" . mysqli_errno($link) . "<br/>" . "SQL执行错误，错误信息为：" . mysqli_error($link) . "<br/>", //该行为测试时用，使用时不能将sql错误返回给用户
//        "error" => "",
        );
        echo json_encode($array);
        exit();
    }
    return $res;
}

function filter($str) //SQL过滤函数 已经防止双写绕过
{
    if (empty($str)) return "";
    $str = htmlspecialchars($str);
    $str = my_replace( 'ASCII', "", $str);
    $str = my_replace( 'ASCII 0x0d', "", $str);
    $str = my_replace( 'alert(', "", $str);
    $str = my_replace( 'ASCII 0x0a', "", $str);
    $str = my_replace( 'sysopen', "", $str);
    $str = my_replace( 'system', "", $str);
    $str = my_replace( 'ASCII 0x08', "", $str);
    $str = my_replace("<SCRIPT>", "", $str);
    $str = my_replace("</SCRIPT>", "", $str);
    $str = my_replace("<script>", "", $str);
    $str = my_replace("</script>", "", $str);
    $str = my_replace("select","",$str);
    $str = my_replace("union","",$str);
    $str = my_replace("insert","",$str);
    $str = my_replace("delete","",$str);
    $str = my_replace("update","",$str);
    $str = my_replace("DROP","",$str);
    $str = my_replace("create","",$str);
    $str = my_replace("modify","",$str);
    $str = my_replace("<br />",chr(13),$str);
    $str = my_replace("CSS","'",$str);
    $str = my_replace("<!--","",$str);
    $str = my_replace("passwd","",$str);
    $str = my_replace("password","",$str);
    $str = my_replace("Array","",$str);
    $str = my_replace("or 1='1'","",$str);
    $str = my_replace(";set|set&set;","",$str);
    $str = my_replace("`set|set&set`","",$str);
    $str = my_replace("response","",$str);
    $str = my_replace("mailto:","",$str);
    $str = my_replace("CHAR","",$str);
    return $str;
}

function my_replace($chars, $empty, $str)
{ // 防止双写绕过
    $temp = $str;
    while (strpos($temp, $chars) !== false) { // 把所有可能是
        $temp = str_replace($chars, $empty, $temp);
    }
    return $temp;
}

?>
