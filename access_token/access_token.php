<?php
//https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx862ff299a491c6c2&secret=114bd225b8c0da5f47dbc8b1386e771d
//获取access_token
/*
$token=file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx862ff299a491c6c2&secret=114bd225b8c0da5f47dbc8b1386e771d");
print_r($token);
*/
$url=isset($_REQUEST['url'])?$_REQUEST['url']:"http://lancasterld.samesamechina.com";
$time=file_get_contents("time.txt");
$access_token=file_get_contents("access_token.txt");
$ticket=file_get_contents("ticket.txt");
if(time()-$time>=100){
	//token过期重新获取
	$appid="wx77e36122ac152d47";
	$secret="406d2bd1165654ed9225370faf499dea";
	$token=file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret);
	$token=json_decode($token,true);
	$access_token=$token['access_token'];
	//获取ticket
	$ticketfile=file_get_contents("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi");
	$ticketfile=json_decode($ticketfile,true);
	$ticket=$ticketfile['ticket'];
	$time=time();
	$fp = fopen("time.txt", "w");
	fwrite($fp,$time);
	fclose($fp);
	$fp = fopen("access_token.txt", "w");
	fwrite($fp,$access_token);
	fclose($fp);
	$fp = fopen("ticket.txt", "w");
	fwrite($fp,$ticket);
	fclose($fp);
}
$time=time();
$ticketstr="jsapi_ticket=".$ticket."&noncestr=asdkhaedhqwui&timestamp=".$time."&url=".$url;
$sign=sha1($ticketstr);
echo json_encode(array("time"=>$time."","access_token"=>$access_token,"sign"=>$sign));
exit();

?>