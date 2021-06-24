<?php
session_start();


// If the user is logged in
if(isset($_POST["postID"]) && isset($_SESSION["userID"]) && isset($_POST["content"]) && isset($_POST["token"])) {
    if($_POST["token"] == $_SESSION["token"]) {
        $userID = $_SESSION["userID"];
        $postID = $_POST["postID"];
        $postContent = $_POST["content"];

        require_once("../models/classes/connectionmanager.php");
        $databaseConnection = ConnectionManager::getInstance(true)->getConnection();
        $databaseConnection->query("INSERT INTO comments(postID, userID, content) VALUES(?, ?, ?)", $postID, $userID, $postContent);
    } else {
        $error = [];
        $error["error"] = "Invalid/Expired request token";
        echo json_encode($error);
    }
} else {
    $error = [];
    $error["error"] = "Invalid post parameters";
    $error["params"] = $_POST;
    echo json_encode($error);
}