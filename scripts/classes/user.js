import Post from "./post.js";

class User {
    // Private field to store information about the user
    userInfo;

    constructor(userInfo) {
        this.userInfo = userInfo;
    }

    // Get the users userID
    get userID() {
        return this.userInfo["userID"];
    }

    // Get the users first name
    get firstName() {
        return this.userInfo["firstName"];
    }

    // Get the users second name
    get secondName() {
        return this.userInfo["secondName"];
    }

    // Get the users full name
    get fullName() {
        // ES6 template string literals to concatenate first and second name
        return `${this.firstName}  ${this.secondName}`;
    }

    // Get the users avatar
    get avatar() {
        return this.userInfo["avatar"];
    }

    // Returns a list of user objects containing user objects of all the users friends
    getFriends(requestToken, limit=5) {
        // Create http request to grab users friends
        let httpRequest = new XMLHttpRequest();
        httpRequest.open("GET", `ajax/getFriends.php?userID=${this.userID}&limit=${limit}&token=${requestToken}`, true);

        let httpRequestPromise = new Promise((resolve, reject) => {
            // Callback that's automatically called when the HTTP request is completed
            httpRequest.onload = function () {
                // If the http request was successful then parse the json into a table
                // Otherwise store an empty table to avoid errors and is passed the list of users
                if (httpRequest.status == 200) {
                    try {
                        // Try and parse the received json string into a table
                        let friends = JSON.parse(this.responseText);
                        // If the request token is invalid or has expired then return the user to the login page
                        if (friends.error) {
                            reject(Error("Expired token"));
                        } else {
                            // Convert each table into users
                            resolve(friends.map(friend => new User(friend)));
                        }
                    } catch {
                        // If JSON.Parse fails return an empty table
                        reject(Error("[Friends List] : Error parsing JSON"));
                    }
                } else {
                    // If the request fails return an empty table
                    reject(Error("[Friends List] : Error retrieving data from the server"));
                }
            }
        });

        // Send HTTP request and return it's promise
        httpRequest.send();
        return httpRequestPromise;
    }

    addFriend(requestToken, userID) {
        // Send userID and requestToken to the
        let httpRequest = new XMLHttpRequest();
        httpRequest.open("POST", "ajax/addFriend.php", true);
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.send(`userID=${userID}&token=${requestToken}`);
    }

    removeFriend(requestToken, userID) {
        let httpRequest = new XMLHttpRequest();
        httpRequest.open("POST", "ajax/removeFriend.php", true);
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.send(`userID=${userID}&token=${requestToken}`);
    }

    isTyping(userID, requestToken) {
        let httpRequest = new XMLHttpRequest();
        httpRequest.open("GET", `ajax/getIsTyping.php?userID=${userID}&token=${requestToken}`, true);
        let isTypingPromise = new Promise((resolve, reject) => {
            httpRequest.onload = function() {
                try {
                    let responseData = JSON.parse(this.responseText);
                    resolve(responseData.isTyping);
                } catch {
                    reject(new Error("Failed to parse JSON"));
                }
            };
        });
        httpRequest.send();
        return isTypingPromise;
    }

    setIsTyping(requestToken, isTyping) {
        let httpRequest = new XMLHttpRequest();
        let currentUser = this;
        httpRequest.open("POST", "ajax/setIsTyping.php", true);
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.send(`userID=${currentUser.userID}&token=${requestToken}&isTyping=${isTyping}`);
    }

    getPosts(requestToken, offset=0, limit=5) {
        let httpRequest = new XMLHttpRequest();
        httpRequest.open("GET", `ajax/getPosts.php?userID=${this.userID}&token=${requestToken}&limit=${limit}&offset=${offset}`, true);
        // As this will refer to the httpRequest inside the onload function let's cache the User into a variable
        let currentUser = this;

        // Promise used to retrieve the users posts
        let getPostPromise = new Promise((resolve, reject) => {
            // When the posts have been loaded
            httpRequest.onload = function() {
                // Parse the posts into a table
                let posts = JSON.parse(this.responseText);
                // Parse the table into Post objects
                resolve(posts.map((post) => new Post(post, currentUser)));
            };
        });

        httpRequest.send();
        return getPostPromise;
    }

    addPost(requestToken, content) {
        // Initialise http request
        let httpRequest = new XMLHttpRequest();

        // open post request
        httpRequest.open("POST", "ajax/addPosts.php", true);

        // Update the request header to allow data to be sent over a post request
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        // Send the content of the post to the server using the post request
        httpRequest.send(`content=${encodeURIComponent(content)}&token=${requestToken}`);
    }

    getMessages(requestToken, userID, offset=0, limit=5) {
        // Initialise http request
        let httpRequest = new XMLHttpRequest();

        // open get request
        httpRequest.open("GET", `ajax/getMessages.php?userID=${userID}&token=${requestToken}&offset=${offset}&limit=${limit}`, true);

        // create promise to retrieve data from http request
        let getMessagePromise = new Promise((resolve, reject) => {
            httpRequest.onload = function() {
                try {
                    let response = JSON.parse(httpRequest.responseText);
                    if(response.error) {
                        reject(new Error(response.error))
                    } else {
                        resolve(response);
                    }
                } catch {
                    reject(new Error())
                }


            }
        });

        httpRequest.send();
        return getMessagePromise;
    }

    sendMessage(requestToken, userID, message) {
        let httpRequest = new XMLHttpRequest();

        // Start post request to sendMessage model asyncronously
        httpRequest.open("POST", "ajax/sendMessage.php", true);

        // Update request header so data can be sent using the post request
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        // Send post request
        httpRequest.send(`userID=${userID}&message=${message}&token=${requestToken}`);
    }
}

export default User;