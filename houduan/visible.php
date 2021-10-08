<?php
session_start();
//if ($_GET['visible'] == '未选择') {
//    $array = array(
//        "error" => "0",
//        "msg" => "并未选择选项！",
//        "data" => array(),
//        "redirect" => "",
//    );
//    echo json_encode($array);
//} else
if ($_GET['visible'] == 1 || $_GET['visible'] == 0) {
    $link = mysqli_connect('localhost', 'root', '1234');
    my_error($link, __LINE__, 'use mydatabase');
    my_error($link, __LINE__, 'update masters_work_user set visible = "' . $_GET['visible'] . '" where email = "' . filter($_SESSION['email']) . '";'); //同样还是要对Session中的email进行SQL过滤
    $temp = '显示';
    if ($_GET['visible'] == 0) {
        $temp = '隐藏';
    }
    $fp = fopen("visible.txt", 'a'); //日志记录
    fwrite($fp, "[" . date('Y-m-d H:i:s') . "]  " . $_SESSION['email'] . " 用户更改了其用户头像属性为" . $temp . "\r\n");
    fclose($fp);
    $array = array(
        "error" => "0",
        "msg" => base64_encode("修改成功！"),
        "data" => array(),
        "redirect" => "",
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