<?php
//LoginOut.php
//用户退出操作


require_once("DB_Connect.php");

$response = array();




// if(isset($_POST['uid']) && isset($_POST['security_code'])){
// 	$uid = $_POST['uid'];
// 	$security_code = $_POST['security_code'];
// }else{
// 	$response['rootcode'] = '701';
// 	$response['message'] = 'Maybe lost one more parameters!';
// 	echo json_encode($response);
// 	return;
// }

if(isset($_POST['uid'])){
    $uid = $_POST['uid'];
}else{
	$response['rootcode'] = '701';
	$response['message'] = 'Parameters Wrong';
	echo json_encode($response);
	return;
}

// if(isset($_POST['security_code'])){
//     $security_code = $_POST['security_code'];
// }

// echo "cccc";
// $_SESSION[$session_prefix.".$uid."] = '';
// session_start();

// var_dump($_SESSION);
// exit;
// if($security_code != $session_id){
// 	$response['rootcode'] = '702';
// 	$response['message'] = 'security verify.';
// 	echo json_encode($response);
// 	return;
// }


session_start();
session_unset();
session_destroy();

$sql = "select line_status from chat_users where uid = '$uid'";


$stPDO = $dbPDO -> prepare($sql);
$stPDO -> execute();
$user_info = $stPDO -> fetch(PDO::FETCH_ASSOC);

if($user_info['line_status'] != '0'){
	//更新离线状态
	$sql = "update chat_users set line_status = 0";
	$stPDO = $dbPDO -> prepare($sql);
	$stPDO -> execute();
}


$response['rootcode'] = '700';
$response['message'] = "LoginOut Successfully!";
echo json_encode($response);

?>