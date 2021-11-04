<?php
require_once '../classes/pdo.php'; 
$sql="insert into accounts(account_email,account_password) values(:email,:password)"; 
$values=array(':email'=>'scott.summers@jjventures.com',':password'=>'balh'); 
$db = new DB(); 
$res = $db->run($sql,$values);
$id = $db->pdo->lastInsertId(); 
echo $id; 
?>