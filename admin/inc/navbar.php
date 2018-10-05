<?php
require_once dirname(__FILE__).'/../../functions.php';
chen_get_current_user();
?>
<nav class="navbar">
    <button class="btn btn-default navbar-btn fa fa-bars"></button>
    <ul class="nav navbar-nav navbar-right">
        <li><a href="/admin/profile.php"><i class="fa fa-user"></i>个人中心</a></li>
        <li><a href="/admin/login.php?action=logout"><i class="fa fa-sign-out"></i>退出</a></li>
    </ul>
</nav>
