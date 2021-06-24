<?php
    $view = new stdClass();

    $view->Title = "Forgotten Password?";
    $view->Error = "";
    $view->Success = "";

    require_once("models/classes/connectionmanager.php");

    if(isset($_POST["submit"])) {
        $email = $_POST["inputEmail"];

        // Establish database connection
        $databaseConnection = ConnectionManager::getInstance()->getConnection();

        $databaseConnection->query("SELECT * FROM users WHERE email = ?", $email);
        $userInfo = $databaseConnection->fetchArray();



        if(count($userInfo) === 0) {
            $view->Error = "There isn't an account associated with this email";
        } elseif($userInfo["verified"] === 1) {
            $resetToken = uniqid();
            $databaseConnection->query("UPDATE users SET verifyCode = ? WHERE email = ?", $resetToken, $email);

            $subject = "MattBook - Reset Password";
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            $activateURL = $_SERVER["HTTP_HOST"] . "/verifyresetpassword.php?resetToken=" . $resetToken;
            $message = "<h3>Reset your MattBook password!</h3>";
            $message .= 'Please follow this <a href="' . $activateURL . '">link</a> to reset your password';
            mail($email, $subject, $message, $headers);

            $view->Success = "A password reset email has been sent to your email at " . $email;
        } else {
            $view->Error = "The account must be verified in order to change the password";
        }
    }

    if(isset($_GET["error"])) {
        $view->Error = $_GET["error"];
    }

    require_once("views/forgotpassword.phtml");
