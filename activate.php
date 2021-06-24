<?php
    $verifyCode = $_GET["verifyCode"];

    // Include the connection manager
    require_once("models/classes/connectionmanager.php");

    // Establish database connection
    $databaseConnection = ConnectionManager::getInstance()->getConnection();
    $databaseConnection->query("UPDATE users SET verified = 1 WHERE verifyCode = ?", $verifyCode);
    header("Location: login.php");

