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

		
		if(isset($_POST) && sizeof($_POST) >0){ //only instantiate after a user has changed the settings, on the first login landing page will load $_SESSION variables
												//from DB--> if it has been previously instantiated by someone from another session.

			instantiate_email_presets($_POST['15_days_overdue'],$_POST['30_days_overdue']);
		}
		

		//include 'dump_all_page_contents.php';
		
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


function instantiate_email_presets($email_id_15_overdue,$email_id_30_overdue){
	
	include 'connectToDB.php'; 
	

			///////////////////////////////////////////////////////////////////////////////////		
			$query_username_15="SELECT username FROM users WHERE user_id='{$email_id_15_overdue}'";
			$result_username_15 = mysqli_query($db,$query_username_15);
			
			$rowUserNames_15=mysqli_fetch_array($result_username_15);
			
			
			//dynamically build the to address from the username selected based on the recipients specified by the step in the process
			$_SESSION['mailto_over15']=$rowUserNames_15['username'].'@chcb.org';
			///////////////////////////////////////////////////////////////////////////////////
			
			
			///////////////////////////////////////////////////////////////////////////////////		
			$query_username_30="SELECT username FROM users WHERE user_id='{$email_id_30_overdue}'";
			$result_username_30 = mysqli_query($db,$query_username_30);
			
			$rowUserNames_30=mysqli_fetch_array($result_username_30);
		
			//var_dump($rowUserNames_30);		

			//dynamically build the to address from the username selected based on the recipients specified by the step in the process
			$_SESSION['mailto_over30']=$rowUserNames_30['username'].'@chcb.org';
			///////////////////////////////////////////////////////////////////////////////////
			
			echo '<br><center><b> Email Settings have been set! </b></center>';

}
	

//Check that new user data submitted is valid that returns array of errors
function validateNewUser (){
	global $_POST;
	$errors = array();
	
	/*
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

	*/
	
	return 'valid';	

	
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

/*

function showPageTwo($username='', $accessLvl = '', $errors = ''){

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


		print <<<ADDUSERPAGE

			<h2 align="center">Status: <b>Regular</b></h2>
			<form method="POST" action="{$_SERVER['PHP_SELF']}" name="add_acc_rec">
		  <table style="width: 100%" border="1">
			<tbody>
			
			  <tr>
				<td>15 Days Overdue Email Recipient</td>
				<td>
				<input maxlength="50" name="15_days_overdue" type="text" value ="{$_POST['15_days_overdue']}">
				
				
				<br>
				</td>
			  </tr>
			  
			 

			  	<tr>
				<td>30 Days Overdue Email Recipient</td>
				<td>
	              <select name="30_days_overdue">
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
EDITUSERPAGE;		

		  
	print <<<EDITUSERPAGE

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

*/



function showPage($username='', $accessLvl = '', $errors = ''){

	showHeader($username, $accessLvl);
	global $db;


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	

		$parts_emailName_30=explode("@",$_SESSION['mailto_over30']);		
		$parts_emailName_15=explode("@",$_SESSION['mailto_over15']);		
		
	
		print <<<ADDUSERPAGE

			<h2 align="center">Configure Email Settings</h2>
			<form method="POST" action="{$_SERVER['PHP_SELF']}" name="add_user">
		  <table style="width: 100%" border="1">
			<tbody>

			  <tr>
				<td>Email After 30 Days</td>
				<td>
	              <select name="30_days_overdue">
ADDUSERPAGE;

			$query_users = 'SELECT user_id, first_name, last_name, username FROM users';
			$result_users = mysqli_query($db,$query_users);
			$selected=1;
							

			while($row_users = mysqli_fetch_array($result_users)){
				
				$selected = ($row_users['username'] === $parts_emailName_30[0]) ? ' selected="selected"' : '';
				
				print "<option value=\"{$row_users['user_id']}\" {$selected} ";
				print ">{$row_users['first_name']} {$row_users['last_name']}</option>";	

			}
			

	
	print <<<ADDUSERPAGE
              </select>
              <br>
            </td>
          </tr>
          <tr>
		  <td>Email After 15 Days</td>

		  <td>
		  <select name="15_days_overdue">
ADDUSERPAGE;

			$result_users_15 = mysqli_query($db,$query_users);
			//$selected=1;

			while($row_users_15 = mysqli_fetch_array($result_users_15)){
				
				$selected = $row_users_15['username'] === $parts_emailName_15[0] ? ' selected="selected"' : '';

				print "<option value=\"{$row_users_15['user_id']}\" {$selected} ";
				print ">{$row_users_15['first_name']} {$row_users_15['last_name']}</option>";	

			}
			
			print <<<ADDUSERPAGE
              </select>
              <br>
            </td>
          </tr>
          <tr>
		  
		</tbody>
      </table>
      <input type="hidden" name="_submit_check" value="1" />
	  <br>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Add User</button></form>


ADDUSERPAGE;

	  
	print '<h4 align="center"><a href="admin.php">Return to User Administration Page</a></h4>';

	showFooter();

}


/*

	if(isset($_POST['department'])){	
		
		while($row = @mysqli_fetch_array($result)){
			print "<option value=\"{$row['dept_id']}\"";
			if ($_POST['department']==$row['dept_id']){
				print ' selected="selected" ';		
			}
			print ">{$row['name']}</option>";	

		}
	}else{
		while($row = @mysqli_fetch_array($result)){
			print "<option value=\"{$row['dept_id']}\"";
			if ($_POST['department']==$row['dept_id']){
				print ' selected="selected" ';		
			}
			print ">{$row['name']}</option>";	

		}
	}


*/

?>