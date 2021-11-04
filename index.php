<?php 
session_start(); 
require "./classes/account.php"; 
$account = new Account(); 
$method = $_SERVER['REQUEST_METHOD']; 
if($account->sessionLogin()) {
    header("Location: grid.php"); 
}
if($method == 'POST') {
    try { 
        $login = $account->login($_POST['login'],$_POST['password']); 
    } catch (Exception $e) { 
        echo $e->getMessage(); 
        die(); 
    }
    if($login) { 
        header("Location: grid.php"); 
    }
}
?>
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1"> 
</head> 
<link rel="stylesheet" href="css/login.css"/>
<link rel="stylesheet" href="js/bootstrap-5.1.1-dist/css/bootstrap.min.css" id="bootstrap-css" />
<script src="js/bootstrap-5.1.1-dist/js/bootstrap.min.js"></script> 
<script src="js/jquery.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
<body>
<div class="wrapper fadeInDown">
  <div id="formContent">
    <form action='' method='post'>
      <input type="text" id="login" class="fadeIn second" name="login" placeholder="login">
      <input type="text" id="password" class="fadeIn third key" name="password" placeholder="password" autocomplete="off">
      <input type="submit" class="fadeIn fourth" value="Log In">
    </form>
  </div>
</div>
<body> 