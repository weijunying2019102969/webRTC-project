
	


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!--[if IE7]><script language="javascript" type="text/javascript" src="js/excanvas.js"></script><![endif]-->
<link rel="stylesheet" type="text/css" href="js/jquery.jqplot.css" />
<script language="javascript" type="text/javascript"
	src="js/jquery.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/excanvas.js"></script>
<script language="javascript" type="text/javascript"
	src="js/jquery.jqplot.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.cursor.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.highlighter.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.barRenderer.min.js"></script>



<link rel="stylesheet" type="text/css" href="bootstrap-combined.min.css">

<link rel="stylesheet" type="text/css" href="index.css">

<style type="text/css">
input.select{ vertical-align:middle};
font{ size:14pt};
</style>


<title>student_speed</title>
</head>
<body>




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
// echo 'urllist size = '.count($urlidlist).'<br>';
// var_dump($urllist);
// var_dump($urlnamelist);
// $url_combine = array_combine($urllist,$urlnamelist);
// var_dump($url_combine);
//modify end
?>


<div style="background-color:#f2f2f2;height:100px; vertical-align:middle">
<table width="60%" align="center" height="100"><tr><td width="330px" align="right" valign="middle"> 
 <img src="logof2.jpg" width="304" height="76"><img src="v1.gif" width="25" height="43"></td>
<td width="150px" align="right" style="padding-top:18px ">
<select name="url_name" id="select_url_name">
<?php
echo '<option style="font-size:14px;max-width:200px;" value="select">选择测速的目标网站</option>';
for ($i=0;$i<count($urlidlist);$i++)
{
        echo '<option style="font-size:14px;height:30px;max-width:200px;" value='.$urlidlist[$i].'>'.$urlkeylist[$i].'</option>';
}
?>
</select></td><td align="left" width="60px" style="padding-top:8px;" >
			<input value="我要测速" class="btn btn-fl" type="submit" onclick="geturl()" >
         </td></tr></table>
<script type="text/javascript">
function geturl()
{
        var url=document.getElementById("select_url_name").value;
        //alert(url);
        if(url!=""){
                var nextpage = "student_speed_frame.php?select_url="+url;
                window.location.href=nextpage;
                document.form.submit();
        }
        else{
                alert("请选择要测速的网址");
        }
}
</script>

</div>
        </div>


        </div>
</div>




</body>
</html>

