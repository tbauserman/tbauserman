<?php 
error_reporting(E_ALL ^ E_WARNING);
require_once '../classes/pdo.php'; 

$method = $_SERVER['REQUEST_METHOD']; 

if($method == 'GET') {
    $db = new DB();
    if($_GET['process_filter']) {
        $query=" 
        SELECT
        media_players.id as mp_id,
        customers.id as customer_id,
        customers.dba,
        customers.legal_name, 
        customers.igb_license, 
        customers.site_code, 
        media_players.ip, 
        media_players.video,
        media_players.number,
        media_players.online 
            FROM customers
        JOIN 
            media_players on media_players.customer_id=customers.id
        "; 
        
        $filters = array("dba","legal_name","igb_license","site_code","ip","video","online"); 
        $sep = "WHERE"; 
        
        foreach($filters as $filter) { 
            if($_GET['filter'][$filter]) {
                $query.=$sep." ".$filter." like :".$filter." "; 
                $args[":".$filter] = "%".$_GET['filter'][$filter]."%"; 
                $sep = "AND";
            }
        }

        try { 
            $stmt = $db->run($query,$args); 
        } catch(PDOException $e) {
            throw new Exception($e); 
        }
        $results = $stmt->fetchAll();  
        if(count($results)==0) {
            $results = array("count"=>0);
        }
    } else if($_GET['mp_search']) {
        $search_term = ":search_term";
        $sql_term = "SET @term = :term";
        $stmt_term = $db->pdo->prepare($sql_term); 
        $stmt_term->bindValue(":term","%".$_GET['mp_search']."%",PDO::PARAM_STR); 
        $stmt_term->execute(); 
        if($_GET['customer_id']) { 
            $query=" 
            SELECT
            media_players.id as mp_id,
            customers.id as customer_id,
            customers.dba,
            customers.legal_name, 
            customers.igb_license, 
            customers.site_code, 
            media_players.ip, 
            media_players.video,
            media_players.number,
            media_players.online 
            FROM customers
            LEFT JOIN 
                media_players on media_players.customer_id=customers.id
                where customers.id= :customer_id
            "; 
            $args = array(':customer_id'=>$_GET['customer_id']);
            $stmt = $db->run($query,$args); 
        } else { 
            $query="
            SELECT
            media_players.id as mp_id,
            customers.id as customer_id,
            customers.dba,
            customers.legal_name, 
            customers.igb_license, 
            customers.site_code, 
            media_players.ip, 
            media_players.video,
            media_players.number,
            media_players.online 
            FROM customers
            LEFT JOIN 
                media_players on media_players.customer_id=customers.id
            WHERE 
            dba like @term
            or customers.legal_name like @term 
            or customers.igb_license like @term
            or customers.site_code like @term
            or media_players.video like @term
            ";
            $stmt = $db->run($query); 
        }

    $stmt->execute(); 
    //$results = DB::run($query)->fetchAll(); 
    $results = $stmt->fetchAll(); 
    } else { 
    $query = " 
    SELECT
            media_players.id as mp_id,
            customers.id as customer_id,
            customers.dba,
            customers.legal_name, 
            customers.igb_license, 
            customers.site_code, 
            media_players.ip, 
            media_players.video,
            media_players.number, 
            media_players.online
            FROM media_players
            JOIN 
                customers on media_players.customer_id=customers.id
    "; 
  
    $stmt_mp = $db->run($query);  
    $results = $stmt_mp->fetchAll(); 
    }

    foreach($results as $row) {
        
        if($row['online']) {
            $online = "<span class='fas fa-check' style='color:#00ff00'></span>"; 
        } else { 
            $online = "<span class='fas fa-times' style='color:#ff0000'></span>";
        }
        
        $output[] = array(
            'customer_id' => $row['customer_id'], 
            'id' => $row['mp_id'], 
            'dba' => $row['dba'], 
            'legal_name' => $row['legal_name'], 
            'igb_license' => $row['igb_license'], 
            'site_code' => $row['site_code'],
            'number' => $row['number'], 
            'ip' => $row['ip'], 
            'video' => $row['video'], 
            'online' => $online, 
        );
        
    }
    header("Content-Type: application/json");
    echo json_encode($output); 
}

