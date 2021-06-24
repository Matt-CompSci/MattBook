<?php
session_start();

$view = new stdClass();
$view->Title = "Register";

// Used to store any errors caught
$view->Error = "";

// Include the connection manager
require_once("models/classes/connectionmanager.php");

// If the submit button has been pressed
if(isset($_POST["submit"])) {

    // GoogleAPI link to send to
    $verificationURL = "https://www.google.com/recaptcha/api/siteverify";

    // Build post request
    $data = Array(
        "secret" => "6LcRvNYUAAAAABrvKA7i9A2_3WPRRA50gFMVeICz",
        "response" => $_POST["g-recaptcha-response"]
    );

    $options = array(
        "http" => array(
            "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
            "method"  => "POST",
            "content" => http_build_query($data)
        )
    );

    // Send post request
    $context  = stream_context_create($options);
    $result = json_decode(file_get_contents($verificationURL, false, $context));

    // If google has confirmed the request
    if(isset($result->success) && $result->success) {
        echo "Captcha worked";
        // Info from form
        $email = $_POST["inputEmail"];
        $password = $_POST["inputPassword"];
        $firstName = $_POST["inputFirstName"];
        $secondName = $_POST["inputSecondName"];
        $birthday = $_POST["inputBirthday"];

        // Establish database connection
        $databaseConnection = ConnectionManager::getInstance()->getConnection();

        // Get users information from the database
        $databaseConnection->query("SELECT * FROM users WHERE email = ? LIMIT 1", $email);
        $userInfo = $databaseConnection->fetchArray();

        $verified = 0;
        $verifyCode = uniqid();

        if(count($userInfo) === 0) {
            $databaseConnection->query("INSERT INTO users(email, password, firstName, secondName, birthday, verified, verifyCode) VALUES(?, ?, ?, ?, ?, ?, ?)",
                strip_tags($email),
                password_hash($password, PASSWORD_DEFAULT),
                strip_tags($firstName),
                strip_tags($secondName),
                strip_tags($birthday),
                $verified,
                $verifyCode
            );

            $subject = "MattBook - Email verification";
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            $activateURL = $_SERVER["HTTP_HOST"] . "/activate.php?verifyCode=" . $verifyCode;
            $message = "<h3>Welcome to MattBook!</h3>";
            $message .= 'Please follow this <a href="' . $activateURL . '">link</a> to activate your account';
            mail($email, $subject, $message, $headers);

            header("Location: login.php");
        } else {
            $view->Error = "An account with this email already exists";
        }


    } else {
        $view->Error = "You failed the Captcha, please try again";
    }
}

if(isset($_SESSION["loggedIn"])) {
    header("Location: index.php");
}


require_once("views/register.phtml");