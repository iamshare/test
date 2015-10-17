<?php
//用于客户端作为一个token使用，但由于Session周期较长，这里只用于混淆。简单的处理26位到32位+7位。

function encryption($session_id){

	$response = '';

	//注意这里使用md5加密的方式绝对不行，Ex:md5不可逆。
	//Mode = ccrhlclc4a682jmv1j1a66bt86  26位识别 打乱

	$session_normal = $session_id;

	$strOne = substr($session_id, 23);	//后3
	$session_id = strrev($session_id);	//反转
	$strTwo = substr($session_id, 23);	//反转的前3



	$strThree = substr($session_normal, 3, 20);
	$strThree = strrev($strThree);
	$strThree = bin2hex($strThree);



	$all = $strOne.$strThree.$strTwo;
	$response = bin2hex($all);




	return $response;

}


function decryption($session_code_security){


	$response = '';

	$all = hex2bin($all);


	$all_back = $all;

	//rvlsdahmt7nb7tvrci1p8nuha6
	//rvlsdahmt7nb7tvrci1p8nuha6


	$strOne = substr($all, 0, 3);

	//pu47530687335766f6f626267736b6e7631713663319p9
	$strTwo = substr($all, 3, 40);
	$strTwo = hex2bin($strTwo);
	$strTwo = strrev($strTwo);



	$strThree = substr($all, 43,46);
	$strThree = strrev($strThree);



	$response =  $strThree.$strTwo.$strOne;


	return $response;


}


?>