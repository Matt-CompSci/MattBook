// Import user class
import User from "./classes/user.js";

// Choose toolbar options
let toolbarOptions = [
    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
    ['blockquote', 'code-block'],

    [{ 'header': 1 }, { 'header': 2 }],               // custom button values
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
    [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
    [{ 'direction': 'rtl' }],                         // text direction

    ['image'],  // custom dropdown
    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

    [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
    [{ 'font': [] }],
    [{ 'align': [] }],

    ['clean']                                         // remove formatting button
];

// Draw quill editor
let quill = new Quill('#editor-container', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow'
});

// Function to send a message to the chat using the current content of quill
function sendChatMessage() {
    // Create user object for the current page
    let user = new User({"userID" : profileUserID});

    // If their is a userid for the current profile page then send that using the post request
    let getParams = new URLSearchParams(window.location.search);

    // Send Message
    user.sendMessage(requestToken, user.userID, encodeURIComponent( quill.container.firstChild.innerHTML.replace("\\n", "")));

    // Clear the message box
    quill.setText('')

    // Update the contents of the chatbox
    updateChat();
}
window.sendChatMessage = sendChatMessage;

let first = true;
async function updateChat() {
    // Get the userid of the user we're messaging
    let getParams = new URLSearchParams(window.location.search);
    let recepientUserID = getParams.get("userID");
    let user = new User({"userID" : profileUserID});

    // Clear messagebox on first load
    if(first) {
        document.getElementById("chatWindow").innerHTML = "";
        first = false;
    }
    try {
        let messages = await(user.getMessages(requestToken, recepientUserID));

        let idx = 0;

        if(messages.forEach instanceof Function) {
            // Loop through each message
            messages.forEach((message) => {
                // Set the text color based on who's sent the message
                let textColor = "";

                if (message["userID"] == recepientUserID) {
                    textColor = "text-danger";
                } else {
                    textColor = "text-primary";
                }

                let messageHTML = "";

                if (idx == messages.length - 1) {
                    // Add the message to the chat
                    messageHTML = "<div id='lastMessage'>" +
                        "<span class='" + textColor + "'>" + message["sender"] + ":</span> " + message["message"].replace("<p>", "").replace("</p>", "") +
                        "</div>";
                } else {
                    // Add the message to the chat
                    messageHTML = "<div>" +
                        "<span class='" + textColor + "'>" + message["sender"] + ":</span> " + message["message"].replace(/(<([^>]+)>)/ig, "")  +
                        "</div>";
                }

                document.getElementById("chatWindow").innerHTML += messageHTML;

                // Resize images
                let images = Array.from(document.getElementById("chatWindow").getElementsByTagName("img"));
                images.forEach((image) => {
                    image.width = 64;
                    image.height = 64;
                });
                idx = idx + 1;

            });

            setTimeout(updateChat, 250);
            document.getElementById("chatWindow").scrollTop = document.getElementById("chatWindow").scrollHeight;

        }
    } catch(err) {
        document.getElementById("chatWindow").innerHTML = err.message;
    }

    try {
        user = new User({"userID" : profileUserID});
        let getParams = new URLSearchParams(window.location.search);
        let isTyping = await(user.isTyping(getParams.get("userID"), requestToken));
        if(isTyping) {
            document.getElementById("isTyping").innerHTML = "Typing...";
        } else {
            document.getElementById("isTyping").innerHTML = "";
        }
    } catch {
        document.getElementById("isTyping").innerHTML = "Login/Request token expired - Refresh the page";
    }
}

updateChat();

// Check if the user is typing or not
let isTyping = false;
function checkIsTyping() {
    let user = new User({"userID" : userUserID});
    if(quill.getLength() <= 1) {
        user.setIsTyping(requestToken, 0);
    } else {
        user.setIsTyping(requestToken, 1);
    }
}

// Automatically check if user is typing
function autoCheckTyping() {
    checkIsTyping();
    setTimeout(autoCheckTyping, 3000);
}
autoCheckTyping();