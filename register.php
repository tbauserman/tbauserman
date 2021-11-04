<?php
require './classes/account.php'; 
$method = $_SERVER['REQUEST_METHOD'];
$error = False; 
$message = False; 
if($method == 'POST') {
    $account = new Account(); 
    try {
        
        $account_id=$account->AddAccount($_POST['first_name'],$_POST['last_name'],$_POST['email'],$_POST['password']); 
        
        if($account_id) {
            $message="Account added succesfully.<br>Your account will be activated shortly.";
        }
        //header("Location: index.php"); 
    } catch(Exception $e) {
        $error=True; 
        $error_message = $e->getMessage(); 
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
    <div class='container h-100'>

        <div class='row d-flex justify-content-center align-items-center h-100'>
            <div class='col-12 col-md-9 col-lg-7 col-xl-6'>
                <div class='card' style='border-radius: 15px;'> 
                    <div class='card-body p-5'> 
                        <h2 class='text-uppercase text-center mb-5'>Create Account</h2>
                        <?php 
                        if($message) { 
                            echo "<div class='row'>"; 
                            echo "<div class='alert-primary'>";
                            echo $message; 
                            echo "</div>"; 
                            echo "</div>"; 
                        }
                        if($error){
                            echo "<div class='row'>"; 
                            echo "<div class='alert-danger'>";
                            echo $error_message; 
                            echo "</div>"; 
                            echo "</div>"; 
                        }
                        ?>
                        <form action='' method='post'> 
                        
                        <div class="row mb-4">
                            <div class="col">
                                <div>
                                    <input type="text" id="first_name" name='first_name' class="form-control-plaintext" />
                                    <label class="form-label" for="first_name">First Name</label>
                                </div>
                            </div>
                            <div class="col">
                                <div>
                                    <input type="text" id="last_name" name='last_name' class="form-control" />
                                    <label class="form-label" for="last_name">Last name</label>
                                </div>
                            </div>
                        </div>

                        <!-- Email input -->
                        <div class='form-outline mb-4'>
                            <input type="text" id="email" name='email' class="form-control" />
                            <label class="form-label" for="email">Email address</label>
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-4">
                            <input type="text" id="password" name='password' class="form-control" style='-webkit-text-security: disc' autocomplete="off"/>
                            <label class="form-label" for="password">Password</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block mb-4">Sign up</button>
                        </form>
                    </div>  
                </div>
            </div>
        </div>
    </div>
    <!--</div>-->
</body>
</html>