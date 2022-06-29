<?php

require('sys_conf.inc.php');
session_start();
?>
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<?php 
if (isset($_SESSION['admin'])) {
		
	//db delete history data
	$link_id=mysql_connect($DBHOST,$DBUSER,$DBPWD);
	mysql_select_db($DBNAME);
	mysql_query("set names UTF8");
// 	$delete_sql3="Truncate Table sensor_url_relation";
// 	$result3=mysql_query($delete_sql3,$link_id);
	
	$exist_sensor=array();
	$exist_sensor_id=array();
	$exist_url=array();
	$exist_short_url=array();
	$exist_url_id=array();
	$time = date('Y-m-d H:i:s');
	
	$sql_show_all_sensor="select * from sensor";
	$result_show_all_sensor = mysql_query($sql_show_all_sensor);
	while ($row_show_all_sensor=mysql_fetch_array($result_show_all_sensor)) {
		// 		echo $row_show_all_sensor["sensor"].'<br>';
		array_push($exist_sensor_id, $row_show_all_sensor["id_sensor"]);
		array_push($exist_sensor, $row_show_all_sensor["sensor"]);
	}
	
	$sql_show_all_url="select * from url";
	$result_show_all_url = mysql_query($sql_show_all_url);
	while ($row_show_all_url=mysql_fetch_array($result_show_all_url)) {
		// 		echo $row_show_all_url["short_url"].'&nbsp&nbsp&nbsp';
		// 		echo $row_show_all_url["url"].'<br>';
		array_push($exist_url_id, $row_show_all_url["id_url"]);
		array_push($exist_url, $row_show_all_url["url"]);
		array_push($exist_short_url, $row_show_all_url["short_url"]);
	}
	for ($i=0;$i<count($exist_short_url);$i++)
	{
		for ($j=0;$j<count($exist_sensor);$j++)
		{
// 			echo $userlist[$i+1]."+".$urllist[$j]."=".$result_insert_sensor."------".$result_insert_url.'<br>';
			if(isset($_POST[$i.$j]))
			{
// 				echo $i.$j.'<br>';
				$insert_relation_sql="insert into sensor_url_relation(sensor,url,selected,time) values('".$exist_sensor_id[$j]."','".$exist_url_id[$i]."',1,'".$time."')";
				$result_insert_relation=mysql_query($insert_relation_sql,$link_id);
// 				echo "relation".$i.$j."=".$result_insert_relation.'<br>';
			}
			else 
			{
				$insert_relation_sql="insert into sensor_url_relation(sensor,url,selected,time) values('".$exist_sensor_id[$j]."','".$exist_url_id[$i]."',0,'".$time."')";
				$result_insert_relation=mysql_query($insert_relation_sql,$link_id);
// 				echo "relation".$i.$j."=".$result_insert_relation.'<br>';
			}
		}
	}
	if($result_insert_relation)
	{
		echo "向数据库写入成功！";
	}
	else {
		echo "向数据库写入失败！";
	}
	mysql_close($link_id);
	session_destroy();
}
else 
	header("Location: login.php");
?>

</body>
</html>
