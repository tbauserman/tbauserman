<?php
$csv = array_map('str_getcsv',file('ips.csv')); 
$f = fopen("offline.txt","w"); 
foreach($csv as $ip) {    
    $result = null;
    $ip=substr($ip[0],0,strrpos($ip[0],'.')).'.1';  
    $output = exec("ping -n 1 ".$ip, $result);
    $online=True;  
    if(strpos($output,"Lost = 1")) {
        $online = False;
    } else {
        $online = True;
    }
    if(!$online) { 
        fwrite($f,$ip."\r\n");
    }
}
fclose($f); 
?>