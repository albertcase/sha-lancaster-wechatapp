<?php
//https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx862ff299a491c6c2&secret=114bd225b8c0da5f47dbc8b1386e771d
//获取access_token
/*
$token=file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx862ff299a491c6c2&secret=114bd225b8c0da5f47dbc8b1386e771d");
print_r($token);
*/
$url=isset($_REQUEST['url'])?$_REQUEST['url']:"http://lancasterld.samesamechina.com";
$time=file_get_contents("time.txt");
$sign=file_get_contents("access_token.txt");
if(time()-$time>=10){
	//token过期重新获取
	$appid="wx77e36122ac152d47";
	$secret="406d2bd1165654ed9225370faf499dea";
	$token=file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret);
	$token=json_decode($token,true);
	$access_token=$token['access_token'];
	$time=time();
	$fp = fopen("time.txt", "w");
	fwrite($fp,$time);
	fclose($fp);
	$ticketfile=file_get_contents("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi");
	$ticketfile=json_decode($ticketfile,true);
	$ticket=$ticketfile['ticket'];
	$ticketstr="jsapi_ticket=".$ticket."&noncestr=asdkhaedhqwui&timestamp=".$time."&url=".$url;
	$sign=sha1($ticketstr);
	$fp = fopen("access_token.txt", "w");
	fwrite($fp,$sign);
	fclose($fp);
}
echo json_encode(array("time"=>$time,"sign"=>$sign));
exit();

?>