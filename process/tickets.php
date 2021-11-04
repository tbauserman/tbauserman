<?php
session_start(); 
require_once '../classes/account.php'; 
$account = new Account; 
if($account->sessionLogin()) { 
require_once '../classes/pdo.php'; 
$method = $_SERVER['REQUEST_METHOD']; 
$db = new DB(); 
if($method == 'PUT') { 
    parse_str(FILE_GET_CONTENTS("php://input"),$_PUT);
    $sql="update tickets set open=:open where id=:ticket_id"; 
    $args = array(":open"=>$_PUT['open'],":ticket_id"=>$_PUT['ticket_id']); 
    try { 
        $res = $db->run($sql, $args); 
    } catch(PDOException $e) { 
        throw new Exception("Unable to update ticket"); 
    }


    if($_PUT['ticket_notes']){ 
        $sql_notes = "
            INSERT INTO 
                ticket_notes(
                    ticket_id,
                    note,
                    rep_id
                ) values(
                    :ticket_id, 
                    :note, 
                    :rep_id
                )"; 
        
        $args_notes = array(
            ":ticket_id" => $_PUT['ticket_id'],
            ":note" => $_PUT['ticket_notes'],
            ":rep_id" => $account->id
        ); 

        try { 
            $res_notes = $db->run($sql_notes,$args_notes); 
        } catch(PDOException $e) { 
            throw new Exception("Unable to add note."); 
        }
    }
    $results=array("success"=>true);
    header("Content-Type: application/json"); 
    echo json_encode($results);
}
if($method == 'GET') {
    // Add stuff for the different search terms // 
    $query="
    SELECT
    tickets.id as ticket_id, 
    tickets.customer_id, 
    accounts_tech.account_id as tech_id, 
    accounts_rep.account_id as rep_id, 
    concat_ws(' ',accounts_tech.first_name,accounts_tech.last_name) as name_tech,  
    customers.legal_name, 
    customers.dba, 
    customers.site_code, 
    customers.igb_license, 
    customers.site_code,  
    tickets.open, 
    tickets.vgt_no,
    GROUP_CONCAT(
        '<b>',
        CONCAT_WS(' ',accounts_rep.first_name,accounts_rep.last_name), 
        DATE_FORMAT(ticket_notes.timestamp,'%l:%i %p %b %D %Y'),
        '</b>'
        '&#010;',
        ticket_notes.note,
        '&#010;'
        SEPARATOR ''
    ) as ticket_notes
    FROM 
        tickets
        JOIN 
            customers 
            ON tickets.customer_id = customers.id
        JOIN 
            accounts AS accounts_tech 
            ON tickets.tech_id = accounts_tech.account_id
        LEFT JOIN 
            (
                ticket_notes 
                JOIN 
                    accounts as accounts_rep 
                ON ticket_notes.rep_id=accounts_rep.account_id
            ) on tickets.id = ticket_notes.ticket_id "; 
    $args = array(); 
    
        $filters = array("dba","legal_name","igb_license","site_code","name_tech"); 
        $sep = "WHERE"; 
        foreach($filters as $filter) {
            if($_GET['filter'][$filter]!='') {
                switch($filter) {
                    case 'name_tech': 
                        $query.=$sep." (accounts_tech.first_name like :filter_first_name or accounts_tech.last_name like :filter_last_name)"; 
                        $args[":filter_first_name"] = "%".$_GET['filter'][$filter]."%";
                        $args[":filter_last_name"] = "%".$_GET['filter'][$filter]."%";                          
                        break; 
                    default: 
                        $query.=$sep." ".$filter." like :".$filter." "; 
                        $args[":".$filter] = "%".$_GET['filter'][$filter]."%"; 
                }
                $sep = "AND";
            }
        }
    
    $query.=" GROUP BY ticket_id"; 


    try { 
        $stmt = $db->run($query,$args); 
    } catch(PDOException $e) {
        throw new Exception($e->getMessage()); 
    }
    $results = $stmt->fetchAll(); 
    if(count($results)==0) { 
        $results = array("count"=>0); 
    }
    header("Content-Type: application/json"); 
    echo json_encode($results); 
}
if($method == 'POST') {
   $sql = "
    INSERT INTO 
        tickets(
            `customer_id`,
            `tech_id`,
            `open`,
            `vgt_no`
        ) values(
            :customer_id,
            :tech_id,
            :open, 
            :vgt_no
        )
    "; 
    $args = array(
        ":customer_id" => $_POST['customer_id'],
        ":tech_id" => $_POST['tech_id'], 
        ":open" => 1, 
        ":vgt_no" => $_POST['vgt_no']
    ); 
    try {
        $db->run($sql,$args);
    } catch(PDOException $e){
        throw new Exception($e->getMessage()); 
    }

    $ticket_id = $db->pdo->lastInsertId();
    $sql_note = "
        INSERT INTO 
            ticket_notes(
                `ticket_id`, 
                `rep_id`,
                `note`
            )
            VALUES (
                :ticket_id, 
                :rep_id, 
                :note
            )
        ";
    $args = array(
        ":ticket_id" => $ticket_id,
        ":rep_id" => $account->id, 
        ":note" => $_POST['ticket_notes']
    ); 
    try {
        $db->run($sql_note,$args); 
    } catch(PDOException $e) {
        throw new Exception($e->getMessage()); 
    }
    $results = array("success"=>True); 
    header("Content-Type: application/json"); 
    echo json_encode($results); 

}
if($method=='DELETE') {
    parse_str(file_get_contents("php://input"),$_DELETE); 
    $sql = "delete from tickets where id=:id";
    $args = array(":id"=>$_DELETE['ticket_id']); 
    try {
        $db->run($sql,$args); 
    } catch(PDOException $e) { 
        throw new Exception($e->getMessage());
    }
    $results = array("Success"=>true); 
    header("Content-Type: application/json"); 
    echo json_encode($results); 
}
}
?>