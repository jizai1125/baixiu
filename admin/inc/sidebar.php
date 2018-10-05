<!-- 也可以利用 $_SERVER['PHP_SELF'] 获取当前访问的url地址，替换$current_page -->
<?php
// 使用物理路径解决使用相对路径遇到的问题(当前文件的载入路径要根据载入该文件的文件路径决定，否则会找不到文件)，
require_once dirname(__FILE__).'/../../functions.php';
chen_get_current_user();
// 获取载入该页面的页面标识，突出显示在菜单项
$current_page=isset($current_page) ? $current_page : '';
// 获取当前登录的用户信息
$current_user=$_SESSION['current_user'];
?>
<div class="aside">
    <div class="profile">
      <img class="avatar" src="<?php echo $current_user['avatar']; ?>">
      <h3 class="name"><?php echo $current_user['nickname']; ?></h3>
    </div>
    <ul class="nav">
      <li<?php echo $current_page==='index' ? ' class="active"': ''; ?>>
        <a href="/admin/index.php"><i class="fa fa-dashboard"></i>仪表盘</a>
      </li>
      <?php $menu_posts=array('posts','post-add','categories'); ?>
      <li<?php echo in_array($current_page,$menu_posts) ? ' class="active"': ''; ?>>
        <a href="#menu-posts"<?php echo in_array($current_page,$menu_posts) ? '': ' class="collapsed"'; ?> data-toggle="collapse">
          <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-posts" class="collapse<?php echo in_array($current_page,$menu_posts) ? ' in': ''; ?>">
          <li<?php echo $current_page==='posts' ? ' class="active"': ''; ?>><a href="/admin/posts.php">所有文章</a></li>
          <li<?php echo $current_page==='post-add' ? ' class="active"': ''; ?>><a href="/admin/post-add.php">写文章</a></li>
          <li<?php echo $current_page==='categories' ? ' class="active"': ''; ?>><a href="/admin/categories.php">分类目录</a></li>
        </ul>
      </li>
      <li<?php echo $current_page==='comments' ? ' class="active"': ''; ?>>
        <a href="/admin/comments.php"><i class="fa fa-comments"></i>评论</a>
      </li>
      <li<?php echo $current_page==='users' ? ' class="active"': ''; ?>>
        <a href="/admin/users.php"><i class="fa fa-users"></i>用户</a>
      </li>
      <?php $menu_settings=array('nav-menus','slides','settings'); ?>
      <li<?php echo in_array($current_page,$menu_settings) ? ' class="active"': ''; ?>>
        <a href="#menu-settings"<?php echo in_array($current_page,$menu_settings) ? '': ' class="collapsed"'; ?> data-toggle="collapse">
          <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-settings" class="collapse<?php echo in_array($current_page,$menu_settings) ? ' in': ''; ?>">
          <li<?php echo $current_page==='nav-menus' ? ' class="active"': ''; ?>><a href="/admin/nav-menus.php">导航菜单</a></li>
          <li<?php echo $current_page==='slides' ? ' class="active"': ''; ?>><a href="/admin/slides.php">图片轮播</a></li>
          <li<?php echo $current_page==='settings' ? ' class="active"': ''; ?>><a href="/admin/settings.php">网站设置</a></li>
        </ul>
      </li>
       <li<?php echo $current_page==='douban' ? ' class="active"': ''; ?>>
        <a href="/admin/douban.php"><i class="fa fa-heart"></i>豆瓣电影</a>
      </li>
    </ul>
  </div>
