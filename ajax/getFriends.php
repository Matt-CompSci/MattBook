<?php
    // Start the session
    session_start();

    // Import the database wrapper
    require_once("../models/classes/connectionmanager.php");

    // As we're running including from the Ajax folder pass true so the path is from the ajax folder
    $databaseConnection = ConnectionManager::getInstance(true)->getConnection();

    // If a limit is in the get parameters use that limit otherwise use 10
    $limit = isset($_GET["limit"]) ? $_GET["limit"] : 10;

    // If an offset is in the get parameters use that offset otherwise use 0
    $offset = isset($_GET["offset"]) ? $_GET["offset"] : 0;

    // Check the user is logged in and a request token is given
    if(isset($_GET["token"]) && isset($_SESSION["userID"])) {
        // Get the userID from either the get parameters or the session
        $userID = isset($_GET["userID"]) ? $_GET["userID"] : $_SESSION["userID"];

        // If the request token from get and the session match
        if($_GET["token"] == $_SESSION["token"]) {
            // Select friends based on the get parameters
            $databaseConnection->query("SELECT friends.friendID AS userID, users.firstName, users.secondName, CONCAT('files/', users.avatar) AS avatar FROM friends INNER JOIN users ON (friends.friendID = users.userID) WHERE friends.userID = ?", $userID);
            $friends = $databaseConnection->fetchAll();

            // Encode the result into json format and send it to the client
            echo json_encode($friends);
        } else {
            $error = [];
            $error["error"] = "Invalid/Expired request token";
            echo json_encode($error);
        }
    } else {
        $error = [];
        if(!isset($_GET["token"])) {
            $error["error"] = "No request token provided";
        } else {
            $error["error"] = "User not logged in";
        }
        echo json_encode($error);
    }