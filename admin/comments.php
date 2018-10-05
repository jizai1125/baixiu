<?php
require_once '../functions.php';
chen_get_current_user();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style type="text/css">
    #loading {
      display: flex;
      position: fixed;
      align-items: center;
      justify-content: center;
      top: 0;
      bottom: 0;
      right: 0;
      left: 0;
      z-index: 999;
      background-color: rgba(0,0,0,.5);
    }
    .flip-txt-loading {
      font: 26px Monospace;
      letter-spacing: 5px;
      color: #fff;
    }

    .flip-txt-loading > span {
      animation: flip-txt  1.5s infinite;
      display: inline-block;
      transform-origin: 50% 50% -30px;
      transform-style: preserve-3d;
    }

    .flip-txt-loading > span:nth-child(1) {
      -webkit-animation-delay: 0.08s;
              animation-delay: 0.08s;
    }

    .flip-txt-loading > span:nth-child(2) {
      -webkit-animation-delay: 0.15s;
              animation-delay: 0.15s;
    }

    .flip-txt-loading > span:nth-child(3) {
      -webkit-animation-delay: 0.22s;
              animation-delay: 0.22s;
    }

    .flip-txt-loading > span:nth-child(4) {
      -webkit-animation-delay: 0.30s;
              animation-delay: 0.30s;
    }

    .flip-txt-loading > span:nth-child(5) {
      -webkit-animation-delay: 0.38s;
              animation-delay: 0.38s;
    }

    .flip-txt-loading > span:nth-child(6) {
      -webkit-animation-delay: 0.45s;
              animation-delay: 0.45s;
    }

    .flip-txt-loading > span:nth-child(7) {
      -webkit-animation-delay: 0.52s;
              animation-delay: 0.52s;
    }

    @keyframes flip-txt  {
      to {
        -webkit-transform: rotateX(1turn);
                transform: rotateX(1turn);
      }
    }
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'  ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch selectBtn" style="display: none">
          <button class="btn btn-info btn-sm approvedBtn">批量批准</button>
          <button class="btn btn-warning btn-sm rejectedBtn">批量拒绝</button>
          <button class="btn btn-danger btn-sm deleteBtn">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">

        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="140">操作</th>
          </tr>
        </thead>
        <tbody id="content">
        </tbody>
      </table>
    </div>
  </div>
  <?php $current_page='comments'; ?>
  <?php include 'inc/sidebar.php'; ?>
  <div id="loading" style="display: none;">
    <div class="flip-txt-loading">
      <span>L</span><span>o</span><span>a</span><span>d</span><span>i</span><span>n</span><span>g</span>
    </div>
  </div>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript" src="/static/assets/vendors/jsrender/jsrender.min.js"></script>
  <script type="text/javascript" src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script id="comments" type="text/html">
    {{for comments}}
      <tr {{if status==='held'}} class='warning'{{else status==='rejected'}} class='danger'{{/if}}  data-id="{{:id}}">
        <td class="text-center"><input type="checkbox"></td>
        <td>{{:author}}</td>
        <td>{{:content}}</td>
        <td>{{:post_title}}</td>
        <td>{{:created}}</td>
        <td>{{:status}}</td>
        <td class="text-center">
          {{if status==='held'}}
            <a href="#" class="btn btn-info btn-xs btn-edit" data-status='approved'>批准</a>
            <a href="#" class="btn btn-warning btn-xs btn-edit" data-status='rejected'>拒绝</a>
          {{/if}}
          <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
        </td>
      </tr>
    {{/for}}
  </script>

  <script type="text/javascript">
    $(function($){
      var currentPage=1;
      // ajax请求时 页面加载的样式
      $(document)
       .ajaxStart(function () {
         NProgress.start();
         $('#loading').fadeIn();
       })
       .ajaxStop(function () {
         NProgress.done();
         $('#loading').fadeOut();
       });

      //ajax请求评论数据=============================
      function loadPageData(page){
        $('#content').fadeOut();
        $.getJSON('/admin/api/comments-list.php',{'page':page,'size':15},function(res){
          //分页插件,动态分页
          if(page>res.totalpages){
            loadPageData(res.totalpages);
            return;
          }
          $('.pagination').twbsPagination('destroy');
          $('.pagination').twbsPagination({
              first: '首页',
              last: '末页',
              prev: '&lt;',
              next: '&gt',
              startPage: page,
              totalPages: res.totalpages,
              visiblePages: 5,
              initiateStartPageClick: false,
              onPageClick: function(e,page){
                //加载点击页数据
                loadPageData(page);
              }
          })
          //渲染模板
          var html=$('#comments').render({
            comments: res.comments
          });
          $('#content').html(html).fadeIn();
          currentPage=page;
        });
      }
      //加载第一页
      loadPageData(currentPage);

      // 操作功能====================================
      //删除评论功能
      var $tbody= $('#content');
      $tbody.on('click','.btn-delete , .btn-reject , .btn-approve ',function(){
        var $tr=$(this).parent().parent();
        var id=$tr.data('id');
        if($(this).text()==='删除'){
          $.get('/admin/api/comments-delete.php',{id: id},function(res){
            // 服务端返回 Boolean值，根据服务端删除是否成功决定是否删除界面元素
            if(!res) return;
            // 重新加载当前页数据，而不是用remove()操作
            loadPageData(currentPage);
            // $tr.remove();
          });
        }
      });
      // 修改评论状态
      $tbody.on('click','.btn-edit',function(){
        var id=$(this).parent().parent().data('id');
        var status=$(this).data('status');
          $.post('/admin/api/comments-status.php?id='+id,{status: status},function(res){
            if(!res) return;
            loadPageData(currentPage);
          })
      })

      //批量操作功能===================
      var idArr=[];
      var selectAll=$('th>input[type=checkbox]');
      var selectBtns=$('.selectBtn');

      //单选操作
      $('tbody').on('change','input',function(){
        // 将未放入idArr的选项对应的id放入idArr,
        var id=$(this).parent().parent().data('id');
        $(this).prop('checked')? (idArr.indexOf(id)!==-1 || idArr.push(id)) : idArr.splice(idArr.indexOf(id),1);
        // 根据选中状态是否显示按钮
        idArr.length>0 ? selectBtns.fadeIn() : selectBtns.fadeOut();
      })
      //全选操作
      selectAll.on('change',function(){
        // 因为评论数据是异步请求，所以必须等数据渲染到页面后，选择器才能选中
        var selectList=$('td>input[type=checkbox]');
        var checked=$(this).prop('checked');
        selectList.prop('checked',checked).trigger('change');
      })

      //批量操作事件
      selectBtns
      // 批准
      .on('click','.approvedBtn',function(){
        $.post('/admin/api/comments-status.php?id='+idArr.join(','),{status: 'approved'},function(res){
            if(!res) return;
            loadPageData(currentPage);
            idArr=[];
            selectAll.prop('checked',false);
            selectBtns.fadeOut();
        })
      })
      // 拒绝
      .on('click','.rejectedBtn',function(){
        $.post('/admin/api/comments-status.php?id='+idArr.join(','),{status: 'rejected'},function(res){
            if(!res) return;
            loadPageData(currentPage);
            idArr=[];
            selectAll.prop('checked',false);
            selectBtns.fadeOut();
        })
      })
      // 删除
      .on('click','.deleteBtn',function(){
        $.get('/admin/api/comments-delete.php',{id: idArr.join(',')},function(res){
          // 服务端返回 Boolean值，根据服务端删除是否成功决定是否删除界面元素
          if(!res) return;
          // 重新加载当前页数据，而不是用remove()操作
          loadPageData(currentPage);
          idArr=[];
          selectAll.prop('checked',false);
          selectBtns.fadeOut();
        });
      })

    });
  </script>
  <script>NProgress.done()</script>
</body>
</html>
