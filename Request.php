<?php
session_start();
include_once('./config/database.php');
include_once('./config/Pdb.php');
include_once('./config/getIpClass.php');
$_POST=$_REQUEST;
$db=Pdb::getDb();
if(isset($_POST['model'])){
	switch ($_POST['model']) {
		case 'islogin':
			if(!isset($_SESSION["openid"])){
				print json_encode(array("code"=>0,"msg"=>"未登录"));
				exit;
			}
			$sql="select * from same_weixin_march where openid=".$db->quote($_SESSION["openid"]);
			$rs=$db->getRow($sql,true);
			print json_encode(array("code"=>1,"msg"=>$rs));
			exit;
			break;

		case 'lotterydraw':
			if(!isset($_SESSION["openid"])){
				print json_encode(array("code"=>0,"msg"=>"未登录"));
				exit;
			}
			
			$sql="select drawstatus,lotterystatus from same_weixin_march where openid=".$db->quote($_SESSION["openid"]);
			$checkrs=$db->getRow($sql,true);
			//检测是否有抽奖资格
			if($checkrs['drawstatus']==1){
				//已经抽过
				print json_encode(array("code"=>3,"msg"=>"对不起，您已经没有抽奖机会了"));
				exit;
			}
			//检测是否中过奖
			if($checkrs['lotterystatus']!=0){
				//中过奖
				$sql="update same_weixin_march set drawstatus=1 where openid=".$db->quote($_SESSION['openid']);
				$db->execute($sql);
				$drawLog="insert into same_weixin_march_log set openid=".$db->quote($_SESSION["openid"]).",status=0";
				$db->execute($drawLog);
				print json_encode(array("code"=>2,"msg"=>"未中奖"));
				exit;
			}
			//未中过奖 给与概率
			$lotteryline=1000;
			$rand=mt_rand(1,10000);
			if($rand>$lotteryline){
				//未中奖
				$sql="update same_weixin_march set drawstatus=1 where openid=".$db->quote($_SESSION['openid']);
				$db->execute($sql);
				$drawLog="insert into same_weixin_march_log set openid=".$db->quote($_SESSION["openid"]).",status=0";
				$db->execute($drawLog);
				print json_encode(array("code"=>2,"msg"=>"未中奖"));
				exit;
			}
			//中奖 设置中奖上限
			$totalnum=100;
			$total=$db->getOne("select count(id) from same_weixin_march where lotterystatus<>0");
			if($total>=$totalnum){
				//奖品已经发完
				$sql="update same_weixin_march set drawstatus=1 where openid=".$db->quote($_SESSION['openid']);
				$db->execute($sql);
				$drawLog="insert into same_weixin_march_log set openid=".$db->quote($_SESSION["openid"]).",status=0";
				$db->execute($drawLog);
				print json_encode(array("code"=>2,"msg"=>"未中奖"));
				exit;
			}
			$sql="update same_weixin_march set drawstatus=1,lotterystatus=1 where openid=".$db->quote($_SESSION['openid']);
			$db->execute($sql);
			$drawLog="insert into same_weixin_march_log set openid=".$db->quote($_SESSION["openid"]).",status=1";
			$db->execute($drawLog);
			print json_encode(array("code"=>1,"msg"=>"恭喜中奖"));
			exit;
			break;
	
		case 'share':
			if(!isset($_SESSION["openid"])){
				print json_encode(array("code"=>0,"msg"=>"未登录"));
				exit;
			}
			$source=isset($_POST['source'])?$_POST['source']:"";
			$sql="update same_weixin_march set drawstatus=0 where openid=".$db->quote($_SESSION['openid']);
			$db->execute($sql);
			$shareLog="insert into same_weixin_march_share set openid=".$db->quote($_SESSION["openid"]).",source=".$db->quote($source);
			$db->execute($shareLog);
			print json_encode(array("code"=>1,"msg"=>"分享记录成功"));
			exit;
			break;
		case 'finish':
			if(!isset($_SESSION["openid"])){
				print json_encode(array("code"=>0,"msg"=>"未登录"));
				exit;
			}
			$tag=false;
			$name=isset($_POST['name'])?$_POST['name']:$tag=true;
			$mobile=isset($_POST['mobile'])?$_POST['mobile']:$tag=true;
			$address=isset($_POST['address'])?$_POST['address']:$tag=true;
			if($tag){
				print json_encode(array("code"=>2,"msg"=>"请填写必填项"));
				exit;
			}
			$sql="update same_weixin_march set name=".$db->quote($name).",mobile=".$db->quote($mobile).",address=".$db->quote($address)." where openid='".$_SESSION["openid"]."'";
			$db->execute($sql);
			print json_encode(array("code"=>1,"msg"=>"提交成功"));
			exit;
			break;
		case 'test':
			$_SESSION["openid"]=123123123;
			var_dump($_SESSION);
			break;	
		default:
			# code...
			print json_encode(array("code"=>9999,"msg"=>"No Model"));
			exit;
			break;
	}
}
print "error";
exit;