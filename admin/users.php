<?php
require_once '../functions.php';
chen_get_current_user();

function add(){
  // 校验表单
  if(empty($_POST['email'])){
    $GLOBALS['message']='邮箱必填！';
    return;
  }
  if(empty($_POST['slug'])){
    $GLOBALS['message']='别名必填！';
    return;
  }
  if(empty($_POST['password'])){
    $GLOBALS['message']='密码必填！';
    return;
  }
  $email=$_POST['email'];
  $slug=$_POST['slug'];
  $password=$_POST['password'];
  $nickname=$_POST['nickname'];
  // 校验别名是否唯一
  $has_slug=(int)chen_fetch_one("select count(1) from users where slug='{$slug}';")['count(1)'];
  $has_email=(int)chen_fetch_one("select count(1) from users where email='{$email}';")['count(1)'];
  if($has_email>0){
    $GLOBALS['message']='邮箱已被注册了！';
    return;
  }
  if($has_slug>0){
    $GLOBALS['message']='别名已被注册了！';
    return;
  }
  // 保存数据
  $row=chen_execute("insert into users (email,slug,password,nickname,status) values ('{$email}','{$slug}','{$password}','{$nickname}','activated');");
  if($row<0){
    $GLOBALS['message']='添加失败！';
    return;
  }
// $success 标识是否保存成功
  $GLOBALS['success']=true;
  $GLOBALS['message']='添加成功！';
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  add();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
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
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off" method="post">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" value="<?php echo isset($_POST['email']) && empty($success) ? $_POST['email'] : '';  ?>" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" value="<?php echo isset($_POST['slug']) && empty($success) ? $_POST['slug'] : '';?>" placeholder="slug">
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" value="<?php echo isset($_POST['nickname']) && empty($success) ? $_POST['nickname'] : '';?>" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm deleteAll" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox" class="selectAll"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody id="users-content">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php $current_page='users'; ?>
  <?php include 'inc/sidebar.php'; ?>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript" src="/static/assets/vendors/jsrender/jsrender.min.js"></script>
  <script type="text/html" id="users">
    {{for users}}
     <tr data-id='{{:id}}'>
      <td class="text-center"><input type="checkbox"></td>
      <td class="text-center"><img class="avatar" src="{{:avatar}}"></td>
      <td>{{:email}}</td>
      <td>{{:slug}}</td>
      <td>{{:nickname}}</td>
      <td>{{:statue==activated ? '激活' : '未激活'}}</td>
      <td class="text-center">
        <a href="post-add.php" class="btn btn-default btn-xs">编辑</a>
        <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
      </td>
    </tr>
    {{/for}}
  </script>
  <script type="text/javascript">
    $(function($){
      var content=$('#users-content');
      // 请求获取用户信息=========================
      function loadUsers(){
        $.get('/admin/api/users-list.php',function(data){
          if(!data.success)return;
          // 渲染模板
          var html=$('#users').render({users: data.users});
          content.html(html).fadeIn();
        })
      }
      loadUsers();
      // 操作用户信息=================================
      content.on('click','.btn-delete',function(){
        var id=$(this).parent().parent().data('id');
        $.get('/admin/api/users-delete.php',{id: id},function(res){
          // 回调函数返回一个布尔值，标识是否删除成功
          if(!res)return;
          // 刷新用户信息页面
          loadUsers();
        })
      })
      // 批量操作=================================
      var idArr=[];
      var deleteAll=$('.deleteAll');
      var selectAll=$('th>input[type=checkbox]');

      content.on('change','td>input[type=checkbox]',function(){
        var id=$(this).parent().parent().data('id');
        if($(this).prop('checked')){
          idArr.indexOf(id)!==-1 || idArr.push(id);
        }else{
          idArr.splice(idArr.indexOf(id),1);
        }
        idArr.length>0 ? deleteAll.fadeIn() : deleteAll.fadeOut();
      })
      // 全选/全不选
      selectAll.on('change',function(){
        var checked=$(this).prop('checked');
        $('td>input[type=checkbox]').prop('checked',checked).trigger('change');
      })
      // 批量删除点击事件
      deleteAll.on('click',function(){
        $.get('/admin/api/users-delete.php',{id: idArr.join(',')},function(res){
          if(!res)return;
          loadUsers();
          deleteAll.fadeOut();
        })
      })

    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
