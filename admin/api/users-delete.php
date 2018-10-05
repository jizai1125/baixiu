<?php
/*
删除用户信息
 */
require_once '../../functions.php';
if(empty($_GET['id'])){
    exit('<h1>缺少参数！</h1>');
}
$id=$_GET['id'];
$idArr=explode(',',$id);
foreach($idArr as $item){
    if(!is_numeric($item)){
        exit('<h1>操作失败！</h1>');
    }
}
$row=chen_execute('delete from users where id in ('.$id.');');
echo json_encode($row>0);

