<?php
require_once '../functions.php';
$current_user=chen_get_current_user();
$categories=chen_fetch_all('select * from categories;');

function add_post(){
  global $current_user;
  // 校验普通表单(title, content, slug, category, created, status)
  if(empty(trim($_POST['title']))
    || empty(trim($_POST['content']))
    || empty(trim($_POST['slug']))
    || empty($_POST['category'])
    || empty($_POST['created'])
    || empty($_POST['status'])){
    $GLOBALS['message']='完整填写所有内容，不能为空！';
    return;
  }

  if(chen_fetch_one("select count(1) from posts where slug='{$_POST['slug']}';")['count(1)']>0){
    $GLOBALS['message']='别名已存在！重新设置！';
  }
  //====文件域(feature)==========
  $image=$_FILES['feature'];
  if($image['error']!==UPLOAD_ERR_OK){
    $GLOBALS['message']='图片上传失败！';
    return;
  }
  //校验文件类型
  $allowed_types= array('image/jpeg','image/jpg','image/png','image/peng');
  if(!in_array($image['type'],$allowed_types)){
      $GLOBALS['error_message']='图片文件格式错误';
      return;
  }
  // 限制大小为5M
  if($image['size']>5*1024*1024){
    $GLOBALS['message']='图片过大！';
    return;
  }
  $source=$image['tmp_name'];
  $target='../static/uploads/'.uniqid().'-'.$image['name'];
  if(!move_uploaded_file($source, $target)){
    $GLOBALS['message']='图片上传失败！';
    return;
  }
  //保存绝对路径
  $feature=substr($target, 2);

  //接收保存数据
  $title=$_POST['title'];
  $content=$_POST['content'];
  $slug=$_POST['slug'];
  // datetime “1000-01-01 00:00:00” 到“9999-12-31 23:59:59” 8字节
  $created=$_POST['created'];
  $category=$_POST['category'];
  $status=$_POST['status'];
  $user_id=$current_user['id'];
  $row=chen_execute("insert into posts (slug,title,feature,created,content,views,likes,status,user_id,category_id) values ('{$slug}','{$title}','{$feature}','{$created}','{$content}',0,0,'{$status}',{$user_id},{$category});");
  //保存是否成功
  if($row>0){
    $GLOBALS['success']=true;
    $GLOBALS['message']='保存成功！';
  }else {
    $GLOBALS['message']='保存失败！';
    return;
  }
  // var_dump($row);
  //跳转所有文章列表
  // header('location: /admin/posts.php');
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  add_post();
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" type="text/css" href="/static/assets/vendors/bootstrap/css/bootstrap-datetimepicker.css">
<script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'  ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>写文章</h1>
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
      <form class="row" autocomplete="off" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" encType="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" value="<?php echo isset($_POST['title'])&&empty($success) ? $_POST['title'] : ''; ?>" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">内容</label>
            <script id="content" class="" name="content" type="text/plain"><?php echo isset($_POST['content'])&&empty($success) ? $_POST['content'] : 'hello'; ?></script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" value="<?php echo isset($_POST['slug'])&&empty($success) ? $_POST['slug'] : ''; ?>" type="text" placeholder="slug">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file" accept="image/*">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
            <?php foreach ($categories as $item):?>
              <option value="<?php echo $item['id'];?>" <?php echo isset($_POST['category'])&&$_POST['category']===$item['id'] ? 'selected' : ''; ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <div class='input-group date' id='datetimepicker2'>
              <input id="created" class="form-control" readonly name="created" value="<?php echo isset($_POST['created'])&&empty($success) ? $_POST['created'] : ''; ?>" type="text">
              <span class="input-group-addon">
                <i class="glyphicon glyphicon-remove"></i>
              </span>
              <span class="input-group-addon">
                <i class="glyphicon glyphicon-calendar"></i>
              </span>
            </div>
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

 <?php $current_page='post-add' ?>
 <?php include 'inc/sidebar.php'; ?>

  <script type="text/javascript" src="/static/assets/vendors/jquery/jquery.js"></script>
  <script type="text/javascript" src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript" src="/static/assets/vendors/ueditor/ueditor.config.js"></script>
  <script type="text/javascript" src="/static/assets/vendors/ueditor/ueditor.all.js"></script>
  <script type="text/javascript" src="/static/assets/vendors/moment/moment.js"></script>
  <script type="text/javascript" src="/static/assets/vendors/bootstrap/js/bootstrap-datetimepicker.js"></script>
  <script type="text/javascript" src="/static/assets/vendors/bootstrap/js/bootstrap-datetimepicker.zh-CN.js"></script>
  <script>NProgress.done()</script>
  <script type="text/javascript">
    var ue=UE.getEditor('content',{
      initialFrameHeight: 390,
      initialFrameWidth: "100%",
      autoHeightEnabled: false

    });

    $(function($){
      $('#created').val(moment().format('YYYY-MM-DD HH:mm'));

      $('#datetimepicker2').datetimepicker({
        language: 'zh-CN',
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        weekStart: 1,
        startDate: "1990-01-01 00:00",
        startView: 3,
        minuteStep: 10,
        pickerPosition: 'bottom-left',
        // clearBtn:true,//清除按钮
      });
    })
  </script>
</body>
</html>
