<?php 
    session_start(); 
    require "./classes/account.php";
    $account = new Account(); 
    if(!$account->sessionLogin()) {
        header("Location: index.php"); 
    }
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1"> 
    <style>
        .hide {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="js/jquery-ui/jquery-ui.min.css" />
    <link rel="stylesheet" href="css/media_players.css" type="text/css" />
    <link rel="stylesheet" href="css/jsgrid.css" type="text/css" />
    <link rel="stylesheet" href="css/jsgrid-theme.css" type="text/css" /> 
    <link rel="stylesheet" href="js/bootstrap-5.1.1-dist/css/bootstrap.min.css" id="bootstrap-css" />
    <script src="js/bootstrap-5.1.1-dist/js/bootstrap.min.js"></script> 
    <script src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jsgrid/jsgrid.js"></script>
    <script src="js/jquery-ui/jquery-ui.min.js"></script>
	<script src="https://kit.fontawesome.com/d8eba02ba0.js" crossorigin="anonymous"></script>
    <script src="js/general.js"></script>
    <script src="js/grid.js"></script>
     
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
                <ul class="nav me-auto" style='float:right;'>
                    <li class="nav-item"> 
                        <a href="Offline Media Players.xlsx" target="_blank" id='offline_mp' nav class='nav-link link-dark px-2'><i style='padding:5px;' class='fas fa-ban'></i>Offline</a>
                    </li> 
                    <li class="nav-item">
                        <a href='logout.php' nav class="nav-link link-dark px-2 active" aria-current="page"><i style='padding:5px;' class='fas fa-user'></i>Logout</a>
                    </li>
                </ul>
            </nav>
        </div>
        
    </div>

    <div class='container-fluid'>
        <div class='row row-cols-5 justify-content-md-center'>
            <div class='col'>
               <a href='#'>Stuff</a>
            </div> 
            <div class='col'>
                <a href='#' id='users' class='nav-link link-dark px-4'><i style='padding:15px;' class='fas fa-user'></i>Users</a>
            </div>
        </div>
    </div> 

    <div class="modal fade" id="media_player_modal" tabindex="-1" role="dialog" aria-labelledby="media_player_modal_label" aria-hidden="true"> 
        <div class="modal-dialog" role="document"> 
            <div class="modal-content">
                <div class="modal-header">
                    <!--
                    <h5 class="modal-title">Media Players</h5> 
                    <nav>
                    <form class="form-inline my-2 my-lg-0">
                        <div style='float:left;'>
                        <input style='float:left;' class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" size='10'>
                        <i class='fa-solid fa-magnifying-glass'></i> 
                        </div>
                    </form>
                    </nav>
                    -->
                </div>
                <div>Stuff here</div> 
                <!--
                <div class="modal-body">
                </div>
                -->
            </div>
        </div>
    </div>

    <div class='overlay'></div>

<!--
<div class='container'>
    <nav class="py-2 bg-light border-bottom">
        <ul class="nav me-auto">
            <li class="nav-item">
                <a href='#' nav class="nav-link link-dark px-2 active" aria-current="page">Media Players</a>
            </li>
        </ul>
    </nav> 
</div> 
<div class='tabs'>
    <ul class='tabs-list'>
        <li><a href='#mp_grid_tab'>Media Players</a></li>
    </ul>


<div id='mp_grid_tab' class='tab-active'>
    <div id='mp_grid_search'><input type='text' name='mp_search' id='mp_search'></div>
    <div id='mp_grid'></div>
</div>
<div class='overlay'></div> 
-->
</body> 
</html> 