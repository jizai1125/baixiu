<?php
require_once '../../functions.php';
//接收客户端的ajax请求，返回评论数据
//处理分页参数========================
// 页码
$page=isset($_GET['page']) ? ($_GET['page']<=0 ? 1 : intval($_GET['page'])) : 1;
// 页大小
$size=isset($_GET['size']) ? ($_GET['size']<=0 ? 20 : intval($_GET['size'])) : 20;;
//根据页码计算越过多少条进行分页查询
$offset=($page-1)*$size;
//查询总评论数
$total_count=chen_fetch_one("select count(1) as count
from comments
inner join posts on comments.post_id=posts.id;")['count'];
// 总页数
$total_pages=ceil($total_count/$size);
if($page > $total_pages){
    header('location: /admin/api/comments-list.php?page='.$total_pages);
    exit;
}
// 分页查询评论数据===================
$comments=chen_fetch_all("select comments.*,
posts.title as post_title
from comments
inner join posts on comments.post_id=posts.id
order by comments.created desc
limit {$offset},{$size}");


// 设置响应体类型为JSON
header('Content-Type: application/json');

$json=json_encode(array(
    'totalpages' => $total_pages,
    'comments' => $comments
));
echo $json;
