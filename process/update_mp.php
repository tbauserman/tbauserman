<?php 
require_once '../classes/pdo.php'; 
$method = $_SERVER['REQUEST_METHOD'];

if($method=='PUT') {
    $db = new DB(); 
    parse_str(file_get_contents("php://input"),$_PUT); 
    $sql= "update media_players set ip=:ip, number=:number where id=:id"; 
    $args = array(
        ':ip' => $_PUT['ip'],
        ':number' => $_PUT['number'], 
        ':id' => $_PUT['id']
    );
    try {
    $stmt = $db->run($sql,$args); 
    } catch(PDOException $e) { 
        throw new Exception("Unable to update media player"); 
    }
   
    $results = array("success"=>True); 
    header("Content-Type: application/json"); 
    echo json_encode($results); 
}