<?php

//start session
ini_set('session.gc_maxlifetime', 600);
session_start();

require('refundFunctions.php');
include 'connectToDB.php'; 

include 'dump_all_page_contents.php';

/*
$db = mysqli_connect('localhost','ptrefund','x22m3y2k','pt_refund'); //connect to database
   if(!$db){die("Can't connect: ". mysqli_connect_error());}
   */
   
/*

The POST Contents are:

array (size=6)
  'initialBillingWarning_Email' => string '3' (length=1)
  'finalBillingWarning_Email' => string '' (length=0)
  'initialAccountingWarning_Email' => string '9' (length=1)
  'finalAccountingWarning_Email' => string '' (length=0)
  '_submit_check' => string '1' (length=1)
  'Submit' => string 'submit' (length=6)
  
  
The SESSION Contents are:

array (size=5)
  'userid' => string '14' (length=2)
  'username' => string 'Derek Devine' (length=12)
  'access' => string 'S' (length=1)
  'BILLING_FINAL_WARN_EMAIL' => string 'yes' (length=3)
  'EMAIL_IF_URGENT' => string 'no' (length=2)


*/


if (array_key_exists('userid', $_SESSION)){	//If user is logged, check for access level 

	if($_SESSION['access']=='S'){
		//once user is authenticated, check to see if this form has been submitted
		//if($_POST['_submit_check']){ //form has been submitted
		

		if(isset($_POST['_submit_dates_set_accounting_check']) && $_POST['_submit_dates_set_accounting_check']!="" && $_POST['_submit_dates_set_accounting_check']!=NULL){ 

			
				/*
				array (size=6)
				'initialBillingWarning_Email' => string '1' (length=1)
				'finalBillingWarning_Email' => string '2' (length=1)
				'initialAccountingWarning_Email' => string '3' (length=1)
				'finalAccountingWarning_Email' => string '4' (length=1)
				'_submit_dates_set_accounting_check' => string '1' (length=1)
				'Submit' => string 'submit' (length=6)
				*/
				//set the session application constants regarding email
			
			
			if(isset($_POST['initialBillingWarning_Email']) && strlen($_POST['initialBillingWarning_Email']) >1){ 

				$_SESSION['BILLING_INITIAL_WARN_EMAIL']=$_POST['initialBillingWarning_Email'];
				//include 'dump_all_page_contents.php';
				//die();
			}
			
			elseif(isset($_POST['finalBillingWarning_Email']) && strlen($_POST['finalBillingWarning_Email']) >1){ 

				$_SESSION['BILLING_FINAL_WARN_EMAIL']=$_POST['finalBillingWarning_Email'];
			//include 'dump_all_page_contents.php';

			//die();
			}

			elseif(isset($_POST['initialAccountingWarning_Email']) && strlen($_POST['initialAccountingWarning_Email']) >1){ 

				$_SESSION['BILLING_INITIAL_WARN_EMAIL']=$_POST['initialAccountingWarning_Email'];
			//include 'dump_all_page_contents.php';
			//die();
			}

			elseif(isset($_POST['finalAccountingWarning_Email']) && strlen($_POST['finalAccountingWarning_Email']) >1){ 

				$_SESSION['BILLING_FINAL_WARN_EMAIL']=$_POST['finalAccountingWarning_Email'];
			//include 'dump_all_page_contents.php';
			//die();
			}
			
			elseif(isset($_POST['email_if_urgent']) && strlen($_POST['email_if_urgent']) >1){ 

				$_SESSION['EMAIL_IF_URGENT']=$_POST['email_if_urgent'];
			
			
			//include 'dump_all_page_contents.php';
			//die();
			}


		}
		
		if(isset($_POST['_submit_check']) && $_POST['_submit_check']!="" && $_POST['_submit_check']!=NULL){ 
		
			//check for errors
			if(validateNewUser()=='valid') //if no errors, create user in db and show success message
			{
				//create user in db
				
				$encrypted_passwd = crypt($_POST['password']);				
				$query = "INSERT INTO users (first_name, last_name, access_lvl, dept_id, password, username) VALUES ('{$_POST['first_name']}','{$_POST['last_name']}','{$_POST['access']}','{$_POST['department']}','{$encrypted_passwd}','{$_POST['username']}')";
				$result = mysqli_query($db,$query);
								
				
				//show success message
				print '<h3 align="center"> User '.$_POST['username'].' ('.$_POST['first_name'].' '.$_POST['last_name'].') created!</h3>';
				print '<h4 align="center"><a href="admin.php">Return to Admin Page</a></h4>';
			
			} else {
				showPage($_SESSION['username'], $_SESSION['access'],validateNewUser());
			}
			
			//if errors exist, show page again & fill in values
		
		} else { //form has not been submitted
			showPage($_SESSION['username'],$_SESSION['access']); //show page only if user is a super user
		}
		
	} else {
			showLogin('The current user is not authorized to view this page.');	//all other users types OWNED!!
	}
	
} elseif($_POST['username']) { //if user has attempted to login, validate login
   
   
	if(validateLogin($_POST['username'],$_POST['password'])){
		showPage($_SESSION['username'], $_SESSION['access']);	//valid user! Show page!
	} else {
		showLogin('Login invalid. Please try again');	
	}

} else { 		//Else show login screen (no user is logged in and no login attempt has been made)
	showLogin();
}
	

//Check that new user data submitted is valid that returns array of errors
function validateNewUser (){
	global $_POST;
	$errors = array();
	
	//check that passwords match
	if ($_POST['password']!=$_POST['passwordConfirm']){
		$errors[]='Passwords must match';	
	}
	
	if (strlen($_POST['username'])<3){
		$errors[]='Usernames must be at least 3 characters long';	
	}

	if (strlen($_POST['first_name'])<2){
		$errors[]='First names must be at least 2 characters long';	
	}
	
	if (strlen($_POST['last_name'])<2){
		$errors[]='Last names must be at least 2 characters long';	
	}

	if (strlen($_POST['password'])<6){
		$errors[]='Passwords must be at least 6 characters long';	
	}

	if($errors) {
		return $errors;
	} else {
		return 'valid';	
	}

} 	


//Page Header (sometimes different depending on whether page has restricted access or not)
function showHeader($username='',$accessLvl=''){
	print <<<HEADER
<HTML>
	<HEAD>
		<link rel="stylesheet" type="text/css" href="refundStyle.css">
		<TITLE>CHCB Patient Refund Manager</TITLE>
	</head>
	<body>
		<table id="head"><tr><td><img src = "logo.png" class="logo" /><td><td><h1 class="title">Patient Refund Manager</h1></td></tr></table>
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
		
		print "<table class = \"topMenu\"><tr><td><a href=\"index.php\"  class = \"button\" >Home</td><td><a href=\"refunds.php\" class = \"button\">Refunds</a></td><td><a href=\"reports.php\"  class = \"button\">Reports</a></td><td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
	if ($accessLvl == 'S'){
		print '<td><a href="admin.php" id = "selected">Admin</a></td></tr></table>';	
	}else {
		print '</tr></table>';
	}
		
	}

	


}



function showPage($username='', $accessLvl = '', $errors = ''){

	showHeader($username, $accessLvl);
	global $db;
	include 'connectToDB.php'; 


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	
	//if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['username'])){

	
	/* Send initial Warning Emails to Accounting After How Many Days?
	
	Send Second Warning Email to Accounting After Delinquent for How Many Days?
	*/
	
		print <<<ADDUSERPAGE
			<h2 align="center"><br><br>Configure Email Send Dates and Times</h2>
			<form method="POST" action="{$_SERVER['PHP_SELF']}" name="cfg_send_date_time">
		  <table style="width: 100%" border="0">
			<tbody align="center">

			<tr>
			
			<td>Send Initial Billing Warning Emails after How many Days?</td>
			

			<td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


              <select name="initialBillingWarning_Email">
ADDUSERPAGE;

			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;

			$days_ctr=1;
			print "<option value=\"\"";
			print "</option>";
			$selected="";
			
			$selected = ($row_users['user_id'] === $rec_1['recipient_1']) ? ' selected="selected"' : '';

			while($days_ctr<=365){

				print "<option value=\"{$days_ctr}\"";
				print ">{$days_ctr}</option>";	

				$days_ctr++;

			}
			
			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;



	print <<<ADDUSERPAGE
	
	</select>

            </td>
			</tr>
							<br><br>


			<tr>
			<td>Send Final Billing Warning Email after How many Days?</td>
			

			<td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

              <select name="finalBillingWarning_Email">
ADDUSERPAGE;

			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;

			$days_ctr=1;
			print "<option value=\"\"";
			print "</option>";
			$selected="";
			
			$selected = ($row_users['user_id'] === $rec_1['recipient_1']) ? ' selected="selected"' : '';

			while($days_ctr<=365){

				print "<option value=\"{$days_ctr}\"";
				print ">{$days_ctr}</option>";	

				$days_ctr++;

			}
			
			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;
			

			
			print <<<ADDUSERPAGES
				</select>
				</td>
				</tr>
				
								<br><br>

				<tr>
				<td>Send Accounting Initial Warning Email after how many Days? </td>
				
ADDUSERPAGES;

			print <<<ADDUSERPAGE
			<td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

              <select name="initialAccountingWarning_Email">
ADDUSERPAGE;

			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;

			$days_ctr=1;
			print "<option value=\"\"";
			print "</option>";
			$selected="";
			
			$selected = ($row_users['user_id'] === $rec_1['recipient_1']) ? ' selected="selected"' : '';

			while($days_ctr<=365){

				print "<option value=\"{$days_ctr}\"";
				print ">{$days_ctr}</option>";	

				$days_ctr++;

			}
			
			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;
							
				

				print <<<ADDUSERPAGED
				</tr>
				<br><br>

				<tr>
				<td>Send Accounting Final Warning Email after how many Days? </td>

				<td>
			  <select name="finalAccountingWarning_Email">
ADDUSERPAGED;
			
			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;

			$days_ctr=1;
			print "<option value=\"\"";
			print "</option>";
			$selected="";
			
			$selected = ($row_users['user_id'] === $rec_1['recipient_1']) ? ' selected="selected"' : '';

			while($days_ctr<=365){

				print "<option value=\"{$days_ctr}\"";
				print ">{$days_ctr}</option>";	

				$days_ctr++;

			}
			
			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;
			
			print <<<ADDUSERPAGES
				</select>
				</tr>
				</td>
ADDUSERPAGES;


	print <<<EDITUSERPAGED
	
	
		<tr>
		<td>Send Emails at each step of the Process if Status is Urgent? </td>
		<td>	  
EDITUSERPAGED;
	
	
	print "<option value=''></option>";

	print <<<EDITUSERPAGED
	
	
		<select name="email_if_urgent">

EDITUSERPAGED;
	
	
	print "<option value=yes>Yes</option>";
	print "<option value=no>No</option>";
	
	print <<<EDITUSERPAGED

		</select>
		</td>
		</tr>

        </tbody>
      </table>
      <input type="hidden" name="_submit_dates_set_accounting_check" value="1" />
	  <br><br><br>

      <center><button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Submit Changes</button></center></form>
	  
EDITUSERPAGED;

//}


	
	print '<br><h4 align="center"><a href="admin.php">Return to User Administration Page</a></h4>';

	showFooter();

}


?>