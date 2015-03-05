<?php
session_start();
include_once('./config/database.php');
include_once('./config/Pdb.php');
if(!isset($_SESSION["openid"])){
	header("Location: http://lancaster.ifm-paris.cn/Wechat/oauth2?callback=http://lancasterld.samesamechina.com/");
	exit;
}
$db=Pdb::getDb();

$sql="select id from same_weixin_march where openid=".$db->quote($_SESSION["openid"]);
$rs=$db->getOne($sql);
if(!$rs){
	$sqlin="insert into same_weixin_march set openid=".$db->quote($_SESSION["openid"]);
	$db->execute($sqlin);
}

$sql="select id from same_weixin_user where openid=".$db->quote($_SESSION["openid"]);
$rs=$db->getOne($sql);
if($rs){
	header("Location: http://lancasterld.samesamechina.com/home.html#index");
	exit;
}
header("Location: http://lancasterld.samesamechina.com/home.html#attention");
exit;
?>