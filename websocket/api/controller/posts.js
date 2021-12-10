const Posts = require("../models/posts");
const PostsClass = new (class PostsClass {
//   active(ws, message, users, wss) {
//     ws.id = message.Token;
//     ws.username = message.username;
//     users[message.username] = {
//       Token: message.Token,
//       Username: message.username,
//       Date: new Date(),
//     };
//     wss.clients.forEach((client) => {
//       const usersLogged = {
//         intent: "logged-in",
//         users: users,
//       };
//       client.send(JSON.stringify(usersLogged));
//     });
//   }

  async broadCastPost(message, ws, users, wss) {
    const sendBack = {
      intent: "new-post",
    };
    const NewPost = new Posts({
      user: message.postData.user,
      postID: message.postData.postID ,
      postText: message.postData.postText,
    });
    try {
      const PostNewPost = Promise.resolve(NewPost.save(function(err,post){
        sendBack.MongoData = post
        wss.clients.forEach((client) => {
            client.send(JSON.stringify(sendBack));
        });
        }));
    } catch (error) {
      wss.clients.forEach((client) => {
        client.send(JSON.stringify(error));
      });
    }
  }
  async Comment(message, ws, users, wss) {
    if (!message.id || !message.comment || !message.user) {
      res.status(400).send({
        message: "Content can not be empty!",
      });
      return;
    }
 
    const new_Comment = {
        user: message.user,
        comment: message.comment
    };
    try {
        Posts.findOneAndUpdate(
        { _id: message.id },
        { $push: {"comments": new_Comment }},
        { safe: true,upsert: true, new: true },
        function (err, model) {
     
          if (err) {
            wss.clients.forEach((client) => {
                client.send(JSON.stringify(err));
            });
          } else {
              const body = {
                  model: model.comments[model.comments.length -1],
                  newComment: new_Comment,
                  intent: "new_comment",
                  parentId: model.postID,
                  postID: model._id
              }
            wss.clients.forEach((client) => {
                client.send(JSON.stringify(body));
            });
          }
        }
      );
    } catch (error) {
      wss.clients.forEach((client) => {
        if (client.username == message.user) {
          client.send(JSON.stringify(error));
        }
      });
    }
}

      async broadCastAllPost(message, ws, users, wss) {
            const OldPosts = await Posts.find()
              .sort({ date: -1 })
              .lean()
              .limit(20);
              
              wss.clients.forEach((client) => {
                if (client.username == message.user) {
                  client.send(JSON.stringify(error));
                }
              });
            ws.send(
              JSON.stringify({
                intent: "all-posts",
                data: OldPosts,
              })
            );
          }

          async LikePost(message, ws, users, wss){
            const new_Like = {
              user_id: message.user,
            };
          try {
            Posts.findOneAndUpdate(
            { _id: message.mongoID },
            { $push: {'people_liked': new_Like }, $inc : {'likes' : 1}},
            { safe: true, upsert: true, new: true },
            function (err, model) {
              
              if (err) {
                wss.clients.forEach((client) => {
                    client.send(JSON.stringify(err));
                });
              } else {
                wss.clients.forEach((client) => {
                    client.send(JSON.stringify(
                      {intent:"user-liked",
                      model:model
                  }));
                });
              }
            }
          );
        } catch (error) {
          wss.clients.forEach((client) => {
            if (client.username == message.user) {
              client.send(JSON.stringify(error));
            }
          });
        }
          }
          async SharePost(message, ws, users, wss){
          try {
            Posts.findOneAndUpdate(
            { _id: message.mongoID },
            { $inc : {'shares' : 1}},
            { safe: true, upsert: true, new: true },
            function (err, model) {
           
              if (err) {
                wss.clients.forEach((client) => {
                    client.send(JSON.stringify(err));
                });
              } else {
                wss.clients.forEach((client) => {
                    client.send(JSON.stringify(
                      {intent:"user-shared",
                      model:model
                  }));
                });
              }
            }
          );
        } catch (error) {
          wss.clients.forEach((client) => {
            if (client.username == message.user) {
              client.send(JSON.stringify(error));
            }
          });
        }
          }
          async LikeComment(message, ws, users, wss){
            const new_Like = {
              user_id: message.user,
            };
            Posts.findOneAndUpdate({_id:message.parentID, 'comments._id':message.mongoID}, 
            {$push : {'comments.$.comment_likes' : new_Like}, $inc : {'comments.$.likes' : 1}},
            { safe: true, upsert: true, new: true },
            function (err, model) {
              if (err) {
              console.log(err)
                wss.clients.forEach((client) => {
                    client.send(JSON.stringify(err));
                });
              } else {
         
                wss.clients.forEach((client) => {
                    client.send(JSON.stringify(
                      {intent:"user-liked-comment",
                      model:model,
                      commentID: message.mongoID
                  }));
                });
              }
            }
            )
          }


          async CommentAnswer(message, ws, users, wss){
const newComment = {
  user: message.user,
  comment: message.comment,
}
Posts.findOneAndUpdate({_id:message.parent, 'comments._id':message.id}, 
{$push : {'comments.$.comment_answers' : newComment}},
{ safe: true, upsert: true, new: true },
function (err, model) {
  if (err) {
  console.log(err)
    wss.clients.forEach((client) => {
        client.send(JSON.stringify(err));
    });
  } else {
    const comment = model.comments.filter(e=>{return e._id ==  message.id})
    wss.clients.forEach((client) => {
        client.send(JSON.stringify(
          {intent:"new-comment_answer",
          model: comment[0].comment_answers[comment[0].comment_answers.length - 1],
          commentID: message.id
      }));
    });
  }
}
)
};

async CommentAnswerLike(message, ws, users, wss){
  console.log(message)
  Posts.findOneAndUpdate({_id:message.parentID, 'comments.comment_answers._id':message.mongoID}, 
  {$inc : {'comments.comment_answers.$.likes' : 1}},
  { safe: true, upsert: true, new: true },
  function (err, model) {
    if (err) {
    console.log(err)
      wss.clients.forEach((client) => {
          client.send(JSON.stringify(err));
      });
    } else {
      const comment = model.comments.filter(e=>{return e._id ==  message.id})
console.log(model)
      return;
      wss.clients.forEach((client) => {
          client.send(JSON.stringify(
            {intent:"new-comment_answer",
            model: comment[0].comment_answers[comment[0].comment_answers.length - 1],
            commentID: message.id
        }));
      });
    }
  }
  )
}
       
})();
module.exports = PostsClass;
