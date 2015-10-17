<?php

require_once("DB_Connect.php");   //引入db_config，使用PDO抽象层
$session_prefix = "isns_"; 

//####################################### Fast Control Begin ##############################################

//注意：快速控制不再对 Email 或者 name 进行验证处理，下面sql执行验证的时候，会进行对比

if(!isset($_POST['user']) || strlen($_POST['user']) < 4){   //Fast Control before mysql connect

    $message = "Post Name is NULL Or this name string short by 4 numbers";
    $response['message'] = $message;
    $response['rootcode'] = 701;       
}else{
    $user = $_POST['user'];
}



if(!isset($_POST['pass']) || strlen($_POST['pass']) < 6){   

    $message = "Post Password is NULL Or less than 6 ";
    $response['message'] = $message;
    $response['rootcode'] = 702;              
}else{
    $pass = $_POST['pass'];
}

if(!isset($_POST['repass']) || strlen($_POST['repass']) < 6){   

    $message = "Post RePassword is NULL Or less than 6 ";
    $response['message'] = $message;
    $response['rootcode'] = 703;                      
}else{
    $repass = $_POST['repass'];
}


if(!isset($_POST['email'])){   

    $message = "Post Email is NULL Or type is wrong ";
    $response['message'] = $message;
    $response['rootcode'] = 704;                     
}else{
    $temp_email = $_POST['email'];          //格式匹配处理
    $pattern = '/^[\w\-\.]+@[\w\-\.]+(\.[\w\-]+)+$/i';
    if(preg_match($pattern, $temp_email)){  //preg_match 匹配错误返回false,不匹配返回0，匹配返回1
        
        $email = $temp_email;
        $message="Email is matched";
        $response['message'] = $message;
        
    }else{
        $message='Email is not matched! please type a using email account';
        $response['message'] = $message;
    }
    
}

if(!isset($_POST['sex'])){   

    $message = "Post Sex is NULL Or";
    $response['message'] = $message;
    $response['rootcode'] = 704;      
    $sex = 1;                   //设置默认   用户没有设置 设置一个默认值
}else{
    $sex = $_POST['sex'];
}

if(!isset($_POST['birth_year'])){   

    $message = "Post Sex is NULL Or";
    $response['message'] = $message;
    $response['rootcode'] = 704;     
    $birth_year = '';           //设置默认   用户没有设置 设置一个默认值
}else{
    $birth_year = $_POST['birth_year'];
}

if(!isset($_POST['birth_month'])){   

    $message = "Post Sex is NULL Or";
    $response['message'] = $message;
    $response['rootcode'] = 704;         
    $birth_month = '';           //设置默认   用户没有设置 设置一个默认值
}else{
    $birth_month = $_POST['birth_month'];
}


if(!isset($_POST['birth_day'])){   

    $message = "Post Sex is NULL Or";
    $response['message'] = $message;
    $response['rootcode'] = 704;       
    $birth_day = '';           //设置默认   用户没有设置 设置一个默认值
}else{
    $birth_day = $_POST['birth_day'];
}


//格式验证  成功后，进行数据库匹配  Begin
//验证目标： username 是否重复   email 是否重复



try{
   
    $sqlusername = "select * from wy_users where user_name='$user'";
    $sqluseremail = "select * from wy_users where user_email='$email'";
    
    $statement = $dbPDO -> prepare($sqlusername);
    $statement->execute();
    $result_username = $statement->fetch(PDO::FETCH_ASSOC);

    if($result_username){
        $message =  "你输入的用户名已存在，请更换其他用户名！";
        $response['message'] = $message;
    }else{
        $message = "用户名可用";
        $response['message'] = $message;
    }
    
    $statement = $dbPDO -> prepare($sqluseremail);
    $statement->execute();
    $result_useremail = $statement->fetch(PDO::FETCH_ASSOC);
    if($result_useremail){
        $message =  "你输入的邮箱已存在，请更换其他邮箱！";
        $response['message'] = $message;
    }else{
        $message = "邮箱可用";
        $response['message'] = $message;
    }
    
    
    //邮箱,用户名,pass,sex,birth 已经全部被定义，insert 到 MySQL
    
    $pass = md5($pass); //给密码md5加密
    $sql = "insert into wy_users(user_email, user_name, user_pws, user_sex, birth_year, birth_month, birth_day) values('$email', '$user', '$pass', '$sex', '$birth_year', '$birth_month', '$birth_day')";
    $statement = $dbPDO -> prepare($sql);
    if($statement -> execute()){    //插入用户成功
        $message =  "注册成功！";
        $response['message']  = $message;
        $lastinsertid = $dbPDO -> lastInsertId();
        session_start();
        $_SESSION[$session_prefix.'userId'] = $lastinsertid;
        $response['uid'] = $lastinsertid;
        $response['rootcode'] = 700;
        $response['line_status'] = 1;                       //直接设置在线状态


        //同步更新chat_users
        
        $time = time();
        $sql = "insert into chat_users(u_name, line_status, last_time) values('$user', '1', '$time')";
        $stPDO = $dbPDO -> prepare($sql);
        $stPDO -> execute();

    }else{
        $message = "注册失败！";
        $response['message']  = $message;
        $response['line_status'] = 0;  
    }
 
    
    echo json_encode($response);
}

catch(PDOException $e){
    echo "connect failed ".$e->getMessage();
}



//####################################### Fast Control End ##############################################







?>