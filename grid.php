<?php 
    session_start(); 
    require "./classes/account.php";
    $account = new Account(); 
    if(!$account->sessionLogin()) {
        header("Location: index.php"); 
    }
    $account_info = $account->getAccounts($account->id);
    $account_types = explode(",",$account_info[0]['account_type']); 
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1"> 
   
    <link rel="stylesheet" href="js/bootstrap-5.1.1-dist/css/bootstrap.min.css" id="bootstrap-css" />
    <link rel="stylesheet" href="js/jquery-ui/jquery-ui.min.css" />
    <link rel="stylesheet" href="css/media_players.css" type="text/css" />
    <link rel="stylesheet" href="css/jsgrid.css" type="text/css" />
    <link rel="stylesheet" href="css/jsgrid-theme.css" type="text/css" /> 
    <style>
        body {
            font-size: .7em; 
        }
        .hide {
            display: none;
        }
        .ui-front { 
            z-index: auto; 
        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .jsgrid-cell { 
            white-space: nowrap;
            overflow: hidden; 
            text-overflow: ellipsis; 
        }
    </style>
    <script src="js/bootstrap-5.1.1-dist/js/bootstrap.min.js"></script> 
    <script src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jsgrid/jsgrid.js"></script>
    <script src="js/jquery-ui/jquery-ui.min.js"></script>
	<script src="https://kit.fontawesome.com/d8eba02ba0.js" crossorigin="anonymous"></script>
    <script src="js/general.js"></script>
    <script src="js/grid.js"></script>
    <script src='js/users.js'></script>
    <script src='js/tickets.js'></script>
     
    <title>Upload new video</title>
</head>
<body> 
    <div class='container-fluid'> 
        <div class='row row-cols-4 py-2 bg-light border-bottom'> 
            <div class='col'></div>
            <div class='col'></div>
            <div class='col'></div>
            <div class='col'>
                <nav> 
                    <ul class='nav me-auto' style='float:right;'> 
                        <li class='nav-item'> 
                            <a  href='Offline Media Players.xlsx' target='_blank' class='nav-link link-dark px-2'>
                                <i style='padding:5px;' class='fas fa-ban'></i>
                                Offline
                            </a>
                        </li>
                        <li class='nav-item'> 
                            <a href='logout.php' class='nav-link link-dark px-2 active' aria-current='page'>
                                <i style='padding:5px;' class='fas fa-user'></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div class='container-fluid'> 
        <div class='row row-cols-4 justify-content-md-center'> 
            <div class='col'></div>
            <?php 
                if(in_array('Marketing',$account_types) || in_array('Admin',$account_types)) { 
            ?>
            <div class='col'> 
                <a  href='#' class='nav-link link-dark px-4' style='font-size:1.2em;' id='open_mp'>
                    <i style='padding:15px;' class='fas fa-play fa-lg'></i>
                    Media Players
                </a>
            </div>
            <?php 
            } 
            if(in_array('Admin',$account_types)) { 
            ?>
            <div class='col'>
                <a href='#' class='nav-link link-dark px-4' style='font-size:1.2em;' id='open_users'> 
                    <i style='padding:15px;' class='fas fa-user fa-lg'></i>
                    Users
                </a>
            </div>
            <?php 
            }
            if(in_array('Admin',$account_types) || in_array('Bally',$account_types)) {
            ?>
            <div class='col'>
                <a href='#' class='nav-link link-dark px-4' style='font-size:1.2em;' id='open_tickets'> 
                    <i style='padding:15px;' class='fas fa-align-justify fa-lg'></i> 
                    Tickets
                </a>
            </div>
            <?php 
            }
            ?>
        </div>

    </div>
    <div id='mp_grid' title='Media Players'></div>
    <div id='user_grid' title='Users'></div>
    <div id='ticket_grid' title='Tickets'></div> 
</body>
</html>



