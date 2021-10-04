<?php
//var_dump('haha');
//var_dump($_POST);
if (!(isset($_POST['email']) || !isset($_POST['password']))) {//后端验证
    $res_arr = array();
    $tishi = '用户名或密码为空！';
} else {
    $post_arr = array('email' => $_POST['email'], 'password' => $_POST['password']);
//    $post_arr = array('email' => 'wrz12138@hdu.edu.cn', 'password' => '1234567');

    $link = mysqli_connect('localhost', 'root', '1234') or die("数据库连接失败！");
    my_error($link, __LINE__, "use mydatabase");

    $res = my_error($link, __LINE__, 'select * from masters_work_user;');  //因为用户数量不多，为了防止SQL注入，可以先把所有记录保存到数组中，再到另一个循环中去判断（这样就不需要过滤函数了）
    $res_arr = array();
    $s = 0;
    while ($temp = mysqli_fetch_assoc($res)) {
        $temp_arr = array('email' => $temp['email'], 'password' => $temp['password'], 'pet_name' => $temp['pet_name'], 'profile' => $temp['profile'] , 'visible' => $temp['visible']);
        $res_arr[$s] = $temp_arr;
        $s++;
    }
}
//echo $res_arr;
//$ss = '';
$flag = false;
foreach ($res_arr as $items) {
//    $ss += '!' + $items['email'];
    if ($items['email'] == $post_arr['email'] && $items['password'] == $post_arr['password']) { // 判断记录中是否有POST来的数据
        $tishi = '登录成功！';
        $fp = fopen("log.txt",'a'); //日志记录
        fwrite($fp,"[".date('Y-m-d H:i:s')."]  ".$post_arr['email']." 用户登录了\r\n");
        fclose($fp);
        session_start(); // 通过session传递用户的名字和密码，并且每次加载时要验证
        $_SESSION['password'] = $items['password'];
//        $_SESSION['profile'] = $items['profile']; // 图片地址应该每次刷新页面时就从数据库查询，而不是通过session传递
//        $_SESSION['visible'] = $items['visible'];
        $_SESSION['email'] = $items['email'];
        $flag = true;
        break;
    }
}
//echo $ss;
if (!$flag) {
    $tishi = '用户名或密码错误！';
}

$array = array(
    "status" => "200",
    "reason" => "Return Tips",
    "msg" => $tishi,
    "error" => "",
);
echo json_encode($array);

function my_error($link, $my_line, $sql)
{
    $res = mysqli_query($link, $sql);
    if (!$res) {
        $array = array(
            "status" => "200",
            "reason" => "Return Tips",
            "msg" => '出错啦！请重新输入',
//            "msg" => "出错啦！行号为" . $my_line . "<br/>" . "SQL执行错误，错误编号为：" . mysqli_errno($link) . "<br/>" . "SQL执行错误，错误信息为：" . mysqli_error($link) . "<br/>",
            "error" => "",
        );
        echo json_encode($array);
        exit();
    }
    return $res;
}

function filter($str) //SQL过滤函数
{
    if (empty($str)) return false;
    $str = htmlspecialchars($str);
    $str = str_replace('ASCII', "", $str);
    $str = str_replace('ASCII 0x0d', "", $str);
    $str = str_replace('ASCII 0x0a', "", $str);
    $str = str_replace('sysopen', "", $str);
    $str = str_replace('ASCII 0x08', "", $str);
    $str = str_replace("&gt", "", $str);
    $str = str_replace("&lt", "", $str);
    $str = str_replace("<SCRIPT>", "", $str);
    $str = str_replace("</SCRIPT>", "", $str);
    $str = str_replace("<script>", "", $str);
    $str = str_replace("</script>", "", $str);
    $str = str_replace("select", "", $str);
    $str = str_replace("union", "", $str);
    $str = str_replace("insert", "", $str);
    $str = str_replace("delete", "", $str);
    $str = str_replace("update", "", $str);
    $str = str_replace("DROP", "", $str);
    $str = str_replace("create", "", $str);
    $str = str_replace("modify", "", $str);
    $str = str_replace("rename", "", $str);
    $str = str_replace("alter", "", $str);
    $str = str_replace("<br />", chr(13), $str);
    $str = str_replace("CSS", "'", $str);
    $str = str_replace("<!--", "", $str);
    $str = str_replace("convert", "", $str);
    $str = str_replace("md5", "", $str);
    $str = str_replace("passwd", "", $str);
    $str = str_replace("password", "", $str);
    $str = str_replace("Array", "", $str);
    $str = str_replace(";set|set&set;", "", $str);
    $str = str_replace("`set|set&set`", "", $str);
    $str = str_replace("mailto:", "", $str);
    $str = str_replace("CHAR", "", $str);
    return $str;
}
?>