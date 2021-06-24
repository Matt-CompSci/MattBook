<?php
    session_start();
    if(isset($_SESSION["userID"]) && isset($_GET["userID"]) && isset($_GET["token"])) {
        if($_SESSION["token"] == $_GET["token"]) {
            require_once("../models/classes/connectionmanager.php");
            $databaseConnection = ConnectionManager::getInstance(true)->getConnection();

            $userID = $_GET["userID"];
            $databaseConnection->query("SELECT isTyping FROM users WHERE userID = ?", $userID);

            echo json_encode($databaseConnection->fetchArray());
        } else {
            $error = [];
            $error["error"] = "Invalid request token";
            echo json_encode($error["error"]);
        }
    } else {
        $error = [];
        $error["error"] = "Invalid get parameters";
        echo json_encode($error["error"]);
    }

