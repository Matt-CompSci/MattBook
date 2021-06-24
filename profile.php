<?php
    // View is an object full of information to be used by the view
    $view = new stdClass();

    // Set the title in the view to Profile
    $view->Title = "Profile";

    // Require the user class
    require_once("models/classes/user.php");

    // Start the session
    session_start();

    // Get the userid of the current profile from either the session or get parameters
    $userID = isset($_GET["userID"]) ? $_GET["userID"] : $_SESSION["userID"];

    // Is the user logged in? Otherwise send them back to the login page
    if(isset($_SESSION["loggedIn"])) {
        // Generate a request token
        $token = uniqid();
        $_SESSION["token"] = $token;

        // Send the request token and their userid to the client
        echo "<script>" .
            "let requestToken = '" . $token . "';" .
            "let profileUserID = " . $_SESSION["userID"] . ";" .
            "let userUserID = " . $_SESSION["userID"] . ";" .
            "</script>";

        // Create a new user object from the users profile we're viewing
        $user = new User($userID);

        // Create a new user object from the user that's currently logged in
        $currentUser = new User($_SESSION["userID"]);

        // Move the information from the user object to the view object
        $view->isOwner = $_SESSION["userID"] == $userID;
        $view->isFriend = $user->isFriendsWith($currentUser);
        $view->Name = $user->getFirstName() . " " . $user->getSecondName();
        $view->Birthday = $user->getBirthday();
        $view->TimeCreated = $user->getTimeCreated();
    } else {
        // Send the user back to the login page
        header("Location: login.php");
    }

    // If the page has been refreshed because a user has updated their profile picture
    if(isset($_POST["profilePictureSubmit"])) {
        // If this user is the owner of the page
        if($view->isOwner) {
            // Include the database and upload classes
            require_once("models/classes/upload.php");
            require_once("models/classes/connectionmanager.php");

            // Grab the static instance of the database
            $databaseConnection = ConnectionManager::getInstance()->getConnection();

            // Find the current avatar of the user
            $databaseConnection->query("SELECT avatar FROM users WHERE userid = ?", $userID);
            $userInfo = $databaseConnection->fetchArray();

            // Delete the file of the current avatar
            unlink(__DIR__ . "/files/" . $userInfo["avatar"]);

            // Upload the new avatar to the server
            $fileName = upload::uploadFile($_FILES['fileToUpload'],['jpg','gif','jpg','jfif','png']);

            // If the upload was successful then update the users avatar in the database
            if ($fileName) {
                $databaseConnection->query("UPDATE users SET avatar = ? WHERE userid = ?", $fileName, $userID);
            }
        }
    }

    // Update the avatar on the page
    $user = new user($userID);
    $view->avatarPath = $user->getAvatarPath();

    require_once("views/profile.phtml");
?>