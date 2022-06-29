<?php
if (isset ( $_GET ['select_url'] )&&isset ( $_GET ['student_speed'] )) {
		$select_url = $_GET ['select_url'];
		$student_time=$_GET['student_speed'];
		
		require_once ('sys_conf.inc.php');
		
		$time_list = array ();
		$count_list = array ();
		$percent = array();
		
		for($i = 0; $i <= $step_num; $i ++) {
			$count_list [$i] = 0;
		}
		// 得到数据
		$link_id = mysql_connect ( $DBHOST, $DBUSER, $DBPWD );
		mysql_select_db ( $DBNAME );
		mysql_query ( "set names UTF8" );
		
		$sql_url = "select * from url where id_url='" . $select_url . "'";
		$result_url = mysql_query ( $sql_url );
		while ( $row_url = mysql_fetch_array ( $result_url ) ) {
			$url_id = $row_url ["id_url"];
			$url_short = $row_url["short_url"];
			// echo 'url_id = '.$url_id.'<br>';
		}
		
		$sql_url_recent = "select * from " . $speedlog_table . " where url=" . $url_id;
		$result_url_recent = mysql_query ( $sql_url_recent );
		while ( $row_url_recent = mysql_fetch_array ( $result_url_recent ) ) {
			
			if ($row_url_recent ["time"] < 0 || $row_url_recent ["time"] >= 30) {
				continue;
			} else {
				// echo $row_url_recent["time"];
				array_push ( $time_list, $row_url_recent ["time"] );
				// echo 'time_list['.$i.'] = '.$row_url_recent["time"].'<br>';
			}
		}
		
		// 求平均值
		$sum = 0;
		for($i = 0; $i < count ( $time_list ); $i ++) {
			$sum = $sum + $time_list [$i];
		}
		$mu = $sum / count ( $time_list );
		
		// 求方差
		$sigma = 0;
		$sum0 = 0;
		for($i = 0; $i < count ( $time_list ); $i ++) {
			$sum0 = $sum0 + ($time_list [$i] - $mu) * ($time_list [$i] - $mu);
		}
		$sigma = sqrt ( $sum0 / count ( $time_list ) );
		// 置信区间上下界
		$lower = $mu - $sigma * 1.96 / sqrt ( count ( $time_list ) );
		$heigher = $mu + $sigma * 1.96 / sqrt ( count ( $time_list ) );
		
// 		echo 'mu = ' . $mu . '<br>';
// 		echo 'sigma = ' . $sigma . '<br>';
// 		echo 'lower = ' . $lower . '<br>';
// 		echo 'heigher = ' . $heigher . '<br>';
		
		// 最大值，最小值
		$time_min = min ( $time_list );
		$time_max = max ( $time_list );
		
		// 将数据分成1000
		$step = ($time_max - $time_min) / $step_num;
		
		// 每一段的概率密度
		$i = 0;
		while ( $i < count ( $time_list ) ) {
			
			$temp = ($time_list [$i] - $time_min) / $step;
			$index = floor ( $temp ); // 向下取整
			$count_list [$index] ++;
			$i ++;
		}
		$j=0;
// 		$s=0;
		while ($j<count($count_list)){
			$percent[$j] = 100*$count_list[$j]/count($time_list);
// 			echo $percent[$j].'<br>';
// 			$s=$s+$percent[$j];
			$j++;
		}
// 		echo 'sum = '.$s;
		
		?>


<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
	src="js/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.cursor.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.highlighter.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/plugins/jqplot.barRenderer.min.js"></script>

<title>outlier</title>
</head>
<body>
	<center>
		<br> <br>

		<!-- 数据图 -->
		<script type="text/javascript">

$(document).ready(function(){
<?php
		echo 'var area=[];';
		//echo 'var outlier=[];';
		echo 'var student=[];';
		for($i = 0; $i <= $step_num; $i ++) {
			$x = $time_min + $i * $step;
// 			echo 'area.push(["' . $x . '","' . $count_list [$i] . '"]);';
			echo 'area.push(["' . $x . '","' . $percent [$i] . '"]);';
// 			if ($x >= ($lower-$step) && $x <= ($heigher+$step)) {
// // 				echo 'outlier.push(["' . $x . '","' . $count_list [$i] . '"]);';
// 				echo 'outlier.push(["' . $x . '","' . $percent [$i] . '"]);';
// 			}
			if ($student_time>=$x&&$student_time<($x+$step)) {
// 				echo 'student.push(["' . $x . '","' . $count_list [$i] . '"]);';
				echo 'student.push(["' . $x . '","0"]);';
				echo 'student.push(["' . $x . '","' . (max($percent)+1) . '"]);';
			}
		}
		
		?>
	var plot3 = $.jqplot('chart3', [area,student],
	{ 
		title:'频数-时间', 
		// Series options are specified as an array of objects, one object
		// for each series.
		//renderer:$.jqplot.BarRenderer,
		series:[{renderer:$.jqplot.BarRenderer,label: '全校测速点测试<?php echo $url_short;?>速度落在不同时间段的频数占所有测速点频数的百分比'},{showMarker:false,label: '您的速度'}],
		 seriesDefaults: {
		        rendererOptions: {
		            barWidth: 8     // 设置柱状图中每个柱状条的宽度
		        }
		    },
		axesDefaults: {
	        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
	        tickOptions: {
	          angle: -30,
	          fontSize: '10pt'
	        }
	    },
	    axes: {
	      xaxis: {
	    	label: "耗时(秒)",
	    	min: 0,      // 横（纵）坐标显示的最小值 
			max: <?php echo $time_max;?>
	      },
	      yaxis: {
			label: "频数",
	 		min: 0,      // 横（纵）坐标显示的最小值  
			max: <?php echo max($percent)+1;?> 
			
	      }
	    },
	   cursor:{ 
			show: true,
			zoom:true, 
			showTooltip:true
		},
		legend: {
			show: true,//设置是否出现分类名称框(即所有分类的名称出现在图的某个位置)
			location: 'ne', //分类名称框出现位置, nw, n, ne, e, se, s, sw, w.
			xoffset: 12, //分类名称框距图表区域上边框的距离(单位px)
			yoffset: 12, //分类名称框距图表区域左边框的距离(单位px)
			background:'', //分类名称框距图表区域背景色
			textColor:'' //分类名称框距图表区域内字体颜色
		}
	}
	);

});
</script>


		<div id="chart3" style="position: relative; height: 80%; width: 80%;"></div>

	</center>
</body>
</html>
<?php
} else {
	echo '数据对比失败！';
}

?>