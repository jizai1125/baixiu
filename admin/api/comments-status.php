<?php
require_once '../../functions.php';
header('Content-Type: application/json');
// 校验从get请求获取的数据
if(empty($_GET['id']) || empty($_POST['status'])){
    exit('<h1>缺少必要参数！</h1>');
}
$id=$_GET['id'];
$status=$_POST['status'];
$rows=chen_execute("update comments set status='{$status}' where id in ($id);");

echo json_encode($rows>0);
