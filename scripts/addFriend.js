// Import the user class
import User from "./classes/user.js";

// Function called when the add friend button is pressed
function addFriend() {
    // Get the profile we're visiting from the GET parameters
    let urlParams = new URLSearchParams(window.location.search);
    let visitingUserID = urlParams.get("userID");

    // Make a new user object
    let currentUser = new User({userID : profileUserID});

    // Get the add friend button
    let btn = document.getElementById("addFriendButton");

    // If the profile we're visiting isn't a friend then add them
    if(btn.value == "Add Friend") {
        currentUser.addFriend(requestToken, visitingUserID);

        // Update the text to "Remove Friend";
        btn.value = "Remove Friend";
        btn.innerHTML = "Remove Friend";

        // Update the friend list
        updateFriendList();
    } else {
        // If they're already friends then remove them as a friend
        currentUser.removeFriend(requestToken, visitingUserID);

        // Update the text to "Add Friend"
        btn.value = "Add Friend";
        btn.innerHTML = "Add Friend";

        // Update the friend list
        updateFriendList();
    }
}

window.addFriend = addFriend;