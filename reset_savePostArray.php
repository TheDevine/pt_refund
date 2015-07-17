<?php	

//session_start();

	//RESET THE SAVE POST ARRAY AND THEN REDIRECT to the search_landing.php page
	$_SESSION['SAVE_POST']="";
	//echo 'coming here';
	//die();
	//echo 'right before redirect' ;
	
	//echo $_SERVER['HTTP_HOST'];
	//echo '<br>';
	
	//echo $_SERVER['PHP_SELF'];
	//echo '<br>';
	
	$redirect_URL=$_SERVER['HTTP_HOST']."/pt_refund/search_landing.php";
	//	echo '<br>';

	//echo $redirect_URL;
	
	header("Location : ".$redirect_URL);
	
	//echo '<br> do I reach this ';

	?>