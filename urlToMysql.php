<?php

/**
* This script import index keywords to db from file
*
* @author Coşkun Soysal
*
**/

header('Content-Type: text/html; charset=utf-8');

define("URL_FILE", "urls.txt");
 
// get config file
$config = parse_ini_file("config.ini", true);

// connect to mysql 
$mysqli = new mysqli( $config['mysql']['host'], 
                      $config['mysql']['user'], 
                      $config['mysql']['password'], 
                      $config['mysql']['db']);


$handle = fopen(URL_FILE, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {

    	$sql = "INSERT INTO urls (old_url)
				VALUES ('$line')";

		if ($mysqli->query($sql) === TRUE) {
		    echo "New record created successfully\n";
		} else {
		    $info = $line."   ->  "."Error: " . $sql . "<br>" . $mysqli->error."\n";
		    echo $info;
		    file_put_contents("error.txt", $info , FILE_APPEND);
		}
    }

    fclose($handle);
} else {
	$error = "no input file";    
    die($error);
	file_put_contents("error.txt", $error, FILE_APPEND);

} 

?>