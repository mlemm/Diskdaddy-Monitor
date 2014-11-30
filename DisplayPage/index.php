
<!DOCTYPE html>
<html>
<head><title>Diskdaddy.com Web Monitor</title></head>
<body><H3>Disk Daddy Web Monitor</H3><p>

<?php

/*
	Markus Lemm - 260444 - diskdaddy.com - Nov 2, 2014

*/
/*
	load the .dd files from each individual page

	------------------

	lables list

	website URL 		  				:site_address
	last user to login in 				:last_user_login
	last user to login in (date) 		:last_user_login_time
	date page/post created/updated 		:last_post_update
	date page/post created/updated time :last_post_update_time
	wordpress version 					:wordpress_version
	plugins that need updating			:plugins_to_update
	Support Contract Expiry Date		:support_contract_expiry
	Last time this file was uploaded	:last_ftp_upload_time
*/

date_default_timezone_set('America/New_York');

//get a list of all the files
$AllDDfiles = scandir(getcwd());

//loop thru all the files and output the data
foreach ($AllDDfiles as $aDDFile) {

	echo "\n";

	//get only the .dd files and don't display the default.dd file
	if(strpos($aDDFile, '.dd') !== FALSE && strcmp($aDDFile, 'default.dd') != 0) {

		//get file contents
		$DDFileContents = file_get_contents($aDDFile);

		//blank the data array
		$allTheData = [];

		//explode info
		$exDDFileContents = explode('*', $DDFileContents);

		//explode and key value pairs and place into an array
		foreach ($exDDFileContents as $aDataPair) {		
			list($DDLabel, $DDData) = explode('|', $aDataPair, 2);
			$allTheData[$DDLabel] = $DDData;
		}

		//now the $allTheData contains the key/value pairs of the data
		//make it look pretty Jon


		$theCurrentdate = time();
		if( array_key_exists('site_address', $allTheData)) 	echo '<p><a href="'. "http://" . $allTheData['site_address'] . '" target="_blank">' . 	$allTheData['site_address'] . '</a></p>';

		
		if( array_key_exists('last_ftp_upload_time', $allTheData)) {
			//let pre determine what we do
			$theValue = floor((time() - intval($allTheData['last_ftp_upload_time'])) / 3600); //hours ago
			if($theValue > 48) echo '<p style="background-color:#ff0000">'; else echo '<p  style="background-color:#00ff00">';
			echo 'Last update was ' . date("Y-m-d H:i:s", $allTheData['last_ftp_upload_time']) . ' - ' . 
			$theValue  . ' Hours ago </p>';
		}

		if( array_key_exists('support_contract_expiry', $allTheData)) {
			//determine how long until the contract expires - if less than 30 days then yellow - if expired then red
			//if less than 60 days then yellow if less than 14 then red
			$theValue =  DateTime::createFromFormat('Y-m-d', $allTheData['support_contract_expiry'])->format('U') - time();
			if($theValue > 5184000) {
				echo '<p style="background-color:#00ff00">';
			} else {
				if($theValue > 1209600) {
					echo '<p style="background-color:#ffff00">';
				} else {
					echo '<p style="background-color:#ff0000">';
				}
			}
			echo 'Support agreement ends ' .  $allTheData['support_contract_expiry'] . '</p>';
		}	
		if( array_key_exists('last_user_login', $allTheData)) 	echo 'Last login was by : ' . $allTheData['last_user_login'] .'<br />';
		if( array_key_exists('last_user_login_time', $allTheData)) 	echo 'Last login was : ' . 	date("Y-m-d H:i:s", $allTheData['last_user_login_time']) . '   ' . date('d', $theCurrentdate - intval($allTheData['last_user_login_time'])) .' - Days ago<br />';

		if( array_key_exists('last_post_update_time', $allTheData)  && intval($allTheData['last_post_update_time']) > 200) {
			$theValue = floor((time() - intval($allTheData['last_post_update_time'])) / 86400); //days since last update
			if($theValue < 14) {
				echo '<p style="background-color:#00ff00">';
			} else {
				if($theValue < 28) {
					echo '<p style="background-color:#ffff00">';
				} else {
					echo '<p style="background-color:#ff0000">';
				}
			}
			echo 'Last page/post creation/update was  : ' . $theValue . ' days ago</p>';
		}
		if( array_key_exists('last_post_update', $allTheData) && $allTheData['last_post_update'] != 'NULL') echo 'Last page/post link : <a href="' . "http://" . $allTheData['last_post_update'] . '" target="_blank">' . $allTheData['last_post_update'] . '</a><br />';

		if( array_key_exists('wordpress_version', $allTheData)) 	echo 'Wordpress Version is : ' . 				$allTheData['wordpress_version'] .'<br />';

		if( array_key_exists('plugins_to_update', $allTheData))	{
			if(intval($allTheData['plugins_to_update']) > 0) { 
				if(intval($allTheData['plugins_to_update']) > 5) {
					echo '<p style="background-color:#ff0000">';
				} else {
					echo '<p style="background-color:#ffff00">';
				}				
			} else {
				echo '<p style="background-color:#00ff00">';
			}
			echo 'How many plugins need an update : ' . $allTheData['plugins_to_update'] . '</p><br /><p><hr>';

		} 
	}
}


?>

</body></html>
