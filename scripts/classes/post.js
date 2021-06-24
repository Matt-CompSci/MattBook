class Post {
    postData;
    author;

    constructor(postData, author) {
        this.postData = postData;
        this.author = author;
    }

    get postID() {
        return this.postData.postID;
    }

    get userID() {
        return this.postData.userID;
    }

    get content() {
        return this.postData.content;
    }

    get avatar() {
        return this.postData.avatar;
    }

    get userName() {
        return this.postData.username;
    }

    addComment(userID, content) {
        let httpRequest = new XMLHttpRequest();
        httpRequest.open("POST", "ajax/addComment.php", true);
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.send(`token=${requestToken}&postID=${this.postID}&userID=${userID}&content=${encodeURIComponent(content)}`);
    }

    getComments(requestToken) {
        let httpRequest = new XMLHttpRequest();
        httpRequest.open("GET", `ajax/getComments.php?postID=${this.postID}&token=${requestToken}`);
        let getCommentPromise = new Promise((resolve, reject) => {
            httpRequest.onload = function() {
                resolve(JSON.parse(this.responseText));
            }
        });
        httpRequest.send();
        return getCommentPromise;
    }
}

export default Post;