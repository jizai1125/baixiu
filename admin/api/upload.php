<?php
// 接收用户上传头像
if(empty($_FILES['avatar'])){
    exit('<h1>必须上传文件！</h1>');
}
$avatar=$_FILES['avatar'];
if($avatar['error']!==UPLOAD_ERR_OK){
    exit('h1>上传失败！</h1');
}
//校验文件类型
$allowed_types= array('image/jpeg','image/jpg','image/png','image/peng');
if(!in_array($avatar['type'],$allowed_types)){
  exit('h1>图片格式不支持！</h1');
}
// 限制大小为5M
if($avatar['size']>5*1024*1024){
exit('h1>图片太大啦！</h1');
}

//将文件从临时目录移到目标目录
$ext=pathinfo($avatar['name'],PATHINFO_EXTENSION);//取得文件的扩展名
//创建存储文件的文件夹
$dir='../../static/uploads/'.date('Ymd',time());
if(!is_dir($dir)){
mkdir($dir,0777,true);
}
//存储的目标路径
$target=$dir.'/img-'.uniqid().'.'.$ext;
if(!move_uploaded_file($avatar['tmp_name'],$target)){
    exit('h1>上传失败！</h1');
}
echo substr($target,5);
//返回文件存放路径
