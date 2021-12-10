const mongoose = require("mongoose");
const PostModel = new mongoose.Schema(
  {
    user: { type: String, required: true },
    postID: { type: String, required: true },
    postText: { type: String, required: true },
    date: { type: Date, default: Date.now },
    likes: {type: Number, default: 0 },
    people_liked: [
      {user_id: { type: Number, required: true },
      date_liked: { type: Date, default: Date.now },
      }
    ],
    shares: {type: Number, default: 0 },
    comments: [
        {
        user: { type: String, required: true },
        comment: { type: String , required: true},
        date_posted: { type: Date, default: Date.now },
          comment_answers: [
            {
              user: { type: String, required: true},
              comment: { type: String, required: true },
              likes: {type: Number, default: 0 },
              date_posted: { type: Date, default: Date.now },
            },
          ],
          likes:{type: Number, default: 0 },
          comment_likes:[
            {user_id: { type: Number, required: true },
            date_liked: { type: Date, default: Date.now }
          },
          ]
        },
      ],
  },
  { collection: "posts" }
);

const model = mongoose.model("PostModel", PostModel);
module.exports = model;
