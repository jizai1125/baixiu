<?php
/*
  此页面为扩展，获取豆瓣电影接口数据
*/
//校验当前访问的用户 session 有没有用户标识
require_once '../functions.php';
chen_get_current_user();

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
  <style type="text/css">
    .table>tbody>tr>td {
      vertical-align: middle;
    }
    tbody td img {
      transition: all .2s;
    }
    tbody td img:hover {
      position: relative;

      width: 135px!important;
      height: 180px!important;
    }
  </style>
</head>
<body>
  <script>NProgress.start()</script>
  <div class="main">
  <?php include 'inc/navbar.php'  ?>
    <h3>豆瓣电影榜单(Top<span id="movie_count" style="font-size: 25px;"></span>)</h3>
    <table class="table table-sm table-hover table-striped table-bordered">
      <thead>
        <th scope="col" >#</th>
        <th scope="col" class="text-center" >类型</th>
        <th scope="col" class="text-center">电影名</th>
        <th scope="col" class="text-center">年份</th>
        <th scope="col" class="text-center">海报</th>
        <th scope="col" class="text-center">导演</th>
      </thead>
      <tbody id="content"></tbody>
    </table>
  </div>
  <?php $current_page='douban'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript" src="/static/assets/vendors/jsrender/jsrender.min.js"></script>
  <script id="movie" type="text/html">
    {{for movie}}
    <tr>
      <td scope="row" id="movie_order">{{: #index+1}}</td>
      <td scope="row" class="text-center">{{:genres}}</td>
      <td scope="row" class="text-center">{{:original_title}}</td>
      <td scope="row" class="text-center">{{:year}}</td>
      <td scope="row" class="text-center"><img width="90" height="120" src="{{for images}}{{:small}}{{/for}}"></td>
      <td scope="row" class="text-center">{{for directors}}{{:name}}{{/for}}</td>
    </tr>
    {{/for}}
  </script>
  <script type="text/javascript">
    $(function($){
      // jquery底层方法
      $.ajax({
        url: 'http://api.douban.com/v2/movie/in_theaters',
        dataType: 'jsonp',
        success: function(data){
          console.log(data);
          //榜单数量
          $("#movie_count").text(data.count);
          // 模板渲染
          var html=$('#movie').render({
            movie: data.subjects
          });
          $('#content').html(html);
        }
      })
    })

    // 第二种方法
   /* //回调函数
    function foo(data){
      console.log(data);
       $("#movie_count").text(data.count);
        var html=$('#movie').render({
          movie: data.subjects
        });
        $('#content').html(html);
    }*/
  </script>
 <!--  <script type="text/javascript" src="http://api.douban.com/v2/movie/in_theaters?callback=foo"></script> -->
  <script>NProgress.done()</script>
</body>
</html>
