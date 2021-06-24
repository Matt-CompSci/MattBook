// Import the user class
import User from "./classes/user.js";

// Function called to update the friends list on the page
async function updateFriendList() {
    // Get the get parameters from the URL
    let getParams = new URLSearchParams(window.location.search);

    // If the is a userID provided then use that otherwise use the clients userid given from php in profile.php
    profileUserID = getParams.has("userID") ? getParams.get("userID") : profileUserID

    // Generate a user object from this id
    let user = new User({"userID" : profileUserID});

    // Store the token provided by profile.php
    let token = requestToken;

    // Inform the user the data is loading
    document.getElementById("friendsList").innerHTML = "Loading...";

    try {
        // Get the users friends from the server asyncronously
        let friends = await (user.getFriends(token));

        // Clear the friends list div
        let userContent = "";

        // Display information on each friend in HTML
        friends.forEach((friend) => {
            userContent += "<div class='row mb-2' style='border: 2px solid cornflowerblue;'>";
            userContent += "<div class='column ml-2 mr-2'><img src='" + friend.avatar + "' style='border-radius: 30px; border:1px solid cornflowerblue' width='40em' height='40em'></div>";
            userContent += "<div class='column ml-2 mt-2'><a href='profile.php?userID=" + friend.userID + "'>" + friend.fullName + "</a></div>";
            userContent += "</div>";
        });
        document.getElementById("friendsList").innerHTML = userContent;
    } catch {
        document.getElementById("friendsList").innerHTML = "Session/Request Token timed out - refresh the page";
    }
}
window.updateFriendList = updateFriendList;
updateFriendList();