<?php

//send stuff using php
 		$curlStuff = curl_init();
        curl_setopt($curlStuff, CURLOPT_URL, "http://www.diskdaddy.com/Monitor/uploadData.php?hello=yeees");
        curl_exec($curlStuff);
        curl_close($curlStuff);


?>