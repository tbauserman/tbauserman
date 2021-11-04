<?php
require_once '../classes/pdo.php'; 
$db = new DB(); 
$email="jon.niebrugge@jjventures.com"; 
$password="abc@123"; 
$query = "INSERT INTO accounts(account_email,account_password) VALUES (:email,:password)"; 

$hash = password_hash($password, PASSWORD_DEFAULT); 
$args = array(':email'=>$email, ':password'=>$hash); 

try {
    $res = $db->run($query,$args); 
} catch(PDOException $e) { 
    throw new Exception('Error creating account'); 
}