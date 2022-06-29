<?php
require_once 'sys_conf.inc.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>student_speed</title>
</head>
<body>
<?php
if (isset($_POST["student_user"])&&isset($_POST["student_tel"])&&isset($_POST["student_email"])&&isset($_POST["student_speed"])&&isset($_POST["student_ip"])&&isset($_POST["student_url"])) {
	$student_user=$_POST["student_user"];
	$student_tel=$_POST["student_tel"];
	$student_email=$_POST["student_email"];
	$student_time=$_POST["student_speed"];
	$student_ip=$_POST["student_ip"];
	$student_url=$_POST["student_url"];
	$now=date('Y-m-d H:i:s');
	
	$link_id=mysql_connect($DBHOST,$DBUSER,$DBPWD);
	mysql_select_db($DBNAME);
	mysql_query("set names UTF8");
	
	$str="INSERT INTO ".$student_info." (student_user,student_email,student_tel,student_url,student_speed,student_ip,time)
        VALUES ('".$student_user."','".$student_email."','". $student_tel ."','".$student_url."','".$student_time."','".$student_ip."','".$now."')";
	$result=mysql_query($str,$link_id);
	if ($result==1) {
// 		echo 'user='.$student_user.' student_email='.$student_email.' student_tel='.$student_tel.' student_time='.$student_time;
		echo '已经将您的信息提交，感谢您的配合。';
//  	header("Location: outliers.php?select_url=".$student_url."&student_speed=".$student_time);
		header("Refresh:1;url=student_speed_select_url.php");
	}
	else {
		echo '提交失败';
		header("Refresh:1;url=student_speed_select_url.php");
	}
	mysql_close($link_id);
}
else {
	header("Location: student_speed_select_url.php");
}
	




?></body></html>