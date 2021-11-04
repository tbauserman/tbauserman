<?php 
error_reporting(E_ALL ^ E_WARNING); 
ini_set('display_errors',1); 
ini_set('upload_max_filesize','20M'); 
ini_set('post_max_size','51M'); 
require_once '../classes/pdo.php'; 

$method = $_SERVER['REQUEST_METHOD'];
$fileUpload=False; 

foreach($_FILES as $key=>$fileArray) {
    if(str_starts_with($key,'uploadVideo')) { 
        $fileName=$key; 
        $fileUpload=True; 
    }
}

foreach($_POST as $key=>$value) {
    if(str_starts_with($key,'mp_id')) {
        $post = True;
        $mp_id = $value; 
    }

}
if($method == 'POST' && $fileUpload && $post) {
    $db = new DB(); 
    $target_dir = $_SERVER['DOCUMENT_ROOT']."/tbauserman/uploads/"; 
    if(!is_writeable($target_dir)) { echo "Cant write to directory."; }
    $target_file = $target_dir.basename($_FILES[$fileName]['name']); 
    $ext = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $uploadOk=False; 
    if($ext !='mpeg') { 
        $error = "Video format be MPEG"; 
    } else {
        if(file_exists($target_dir."boats.mpeg")) {
            unlink($target_dir."boats.mpeg"); 
        } 
        $res = move_uploaded_file($_FILES[$fileName]['tmp_name'],$target_dir."boats.mpeg");  
        if($res) {
            $sql_mp = "select * from media_players where id=:mp_id"; 
            $args = array(':mp_id'=>$mp_id);
            $stmt_mp = $db->run($sql_mp,$args); 
            $mp = $stmt_mp->fetch(); 

            if($mp['online']) { 
                $command = 'echo y | '.escapeshellarg("c:\Program Files\PuTTy\pscp.exe").' -i c:\xampp\htdocs\tbauserman\id_rsa.ppk c:\xampp\htdocs\tbauserman\uploads\boats.mpeg pi@'.$mp['ip'].':/home/pi/JJVideos/ && echo success || echo error'; 
                echo $command; 
                $result = null; 
                exec($command,$result); 
                $sql = "update media_players set video=:video where id=:id"; 
                $args = array(':video'=>basename($target_file),':id'=>$mp_id);
                $db->run($sql,$args);  
            } else { 
                $error = "Media player is offline.";
            }

        } else { 
            $error = "Unable to upload file."; 
        }
    }
    echo $error; 
}
?>