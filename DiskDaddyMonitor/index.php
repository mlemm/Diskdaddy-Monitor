<?php  
    /* 
    Plugin Name: Diskdaddy.com Web Monitor
    Plugin URI: http://www.diskdaddy.com 
    Version: 1.8.1
    Author: Markus Lemm
    Description: Monitor page for last user login, page/post updates, wordpress version, updates, support expiry date - cURL
    Author URI: http://www.markuslemm.com
    */

    /* 
        Markus Lemm - mlemm@diskdaddy.com - 260444
        Created November 3, 2014
    */

        /*
            Submit thru html get method - simple and fast
            cURL to make this work - add cURL test button
        */

        /* CRON */

    function Disk_Daddy_Plugin_upload() {

        //Now cURL

        //get access to database
        global $wpdb;


        $fileOutput = 'support_contract_expiry=' . get_option('Support_Contract_Expiry', '2020-12-31') . "&";

        $fileOutput .= 'file_name=' . get_option('FTPFileName', 'testing.dd') . "&";

        $result = time();
        $fileOutput .= 'last_ftp_upload_time=' . $result . "&";

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_post_update_time';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'last_post_update_time=' . $result . "&";
        

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_post_update';";
        $result = $wpdb->get_var($sql);
        
       // $fileOutput .= "last_post_update=". urlencode("http://markuslemm.com/wordpress-cron/") . "&";
     //   $fileOutput .= "last_post_update=http://markuslem.com/wordpress-cron/&";
        $fileOutput .= "last_post_update=" . preg_replace('#^https?://#','', $result) . "&";


        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_user_login_time';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'last_user_login_time=' . $result . "&";
        

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_user_login';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'last_user_login=' . $result . "&";


        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'site_address';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'site_address=' . preg_replace('#^https?://#','', $result) . "&";

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'wordpress_version';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'wordpress_version=' . $result . "&";

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'plugins_to_update';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'plugins_to_update=' . $result;

        //fileOutput now contains the get string

        //init curl

        $curlStuff = curl_init();
        //curl_setopt($curlStuff, CURLOPT_URL, "http://www.diskdaddy.com/Monitor/uploadData.php?" . $fileOutput);
        curl_setopt($curlStuff, CURLOPT_URL, "http://www.diskdaddy.com/Monitor/uploadData.php");
        curl_setopt($curlStuff, CURLOPT_POST, 1);
        curl_setopt($curlStuff, CURLOPT_POSTFIELDS , $fileOutput);
        curl_exec($curlStuff);
        curl_close($curlStuff);





/*
        //ftp the info
        
        //create temp file
        $fSetup = tmpfile();
        //put in the db info

        $fileOutput = 'support_contract_expiry|' . get_option('Support_Contract_Expiry', '2020-12-31') . '*';

        $result = time();
        $fileOutput .= 'last_ftp_upload_time|' . $result . '*';

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_post_update_time';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'last_post_update_time|' . $result . '*';
        

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_post_update';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'last_post_update|' . $result . '*';

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_user_login_time';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'last_user_login_time|' . $result . '*';
        

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'last_user_login';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'last_user_login|' . $result . '*';


        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'site_address';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'site_address|' . $result . '*';

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'wordpress_version';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'wordpress_version|' . $result . '*';

        $sql = "SELECT the_value FROM wp_diskdaddy_plugin_variables WHERE the_key = 'plugins_to_update';";
        $result = $wpdb->get_var($sql);
        
        $fileOutput .= 'plugins_to_update|' . $result;

        fwrite($fSetup, $fileOutput);

        fseek($fSetup,0);

        //upload to the ftp server
        $ftp_server = 'ftp.diskdaddy-development.com'; // Address of FTP server.
        $ftp_user_name = 'plugin'; // Username
        $ftp_user_pass = '.3$r{}1~yI'; // Password

        // set up basic connection
        //need to test if we can connect to the ftp server

        $conn_id = ftp_connect($ftp_server);
        if($conn_id == FALSE) {
            mail('mlemm@diskdaddy.com', 'Disk Daddy Web Monitor Error', 'Got an error on ftp_connect on site ' .  home_url());
        } else {
        
            // login with username and password
            $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

            if($login_result == FALSE) {
                mail('mlemm@diskdaddy.com', 'Disk Daddy Web Monitor Error', 'Got an error on ftp_login on site ' . home_url()); 
            } else {

                //upload the actual file
                $uploadFileName = get_option('FTPFileName','default.dd');
                $upload = ftp_fput($conn_id, $uploadFileName, $fSetup, FTP_BINARY);  // upload the file

                if($upload == FALSE) mail('mlemm@diskdaddy.com', 'Disk Daddy Web Monitor Error', 'Got an error on ftp_fput ' . home_url()); 
                 
                ftp_close($conn_id); // close the FTP stream   
                fclose($fSetup); //close the temp file
            }
        }

        */

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
        $the_site_urls = home_url();//get_site_url();
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

        //PLUGINS THAT NEED AN UPDATE
        $sql = "SELECT * FROM wp_diskdaddy_plugin_variables WHERE the_key = 'plugins_to_update';";
        $result = $wpdb->query($sql);
        if( !$result) {
            //variable does not exist           
            $sql = "INSERT INTO wp_diskdaddy_plugin_variables (the_key, the_value) VALUES ('plugins_to_update', '0');";
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

        //Disk_Daddy_removePluginTables();


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
      
         
        //get the email of the user that is currently logged in
        global $current_user;
        get_currentuserinfo();
        $emailOfCurrentUser = $current_user->user_email;

        //only display admin options if the user that is logged in is with diskdaddy.com
        if( strpos($emailOfCurrentUser, 'diskdaddy.com') !== FALSE) {

            echo '<div class="wrap">';
            echo '<h2>Please edit the info below - Diskdaddy Admin</h2>' ;
            echo '<form method="post" action="options.php">';
            settings_fields( 'Disk_Daddy_Plugin-Group' ); 
            do_settings_fields( 'Disk_Daddy_Plugin-Group', '' );
            echo '<p>Input filename to use when uploading data (somethingUnique.dd) - <input type="text" name="FTPFileName" value="' . get_option('FTPFileName', 'default.dd') .'" />';
            echo '<p>Input date that support agreement expires (1979-12-31) - <input type="date" name="Support_Contract_Expiry" value="' . get_option('Support_Contract_Expiry', '2020-12-31') .'" />';
            
            submit_button();
            
            if(function_exists('curl_version')) {
                echo '<h3 style="color:green"> cURL is enabled on this server</h3>';
            } else {
                echo '<h3 style="color:red"> cURL is NOT enabled on this server</h3>';
            }
            echo '</div>';
        } else {

            echo '<div class="wrap">';
            echo '<h2>You are not a Diskdaddy Admin!</h2><p>You do not have permissions to edit these settings' ;
            
            echo '</div>';

        }
    
    }


    function Disk_Daddy_Plugin_Menu() {
    
      add_options_page('Diskdaddy Plugin Options Menu','Diskdaddy Monitor','manage_options', 'Disk_Daddy_Plugin', 'Disk_Daddy_callOptionsPage');

    }

    function display_transient_update_plugins($transient) {

        global $wpdb;

        //if there are any plugins that need to be updated then this them here
        if(count($transient->response)) {
            //update the db
            //echo 'Size of the array is ' . count($transient->response);
            $number_of_plugins_to_update = count($transient->response);
            $sql = "UPDATE wp_diskdaddy_plugin_variables SET the_value = '$number_of_plugins_to_update' WHERE the_key = 'plugins_to_update';";
            $result = $wpdb->query($sql);
        }
        
       
        return $transient;
    }

    function Disk_Daddy_Plugin_register_variables() {
        register_setting('Disk_Daddy_Plugin-Group', 'FTPFileName');
        register_setting('Disk_Daddy_Plugin-Group', 'Support_Contract_Expiry');
        
    }



        /*  HOOKS   */

    //register the activation of the plugin   
    register_activation_hook( __FILE__, 'Disk_Daddy_InitThePlugin' );
    register_deactivation_hook( __FILE__, 'Disk_Daddy_UninitThePlugin' );

    //user logged in
    add_action('wp_login',      'Disk_Daddy_Update_Login', 10, 2);

    //update last page or post modded
    add_action('publish_page',  'Disk_Daddy_Update_Post', 10, 1);
    add_action('post_updated',  'Disk_Daddy_Update_Post', 10, 1);
    add_action('edit_post',     'Disk_Daddy_Update_Post', 10, 1);

    //add the options page
    add_action('admin_menu', 'Disk_Daddy_Plugin_Menu');
    add_action('admin_init', 'Disk_Daddy_Plugin_register_variables');

    //cron hook event
    add_action('Disk_Daddy_Plugin_upload_hook', 'Disk_Daddy_Plugin_upload');

    //check for plugin updates
    add_filter ('site_transient_update_plugins', 'display_transient_update_plugins');

?>
