<?php
    session_start();

    if(isset($_GET["userID"]) && isset($_SESSION["userID"]) && isset($_GET["token"])) {
        if($_GET["token"] == $_SESSION["token"]) {
            require_once("../models/classes/connectionmanager.php");
            $databaseConnection = ConnectionManager::getInstance(true)->getConnection();

            $senderUserID = $_SESSION["userID"];
            $recepientUserID = $_GET["userID"];
            $token = $_GET["token"];

            $databaseConnection->query("SELECT messages.messageID, users.firstName AS sender, messages.userID, messages.recepientID, messages.message  FROM messages INNER JOIN users ON (messages.userID = users.userid) WHERE ((messages.userID = ? AND messages.recepientID = ?) OR (messages.userID = ? AND messages.recepientID = ?)) AND messages.requestToken != ? ORDER BY timeSent ASC LIMIT 50", $senderUserID, $recepientUserID, $senderUserID, $recepientUserID, $token);
            $messages = $databaseConnection->fetchAll();

            foreach($messages as $key => $message) {
                $messageID = $message["messageID"];
                $databaseConnection->query("UPDATE messages SET requestToken = ? WHERE messageID = ?", $token, $messageID);
            }
            echo json_encode($messages);
        } else {
            $error = [];
            $error["error"] = "Invalid/Expired request token";
            echo json_encode($error);
        }
    } else {
        $error = [];
        $error["error"] = "Invalid get parameters";
        echo json_encode($error);
    }