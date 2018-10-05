<?php
require_once '../functions.php';

$current_user=chen_get_current_user();

function update(){
  global $current_user;
  // 校验表单是否为空
  if(empty($_POST['old'])){
    $GLOBALS['message']='填写旧密码！';
    return;
  }
  if(empty($_POST['password'])){
    $GLOBALS['message']='填写新密码！';
    return;
  }
  if(empty($_POST['confirm'])){
    $GLOBALS['message']='填写确认新密码！';
    return;
  }
  $new_psw=$_POST['password'];
  $confirm=$_POST['confirm'];
  // 校验新密码两次填写是否一致
  if($new_psw!==$confirm){
    $GLOBALS['message']='新密码不一致！';
    return;
  }
  $old=$_POST['old'];
  $old_psw=chen_fetch_one("select password from users where id={$current_user['id']};")['password'];
  // 校验密码是否正确
  if($old!==$old_psw){
    $GLOBALS['message']='旧密码错误！';
    return;
  }
  $affect_row=chen_execute("update users set password='{$new_psw}' where id={$current_user['id']};");
  if($affect_row<0){
    $GLOBALS['message']='密码更改失败！';
    return;
  }
  $GLOBALS['success']=true;
  $GLOBALS['message']='密码更改成功！';
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  update();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Password reset &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
       <?php include 'inc/navbar.php'  ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>修改密码</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($message)): ?>
        <?php if(isset($success)): ?>
        <div class="alert alert-success">
          <strong><?php echo $message; ?></strong>
        </div>
        <?php else: ?>
        <div class="alert alert-danger">
          <strong><?php echo $message; ?></strong>
        </div>
        <?php endif ?>
      <?php endif ?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="form-group">
          <label for="old" class="col-sm-3 control-label">旧密码</label>
          <div class="col-sm-7">
            <input id="old" class="form-control" name="old" type="password" placeholder="旧密码">
          </div>
        </div>
        <div class="form-group">
          <label for="password" class="col-sm-3 control-label">新密码</label>
          <div class="col-sm-7">
            <input id="password" class="form-control" name="password" type="password" placeholder="新密码">
          </div>
        </div>
        <div class="form-group">
          <label for="confirm" class="col-sm-3 control-label">确认新密码</label>
          <div class="col-sm-7">
            <input id="confirm" class="form-control" name="confirm" type="password" placeholder="确认新密码">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-7">
            <button type="submit" class="btn btn-primary">修改密码</button>
          </div>
        </div>
      </form>
    </div>
  </div>

 <?php include 'inc/sidebar.php'; ?>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
