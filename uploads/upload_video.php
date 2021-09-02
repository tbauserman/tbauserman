<?php
error_reporting(E_ALL); 
echo "huh?";
$command = escapeshellarg("c:\Program Files\PuTTY\pscp.exe").' -i c:\xampp\htdocs\rpi\id_rsa.ppk c:\xampp\htdocs\rpi\uploads\boats.wmv pi@172.99.10.253:/home/pi/JJVideos/ && echo success || echo error';  
$result = null; 
exec($command,$result);
print_r($result);
?>