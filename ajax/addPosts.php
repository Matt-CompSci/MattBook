<?php
    session_start();


    // If the user is logged in
    if(isset($_SESSION["userID"])) {
        $userID = $_SESSION["userID"];
        $postContent = $_POST["content"];

        require_once("../models/classes/connectionmanager.php");
        $databaseConnection = ConnectionManager::getInstance(true)->getConnection();

        $databaseConnection->query("INSERT INTO posts(userID, content) VALUES(?, ?)", $userID, $postContent);
    } else {
        echo "Forbidden";
    }