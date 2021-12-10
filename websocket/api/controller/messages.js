const Message = require("../models/messages");
const MessageClass = new (class MessageClass {
  active(ws, message, users, wss) {
    ws.id = message.Token;
    ws.username = message.username;
    users[message.username] = {
      Token: message.Token,
      Username: message.username,
      Date: new Date(),
    };
    wss.clients.forEach((client) => {
      const usersLogged = {
        intent: "logged-in",
        users: users,
      };
      client.send(JSON.stringify(usersLogged));
    });
  }

  async broadCastMessage(message, ws, whom, users, wss) {
    const arr = [message.username, whom];
    arr.sort();
    const ident = arr.join("-");

    const sendBack = {
      intent: "new-message",
      data: [{ message: message.message, username: message.username }],
      toWhom: whom,
      identifier: ident,
      from: message.username,
    };
    const NewMessage = new Message({
      username: message.username,
      message: message.message,
      toWhom: message.toWhom,
    });
    try {
      const PostNewMessage = Promise.resolve(NewMessage.save());
      wss.clients.forEach((client) => {
        if (client.username === message.username || client.username === message.toWhom) {
          client.send(JSON.stringify(sendBack));
        }
      });
    } catch (error) {
      wss.clients.forEach((client) => {
        client.send(JSON.stringify(error));
      });
    }
  }

  async UserIsTyping(message, users, wss) {
    const info = {
      username: message.username,
      intent: "typing",
      toWhom: message.toWhom,
    };

    wss.clients.forEach((client, users, wss) => {
      if (client.username == message.toWhom) {
        client.send(JSON.stringify(info));
      }
    });
  }

  async UserStoppedTyping(message, users, wss) {
    const info = {
      intent: "stopped-typing",
      username: message.username,
      toWhom: message.toWhom,
    };
    wss.clients.forEach((client) => {
      if (client.username == message.toWhom) {
        client.send(JSON.stringify(info));
      }
    });
  }
  async retrieveAndSentMessage(ws, count, whom, username, users, wss) {
    console.log(count, whom, username);

    const messages = await Message.find({
      $or: [{ $and: [{ toWhom: whom }, { username: username }] }, { $and: [{ toWhom: username }, { username: whom }] }],
    })
      .sort({ date: 1 })
      .lean();
    console.log(messages);
    const arr = [username, whom];
    arr.sort();
    const ident = arr.join("-");
    ws.send(
      JSON.stringify({
        intent: "old-messages",
        data: messages,
        toWhom: whom,
        from: username,
        identifier: ident,
      })
    );
  }
})();
module.exports = MessageClass;
