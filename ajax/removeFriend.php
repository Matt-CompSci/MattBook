<?php
    // Start the session
    session_start();

    // Check the correct information has been sent
    if(isset($_POST["userID"]) && isset($_SESSION["userID"]) && isset($_POST["token"])) {
        $friendID = $_POST["userID"];
        $currentID = $_SESSION["userID"];

        // Check the token is equal to the current page token
        if($_POST["token"] == $_SESSION["token"]) {
            // Remove the friendship from the database
            require_once("../models/classes/connectionmanager.php");
            $databaseConnection = ConnectionManager::getInstance(true)->getConnection();
            $databaseConnection->query("DELETE from friends WHERE userID = ? AND friendID = ?", $currentID, $friendID);
            $databaseConnection->query("DELETE from friends WHERE userID = ? AND friendID = ?", $friendID, $currentID);
        } else {

        }
    } else {
        // Inform the user that they've given invalid get parameters
        $error = [];
        $error["error"] = "Invalid get parameters";
        echo json_encode($error);
    }

?>