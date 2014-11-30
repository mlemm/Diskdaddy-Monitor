<?php

/*
	Switch plugin to use cURL to send data instead of ftp since alot of servers limit or disallow ftp send requests
*/

//get the post request and create a file called testing.dd to display the data
	//add a check key to make sure that the data is not an attack

	
	$checkString = "BqXfd6moMeDiskDaddypVDJzo7k7X";
	$theOutputString = "";
	$theFilename = "testing.dd"; //incase someone forgets to include the filename

	$securityCheck = false;

	foreach ($_POST as $key => $value) {
	 	$theOutputString .= $key . "|" . $value . "*";
	 	if($key == "file_name") $theFilename = $value;
	 	if($key == "disk_daddy_key" && $value == $checkString) $securityCheck = true;

	}

	//trim off the last *
	$theOutputString = rtrim($theOutputString, "*");

	if($securityCheck == false) {
		//someone as tried to access this to upload a bad file
		//send an email
		mail("info@diskdaddy.com", "Illegal data upload attempt on Diskdaddy Monitor", "Someone did not provide the correct check string to upload data to the Monitor! - POST Data :" . $_POST);
		exit(-1);
	}


	$fileHandle = fopen($theFilename, "wb");

	fwrite($fileHandle, $theOutputString);

	fclose($fileHandle);

?>