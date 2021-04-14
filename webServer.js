const { response } = require("express");

var app = require("express")();
var http = require("http").Server(app);

http.listen(3000, () => {
    console.log("web server listen port 3000");
});

app.get("/", (require, response) => {
    response.sendFile("index.html", {root: __dirname});
});
