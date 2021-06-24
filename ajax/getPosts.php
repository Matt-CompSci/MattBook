<?php
    // Start session
    session_start();


    $limit = isset($_GET["limit"]) ? $_GET["limit"] : 5;
    $offset = isset($_GET["offset"]) ? $_GET["offset"] : 0;

    // Make sure get parameters are okay
    if((isset($_GET["userID"]) || isset($_SESSION["userID"])) && isset($_GET["token"])) {
        // Check the given token is valid
        if($_GET["token"] == $_SESSION["token"]) {
            // Get the userID from either get parameters or the session
            $userID = isset($_GET["userID"]) ? $_GET["userID"] : $_SESSION["userID"];

            // Get the post information from the database
            require_once("../models/classes/connectionmanager.php");
            $databaseConnection = ConnectionManager::getInstance(true)->getConnection();
            $databaseConnection->query("SELECT posts.postID, users.userid, CONCAT(users.firstName, ' ', users.secondName) AS username, users.avatar, posts.content FROM posts INNER JOIN users ON (posts.userID = users.userid) WHERE posts.userID = ? ORDER BY timePosted DESC LIMIT ? OFFSET ?", $userID, $limit, $offset);

            // Remove HTML from the post
            $data = $databaseConnection->fetchAll();
            foreach ($data as $key => $value) {
                array_map("htmlentities", $value);
            }

            // Send the post data to the user
            echo json_encode($data);
        } else {
            // Inform the user that their token is invalid
            $error = [];
            $error["error"] = "Invalid/Expired request token";
            echo json_encode($error);
        }
    } else {
        // Inform the user that they have invalid get parameters
        $error = [];
        $error["error"] = "Invalid get parameters";
        echo json_encode($error);
    }