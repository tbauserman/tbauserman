<?php
require "classes/pdo.php"; 
require "vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 
$db = new DB(); 

$headers=array(
    "region"=>"Region", 
    "dba"=>"DBA", 
    "legal_name"=>"Legal Name", 
    "igb_license"=>"IGB License",
    "site_code"=>"BMC Site Code", 
    "number"=>"Player #", 
    "ip"=>"Player IP"
); 

$sql="select 
    regions.region,
    customers.dba,
    customers.legal_name,
    customers.igb_license,
    customers.site_code,
    media_players.number,
    media_players.ip 
    FROM 
        media_players 
    LEFT JOIN (
        customers JOIN regions on customers.region_id=regions.id
    ) 
    ON media_players.customer_id=customers.id
    where media_players.online=0"; 
$res=$db->run($sql);
$offline_mp=$res->fetchAll();
array_unshift($offline_mp,$headers); 

$cells = array(); 

$date = date("F jS Y"); 
$fh = "Offline Media Players.xlsx";
$spreadsheet=new Spreadsheet(); 
$spreadsheet->getActiveSheet()->fromArray($offline_mp,NULL,"A1"); 
$writer = new Xlsx($spreadsheet); 
$writer->save($fh); 
?>



