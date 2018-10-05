<?php
require_once '../functions.php';
//校验当前是否有用户登录
chen_get_current_user();

//编辑功能：获取数据库数据将数据显示在表单
if(!empty($_GET['id'])){
  $id=$_GET['id'];
  //防止sql注入
  if(!is_numeric($id)){
      exit('<h1>查询错误！</h1>');
  }
  $edit_current_categories=chen_fetch_one("select * from categories where id={$id};");
}
//添加分类信息
function add_categories(){
  //校验表单
  if(empty(trim($_POST['name']))){
    $GLOBALS['message']='名称不能为空哦！';
    return;
  }
  if(empty(trim($_POST['slug']))){
    $GLOBALS['message']='别名不能为空哦！';
    return;
  }
  //持久化
  $name=$_POST['name'];
  $slug=$_POST['slug'];
  $rows=chen_execute("insert into categories (name,slug) values ('{$name}','{$slug}');");
  // $success变量标识是否添加成功
  $GLOBALS['success']= $rows>0;
  $GLOBALS['message']= $rows<=0 ? '添加失败': '添加成功';
}
//编辑分类信息
function edit_categories(){
  global $edit_current_categories;
  //如果表单为空，则使用原有数据
  $name=empty(trim($_POST['name'])) ? $edit_current_categories['name'] : $_POST['name'];
  $slug=empty(trim($_POST['slug'])) ? $edit_current_categories['slug'] : $_POST['slug'];
  $edit_current_categories['name']=$name;
  $edit_current_categories['slug']=$slug;
  $id=$_GET['id'];

  $rows=chen_execute("update categories set name='{$name}',slug='{$slug}' where id='{$id}';");
  //设置 $success变量标识是否修改成功
  $GLOBALS['success']= $rows>0;
  $GLOBALS['message']= $rows<=0 ? '更新失败': '更新成功';
  if($GLOBALS['success']){
    //设置一个全局变量标记 编辑成功，跳转到添加功能模块
    $GLOBALS['flag']=true;
  }
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  //url后面id为空则为添加操作，否则为编辑操作
  if(empty($_GET['id'])){
    add_categories();
  }else{
    edit_categories();
  }
}

//查询放在修改操作下面
$categories=chen_fetch_all("select * from categories;");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($message)): ?>
        <?php if(isset($success) && $success): ?>
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
          <!-- $flag变量存在时代表编辑功能完成，销毁$edit_current_categories跳转到添加功能模块-->
          <?php if(isset($flag))unset($edit_current_categories); ?>
          <!-- 判断$edit_current_categories是否存在来设置表单为添加功能或者编辑功能 -->
          <form action="<?php echo isset($edit_current_categories) ? $_SERVER['PHP_SELF'].'?id='.$_GET['id'] : $_SERVER['PHP_SELF']; ?>" data-flag='<?php  ?>' method="POST" autocomplete="off">
            <h2><?php echo isset($edit_current_categories) ? '编辑'.($edit_current_categories['name']) : '添加';?>目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" value="<?php if(!empty($_POST['name']) && empty($success)&&empty($_GET['id'])) echo $_POST['name']; if(isset($edit_current_categories)) echo $edit_current_categories['name'];?>" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" value="<?php if(!empty($_POST['slug']) && empty($success)&&empty($_GET['id'])) echo $_POST['slug']; if(isset($edit_current_categories)) echo $edit_current_categories['slug']; ?>" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit"><?php echo isset($edit_current_categories) ? '保存' : '添加';?></button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action ">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm deleteAll" href="/admin/api/categories-delete.php" style="display: none;">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center " width="40"><input class="selectAll" type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody class="selectList">
              <!-- 遍历分类结果集 -->
            <?php if(isset($categories)): ?>
            <?php foreach ($categories as $item):?>
              <tr>
                <td class="text-center"><input data-id="<?php echo $item['id']?>" type="checkbox"></td>
                <td><?php echo $item['name'] ?></td>
                <td><?php echo $item['slug'] ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/api/categories-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
            <?php endforeach ?>
            <?php endif ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php $current_page='categories' ?>
  <?php include 'inc/sidebar.php'; ?>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript">
    $(function($){
      var idArr=[]; //存储选中的复选框的id
      var $deleteAll=$('.deleteAll');
      var $selectAll=$('.selectAll');
      var $checkboxList=$("tbody input");

      //单选事件，通过事件冒泡定位所选元素，显示批量删除按钮，当没有一个选中时，隐藏按钮
      $('.selectList').on('change',"input",function(){
        var id=$(this).data('id');
        //选中复选框，则将它对应的id放进idArr,取消选中则从idArr中删除
        if($(this).prop('checked')){
          // idArr.indexOf(id)!==-1 || idArr.push(id);
          idArr.includes(id) || idArr.push(id);
        }else{
          //取消选中，删除对应的id
          idArr.splice(idArr.indexOf(id),1);
        }
        //当idArr为空时，即没有按钮被选中，则隐藏批量删除按钮，同时如果全选框为选中状态，则取消选中
        idArr.length>0 ? $deleteAll.fadeIn() : $deleteAll.fadeOut();

        $deleteAll.prop('search', '?id='+idArr);
      });

      //全选事件，显示批量删除按钮，将所有选项的id放入idArr
      $selectAll.on('change',function(){

        var checked=$(this).prop('checked');
        $checkboxList.prop('checked',checked).trigger('change');
      });
    });

  </script>
  <script>NProgress.done();</script>
</body>
</html>
