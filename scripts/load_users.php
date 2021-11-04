<?php 
require_once '../classes/pdo.php'; 
$db = new DB(); 
$csv = fopen("collectors.csv",'r'); 
$sql = "INSERT INTO 
    accounts(

        account_email,
        account_enabled,
        first_name,
        last_name,
        phone,
        account_type_id,
        region_id
    ) 
    VALUES (
        :account_email, 
        :account_enabled, 
        :first_name, 
        :last_name, 
        :phone,
        :account_type_id,
        :region_id
    )"; 

if($csv !== FALSE) { 
    while(! feof($csv)) { 
        $data = fgetcsv($csv, 1000, ","); 
        print_r($data); 
        $sql_check = "select * from accounts where account_email=:account_email"; 
        if($data[4] === NULL) { $data[4] = ''; }
        $args_check = array(':account_email' => $data[4]); 
        $res_check = $db->run($sql_check,$args_check);
        if($res_check->rowCount()==0) {
            $args = array(
                ":account_email" => $data[4],
                'account_enabled' => 1, 
                ':first_name' => $data[2], 
                ':last_name' => $data[3], 
                ':phone' => $data[5], 
                ':account_type_id' => 3,
                ':region_id' => $data[1]
            ); 
            $db->run($sql,$args); 
        }
    }
}
fclose($csv); 
?>

