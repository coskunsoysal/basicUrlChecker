<?php

/**
* This script check specific urls
*
* @author Coşkun Soysal
*
**/

header('Content-Type: text/html; charset=utf-8');

$mysqlStart = $argv[1];
$mysqlEnd 	= $argv[2];
 
// get config file
$config = parse_ini_file("config.ini", true);

// connect to mysql 
$mysqli = new mysqli( $config['mysql']['host'], 
                      $config['mysql']['user'], 
                      $config['mysql']['password'], 
                      $config['mysql']['db']);

$cont = True;
while($cont == True){

	// Select queries return a resultset 
	$sql = "SELECT id, old_url 
			FROM urls 
			WHERE checked=0 AND id>=$mysqlStart AND id<=$mysqlEnd 
			LIMIT 1";
	
	if ($result = $mysqli->query($sql)) {

		if ($result->num_rows == 0){
			exit("No url found!");
		}

		// get one url from database
		$row 	= $result->fetch_assoc();
		
		$old_url = trim($row['old_url']);
		$old_url = str_replace(' ', '%20', $old_url);
		echo $old_url;

		ini_set(
			'user_agent', 
			'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36'
			);

		// get url return details
	    $headers = get_headers($old_url, 1);

	    $new_url = '';

	    // check redirected url
		if (array_key_exists('Location', $headers)) {
		    if(is_array($headers['Location'])){
		    	$new_url=end($headers['Location']);
		    }else{
		    	$new_url=$headers['Location'];
		    }
	     }

	    // update url check details
    	$sql = "UPDATE urls 
    			SET checked='1', code='$headers[0]', new_url='$new_url' 
    			WHERE id='".$row['id']."'";

		if ($mysqli->query($sql) === TRUE) {
		    echo "New record checked successfully\n";
		} else {
		    $info = $old_url."   ->  "."Error: " . $sql . "<br>" . $mysqli->error."\n";
		    echo $info;
		    file_put_contents("error_insert.txt", $info , FILE_APPEND);
		}
	}else{
		$cont = False;
	}
}

?>