<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
    session_start();
    if(isset($_SESSION["userID"]) && isset($_GET["postID"]) && isset($_GET["token"])) {
        if($_SESSION["token"] == $_GET["token"]) {
            require_once("../models/classes/connectionmanager.php");
            $databaseConnection = ConnectionManager::getInstance(true)->getConnection();

            $postID = $_GET["postID"];
            $databaseConnection->query("SELECT users.userID, CONCAT(users.firstName, ' ', users.secondName) AS fullName, users.avatar, comments.postID,  comments.content FROM comments INNER JOIN users ON (users.userID = comments.userID) WHERE comments.postID = ?", $postID);
            echo json_encode($databaseConnection->fetchAll());
        } else {
            $error = [];
            $error["error"] = "Invalid request token";
            echo json_encode($error["error"]);
        }
    } else {
        $error = [];
        $error["error"] = "Invalid get parameters";
        $error["params"] = $_GET;
        echo json_encode($error["error"]);
    }

