<?php 
require_once('sys_conf.inc.php');
session_start();
if (isset($_SESSION['admin'])) {

?>
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body bgcolor="#E7F3FF"
  leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"
  text="#003374" link="#000000" vlink="#000000" alink="#000000" scroll="no" style="overflow-y:hidden">
<?php

$start_time=time();
$time="0";
$username="student";
$url=$_GET["url"];
$url_seq=0
?>
<?php
    if($url_seq < 1){
      //  echo 'username : '.$username.'&nbsp;';
      //  echo 'loading : "'.$url.'"';
        echo '<form action="student_speed_for_admin.php" name=WebtimerForm target=_top method=post>';
        echo '<br><font color="#ff0000">正在测速中，请勿关闭窗口!!! 测速计时<input type=button value="" name=WebtimerButton2>秒。</font><br><br>';
        echo '<INPUT TYPE=hidden NAME=url VALUE="'.$url.'">';
        echo '<INPUT TYPE=hidden NAME=time VALUE="'.$time.'">';
        echo '<INPUT TYPE=hidden NAME=start_time VALUE="'.$start_time.'">';
        echo '<INPUT TYPE=hidden NAME=username VALUE="'.$username.'">';
        echo '<b><input type=submit value="" name=WebtimerButton></b>';
        echo '</form>';
    }
    else {
        echo '<script language="JavaScript"> window.top.location.href="'.$end_page.'?username='.$username.'" </script>';
}

?> 


<script language="JavaScript">
<!--

function TimeStr(Delta, Long) {
  Mseconds = Delta % 1000;
  Delta = (Delta - Mseconds) / 1000;
  Seconds = Delta % 60;
  Delta = (Delta - Seconds) / 60;
  Minutes = Delta % 60;
  Delta = (Delta - Minutes) / 60;
  Hours = Delta % 60;
  Result = "";
  if (Hours <= 9) Result = "0";
  Result = Result + Hours + ":";
  if (Minutes <= 9) Result = Result + "0";
  Result = Result + Minutes + ":";
  if (Seconds <= 9) Result = Result + "0";
  Result = Result + Seconds;
  if (Long == "yes") Result = Result + "." + Mseconds;
  return(Result);
  }

function WebTimer() {

<?php
    echo 'timeout = '.$timeout.";\n";
    echo 'timeout_flag = "'.$timeout_flag.'"'.";\n";
?>
    CurrentTime = new Date();
    Delta = CurrentTime.getTime() - parent.start_time.getTime();
    if ( parent.finished == false && Delta < timeout  ) {
      document.WebtimerForm.WebtimerButton.value = TimeStr(Delta,"no") + ".000 " + Delta.toString() + "<" + timeout.toString();
      document.WebtimerForm.WebtimerButton2.value = TimeStr(Delta,"no");
      setTimeout('WebTimer()',1000);     }
    else if(parent.finished == true) {
      document.WebtimerForm.WebtimerButton.value = TimeStr(Delta,"yes");
      document.WebtimerForm.time.value = Delta/1000;
      document.WebtimerForm.WebtimerButton.click();      }
    else      { // delta >= time out
      document.WebtimerForm.WebtimerButton.value = timeout_flag;
      document.WebtimerForm.time.value = timeout_flag;
      document.WebtimerForm.WebtimerButton.click();
    }
}
onError = null;
WebTimer();

// -->
</script>

</body>
</html>


<?php 
}
else header("Location: ../speed201503/login.php");


?>