<?php 
/**
 * 获取IP
 * 
 * @author Demon.zhang@samesamechina.com
 */
class getIpClass{
	public static function getIp()
	{
		global $HTTP_SERVER_VARS;
		if(getenv('HTTP_CLIENT_IP')) {
				$onlineip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR')) {
				$onlineip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR')) {
				$onlineip = getenv('REMOTE_ADDR');
		} else {
				$onlineip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
		}
		return $onlineip;
	}
}
?>