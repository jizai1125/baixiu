<?php
/*
获取用户信息
 */
require_once '../../functions.php';

header('Content-Type: application/json');

$users=chen_fetch_all('select * from users;');
if(empty($users)){
    exit('<h1>查询失败！</h1>');
}
echo json_encode(array(
    'success'=>true,
    'users'=>$users
));
