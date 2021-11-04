<?php 
require_once '../classes/pdo.php'; 
$method = $_SERVER['REQUEST_METHOD'];

if($method=='DELETE') {
    $db = new DB(); 
    parse_str(file_get_contents("php://input"),$_DELETE); 
    $sql= "delete from media_players where id=:id"; 
    $stmt = $db->run($sql,array(':id'=>$_DELETE['id']));
}