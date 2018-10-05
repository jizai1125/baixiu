<?php
require_once '../functions.php';
chen_get_current_user();

// 接收数据筛选==================================
$where='1=1';
$search='';
if(isset($_GET['category'])&&$_GET['category']!=='all'){
  $where.=' and posts.category_id='.$_GET['category'];
  $search.='&category='.$_GET['category'];
}
if(isset($_GET['status'])&&$_GET['status']!=='all'){
  $where.=" and posts.status='{$_GET['status']}'";
  $search.='&status='.$_GET['status'];
}

//单页显示条数
$size=20;
// 当前页
$page=empty($_GET['page']) ? 1 : (int)$_GET['page'];
if($page<0){
  header('location: /admin/posts.php?page=1'.$search);
}
// 处理分页页码===============================
// 总文章数
$total_count=(int)chen_fetch_one("select count(1) as num from posts
inner join categories on posts.category_id=categories.id
inner join users on posts.user_id=users.id
where $where
")['num'];
// 总页数
$total_pages=(int)ceil($total_count/$size);
if($page>$total_pages){
  header("location: /admin/posts.php?page={$total_pages}".$search);
}
// 展示页码数
$visiables=5;
// 最小和最大展示的页码
$begin=$page-($visiables-1)/2;
$end=$page+($visiables-1)/2;

//begin不能小于 1 ，end不能大于最大页数
$begin=$begin<1 ? 1 : $begin;
$end=$begin+$visiables-1;

$end=$end>$total_pages ? $total_pages : $end;
$begin=$end-$visiables+1;
$begin=$begin<1 ? 1 : $begin;

//获取全部关联数据================================
$offset=($page-1)*$size;

$posts=chen_fetch_all("select posts.id,
posts.title,
users.nickname as user_name,
categories.name as category_name,
posts.created,
posts.status
from posts
inner join categories on posts.category_id=categories.id
inner join users on posts.user_id=users.id
where {$where}
order by posts.created desc
limit {$offset},{$size};");


$categories=chen_fetch_all('select * from categories;');
//数据处理，格式转换============================
/**
 * [convert_status 转换状态显示]
 * @param  string $status  英文
 * @return string         中文
 */
function convert_status(string $status){
  $dict=array(
    'published'=>'已发布',
    'drafted'=>'草稿',
    'trashed'=>'回收站'
  );
  return isset($dict[$status]) ? $dict[$status] : '未知';
}

/**
 * [convert_date 转换时间显示]
 * @param  string $created 日期格式2017-07-01 09:00:00
 * @return [type]          中文格式2017年07月01日 09:00:00
 */
function convert_date($created){
  $time=strtotime($created);
  return date('Y年m月d日<b\r>H:i:s',$time);
}


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm deleteAll" href="/admin/api/posts-delete.php" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $item):?>
            <option
              value="<?php echo $item['id'];?>"
              <?php echo isset($_GET['category'])&&$item['id']===$_GET['category'] ? 'selected' : '';?>>
              <?php echo $item['name']; ?>
            </option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="published" <?php echo isset($_GET['status'])&&$_GET['status']==='published' ? "selected" : '';?>>已发布</option>
            <option value="drafted" <?php echo isset($_GET['status'])&&$_GET['status']==='drafted' ? "selected" : '';?>>草稿</option>
            <option value="trashed" <?php echo isset($_GET['status'])&&$_GET['status']==='trashed' ? "selected" : '';?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <?php if($begin>1): ?>
          <li><a href="?page=<?php echo 1;?>">首页</a></li>
          <li><a href="?page=<?php echo $page-1;?>">上一页</a></li>
          <?php endif ?>
          <?php for($i=$begin; $i<=$end; $i++): ?>
            <li <?php echo $i===$page ? "class='active'" : '';?>>
              <a href="?page=<?php echo $i.$search;?>"><?php echo $i; ?>
              </a>
            </li>
          <?php endfor ?>
          <?php if($end<$total_pages): ?>
          <li><a href="?page=<?php echo $page+1; ?>">下一页</a></li>
          <li><a href="?page=<?php echo $total_pages; ?>">末页</a></li>
          <?php endif ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input class="selectAll" type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($posts as $item): ?>
          <tr>
            <td class="text-center"><input class="select" type="checkbox" data-id="<?php echo $item['id'];?>"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['category_name']; ?></td>
            <td class="text-center"><?php echo convert_date($item['created']); ?></td>
            <td class="text-center"><?php echo convert_status($item['status']) ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/api/posts-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
        <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php $current_page='posts'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript">
    $(function($){
      var idArr=[];
      var $selectAll=$('.selectAll');
      var $selectList=$('tbody .select');
      var $deleteAllBtn=$('a.deleteAll');
      //单选事件
      $selectList.on('change',function(){
        var id=$(this).data('id');
        //根据选中状态将对应的id 从idArr 加入或删除,如果id在idArr中已存在，则不做push操作
        $(this).prop('checked') ? (idArr.indexOf(id)!==-1 || idArr.push(id)) : idArr.splice(idArr.indexOf(id),1);
        // console.log(idArr);
        //将idArr 拼接到删除按钮 href ?后面
        $deleteAllBtn.prop('search','?id='+idArr);
        //判断idArr是否为空，空时隐藏删除按钮
        idArr.length>0 ? $deleteAllBtn.fadeIn() : $deleteAllBtn.fadeOut();
      })
      //全选事件
      $selectAll.on('change',function(){
        var checked=$(this).prop('checked');
        $selectList.prop('checked',checked).trigger('change');
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
