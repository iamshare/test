<?php
//ChangePass.php
//用户更改密码的接口

require_once("List/Config.php");
require_once("DB_Connect.php");

$response = array();


/*
*@param 用户名
*@param 老密码
*@param 新密码
*/

if(isset($_POST['uid'])){
	$uid=$_POST['uid'];
}
if(isset($_POST['oldpass'])){
	$oldpass=md5($_POST['oldpass']);
}
if(isset($_POST['newpass'])){
	$newpass=md5($_POST['newpass']);
}

$sql = "updata wy_users set user_pws='$newpass' where user_id='$uid'";
$stPDO = $dbPDO -> prepare($sql);
$stPDO -> execute();
if($dbPDO->lastInsertId()){
	$response['rootcode'] = "700";
	$response['message'] = "更改密码成功";
}else{
	$response['rootcode'] = "710";
	$response['message'] = "更改密码失败";
}

echo json_encode($response);

?>