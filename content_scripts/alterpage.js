(function () {
    console.log("alterpage.js");
    chrome.runtime.onMessage.addListener(function (request, sender, sendResponse) {
        document.body.innerHTML = request.data;
        console.log(request.data);
    });
    chrome.runtime.sendMessage({ "state": "i am ready" }, res => {
        console.log(res);
        document.body.innerHTML = res.data;
    });
})();