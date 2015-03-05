<?php
session_start();
if(!isset($_SESSION['openid'])){
	//未授权
	Header("Location: ");
}
?>