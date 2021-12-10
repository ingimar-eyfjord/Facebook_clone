const express = require("express");
const { Server } = require("ws");
const mongoose = require("mongoose");
const PORT = process.env.PORT || 3000;
const INDEX = "/index.html";
const Message = require("./api/controller/messages");
const Post = require("./api/controller/posts");
const server = express()
  .use((req, res) => res.sendFile(INDEX, { root: __dirname }))
  .listen(PORT, () => console.log(`Listening on ${PORT}`));

  
const wss = new Server({ server });
var corsOptions = {
  // origin will allow specific domains to access the server.
  origin: "*",
};
let clients = [];
function processMessage(payload) {
  try {
    return JSON.parse(payload);
  } catch (error) {
    return null;
  }
}
mongoose
  .connect(
    "mongodb+srv://Ingimar:ecktHK2Jd98jolfl@cluster0.o7mt9.mongodb.net/ExamMessageApp?retryWrites=true&w=majority",
    {
      useNewUrlParser: true,
      useUnifiedTopology: true,
      useFindAndModify: false,
    }
  )
  .then(() => console.log("MongoDB has been connected"))
  .catch((err) => console.log(err));
let users = {};
wss.on("connection", function connection(ws) {
  ws.send(JSON.stringify({ intent: "connected" }));
  clients.push(ws);
  ws.on("close", () => {
    // remove saved ws from users object
    delete users[ws.id];
    wss.clients.forEach((client) => {
      const usersLogged = {
        intent: "Disconnected",
        users: users,
      };
      client.send(JSON.stringify(usersLogged));
    });
  });
  ws.on("message", function incoming(payload) {
    const message = processMessage(payload.toString());
    if (!message) {
      // corrupted message from Client
      // ignore
      return;
    }
    if (message.intent === "chat") {
      const whom = message.toWhom;
      if (whom !== undefined) {
        Message.broadCastMessage(message, ws, whom, users, wss);
      }
    }
    if (message.intent === "old-messages") {
      const count = message.count;
      if (!count) return;
      const whom = message.toWhom;
      Message.retrieveAndSentMessage(ws, count, whom, message.username, users, wss);
    }
    if (message.intent === "typing") {
      Message.UserIsTyping(message, users, wss);
    }
    if (message.intent === "stopped-typing") {
      Message.UserStoppedTyping(message, users, wss);
    }
    if (message.intent === "login") {
      Message.active(ws, message, users, wss);
    }
    if (message.intent === "createPost") {
      Post.broadCastPost(message, ws, users, wss);
    }
    if (message.intent === "comment_first_line") {
      Post.Comment(message, ws, users, wss);
    }
    if(message.intent === "get_posts"){
      Post.broadCastAllPost(message, ws, users, wss);
    }
    if(message.intent === "like"){
      Post.LikePost(message, ws, users, wss);
    }
    if(message.intent === "share"){
      Post.SharePost(message, ws, users, wss);
    }
    if(message.intent === "like-comment"){
      Post.LikeComment(message, ws, users, wss);
    }
    if(message.intent === "comment_answer"){
      Post.CommentAnswer(message, ws, users, wss);
    }
    if(message.intent === "like-comment_answer"){
      Post.CommentAnswerLike(message, ws, users, wss);
    }
    
  });
});


function setClients(Newclients) {
  clients = Newclients;
}
