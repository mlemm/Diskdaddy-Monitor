<?php  
    /* 
    Plugin Name: Diskdaddy.com Web Monitor Plugin
    Plugin URI: http://www.diskdaddy.com 
    Version: 1.6.0
    Author: Markus Lemm
    Description: Monitor page for last user login, page/post updates, unique ip counter, wordpress version, updates, support expiry date - October 22, 2014
    Author URI: http://www.markuslemm.com
    */



/*

	Changes here
	Upload data to main server (diskdaddy.com)
	

	Options page (@diskdaddy.com / regular user)
		-support expiry date
		-ftp server to send data to including username/password
		-output basic data info (last user to login/ last post/page update, counter per day/48hours/7days, wordpress version )

	Error checking!!

	- plugin updates
	- variable names and name spaces - wp_diskdaddy_plugin_(variable)


*/

/*
	To do:
		- ftp upload
		- wordpress cron
		



*/

		/* CRON */

	function Disk_Daddy_Plugin_upload() {
		//ftp the info
		//mail('mlemm@diskdaddy.com', 'Disk Daddy Web Monitor Cron', 'Cron job on ' . home_url() . ' fired'); 
		echo 'uploaded';
	}


		/* FUNCTIONS */


	function Disk_Daddy_Update_Post($post_id) {

		//get access to database
		global $wpdb;

		$current_time = time();
        $sql = "UPDATE wp_diskdaddy_plugin_variables SET the_value = '$current_time' WHERE the_key = 'last_post_update_time';";
        $result = $wpdb->query($sql);
       
        	   	
       	$actual_post_url = get_permalink($post_id);
        $sql = "UPDATE wp_diskdaddy_plugin_variables SET the_value = '$actual_post_url' WHERE the_key = 'last_post_update';";
      	$result = $wpdb->query($sql);
 
    }

	function Disk_Daddy_Update_Login($user_login, $user) {

		//get access to database
		global $wpdb;

		//update the user and time

		//LAST USER LOGIN TIME
        $current_time = time();
        $sql = "UPDATE wp_diskdaddy_plugin_variables SET the_value = '$current_time' WHERE the_key = 'last_user_login_time';";
        $result = $wpdb->query($sql);


        //LAST USER LOGIN             	
        $sql = "UPDATE wp_diskdaddy_plugin_variables SET the_value = '$user_login' WHERE the_key = 'last_user_login';";
        $result = $wpdb->query($sql);
      
	}

	//create the table and add all the variables (if they don't already exist)
	function Disk_Daddy_InitThePlugin() {

		//get access to database
		global $wpdb;


		//create the table (if not already exists)
        $sql = "CREATE TABLE IF NOT EXISTS wp_diskdaddy_plugin_variables(the_key CHAR(100), the_value CHAR(100));";
        $result = $wpdb->query($sql);

        //fill in variables

        //LAST POST UPDATE TIME
        $sql = "SELECT * FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_post_update_time';";
        $result = $wpdb->query($sql);
        if( !$result) {
        	//variable does not exist        	
        	$sql = "INSERT INTO wp_diskdaddy_plugin_variables (the_key, the_value) VALUES ('last_post_update_time', '0');";
        	$result = $wpdb->query($sql);
        }

        //LAST POST UPDATE
        $sql = "SELECT * FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_post_update';";
        $result = $wpdb->query($sql);
        if( !$result) {
        	//variable does not exist        	
        	$sql = "INSERT INTO wp_diskdaddy_plugin_variables (the_key, the_value) VALUES ('last_post_update', 'NULL');";
        	$result = $wpdb->query($sql);
        }


        //LAST USER LOGIN TIME
        $sql = "SELECT * FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_user_login_time';";
        $result = $wpdb->query($sql);
        $current_time = time();
        if( !$result) {
        	//variable does not exist        	
        	$sql = "INSERT INTO wp_diskdaddy_plugin_variables (the_key, the_value) VALUES ('last_user_login_time', '$current_time');";
        	$result = $wpdb->query($sql);
        }


        //LAST USER LOGIN
        $sql = "SELECT * FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_user_login';";
        $result = $wpdb->query($sql);
        $current_logged_in = wp_get_current_user()->user_login;
        if( !$result) {
        	//variable does not exist        	
        	$sql = "INSERT INTO wp_diskdaddy_plugin_variables (the_key, the_value) VALUES ('last_user_login', '$current_logged_in');";
        	$result = $wpdb->query($sql);
        }

        //SITE ADDRESS (URL)
        $sql = "SELECT * FROM wp_diskdaddy_plugin_variables WHERE the_key = 'site_address';";
        $result = $wpdb->query($sql);
        $the_site_urls = get_site_url();
        if( !$result) {
        	//variable does not exist        	
        	$sql = "INSERT INTO wp_diskdaddy_plugin_variables (the_key, the_value) VALUES ('site_address', '$the_site_urls');";
        	$result = $wpdb->query($sql);
        } else {
        	//variable exist so update it
        	$sql = "UPDATE wp_diskdaddy_plugin_variables SET the_value = '$the_site_urls' WHERE the_key = 'site_address';";
        	$result = $wpdb->query($sql);
        }

        //WORDPRESS VERSION
        $sql = "SELECT * FROM wp_diskdaddy_plugin_variables WHERE the_key = 'wordpress_version';";
        $result = $wpdb->query($sql);
        $current_version = get_bloginfo('version');
        if( !$result) {
        	//variable does not exist        	
        	$sql = "INSERT INTO wp_diskdaddy_plugin_variables (the_key, the_value) VALUES ('wordpress_version', '$current_version');";
        	$result = $wpdb->query($sql);
        } else {
        	//variable exist so update it
        	$sql = "UPDATE wp_diskdaddy_plugin_variables SET the_value = '$current_version' WHERE the_key = 'wordpress_version';";
        	$result = $wpdb->query($sql);

        }


        //activate cron
        //check if already activated
        $timestamp = wp_next_scheduled( 'Disk_Daddy_Plugin_upload_hook' );

  		if( $timestamp == false ){
    		//Schedule the event for right now, then to repeat daily
   		 	wp_schedule_event( time(), 'daily', 'Disk_Daddy_Plugin_upload_hook' );
   		}
   		
   		
  }




	function Disk_Daddy_UninitThePlugin() {

		//display a message saying something

		//deactivte cron
		wp_clear_scheduled_hook('Disk_Daddy_Plugin_upload_hook');


	}

	//used to remove any footprint of the plugin on the database
	function Disk_Daddy_removePluginTables() {

		global $wpdb;

		//remove tables
		$sql = "DROP TABLE IF EXISTS wp_diskdaddy_plugin_variables;";
        $result = $wpdb->query($sql);

        echo "<p>All Database tables have been removed";

	}

		/* OPTIONS PAGE */


	function Disk_Daddy_callOptionsPage() {
      
      	 
      	echo '<div class="wrap">';
      	echo 'HELLO - add options stuff here<p>Support Expiry Date<p>Ftp Login username/password<p>@diskdaddy.com only options<p>list of info<p>table erase button';
		echo '</div>';
		
	
    }


	function Disk_Daddy_Plugin_Menu() {
    
      add_options_page('Diskdaddy Plugin Options Menu','Diskdaddy Plugin','manage_options', 'Disk_Daddy_Plugin', 'Disk_Daddy_callOptionsPage');

    }


    


		/*  HOOKS   */

	//register the activation of the plugin   
    register_activation_hook( __FILE__, 'Disk_Daddy_InitThePlugin' );
    register_deactivation_hook( __FILE__, 'Disk_Daddy_UninitThePlugin' );

    //user logged in
    add_action('wp_login', 		'Disk_Daddy_Update_Login', 10, 2);

    //update last page or post modded
    add_action('publish_page', 	'Disk_Daddy_Update_Post', 10, 1);
	add_action('post_updated', 	'Disk_Daddy_Update_Post', 10, 1);
	add_action('edit_post', 	'Disk_Daddy_Update_Post', 10, 1);

	//add the options page
	add_action('admin_menu', 'Disk_Daddy_Plugin_Menu');

	//cron hook event
	add_action('Disk_Daddy_Plugin_upload_hook', 'Disk_Daddy_Plugin_upload');

?>
