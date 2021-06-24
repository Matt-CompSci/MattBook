
<?php
    $view = new stdClass();
    $view->Title = "Home";
    session_start();
    require_once("views/index.phtml");

    if(isset($_SESSION["loggedIn"])) {
        header("Location: profile.php");
    } else {
        header("Location: login.php");
    }


