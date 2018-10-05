<?php
require_once 'config.php';
/**
 * 封装公共的函数
 * 定义函数时一定要注意 函数名与内置函数冲突问题
 */
session_start();
/**
 * [get_current_user 获取当前登录用户信息，没有获取到则跳转回登录页]
 * @return [type] [description]
 */
function chen_get_current_user(){
    if(empty($_SESSION['current_user'])){
        //没有当前登录用户信息，则跳转回登录页
        header('location: /admin/login.php');
        exit();//退出当前页面代码的执行
    }
    return $_SESSION['current_user'];
}
/**
 *建立数据连接，执行sql语句
 */
function chen_query($sql){
    $conn=mysqli_connect(CHEN_DB_HOST,CHEN_DB_USER,CHEN_DB_PASSWORD,CHEN_DB_NAME);
    if(!$conn){
        exit('连接失败');
    }
    $query=mysqli_query($conn,$sql);
    if(!$query){
        return false;
    }
    return array($conn,$query);
}
/**
 * 连接查询数据库获取多条数据
 * @return 索引数组 => 关联数组
 */
function chen_fetch_all($sql){
    if(!$conn_query=chen_query($sql)){
        return false;
    }
    $conn=$conn_query[0];
    $query=$conn_query[1];
    while($row=mysqli_fetch_assoc($query)){
        $result[]=$row;
    }
    mysqli_free_result($query);
    mysqli_close($conn);
    return $result=isset($result) ? $result : null;
}
/**
 * [chen_fetch_one 获取单条数据]
 * @return 索引数组
 */
function chen_fetch_one($sql){
  $res=chen_fetch_all($sql);
  return isset($res[0]) ? $res[0] : null;
}
/**
 * [chen_execute 执行一个增删改语句]
 * @param  [type] $sql [description]
 * @return [type]      [description]
 */
function chen_execute($sql){
    if(!$conn_query=chen_query($sql)){
        return false;
    }
    $conn=$conn_query[0];
    $query=$conn_query[1];
    //对于增删改的操作都是获取受影响行数
    $affected_rows=mysqli_affected_rows($conn);
    mysqli_close($conn_query[0]);
    return $affected_rows;
}
