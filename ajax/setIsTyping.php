<?php
session_start();
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
if(isset($_SESSION["userID"]) && isset($_POST["token"]) && isset($_POST["isTyping"])) {
    if($_SESSION["token"] == $_POST["token"]) {
        require_once("../models/classes/connectionmanager.php");
        $databaseConnection = ConnectionManager::getInstance(true)->getConnection();

        $userID = $_POST["userID"];
        $isTyping = $_POST["isTyping"];
        echo $isTyping . " " . $userID;
        $databaseConnection->query("UPDATE users SET isTyping = ? WHERE userID = ?", $isTyping, $userID);
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

