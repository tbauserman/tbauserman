<?php
require_once '../classes/pdo.php'; 

$sql_ip="select id as mp_id,ip from media_players"; 
$stmt_ip=DB::Prepare($sql_ip);
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
    $stmt = DB::run("update media_players set online=:online where id=:mp_id",['mp_id'=>$ip['mp_id'],'online'=>$online]);
}

?>