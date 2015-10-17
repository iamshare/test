<?php

//数据库 连接、断开、构造、析构


require_once("DB_Config.php");




$dbname = DB_DATEBASE;
$dbhost = DB_SERVER;
$dsn = "mysql:dbname=$dbname;host=$dbhost";

try{

    $dbPDO = new PDO($dsn, DB_USER, DB_PASSWORD);
}

catch(PDOException $e){
    echo "connect failed ".$e->getMessage();
}


?>