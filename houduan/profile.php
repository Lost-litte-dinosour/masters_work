<?php
//echo json_encode($_FILES);
session_start();
// 后端判断类型和大小
@$my_type = $_FILES["user_image"]['type'];
if (isset($my_type) && ($my_type == 'image/jpeg' || $my_type == 'image/png' || $my_type == 'image/bmp' || $my_type == 'image/jpg' || $my_type == 'image/gif') && $_FILES["user_image"]['size'] < 1048577) {//后端验证
    $houzui = explode("/", $my_type)[1];
    $link = mysqli_connect('localhost', 'root', '1234') or die("数据库连接失败！");
    my_error($link, __LINE__, "use mydatabase");

    $res = my_error($link, __LINE__, 'select * from masters_work_user;');  //因为用户数量不多，为了防止SQL注入，可以先把所有记录保存到数组中，再到另一个循环中去判断（这样就不需要过滤函数了）但当用户数量多时，这样做就会导致查询时间过长
    $res_arr = array();
    $s = 0;
    while ($temp = mysqli_fetch_assoc($res)) {
        $temp_arr = array('email' => $temp['email'], 'password' => $temp['password'], 'pet_name' => $temp['pet_name'], 'profile' => $temp['profile'], 'visible' => $temp['visible']);
        $res_arr[$s] = $temp_arr;
        $s++;
    }

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
        my_back("200", "Return Tips", "您未登录！", "");
    }
    my_error($link, __LINE__, "use mydatabase");
    $res = my_error($link, __LINE__, 'select * from masters_work_user where email="' . filter(base64_decode($_COOKIE['email'])) . '";'); //先对邮箱进行SQL过滤，再拼接进SQL语句中查询
    $temp = mysqli_fetch_assoc($res);
    if ($temp['password'] == base64_decode($_COOKIE['password'])) {
        $imgname = '@' . $_FILES['user_image']['name'];
        $tmp = $_FILES['user_image']['tmp_name'];
        $filepath = '../image/user_image/' . '@' . filter($_SESSION['email']) . '.' . $houzui; //保存图片的名称为用户邮箱+'@'，既保证了不会和其他用户的图片重名（因为用户邮箱为主键），又保证了一个用户只能上传并保存一张图片
        if (move_uploaded_file($tmp, $filepath)) {
            my_error($link, __LINE__, 'update masters_work_user set profile = "' . './image/user_image/' . '@' . filter($_SESSION['email']) . '.' . $houzui . '" where email = "' . filter($_SESSION['email']) . '";');//对session中的Email进行SQL过滤再拼接进SQL语句中查询
            $fp = fopen("profile.txt", 'a'); //日志记录
            fwrite($fp, "[" . date('Y-m-d H:i:s') . "]  " . $_SESSION['email'] . " 用户更改了其用户头像\r\n");
            fclose($fp);
            my_back("200", "Return Tips", "上传成功！(如果刷新后未改变头像请按shift+F5刷新缓存)", "");
        } else {
            my_back("200", "Return Tips", "上传失败", "");
        }
    } else {
        my_back("200", "Return Tips", "您的Cookie信息已经被更改，请重新登录！(大佬别搞了球球了)", "");
    }
} else {
    my_back("200", "Return Tips", "上传类型不正确或者超出大小(最大为1MB),请重新上传！", "");
}

function my_back($status, $reason, $data, $error)
{
    $array = array(
        "status" => $status,
        "reason" => $reason,
        "data" => $data,
        "error" => $error,
    );
    echo json_encode($array);
}

function my_error($link, $my_line, $sql)
{
    $res = mysqli_query($link, $sql);
    if (!$res) {
        my_back("200", "Return Tips", "出错啦!请重新输入", "");
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
