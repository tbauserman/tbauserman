<?php
    session_start();
    require "./classes/account.php";
    $account = new Account(); 
    if($account->sessionLogin()) {
        $account->logout(); 
        header("Location: index.php");
    } 
?>