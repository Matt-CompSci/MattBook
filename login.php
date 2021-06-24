<?php
    session_start();

    $view = new stdClass();
    $view->Title = "Login";

    // Used to store any errors caught
    $view->Error = isset($_GET["error"]) ? $_GET["error"] : "";

    // Include the connection manager
    require_once("models/classes/connectionmanager.php");

    // If the submit button has been pressed
    if(isset($_POST["submit"])) {
        // Info from form
        $email = $_POST["inputEmail"];
        $password = $_POST["inputPassword"];

        // Establish database connection
        $databaseConnection = ConnectionManager::getInstance()->getConnection();

        // Get users information from the database
        $databaseConnection->query("SELECT * FROM users WHERE email = ? LIMIT 1", $email);
        $userInfo = $databaseConnection->fetchArray();

        if($userInfo["verified"] === 1) {
            // If no result was returned
            if (count($userInfo) === 0) {
                $view->Error = "No account is associated with the given email";
            } else {
                // If the password is correct
                if (password_verify($password, $userInfo["password"])) {
                    $_SESSION["loggedIn"] = true;
                    $_SESSION["userID"] = $userInfo["userid"];
                    $_SESSION["name"] = $userInfo["firstName"] . " " . $userInfo["secondName"];
                } else {
                    $view->Error = "Incorrect Email or Password";
                }
            }
        } else {
            $view->Error = "Please verify you email before you log in";
        }
    }

    if(isset($_SESSION["loggedIn"])) {
        header("Location: index.php");
    }


    require_once("views/login.phtml");