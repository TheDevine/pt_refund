<?php

if (!isset($_SESSION))
  {
	session_start();
  }
  
if (array_key_exists('userid', $_SESSION)){	//If user is logged in show page


		if (sizeof($_REQUEST)==0 || isset($_GET['page_number'])){
			//echo 'coming here each time';
			showPage($_SESSION['username'],$_SESSION['access']); //so index page wont duplicate content under content when selections are made
		}


} elseif(isset($_POST['username']) && $_POST['username']!=NULL && $_POST['username']!="" ) { //if user has attempted to login, validate login
   
	if(validateLogin($_POST['username'],$_POST['password'])){
		showPage($_SESSION['username'], $_SESSION['access']);	
	} else {
		showLogin('Login invalid. Please try again');	
	}
	
} else { //Else show login screen
	 //echo 'Hello ET';
	 showLogin(); 

}

?>