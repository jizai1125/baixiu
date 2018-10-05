<?php
/**
 * 根据用户邮箱获取用户头像
 */
require_once '../../config.php';

if(empty($_GET['email'])){
    exit('缺少必要参数');
}
$email=$_GET['email'];

$conn=mysqli_connect(CHEN_DB_HOST,CHEN_DB_USER,CHEN_DB_PASSWORD,CHEN_DB_NAME);
if(!$conn){
    exit('连接数据库失败');
}
$res=mysqli_query($conn,"select avatar from users where email='{$email}' limit 1;");
if(!$res){
    exit('查询失败');
}
$row=mysqli_fetch_assoc($res);
echo $row['avatar'];
