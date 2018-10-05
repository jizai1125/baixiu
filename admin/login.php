<?php
//载入配置文件
require_once '../config.php';
session_start();
function login(){
  //校验表单
  if(empty($_POST['email'])){
    $GLOBALS['erro_message']='邮箱不能为空';
    return;
  }
  if(empty($_POST['password'])){
    $GLOBALS['erro_message']='密码不能为空';
    return;
  }
  //获取表单字段值
  $email=$_POST['email'];
  $psw=$_POST['password'];
  //连接数据库判断是否正确
  $conn=mysqli_connect(CHEN_DB_HOST,CHEN_DB_USER,CHEN_DB_PASSWORD,CHEN_DB_NAME);
  if(!$conn){
    exit("数据库连接失败");
  }
  $query=mysqli_query($conn,"select * from users where email='{$email}' limit 1;");
  if(!$query){
    $GLOBALS['erro_message']='失败，重试';
    return;
  }
  $user=mysqli_fetch_assoc($query);
  if(!$user){
    $GLOBALS['erro_message']='邮箱与密码不匹配';
    return;
  }
  if($user['password']!==$psw){
    $GLOBALS['erro_message']='邮箱与密码不匹配';
    return;
  }
  //释放结果内存， 关闭连接
  mysqli_free_result($query);
  mysqli_close($conn);
  //设置用户状态标识，为了后续可以获取当前登录用户的信息
  $_SESSION['current_user']=$user;
  //跳转页面
  header('location: /admin/index.php');
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  login();
}
//退出登录
if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) && $_GET['action']=='logout'){
  unset($_SESSION['current_user']);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" type="text/css" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <!-- 添加 novalidate 关闭浏览器自带的表单校验 -->
    <!-- autocomplete="off" 关闭客户端自动完成功能 -->
    <!-- required 设置为必填不能为空 -->
    <form class="login-wrap" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"novalidate autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if(isset($erro_message)): ?>
      <div class="alert alert-danger shake animated">
        <strong><?php echo $erro_message; ?></strong>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" value="<?php echo empty($_POST['email'])? '' : $_POST['email']; ?>" class="form-control" placeholder="邮箱" autofocus required>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码" required>
      </div>
      <button class="btn btn-primary btn-block" href="index.html">登 录</button>
    </form>
  </div>

  <script type="text/javascript" src="/static/assets/vendors/jquery/jquery.js"></script>
  <script type="text/javascript">
    $(function($){
      //email失去焦点后，发送一个ajax请求，获取邮箱对应的头像路径地址，展现到img元素
      var $email=$('#email');
      var preValue='';

      $email.on('blur',function(){
        var value=$(this).val();
        // 如果上次填写的邮箱和这次的一样，则不发送ajax请求
        if(value===preValue)return;
        preValue=value;
        var emailFormat=/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
        if(!value || !emailFormat.test(value))return;

        $.get('/admin/api/avatar.php',{'email':value},function(res){
              if(!res)return;
              $('.avatar').fadeOut(function(){
                //等图片加载完后执行动画
                $(this).on('load',function(){
                  $(this).fadeIn();
                }).attr('src',res);
              });
          });
      });
    });
  </script>
</body>
</html>
