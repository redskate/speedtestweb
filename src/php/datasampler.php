<?php

	$loopinterval_sec=200; // wait 3 minutes then retest

	error_reporting(0);
	
	include_once 'utilities.php';
	print "Starting internet speed datasampler with loopinterval=$loopinterval_sec sec\n";
	while(true)
	{
	  $data = measure_speeds();
	   
	  //parse speeds, open db and write record:
	  parse_and_store_data($data);
	  print ".";
	  sleep($loopinterval_sec);
	} 
    
  
	function measure_speeds()
  {
  	 $cwd=getcwd();
  	 $logfilename = $cwd."/locallog.txt";
  	 $COMMAND="$cwd/../../vendors/speedtest-cli/speedtest_cli.py > $logfilename";
  	 system($COMMAND);
  	 return file_get_contents($logfilename);
  }

    
  function parse_and_store_data($data)
  {
  	//TESTFROM
  	if (preg_match("/Testing\sfrom\s(.*)\.\.\./",$data,$match))
  	{
  		$testfrom=$match[1];
  	}
  	
  	//HOSTEDBY DISTANCE PING
  	if (preg_match("/Hosted\sby\s(.*)\)\s\W(.*)\skm\W\W\s(.*)\sms/",$data,$match))
  	{
  		$hostedby=$match[1].")";
  		$distance_km=$match[2];
  		$ping_ms=$match[3];
  	}
  	
  	//DOWNLOAD SPEED
  	if (preg_match("/Download\W\s(.*)\sMbit/",$data,$match))
  	{
  		$download_mbitxs=$match[1];
  	}
  	 
  	//UPLOAD SPEED
  	if (preg_match("/Upload\W\s(.*)\sMbit/",$data,$match))
  	{
  		$upload_mbitxs=$match[1];
  	}
  	
  	if ($DEBUG)
  	{
	  	print "testfrom: $testfrom";
	  	print "\nhostedby: $hostedby";
	  	print "\ndistance_km: $distance_km";
	  	print "\nping_ms: $ping_ms";
	  	print "\ndownload_mbitxs: $download_mbitxs";
	  	print "\nupload_mbitxs: $upload_mbitxs";
  		print "\n";
  	}
  	
  	// STORE IN DB
  	$dbconn = check_create_conn();
  	
  	$QUERY = "INSERT INTO log(timestamp,hostedby,testfrom,distance,ping,downloadspeed,uploadspeed) "
  					."VALUES(NOW(),'$hostedby','$testfrom',$distance_km,$ping_ms,$download_mbitxs,$upload_mbitxs)";
  	
  	$qresultDB_INSERT = mysqli_query($dbconn,$QUERY);
  	 
  }
?>