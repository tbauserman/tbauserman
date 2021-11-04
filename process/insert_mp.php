<?php 
require_once '../classes/pdo.php'; 
$method = $_SERVER['REQUEST_METHOD'];
if($method == 'POST') {
    $db = new DB(); 
    $online=False; 
    $output = exec("ping -n 1 ".$_POST['ip'],$result); 
    if(strpos($output,"Lost = 1")) {
        $online = False; 
    } else { 
        $online = True; 
    }
    $sql= "INSERT INTO media_players(`customer_id`,`ip`,`number`,`online`) values(:customer_id,:ip,:number,:online)"; 
    $args = array(
        ':customer_id'=>$_POST['customer_id'],
        ':ip'=>$_POST['ip'],
        ':number'=>$_POST['number'], 
        ':online'=>$online
    ); 
    try {
    $stmt = $db->run($sql,$args); 
    } catch(PDOException $e) {
        throw new Exception("Unable to add media player"); 
    }
    $id = $db->pdo->lastInsertId(); 
    $result = array("id"=>$id); 
    echo json_encode($result); 
}
?>