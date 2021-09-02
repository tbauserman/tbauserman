<?php
require_once '../classes/pdo.php'; 
$csv = fopen("customers.csv","r");
$regions = array(); 
$stmt_region = DB::prepare("INSERT INTO regions VALUES(NULL,?)");
$stmt_customer = DB::prepare("INSERT INTO customers(`legal_name`,`dba`,`region_id`,`site_code`,`igb_license`) VALUES(:legal_name,:dba,:region_id,:site_code,:igb_license)");
if($csv !== FALSE) {
    while(! feof($csv)) { 
        $data = fgetcsv($csv, 1000, ",");  

        if(!in_array($data[2],$regions)) {
            //$stmt_region->execute([$data[2]]); 
            // get id and create regions array // 
            //var_dump(DB::lastInsertId());
            $regions[]=$data[2]; 
        }
        $customers[] = $data; 
    }
}

$counter = 0;
foreach($regions as $region) {
    $stmt_region->execute([$region]); 
    $db_regions[$region]=DB::lastInsertId();
    //var_dump(DB::lastInsertId());
}
$counter = 0;
foreach($customers as $customer) {
    $stmt_customer->execute(
        array(
           ':legal_name'    => $customer[1],
           ':dba'           => $customer[5],
           ':region_id'     =>$db_regions[$customer[2]], 
           ':site_code'     =>$customer[0],
           ':igb_license'   =>$customer[4],
        )
    );
}

fclose($csv); 
?>