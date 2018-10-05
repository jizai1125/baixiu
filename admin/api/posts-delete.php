<?php
require_once "../../functions.php";

//通过 get 方式获取对应数据的id
if(empty($_GET['id'])){
    exit('<h1>缺少必要参数!</h1>');
}
$id_str=$_GET['id'];
$id_arr=explode(',',$id_str);

//判断id 是否为数字，防止sql注入 例如 1 or 1=1, 也可以用强类型转换(int)$_GET['id']
foreach ($id_arr as $value) {
    if(!is_numeric($value)){
        exit('<h1>删除错误!</h1>');
    }
}
//根据id 从数据库删除对应的数据
chen_execute("delete from posts where id in({$id_str});");
// http中的referer 用来标识当前页面请求的来源
header('location: '.$_SERVER['HTTP_REFERER']);
