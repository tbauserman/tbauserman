<?php
set_time_limit(3600);
$time_start = microtime(true); 
require_once '../classes/pdo.php'; 
$db = new DB(); 
$sql_ip="select id as mp_id,ip from media_players"; 
$stmt_ip=$db->run($sql_ip);
$stmt_ip->execute(); 
$ips = $stmt_ip->fetchAll(); 
foreach($ips as $ip) {
    $result = null;
    $output = exec("ping -n 1 ".$ip['ip'], $result);
    $online=True;  
    if(strpos($output,"Lost = 1")) {
        $online = False;
    } else {
        $online = True;
    }
    $sql="update media_players set online=:online where id=:mp_id"; 
    $args = array(":mp_id"=>$ip['mp_id'],":online"=>$online);
    $db->run($sql,$args); 
    //$stmt = $db->("update media_players set online=:online where id=:mp_id",['mp_id'=>$ip['mp_id'],'online'=>$online]);
}
$fh = fopen("check_online.log","a"); 
fwrite($fh,date('F jS Y h:i:s A')."\t"."check_online.php"."\t".(microtime(true)-$time_start)." seconds"); 
fclose($fh); 
?>