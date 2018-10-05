<?php
require_once '../../functions.php';
chen_get_current_user();

//通过get方式 获取id ，连接数据库删除对应的数据
//校验id
if(empty($_GET['id'])){
    exit('<h1>缺少相应参数</h1>');
}
$id=$_GET['id'];
$id_arr=explode(',', $id);
foreach ($id_arr as $value) {
    if(!is_numeric($value)){
        exit('<h1>操作失败！</h1>');
    }
}
//连接数据库执行sql语句
$row=chen_execute("delete from comments where id in ({$id});");
// 声明响应体类型
header('Content-Type: application/json');
echo json_encode($row>0);
