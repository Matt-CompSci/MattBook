<?php
    // Start the session
    session_start();


    // If check the right information has been sent
    if(isset($_POST["userID"]) && isset($_SESSION["userID"]) && isset($_POST["token"])) {
        $friendID = $_POST["userID"];
        $currentID = $_SESSION["userID"];

        // If the token provided is valid
        if($_POST["token"] == $_SESSION["token"]) {
            // Insert add the friendship to the database
            require_once("../models/classes/connectionmanager.php");
            $databaseConnection = ConnectionManager::getInstance(true)->getConnection();
            $databaseConnection->query("INSERT INTO friends(userID, friendID) VALUES(?, ?)", $currentID, $friendID);
            $databaseConnection->query("INSERT INTO friends(userID, friendID) VALUES(?, ?)", $friendID, $currentID);
        } else {
            // Give an error telling the user that the token is invalid
            $error = [];
            $error["error"] = "Invalid page token";
            echo json_encode($error);
        }
    } else {
        // Give an error telling the user the wrong information has been sent
        $error = [];
        $error["error"] = "Invalid get parameters";
        echo json_encode($error);
    }
?>