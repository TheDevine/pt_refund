<?php
/*
This script will dump all the contents of whichever page the file is called from.

*/
if (!isset($_SESSION))
  {
	session_start();
  }
	echo "The SESSION Contents are: ";
	var_dump($_SESSION);
	
	echo "The POST Contents are: ";
	var_dump($_POST);

	echo "The REQUEST Contents are: ";
	var_dump($_REQUEST);

	echo "The GET Contents are: ";
	var_dump($_GET);

	echo "The ENV Contents are: ";
	var_dump($_ENV);

	echo "The SERVER Contents are: ";
	var_dump($_SERVER);


?>