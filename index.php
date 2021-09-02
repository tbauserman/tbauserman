<?php 
session_start(); 
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/media_players.css" type="text/css">
    <script src="js/jquery.js"></script> 
    <script type="text/javascript">
        $(document).ready(function(){
            $(".tabs-list li a").click(function(e){
                e.preventDefault();
            });

            $(".tabs-list li").click(function(){
                var tabid = $(this).find("a").attr("href");
                $(".tabs-list li,.tabs div.tab").removeClass("active");   // removing active class from tab
                $(".tab").hide();   // hiding open tab
                $(tabid).show();    // show tab
                $(this).addClass("active"); //  adding active class to clicked tab
            });
        });
    </script>
    <title>Upload new video</title>
</head>
<body>
<?php
require_once 'classes/pdo.php'; 
if(isset($_POST['customer'])) {
    $_SESSION['customer_id']=$_POST['customer']; 
}
if(isset($_POST['pi'])) {
    $_SESSION['mp_id']=$_POST['pi']; 
}
$sql_mp_offline="select 
    concat_ws(' - ',`legal_name`,`dba`,`igb_license`,`site_code`) as customer,
    customers.id as customer_id, 
    media_players.ip 
    from 
    media_players JOIN 
    customers on media_players.customer_id=customers.id 
    where media_players.online=False
    order by customers.legal_name,media_players.customer_id"; 
$stmt_mp_offline=DB::Prepare($sql_mp_offline);
$stmt_mp_offline->execute(); 
$mps_offline=$stmt_mp_offline->fetchAll(); 
foreach($mps_offline as $mp_offline) { 
    $offline[$mp_offline['customer_id']][]=$mp_offline; 
}

if(isset($_SESSION['mp_id']) && isset($_SESSION['customer_id']) && isset($_FILES['video'])) {
    // upload video here // 
    $target_dir = "./uploads/"; 
    $target_file = $target_dir.basename($_FILES['video']['name']); 
    $ext = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
    $uploadOk=1; 
    if($ext != 'wmv') {
        $error = "Video format must be WMV"; 
    } else { 
        if(file_exists($target_dir."boats.wmv")) {
            unlink($target_dir."boats.wmv"); 
        }
       
        if(move_uploaded_file($_FILES['video']['tmp_name'],$target_dir."boats.wmv")) {

            $sql_mp="select * from media_players where id=:mp_id"; 
            $stmt_mp=DB::Prepare($sql_mp); 
            $stmt_mp->execute(['mp_id'=>$_SESSION['mp_id']]); 
            $mp = $stmt_mp->fetch(); 
            
            if($mp['online']){ 
                $command = escapeshellarg("c:\Program Files\PuTTy\pscp.exe").' -i c:\xampp\htdocs\rpi\id_rsa.ppk c:\xampp\htdocs\rpi\uploads\boats.wmv pi@'.$mp['ip'].':/home/pi/JJVideos/ && echo success || echo error';
                $result = null;
                exec($command,$result);
            } else {
                $error = "Media player is offline.";
            }
        } else {
            $error = "Unable to upload file."; 
        }
        
    }
}
if(isset($_POST['customer_s']) || isset($_POST['customer'])) {
    
    if(isset($_POST['customer_s'])) {
        $search_term=":search_term"; 
        $sql_term = "SET @term =:term";
        $stmt_term=DB::Prepare($sql_term);
        $stmt_term->bindValue(":term","%".$_POST['customer_s']."%",PDO::PARAM_STR);
        $stmt_term->execute(); 
        $sql_customer = "select concat_ws(' - ',`legal_name`,`dba`,`igb_license`,`site_code`) as customer,id as customer_id from customers where legal_name like @term or dba like @term or site_code like @term or igb_license like @term"; 
        $customers=DB::run($sql_customer)->fetchAll();
    } else if(isset($_POST['customer'])) {
        $stmt_customer = DB::Prepare("select concat_ws(' - ',`legal_name`,`dba`,`igb_license`,`site_code`) as customer,id as customer_id from customers where id=:customer_id");
        $stmt_customer->execute(['customer_id'=>$_POST['customer']]); 
        $customers = $stmt_customer->fetchAll(); 

        $stmt_mp = DB::Prepare("select media_players.id as mp_id,ip from media_players JOIN customers on media_players.customer_id=customers.id where customer_id=:customer_id"); 
        $stmt_mp->execute(['customer_id'=>$_POST['customer']]);
        $mps = $stmt_mp->fetchAll();
    }
}
echo "
<div class='tabs'>
    <ul class='tabs-list'>
        <li class='active'><a href='#upload_video'>Upload Video</a></li>
        <li><a href='#media_players'>Media Players</a></li>
    </ul>

<div id='upload_video' class='tab active'> 
<h3>Upload Video</h3>
";
if(isset($error)) {
    echo "<h1 style='color:red'>".$error."</h1>"; 
}
echo "
<form action='' method='post'>
    <p>
        <input type='text' name='customer_s' id='customer_s'> 
        <input type='submit' value='Search Customers'> 
    </p>
</form> 
<form action='' method='post'>
    <p>
        <select id='customer' name='customer'>
";
        if(isset($customers) && count($customers) > 0) {
            
            foreach($customers as $customer) {
                if(isset($_POST['customer']) && $_POST['customer']==$customer['id']) {
                    echo "<option value='".$customer['customer_id']."' selected>".$customer['customer']."</option>"; 
                } else {
                    echo "<option value='".$customer['customer_id']."'>".$customer['customer']."</option>";
                }
            }
        }
        
echo "
        </select>
        <label for='customer'>Select Customer</label>
        <input type='submit' value='Go'> 
    </p>
</form> 
<form action='' method='post' enctype='multipart/form-data'> 
    <p>
        <select id='pi' name='pi'>
"; 
        $counter =1; 
        if(isset($mps) && count($mps) > 0) {
            foreach($mps as $mp) {
                echo "<option value='".$mp['mp_id']."'>Media Player #".$counter."</option>"; 
                $counter++; 
            }
        }
echo "
        </select> 
        <label for='pi'>PI</label> 
    
        <input type='file' name='video' id='video'> 
    </p>
    <input type='submit' value='GO!'> 
</form> 
</div> 
<div id='media_players' class='tab'> 
<h3>Offline Media Players</h3> 
";
//print_r($mps_offline);
foreach($offline as $customer_id=>$mps_offline_arr) {
    $counter=1; 
    foreach($mps_offline_arr as $mp_offline_arr) {
    //echo $mp_offline_arr; 

    echo "<p>".$mp_offline_arr['customer']." Media Player #".$counter."</p>";
    $counter++; 
    }
}
echo "</div>"
?>
</body> 
</html> 