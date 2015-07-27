<?php

/*
Repository of Functions for the Patient Refund Manager Application
*/


function logout(){
	
	unset($_SESSION['userid']);

	print <<<LOGOUT
	<HTML>
		<HEAD>
			<TITLE>User Logged Out</title>
		</head>
		<body>
			<h1 align="center">You have been logged out</h1>
			<h3 align="center"><a href="index.php">Click here to return to the homepage</a></h3>
		</body>
	</html>
LOGOUT;
			

}


function showFooter(){

print <<<FOOTER
	<br><br>
	</div> 
	<center><div class="footer">(index.php version) <br />
	&copy; Community Health Centers of Burlington, Inc. 2014</div></center>
	</body>
	</html>
FOOTER;

/*

print <<<FOOTER
	<br><br>
	</div> 
	<center><div class="footer">Created by Jonathan Bowley<br /> 
	Modified By Derek Devine (index.php version) <br />
	&copy; Community Health Centers of Burlington, Inc. 2014</div></center>
	</body>
	</html>
FOOTER;



*/

}

function showLogin($error=''){
	
	showHeader();

	if($error){
		print $error;	
	}	
	
	print <<<LOGINFORM
		<form action="{$_SERVER['PHP_SELF']}" method="post">
		<table>
			<tr><td><b>Username:</b></td><td><input type="text" name="username" /></td></tr>
			<tr><td><b>Password:</b></td><td><input type="password" name="password" /></td></tr>
			<tr><td colspan="2"><button name="Login" value="Login" type="submit">Login</button></td></tr>
		</table>
		</form>
LOGINFORM;
	
	showFooter();

}

function validateLogin($username ='', $password=''){
	global $_SESSION;	
	global $db;
	global $_POST;	

	//check password is correct for supplied username
	$query = 	"SELECT user_id, password, first_name,last_name,access_lvl FROM users WHERE username = '{$_POST['username']}'";
	
	//save the username entered into a newly created loginName session variable -- $_SESSION['loginName']
	if (isset($_POST['username'])  && $_POST['username']!="" && $_POST['username']!=NULL){
		$_SESSION['loginName'] = $_POST['username'];
	}
	
	$result = mysqli_query($db,$query);
	
	//var_dump($result);
	//die();
	
	$row = mysqli_fetch_array($result);
	$dbPassword = $row['password'];
	
	if($dbPassword==crypt($password,$dbPassword)){
		$_SESSION['userid'] = $row['user_id'];
		$_SESSION['username'] = $row['first_name'].' '.$row['last_name'];
		$_SESSION['access'] = $row['access_lvl'];
		return true;	
	} else {
		return false;	
	}

}




//REWRITE SO THIS FUNCTION CAN BE KEPT IN EXTERNAL FILE AND USED BY ALL PAGES
function showHeader($username='',$accessLvl=''){
	
	include 'connectToDB.php'; 
	
	//dept_id=2 is accounting, 3= billing
	
	$query_dept = "SELECT dept_id FROM users WHERE user_id='{$_SESSION['userid']}'";
	$result_dept = mysqli_query($db,$query_dept); 
	$dept_row = mysqli_fetch_array($result_dept);
	
	$query_deptName = "SELECT name FROM departments WHERE dept_id='{$dept_row['dept_id']}'";
	$result_deptName = mysqli_query($db,$query_deptName); 
	$dept_rowName = mysqli_fetch_array($result_deptName);
	

	
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
		
			if($dept_rowName[0]=="Accounting"){
			
				print "<table class = \"topMenu\"><tr><td>
				<a href=\"reset_home.php\" id = \"selected\" class = \"button\" >Home</td>
				<td><a href=\"reports.php\"  class = \"button\">Reports</a></td>
				<td><a href=\"unset_search.php\"  class = \"button\">Search</a></td>
				<td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
				if ($accessLvl == 'S'){
				print '<td><a href="admin.php" class = "button">Admin</a></td></tr></table>';	
				}else {
				print '</tr></table>';
				}
				
			}elseif($dept_rowName[0]=="Billing"){
			
				print "<table class = \"topMenu\"><tr><td>
				<a href=\"reset_home.php\" id = \"selected\">Home</td>
				<td><a href=\"reports.php\"  class = \"button\">Reports</a></td>
				<td><a href=\"unset_search.php\"  class = \"button\">Search</a></td>
				<td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
				if ($accessLvl == 'S'){
				print '<td><a href="admin.php" class = "button">Admin</a></td></tr></table>';	
				}else {
				print '</tr></table>';
				}

			}else{
				
				print "<table class = \"topMenu\"><tr><td>
				<a href=\"reset_home.php\" id = \"selected\">Home</td>
				<td><a href=\"reports.php\"  class = \"button\">Reports</a></td>
				<td><a href=\"unset_search.php\"  class = \"button\">Search</a></td>
				<td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
				if ($accessLvl == 'S'){
				print '<td><a href="admin.php" class = "button">Admin</a></td></tr></table>';	
				}else {
				print '</tr></table>';
				}
					
				
			}

		
	}

}


/*

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
		
		print "<table class = \"topMenu\">
		<tr>
		<td><a href=\"reset_home.php\"  class = \"button\" >Home</td>
		<td><a href=\"reports.php\"  class = \"button\">Reports</a></td>
		<td><a href=\"unset_search.php\"  class = \"button\">Search</a></td>		
		<td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
	if ($accessLvl == 'S'){
		print "<td><a href=\"admin.php\" class = \"button\" id = \"selected\">Admin</a></td></tr></table>";	
	}else {
		print '</tr></table>';
	}
	
	
		
	}


}

*/


function showAdminPage($username='', $accessLvl = '', $errors = ''){

	showHeader($username, $accessLvl);

	//include 'dump_all_page_contents.php';

	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}

	print <<<ADMINPAGE
			<hr>		<center>

		<h2>User Administration</h2>
		<h3>USER SETTINGS</h3>
		<a href="adduser.php">Add a User</a><br><br>
		<a href="edituser.php">Edit/Delete a User</a><br><br>
ADMINPAGE;


	if($accessLvl=='S'){//if they are a superuser then they can edit email recipients		

	print <<<RECIPIENTSEMAILS
		<a href="recipientsAccountingEmail.php">Edit/Add Email Recipients for Accounting Department</a><br><br>
		<a href="recipientsBillingEmail_1.php">Edit/Add Email Recipients for Billing Department First Approval (PAR2)</a><br><br>
		<a href="recipientsBillingEmail_2.php">Edit/Add Email Recipients for Billing Department Final Approval (PAR1)</</a><br><br>
		
		<a href="configureEmailSendDatesAndTimes.php">Configure Send Days and Times By Status</</a><br><br>
		</center>

RECIPIENTSEMAILS;

	}
	
	print <<<ADMINPAGE
		</ul>
				<hr>

		<!-- <h2 align="center">Application Settings</h2></center> -->
ADMINPAGE;

	showFooter();

}


/*

		
		<a href="configureEmailSendDatesAndTimes_accounting.php">Configure/Customize Accounting Emails (Send Dates and Times)</</a><br><br>
		<a href="configureEmailParametersSendConditions_accounting.php">Configure/Customize Accounting Emails (Emailing Conditionally By Status)</</a><br><br>
		

		


		<a href="configureEmailSendDatesAndTimes_billing.php">Configure/Customize Billing Emails (Send Dates and Times)</</a><br><br>
		<a href="configureEmailParametersSendConditions_billing.php">Configure/Customize Billing Emails (Emailing Conditionally By Status)</</a><br>

*/


function showPage($username='', $accessLvl = '', $errors = ''){

	global $db;
	showHeader($username, $accessLvl);
	include 'pagination_functionality.php';
	//echo 'badabeeboo';
	
	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}

	//Upon initial login to the app set some of the initial global settings
		$_SESSION['RowsPerPage']=10;
		$_SESSION['initialOffset']=0;
	///////////////////////////////////////////////////////////////////////////////
	instantiate_initialOffset();


	//dump out session info for debugging purposes
	//include 'dump_all_page_contents.php';	
	//print 'Here are your new Renewal Requests: ';
	//lists all refunds open for the users department
	//show list of refunds pending approval if user is administrator


	
    print '<h2 align="center">Welcome '.$_SESSION['loginName'] .'! </h2>';

	
	$specifier="";
	$query_dept_id="SELECT dept_id FROM users WHERE user_id={$_SESSION['userid']}";
	$result_dept_id = mysqli_query($db,$query_dept_id);
	$rowquery_dept_id=mysqli_fetch_array($result_dept_id);


	$query_name="SELECT name FROM departments WHERE dept_id={$rowquery_dept_id['dept_id']}";
	$result_name = mysqli_query($db,$query_name);
	$rowquery_dept_name=mysqli_fetch_array($result_name);
	
	//BUILD SPECS AND specifier to modify sql query as necessary
	//what dept are you?
	//possible depts: PAR2,PAR1 (Billing)
	//Accounting
	
	//on creation mark as new
	
	//if PAR2 only display:
	// 'NEW' AND 'PAR2 Initial'
	//if (amt>500) --> set status to: 'PAR2 Initial'
	//else --> set status to: 'Accounting Approval'
	
	//if PAR1 only display:
	// 'ACCOUNTING APPROVED'
	
	//if Accounting only display:
	// 'ACCOUNTING APPROVAL'
	
	$specifier="";
	if(strtoupper($rowquery_dept_name[0])=='PAR2'){
		
		$specifier= " status='NEW' OR status='PAR2 Initial' AND modified_by!='{$_SESSION['userid']}' AND created_by!='{$_SESSION['userid']}' ";
	}elseif(strtoupper($rowquery_dept_name[0])=='ACCOUNTING'){
		$specifier= " status= 'ACCOUNTING APPROVAL' AND modified_by!='{$_SESSION['userid']}' AND created_by!='{$_SESSION['userid']}' ";
	}elseif(strtoupper($rowquery_dept_name[0])=='PAR1'){
		$specifier= " status= 'ACCOUNTING APPROVED' AND modified_by!='{$_SESSION['userid']}' AND created_by!='{$_SESSION['userid']}' ";
	}else{ //because currently isnt reflective of new dept status structure
		$specifier= " status='NEW' OR status='PAR2 Initial' AND modified_by!='{$_SESSION['userid']}' AND created_by!='{$_SESSION['userid']}' ";

	}		
	
	
	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

		//change user groups
	
		if(!isset($_SESSION['order'])){

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount,status,refund_id,payable,accounting_approval,billing_initial_approval,billing_final_approval,urgent 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.assigned_to = U.user_id 
			WHERE ".$specifier."
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		
		}else{
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount,status,refund_id,payable,accounting_approval,billing_initial_approval,billing_final_approval,urgent 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.assigned_to = U.user_id 
			WHERE ".$specifier."
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}


	}else{
		
		if(!isset($_SESSION['order'])){

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id,payable,accounting_approval,billing_initial_approval,billing_final_approval,urgent
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.assigned_to = U.user_id 
			WHERE ".$specifier."
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}else{
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id,payable,accounting_approval,billing_initial_approval,billing_final_approval,urgent
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.assigned_to = U.user_id 
			WHERE ".$specifier."
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

		}

	}
	
	$result = mysqli_query($db,$query); 
	$row = @mysqli_fetch_array($result);
	$sizeOfResultSet=sizeof($row);	
	
	
	//FULL RESULT SET
	$queryFullResultSet = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount,status,refund_id,payable,accounting_approval,billing_initial_approval,billing_final_approval,urgent 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.assigned_to = U.user_id 
			WHERE ".$specifier;
			
			
	$resultFull = mysqli_query($db,$queryFullResultSet); 
	$rowEntire = @mysqli_fetch_array($resultFull);
	$numResultENTIRERows=$resultFull->num_rows;
	//END FULL RESULT SET
	
	
	/*
	echo 'the query was ';
	echo $query ;
	echo '<br>';
	*/
	
	if($sizeOfResultSet){
		

	    print '<p align="center"> All '.$rowquery_dept_name[0].' Refund Requests:</p>';

		print '<div align = "center">';
		print '<table border="1" cellpadding = "3"><tr>
		<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>ENCOUNTER Number</a></b></center></td>
		<td><center><b><a href='.$_SERVER['PHP_SELF'].'?refund_id=y>Refund ID</a></b></center></td>
		<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
		<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
		<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
		<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
		<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
		<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>';

	
		
	$result = mysqli_query($db,$query); 
	$result_display_ctr=0;
	
	$numResultRows=$result->num_rows;
	
	/*
	echo 'the numResults is ';
	echo $numResultRows;
	echo '<br>';
	var_dump($result);
	*/

	while ($row = @mysqli_fetch_array($result)){
		
		$today_dt=$entered_dt=$interval=$refund_requested_by=$date_requested=$refund_assigned_to=$interval="";
		calculateInterval($row,$refund_requested_by,$date_requested,$today_dt,$entered_dt,$interval,$refund_assigned_to);
		$currentRowSize=sizeof($row);

		
		if($result_display_ctr<$_SESSION['RowsPerPage']){
	
			$result_display_ctr++;

				if($row['urgent']){
					print '<tr bgcolor=#EE0000 height=50>';
				}
				elseif($interval->days>30 && $row['status']!="COMPLETED"){
					print '<tr bgcolor=#FF69B4>';
				}elseif(($interval->days>=15 && $interval->days<30) && $row['status']!="COMPLETED"){
					print '<tr bgcolor=yellow>';
				}elseif(($interval->days<=1) && $row['status']!="COMPLETED"){
					print '<tr bgcolor=#00BB00>';
				}else{
					print '<tr>';
				}
				
				
				print '<td><a href="search_landing.php?refund_id='.$row['refund_id'].'&action=edit">'.$row['NG_enc_id'].'</a></td>';
				
				print '<td><a href="search_landing.php?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>';


				print '<td>'.$row['dt_request'].'</td>
				<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
				<td>'.$row['first_name'].' '.$row['last_name'].'</td>
				<td>'.$row['payable'].'</td>';
				print '<td>'.'$'.$row['amount'].'</td>
				<td>'.$row['status'].'</td>';
				print	'</tr>';
				
			
				
		}

		instantiate_page_variables($numResultENTIRERows,$tempOrigStartPosition,$page,$URL_String_BACK,$URL_String_FORWARD);

		//instantiate_page_variables($row,$tempOrigStartPosition,$page,$URL_String_BACK,$URL_String_FORWARD);
	}	
	
	print '</table></div>';


	
	//if ($currentRowSize>$_SESSION['RowsPerPage']){ //only conditionally display the pagination

		displayPaginationINDEX($numResultENTIRERows,$tempOrigStartPosition,$URL_String_BACK,$URL_String_FORWARD);

	//}

	
}else{
	//print message saying they have no refunds to are
	echo '<center><b>You currently have no Active Refunds for approval!</b></center>';
	
}

	if ($rowquery_dept_id['dept_id']==3){ //3 is PAR2, only PAR2 creates

		print '<h3 align="center"><a href="addrefund.php">Create a NEW Refund Request</a></h3>';
	}

	showFooter();
	
}



//refund functions below


function showEditPage($username='', $accessLvl = '', $errors = ''){ //page where user will actually edit user information

	//echo 'hi';

	showHeader($username, $accessLvl);
	global $db;
	
	?>
		<html lang="en">
		<head>
		<meta charset="utf-8">
		<title>jQuery UI Datepicker - Default functionality</title>

		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<link rel="stylesheet" href="/resources/demos/style.css">
		<script>
		$(function() {
		$( "#datepickerSTART" ).datepicker();
		});
		
		$(function() {
		$( "#datepickerEND" ).datepicker();
		});
		</script>
		</head>

		<body>
			
		</body>
		</html>
		
<?php
	
	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}

	$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, dt_required, payable, addr_ln_1, addr_ln_2, city, 
	state, zip, purpose, amount, status, comments 
	FROM refund AS R INNER JOIN users AS U 
	ON R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}'";
	$result = mysqli_query($db,$query); 
	$row = mysqli_fetch_array($result);
	
	//echo $query;
	//echo '<br>';

	print <<<EDITUSERPAGE
<h2 align="center">Edit Refund INDEX PAGE</h2>
<a href="index.php">Back to Refunds</a>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="update_refund">
      <table style="width: 100%" border="1">
        <tbody>
          <tr>
            <td>Date Required</td>

			<td><input type="text" name="dt_request" id="datepickerSTART" value ="{$row['dt_request']}"></td>

          </tr>
          <tr>
          	<td>Amount</td>
          	<td>$<input maxlength="50" name="amount" type="text" value ="{$row['amount']}"><br />
          </tr>
          <tr>
            <td>Check Payable To:</td>
            <td><input name="payable" type="text" value="{$row['payable']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 1</td>
            <td><input name="addr_ln_1" type="text" value="{$row['addr_ln_1']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 2</td>
            <td><input name="addr_ln_2" type="text" value="{$row['addr_ln_2']}">
            </td>
          </tr>
          <tr>
            <td>City</td>
            <td><input  name="city" type="text" value="{$row['city']}">
            </td>
          <tr>
            <td>State</td>
            <td><input maxlength="2" name="state" type="text" value="{$row['state']}">
            </td>
          </tr>
          <tr>
            <td>Zip</td>
            <td><input  maxlength="10" name="zip" type="text" value="{$row['zip']}">
            </td>
          </tr>
          <tr>
            <td>Encounter Number</td>
            <td><input name="enc_nbr" type="text" readonly value="{$row['NG_enc_id']}">
            </td>
          </tr>
		  <tr>
            <td>Refund ID</td>
            <td><input name="refund_id" type="text" value="{$row['refund_id']}">
            </td>
          </tr>
          <tr>
            <td>Purpose</td>
            <td><input name="purpose" type="text" value="{$row['purpose']}">
            </td>
          </tr>
          <tr>
            <td>Comments</td>
            <td><input name="comments" type="text" value="{$row['comments']}">
            </td>
          </tr>
        </tbody>
      </table>
      <input type="hidden" name="_edit_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Update Refund</button></form>
EDITUSERPAGE;
	showFooter();

}


?>