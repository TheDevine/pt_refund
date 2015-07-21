<?php

//start session
ini_set('session.gc_maxlifetime', 600);
session_start();

require('lib\refundFunctions.php');

//uncomment the next line to toggle session dumps on and off
//include 'dump_all_page_contents.php';    

//include 'lib\functions.php';

include 'connectToDB.php';  

if (array_key_exists('userid', $_SESSION)){	//If user is logged in show page
	showReportsPage($_SESSION['username'],$_SESSION['access']);
	
} elseif($_POST['username']) { //if user has attempted to login, validate login
   
   
	if(validateLogin($_POST['username'],$_POST['password'])){
		showReportsPage($_SESSION['username'], $_SESSION['access']);	
	} else {
		showLogin('Login invalid. Please try again');	
	}

} else { 		//Else show login screen
	showLogin();
}
	
/*
$db = mysqli_connect('localhost','ptrefund','x22m3y2k','pt_refund'); //connect to database
   if(!$db){die("Can't connect: ". mysqli_connect_error());}



//Function to display homepage

//REWRITE SO THIS FUNCTION CAN BE KEPT IN EXTERNAL FILE AND USED BY ALL PAGES
function showHeader($username='',$accessLvl=''){
	print <<<HEADER
<HTML>
	<HEAD>
		<link rel="stylesheet" type="text/css" href="refundStyle.css">
		<TITLE>CHCB Patient Refund Manager</TITLE>
	</head>
	<body>
		<div class="pageContainer">
		<table id="head" ><tr><td><img src = "logo.png" class="logo" /></td><td><h1 class="title">Patient Refund Manager</h1></td></tr></table>
HEADER;

	if($username){
		print '<div class="greeting">';		
		print "<h4>Hi, {$username}!</h4>";
		print "<h4> Your access level is: ";
			if($accessLvl == 'S'){
				print "Superuser </h4>";			
			} elseif($accessLvl == 'U'){
				print "Standard User </h4>";			
			} elseif($accessLvl =='A'){
				print "Approver </h4>";			
			} else {
				print "Unrecognized! </h4>";
			}
		
		
		print "<a href =\"logout.php\">Logout</a></div>";
		
		print "<table class = \"topMenu\"><tr><td><a href=\"index.php\" class = \"button\">Home</td><td><a href=\"refunds.php\" class = \"button\">Refunds</a></td><td><a href=\"reports.php\" class = \"button\" id = \"selected\">Reports</a></td><td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
	if ($accessLvl == 'S'){
		print '<td><a href="admin.php" class = "button">Admin</a></td></tr></table>';	
	}else {
		print '</tr></table>';
	}
		
	}

}

*/

/*
string message="<a href='.$_SERVER['PHP_SELF'].'?report_id=1>All Refunds</a>";
showPage('','','',message);
*/



?>