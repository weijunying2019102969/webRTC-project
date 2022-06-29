
<?php
require_once('sys_conf.inc.php');
$urlkeylist=array();
$urlidlist=array();
$link_id=mysql_connect($DBHOST,$DBUSER,$DBPWD);
mysql_select_db($DBNAME);
mysql_query("set names UTF8");

// modify by qijl 2015-03-22
$sql_url="select * from url where isShow='1'";
$result_url=mysql_query($sql_url);
while ($row_url=mysql_fetch_array($result_url)){
        array_push($urlkeylist, $row_url["short_url"]);
        array_push($urlidlist, $row_url["id_url"]);
}

?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>我行我速</title>

<meta name="description" content="我行我速">

<link rel="stylesheet" type="text/css" href="bootstrap-combined.min.css">
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="bootstrap-ie6.min.css">
<![endif]-->
<!--[if lte IE 7]>
<link rel="stylesheet" type="text/css" href="ie.css">
<![endif]-->

<link rel="stylesheet" type="text/css" href="index.css">
<link rel="search" type="application/opensearchdescription+xml" href="opensearch.xml" title="gfsoso">

<script src="base.js"></script>
<!--[if lte IE 6]>
<script type="text/javascript" src="bootstrap-ie.js"></script>
<![endif]-->
<!--[if lt IE 9]>
<script src="ie9.js"></script>
<![endif]-->


<!-- <script src="iui.js"></script> -->


<script language="javascript">
if(top.location!==self.location){
	top.location.href=self.location.href;
}
</script>


<style type="text/css">
html,body{width:100%;height:100%;margin:0px;padding:0px;}
#bottom{width:100%;height:35px;border:0px #FF9900 solid;background-color:#FFCCCC;position:absolute;top:0px;left:0px;visibility:hidden;}
</style>


</head>

<body onload='$("#lst-ib").focus()'>
<div>
	<center>
		
		<div id="logo-gf" style="height:291px;">
		  <div id="logo-entity" style="padding-top:170px;">
				<h1>
					<img id="logo-img" src="logo.jpg" alt="我行我速" width="304px" height="76px">
				</h1>
			    <img src="v1.gif" width="25" height="43"></div>
		</div>
		

		<div>
			<ul id="middle-nav" class="nav nav-pills inline" style="padding-bottom:10px;">
				<li class="active">
				<a href="#" style="color:#fff;"><strong>校内测校外</strong></a>
				</li>
				<li class="">
				<div title="相约V2.0"  style="color:#666; cursor:pointer">校外测校内</div>
				</li>
				<li class="">
				<div title="相约V2.0"  style="color:#666; cursor:pointer">自定义</div>
				</li>
			</ul>
		</div>


<select name="url_name" id="select_url_name"  class="input-xxlarge">

<?php
echo '<option style="font-size:14px;height:25px;max-width:500px;" value="select">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;选择要测速的目标网站</option>';

for ($i=0;$i<count($urlidlist);$i++){
// echo 'id = '.$urlidlist[$i].' url = '.$urlkeylist[$i].'<br>';
	echo '<option style="font-size:14px;height:25px;max-width:500px; text-align:center;" value='.$urlidlist[$i].'>'.$urlkeylist[$i].'</option>';
}
// foreach ($urllist as $url)
// {
// 	echo '<option style="font-size:14px;height:25px;max-width:500px; text-align:center; value="'.$url_combine[$url].'">'.$url.'</option>';
// }
?>
</select>
		<div style="padding-top:10px;">
			<input value="我要测速" class="btn btn-fl" style="margin-left:5px;" type="submit" onclick="geturl()">
			<a href="../speed201503/current_speed.php"><span class="btn btn-fl" style="margin-left:5px;">看看全校</span></a>
		</div>

<script type="text/javascript">
function geturl()
{
	var url=document.getElementById("select_url_name").value;
// 	alert(url);
// 	var index=url.selectedIndex;
// 	va val=url.options[index].value;
// 	alert(val);
	if(url!=""){
		var nextpage = "student_speed_frame.php?select_url="+url;
		window.location.href=nextpage;
		document.form.submit();
	}
	else{
		alert("选择测速的目标网站");
	}
}
</script>



		</div>	

		<div id="hint">
			<font size="-1">
				<div class="hint-links">
					
<style>
.x-circle {
-webkit-border-radius: 500px;
-moz-border-radius: 500px;
border-radius: 500px;
border:1px solid #ccc;
width:44px;
padding:0px;
margin:20px;
}

</style>



				</div>
			</font>
		</div>

	</center>
</div>







<div id="bottom">
<table  style=" position:absolute; vertical-align:bottom; background-color:#CCCCCC; border-bottom:0" border="0" width="100%" height="35">
  <tr>
    <td>
  <font color="#FF0000">提示：若同时下载文件或在线播放视频，测速结果可能受到影响。</font>
    </td>
	<td align="right">
	2015 &copy;信息化管理与规划办公室    
	</td>
  </tr>

</table>
</div>

<div>
	

</div>

<script type="text/javascript">
window.onload = position;
window.onresize = position;
function position(){
    var bottomDiv = document.getElementById("bottom");
    var ch,cw,bottomWidth,bottomHeight;
    ch = document.body.clientHeight;
    cw = document.body.clientWidth;
    bottomWidth = bottomDiv.offsetWidth;
    bottomHeight = bottomDiv.offsetHeight;
    with(bottomDiv){
        style.top=(ch - bottomHeight)+"px";
        style.left=(cw - bottomWidth)+"px";
        style.visibility = "visible";
    }
};
</script>

</body>
</html>
