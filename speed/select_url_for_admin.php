<?php
require_once('sys_conf.inc.php');
session_start();
if (isset($_SESSION['admin'])) {
$urllist=array();
$urlnamelist=array();
$link_id=mysql_connect($DBHOST,$DBUSER,$DBPWD);
mysql_select_db($DBNAME);
mysql_query("set names UTF8");

// modify by qijl 2015-03-22
$sql_url="select * from url";
$result_url=mysql_query($sql_url);
while ($row_url=mysql_fetch_array($result_url)){
	array_push($urllist, $row_url["short_url"]);
	array_push($urlnamelist, $row_url["id_url"]);
}

// var_dump($urllist);
// var_dump($urlnamelist);
$url_combine = array_combine($urllist,$urlnamelist);
// var_dump($url_combine);
//modify end
?>
<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<p align="center">
<br><br>
选择您希望测速的网站：
<select name="url_name" id="select_url_name">

<?php
echo '<option value="" selected>-----请选择-----</option>';
foreach ($urllist as $url)
{
	echo '<option value="'.$url_combine[$url].'">'.$url.'</option>';
}
?>
</select>
&nbsp;<input type="submit" value="提交" onclick="geturl()"/>
<script type="text/javascript">
function geturl()
{
	var url=document.getElementById("select_url_name").value;
	
//  alert(url);
	if(url!=""){
		var nextpage = "student_speed_frame_for_admin.php?select_url="+url;
		window.location.href=nextpage;
		document.form.submit();
	}
	else{
		alert("请选择要测速的网址");
	}
}
</script>
</p>
</body>
</html>
<?php 
}
else header("Location: ../speed201503/login.php");


?>