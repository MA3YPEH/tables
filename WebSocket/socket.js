var app = require('express')();
var http = require('http').Server(app);
var socket = require('socket.io')(http);

var port = process.env.APP_PORT;

var user = {
    queue: [],
    chat: [],
    quit: [],
    total: 0
};

socket.on('connection', function(client){
    var userid = false;
    client.on('login', function(data) {
        userid = data.userid;
    });
    client.on('subscribe', function(data) {
        if(data.KEY === KEY){
            var exists = false;
            for (var i=user.queue.length-1; i>=0; i--) {
                if (user.queue[i].userid === userid)
                {
                    exists = true;
                    user.queue[i].join_at = new Date().toISOString();
                    break;       //<-- Uncomment  if only the first term has to be removed
                }
            }
            if (exists === false)
            {
                user.total++;
                user.queue.push({
                    id: user.total,
                    userid: userid,
                    room: data.room,
                    status: 'new',
                    join_at: new Date().toISOString()
                });
            }

            client.join(data.room);
            console.log(userid + " start private chat. You are "+ user.total + ".")
            console.log('joining room', userid, data.room);
        }
    });

    client.on('unsubscribe', function(data) { 

        // if staff will update queue is talked
        for (var i=user.queue.length-1; i>=0; i--) {
            if (user.queue[i].userid === data.room)
            {
                user.queue[i].status = "out";
                break;       //<-- Uncomment  if only the first term has to be removed
            }
        }

        // remove from queue
        for (var i=user.queue.length-1; i>=0; i--) {
            if (user.queue[i].userid === userid)
            {
                user.queue.splice(i, 1);
                break;       //<-- Uncomment  if only the first term has to be removed
            }
        }

        console.log(userid + ' leaving room', data.room);
        client.leave(data.room); 
    });

    client.on('send', function(data) {
        console.log('sending message');
        socket.in(data.room).emit('message', {
            sender: userid,
            message: data.message,
            send_at: new Date().toISOString()
        });
    });
});

app.get('/user/queue', function(req, res) {
    res.json(user.queue);
});

http.listen(process.env.APP_PORT, process.env.APP_IP, function(){
    console.log('listening on *:'+ process.env.APP_PORT);
});