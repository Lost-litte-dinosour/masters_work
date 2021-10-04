<?php
//var_dump($_POST);
//$link = mysqli_connect('localhost', 'root', '1234') or die("数据库连接失败！");
//my_error($link, __LINE__, "use mydatabase");
//
//my_error($link, __LINE__, "insert into masters_work values ('林俊杰','image\linjunjie.png',0)");
//
//
//function my_error($link, $my_line, $sql)
//{
//    $res = mysqli_query($link, $sql);
//    if (!$res) {
//        echo "出错啦！行号为" . $my_line . "<br/>";
//        echo "SQL执行错误，错误编号为：" . mysqli_errno($link) . "<br/>";
//        echo "SQL执行错误，错误信息为：" . mysqli_error($link) . "<br/>";
//        exit();
//    }
//    return $res;
//}
$fp = fopen("log.txt",'a');
fwrite($fp,"[".date('Y-m-d H:i:s')."]  root1登出了\r\n");
fclose($fp);


?>