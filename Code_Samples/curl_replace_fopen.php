<?php
	/**
	 * Script: Curl_Replace_Fopen.php
	 * Descr: If fopen is not available for some reason, we can use CURL
	 * Date: 20 Oct 2011
	*/

	//Check to see if we have access to fopen
	if (function_exists('fopen')){
		echo 'You can use fopen on this server, but we will use curl anyways';
	}else{
		echo 'Fopen is not available on this server, let\'s use curl';
	}
	echo '<br /><br /><hr></hr>';

	/**
	 * Attempt to read a file's contents with curl if fopen is not available
	 * @param string $source
	 * @return string $file_contents
	*/
	function fopen_curl($source = 'http://applaunch.ca/TheWebSandbox/Code_Samples/fopen_curl_test.html'){
		if (!function_exists('curl_init')) die( 'CURL is not available on this server' );

		//Create a new CURL resource
		$ch = curl_init();
		if (!is_resource($ch)) die( 'Could not initiate a CURL resource' );

		//Set source URL of fetch action
		curl_setopt($ch, CURLOPT_URL, $source);

		//We do not want headers returned
		curl_setopt($ch, CURLOPT_HEADER, 0);

		//Do not print response to the browser but return it instead
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//Get the contents of the source
		$file_contents = curl_exec($ch);

		//Close curl session
		curl_close($ch);

		return $file_contents;
	}

	//Call func
	$contents = fopen_curl();

	echo "<h2>File Contents:</h2><br />" . $contents;
?>
