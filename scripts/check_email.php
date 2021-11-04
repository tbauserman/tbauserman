<?php
require_once '../classes/pdo.php';
$db = new DB(); 
$email="bruce.wayne@jjventures.com"; 
$query ="select account_id from `accounts` WHERE `account_email` = :email"; 
$args = array(':email'=>$email);
$res = $db->run($query,$args); 
$row = $res->fetch(); 
echo $row['account_id'];
?>