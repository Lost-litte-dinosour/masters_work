<?php

$my_post['email'] = filter(base64_decode($_POST['email'])); // 进行SQL过滤
$my_post['phone_num'] = filter(base64_decode($_POST['phone_num']));
$my_post['pet_name'] = filter(base64_decode($_POST['pet_name']));
$my_post['password'] = filter(base64_decode($_POST['password']));


if (strlen($my_post['password']) < 32 and strlen($my_post['password']) > 5 and strlen($my_post['phone_num']) < 16 and strlen($my_post['password']) < 32 and strlen($my_post['password']) > 5 and strlen($my_post['pet_name']) < 32 and strlen($my_post['pet_name']) > 0) {// 后端验证！！！！！！！！！！！！！！！
    $link = mysqli_connect('localhost', 'root', '1234') or die("数据库连接失败！");
    my_error($link, __LINE__, "use mydatabase");

    $res = my_error($link, __LINE__, 'select * from masters_work_user');
    $flag = 1;
    while ($items = mysqli_fetch_assoc($res)) {
        if ($items['email'] == $my_post['email']) {
            $array = array(
                "status" => "200",
                "reason" => "Return Tips",
                "msg" => base64_encode('该邮箱已被注册！请重新输入！'),
                "error" => "",
            );
            echo json_encode($array);
            $flag = 0;
            break;
        }
    }
    if ($flag) {
        if (!isset($my_post['phone_num'])) {
            $my_phone_num = '';
        } else {
            $my_phone_num = $my_post['phone_num'];
        }
        my_error($link, __LINE__, "insert into masters_work_user values ('" . $my_post['email'] . "','" . $my_post['pet_name'] . "','" . $my_post['password'] . "','" . $my_phone_num . "', './image/user_image/moren.png', 1);"); // 已经进行SQL过滤和长度过滤
        $fp = fopen("register.txt", 'a'); //日志记录
        fwrite($fp, "[" . date('Y-m-d H:i:s') . "]  " . $my_post['email'] . " 用户注册了\r\n");
        fclose($fp);
        $array = array(
            "status" => "200",
            "reason" => "Return Tips",
            "msg" => base64_encode('注册成功！'),
            "error" => "",
        );
        echo json_encode($array);
    }
} else {
    $array = array(
        "status" => "200",
        "reason" => "Return Tips",
        "msg" => base64_encode('输入过长或过短字符！请重新输入！'),
        "error" => "",
    );
    echo json_encode($array);
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
    $str = my_replace('ASCII', "", $str);
    $str = my_replace('ASCII 0x0d', "", $str);
    $str = my_replace('alert(', "", $str);
    $str = my_replace('ASCII 0x0a', "", $str);
    $str = my_replace('sysopen', "", $str);
    $str = my_replace('system', "", $str);
    $str = my_replace('ASCII 0x08', "", $str);
    $str = my_replace("<SCRIPT>", "", $str);
    $str = my_replace("</SCRIPT>", "", $str);
    $str = my_replace("<script>", "", $str);
    $str = my_replace("</script>", "", $str);
    $str = my_replace("select", "", $str);
    $str = my_replace("union", "", $str);
    $str = my_replace("insert", "", $str);
    $str = my_replace("delete", "", $str);
    $str = my_replace("update", "", $str);
    $str = my_replace("DROP", "", $str);
    $str = my_replace("create", "", $str);
    $str = my_replace("modify", "", $str);
    $str = my_replace("<br />", chr(13), $str);
    $str = my_replace("CSS", "'", $str);
    $str = my_replace("<!--", "", $str);
    $str = my_replace("passwd", "", $str);
    $str = my_replace("password", "", $str);
    $str = my_replace("Array", "", $str);
    $str = my_replace("or 1='1'", "", $str);
    $str = my_replace(";set|set&set;", "", $str);
    $str = my_replace("`set|set&set`", "", $str);
    $str = my_replace("response", "", $str);
    $str = my_replace("mailto:", "", $str);
    $str = my_replace("CHAR", "", $str);
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