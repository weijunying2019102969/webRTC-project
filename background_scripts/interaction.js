/** web server地址端口号，重定向空白页使用 */
var server = "http://192.168.1.109:3000/";
/** web server地址 */
var serverHost = "http://192.168.1.109/";
/** socket.io地址 */
var socketIo = "ws://192.168.1.109:9090/"
/** turnserver */
var turnServer = {
    'url': 'turn:192.168.1.109:3478',
    'username': 'server',
    'userpwd': 'server'
}
/** 创建socket.io对象 */
var socket = new WebSocket(socketIo);
/** socket.io client名称，随机生成 */
var peerName;
/** 记录需要从localStorage中获取的网站 */
var needGetPage = null;
/** 记录是否向页面插入信息 */
var isInsertToPage = false;
/** 记录RTCPeerConnection */
var RTCConn = null;
/** 记录RTCDataChannel，使用完关闭 */
var dataChannel = null;
/** RTCPeerConnection对方名称 */
var connectUser = null;
/** 网页内容 */
var pageData = null;
/** 收到消息的队列 */
var receiveData = {};
/** 等待时间间隔 */
const intervalTime = 100;

/** 进行缓存的网址 */
var webUrlList = ["<all_urls>", "https://www.baidu.com/", "https://www.cnblogs.com/", "https://hd.58.com/", "http://127.0.0.1/main.html"];

/** MIME对应关系 */
var MIMEList = {
    txt: "text/plain",
    svg: "image/svg+xml",
    png: "image/png",
    gif: "image/gif",
    jpg: "image/jpeg",
    html: "text/html",
    htm: "text/html",
    css: "text/css",
    js: "application/javascript",
    bmp: "image/bmp",
    ico: "image/x-icon"
};

/**
 * 向socket.io server发送消息
 * @param {消息类型} msgType 
 * @param {消息} msg 
 */
async function sockSendMsg(msgType, msg = null) {
    switch (msgType) {
        /** 和socket.io server建立连接 */
        case "login":
            peerName = Math.random().toString().substring(2);
            socket.send(JSON.stringify({
                type: msgType,
                name: peerName
            }));
            break;

        /** 插入资源和资源地址到socket.io server */
        case "insert":
            socket.send(JSON.stringify({
                type: msgType,
                name: peerName,
                url: msg.url
            }));
            break;

        /** 查询socket.io server上是否有资源 */
        case "inquire":
            socket.send(JSON.stringify({
                type: msgType,
                name: peerName,
                url: msg.url
            }));
            var msgData = await new Promise(resolve => {
                socket.onmessage = e => {
                    resolve(e);
                }
            });
            return JSON.parse(msgData.data);
            break;

        case "offer":
            createChannel();
            connectUser = msg.name;
            RTCConn.createOffer(function (offer) {
                socket.send(JSON.stringify({
                    type: msgType,
                    offer: offer,
                    name: msg.name
                }));
                RTCConn.setLocalDescription(offer);
            }, function (error) {
                console.log("An error has occurred.");
            });
            break

        case "answer":
            socket.send(JSON.stringify({
                type: msgType,
                answer: msg.answer,
                name: msg.name
            }));
            break;

        case "candidate":
            socket.send(JSON.stringify({
                type: msgType,
                candidate: msg.candidate,
                name: msg.name
            }));
            break

        default: break;
    }
}

/** 当socket打开后发送login建立socket.io连接 */
socket.onopen = function () {
    sockSendMsg("login");
}

socket.onmessage = sockOperMsg;

/** socket消息处理 socket.onmessage */
function sockOperMsg(message) {
    // console.log(message);
    var data = JSON.parse(message.data);
    switch (data.type) {
        case "login":
            if (data.success == false) {
                sockSendMsg("login");
            } else if (data.success == true) {
                console.log("login success");
            }
            break;

        case "inquire":
            if (data.res) {
                /** 资源存在 */
            } else {
                /** 资源不存在 */
            }
            break;

        case "offer":
            createChannel();
            connectUser = data.name;
            RTCConn.setRemoteDescription(new RTCSessionDescription(data.offer));
            RTCConn.createAnswer(function (answer) {
                RTCConn.setLocalDescription(answer);
                sockSendMsg("answer", { "answer": answer, "name": data.name });
            }, function (e) {
                console.log(e);
            });
            break;

        case "answer":
            RTCConn.setRemoteDescription(new RTCSessionDescription(data.answer));
            break;

        case "candidate":
            RTCConn.addIceCandidate(new RTCIceCandidate(data.candidate));
            break;

        default:
            break;
    }
}

/** 创建RTCDataChannel */
async function createChannel() {
    var configuration = {
        'iceServers': [{
            'urls': turnServer.url,
            'username': turnServer.username,
            'credential': turnServer.userpwd
        }]
    };
    RTCConn = new RTCPeerConnection(configuration, {
        optional: [{ RtpDataChannels: true }]
    });
    console.log("RTCPeerConnection object was created");
    RTCConn.onicecandidate = function (event) {
        if (event.candidate) {
            sockSendMsg("candidate", { "candidate": event.candidate, "name": connectUser });
        }
    };
    var dataChannelOptions = {
        reliable: true,
        negotiated: true,
        id: 1
    };
    dataChannel = RTCConn.createDataChannel("myDataChannel", dataChannelOptions);
    dataChannel.onmessage = dataChannelOnMsg;
    dataChannel.onopen = dataChannelOnOpen;
    dataChannel.onclose = function (e) {
        console.log("channel is closed", e);
        RTCConn.close();
        console.log(RTCConn);
    }

    dataChannel.onerror = function (error) {
        console.log("Error:", error);
    };
}

/**
 * 创建RTCDataChannel连接
 * @param {WebSocket name} name 
 */
function createChannelConn(name) {
    sockSendMsg("offer", { "name": name });
}

/**
 * 发送消息，获取资源
 * @param {消息url} msgData 
 */
function dataChannelSend(msgData) {
    dataChannel.send(JSON.stringify({
        name: peerName,
        type: "request",
        url: msgData.url
    }))
}

/**
 * RTCDataChannel消息处理
 * @param {消息} msg 
 */
function dataChannelOnMsg(msg) {
    var msgData = JSON.parse(msg.data);
    console.log(msgData);
    if (msgData.type == "response") {
        receiveData[msgData.url] = msgData.data || msgData.url;
    } else if (msgData.type == "request") {
        dataChannel.send(JSON.stringify({
            name: peerName,
            type: "response",
            url: msgData.url,
            data: localStorage.getItem(msgData.url)
        }));
    }
}

/** RTCDataChannel onopen事件 */
async function dataChannelOnOpen(msg) {
    if (isInsertToPage == true) {
        dataChannelSend({ "url": needGetPage });
        while (receiveData[needGetPage] == null) {
            await new Promise(resolve => setTimeout(resolve, intervalTime));
        };
        pageData = receiveData[needGetPage];
        chrome.tabs.query({ active: true, currentWindow: true }, function (tabs) {
            chrome.tabs.sendMessage(tabs[0].id, { "data": receiveData[needGetPage] }, response => { });
        });
        isInsertToPage = false;
    }
}

/**
 * 获取文本类文件的data uel，存储到localStorage中，异步
 * @param {文件的localStorage的键} fileAddr 
 * @param {标识main_frame，true或false} isMain 
 */
function setTxtDataUrl(fileAddr, isMain) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", fileAddr, true);
    xhr.addEventListener("load", function () {
        if (xhr.status == 200) {
            var responseText = (xhr.responseText || xhr.responseXML);
            if (isMain) {                                                         /** 判断网页类型是否为main_frame */
                localStorage.setItem(fileAddr, responseText);
            } else {
                var urlAddrList = fileAddr.split(".");
                var urlType = urlAddrList[urlAddrList.length - 1];
                var dataUrl = "data:" + (MIMEList[urlType] || "text/plain") + ";charset=utf-8," + encodeURIComponent(responseText);      // 构造data url
                localStorage.setItem(fileAddr, dataUrl);
                // console.log(fileAddr, localStorage.getItem(fileAddr));
            }
        };
    }, false);
    xhr.send();
}

/**
 * 获取二进制文件的data url，存储到localStorage中，异步
 * @param {文件的localStorage的键} fileAddr 
 */
function setBinDataUrl(fileAddr) {
    var xhr = new XMLHttpRequest();
    var fileReader = new FileReader();
    xhr.open("GET", fileAddr, true);                                                    // 异步执行
    xhr.responseType = "blob";
    xhr.addEventListener("load", function () {
        if (xhr.status == 200) {                                                         // 状态码为200
            fileReader.onload = function (evt) {
                localStorage.setItem(fileAddr, evt.target.result);                      // data url根据地址存入localStorage
                // console.log(fileAddr, localStorage.getItem(fileAddr));
            }
            fileReader.readAsDataURL(xhr.response);
        };
    }, false);
    xhr.send();
}

/** 地址转换 */
function translateAddr(addr) {
    return needGetPage + addr.substring(server.length);
}

/**
 * 根据URL类型进行重定向
 * @param {*} urlDetail 
 */
async function getResToStorage(urlDetail) {
    /** 如果HTTP请求的类型为websocket、xmlhttprequest不进行操作 */
    if (urlDetail.type == "websocket" || urlDetail.type == "xmlhttprequest" || urlDetail.url == server) return;
    var fileAddr = urlDetail.url;
    /** 如果RTCPeerConnection存在，获取需要的资源 */
    if (urlDetail.originUrl == server) {
        /** localStorage中存储的资源地址是原网站的地址，而现在的地址是http://192.168.1.109:3000/ */
        /** 地址转换 http://192.168.1.109:3000/index.css 转换为 http://192.168.1.108/index.css */
        fileAddr = translateAddr(fileAddr)
        if (connectUser == peerName) {
            /** 在本机取资源 */
            return { redirectUrl: localStorage.getItem(fileAddr) };
        } else {
            /** 通过RTCDataChannel取资源 */
            dataChannelSend({ "url": fileAddr });
            while (receiveData[fileAddr] == null) {
                await new Promise(resolve => setTimeout(resolve, intervalTime));
            }
            return { redirectUrl: receiveData[fileAddr] };
        }
    }
    if (urlDetail.type == "main_frame") {
        /** 网站的主页面 */
        var msg = await sockSendMsg("inquire", { "url": fileAddr });
        socket.onmessage = sockOperMsg;
        /** 获得socket.io server发送来的信息 */
        if (msg.res == null) {
            /** 资源不存在，对资源进行缓存 */
            connectUser = null;
            needGetPage = null;
            sockSendMsg("insert", { "url": fileAddr });
            setTxtDataUrl(fileAddr, true);
        }
        else {
            /** 第一钟情况资源存在，但不是本机，建立RTCPeerConnection连接 */
            /** 第二种情况资源存在，是本机 */
            needGetPage = fileAddr;
            connectUser = msg.res;
            if (msg.res != peerName) {
                /** 建立RTCDataChannel，重定向到空白页 */
                createChannelConn(msg.res);
                isInsertToPage = true;
            }
            return { redirectUrl: server };
        }
    } else {
        /** 网站的资源，缓存 */
        console.log("资源缓存", urlDetail.url);
        if (urlDetail.type == "image") {
            setBinDataUrl(fileAddr);
        } else {
            setTxtDataUrl(fileAddr, false);
        }
    }
}

chrome.webRequest.onBeforeRequest.addListener(getResToStorage, { urls: webUrlList }, ["blocking"]);

chrome.runtime.onMessage.addListener(function (request, sender, sendResponse) {
    console.log(request);
    sendResponse({ "data": localStorage.getItem(needGetPage) || pageData });
    pageData = null;
});
