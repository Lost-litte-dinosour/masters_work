<?php
$image_num = $_GET['image_num'];
$name_num = $_GET['name_num'];
if (!(is_numeric($image_num) and is_numeric($name_num) and $name_num < 7 and $image_num < 7 and $name_num > 0 and $image_num > 0)) {
    $array = array(
        "error" => "0",
        "msg" => base64_encode("数据格式有误！"),
        "data" => array(),
        "redirect" => "",
    );
    echo json_encode($array);
} else {
    $link = mysqli_connect('localhost', 'root', '1234') or die("数据库连接失败！");
    my_error($link, __LINE__, "use mydatabase");

    $res = my_error($link, __LINE__, 'select * from masters_work_po;');
    $res_arr = array();
    $s = 0;
    while ($temp = mysqli_fetch_assoc($res)) {
        $temp_arr = array('name' => $temp['name'], 'image' => $temp['image'], 'sex' => $temp['sex']);
//        echo $temp['name'] . ' : ';
//        echo $temp['image'] . '   ';
//        echo $temp['sex'];
//        echo '<br>';
        $res_arr[$s] = $temp_arr;
        $s++;
    }
}

$image_temp = array();
$name_temp = array();

for ($i = 0; $i < 6; $i++) { //保存所有对应的值
    $image_temp[$i] = $res_arr[$i]['image'];
    $name_temp[$i] = $res_arr[$i]['name'];
}


//for($i = 0; $i < $image_num; $i++){
$temp_key = array_rand($image_temp);
$image_arr = base64_encode($image_temp[$temp_key]); //确保出现正确答案
$name_arr = array(base64_encode($name_temp[$temp_key]));
unset($image_temp[$temp_key]);
unset($name_temp[$temp_key]);
//}
for ($i = 1; $i < $name_num; $i++) {
    $temp_key = array_rand($name_temp);
    $name_arr[$i] = base64_encode($name_temp[$temp_key]);
    unset($name_temp[$temp_key]);
}

shuffle($name_arr); //打乱数组

$response_temp_arr = array('sex' => [base64_encode('0'), base64_encode('1')], 'name' => $name_arr);
$response_temp = array_rand($response_temp_arr); // 随机返回，让用户随机判断性别或者名字
//var_dump($image_arr);
$array = array(
    "error" => "0",
    "msg" => "",
    "data" => array('image' => $image_arr, 'index' => base64_encode($response_temp), 'choice' => $response_temp_arr[$response_temp]),  //采用了base64加密
    "redirect" => "",
);
echo json_encode($array);


function my_error($link, $my_line, $sql)
{
    $res = mysqli_query($link, $sql);
    if (!$res) {
        $array = array(
            "error" => "0",
            "msg" => base64_encode('出错啦！请重新输入'),
//            "msg" => base64_encode("出错啦！行号为" . $my_line . "<br/>" . "SQL执行错误，错误编号为：" . mysqli_errno($link) . "<br/>" . "SQL执行错误，错误信息为：" . mysqli_error($link) . "<br/>"),
            "data" => array(),
            "redirect" => "",
        );
        echo json_encode($array);
        exit();
    }
    return $res;
}

?>