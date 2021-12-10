<?php  require_once(__DIR__.'./header.php'); 
if(!isset($_SESSION['username'])){
header("Location: /login");
}
?>
<main class="content " style="max-height:100vh">
    <div id="openMessages">
        <h2>Messages - online: <span id="onlineNow">0</span></h2>
    </div>
    <script>
    function uploadImages(body) {
        return Promise.resolve(
            $.ajax({
                type: 'POST',
                url: '/posts/images',
                data: JSON.stringify(body),
                success: function(response) {
                    return response;
                },
                error: function(response) {
                    return response;
                }
            })
        );
    }


    async function CreatePost(form) {
        event.preventDefault();
        const img = form.querySelectorAll("img")
        const Data = formToJSON(form)
        Data.postID = `<?= bin2hex(random_bytes(16))?>`
        Data.user = `<?=$_SESSION['username']?>`
        // const images = await createImages(img, Data.postID)
        // console.log(await images)
        // const images = createImages(img, Data.postID, function(val) {
        //     console.log(val)
        // })
        let images = []
        for (const [i, e] of img.entries()) {
            const response = await fetch(e.src)
            const blob = await response.blob()
            images.push(blob)
        }

        images = await Promise.all(images.map((f, index) => {
            return readAsDataURL(f, index)
        }));

        function readAsDataURL(file, index) {
            return new Promise((resolve, reject) => {
                let fileReader = new FileReader();
                fileReader.onload = function() {
                    return resolve({
                        data: fileReader.result,
                        name: `${Data.postID}_${index}`,
                        size: file.size,
                        type: file.type
                    });
                }
                fileReader.readAsDataURL(file);
            })
        }
        const upload = await uploadImages(await images)
        if (upload == "Files uploaded") {
            socket.send(JSON.stringify({
                intent: 'createPost',
                postData: Data
            }))
        }

    }
    </script>
    <div class="createPost card container">

        <form id="createPost" onsubmit="CreatePost(this); return false;">
            <p id="postContentEdit">
                Create a post</p>
            <div class="PostImageContainer displayNone"></div>
            <textarea id="PostTextEditor" name="postText" placeholder="Write new post" class="input-group"></textarea>
            <label class="custom-file-upload_image">
                <input type="file" onchange="loadFile(event)" name="File" id="fileToUpload">
                <i class="bi bi-camera"></i>
            </label>
            <button class="btn btn-primary">Post</button>
        </form>
    </div>

    <div id="postsContainer" class="container d-flex flex-column align-items-center  post_container">

    </div>
    <div class="container p-0  messageContainer displayNone" style="max-height:100%; height:100%">
        <div class="messaging">
            <div id="userList" class="col-12 col-lg-5 col-xl-3 border-right">
                <div class="closeMessage- "><span onclick="closeMessageList()">&#10006;</span> </div>
                <div class="px-4 d-none d-md-block">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <input type="text" id="filterUser" class="form-control my-3" placeholder="Search...">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
</main>
<script>
let currentUser = undefined
// let host = 'ws://exam-websocket.herokuapp.com/';
let host = 'ws://localhost:3000'
let socket = new WebSocket(host);
socket.onopen = async function() {
    await getUsers()
    const token = document.querySelector("#Token").value
    let login = {
        Token: token,
        intent: "login",
        username: `<?= $_SESSION['username']?>`
    }
    let getPosts = {
        intent: "get_posts",
    }
    socket.send(JSON.stringify(login));
    socket.send(JSON.stringify(getPosts));
    socket.onmessage = function(e) {
        const data = JSON.parse(e.data)
        console.log(data)
        if (data.intent === "old-messages") {
            displayMessages(data, "old-messages")
        }
        if (data.intent === "typing") {
            displayUserIsTyping(data)
        }
        if (data.intent === "new-message") {
            const element = document.querySelector("#UserTyping");
            if (element !== null) {
                element.remove()
            }
            displayMessages(data, "new-message")
        }
        if (data.intent === "stopped-typing") {
            const element = document.querySelector(`.typing-${data.username}`);
            if (element !== null) {
                element.remove()
            }
        }
        if (data.intent === "logged-in") {
            loggedIn(data)
        }
        if (data.intent === "Disconnected") {
            loggedIn(data)
        }
        if (data.intent === "new-post") {
            displayNewPost(data)
        }
        if (data.intent === "new_comment") {
            displayNewComment(data)
        }
        if (data.intent === "all-posts") {
            displayPosts(data)
        }
        if (data.intent === "user-liked") {
            updateLikes(data)
        }
        if (data.intent === "user-shared") {
            updateShares(data)
        }
        if (data.intent === "user-liked-comment") {
            updateCommentLike(data)
        }
        if (data.intent === "new-comment_answer") {
            displayNewCommentUnderComment(data)
        }
    };
    // if (socket.readyState === WebSocket.CLOSED) {
    //    // Do your stuff...
    // }
    // let element = document.querySelector(".chat-messages");
    // element.scrollTop = element.scrollHeight;
}

async function displayNewPost(data) {
    const post = await PostTemplate(data.MongoData.postID, data.MongoData.user, data.MongoData.date, data.MongoData
        .postText, data.MongoData.likes, data.MongoData.shares, data.MongoData._id)
    document.querySelector("#postsContainer").insertAdjacentHTML("afterbegin", post)
    tryImages(data.MongoData.user)
    CheckPostImageContents(data.MongoData.postID)
}


function displayPosts(data) {
    data.data.forEach(async e => {
        const post = await PostTemplate(e.postID, e.user, e.date, e.postText, e.likes, e.shares, e._id)
        document.querySelector("#postsContainer").insertAdjacentHTML("beforeend", post)
        tryImages(e.user)
        e.comments.forEach(async t => {
            const post = await CommentTemplate(t.comment, t.user, t.date_posted, t._id, e._id,
                t)
            const commentFooter = document.querySelector(`[data-post_id="${e.postID}"]`)
                .querySelector(".post-footer")
            commentFooter.insertAdjacentHTML("beforeend", post)
            tryImages(t.user)

            t.comment_answers.forEach(async et => {
                console.log(et)
                const commentAns = await CommentAnswerTemplate(et.comment, et.user,
                    et
                    .date, et._id, t._id, et)
                const CommentContainer = document.querySelector(
                    `[data-comment_id="${t._id}"]`)
                CommentContainer.insertAdjacentHTML("beforeend", commentAns)
                tryImages(et.user)
            })

        })

        CheckPostImageContents(e.postID)
    })
}

async function displayNewComment(data) {
    const post = await CommentTemplate(data.newComment.comment, data.newComment.user, Date.now(), data.model
        ._id, data.postID, data.model)
    const commentFooter = document.querySelector(`[data-post_id="${data.parentId}"]`)
        .querySelector(".post-footer")
    commentFooter.insertAdjacentHTML("beforeend", post)
    tryImages(data.newComment.user)
}

function CommentUnderPost(id) {
    const textBox = document.querySelector(`[data-comment_input_id="${id}"]`)
    const body = {
        user: `<?= $_SESSION['username']?>`,
        comment: textBox.value,
        intent: 'comment_first_line',
        id: id
    }
    socket.send(JSON.stringify(body))
    textBox.value = ""

}

function loggedIn(data) {
    const loggedInUsers = Object.values(data.users)
    const elseS = document.querySelectorAll('[class*="status-"]')
    elseS.forEach(e => {
        e.textContent = "Offline"
    })
    let onlineNow = loggedInUsers.length - 1
    $("#onlineNow").text(onlineNow)
    loggedInUsers.forEach(e => {
        if (e.Username !== `<?= $_SESSION['username']?>`) {
            const status = document.querySelector(`.status-${e.Username}`)
            status.textContent = "Online"
        }
    })

}

function sendMessage(button) {

    const element = button.parentElement.querySelector("textarea")
    const message = element.value
    if (element.value.length != 0) {
        if (element.value.trim() != "") {
            socket.send(JSON.stringify({
                username: `<?= $_SESSION['username']?>`,
                message: message,
                intent: 'chat',
                toWhom: button.dataset.towho
            }))
        }
    }
    element.value = ""
}

function displayMessages(data, oldOrNew) {
    const messageContainer = document.querySelector(`.messageList-${data.identifier}`)
    data.data.forEach(e => {
        if (e.username === `<?= $_SESSION['username']?>`) {
            const message = `
            <div class="chat-message-right pb-4">
            <div>
            <img class="rounded-circle mr-1 img-${e.username}" alt="${e.username}"
            width="80" height="80">
            <div class="text-muted small text-nowrap mt-2">${moment(e.date).fromNow()}</div>
            </div>
            <div class="flex-shrink-1 bg-primary rounded text-white py-2 px-3 mr-3 messageSent-right">
            <div class="font-weight-bold text-white mb-1">Me</div>
            ${e.message}
            </div>
            </div>`
            messageContainer.insertAdjacentHTML("beforeend", message)
        } else {
            if (messageContainer === null) {
                const haveMessage = document.querySelector(`.have_message-${e.username}`)
                haveMessage.classList.remove("displayNone")
                const numb = haveMessage.textContent
                haveMessage.textContent = parseInt(numb) + 1
            } else {

                const message = `
            <div class="chat-message-left pb-4">
            <div>
            <img class="rounded-circle mr-1 img-${e.username}" alt="${e.username}"
            width="80" height="80">
            <div class="text-muted small text-nowrap mt-2">${moment(e.date).fromNow()}</div>
            </div>
            <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3 messageSent-left">
            <div class="font-weight-bold mb-1">${e.username}</div>
            ${e.message}
            </div>
            </div>`

                const messageContainer = document.querySelector(`.messageList-${data.identifier}`)
                messageContainer.insertAdjacentHTML("beforeend", message)
            }
        }
        tryImages(e.username)
    })

    if (oldOrNew === "new-message") {
        const instance = OverlayScrollbars($(`.messageList-${data.identifier}`))
        instance.destroy();
    }
    const instance = $(`.messageList-${data.identifier}`).overlayScrollbars({}).overlayScrollbars();
    instance.scroll({
        y: "100%"
    });
}
let timer;
let TimeoutTimer;

function typing(element) {
    if (timer) {
        clearTimeout(timer)
    }
    timer = setTimeout(function() {
        if (element.value.length >= 2) {
            socket.send(JSON.stringify({
                username: `<?= $_SESSION['username']?>`,
                intent: 'typing',
                toWhom: currentUser
            }))
        } else {
            socket.send(JSON.stringify({
                username: `<?= $_SESSION['username']?>`,
                intent: 'stopped-typing',
                toWhom: currentUser
            }))
        }
    }, 2000)
    if (TimeoutTimer) {
        clearTimeout(TimeoutTimer)
    }
    TimeoutTimer = setTimeout(function() {
        socket.send(JSON.stringify({
            username: `<?= $_SESSION['username']?>`,
            intent: 'stopped-typing',
            toWhom: currentUser
        }))
    }, 30000)

    const keyCode = event.keyCode
    timer = setTimeout(function() {
        if (keyCode === 13) {
            element.parentElement.querySelector(".btn").click()
        }
    }, 100)
}

function displayUserIsTyping(data) {
    const arr = ["<?= $_SESSION['username']?>", data.username]
    arr.sort();
    const ident = arr.join("-");
    ///Need here
    if (data.toWhom === `<?= $_SESSION['username']?>`) {
        const typing = document.querySelector(`.${data.username}-Typing`)
        const element = document.querySelector(`.typing-${data.username}`);
        if (element !== null) {
            return;
        }
        const message = `
                        <div class="text-muted small typing-${data.username}">
                        <em>Typing</em>
                        <div id="wave">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        </div>
    </div>
    `
        typing.insertAdjacentHTML("afterbegin", message)
        tryImages(data.username)
    }
}

function tryImages(username) {
    const images = document.querySelectorAll(`.img-${username}`)
    images.forEach(img => {
        img.src = `services/find_image/${username}`;
    })
}
async function getUsers() {
    await $.ajax({
        // dataType: "json",
        // accepts: 'application/json; charset=utf-8',
        url: "/users/getAll",
        success: function(response) {

            const users = document.querySelector("#userList")
            Object.values(response).forEach(e => {
                if (e.username === `<?= $_SESSION['username']?>`) {
                    return;
                }
                const message = `
                <a data-message_user="${e.user_id}" onclick="activateUserChat('${e.username} , ${e.user_id}')" data-name="${e.username}" class="messageUser list-group-item list-group-item-action border-0">
                <div id="userSettings" data-userPreference='${e.username}' data-user_id='${e.user_id}' onclick="changeUserPreference(this)">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                </div>
                <ul class="displayNone user_settings user_setting_list-${e.username}" class="list-group">
                <li class="list-group-item">Call (Not implemented)</li>
                <!-- <a href="tel:+496170961709">Call</a>-->
                <li onclick="TakeMeToProfile('${e.username}')" class="list-group-item">Info (Not implemented)</li>
                <li onclick="blockUser('${e.user_id}')" class="list-group-item list-group-item-danger">Block</li>
                </ul>
                               <div class="badge displayNone have_message-${e.username} bg-success float-bottom">0</div>
                                 <div class="d-flex align-items-start">
                                     <img class="rounded-circle mr-1 img-${e.username}" alt="${e.username}"
                                     width="80" height="80">
                                     <div class="username flex-grow-1 ml-3">
                                     ${e.username}
                                         <div class="small"><span class="fas fa-circle chat-online"></span><span class="status-${e.username}"> Offline</span></div>

                                         <div class="${e.username}-Typing"></div>
                                        
                                     </div>
                                 </div>
                                 <hr class="d-block mt-1 mb-0">
                            </a>
                `
                users.insertAdjacentHTML("beforeend", message)
                tryImages(e.username)
            })
        },
        error: function(result) {
            console.log(result)

        }
    });
    $(function() {
        //The passed argument has to be at least a empty object or a object with your desired options
        //set the scroll-offset to 50 on both axis.
        var instance = $("#userList").overlayScrollbars({}).overlayScrollbars();
    });

}

function activateUserChat(data) {
    let split = data.split(" , ");
    let user = split[0]
    let id = split[1]
    const arr = ["<?= $_SESSION['username']?>", user]
    arr.sort();
    const ident = arr.join("-");
    currentUser = user
    const messageContainer = document.querySelector(".messaging")
    const container = document.querySelector(`.container-${ident}`)
    if (container === null) {
        messageContainer.insertAdjacentHTML("beforeend", `   <div class="card twoGrids container-${ident}" data-message_user="${id}" >
        <div class="closeMessage-${ident}"><span onclick="closeMessage('${ident}')" >&#10006;</span> </div>
        <div class="twoGird-Item" style="position:relative">
        <div class="py-2 px-4 border-bottom d-lg-block" style="height:90%">
        <div class="position-relative" style="height:100%">
            <div class="chat-messages messageList-${ident} p-4" style="height:90%">
            </div>
        </div>
        </div>
        <div style="height:fit-content">
        <div class=" py-3 px-4 border-top">
        <div class="input-group messageGroup">
            <textarea type="text" class="form-control" onkeyup="typing(this)"
                onkeydown="typing(this)" placeholder="Type your message"></textarea>
            <button data-towho="${user}" onclick="sendMessage(this)" class="btn btn-primary">Send</button>
        </div>
         </div>
        </div>
        </div>`)
        socket.send(JSON.stringify({
            intent: 'old-messages',
            count: 20,
            toWhom: user,
            username: `<?= $_SESSION['username']?>`
        }))
    }
}

function changeUserPreference(element) {
    const list = element.nextElementSibling
    list.classList.remove("displayNone")
}
document.addEventListener("click", function(e) {
    if (e.target.id !== "userSettings" && !e.target.classList.contains("dot")) {
        $("[class*='user_setting_list']").addClass("displayNone")
    }
})

function TakeMeToProfile(element) {}


function blockUser(id) {
    let txt;
    let r = confirm("This will block the user, and you won't be able to see them or their messages?");
    if (r !== true) {
        return;
    }
    const body = {
        user_id: <?= $_SESSION['user_id']?>,
        whom: id
    }
    const changeRequest = $.post("/users/block", JSON.stringify(body)).done(function(data) {
        // _$.one('#message').innerHTML = data;
        // _$.one('#message').style.color = "green";
        // return
        const remove = $(`[data-message_user="${id}"]`)
        Object.values(remove).forEach(e => {
            e.remove();
        })
    }).fail(function(data) {
        console.log(data)
        // _$.one('#message').innerHTML = data;
        // _$.one('#message').style.color = "red";
        // return
    });
};
$("#openMessages").click(function(element) {
    $(this).addClass("displayNone")
    $(".messageContainer").removeClass("displayNone")
})
$(function() {
    $('#filterUser').on('input', function() {
        const items = $(".messageUser");
        const val = this.value;
        items.hide().filter(function() {
            return new RegExp('^' + val, 'gi').test($(this).data('name'));
        }).show();
    });
});

function closeMessage(which) {
    $(`.container-${which}`).remove()
}

function closeMessageList() {
    $(".messageContainer").addClass("displayNone")
    $("#openMessages").removeClass("displayNone")
}
const loadFile = function(event) {
    const img = document.createElement("img")
    img.src = URL.createObjectURL(event.target.files[0]);
    img.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
    }
    $(".PostImageContainer").removeClass("displayNone")
    img.classList.add("postImage")
    $(".PostImageContainer").prepend(img)
};

function PostTemplate(post_id, post_user, post_date, post_text, likes, shares, mongo_id) {
    const post = `<div data-post_id="${post_id}" class="col-12 card">
            <div class=" post">
                <div class="post-heading d-flex flex-row align-items-center">
                    <div class="pull-left postAvatar me-3 mb-3">
                            <img class="postAvatar mr-1 img-${post_user}" alt="${post_user}">
                    </div>
                    <div class="pull-left meta">
                        <div class="title h5">
                            <a href="#"><b>${post_user}</b></a>
                            made a post.
                        </div>
                        <h6 class="text-muted time">${moment(post_date).fromNow()}</h6>
                    </div>
                </div>
                <div class="post-description">
                <div id="postContentContainer" class="card p-3">
                <p >${post_text}</p>
                <div class="InnerPostImageContainer"></div>
                </div>
                    
                    <div class="stats">
                        <a href="#" class="btn btn-default stat-item" onclick="likeThisPost('${mongo_id}')">
                            <i class="bi bi-hand-thumbs-up"></i>  <span class="postsLikes">${likes}</span>
                        </a>
                        <a href="#" class="btn btn-default stat-item" onclick="shareThisPost('${mongo_id}')">
                            <i class="bi bi-share"></i>  <span class="postsShares">${shares}</span>
                        </a>
                    </div>
                </div>
                <div class="post-footer">
                    <div class="input-group">
                        <input class="form-control mb-5" data-comment_input_id="${mongo_id}" placeholder="Add a comment" type="text">
                        <span class="input-group-addon" onclick="CommentUnderPost('${mongo_id}')">
                            <a href="#"><i class="bi bi-pencil-square"></i></a>
                        </span>
                    </div>
                </div>
            </div>
        </div>`
    return post
}

function CommentTemplate(comment, user, date, id, parentId, model) {
    const commentTemp = `
    <ul data-comment_id='${id}' class="comments-list">
                        <li class="comment d-flex flex-row align-items-center">
                        <a class="pull-left" href="#">
                                        <img class="postAvatar mr-1 img-${user}" alt="${user}">
                                        </a>
                            <div class="post-heading  d-flex flex-column ">
                                        <div class="comment-body card ">
                                        <div class="comment-heading ">
                                        <h4 class="user me-3">${user}</h4>
                                        <p class="time">${moment(date).fromNow()}</p>
                                        </div>
                                        <p>${comment}</p>
                                        </div>
                                        <div class="stats">
                                        <a href="#" class="btn btn-default stat-item" onclick="likeThisPostComment('${id}', '${parentId}')">
                                        <i class="bi bi-hand-thumbs-up"></i>  <span class="postsCommentLikes">${model.likes}</span>
                                        </a>
                                        <a href="#" class="btn btn-default stat-item" onclick="replyToPost('${id}')">
                                        <i class="bi bi-reply"></i>  
                                        </a>

                                             <div data-comment_answer_editor='${id}' class="input-group comment_answer_editor displayNone">
                                                <input class="form-control" data-comment_answer_input_id="${id}" placeholder="Add a comment" type="text">
                                                <span class="input-group-addon" onclick="CommentUnderPostComment('${id}', '${parentId}')">
                                                 <a href="#"><i class="bi bi-pencil-square"></i></a>
                                                </span>
                                            </div>

                                        </div>
                            </div>
                        </li>
                    </ul>`
    return commentTemp
}


function CommentAnswerTemplate(comment, user, date, id, parentId, model) {
    const commentTemp = `
                        <li data-comment_answer_id='${id}' class="comment comment_answer d-flex flex-row align-items-center">
                                        <img class="postAvatar mr-1 img-${user}" alt="${user}">
                            <div class="post-heading  d-flex flex-column ">
                                        <div class="comment-body card ">
                                        <div class="comment-heading ">
                                        <h4 class="user me-3">${user}</h4>
                                        <p class="time">${moment(date).fromNow()}</p>
                                        </div>
                                        <p>${comment}</p>
                                        </div>
                                        <div class="stats">
                                        <a href="#" class="btn btn-default stat-item" onclick="likeThisPostCommentAnswer('${id}', '${parentId}')">
                                        <i class="bi bi-hand-thumbs-up"></i>  <span class="postsCommentLikes"> 0</span>
                                        </a>
                                      
                                             <div data-comment_answer_editor='${id}' class="input-group comment_answer_editor displayNone">
                                                <input class="form-control" data-comment_answer_input_id="${id}" placeholder="Add a comment" type="text">
                                                <span class="input-group-addon" onclick="CommentUnderPostComment('${id}', '${parentId}')">
                                                 <a href="#"><i class="bi bi-pencil-square"></i></a>
                                                </span>
                                            </div>

                                        </div>
                            </div>
                        </li>
                  `
    return commentTemp
}

function CheckPostImageContents(id) {
    const container = document.querySelector(`[data-post_id="${id}"]`).querySelector(".InnerPostImageContainer")
    $.ajax({
        type: 'GET',
        url: `/posts/images/${id}`,
        success: function(response) {
            const imagePaths = JSON.parse(response);
            imagePaths.forEach(e => {
                const image = `
                <img class="innerPostImage" src="${e}" alt="${e}">
                `
                container.insertAdjacentHTML("afterbegin", image)
            })
        },
        error: function(response) {
            // return response;
            console.log(response)
        }
    })
}

function likeThisPost(id) {
    socket.send(JSON.stringify({
        intent: 'like',
        mongoID: id,
        user: <?= $_SESSION['user_id']?>
    }))
}

function updateLikes(data) {
    const id = data.model.postID
    const likes = document.querySelector(`[data-post_id="${id}"]`).querySelector(".postsLikes")
    likes.textContent = data.model.likes
}

function updateShares(data) {
    const id = data.model.postID
    const likes = document.querySelector(`[data-post_id="${id}"]`).querySelector(".postsShares")
    likes.textContent = data.model.shares
}

function shareThisPost(id) {
    socket.send(JSON.stringify({
        intent: 'share',
        mongoID: id
    }))
}

function replyToPost(id) {
    const editor = document.querySelector(`[data-comment_answer_editor='${id}']`)
    editor.classList.toggle("displayNone")

}

function CommentUnderPostComment(id, parentId) {
    const textBox = document.querySelector(`[data-comment_answer_input_id="${id}"]`)
    const body = {
        user: `<?= $_SESSION['username']?>`,
        comment: textBox.value,
        intent: 'comment_answer',
        id: id,
        parent: parentId
    }
    socket.send(JSON.stringify(body))
    textBox.value = ""

}

function updateCommentLike(data) {
    const likes = document.querySelector(`[data-comment_id="${data.commentID}"]`).querySelector(".postsCommentLikes")
    let Comments = data.model.comments
    Comments = Comments.filter(e => {
        return e._id === data.commentID
    })
    likes.textContent = Comments[0].likes
}


function likeThisPostComment(id, parentId) {
    socket.send(JSON.stringify({
        intent: 'like-comment',
        mongoID: id,
        parentID: parentId,
        user: <?= $_SESSION['user_id']?>
    }))
}

function likeThisPostCommentAnswer(id, parentId) {
    socket.send(JSON.stringify({
        intent: 'like-comment_answer',
        mongoID: id,
        parentID: parentId,
        user: <?= $_SESSION['user_id']?>
    }))
}

async function displayNewCommentUnderComment(data) {
    console.log(data)
    const newAnswer = await CommentAnswerTemplate(data.model.comment, data.model.user, data.model.date_posted, data
        .model._id, data.parentId, data.model)
    const CommentContainer = document.querySelector(`[data-comment_id="${data.commentID}"]`)
    CommentContainer.insertAdjacentHTML("beforeend", newAnswer)
    tryImages(data.model.user)
    const editor = document.querySelector(`[data-comment_answer_editor='${data.commentID}']`)
    editor.classList.toggle("displayNone")
}
</script>
<?php  require_once(__DIR__.'./footer.php'); 