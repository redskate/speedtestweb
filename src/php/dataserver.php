<?php

#####################################################################
# Data Server for visualisation of internet speed data
# on the basis of 
#
# Fabio Ricci semweb LLC,  http://semweb.ch, fabio.ricci@semweb.ch
# Date: 2015
# Assuption: The application folder (not src) is put at top level on a web virtual server
#
include_once 'utilities.php';

############
#
# This script uses a mysql table like:
#
# CREATE TABLE `log` (
# 		`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
# 		`distance` float DEFAULT NULL COMMENT 'km',
# 		`testfrom` text,
# 		`hostedby` text,
# 		`ping` float DEFAULT NULL COMMENT 'msec ping time',
# 		`downloadspeed` float DEFAULT NULL COMMENT 'in Mbit/s',
# 		`uploadspeed` float DEFAULT NULL COMMENT 'in Mbit/s',
# 		PRIMARY KEY (`timestamp`)
# ) ENGINE=InnoDB DEFAULT CHARSET=latin1
# 
############
#
# This script was installed in a virtual server of an apache server
# documentRoot=/path/to/

$last=$_REQUEST['last']; if ($last=='') $last=1000; // limit by default
$from=$_REQUEST['from'];
$to=$_REQUEST['to'];
$a=$_REQUEST['a'];
$reload=$_REQUEST['reload'];
$DELIMITER=';';
date_default_timezone_set('Europe/Zurich');

if ($a=='csv')
{
	$dbconn = check_create_conn();
	 
	$QUERY = "SELECT * FROM ISPEED.log ORDER BY timestamp DESC"; // attach WHERE (from, to)
	if ($last<>'')
		$QUERY.=" LIMIT $last";
	
	$resultset = mysqli_query($dbconn,$QUERY);
	
	$data="Time$DELIMITER Download_mbps$DELIMITER Upload_mbps\n";
	while(($row = mysqli_fetch_assoc($resultset)))
	{
		$timestamp =str_replace("-","/",$row['timestamp']);
		$downloadspeed =number_format($row['downloadspeed'], 2, '.', '');
		$uploadspeed =number_format($row['uploadspeed'], 2, '.', '');
		$data.="$timestamp$DELIMITER $downloadspeed$DELIMITER $uploadspeed\n";
	}
	header('Content-Type: text/csv; charset=utf-8');
	echo $data;
}
elseif (strstr(".".$a,'txt'))
	{
		$dbconn = check_create_conn();
	
		$QUERY = "SELECT * FROM ISPEED.log ORDER BY timestamp DESC"; // attach WHERE (from, to)
		if ($last<>'')
			$QUERY.=" LIMIT $last";
	  $i = 0;
		$resultset = mysqli_query($dbconn,$QUERY);
		//Only if exact txt
	  if ($a=='txt')
			$data="Sep='$DELIMITER'\n";
		$data.="   N$DELIMITER Timestamp$DELIMITER Download_mbps$DELIMITER Upload_mbps$DELIMITER Ping_ms$DELIMITER TestFrom$DELIMITER Hostedby$DELIMITER Distance_km\n";
		while(($row = mysqli_fetch_assoc($resultset)))
		{
			$i++;
			$num = sprintf("%4d", $i);
			$timestamp =str_replace("-","/",$row['timestamp']);
			$distance =$row['distance'];
			$testfrom =$row['testfrom'];
			$hostedby =$row['hostedby'];
			$ping = number_format($row['ping'], 2, '.', '');
			$downloadspeed =number_format($row['downloadspeed'], 2, '.', '');
			$uploadspeed =number_format($row['uploadspeed'], 2, '.', '');
			$data.="$num$DELIMITER $timestamp$DELIMITER $downloadspeed$DELIMITER $uploadspeed$DELIMITER $ping$DELIMITER $testfrom$DELIMITER $hostedby$DELIMITER $distance \n";
		}
		header('Content-Type: text/plain; charset=utf-8');
		echo $data;
	}
else // show it
{
	$IP = $_SERVER['SERVER_NAME'];
  $urlcsv=$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['HTTP_HOST']."/src/php/dataserver.php?a=csv&from=$from&to=$to&last=$last";
	$urltxt=$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['HTTP_HOST']."/src/php/dataserver.php?a=txt&from=$from&to=$to&last=$last";
	$urltxtiframe=$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['HTTP_HOST']."/src/php/dataserver.php?a=txtiframe&from=$from&to=$to&last=$last";
	$url=$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['HTTP_HOST']."/src/php/dataserver.php?from=$from&to=&last=$last";
	?>
<html>
<head>
  <title>Internet <?php echo $IP; ?></title>
  <meta charset="UTF-8">
  <?php if ($reload) { ?><meta http-equiv="refresh" content="<?php echo $reload;?>"> <?php }?>
  <script src="/vendors/dygraphs/dygraph-combined.js"></script>
</head>
<body style="font-size: 14px;">
<table id="semweblogo" width="100%">
<tr>
  <td valign="TOP" align="LEFT">
    <a style="margin-left: 30px;" href="http://semweb.ch" title="Visit us" target="_blank"><img src="/images/logo_semweb.jpg" height="70"></a>
  </td>
</tr>
</table>  
<div style='margin-left: 37px;margin-top: 25px; margin-bottom:20px;'>
	<h3 ><?php echo date("d.m.Y H:i:s")." Measurements Internet Speeds on $IP"; ?></h3>
<table style="white-space: nowrap">
 <tr>
  <td>
  	<a href="<?php echo $urltxt; ?>" title="Click to download in a new tab visualized data series more on detail" 
  		 target="_blank"
  		 >Download detailed data series</a> 
  	<?php if ($last<>'') { ?> (<?php echo $last;?>) <?php } ?></td>
  <td>
  <td>
		<button style="margin-left: 120px; background-color:#E76600;color:white;font-size: 14px;"
						onclick="window.open('<?php echo $url;?>&last='+last.value,',_self');"			
			>(Re)Visualize</button>
		<span> last </span>
		<input onkeyup="if(event.keyCode==13) {window.open(URL_set_parameter('<?php echo $url;?>','last',this.value),'_self');}" 
					 style="width: 100px;text-align:center" 
					 type="text"
					 value="<?php echo $last;?>"
					 name="last" 
					 id="last" 
					 title="Type a number to see only the last data items and press ENTER or the Visualize button.">
		<span> data points</span>
	</td>
 </tr>
</table>
</div>
<div id="graphdiv4" style="width:700px; height:400px;float: left;"></div>
<iframe title="Detailed visualized data series" width="700" height="400" frameBorder="0" src="<?php echo $urltxtiframe; ?>" style='float: right;'></iframe>
<script type="text/javascript">
  g4 = new Dygraph(
    document.getElementById("graphdiv4"),
    "<?php echo $urlcsv; ?>",
    {
    	delimiter: '<?php echo $DELIMITER;?>',
    	strokeWidth: 3,
    }
  );
  function URL_set_parameter(url, param, value){
	    var hash       = {};
	    var parser     = document.createElement('a');

	    parser.href    = url;

	    var parameters = parser.search.split(/\?|&/);

	    for(var i=0; i < parameters.length; i++) {
	        if(!parameters[i])
	            continue;

	        var ary      = parameters[i].split('=');
	        hash[ary[0]] = ary[1];
	    }

	    hash[param] = value;

	    var list = [];  
	    Object.keys(hash).forEach(function (key) {
	        list.push(key + '=' + hash[key]);
	    });

	    parser.search = '?' + list.join('&');
	    return parser.href;
	}
</script>
</body>
</html>
<?php }?>