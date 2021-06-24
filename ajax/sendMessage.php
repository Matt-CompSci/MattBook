<?php
    session_start();

    if(isset($_POST["userID"]) && isset($_SESSION["userID"]) && isset($_POST["message"]) && isset($_POST["token"])) {
        if($_POST["token"] == $_SESSION["token"]) {
            $recepientID = $_POST["userID"];
            $senderUserID = $_SESSION["userID"];
            $message = $_POST["message"];
            $token = $_POST["token"];

            require_once("../models/classes/connectionmanager.php");
            $databaseConnection = ConnectionManager::getInstance(true)->getConnection();
            $databaseConnection->query("INSERT INTO messages(userID, recepientID, message) VALUES(?, ?, ?)", $senderUserID, $recepientID, $message);
        } else {
            $error = [];
            $error["error"] = "Invalid/Expired request token";
        }
    } else {
        $error = [];
        $error["error"] = "Invalid get parameters";
        json_encode($error);
    }