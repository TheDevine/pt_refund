<?php

//start session
ini_set('session.gc_maxlifetime', 600);
session_start();

require('refundFunctions.php');
include 'connectToDB.php'; 


/*
$db = mysqli_connect('localhost','ptrefund','x22m3y2k','pt_refund'); //connect to database
   if(!$db){die("Can't connect: ". mysqli_connect_error());}
   */

if (array_key_exists('userid', $_SESSION)){	//If user is logged, check for access level 
	if($_SESSION['access']=='S'){
		//once user is authenticated, check to see if this form has been submitted
		//if($_POST['_submit_check']){ //form has been submitted
		
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

	
	//Send initial Warning Emails to Accounting After How Many Days?
	
	//Send Second Warning Email to Accounting After Delinquent for How Many Days?
	
		print <<<ADDUSERPAGE

			<h2 align="center">Configure Email Send Dates and Times</h2>
			<form method="POST" action="{$_SERVER['PHP_SELF']}" name="add_acc_rec">
		  <table style="width: 100%" border="1">
			<tbody>
			  <tr>
				<td>Accounting Recipient 1</td>
				<td><input maxlength="50" name="accounting_recipient_1" type="text" value ="{$_POST['accounting_recipient_1']}"><br>
				</td>
			  </tr>
			  	<tr>
				<td>Accounting Recipient 2</td>
				<td><input maxlength="50" name="accounting_recipient_2" type="text" value ="{$_POST['accounting_recipient_2']}"><br>
				</td>
			  </tr>
			  	<tr>
				<td>Accounting Recipient 3</td>
				<td><input maxlength="50" name="accounting_recipient_3" type="text" value ="{$_POST['accounting_recipient_3']}"><br>
				</td>
			  </tr>
			  
			  <tr>
				<td>Billing Recipient 1</td>
				<td><input maxlength="50" name="par1_recipient_1" type="text" value ="{$_POST['par1_recipient_1']}"><br>
				</td>
			  </tr>
			<tr>
				<td>Billing Recipient 2</td>
				<td><input maxlength="50" name="par1_recipient_2" type="text" value ="{$_POST['par1_recipient_2']}"><br>
				</td>
			  </tr>
			  <tr>
				<td>Billing Recipient 3</td>
				<td><input maxlength="50" name="par1_recipient_3" type="text" value ="{$_POST['par1_recipient_3']}"><br>
				</td>
			  </tr>
			  		  
					  
					  <tr>
				<td>Overdue 15 Days Recipient 1</td>
				<td><input maxlength="50" name="over15_recipient_1" type="text" value ="{$_POST['over15_recipient_1']}"><br>
				</td>
			  </tr>
			<tr>
				<td>Overdue 15 Days Recipient 2</td>
				<td><input maxlength="50" name="over15_recipient_2" type="text" value ="{$_POST['over15_recipient_2']}"><br>
				</td>
			  </tr>
			  <tr>
				<td>Overdue 15 Days Recipient 3</td>
				<td><input maxlength="50" name="over15_recipient_3" type="text" value ="{$_POST['over15_recipient_3']}"><br>
				</td>
			  </tr>
			  
					  <tr>
				<td>Overdue 30 Days Recipient 1</td>
				<td><input maxlength="50" name="over30_recipient_1" type="text" value ="{$_POST['over30_recipient_1']}"><br>
				</td>
			  </tr>
			<tr>
				<td>Overdue 30 Days Recipient 2</td>
				<td><input maxlength="50" name="over30_recipient_2" type="text" value ="{$_POST['over30_recipient_2']}"><br>
				</td>
			  </tr>
			  <tr>
				<td>Overdue 30 Days Recipient 3</td>
				<td><input maxlength="50" name="over30_recipient_3" type="text" value ="{$_POST['over30_recipient_3']}"><br>
				</td>
			  </tr>			  
					  
				  
					  <tr>
				<td>Completed Recipient 1</td>
				<td><input maxlength="50" name="completed_recipient_1" type="text" value ="{$_POST['completed_recipient_1']}"><br>
				</td>
			  </tr>
			  
			  		    <tr>
            <td>Re-Assign Refund To: </td>
            <td>
              <select name="assignee">
ADDUSERPAGE;

			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;

	
			while($row_users = mysqli_fetch_array($result_users)){
				print "<option value=\"{$row_users['user_id']}\"";
				print ">{$row_users['first_name']} {$row_users['last_name']}</option>";	

			}
			
			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;
	print <<<EDITUSERPAGE
              </select>
              <br>
            </td>
          </tr>
			  
			<tr>
				<td>Completed Recipient 2</td>
				<td><input maxlength="50" name="completed_recipient_2" type="text" value ="{$_POST['completed_recipient_2']}"><br>
				</td>
			  </tr>
			  <tr>
				<td>Completed Recipient 3</td>
				<td><input maxlength="50" name="completed_recipient_3" type="text" value ="{$_POST['completed_recipient_3']}"><br>
				</td>
			  </tr>			  
					  <tr> Select A Name: &nbsp;&nbsp;&nbsp;&nbsp;
		  <select name="refund_search_term">
EDITUSERPAGE;
		while($row_users = mysqli_fetch_array($result_users)){
			print "<option value=\"{$row_users['user_id']}\"";
			print $selected; ">{$row_users['first_name']} {$row_users['last_name']}</option>";	

		}
print <<<EDITUSERPAGE
		  </select>
		  <br>
		</td>
	  </tr>	 

        </tbody>
      </table>
      <input type="hidden" name="_submit_check" value="1" />
	  <br>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Submit Changes</button></form>
	  
EDITUSERPAGE;

//}


	
	print '<h4 align="center"><a href="admin.php">Return to User Administration Page</a></h4>';

	showFooter();

}


?>