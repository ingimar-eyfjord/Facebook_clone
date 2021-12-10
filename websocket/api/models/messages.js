const mongoose = require("mongoose");
const MessageModel = new mongoose.Schema(
  {
    username: { type: String, required: true },
    message: { type: String, required: true },
    date: { type: Date, default: Date.now },
    toWhom: { type: String, required: true },
  },
  { collection: "messages" }
);

const model = mongoose.model("MessageModel", MessageModel);
module.exports = model;
