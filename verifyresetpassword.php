<?php

    $view = new stdClass();

    $view->Error = "";

    if(isset($_GET["resetToken"])) {
        require_once("models/classes/connectionmanager.php");
        $databaseConnection = ConnectionManager::getInstance()->getConnection();

        $token = $_GET["resetToken"];
        $databaseConnection->query("SELECT * FROM users WHERE verifyCode = ?", $token);
        $userData = $databaseConnection->fetchArray();

        if($token === "" || count($userData) === 0) {
            header("Location: forgotpassword.php?error=" . urlencode("Password token expired"));
        } else {
            require_once("views/verifyresetpassword.phtml");
        }
    }

    if(isset($_POST["submit"])) {
        $password = $_POST["inputPassword"];
        $confirmPassword = $_POST["inputPasswordConfirm"];

        if($password === $confirmPassword) {
            if(strlen($password) < 8) {
                $view->Error = "Your password must be atleast 8 characters";
            } else {
                require_once("models/classes/connectionmanager.php");
                $databaseConnection = ConnectionManager::getInstance()->getConnection();
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $token = $_GET["resetToken"];

                $databaseConnection->query("UPDATE users SET password = ? WHERE verifyCode = ?", $hashedPassword, $token);
                $databaseConnection->query("UPDATE users SET verifyCode = '' WHERE verifyCode = ?", $token);
                header("Location: login.php");
            }
        } else {
            $view->Error = "The passwords must match";
        }
    }
