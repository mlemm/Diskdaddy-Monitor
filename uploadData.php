<?php

/*
	Switch plugin to use cURL to send data instead of ftp since alot of servers limit or disallow ftp send requests
*/

//get the post request and create a file called testing.dd to display the data

	$fileHandle = fopen("testing.dd", "wb");

	print_r($_POST);

	fwrite($fileHandle, "this is the post data\n");
	 foreach ($_POST as $key => $value) {
	 	fwrite($fileHandle, $key . " : " . $value . "\n");
	 }

	fclose($fileHandle);

?>