<?php
require_once '../classes/pdo.php';

if(isset($_GET['term'])) {
    $db = new DB(); 
    $search_term=":search_term"; 
    $sql_term = "SET @term =:term";
    $stmt_term=$db->pdo->prepare($sql_term);
    $stmt_term->bindValue(":term","%".$_GET['term']."%",PDO::PARAM_STR);
    $stmt_term->execute(); 
    $sql_customer = "
        select 
            `legal_name`,
            `dba`,
            `igb_license`,
            `site_code`,
            `id` as customer_id 
        from customers 
        where 
            legal_name like @term 
            or dba like @term 
            or site_code like @term 
            or igb_license like @term
        ORDER BY dba
        "; 
    $res=$db->run($sql_customer);
    $customers=$res->fetchAll(); 
    echo json_encode($customers); 
}