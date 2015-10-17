<?php
//Root.php
//仅供用于功能性测试


require_once("DB_Connect.php");

if(isset($_GET['uid'])){
    $uid = $_GET['uid'];
}

if(isset($_GET['pals_id'])){
    $pals_id = $_GET['pals_id'];
}

$player_ids = array($uid, $pals_id);
sort($player_ids);
$player_ids = implode(',', $player_ids);
$player_ids = ','.$player_ids.',';          //player_ids 组合成功


$sql = "select * from chat_session where player_ids='$player_ids'";

$stPDO = $dbPDO -> prepare($sql);
$stPDO -> execute();

echo $stPDO -> rowCount();

$row = $stPDO -> fetch(PDO::FETCH_ASSOC);


$pt = $row['readed_pagect_position'];

$temp_array = explode(",", $pt);
//var_dump($temp_array);


//注意此处的$temp_array = 类似 1->8,1->2  但是，由于im数据库对session的用户ID排序有影响，所有就是
//前面的ID小的，对应的读取位置是前面的1->8,大于的ID是后面的1->2.


$need_read_positon = '';
if($uid > $pals_id){
    $need_read_positon = $temp_array[1];
}else{
    $need_read_positon = $temp_array[0];
}


$returnValue = explode("->", $need_read_positon);   //最终返回的数据就死活 $returnValue[0]=查看到的page_num,$returnValue[1]=该页面查看的到位置

return $returnValue;


?>