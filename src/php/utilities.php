<?php

// UTILITIES
$DB_HOST='127.0.0.1:3307';

function check_create_conn()
{
	$DEBUG=0;
	global $dbconn;

	if (!$dbconn)
	{
		global $DB_HOST;
		$DB_UNAME='...'; // <<- your mysql user name here
		$DB_PWORD='...'; // <<- its password
		$DB_DB= 'ISPEED';

		$dbconn = mysqli_connect($DB_HOST,$DB_UNAME,$DB_PWORD,$DB_DB) or $errors = $errors . "Could not connect to database.\n";
	}
	return $dbconn;
}


?>