<?php
//注销登录时，把cookie和session都删掉

//echo json_encode(array(1=>'1'));
setcookie("password", "", -1);
setcookie("email", "", -1);
//setcookie("visible", "", -1);
session_start();
$_SESSION['password'] = '';
$temp = $_SESSION['email'];
$_SESSION['email'] = '';
$array = array(
    "status" => "200",
    "reason" => "Return Tips",
    "msg" => base64_encode('注销成功'),
    "error" => "",
);
echo json_encode($array);
$fp = fopen("log.txt", 'a'); //日志记录
fwrite($fp, "[" . date('Y-m-d H:i:s') . "]  " . $temp . " 用户登出了\r\n");
fclose($fp);
?>