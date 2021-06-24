import Post from "./classes/post.js";
import User from "./classes/user.js";

// Function to update posts inside of the postArea div
async function updatePosts(offset=0) {
    // If initial load then clear the post area
    if(offset == 0) {
        // Clear post area
        document.getElementById("postArea").innerHTML = "";
    }
    // Get user of current page
    let user = new User({"userID" : profileUserID});

    // Get users posts
    let posts = await(user.getPosts(requestToken, offset));

    // Loop through and draw posts
    let idx = 0;

    if(posts.forEach instanceof Function) {
        posts.forEach((post) => {
            drawPost(idx, post, posts, requestToken);
            idx++;
        })
    }
}
window.updatePosts = updatePosts;
updatePosts();

async function drawPost(idx, post, posts, requestToken) {
    if(idx == posts.length - 1) {
        document.getElementById("postArea").innerHTML += "<div class='container'>" +
            "<div class='row mb-2 bg-light' style='border: cornflowerblue solid 2px'>" +
            "<div class='col-3'> + " +
            "<img src='files/" + post.avatar + "' width='50em' height='50em' style='border-radius: 45px'/>" +
            "<h5>" + post.userName + "</h5>" +
            "</div>" +
            "<div class='col-9' id='lastPost'>" +
            post.content.replace("/\\n/g", "") +
            "</div>" +
            `<div id='commentArea${post.postID}' class='container'></div>` +
            `<textarea class=\"form-control\" id=\"commentBox${post.postID}\" rows=\"1\" placeholder='Comment'></textarea>` +
            `<button type=\"button\" id='${post.postID}' onclick='addComment(this)' class=\"mt-2 ml-2 mb-2 btn  btn-sm btn-primary\">Comment</button>` +
            "</div>" +
            "</div>" +
            "</div>";
    } else {
        document.getElementById("postArea").innerHTML += "<div class='container'><div class='row mb-2 bg-light' style='border: cornflowerblue solid 2px'>" +
            "<div class='col-3' style='border-bottom: cornflowerblue solid 2px'> + " +
            "<img src='files/" + post.avatar + "' width='50em' height='50em' style='border-radius: 45px'/>" +
            "<h5>" + post.userName + "</h5>" +
            "</div>" +
            "<div class='col-9' style='border-bottom: cornflowerblue solid 2px'>" +
            post.content.replace("/\\n/g", "") +
            "</div>" +
            `<div id='commentArea${post.postID}' class='container'></div>` +
            `<textarea class=\"form-control\" id=\"commentBox${post.postID}\" rows=\"1\" placeholder='Comment'></textarea>` +
            `<button type=\"button\" id='${post.postID}' onclick='addComment(this)' class=\"mt-2 ml-2 mb-2 btn  btn-sm btn-primary\">Comment</button>` +
            "</div>" +
            "</div>" +
            "</div>";
    }

    let comments = await(post.getComments(requestToken));

    if(comments.forEach instanceof Function) {
        document.getElementById("commentArea" + post.postID).innerHTML = "<h5>Comments:</h5>";
        comments.forEach((comment) => {
            document.getElementById("commentArea" + post.postID).innerHTML += "" +
                "<div class='row mb-2' style='background-color: lightgrey; border: greenyellow solid 2px'>" +
                "<div class='col-3'> + " +
                "<img src='files/" + comment.avatar + "' width='25em' height='25em' style='border-radius: 45px'/>" +
                "<h5>" + comment.fullName + "</h5>" +
                "</div>" +
                "<div class='col-9'>" +
                comment.content +
                "</div>" +
                "</div>";
        })
    }

    idx++;
}

// Function to add a new post when the user presses the 'Post' button
function addPost() {
    let user = new User({"userID" : profileUserID});

    // Add post
    user.addPost(requestToken, quill.container.firstChild.innerHTML);

    // Clear the post box
    quill.setText("");
    // Update the posts to show the post that's just been added
    location.reload();
}
window.addPost = addPost;

// Function to add a comment
function addComment(e) {
    let postID = e.id;
    let post = new Post({"postID" : postID});
    let content = document.getElementById("commentBox" + postID).value;
    post.addComment(profileUserID, content);
    postsLoaded = 0;
    updatePosts();
}
window.addComment = addComment;

let postsLoaded = 0;
let firstLoad = true;
window.onscroll = function(e) {
    if(firstLoad) {
        firstLoad = false;
    } else {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
            postsLoaded += 5;
            updatePosts(postsLoaded);
        }
    }
};