<?php

// session prefix Must be samed as iwebim & iwebsns
//The most security point is session mode .
//client use get/post/request send something like session as a key open a link for A-B.
//This session id must be encryption and decryption.

$session_prefix = "isns_"; 

$response = array();                // return JSON

                                    // response Body Maybe include: like follow index
 


                                    // response['user_info']    include all user information

                                    // response['message']      a common message for callback

                                    // response['rootcode']     It's just a verification code for switch work mode

                                            

                                            //rootcode = 701 = name error

                                            //rootcode = 702 = password error

                                            //rootcode = 700 = Success

                                    

//####################################### Fast Control Begin ##############################################



//注意：快速控制不再对 Email 或者 name 进行验证处理，下面sql执行验证的时候，会进行对比
//由于设备不同，后期需要查看相关信息。这里需要对login_device新赋值。属性置1。

if(!isset($_POST['user']) || strlen($_POST['user']) < 4){   //Fast Control before mysql connect
    $message = "Post Name is NULL or this name string short by 4 numbers";
    $response['message'] = $message;
    $response['rootcode'] = 701;  
}


if(!isset($_POST['pass'])){   
    $message = "Post Password is NULL";
    $response['message'] = $message;
    $response['rootcode'] = 702;   	
}

//####################################### Fast Control End ##############################################
//pass verification 

$username = $_POST['user'];
$password = md5($_POST['pass']);


//include db_connect get mysqli object
require_once("DB_Connect.php");       
//encryption session for a token Key.
require_once("Encryption.php");

//control session life time 生存周期，客户端需和WEB端不同。
$life_time = 3600 * 24 * 365; //limit: One year
session_set_cookie_params($life_time);              


$sql_verify_username = "select * from wy_users where user_name='$username'";
$sql_verify_email = "select * from wy_users where user_email='$username'";
$stPDO = $dbPDO -> prepare($sql_verify_username);
$stPDO -> execute();
$numResult = $stPDO -> rowCount();
if($numResult > 0){        							//verification result rows numbers
															//User_Name control
    $user_info = $stPDO -> fetch(PDO::FETCH_ASSOC);
    
    if($user_info){

        if($user_info['user_pws'] == $password)
        {
                //set login state
            session_start();
            //echo "user login successfully！";
            $_SESSION[$session_prefix.'userId'] = $user_info['user_id'];

        }
    }
}else{
												//Email Control
    $stPDO = $dbPDO -> prepare($sql_verify_email);
    $numResult = $stPDO -> rowCount();
    $stPDO -> execute();
    if($numResult > 0){     
        $user_info = $stPDO -> fetch(PDO::FETCH_ASSOC);
        if($user_info){
            if($user_info['user_pws'] == $password)
            {
                //set login state
                session_start();
                //echo "email mode login successfully！";
                $_SESSION[$session_prefix.'userId'] = $user_info['user_id'];
            }           
        }        
    }else{
        $message = "用户名或者邮箱无效！或者与密码不匹配！";
        $response['message'] = $message;   
		$response['rootcode'] = 703;  	
    }
}

//session 一旦启动，session_id 便作为唯一标识，并且做简单的 加密与解密 防止客户端使用PC端模拟POST看到规律





$session_id = session_id();



// $returnKey = encryption($session_id);


// $session_key = decryption($_POST['ss']);


//######################################  Session Control & Response JSON Begin #######################################################



if($_SESSION[$session_prefix.'userId']){

    //Set Session More Var

    $_SESSION['user_name'] = $user_info['user_name'];

    //$_SESSION['user_pws'] = $user_info['user_pws'];		//User_id was registered by verification login

    $_SESSION['user_ico'] = "http://www.uumatch.com/".$user_info['user_ico'];

    $_SESSION['security_code'] = $session_id;

	$_SESSION['isLogin'] = 1;							//This session variables is associate the table chat_users key assignments ['line_status']

    $safe_user_info = array();          //Use array fitering everything.protect json information.use make trouble to resolve security

    $now_time = date('Y-m-d H:i:s');     //update user lastlogin ip time   And chat line_status.On webserver.this line_status state has something wrong.

    $sql_wy_users="update wy_users set lastlogin_datetime='".$now_time."',login_ip='".$_SERVER[REMOTE_ADDR]."' where user_id=$user_info[user_id]";

    $hash_time = time();
    $sql_chat_users="update chat_users set last_time='".$hash_time."',line_status='1' where uid=$user_info[user_id]";


    $stPDO = $dbPDO -> prepare($sql_wy_users);
    $stPDO -> execute() or die("update wy_users login information crashs");
    
    $stPDO = $dbPDO -> prepare($sql_chat_users);
    $stPDO -> execute() or die("update chat_users login information crashs");
    
	//JSON return value used response variables.It's will include two associate tables. 

	//One is wy_users.

	//The second is chat_users.

	//Follow Key & Value == The table named wy_users

	

    $safe_user_info['uid'] = $user_info['user_id'];           //Comment 几个important attribute

    $safe_user_info['user_email'] = $user_info['user_email'];   

    $safe_user_info['user_name'] = $user_info['user_name']; //username

    //$safe_user_info['user_sex'] = $user_info['user_sex'];   //sex

    //$safe_user_info['country'] = $user_info['country']; //Comment By Root 

    //$safe_user_info['birth_province'] = $user_info['birth_province'];   

    //$safe_user_info['birth_city'] = $user_info['birth_city'];

    if(!$user_info['user_ico'] == ""){
            $safe_user_info['user_ico'] = "http://www.uumatch.com/".$user_info['user_ico']; 
    }else{
       $safe_user_info['user_ico'] = $user_info['user_ico'];  
    }
    //$safe_user_info['user_ico'] = "http://www.uumatch.com/".$user_info['user_ico'];               //avatar

    //$safe_user_info['user_add_time'] = $user_info['user_add_time'];     //register_time

    //$safe_user_info['birth_year'] = $user_info['birth_year'];

    //$safe_user_info['birth_month'] = $user_info['birth_month'];

    //$safe_user_info['birth_day'] = $user_info['birth_day'];

    //$safe_user_info['join_group'] = $user_info['join_group'];

    $safe_user_info['lastlogin_datetime'] = $now_time;   //上次登录时间  作为新登陆需要更新时间、IP和状态last login time.
    

    $safe_user_info['login_ip'] = $_SERVER['REMOTE_ADDR'];                       //last login ip

    $safe_user_info['security_code'] = $session_id;


    //Follow Key & Value == The table named chat_users.Maybe it is important.

    //$safe_user_info['last_time'] = time();                          //Important 这里是一个时间戳格式，切记不要格式化

    //$safe_user_info['line_status'] = 1;


    //array_push($response['user_info'], $safe_user_info);										//online status By Root 

    //$response['message'] = "登陆已经成功，Session开启！";

    
	$response = $safe_user_info;
	$response['message'] = "登陆成功";
	$response['rootcode'] = 700;
}

echo json_encode($response);

//######################################  Session Control & Response JSON End #######################################################





?>