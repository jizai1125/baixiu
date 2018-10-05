<?php
require_once '../functions.php';
$current_user=chen_get_current_user();

function update(){
  global $current_user;
  // 表单校验
  if(empty($_POST['slug'])){
    $GLOBALS['message']='别名不能为空！';
    return;
  }
  $slug=$_POST['slug'];
  $avatar=$_POST['avatar'];
  $nickname=$_POST['nickname'];
  $bio=$_POST['bio'];
  // 保存到数据库
  $row=chen_execute("update users set slug='{$slug}',nickname='{$nickname}',avatar='{$avatar}',bio='{$bio}'where id={$current_user['id']};");
  if($row<=0){
    $GLOBALS['message']='更新失败！';
    return;
  }
  $GLOBALS['success']=true;
  $GLOBALS['message']='更新成功！';
}
if($_SERVER['REQUEST_METHOD']==='POST'){
  update();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
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
        <h1>我的个人资料</h1>
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
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file">
              <img src="<?php echo empty($_POST['avatar']) ? $current_user['avatar'] : $_POST['avatar']; ?>">
              <i class="mask fa fa-upload"></i>
              <input type="hidden" name="avatar" value="<?php echo empty($_POST['avatar']) ? '/static/uploads/avatar.jpg': $_POST['avatar']; ?>">
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="<?php echo $current_user['email']; ?>" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" value="<?php echo empty($_POST['slug']) ? $current_user['slug'] : $_POST['slug']; ?>" placeholder="slug">
            <p class="help-block"></p>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="<?php echo empty($_POST['nickname']) ? $current_user['nickname'] : $_POST['nickname']; ?>" placeholder="昵称">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" name="bio" placeholder="Bio" cols="30" rows="6"><?php echo empty($_POST['bio']) ? $current_user['bio'] : $_POST['bio']; ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <a class="btn btn-link" href="password-reset.php">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>

<?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript">
    //通过ajax异步上传文件
    $(function($){
      //判断有没有选中文件
      $('#avatar').on('change',function(){
        var fileList=$(this).prop('files');
        if(!fileList.length) return;
        //取得选中文件信息
        var file=fileList[0];
        //FormData()专门用来配合ajax操作 传输二进制数据
        var formData=new FormData();
        formData.append('avatar',file);
      //创建xhr对象
        var xhr=new XMLHttpRequest();
        xhr.open('post','/admin/api/upload.php');
        xhr.send(formData);
        //等待响应
        xhr.onload=function(){
          console.log(xhr.responseText)
          var src=xhr.responseText;
          //将服务端返回的已上传的图片路径赋给img元素 和一个隐藏的表单元素
          $('#avatar + img').attr('src',src);
          $('#avatar ~ input').val(src);
        }

      })

    })

  </script>
  <script>NProgress.done()</script>
</body>
</html>
