<?php
//echo strlen($_POST['index']);
//echo strlen($_POST['choice']);
//echo strlen($_POST['image'][0]);
//SQL过滤
$my_post['index'] = filter($_POST['index']);
$my_post['image'][0] = filter($_POST['image'][0]);
$my_post['choice'] = filter($_POST['choice']);
//echo $my_post['index'];


if (strlen($my_post['index']) < 6 and strlen($my_post['choice']) < 10 and strlen($my_post['image'][0]) < 25) { //防SQL注入 注意：一个中文占三个长度
    $link = mysqli_connect('localhost', 'root', '1234');
    my_error($link, __LINE__, 'use mydatabase');

    $res = my_error($link, __LINE__, 'select * from masters_work_po where image="' . $my_post['image'][0] . '";'); //已经对POST来的数据进行SQL注入过滤
    $items = mysqli_fetch_assoc($res);

    $flag = 0;
    if ($my_post['index'] == 'sex' and $my_post['choice'] == $items['sex'] and $my_post['image'][0] == $items['image']) {
        $flag = 1;
    } elseif ($my_post['index'] == 'name' and $my_post['choice'] == $items['name'] and $my_post['image'][0] == $items['image']) {
        $flag = 1;
    }

    if ($flag == 1) {
        $array = array(
            "error" => "0",
            "msg" => "",
            "data" => base64_encode('判断正确！'),
            "redirect" => "",
        );
        echo json_encode($array);
    } else {
        $array = array(
            "error" => "0",
            "msg" => "",
            "data" => base64_encode('啊哦，判断错误啦'),
            "redirect" => "",
        );
        echo json_encode($array);
    }

} else {
    $array = array(
        "error" => "0",
        "msg" => base64_encode("数据格式有误！"),
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
            "msg" => base64_encode('出错啦！请重新输入'),
//            "msg" => base64_encode("出错啦！行号为" . $my_line . "<br/>" . "SQL执行错误，错误编号为：" . mysqli_errno($link) . "<br/>" . "SQL执行错误，错误信息为：" . mysqli_error($link) . "<br/>"),
            "error" => "0",
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