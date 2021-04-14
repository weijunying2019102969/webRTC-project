var WebSocketServer = require('ws').Server;        /** require our websocket library */
var wss = new WebSocketServer({ port: 9090 });     /** creating a websocket server at port 9090 */
var users = {};                                    /** all connected to the server users */
// var resDetail = { "url": "firefox a" };
var resDetail = {};                                /** all url resource */
/** when a user connects to our sever  */
wss.on('connection', function (connection) {
    /** when server gets a message from a connected user */
    connection.on('message', function (message) {
        var data;
        /** accepting only JSON messages */
        try {
            data = JSON.parse(message);
        } catch (e) {
            console.log("Invalid JSON");
            data = {};
        }
        /** switching type of the user message */
        switch (data.type) {
            /** when a user tries to login */
            case "login":
                /** if anyone is logged in with this username then refuse */
                if (users[data.name]) {
                    sendTo(connection, {
                        type: "login",
                        success: false
                    });
                } else {
                    console.log("user connect", data.name);
                    users[data.name] = connection;                     /** save user connection on the server */
                    connection.name = data.name;
                    sendTo(connection, {
                        type: "login",
                        success: true
                    });
                }
                break;

            case "insert":
                /** 插入资源对应的RTCPeerConnection的name */
                resDetail[data.url] = data.name;
                console.log(resDetail);
                var conn = users[data.name];
                sendTo(conn, {
                    type: "insert",
                    success: true
                });
                break;

            case "inquire":
                /** 查询资源对应的RTCPeerConnection的name，并返回 */
                var res = resDetail[data.url];
                var conn = users[data.name];
                if (conn != null) {
                    sendTo(conn, {
                        type: "inquire",
                        url: data.url,
                        res: res || null
                    });
                }
                break;

            case "offer":
                console.log("Sending offer to: ", data.name);         /** for ex. UserA wants to call UserB */
                var conn = users[data.name];                          /** if UserB exists then send him offer details */

                if (conn != null) {
                    connection.otherName = data.name;                  /** setting that UserA connected with UserB */
                    sendTo(conn, {
                        type: "offer",
                        offer: data.offer,
                        name: connection.name
                    });
                }
                break;

            case "answer":
                console.log("Sending answer to: ", data.name);
                var conn = users[data.name];                          /** for ex. UserB answers UserA */
                if (conn != null) {
                    connection.otherName = data.name;
                    sendTo(conn, {
                        type: "answer",
                        answer: data.answer
                    });
                }
                break;

            case "candidate":
                console.log("Sending candidate to:", data.name);
                var conn = users[data.name];
                if (conn != null) {
                    sendTo(conn, {
                        type: "candidate",
                        candidate: data.candidate
                    });
                }
                break;

            case "leave":
                console.log("Disconnecting from", data.name);
                var conn = users[data.name];
                conn.otherName = null;
                /** notify the other user so he can disconnect his peer connection */
                if (conn != null) {
                    sendTo(conn, {
                        type: "leave"
                    });
                }
                break;

            default:
                sendTo(connection, {
                    type: "error",
                    message: "Command not found: " + data.type
                });
                break;
        }
    });
    /**
     * when user exits, for example closes a browser window
     * this may help if we are still in "offer","answer" or "candidate" state
     */
    connection.on("close", function () {
        console.log("user disconnect", connection.name);
        if (connection.name) {
            for (var key in resDetail) {
                if (resDetail[key] == connection.name) {
                    delete resDetail[key];
                }
            }
            delete users[connection.name];
            /** 当用户退出时，删除相关的资源和资源地址 */
            if (connection.otherName) {
                console.log("Disconnecting from ", connection.otherName);
                var conn = users[connection.otherName];
                if (conn != null) {
                    conn.otherName = null;
                    sendTo(conn, {
                        type: "leave"
                    });
                }
            }


        }
    });
});

function sendTo(connection, message) {
    connection.send(JSON.stringify(message));
}
