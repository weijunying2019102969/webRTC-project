# webRTC-project 源代码说明
# 该项目的部署环境：一台WebSocket Server，一台TURN Server，一台Web Server和若干个带火狐浏览器的PC，在每一台火狐浏览器中安装我们项目的结果插件。
# 该项目的技术路线：首先，当系统内的某一台浏览器A第一次访问某一个网站时，我们的项目插件（下面缩写问MCNAS客户端）会拦截这次的网络请求，然后将网络请求的内容缓存至浏览器中，
# 并且向WebSocket Server登记缓存的资源。释放掉此次浏览器的网络请求，使其能够正常访问。
# 在系统内的某一台浏览器B再次访问相同的网络资源时，MCNAS客户端会拦截掉此次网络请求，然后向WebSocket Server查询系统内是否有此资源，如果有则返回给浏览器B（此时应该返回浏览
# 器A的地址），浏览器B根据返回的内容与浏览器A建立WebRTC连接，并且向浏览器A请求资源。
# 建立WebRTC连接涉及到使用TURN Server。而在资源重定向的过程中涉及到了跨域问题，本项目借用Web Server存储了空白的html资源，解决了跨域问题。
# 代码说明：
# 其中客户端包括manifest.json, interaction.js和alterpage.js。其中manifest.json是浏览器插件的配置文件，interaction.js是浏览器插件的background script，负责与服务器进行
# 交互，alterpage.js是浏览器插件的content script负责与浏览器进行狡猾。interaction.js和alterpage.js之间可以进行信息交换。服务端包括MCNASServer.js为WebSocket Server和
# index.html的主要内容，负责管理维护资源信息，也负责在客户端之间建立端对端连接时提供信息交换中介服务。webServer.js为Web Server的主要内容，负责维护index.html资源。
