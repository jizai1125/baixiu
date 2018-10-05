<?php
//校验当前访问的用户 session 有没有用户标识
require_once '../functions.php';
chen_get_current_user();
//获取界面所需的数据
$posts_count=chen_fetch_one("select count(1) as num from posts;")['num'];
$posts_drafted_count=chen_fetch_one("select count(1) as num from posts where status='drafted'")['num'];
$categories_count=chen_fetch_one("select count(1) as num from categories;")['num'];
$comments_count=chen_fetch_one("select count(1) as num from comments;")['num'];
$comments_held_count=chen_fetch_one("select count(1) as num from comments where status='held'")['num'];
//校验查询的数据是否存在，不存在则设为0
if(empty($posts_count) || !isset($posts_count)){
  $posts_count=0;
}
if(empty($posts_drafted_count) || !isset($posts_drafted_count)){
  $posts_drafted_count=0;
}
if(empty($categories_count) || !isset($categories_count)){
  $categories_count=0;
}
if(empty($comments_count) || !isset($comments_count)){
  $comments_count=0;
}
if(empty($comments_held_count) || !isset($comments_held_count)){
  $comments_held_count=0;
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
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="/admin/post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $posts_count ?></strong>篇文章（<strong><?php echo $posts_drafted_count ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $categories_count ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comments_count ?></strong>条评论（<strong><?php echo $comments_held_count ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>
  <?php $current_page='index'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
