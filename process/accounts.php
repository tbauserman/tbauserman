<?php
session_start(); 
require '../classes/account.php'; 
$account = new Account(); 
$method = $_SERVER['REQUEST_METHOD']; 
if($account->sessionLogin() && $method == 'GET') {
    $account_id=False;
    $term = trim($_GET['term']); 
    if(isset($_GET['account_id'])) {
    $account_id = intval($_GET['term']); 
    }
    $rows = $account->getAccounts($account_id,$term); 
    
    foreach($rows as $row) { 
        if($row['admin']) {
            $admin = "<span class='fas fa-check' style='color:#00ff00'></span>"; 
        } else { 
            $admin = "<span class='fas fa-times' style='color:#ff0000'></span>";
        }

        if($row['account_enabled']) {
            $enabled = "<span class='fas fa-check' style='color:#00ff00'></span>"; 
        } else { 
            $enabled = "<span class='fas fa-times' style='color:#ff0000'></span>";
        }

        $output[] = array(
            'account_id' => $row['account_id'], 
            'region' => $row['region'],
            'first_name' => $row['first_name'], 
            'last_name' => $row['last_name'], 
            'account_email' => $row['account_email'], 
            'phone' => $row['phone'], 
            'account_type' => $row['account_type'],
            'enabled' => $enabled, 
            'admin' => $admin
        );
    }
    header("Content-Type: application/json");
    echo json_encode($output);
}