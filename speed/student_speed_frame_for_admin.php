<?php
require_once('sys_conf.inc.php');
session_start();
if (isset($_SESSION['admin'])) {
if (isset ( $_GET ['select_url'] )) {
	$select_url = $_GET ['select_url'];
	if ($select_url == "-----请选择-----") {
		header ( "Location: select_url_for_admin.php" );
	} else {
		require_once ('sys_conf.inc.php');
		?>
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">


<script type="text/javascript" language="javascript"> 

function StopTimer() {
  finished = true;
  }

start_time = new Date();
finished = false;
</script>

<!-- The html for other browsers. -->
<noscript>
</head>
<body>
  <center>Sorry, the WebTimer only works if your browser supports frames and javascript.
</body>
</noscript>
<?php

		
		
		$link_id = mysql_connect ( $DBHOST, $DBUSER, $DBPWD );
		mysql_select_db ( $DBNAME );
		mysql_query ( "set names UTF8" );
		
		$sql_url = "select * from url";
		$result_url = mysql_query ( $sql_url );
		while ( $row_url = mysql_fetch_array ( $result_url ) ) {
			if ($row_url ["id_url"] == $select_url) {
				$url = $row_url ["url"];
			}
		}
		
		echo '<frameset rows="40,*" border=4 frameborder=yes framespacing=0 onload="setTimeout(StopTimer(), 0)">';
		echo '  <frame noresize="noresize" src="student_speed_counter_for_admin.php?url=' . $url . '&time=' . $time . '" name="TimeFrame" scrolling=auto marginwidth=0 marginheight=0 >';
		echo '  <frame noresize="noresize" src="' . $url . '" name="PageFrame" scrolling=auto>';
		echo '</frameset>';
	}
} else {
	header ( "Location: select_url_for_admin.php" );
}
}
else header("Location: ../speed201503/login.php");

?>