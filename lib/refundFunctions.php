<?php
//Common functions for all CHCB Patient Refund Pages

/*
	if(isset($_GET['report_id']) && sizeof($_GET['report_id'])>0){
		
	}else{
			echo 'caught here';
	die();
	
	}
	*/


if(isset($_GET['report_id']) && sizeof($_GET['report_id'])>0){
	
				
		//	echo 'refund functions top';
		//die();
			
	if($_GET['report_id']==0){
		reportAll();
	}		

	elseif($_GET['report_id']==1){
		reportCompleted();
	}
	elseif($_GET['report_id']==2){
		reportNew();
	}
	elseif($_GET['report_id']==3){
		reportAccountingApproved();
	}
	elseif($_GET['report_id']==4){
		reportBillingInitial();
	}
	elseif($_GET['report_id']==5){
		reportBillingFinal();
	}
	elseif($_GET['report_id']==6){
		reportRejected();
	}
	elseif($_GET['report_id']==7){
		reportVoided();
	}
	
}

elseif(isset($_POST)){
	

	if(isset($_POST['_assign_submit']) && $_POST['_assign_submit']!="" && $_POST['_assign_submit']!=NULL){ 
		showAssignPage(); 
	}
	elseif(isset($_POST['_del_submit']) && $_POST['_del_submit']!="" && $_POST['_del_submit']!=NULL){ 
		showDelPage(); //this function doesn't seem to exist yet.
	}
	elseif(isset($_POST['_del_submit_execute']) && $_POST['_del_submit_execute']!="" && $_POST['_del_submit_execute']!=NULL){ 
	
		execute_the_delete();

	}
	elseif(isset($_POST['_void_submit']) && $_POST['_void_submit']!="" && $_POST['_void_submit']!=NULL){ 
		
		showVoidPage(); //this function doesn't seem to exist yet.
	}elseif(isset($_POST['_void_submit_execute']) && $_POST['_void_submit_execute']!="" && $_POST['_void_submit_execute']!=NULL){ 

		execute_the_void();

	}
	elseif(isset($_POST['_rej_submit']) && $_POST['_rej_submit']!="" && $_POST['_rej_submit']!=NULL){ 
		//echo 'on refund functions page ';
		//die();
		//echo 'am i coming here';
		//die();
		showRejPage(); //this function doesn't seem to exist yet.
	}elseif(isset($_POST['_reject_submit']) && $_POST['_reject_submit']!="" && $_POST['_reject_submit']!=NULL){ 
		
		execute_the_reject();
	}

	elseif(isset($_POST['_app_submit']) && $_POST['_app_submit']!="" && $_POST['_app_submit']!=NULL){ 
	//echo 'i AM coming in here';
		//die();
		showApprovePage(); //this function doesn't seem to exist yet
	}
	
}


function showFooter(){

print <<<FOOTER
	<br><br>
	</div> 
	<center><div class="footer">
	(refundFunctions.php VERSION) <br />
	&copy; Community Health Centers of Burlington, Inc. 2014</div></center>
	</body>
	</html>
FOOTER;


/*
print <<<FOOTER
	<br><br>
	</div> 
	<center><div class="footer">
	Created by Jonathan Bowley<br />
	Enhanced by Derek Devine (refundFunctions.php version) <br />
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
	//echo $query;
	$result = mysqli_query($db,$query);
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

function validateLoginFooter($username ='', $password=''){
	global $_SESSION;	
	global $db;
	global $_POST;	

	//check password is correct for supplied username
	$query = 	"SELECT user_id, password, first_name,last_name,access_lvl FROM users WHERE username = '{$_POST['username']}'";
	$result = mysqli_query($db,$query);

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

	showFooter();
}




//Check that new user data submitted is valid that returns array of errors
function validateRefundChanges(){

	$errors = array();
	$current_date=date("Y-m-d H:i:s");  
	
	if (strlen($_POST['dt_required'])<8 || !is_numeric($_POST['dt_required']) ){
		$errors[]='Please Enter a Valid Date <br> 
		numeric only-no spaces, dashes or slashes.'; //add better date validation logic	
	}else{
	
		$refund_month=substr($_POST['dt_required'],0,2);
		$refund_day=substr($_POST['dt_required'],2,2);
		$refund_year=substr($_POST['dt_required'],4);
		
		$converted_date=date("Y-m-d H:i:s", mktime(0, 0, 0, $refund_month, $refund_day, $refund_year));
		$today_dt = new DateTime($current_date);
		$entered_dt = new DateTime($converted_date);
		
		if(!$converted_date){
			$errors[]='Please Enter a Valid Date <br> 
		numeric only-no spaces, dashes or slashes.'; //add better date validation logic	
		}
		elseif($entered_dt>$today_dt){//if the date entered is invalid because it is sometime in the future, which is not possible.
			$errors[]='Please Enter a Valid Date <br> 
			numeric only-no spaces, dashes or slashes. (cannot be a future date)'; //add better date validation logic	
		}else{
			echo 'The date entered is valid';

		}
		//$converted_date=date("Y-m-d H:i:s", $_POST['dt_required']);

	}

	if (strlen($_POST['amount'])<1){
		$errors[]='Amount cannot be blank';	
	}

	if (!is_numeric($_POST['amount'])){
		$errors[]='Amount field value must be numeric';	
	}
	
	if (strlen($_POST['payable'])<2){
		$errors[]='Payable names must be at least 2 characters long';	
	}

	if (strlen($_POST['addr_ln_1'])<1){
		$errors[]='Address Line 1 cannot be blank';	
	}

	if (strlen($_POST['city'])<1){
		$errors[]='City cannot be blank';	
	}

	if (strlen($_POST['state'])!=2){
		$errors[]='State must be exactly 2 characters long';	
	}

	if (strlen($_POST['zip'])<5){
		$errors[]='Zip code must be at least 5 characters long';	
	}

	if (strlen($_POST['enc_nbr'])<3){
		$errors[]='Encounter numbers must be at least 3 characters long';	
	}

	if (strlen($_POST['purpose'])<3){
		$errors[]='Purpose cannot be blank';	
	}


	if(sizeof($errors)) {
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
		
		//include 'dump_all_page_contents.php';
		///GET DEPT NAME//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Get the name of the department, so we can select the correct column headings
		$query_dept_id="SELECT dept_id FROM users WHERE user_id={$_SESSION['userid']}";
		$result_dept_id = mysqli_query($db,$query_dept_id);
		$rowquery_dept_id=mysqli_fetch_array($result_dept_id);

		
		$query_name="SELECT name FROM departments WHERE dept_id={$rowquery_dept_id['dept_id']}";
		$result_name = mysqli_query($db,$query_name);
		$rowquery_dept_name=mysqli_fetch_array($result_name);				
	
	///GET DEPT NAME//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		
		
		print "<table class = \"topMenu\">
		<tr>
		<td><a href=\"index.php\"  class = \"button\" >Home</td>";
		if(strtoupper($rowquery_dept_name[0])=="ACCOUNTING"){
			print "<td><a href=\"accounting_refunds.php\" class = \"button\" id = \"selected\">Assigned Refunds</a></td>";
		}ELSE{
			print "<td><a href=\"billing_refunds.php\" class = \"button\" id = \"selected\">Assigned Refunds</a></td>";

		}
		//<td><a href=\"index.php\" class = \"button\" id = \"selected\">Refunds</a></td>
		
		print "<td><a href=\"reports.php\"  class = \"button\">Reports</a></td>
		<td><a href=\"unset_search.php\"  class = \"button\">Search</a></td>		
		<td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
		
	if ($accessLvl == 'S'){
		print '<td><a href="admin.php" class = "button" >Admin</a></td></tr></table>';	
	}else {
		print '</tr></table>';
	}
		
	}


}

//Page Header (sometimes different depending on whether page has restricted access or not)
function showHeaderALL($username='',$accessLvl=''){
	
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
		
		print "<table class = \"topMenu\">
		<tr>
		<td><a href=\"index.php\"  class = \"button\" >Home</td>";
			if(strtoupper($rowquery_dept_name[0])=="ACCOUNTING"){
			print "<td><a href=\"accounting_refunds.php\" class = \"button\" id = \"selected\">Assigned Refunds</a></td>";
		}ELSE{
			print "<td><a href=\"billing_refunds.php\" class = \"button\" id = \"selected\">Assigned Refunds</a></td>";

		}
		//<td><a href=\"index.php\" class = \"button\">Refunds</a></td>
		
		print "<td><a href=\"reports.php\"  class = \"button\" id = \"selected\">Reports</a></td>
		<td><a href=\"unset_search.php\"  class = \"button\">Search</a></td>		
		<td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
	if ($accessLvl == 'S'){
		print '<td><a href="admin.php" class = "button" >Admin</a></td></tr></table>';	
	}else {
		print '</tr></table>';
	}
		
	}


}


//Page Header (sometimes different depending on whether page has restricted access or not)
function showHeaderReports($username='',$accessLvl=''){
	
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
		
		print "<table class = \"topMenu\">
		<tr>
		<td><a href=\"index.php\"  class = \"button\" >Home</td>";
		
		
		if(strtoupper($rowquery_dept_name[0])=="ACCOUNTING"){
			print "<td><a href=\"accounting_refunds.php\" class = \"button\" id = \"selected\">Assigned Refunds</a></td>";
		}ELSE{
			print "<td><a href=\"billing_refunds.php\" class = \"button\" id = \"selected\">Assigned Refunds</a></td>";

		}

		//<td><a href=\"index.php\" class = \"button\">Refunds</a></td>
		print "<td><a href=\"reports.php\"  class = \"button\" id = \"selected\">Reports</a></td>
		<td><a href=\"search_landing.php\"  class = \"button\" onclick= \" {reset_SAVE_POST();} \">Search</a></td>		
		<td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
	if ($accessLvl == 'S'){
		print '<td><a href="admin.php" class = "button" >Admin</a></td></tr></table>';	
	}else {
		print '</tr></table>';
	}
		
	}


}




//Page Header (sometimes different depending on whether page has restricted access or not)
function showHeaderSearch($username='',$accessLvl=''){
	
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
		
		print "<table class = \"topMenu\">
		<tr>
		<td><a href=\"index.php\"  class = \"button\" >Home</td>";
		
		if(strtoupper($rowquery_dept_name[0])=="ACCOUNTING"){
				print "<td><a href=\"accounting_refunds.php\" class = \"button\" id = \"selected\">Assigned Refunds</a></td>";
			}ELSE{
				print "<td><a href=\"billing_refunds.php\" class = \"button\" id = \"selected\">Assigned Refunds</a></td>";

			}
		
		//<td><a href=\"index.php\" class = \"button\">Refunds</a></td>
		print "<td><a href=\"reports.php\"  class = \"button\">Reports</a></td>
		<td><a href=\"unset_search.php\"  class = \"button\" id = \"selected\">Search</a></td>		
		<td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
	if ($accessLvl == 'S'){
		print '<td><a href="admin.php" class = "button" >Admin</a></td></tr></table>';	
	}else {
		print '</tr></table>';
	}
		
	}


}


//Page Header (sometimes different depending on whether page has restricted access or not)
function showHeaderSearchLanding($username='',$accessLvl=''){
	
	
	?>
	
	<script>
function clear_SAVE_POST() {
    $.post('reset_savePostArray.php', {}, function(response) {
        console.log(response);
    });
	   $.get('reset_savePostArray.php', {}, function(response) {
        console.log(response);
    });	  

	$.request('reset_savePostArray.php', {}, function(response) {
        console.log(response);
    });
}


	
		
</script>


<?php
/*

<!--
	$(function() {
		$( "#datepickerSTART" ).datepicker();
		});
		-->


*/


	
	print <<<HEADER
<HTML>
	<HEAD>
		<link rel="stylesheet" type="text/css" href="refundStyle.css">
		<TITLE>CHCB Patient Refund Manager</TITLE>
	</head>
	<body>
		<table id="head"><tr><td><img src = "logo.png" class="logo" /><td><td><h1 class="title">Patient Refund Manager</h1></td></tr></table>
HEADER;

//include 'dump_all_page_contents.php'; 

if( isset($_SESSION['username']) && isset($_SESSION['access']) ){
	
	$username=$_SESSION['username'];
	$accessLvl=$_SESSION['access'];
	
}

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
		<td><a href=\"index.php\"  class = \"button\" >Home</td>
		<td><a href=\"refunds.php\" class = \"button\">Refunds</a></td>
		<td><a href=\"reports.php\"  class = \"button\">Reports</a></td>
		<td><a href=\"unset_search.php\"  class = \"button\" id = \"selected\" >Search</a></td>		
		<td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
	if ($accessLvl == 'S'){
		print '<td><a href="admin.php" class = "button" >Admin</a></td></tr></table>';	
	}else {
		print '</tr></table>';
	}
	
		
	}


}

function mail_presets($to,$status){
	
		$from = "Patient Refund <noreply@chcb.org>";
		$subject = "Updated Patient Refund Request";
		$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been updated. Please login to the Patient Refund web application to review.";
		$body .="<br>Status: ".$status;
		
		$host = "ssl://smtpout.secureserver.net";
		$port = "465";


		$username = "jonathan@jonathanbowley.com";
		$password = "paw52beh";
		
		echo $from;
		echo '<br>';
		
		echo $to;
		echo '<br>';
		
		echo $subject;
		echo '<br>';
		
		echo $body;
		echo '<br>';
		
		echo $host;
		echo '<br>';
		
		echo $port;
		echo '<br>';
		
		//sendOutEmail($from, $to, $subject,$body,$host,$port);

		
}

function sendOutEmail($from, $to, $subject,$body,$host,$port){

		$headers = array ('From' => $from,'To' => $to,'Subject' => $subject);
		$smtp = Mail::factory('smtp',
		array ('host' => $host,
			'port' => $port,
			'auth' => true,
			'username' => $username,
			'password' => $password));

		//uncomment below to actually mail
		$mail = $smtp->send($to, $headers, $body);
		
		
		if (PEAR::isError($mail)) {
		 echo("<p>" . $mail->getMessage() . "</p>");
		} 				
		
}



function showEditPage($username='', $accessLvl = '', $errors = ''){ //page where user will actually edit user information

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
		
		
		//show errors at center of page
		print '<center>';
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
		echo "Please press the 'Correct Errors' below to correct these errors.";
		echo '<br>';
		echo '<br>';
		
		print <<<EDITUSERPAGE
		<a href="{$_SERVER['HTTP_REFERER']}"><button value="Correct Errors" name="Correct Errors">Correct Errors</button></a>
EDITUSERPAGE;
		print '</center>';
		
		
		

	}else{
		
	//	echo 'BOOOO';
	include 'pagination_functionality.php';	
	showHeader($username, $accessLvl);
	//global $db;
	include 'connectToDB.php'; 
		instantiate_initialOffset();	

	
	$query_dept = "SELECT dept_id FROM users WHERE user_id={$_SESSION['userid']}";
	$result_dept = mysqli_query($db,$query_dept); 
	$dept_row = mysqli_fetch_array($result_dept);
	
	$query_dept = "SELECT name FROM departments WHERE dept_id={$dept_row['dept_id']}";

	$result_deptName = mysqli_query($db,$query_dept); 
	$dept_rowName = mysqli_fetch_array($result_deptName);
	
	if ($dept_rowName['name']=="Accounting"){
	
		if(isset($_GET['refund_id'])){
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, 
			addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments, assigned_to,created_by,check_date,check_nbr,refund_id 
			FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}else{
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, 
			addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments, assigned_to,created_by,check_date,check_nbr,refund_id 
			FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_POST['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}
	}elseif ($dept_rowName['name']=="Billing"){
	
		if(isset($_GET['refund_id'])){
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, 
			addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments, assigned_to,created_by,check_date,check_nbr,refund_id 
			FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}else{
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, 
			addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments, assigned_to,created_by,check_date,check_nbr,refund_id 
			FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_POST['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}
	}

	/*
	echo 'the Query is <br>';
	echo $query;
	echo '<br>';
	*/
	
	$result = mysqli_query($db,$query); 
	$row = mysqli_fetch_array($result);
	

	///////////////////////////////////////////////////////////////////////////////////////
	//include 'dump_all_page_contents.php'; 
	
	/*
	if($row['created_by']==$_SESSION['userid']){
		echo 'whats up in da hood?';
	}
	
		print <<<EDITUSERPAGE

            <td><input name="dt_required" readonly type="text" value ="{$row['dt_required']}"> <font color=red>* Required Format: MMDDYYY (numeric only-no spaces, dashes or slashes.) </font><br>
            </td>
EDITUSERPAGE;				
	
	*/
	
	if($row['created_by']==$_SESSION['userid']){ 
	
	//echo $_GET['refund_id'];
	//echo 'that was get refund id ';
	//echo '<br>';
	//die();
	print <<<EDITUSERPAGE
<h2 align="center">Edit Refund</h2>
<a href="{$_SERVER['HTTP_REFERER'] }">Back</a>
<br/><br/>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="update_refund">
      <table style="width: 100%" border="1">
        <tbody>
			<tr>
          	<td><b>Refund ID</b></td>
          	<td><input maxlength="50" readonly name="refund_id" type="text" value ="{$row['refund_id']}"><br />
          </tr>
          <tr>
            <td>Date</td>

            <td><input name="dt_required" type="text" id="datepickerSTART" value ="{$row['dt_required']}"> 
			<font color=red>* Required Format: MMDDYYY (numeric only-no spaces, dashes or slashes.) </font><br>
            </td>
          </tr>
          <tr>
          	<td>Amount</td>
          	<td><input maxlength="50" name="amount" type="text" value ="$ {$row['amount']}"><br />
          </tr>
          <tr>
            <td>Check Payable fdsfdsTo:</td>
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
            <td><input name="enc_nbr" type="text" value="{$row['NG_enc_id']}">
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
		   <tr>
            <td>Current Status</td>
            <td><input name="status" type="text" size=25 readonly value="{$row['status']}"></td>
          </tr>
		  <tr>
            <td>Check Date</td>
            <td><input name="check_date" type="text" size=25  value="{$row['check_date']}"></td>
          </tr>
		  <tr>
            <td>Check Number</td>
            <td><input name="check_nbr" type="text" size=25  value="{$row['check_nbr']}"></td>
          </tr>

        </tbody>
      </table>
      <input type="hidden" name="_edit_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <br/>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="{$_GET['refund_id']}" name="_edit_submit">Update Refund</button>
	  
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="{$_GET['refund_id']}" name="_assign_submit">Assign Refund</button>
	  
	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="{$_GET['refund_id']}" name="_void_submit">Void Refund</button>

	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="{$_GET['refund_id']}" name="_rej_submit">Reject Refund</button>
	  
	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="{$_GET['refund_id']}" name="_app_submit">Approve Refund</button>


	  </form>
EDITUSERPAGE;

	}else{
		
	print <<<EDITUSERPAGE
<h2 align="center">EDIT Refund</h2>
<a href="reports.php">Back to Refunds</a>
<br/><br/>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="update_refund">
      <table style="width: 100%" border="1">
        <tbody>
          <tr>
            <td>Date</td>

            <td><input name="dt_required" type="text" readonly value ="{$row['dt_required']}"> <font color=red>* Required Format: MMDDYYY (numeric only-no spaces, dashes or slashes.) </font><br>
            </td>
          </tr>
          <tr>
          	<td>Amount</td>
          	<td><input maxlength="50" name="amount" type="text" readonly value ="{$row['amount']}"><br />
          </tr>
 <tr>
            <td>Check Payable fdsfdsTo:</td>
            <td><input name="payable" type="text" readonly value="{$row['payable']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 1</td>
            <td><input name="addr_ln_1" type="text" readonly value="{$row['addr_ln_1']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 2</td>
            <td><input name="addr_ln_2" type="text" readonly value="{$row['addr_ln_2']}">
            </td>
          </tr>
		   <tr>
            <td>City</td>
            <td><input  name="city" type="text" readonly value="{$row['city']}">
            </td>
          <tr>
            <td>State</td>
            <td><input maxlength="2" name="state" type="text" readonly value="{$row['state']}">
            </td>
          </tr>
          <tr>
            <td>Zip</td>
            <td><input  maxlength="10" name="zip" type="text" readonly value="{$row['zip']}">
            </td>
          </tr>
          <tr>
            <td>Encounter Number</td>
            <td><input name="enc_nbr" type="text" readonly value="{$row['NG_enc_id']}">
            </td>
          </tr>
          <tr>
            <td>Purpose</td>
            <td><input name="purpose" type="text" readonly value="{$row['purpose']}">
            </td>
          </tr>
          <tr>
            <td>Comments</td>
            <td><input name="comments" type="text"  value="{$row['comments']}">
            </td>
          </tr>
		   <tr>
            <td>Current Status</td>
            <td><input name="status" type="text" size=25 readonly value="{$row['status']}"></td>
          </tr>
		  		   <tr>
            <td>Check Date</td>
            <td><input name="check_date" type="text" size=25  value="{$row['check_date']}"></td>
          </tr>
		  		   <tr>
            <td>Check Number</td>
            <td><input name="check_nbr" type="text" size=25  value="{$row['check_nbr']}"></td>
          </tr>
		  </tbody>
      </table>
      <input type="hidden" name="_edit_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <br/>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="{$_GET['refund_id']}" name="_edit_submit">Update Refund</button>
	  
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="{$_GET['refund_id']}" name="_assign_submit">Assign Refund</button>
	  
	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="{$_GET['refund_id']}" name="_void_submit">Void Refund</button>

	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="{$_GET['refund_id']}" name="_rej_submit">Reject Refund</button>
	  
	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="{$_GET['refund_id']}" name="_app_submit">Approve Refund</button>


	  </form>
EDITUSERPAGE;
		
	
		
	}
	
	showFooter();

	}

}

function showAssignPage($username='', $accessLvl = '', $errors = ''){ //page where user will actually edit user information


	if($errors){
		
		//show errors at center of page
		print '<center>';
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
		echo "Please press the 'Correct Errors' below to correct these errors.";
		echo '<br>';
		echo '<br>';
		
		print <<<EDITUSERPAGE
		<a href="{$_SERVER['HTTP_REFERER']}"><button value="Correct Errors" name="Correct Errors">Correct Errors</button></a>
EDITUSERPAGE;
		print '</center>';

	}else{
		
		
	if(!isset($_POST['assignee']) && !$_POST['assignee']!=""){ //need to re-evaluate

			showHeader($username, $accessLvl);
			//global $db;
			include 'connectToDB.php'; 

			if(isset($_GET['refund_id'])){
				$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, 
				addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments, assigned_to, refund_id 
				FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
				

			}else{
				$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, 
				addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments, assigned_to, refund_id 
				FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_POST['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
				

			}




			$result = mysqli_query($db,$query); 
			$row = mysqli_fetch_array($result);


			$rowAssignedTo="";

			$assignedToQuery="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}' ";
			$resultAssignedTo = mysqli_query($db,$assignedToQuery); 
			$rowAssignedTo = @mysqli_fetch_array($resultAssignedTo);


			///////////////////////////////////////////////////////////////////////////////////////
			$arrayRefundUsers=array();
			$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
			$resultUserIDs = mysqli_query($db,$queryUserIDs); 

			while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){
				$arrayRefundUsers[$rowUserIds['user_id']]=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
			}


			///////////////////////////////////////////////////////////////////////////////////////


			print <<<EDITUSERPAGE
			<h2 align="center">Assign Refund</h2>
			<a href="reports.php">Back to Refunds</a>
			<br/><br/>
				<form method="POST" action="{$_SERVER['PHP_SELF']}" name="update_refund">
			  <table style="width: 100%" border="1">
				<tbody>
				<tr>
				<td><b>Refund ID</b></td>
				<td><input maxlength="50" readonly name="refund_id" type="text" value ="{$row['refund_id']}"><br />
				</tr>
				  <tr>
					<td>Date</td>
					<td><input name="dt_required" type="text" readonly value ="{$row['dt_required']}"> <font color=red>* Required Format: MMDDYYY (numeric only-no spaces, dashes or slashes.) </font><br>
					</td>
				  </tr>
				  <tr>
					<td>Amount</td>
					<td><input maxlength="50" name="amount" type="text" readonly value ="{$row['amount']}"><br />
				  </tr>
				  <tr>
					<td>Check Payable To:</td>
					<td><input name="payable" type="text" readonly value="{$row['payable']}">
					</td>
				  </tr>
				  <tr>
					<td>Address Line 1</td>
					<td><input name="addr_ln_1" type="text" readonly value="{$row['addr_ln_1']}">
					</td>
				  </tr>
				  <tr>
					<td>Address Line 2</td>
					<td><input name="addr_ln_2" type="text" readonly value="{$row['addr_ln_2']}">
					</td>
				  </tr>
				  <tr>
					<td>City</td>
					<td><input  name="city" type="text" readonly value="{$row['city']}">
					</td>
				  <tr>
					<td>State</td>
					<td><input maxlength="2" name="state" readonly type="text" value="{$row['state']}">
					</td>
				  </tr>
				  <tr>
					<td>Zip</td>
					<td><input  maxlength="10" name="zip" readonly type="text" value="{$row['zip']}">
					</td>
				  </tr>
				  <tr>
					<td>Encounter Number</td>
					<td><input name="enc_nbr" type="text" readonly value="{$row['NG_enc_id']}">
					</td>
				  </tr>
					<tr>
					<td>Refund ID</td>
					<td><input name="enc_nbr" type="text" value="{$row['refund_id']}">
					</td>
					</tr>
				  <tr>
					<td>Purpose</td>
					<td><input name="purpose" type="text" readonly value="{$row['purpose']}">
					</td>
				  </tr>
				  <tr>
					<td>Comments</td>
					<td><input name="comments" type="text" readonly value="{$row['comments']}">
					</td>
				  </tr>
				   <tr>
					<td>Current Status</td>
					<td><input name="status" type="text" size=25 readonly value="{$row['status']}"></td>
				  </tr>
				  <tr>
					<td>Current Assignee: </td>
					<td><input name="status" type="text" readonly value="{$rowAssignedTo['first_name']} {$rowAssignedTo['last_name']}"></td>
				  </tr>
				  

					<tr>
					<td>Re-Assign Refund To: </td>
					<td>
					  <select name="assignee">
EDITUSERPAGE;

					$query_users = 'SELECT user_id, first_name, last_name FROM users';
					$result_users = mysqli_query($db,$query_users);

					while($row_users = mysqli_fetch_array($result_users)){
						print "<option value=\"{$row_users['user_id']}\"";
						print ">{$row_users['first_name']} {$row_users['last_name']}</option>";	

					}
			print <<<EDITUSERPAGE
					  </select>
					  <br>
					</td>
				  </tr>

				</tbody>
			  </table>
			  <input type="hidden" name="_assign_submit" value="1" />
			  <input type="hidden" name="refund_id" value = "{$_REQUEST['refund_id']}">
			  <br/>
			  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Assign Refund</button>

			  </form>
EDITUSERPAGE;
			/*
			?>
			<select name='assigned_to' id='assigned_to'>
			<option selected='selected'>Assign Refund To:</option>
			<?php
			 foreach($arrayRefundUsers as $key => $value) {
				?>
					<option value="<?php echo $key ?>"><?php echo $value ?></option> <?php
				} 
				?>
			</select>
			<?php
			*/
			showFooter();

		}

	}
}


function showDelPage($username='', $accessLvl = '', $errors = ''){ //page where user will actually delete user information

	showHeader($username, $accessLvl);
	//global $db;
	include 'connectToDB.php'; 


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}

	if(isset($_GET['refund_id'])){
		$query = "SELECT NG_enc_id, U.first_name, U.last_name, status, dt_request, dt_required, payable, addr_ln_1, 
		addr_ln_2, city, state, zip, purpose, amount, status, comments FROM refund AS R INNER JOIN users AS U ON 
		R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
	}else{
		$query = "SELECT NG_enc_id, U.first_name, U.last_name, status, dt_request, dt_required, 
		payable, addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments 
		FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_POST['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];	
	}
	
	$result = mysqli_query($db,$query); 
	$row = mysqli_fetch_array($result);
	
	print <<<EDITUSERPAGE
<h2 align="center">DO I EVER CALL THIS PAGE?? Refund</h2>
<a href="reports.php">Back to Refunds</a>
<br/><br/>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="update_refund">
      <table style="width: 100%" border="1">
        <tbody>
		<tr>
		<td><b>Refund ID</b></td>
		<td><input maxlength="50" readonly name="refund_id" type="text" value ="{$row['refund_id']}"><br />
		</tr>
          <tr>
            <td>Date</td>
            <td><input name="dt_required" type="text" value ="{$row['dt_required']}"><font color=red>* Required Format: MMDDYYY (numeric only-no spaces, dashes or slashes.) </font><br>
            </td>
          </tr>
          <tr>
          	<td>Amount</td>
          	<td><input maxlength="50" name="amount" type="text" value ="{$row['amount']}"><br />
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
            <td><input name="enc_nbr" type="text" value="{$row['NG_enc_id']}">
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
		   <tr>
            <td>Current Status</td>
            <td><input name="status" type="text" size=25 readonly value="{$row['status']}"></td>
          </tr>
		  <tr>
            <td>Assigned To: </td>
            <td><input name="status" type="text" readonly value="{$row['assigned_to']}"></td>
          </tr>
		  
        </tbody>
      </table>
      <input type="hidden" name="_del_submit_execute" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <br/>

	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="void" name="Void">Void Refund</button>
	  </form>
EDITUSERPAGE;

	showFooter();

}


function execute_the_reject(){
	//$_POST

	showHeader($username, $accessLvl);
	//global $db;
	include 'connectToDB.php'; 
	
	
	//echo 'Executing the VOID <br>';	
	$now = date("Y-m-d H:i:s");			
	//update the record in the DB as voided
	//sets both the status field to voided as well as the voided flag to 1
	$query = "UPDATE refund SET 
	modified_by='{$_SESSION['userid']}', 
	modified_dt='{$now}', 
	status='REJECTED', 
	rejected='1' 
	WHERE refund_id = '{$_POST['refund_id']}' ";
	$result = mysqli_query($db,$query);
	$last_id = mysqli_insert_id($db);

	if (mysqli_error($result)){
		print mysqli_error($result);
	}
	
	
	///////////////////////BEGIN INSERT/////////////////////////////////////////////////////////////////////////////
				$query = "SELECT username FROM users WHERE user_id='{$_SESSION['userid']}'";
				$result = mysqli_query($db,$query);
				
				
				$rowUserNames=mysqli_fetch_array($result);
				//dynamically build the to address from the username selected based on the recipients specified by the step in the process
				$to=$rowUserNames['username'].'@chcb.org'; //build the creator email
				
				
				if($_POST['urgent']){ //verify that this works as intended
				
					$status="A Refund for ".$_POST['payable']." with a Refund ID ".$_POST['refund_id']." has been rejected. <br>  This refund is marked as URGENT.";
				

					$from = "Patient Refund <noreply@chcb.org>";
					$subject = "Updated Patient Refund Request";
					$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been rejected. Please login to the Patient Refund web application to review.";
					$body .="<br>Status: ".$status;
						
						
					echo 'the from field is <br>';
					echo $from;
					echo '<br>';
					
					echo 'the to field is <br>';
					echo $to;
					echo '<br>';
					
														
					echo 'the status is <br>';
					echo $status;
					echo '<br>';
					
					
					
					echo 'the subject is <br>';
					echo $subject;
					echo '<br>';
					
	
					echo 'the body of the email is something to the effect of: <br>';
					echo $body;
					
					echo '<br>';

				
					mail_presets($to,$status); //creator
					mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)
				}else{
		
					$status="A Refund for ".$_POST['payable']." with a Refund ID ".$_POST['refund_id']." has been rejected.";
						
	
					$from = "Patient Refund <noreply@chcb.org>";
					$subject = "Updated Patient Refund Request";
					$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been rejected. Please login to the Patient Refund web application to review.";
					$body .="<br>Status: ".$status;
						
						
					echo 'the from field is <br>';
					echo $from;
					echo '<br>';
					
					echo 'the to field is <br>';
					echo $to;
					echo '<br>';
					
														
					echo 'the status is <br>';
					echo $status;
					echo '<br>';
					
					
					
					echo 'the subject is <br>';
					echo $subject;
					echo '<br>';
					
	
					echo 'the body of the email is something to the effect of: <br>';
					echo $body;
					
					echo '<br>';

					/*
					echo $status;
					echo '<br>';
					*/
					

					mail_presets($to,$status);
					
				}
	
	
	
	///////////////END INSERT
	
	

	print '<h3 align="center"> Refund with Refund ID:  '.$_POST['refund_id'].' has been rejected!</h3>';
	print '<h4 align="center"><a href="index.php">Return to Refunds Page</a></h4>';
	echo '<br>';
	
	
}



function execute_the_void(){
	//$_POST

	showHeader($username, $accessLvl);
	//global $db;
	include 'connectToDB.php'; 
	
	
	//echo 'Executing the VOID <br>';	
	$now = date("Y-m-d H:i:s");			
	//update the record in the DB as voided
	//sets both the status field to voided as well as the voided flag to 1
	$query = "UPDATE refund SET 
	modified_by='{$_SESSION['userid']}', 
	modified_dt='{$now}', 
	status='VOIDED', 
	voided='1' 
	WHERE refund_id = '{$_POST['refund_id']}' ";
	$result = mysqli_query($db,$query);

	if (mysqli_error($result)){
		print mysqli_error($result);
	}

	print '<h3 align="center"> Refund with Refund ID:  '.$_POST['refund_id'].' has been voided!</h3>';
	print '<h4 align="center"><a href="refunds.php">Return to Refunds Page</a></h4>';
	echo '<br>';
	
	
}

function execute_the_delete(){
	//$_POST

	showHeader($username, $accessLvl);
	//global $db;
	include 'connectToDB.php'; 
	
	
	//echo 'Executing the VOID <br>';	
	$now = date("Y-m-d H:i:s");			
	//update the record in the DB as voided
	//sets both the status field to voided as well as the voided flag to 1
	$query = "UPDATE refund SET 
	modified_by='{$_SESSION['userid']}', 
	modified_dt='{$now}', 
	status='Rejected', 
	rejected='1' 
	WHERE refund_id = '{$_POST['refund_id']}' ";
	$result = mysqli_query($db,$query);

	if (mysqli_error($result)){
		print mysqli_error($result);
	}

	print '<h3 align="center"> Refund with Refund ID:  '.$_POST['refund_id'].' has been rejected!</h3>';
	print '<h4 align="center"><a href="refunds.php">Return to Refunds Page</a></h4>';
	echo '<br>';
	
	
}


function showVoidPage($username='', $accessLvl = '', $errors = ''){ //page where user will actually delete user information

	showHeader($username, $accessLvl);
	//global $db;
	include 'connectToDB.php'; 


	/*
	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	*/

	if(isset($_GET['refund_id'])){
				echo 'im in the if';

		$query = "SELECT refund_id, NG_enc_id, U.first_name, U.last_name, status, dt_request, dt_required, 
		payable, addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments 
		FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}' 
		LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
	}else{
		//echo 'im in the else';
		$query = "SELECT refund_id, NG_enc_id, U.first_name, U.last_name, status, dt_request, dt_required, 
		payable, addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments 
		FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_POST['refund_id']}' 
		LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];	
	}
	
	$result = mysqli_query($db,$query); 
	$row = mysqli_fetch_array($result);
	/*
		echo '<br>';

	echo $query;
	echo '<br>';
	*/
	//var_dump($result);


	print <<<EDITUSERPAGE
<h2 align="center">Void Refund</h2>
<a href="reports.php">Back to Refunds</a>
<br/><br/>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="update_refund">
      <table style="width: 100%" border="1">
        <tbody>
		 <tr>
		<td><b>Refund ID</b></td>
		<td><input name="refund_id" readonly type="text" value ="{$row['refund_id']}"><br>
		</td>
		</tr>
          <tr>
            <td>Date</td>
            <td><input name="dt_required" readonly type="text" value ="{$row['dt_required']}"><font color=red>* Required Format: MMDDYYY (numeric only-no spaces, dashes or slashes.) </font><br>
            </td>
          </tr>
          <tr>
          	<td>Amount</td>
          	<td><input maxlength="50" readonly name="amount" type="text" value ="$ {$row['amount']}"><br />
          </tr>
          <tr>
            <td>Check Payable To:</td>
            <td><input name="payable" readonly type="text" value="{$row['payable']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 1</td>
            <td><input name="addr_ln_1" readonly type="text" value="{$row['addr_ln_1']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 2</td>
            <td><input name="addr_ln_2" readonly type="text" value="{$row['addr_ln_2']}">
            </td>
          </tr>
          <tr>
            <td>City</td>
            <td><input  name="city" readonly type="text" value="{$row['city']}">
            </td>
          <tr>
            <td>State</td>
            <td><input maxlength="2" name="state" readonly type="text" value="{$row['state']}">
            </td>
          </tr>
          <tr>
            <td>Zip</td>
            <td><input  maxlength="10" name="zip" readonly type="text" value="{$row['zip']}">
            </td>
          </tr>
          <tr>
            <td>Encounter Number</td>
            <td><input name="enc_nbr" type="text" readonly value="{$row['NG_enc_id']}">
            </td>
          </tr>
          <tr>
            <td>Purpose</td>
            <td><input name="purpose" type="text" readonly value="{$row['purpose']}">
            </td>
          </tr>
          <tr>
            <td>Comments</td>
            <td><input name="comments" type="text" readonly value="{$row['comments']}">
            </td>
          </tr>
		   <tr>
            <td>Current Status</td>
            <td><input name="status" type="text" size=25 readonly value="{$row['status']}"></td>
          </tr>

        </tbody>
      </table>
      <input type="hidden" name="_void_submit_execute" value="1" />

	  <br/>

	 
	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="void" name="Void">Void Refund</button>
	  </form>
EDITUSERPAGE;


	showFooter();

}

function showRejPage($username='', $accessLvl = '', $errors = ''){ //page where user will actually delete user information

	showHeader($username, $accessLvl);
	//global $db;
	include 'connectToDB.php'; 



	if(isset($_GET['refund_id'])){
		$query = "SELECT refund_id, NG_enc_id, U.first_name, U.last_name, status, dt_request, dt_required, 
		payable, addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments 
		FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}' 
		LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];		
	}else{
		$query = "SELECT refund_id, NG_enc_id, U.first_name, U.last_name, status, dt_request, dt_required, 
		payable, addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments 
		FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_POST['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];	
	}
	
	$result = mysqli_query($db,$query); 
	$row = mysqli_fetch_array($result);
	
	/*
	echo $query;
	echo '<br>';
	var_dump($result);
	
		var_dump($row);
		*/
	

	/*
	
	echo 'hiya';
	echo '<br>';
	*/

	print <<<EDITUSERPAGE
<h2 align="center">Reject The Refund</h2>
<a href="reports.php">Back to Refunds</a>
<br/><br/>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="update_refund">
      <table style="width: 100%" border="1">
        <tbody>
		   <tr>
          	<td><b>Refund ID</b></td>
          	<td><input maxlength="50" readonly name="refund_id" type="text" value ="{$row['refund_id']}"><br />
          </tr>
          <tr>
            <td>Date</td>
            <td><input name="dt_required" readonly type="text" value ="{$row['dt_required']}"><font color=red>* Required Format: MMDDYYY (numeric only-no spaces, dashes or slashes.) </font><br>
            </td>
          </tr>
          <tr>
          	<td>Amount</td>
          	<td><input maxlength="50" readonly name="amount" type="text" value ="$ {$row['amount']}"><br />
          </tr>
          <tr>
            <td>Check Payable To:</td>
            <td><input name="payable" readonly type="text" value="{$row['payable']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 1</td>
            <td><input name="addr_ln_1" readonly type="text" value="{$row['addr_ln_1']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 2</td>
            <td><input name="addr_ln_2" readonly type="text" value="{$row['addr_ln_2']}">
            </td>
          </tr>
          <tr>
            <td>City</td>
            <td><input  name="city" readonly type="text" value="{$row['city']}">
            </td>
          <tr>
            <td>State</td>
            <td><input maxlength="2" name="state" readonly type="text" value="{$row['state']}">
            </td>
          </tr>
          <tr>
            <td>Zip</td>
            <td><input  maxlength="10" name="zip" readonly type="text" value="{$row['zip']}">
            </td>
          </tr>
          <tr>
            <td>Encounter Number</td>
            <td><input name="enc_nbr" type="text" readonly value="{$row['NG_enc_id']}">
            </td>
          </tr>
          <tr>
            <td>Purpose</td>
            <td><input name="purpose" type="text" readonly value="{$row['purpose']}">
            </td>
          </tr>
          <tr>
            <td>Comments</td>
            <td><input name="comments" type="text" readonly value="{$row['comments']}">
            </td>
          </tr>
		   <tr>
            <td>Current Status</td>
            <td><input name="status" type="text" size=25 readonly value="{$row['status']}"></td>
          </tr>

        </tbody>
      </table>
      <input type="hidden" name="_reject_submit" value="1" />

	  <br/>

	 
	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="reject" name="Reject">REJECT Refund</button>
	  </form>
EDITUSERPAGE;

	die();

	showFooter();

	//echo 'below here ';
}



function showApprovePage($username='', $accessLvl = '', $errors = ''){ //page where user will actually approve user information

	showHeader($username, $accessLvl);
	//global $db;
	include 'connectToDB.php'; 

	/*
	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	*/
	
	
	$arrayRefundUsers=array();
	$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 

	if(isset($_GET['refund_id'])){
		$query = "SELECT NG_enc_id, U.first_name, U.last_name, status, dt_request, dt_required, payable, 
		addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments,refund_id FROM refund 
		AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
	}else{
		$query = "SELECT NG_enc_id, U.first_name, U.last_name, status, dt_request, dt_required, payable, 
		addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments,refund_id FROM refund 
		AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_POST['refund_id']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		
	}
	
	$result = mysqli_query($db,$query); 
	$row = mysqli_fetch_array($result);
	


	print <<<EDITUSERPAGE
<h2 align="center">Approve Refund</h2>
<a href="reports.php">Back to Refunds</a>
<br/><br/>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="update_refund">
      <table style="width: 100%" border="1">
        <tbody>
		<tr>
		<td><b>Refund ID</b></td>
		<td><input maxlength="50" readonly name="refund_id" type="text" value ="{$row['refund_id']}"><br />
		</tr>
          <tr>
            <td>Date</td>
            <td><input name="dt_required" type="text" readonly value ="{$row['dt_required']}"><font color=red>* Required Format: MMDDYYY (numeric only-no spaces, dashes or slashes.) </font><br>
            </td>
          </tr>
          <tr>
          	<td>Amount</td>
          	<td><input maxlength="50" name="amount" readonly type="text" value ="{$row['amount']}"><br />
          </tr>
          <tr>
            <td>Check Payable To:</td>
            <td><input name="payable" type="text" readonly value="{$row['payable']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 1</td>
            <td><input name="addr_ln_1" type="text" readonly value="{$row['addr_ln_1']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 2</td>
            <td><input name="addr_ln_2" type="text" readonly value="{$row['addr_ln_2']}">
            </td>
          </tr>
          <tr>
            <td>City</td>
            <td><input  name="city" type="text" readonly value="{$row['city']}">
            </td>
          <tr>
            <td>State</td>
            <td><input maxlength="2" name="state" type="text" readonly value="{$row['state']}">
            </td>
          </tr>
          <tr>
            <td>Zip</td>
            <td><input  maxlength="10" name="zip" type="text" readonly value="{$row['zip']}">
            </td>
          </tr>
          <tr>
            <td>Encounter Number</td>
            <td><input name="enc_nbr" type="text" readonly value="{$row['NG_enc_id']}">
            </td>
          </tr>
          <tr>
            <td>Purpose</td>
            <td><input name="purpose" type="text" readonly value="{$row['purpose']}">
            </td>
          </tr>
          <tr>
            <td>Comments</td>
            <td><input name="comments" type="text" readonly value="{$row['comments']}">
            </td>
          </tr>
		   <tr>
            <td>Current Status</td>
            <td><input name="status" type="text" size=25 readonly value="{$row['status']}"></td>
          </tr>

		  
        </tbody>
      </table>
      <input type="hidden" name="_approve_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$row['refund_id']}">

	  <br/>
	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="approve" name="Approve">APPROVE Refund</button>
	  </form>
EDITUSERPAGE;
	showFooter();

}


function approveTheRefund(){
		
		
			global $db;

			$now = date("Y-m-d H:i:s");			
			//update the record in the DB as Approved
	
			
			$query = "SELECT dept_id from users WHERE user_id='{$_SESSION['userid']}'";
			$result = mysqli_query($db,$query);

				
			$current_user_dept_id="";
			$department_name="";
			$accouting_approval="";
			$billing_approval="";


				
			while ($row = mysqli_fetch_array($result)){
				$current_user_dept_id=$row['dept_id'];
			}
						
			$query = "SELECT name from departments WHERE dept_id=$current_user_dept_id";
			$result = mysqli_query($db,$query);
			


			while ($row = @mysqli_fetch_array($result)){
				$department_name=$row['name'];
			}
			


			if($department_name=="Accounting"){
				
					$billing_initial_approval=0;
					$billing_final_approval=0;
					$accounting_approval=0;

					$queryCheckStatus = "SELECT accounting_approval,billing_initial_approval,billing_final_approval WHERE refund_id = {$_POST['refund_id']} ";
					$resultCheckStatus = mysqli_query($db,$queryCheckStatus);

					while ($rowCheckStatus = @mysqli_fetch_array($resultCheckStatus)){
						$billing_initial_approval=$rowCheckStatus['billing_initial_approval'];
						$billing_final_approval=$rowCheckStatus['billing_final_approval'];
						$accounting_approval=$rowCheckStatus['accounting_approval'];
					}
					
					if(!$accounting_approval){
						$query = "UPDATE refund 
							SET status='ACCOUNTING APPROVED',
							modfied_by={$_SESSION['userid']}, 
							modified_dt='{$now}',
							accounting_approval=1,
							voided =0 
						WHERE refund_id = {$_POST['refund_id']} ";
					
						
						$result = mysqli_query($db,$query); //execute the update
						
						//TRACK CHANGES
						IF ($billing_initial_approval){ //WAS IN THIS STAGE BEFORE THE ABOVE UPDATE
						
							$status_before='ACCOUNTING APPROVAL';
							$status_after='ACCOUNTING APPROVED';
							
							trackRefundChanges($_POST,$status_before,$status_after);				
							//$queryStatusChange = "INSERT INTO refund_changes (refund_id, status_before, status_after, date, name) VALUES ('{$_POST['refund_id']}','ACCOUNTING APPROVAL','ACCOUNTING APPROVED','{$now}','{$_SESSION['userid']}'";
							//$result = mysqli_query($db,$queryStatusChange);
						}
						
						
						//TRACK THE CHANGES
						
						echo 'the result was <br>';
						echo $query;

					}
					
					
					
				//select info to build up email for creator notification of rejection//////////////////////////////////////////////////////////////////////////////////////
				$created_by="";	
				$status="";
				$is_urgent="";
				$payable_to="";
				$query = "SELECT created_by,urgent,payable FROM refund WHERE refund_id = {$_POST['refund_id']} ";
				$result = mysqli_query($db,$query);
				
					while ($row = mysqli_fetch_array($result)){
						$created_by=$row['created_by'];
						$is_urgent=$row['urgent'];
						$payable_to=$row['payable'];
					}
					
				$queryUsername = "SELECT username FROM users WHERE user_id='{$created_by}'";
				$resultUsername = mysqli_query($db,$queryUsername);

				$rowUsername=mysqli_fetch_array($resultUsername);
				$to=$rowUsername['username'].'@chcb.org';
				
				//send notification that a refund has been accounting approved: call mail_presets
				//RULE: ON Accounting Approval:
				//IF urgent status email both creator and Erika, otherwise just email Erika///////////////////////////////////////////
				//Email Three people from PAR1 -->Laura W., E.B., and Kim F. //
				
				if($is_urgent){ //verify that this works as intended
				
					//$last_id = mysqli_insert_id($db);
					$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has been approved by accounting, and is awaiting PAR1 Completion. <br>  This refund was marked as URGENT.";
						
					mail_presets($to,$status); //creator
					mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)
					
					//email PAR1 recipients
					mail_presets("lwheatley@chcb.org",$status); //email 
					mail_presets("kfuller@chcb.org",$status); //email 


					
				}else{
		
					//$last_id = mysqli_insert_id($db);
					$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has been approved by accounting,and is awaiting PAR1 Completion.";
						
					mail_presets($to,$status); //creator
					mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)		
					
					//email PAR1 recipients
					mail_presets("lwheatley@chcb.org",$status); //email 
					mail_presets("kfuller@chcb.org",$status); //email 

					//email PAR1 three recipients

				}
								
				//END Send Emails Upon Accounting Approval ////////////////////////////////////////////////////////////////////////////////////////
		
					
			}
			elseif(trim($department_name)=="Billing"){
				
					$billing_initial_approval=0;
					$accounting_approval=0;
					$billing_final_approval=0;
					$current_status="";
					$refund_amt=0;
				
					$queryCheckStatus = "SELECT amount,status,accounting_approval, billing_initial_approval,billing_final_approval FROM refund WHERE refund_id = '{$_POST['refund_id']}' ";
					$resultCheckStatus = mysqli_query($db,$queryCheckStatus);

					while ($rowCheckStatus = @mysqli_fetch_array($resultCheckStatus)){
						$billing_initial_approval=$rowCheckStatus['billing_initial_approval'];
						$accounting_approval=$rowCheckStatus['accounting_approval'];
						$billing_final_approval=$rowCheckStatus['billing_final_approval'];
						$status=$rowCheckStatus['status'];
						$refund_amt=$rowCheckStatus['amount'];
					}
					
					//THREE MAJOR USE CASES: ///////////////////////////////////////////////////////////////////////////////////////////////////////////
					//BILLING:
					//IF THE STATUS WAS MARKED AS 'NEW' YOU NOW MARK IT AS: 'ACCOUNTING APPROVAL'
					
					//IF THE STATUS WAS MARKED AS 'ACCOUNTING APPROVED'
					//--> IF AMT <= 500 MARK AS 'COMPLETED' (BUT check that all: $billing_initial_approval,$accounting_approval, and $billing_final_approval are set)
					//--> IF AMT > 500 MARK AS 'BILLING APPROVED'
					
					//IF THE STATUS WAS MARKED AS BILLING APPROVED
					//UPDATE THE STATUS AS COMPLETED (BUT check that all: $billing_initial_approval,$accounting_approval, and $billing_final_approval are set)
					
					
					//IF THE STATUS WAS MARKED AS 'NEW' YOU NOW MARK IT AS: 'ACCOUNTING APPROVAL'
					if($status=='NEW'){
						
								$created_by="";	
								$status="";
								$is_urgent="";
								$payable_to="";
								$query = "SELECT created_by,urgent,payable FROM refund WHERE refund_id = '{$_POST['refund_id']}' ";
								$result = mysqli_query($db,$query);
								
							//	echo 'the new query is <br>';
							//	echo $query;
								
									while ($row = mysqli_fetch_array($result)){
										$created_by=$row['created_by'];
										$is_urgent=$row['urgent'];
										$payable_to=$row['payable'];
									}
									
								$queryUsername = "SELECT username FROM users WHERE user_id='{$created_by}'";
								$resultUsername = mysqli_query($db,$queryUsername);

								$rowUsername=mysqli_fetch_array($resultUsername);
								$to=$rowUsername['username'].'@chcb.org';
						
						//send to accounting approval and mark as billing initially approved
						$query = "UPDATE refund 
							SET status='ACCOUNTING APPROVAL',
							modified_by={$_SESSION['userid']}, 
							modified_dt='{$now}',
							billing_initial_approval=1,
							voided =0 
						WHERE refund_id = '{$_POST['refund_id']}' ";
						

						$result = mysqli_query($db,$query); //execute the update

												
						//TRACK CHANGES
							$status_before=$status;
							$status_after='ACCOUNTING APPROVAL';
							
							trackRefundChanges($_POST,$status_before,$status_after);		
					
						//TRACK THE CHANGES

						////////

						$status="A Refund for ".$_POST['payable']." with a Refund ID ".$_POST['refund_id']." has initial approval by PAR2. <br> ";


						$from = "Patient Refund <noreply@chcb.org>";
						$subject = "Updated Patient Refund Request";
						$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been updated. Please login to the Patient Refund web application to review.";
						$body .="<br>Status: ".$status;


						echo 'the from field is <br>';
						echo $from;
						echo '<br>';

						echo 'the to field is <br>';
						echo $to;
						echo '<br>';

						echo 'the subject is <br>';
						echo $subject;
						echo '<br>';

						echo 'the body of the email is something to the effect of: <br>';
						echo $body;

						echo '<br>';

						
						/////////
						
						
								if($is_urgent){ //verify that this works as intended
								
									//$last_id = mysqli_insert_id($db);
									$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has initial approval by PAR2. <br> It has been marked as URGENT. "; 
										
									mail_presets($to,$status); //creator
									mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)
									
									//email PAR1 recipients
									mail_presets("lwheatley@chcb.org",$status); //email 
									mail_presets("kfuller@chcb.org",$status); //email 
	
								}else{
						
									//$last_id = mysqli_insert_id($db);
									$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has initial approval by PAR2. <br> "; 
										
									mail_presets($to,$status); //creator
									mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)	
									
									//email PAR1 recipients
									mail_presets("lwheatley@chcb.org",$status); //email 
									mail_presets("kfuller@chcb.org",$status); //email 
																	
									
								}

					}elseif($status=='ACCOUNTING APPROVED'){
						//IF accounting approved, then billers can only have two possible actions <500 or not

						//mark as completed if meets conditions
						//$billing_initial_approval $accounting_approval $billing_final_approval
						if($refund_amt<=500 && billing_initial_approval && $accounting_approval && $billing_final_approval){
						//if($refund_amt<=500){
							$query = "UPDATE refund 
							SET status='COMPLETED',
							modified_by={$_SESSION['userid']}, 
							modified_dt='{$now}',
							voided =0 
						WHERE refund_id = {$_POST['refund_id']} ";
						
						$result = mysqli_query($db,$query); //execute the update

						//TRACK CHANGES
							$status_before=$status;
							$status_after='COMPLETED';
							trackRefundChanges($_POST,$status_before,$status_after);		
						//TRACK THE CHANGES

						
							//SEND OFF THE APPROPRIATE NOTIFICATION EMAILS
							//select info to build up email for creator notification//////////////////////////////////////////////////////////////////////////////////////
							$created_by="";	
							$status="";
							$is_urgent="";
							$payable_to="";
							$query = "SELECT created_by,urgent,payable FROM refund WHERE refund_id = {$_POST['refund_id']} ";
							$result = mysqli_query($db,$query);
							
								while ($row = mysqli_fetch_array($result)){
									$created_by=$row['created_by'];
									$is_urgent=$row['urgent'];
									$payable_to=$row['payable'];
								}
								
							$queryUsername = "SELECT username FROM users WHERE user_id='{$created_by}'";
							$resultUsername = mysqli_query($db,$queryUsername);

							$rowUsername=mysqli_fetch_array($resultUsername);
							$to=$rowUsername['username'].'@chcb.org';
							
							//send notification that a refund has been billing approved: call mail_presets
							//RULE: ON Billing Approval:
							//IF urgent status email both creator and Erika, otherwise just email Erika///////////////////////////////////////////
							//Email Three people from PAR1 -->Laura W., E.B., and Kim F. //
							
							if($is_urgent){ //verify that this works as intended
							
								//$last_id = mysqli_insert_id($db);
								$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has been approved by billing and is now marked as completed. <br>  This refund was marked as URGENT.";
									
								mail_presets($to,$status); //creator
								mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)
								
								//email PAR1 recipients
								mail_presets("lwheatley@chcb.org",$status); //email 
								mail_presets("kfuller@chcb.org",$status); //email 


							}else{
					
								//$last_id = mysqli_insert_id($db);
								$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has been approved by billing and is now marked as completed.";
									
								mail_presets($to,$status); //creator
								mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)		
								
								//email PAR1 recipients
								mail_presets("lwheatley@chcb.org",$status); //email 
								mail_presets("kfuller@chcb.org",$status); //email 

								
							}

						}else{ //if greater than 500, update with second approval
							//status is set as BILLING APPROVED as soon as either PAR2 verifies and accounting, 
							//or PAR2 double verifies and accounting
								$query = "UPDATE refund 
							SET status='BILLING APPROVED',
							modified_by={$_SESSION['userid']}, 
							modified_dt='{$now}',
							billing_final_approval=1,
							voided =0 
						WHERE refund_id = {$_POST['refund_id']} ";
						
						$result = mysqli_query($db,$query); //execute the update
						
						//TRACK CHANGES
							$status_before=$status;
							$status_after='BILLING APPROVED';
							trackRefundChanges($_POST,$status_before,$status_after);		
						//TRACK THE CHANGES						
						
							//SEND OFF THE APPROPRIATE NOTIFICATION EMAILS
							//select info to build up email for creator notification//////////////////////////////////////////////////////////////////////////////////////
							$created_by="";	
							$status="";
							$is_urgent="";
							$payable_to="";
							$query = "SELECT created_by,urgent,payable FROM refund WHERE refund_id = {$_POST['refund_id']} ";
							$result = mysqli_query($db,$query);
							
								while ($row = mysqli_fetch_array($result)){
									$created_by=$row['created_by'];
									$is_urgent=$row['urgent'];
									$payable_to=$row['payable'];
								}
								
							$queryUsername = "SELECT username FROM users WHERE user_id='{$created_by}'";
							$resultUsername = mysqli_query($db,$queryUsername);

							$rowUsername=mysqli_fetch_array($resultUsername);
							$to=$rowUsername['username'].'@chcb.org';
							
							//send notification that a refund has been billing approved: call mail_presets
							//RULE: ON Billing Approval:
							//IF urgent status email both creator and Erika, otherwise just email Erika///////////////////////////////////////////
							//Email Three people from PAR1 -->Laura W., E.B., and Kim F. //
							
							if($is_urgent){ //verify that this works as intended
							
								//$last_id = mysqli_insert_id($db);
								$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has received dual approval by Billing and is awaiting final completion by PAR1. <br>  This refund was marked as URGENT.";
									
								mail_presets($to,$status); //creator
								mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)
								
								//email PAR1 recipients
								mail_presets("lwheatley@chcb.org",$status); //email 
								mail_presets("kfuller@chcb.org",$status); //email 

							}else{
					
								//$last_id = mysqli_insert_id($db);
								$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." received dual approval by Billing and is awaiting final completion by PAR1.";
									
								mail_presets($to,$status); //creator
								mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)		
								
								//email PAR1 recipients
								mail_presets("lwheatley@chcb.org",$status); //email 
								mail_presets("kfuller@chcb.org",$status); //email 
								
								
							}
						
						}//end else amount > 500
						
					}elseif($status=='BILLING APPROVED'){
						//if status is billing approved and you are a biller the only possible action is to complete
						
						//contingent on all other approvals
						if(billing_initial_approval && $accounting_approval && $billing_final_approval){
									$query = "UPDATE refund 
									SET status='COMPLETED',
									modified_by={$_SESSION['userid']}, 
									modified_dt='{$now}',
									voided =0 
								WHERE refund_id = {$_POST['refund_id']} ";
								
								$result = mysqli_query($db,$query); //execute the update
						
						}
						
						//TRACK CHANGES
							$status_before=$status;
							$status_after='COMPLETED';
							trackRefundChanges($_POST,$status_before,$status_after);		
						//TRACK THE CHANGES		

						//SEND OFF THE APPROPRIATE NOTIFICATION EMAILS
						//select info to build up email for creator notification of rejection//////////////////////////////////////////////////////////////////////////////////////
						$created_by="";	
						$status="";
						$is_urgent="";
						$payable_to="";
						$query = "SELECT created_by,urgent,payable FROM refund WHERE refund_id = {$_POST['refund_id']} ";
						$result = mysqli_query($db,$query);
						
							while ($row = mysqli_fetch_array($result)){
								$created_by=$row['created_by'];
								$is_urgent=$row['urgent'];
								$payable_to=$row['payable'];
							}
							
						$queryUsername = "SELECT username FROM users WHERE user_id='{$created_by}'";
						$resultUsername = mysqli_query($db,$queryUsername);

						$rowUsername=mysqli_fetch_array($resultUsername);
						$to=$rowUsername['username'].'@chcb.org';
						
						//send notification that a refund has been accounting approved: call mail_presets
						//RULE: ON Billing Approval:
						//IF urgent status email both creator and Erika, otherwise just email Erika///////////////////////////////////////////
						//Email Three people from PAR1 -->Laura W., E.B., and Kim F. //
						
						if($is_urgent){ //verify that this works as intended
						
							//$last_id = mysqli_insert_id($db);
							$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has been marked as completed by billing. <br>  This refund was marked as URGENT.";
								
							mail_presets($to,$status); //creator
							mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)
							
							//email PAR1 recipients
							mail_presets("lwheatley@chcb.org",$status); //email 
							mail_presets("kfuller@chcb.org",$status); //email 

						}else{
				
							//$last_id = mysqli_insert_id($db);
							$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has been marked as completed by billing.";
								
							mail_presets($to,$status); //creator
							
							mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)		
							//email PAR1 recipients
							mail_presets("lwheatley@chcb.org",$status); //email 
							mail_presets("kfuller@chcb.org",$status); //email 
							

						}
								
						
					}

					//THREE MAJOR USE CASES: ///////////////////////////////////////////////////////////////////////////////////////////////////////////
					
			}else{//this means they are an admin with either Approver or SuperUser status, either way they have override approval abilities for purposes of this app
				//haven't fully flushed out this use case	
					
					$query = "UPDATE refund 
						SET status='APPROVED', 
						modified_by={$_SESSION['userid']}, 
						modified_dt='{$now}',
						voided =0				
					WHERE refund_id = {$_POST['refund_id']} ";
			}
			

			$result = mysqli_query($db,$query);
			if (@mysqli_error($result)){
				print mysqli_error($result);
			}
			
			
				//START Derek Hack////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//Hack inserted because app previously wasn't immediately displaying changes, forces a refresh of page if the form has been submitted.
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				if(isset($_POST['_app_submit']) && $_POST['Approve']=="approve"){
					
					//include 'dump_all_page_contents.php'; 
					//build up the redirect string to redirect to the edit page of the refund you are currently editing (needs to reference correct refund_id)
					//format:
					//?refund_id=3&action=delete
					$refresh_id="?refund_id=";
					$refresh_id.=$_POST['refund_id'];
					$refresh_id.="&action=approve";
				
					Header('Location: '.$_SERVER['PHP_SELF'].$refresh_id);
					
				}
				//END Derek Hack//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			

			//show successful void message
			print '<h3 align="center"> Refund for  '.$_POST['payable'].' has been successfully Approved!</h3>';
			print '<h4 align="center"><a href="index.php">Return to Refunds Page</a></h4>';
			
			//echo "I'm about to approve you.";
			//die();
		
}



function showPage($username='', $accessLvl = '', $errors = ''){ //page where user will select user to edit

	//echo 'this is being called ';
	
	global $db;
	showHeader($username, $accessLvl);
	include 'connectToDB.php'; 
	include 'pagination_functionality.php'; 
	
	//include 'dump_all_page_contents.php'; 
	
	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	
	/*
	if(!isset($_GET['page_number'])){
		//echo 'settign the get page number <br>';
		$_GET['page_number']=0;
		
		//echo 'im here';
		//die();
	}
	instantiate_initialOffset();
	*/
	
	$query_dept = "SELECT dept_id FROM users WHERE user_id={$_SESSION['userid']}";
	$result_dept = mysqli_query($db,$query_dept); 
	$dept_row = mysqli_fetch_array($result_dept);
	
	//echo $query_dept ;
	
	$query_dept = "SELECT name FROM departments WHERE dept_id={$dept_row['dept_id']}";

	$result_deptName = mysqli_query($db,$query_dept); 
	$dept_rowName = mysqli_fetch_array($result_deptName);
	
	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

	if ($dept_rowName['name']=="Accounting"){
	
		if(isset($_GET['refund_id'])){
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, 
			addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments, assigned_to,created_by,check_date,check_nbr 
			FROM refund AS R 
			INNER JOIN users AS U 
			ON R.created_by= U.user_id 
			WHERE refund_id = '{$_GET['refund_id']}' 
			AND (status='ACCOUNTING APPROVAL') 
			AND assigned_to='{$_SESSION['userid']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}else{
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, 
			addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments, assigned_to,created_by,check_date,check_nbr 
			FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_POST['refund_id']}' 
			AND (status='ACCOUNTING APPROVAL') 
			AND assigned_to='{$_SESSION['userid']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}
	}elseif($dept_rowName['name']=="Billing"){
		
				if(isset($_GET['refund_id'])){
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, 
			addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments, assigned_to,created_by,check_date,check_nbr 
			FROM refund AS R 
			INNER JOIN users AS U 
			ON R.created_by= U.user_id 
			WHERE refund_id = '{$_GET['refund_id']}' 
			AND assigned_to='{$_SESSION['userid']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}else{
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, 
			addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments, assigned_to,created_by,check_date,check_nbr 
			FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id 
			WHERE refund_id = '{$_POST['refund_id']}' 
			AND assigned_to='{$_SESSION['userid']}' LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}
		
		
	}
	
	
	

		if(!isset($_SESSION['order'])){
				if ($dept_rowName['name']=="Accounting"){

					$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
					FROM refund AS R 
					INNER JOIN 
					users AS U 
					ON R.created_by = U.user_id 
					WHERE status !='deleted' AND status !='VOIDED' AND (status='ACCOUNTING APPROVAL') AND assigned_to='{$_SESSION['userid']}'  
					ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
					
				}elseif($dept_rowName['name']=="Billing"){
					
					$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
					FROM refund AS R 
					INNER JOIN 
					users AS U 
					ON R.created_by = U.user_id 
					WHERE status !='deleted' AND status !='VOIDED' AND (status='ACCOUNTING APPROVED' OR status='NEW') assigned_to='{$_SESSION['userid']}'  
					ORDER BY dt_request,U.last_name,status AND LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
					
				}
			
		}else{

				if ($dept_rowName['name']=="Accounting"){

					$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
					FROM refund AS R 
					INNER JOIN 
					users AS U 
					ON R.created_by = U.user_id 
					WHERE status !='deleted' AND status !='VOIDED' AND (status='ACCOUNTING APPROVAL') AND assigned_to='{$_SESSION['userid']}' 
					ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
					
				}elseif($dept_rowName['name']=="Billing"){
					$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
					FROM refund AS R 
					INNER JOIN 
					users AS U 
					ON R.created_by = U.user_id 
					WHERE status !='deleted' AND status !='VOIDED' AND (status='ACCOUNTING APPROVED' OR status='NEW') AND assigned_to='{$_SESSION['userid']}' 
					ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
					
				}
		}


	}else{ //else access levels

		if(!isset($_SESSION['order'])){
		
				if ($dept_rowName['name']=="Accounting"){

					$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
					accounting_approval,billing_initial_approval,billing_final_approval,urgent 
					FROM refund AS R 
					INNER JOIN 
					users AS U 
					ON R.created_by = U.user_id 
					WHERE status !='deleted' AND status !='VOIDED' AND (status='ACCOUNTING APPROVAL') AND assigned_to='{$_SESSION['userid']}'    
					ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
				}elseif($dept_rowName['name']=="Billing"){
					
					$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
					accounting_approval,billing_initial_approval,billing_final_approval,urgent 
					FROM refund AS R 
					INNER JOIN 
					users AS U 
					ON R.created_by = U.user_id 
					WHERE status !='deleted' AND status !='VOIDED' AND (status='ACCOUNTING APPROVED' OR status='NEW') AND assigned_to='{$_SESSION['userid']}'    
					ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
					
					
				}
		
		}else{

				if ($dept_rowName['name']=="Accounting"){

					$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
					accounting_approval,billing_initial_approval,billing_final_approval,urgent 			
					FROM refund AS R 
					INNER JOIN 
					users AS U 
					ON R.created_by = U.user_id 
					WHERE status !='deleted' AND status !='VOIDED' AND (status='ACCOUNTING APPROVAL') AND assigned_to='{$_SESSION['userid']}'   
					ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
				
				}elseif($dept_rowName['name']=="Billing"){
					
					$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
					accounting_approval,billing_initial_approval,billing_final_approval,urgent 
					FROM refund AS R 
					INNER JOIN 
					users AS U 
					ON R.created_by = U.user_id 
					WHERE status !='deleted' AND status !='VOIDED' and (status='ACCOUNTING APPROVED' OR status='NEW') AND assigned_to='{$_SESSION['userid']}'    
					ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
					
					
				}

		}
		

	}
	


	$result = mysqli_query($db,$query); 

	$arrayRefundUsers=array();
	

	
	$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 

		
	$ctr=0;
	while ($row = mysqli_fetch_array($resultUserIDs)){

		$arrayRefundUsers[$row['user_id']]=$row['first_name'].' '.$row['last_name'];
	}
	
	
	$row = mysqli_fetch_array($result);
	
	print '<br /><br /><div align = "center"><b><h2>Assigned Refunds </h2></b>';

	if(sizeof($row)){
	///////HEADINGS FROM THE REFUNDS PAGE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '<div align = "center"><p>Refund Requests Currently Assigned to you: </p><br>';

	print '<table border="1" cellpadding = "3">
	<tr>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?refund_id=y>Refund ID</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?urgent=y>Urgent</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
	;	
	///////END HEADINGS FROM THE REFUNDS PAGE////////////////////?////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/*
		<td><b><center>Actions</center></b></td>
	*/

	$result_display_ctr=0;
		
	$current_date=date("Y-m-d H:i:s");  
	$result = mysqli_query($db,$query); 

	while ($row = mysqli_fetch_array($result)){
		
		$today_dt=$entered_dt=$interval=$refund_requested_by=$date_requested=$refund_assigned_to=$interval="";
		calculateInterval($row,$refund_requested_by,$date_requested,$today_dt,$entered_dt,$interval,$refund_assigned_to);
		//$refund_assigned_to=$row['assigned_to'];
		$refund_assigned_to="";
		$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
		$resultUserIDs = mysqli_query($db,$queryUserIDs); 
		
		
		while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){//build up the assigned to username
			
			$refund_assigned_to=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
		}
		
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

		//print '<tr>
		print '<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['NG_enc_id'].'</a></td>
		<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>
		<td>'.$row['dt_request'].'</td>
		<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
		<td>'.$row['first_name'].' '.$row['last_name'].'</td>
		<td>'.$row['payable'].'</td>';
		print '<td>$ '.$row['amount'].'</td>';
		
		if(!$row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>NEW</td>';
		}elseif(!$row['accounting_approval'] && $row['billing_initial_approval']){
			print '<td>ACCOUNTING APPROVAL</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && $row['billing_final_approval']){
			print '<td>ACCOUNTING VERIFIED</td>';
		}elseif($row['status']=="REJECTED"){
			print '<td>REJECTED</td>';
		}elseif($row['status']=="VOIDED"){
			print '<td>VOIDED</td>';
		}


		print '<td>'.$refund_assigned_to.'</td>';
		print	'</td></tr>';

	}	
			instantiate_page_variables($row,$tempOrigStartPosition,$page,$URL_String_BACK,$URL_String_FORWARD);

	}
	print '</table></div>';
	
	
	if (sizeof($row)>$_SESSION['RowsPerPage']){ //only conditionally display the pagination

		//echo $_SESSION['RowsPerPage'].'<br>';
		//echo sizeof($row).'<br>';
		displayPagination($row,$tempOrigStartPosition,$URL_String_BACK,$URL_String_FORWARD);
	
	}

	//print '<h3 align="center"><a href="addrefund.php">Create a New Refund Request</a></h3>';
	
	}else{//end if they have refunds	
	
		echo '<br><br>';
		echo '<center><b> You currently have no refunds assigned to you! </b></center>'; 
	}

	showFooter();


}


function showReportsPage($username='', $accessLvl = '', $errors = ''){

	showHeaderReports($username, $accessLvl);

	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	
	print '<h2 align="center">Generate Reports</h2>';
	print '<center><a href='."refunds.php".'?report_id=0>All Refunds</a></center>';
	print '<br>';
	print '<center><a href='."refunds.php".'?report_id=2>NEW</a></center>';
	print '<center><a href='."refunds.php".'?report_id=1>COMPLETED</a></center>';
	print '<center><a href='."refunds.php".'?report_id=4>PAR 2 APPROVED</a></center>';
	print '<center><a href='."refunds.php".'?report_id=3>ACCOUNTING APPROVED</a></center>';
	/* print '<center><a href='."refunds.php".'?report_id=5>PAR 1 APPROVED</a></center>'; */
	print '<center><a href='."refunds.php".'?report_id=6>REJECTED</a></center>';
	print '<center><a href='."refunds.php".'?report_id=7>VOIDED</a></center>';



		  
	//reportAccountingApproved();
	showFooter();
	

}





function showSearchPage($username='', $accessLvl = '', $errors = ''){

	showHeaderSearch($username, $accessLvl);
	
	include 'connectToDB.php'; 

	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	
	$query_refund_cols = "SELECT column_name FROM information_schema.columns WHERE table_name='refund'"; 			
	$result_refund = mysqli_query($db,$query_refund_cols);
	$row_users = mysqli_fetch_array($result_refund);
	
	print <<<EDITUSERPAGE
<center><h2 align="center">Search Refunds</h2>
<a href="reports.php">Back to Refunds</a>
<br/><br/>
	Please select the term you would like to search for from the Drop down menu, <br> then type value you are interested in matching pertaining to that term and click "Search Refunds" 
<br><br>	

		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="search_refunds">
      <table style="width: 100%" border="1">
        <tbody>
          <tr>Search term: &nbsp;&nbsp;&nbsp;&nbsp;
              <select name="refund_search_term">
EDITUSERPAGE;

			while($row_users = mysqli_fetch_array($result_refund)){
				
				$selected = ($row_users['column_name'] === $_POST['refund_search_term']) ? ' selected="selected"' : '';
			
				print "<option value=\"{$row_users['column_name']}\" {$selected} ";	
				print ">{$row_users['column_name']}</option>";	

			}
	

	if(isset($_POST['search_value']) && strlen($_POST['search_value']>=1) && $_POST['search_value']!=NULL){
		$default=$_POST['search_value'];
	}else{
		$default="Default";
	}
			
	print <<<EDITUSERPAGE
              </select>
              <br>
            </td>
          </tr>
		  <br>
		  <td>
		  <tr>
		  Matching Value:  <input type="text" name="search_value" value="{$default}"></tr></td>
  
        </tbody>
      </table>
      <input type="hidden" name="_search_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <br>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="search" name="Search">Search Refunds</button>

	  </form></center>
EDITUSERPAGE;

	//$row_refunds = mysqli_fetch_array($result_refund);
	//reportAccountingApproved();
	//showFooter();

}

function searchByNames($username='', $accessLvl = '', $errors = ''){

	showHeaderSearch($username, $accessLvl);
	
	include 'connectToDB.php'; 


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	
	$query_refund_cols = "SELECT column_name FROM information_schema.columns WHERE table_name='refund'"; 			
	$result_refund = mysqli_query($db,$query_refund_cols);
	$row_users = mysqli_fetch_array($result_refund);
	
	$names_array=array('created_by','approved_by','payable','modified_by','assigned_to');

	print <<<EDITUSERPAGE
<center><h2 align="center">Search Refunds By A Name</h2>

	Please select the term you would like to search for from the Drop down menu, <br> then type value you are interested in matching pertaining to that term and click "Search Refunds" 
<br><br>	

		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="search_refunds">
      <table style="width: 100%" border="1">
        <tbody>
          <tr>Search term: &nbsp;&nbsp;&nbsp;&nbsp;
              <select name="refund_search_termName">
EDITUSERPAGE;

			while($row_users = mysqli_fetch_array($result_refund)){
				
				//in_array ( mixed $needle , array $haystack [, bool $strict = FALSE ] )
				if(in_array($row_users['column_name'],$names_array)){	//I do this to limit the values displayed in drop down based on which search page we are on
					$selected = ($row_users['column_name'] === $_POST['refund_search_term']) ? ' selected="selected"' : '';
					print "<option value=\"{$row_users['column_name']}\" {$selected} ";	
					print ">{$row_users['column_name']}</option>";	
				}

			}
	

	if(isset($_POST['search_value']) && strlen($_POST['search_value']>=1) && $_POST['search_value']!=NULL){
		$default=$_POST['search_value'];
	}else{
		$default="Default";
	}
			
	print <<<EDITUSERPAGE
              </select>
              <br>
            </td>
          </tr>
		  <br>
		  <td>
		  <tr>
		  
		    Matching Name:
		     <select name="search_valueName">
EDITUSERPAGE;

			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
	
			while($row_users = mysqli_fetch_array($result_users)){
				print "<option value=\"{$row_users['user_id']}\"";
				print ">{$row_users['first_name']} {$row_users['last_name']}</option>";	

			}
	print <<<EDITUSERPAGE
              </select>
		  <br>
		  </tr></td>
		<br>
        </tbody>
      </table>
      <input type="hidden" name="_search_by_names_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <br>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="search" name="Search">Search Refunds</button>

	  </form></center>
EDITUSERPAGE;

//echo '<br> <center><a href="{'$_SERVER['HTTP_REFERER']'}">Back to Refunds</a></center>';

	//$row_refunds = mysqli_fetch_array($result_refund);
	//reportAccountingApproved();
	//showFooter();

}

function searchByValues($username='', $accessLvl = '', $errors = ''){

	showHeaderSearch($username, $accessLvl);
	
	include 'connectToDB.php'; 


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	
	$query_refund_cols = "SELECT column_name FROM information_schema.columns WHERE table_name='refund'"; 			
	$result_refund = mysqli_query($db,$query_refund_cols);
	//$row_users = mysqli_fetch_array($result_refund);
	

	$values_array=array('NG_enc_id','refund_id','amount','addr_ln_1','addr_ln_2','city','state','zip','purpose','vo_po_nbr','GL_acct_nbr','comments');

	print <<<EDITUSERPAGE
<center><h2 align="center">Search Refunds By a Value</h2>

	Please select the term you would like to search for from the Drop down menu, <br> then type value you are interested in matching pertaining to that term and click "Search Refunds" 
<br><br>	

		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="search_refunds">
      <table style="width: 100%" border="1">
        <tbody>
          <tr>Search term: &nbsp;&nbsp;&nbsp;&nbsp;
              <select name="refund_search_termValue">
EDITUSERPAGE;

//$row_users = mysqli_fetch_array($result_refund);
//var_dump($row_users);


			while($row_users = mysqli_fetch_array($result_refund)){
				
				
				if(in_array($row_users['column_name'],$values_array)){	
					$selected = ($row_users['column_name'] === $_POST['refund_search_term']) ? ' selected="selected"' : '';
					print "<option value=\"{$row_users['column_name']}\" {$selected} ";	
					print ">{$row_users['column_name']}</option>";	
				}

			}
	

	if(isset($_POST['search_value']) && strlen($_POST['search_value']>=1) && $_POST['search_value']!=NULL){
		$default=$_POST['search_value'];
	}else{
		$default="Default";
	}
	
	
			
	print <<<EDITUSERPAGE
              </select>
              <br><br>
            </td>
          </tr>
		  
			<tr>
				<select name="match_exactly">

				<option value=0>Match Words Containing</option>
				<option value=1>Match Words Matching Exactly</option>
				</select>
			</tr>

		  <br><br>
		  <td>
		  <tr>
		   Value:  <input type="text" name="search_value" value="{$default}"></tr></td>
        </tbody>
      </table>
      <input type="hidden" name="_search_by_value_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <br>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="search" name="Search">Search Refunds</button>

	  </form></center>
EDITUSERPAGE;

//echo '<br> <center><a href="{$_SERVER['http_referer']}">Back to Refunds</a></center>';

	//$row_refunds = mysqli_fetch_array($result_refund);
	//reportAccountingApproved();
	//showFooter();

}

function searchByDates($username='', $accessLvl = '', $errors = ''){

	showHeaderSearch($username, $accessLvl);
	
	include 'connectToDB.php'; 
	
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
	
	$query_refund_cols = "SELECT column_name FROM information_schema.columns WHERE table_name='refund'"; 			
	$result_refund = mysqli_query($db,$query_refund_cols);
	$row_users = mysqli_fetch_array($result_refund);
	

	$dates_array=array('dt_request','dt_required','dt_approved','modified_dt','approved_dt');
	
	
	print <<<EDITUSERPAGE
<center><h2 align="center">Search Refunds By a Date</h2>

	Please select the term you would like to search for from the Drop down menu, <br> then type value you are interested in matching pertaining to that term and click "Search Refunds" 
<br><br>	

		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="search_refunds">
      <table style="width: 100%" border="0">
        <tbody>
          <tr>Search term: &nbsp;&nbsp;&nbsp;&nbsp;
              <select name="refund_search_termDate">
EDITUSERPAGE;

			while($row_users = mysqli_fetch_array($result_refund)){
				
				
				if(in_array($row_users['column_name'],$dates_array)){	
					$selected = ($row_users['column_name'] === $_POST['refund_search_term']) ? ' selected="selected"' : '';
					print "<option value=\"{$row_users['column_name']}\" {$selected} ";	
					print ">{$row_users['column_name']}</option>";	
				}

			}
	

	if(isset($_POST['search_value']) && strlen($_POST['search_value']>=1) && $_POST['search_value']!=NULL){
		$default=$_POST['search_value'];
	}else{
		$default="Default";
	}
			
	print <<<EDITUSERPAGE
              </select>
              <br>
            </td>
          </tr>
			<br><br>
			<tr><td><center>
				FROM Date: 
				<input type="text" name="datepickerSTART" id="datepickerSTART">
				&nbsp;
				TO Date: 
				<input type="text" name="datepickerEND" id="datepickerEND"></center>
			</td></tr>

			<tr><td>&nbsp;</td></tr>

        </tbody>
      </table>
      <input type="hidden" name="_search_by_date_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <input type="hidden" name="startPos"  value=0 />

	  <br>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="search" name="Search">Search Refunds</button>

	  </form></center>
EDITUSERPAGE;


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		

	//	echo '<br> <center><a href="{$_SERVER['http_referer']}">Back to Refunds</a></center>';


	//$row_refunds = mysqli_fetch_array($result_refund);
	//reportAccountingApproved();
	//showFooter();

}

function searchByStatus($username='', $accessLvl = '', $errors = ''){

	showHeaderSearch($username, $accessLvl);
	
	include 'connectToDB.php'; 


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	
	$query_refund_cols = "SELECT column_name FROM information_schema.columns WHERE table_name='refund'"; 			
	$result_refund = mysqli_query($db,$query_refund_cols);
	$row_users = mysqli_fetch_array($result_refund);
	
	
	
	$status_array=array('urgent','voided','rejected','accounting_approval','billing_initial_approval','billing_final_approval','completed');
	
	
	print <<<EDITUSERPAGE
<center><h2 align="center">Search Refunds By a Status</h2>

	Please select the term you would like to search for from the Drop down menu, <br> then type value you are interested in matching pertaining to that term and click "Search Refunds" 
<br><br>	

		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="search_refunds">
      <table style="width: 100%" border="1">
        <tbody>
          <tr>Matching Criteria: &nbsp;&nbsp;&nbsp;&nbsp;
              <select name="refund_search_termStatus">
EDITUSERPAGE;

			while($row_users = mysqli_fetch_array($result_refund)){
				
				
				if(in_array($row_users['column_name'],$status_array)){	
					$selected = ($row_users['column_name'] === $_POST['refund_search_term']) ? ' selected="selected"' : '';
					print "<option value=\"{$row_users['column_name']}\" {$selected} ";	
					print ">{$row_users['column_name']}</option>";	
				}

			}
	

	if(isset($_POST['search_value']) && strlen($_POST['search_value']>=1) && $_POST['search_value']!=NULL){
		$default=$_POST['search_value'];
	}else{
		$default="Default";
	}
			
	print <<<EDITUSERPAGE
              </select>
              <br><br>
            </td>
          </tr>
		  
			<tr>Matching Value: &nbsp;&nbsp;&nbsp;&nbsp;
			<select name="refund_search_termStatus_Value">
			<option value=1>Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
			<option value=0>No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
			</select>
			</tr>

		  <br>
		  <br>
        </tbody>
      </table>
      <input type="hidden" name="_search_by_status_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <br>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="search" name="Search">Search Refunds</button>

	  </form></center>
EDITUSERPAGE;

echo '<br> <center><a href="reports.php">Back to Refunds</a></center>';

	//$row_refunds = mysqli_fetch_array($result_refund);
	//reportAccountingApproved();
	//showFooter();
	
	/*
	  <td>
		  <tr>
		  Matching Value:  <input type="text" name="search_value" value="{$default}"></tr></td>
	*/

}


function reportAccountingApproved(){
	
	//include 'dump_all_page_contents.php'; 
	showHeaderALL($_SESSION['username'], $_SESSION['access']);
	include 'connectToDB.php'; 
	include 'pagination_functionality.php';


	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

		if(!isset($_SESSION['order'])){
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,urgent 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND (status='ACCOUNTING APPROVED') 
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,urgent 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND (status='ACCOUNTING APPROVED')  
			ORDER BY ".$_SESSION['order'] ." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}

	
	}else{

		if(!isset($_SESSION['order'])){
		
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval,urgent 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND (status='ACCOUNTING APPROVED') 
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval,urgent 			
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND (status='ACCOUNTING APPROVED')  
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

		}
		

	}
	
	$result = mysqli_query($db,$query); 
	$arrayRefundUsers=array();
	
	$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 

		
	$ctr=0;
	while ($row = mysqli_fetch_array($resultUserIDs)){

		$arrayRefundUsers[$row['user_id']]=$row['first_name'].' '.$row['last_name'];
	}
	
	///////HEADINGS FROM THE REFUNDS PAGE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '<br><br>';
	print '<center>Displaying: <b> ACCOUNTING APPROVED Reports</b></center>';
	print '<br /><br /><div align = "center">';
	print '<table border="1" cellpadding = "3">
	<tr>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?refund_id=y>Refund IDs</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
	;	
	///////END HEADINGS FROM THE REFUNDS PAGE////////////////////?////////////////////////////////////////////////////////////////////////////////////////////////////////
	

	$current_date=date("Y-m-d H:i:s");  

	while ($row = mysqli_fetch_array($result)){
		
		
		$today_dt=$entered_dt=$interval=$refund_requested_by=$date_requested=$refund_assigned_to=$interval="";
		calculateInterval($row,$refund_requested_by,$date_requested,$today_dt,$entered_dt,$interval,$refund_assigned_to);
		//$refund_assigned_to=$row['assigned_to'];
		
		$refund_assigned_to="";
		$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
		$resultUserIDs = mysqli_query($db,$queryUserIDs); 
		

		
		while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){//build up the assigned to username
			
			$refund_assigned_to=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
		}
		
		
		
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
		

		//print '<tr>
		print '<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['NG_enc_id'].'</a></td>
		<td>'.$row['refund_id'].'</td>
		<td>'.$row['dt_request'].'</td>
		<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
		<td>'.$row['first_name'].' '.$row['last_name'].'</td>
		<td>'.$row['payable'].'</td>';
		print '<td>$ '.$row['amount'].'</td>';
		
		if(!$row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>NEW</td>';
		}elseif(!$row['accounting_approval'] && $row['billing_initial_approval']){
			print '<td>ACCOUNTING APPROVAL</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && $row['billing_final_approval']){
			print '<td>COMPLETED</td>';
		}elseif($row['status']=="REJECTED"){
			print '<td>REJECTED</td>';
		}elseif($row['status']=="VOIDED"){
			print '<td>VOIDED</td>';
		}elseif($row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}


		print '<td>'.$refund_assigned_to.'</td>';

		print	'</td></tr>';

	}	

	print '</table></div>';
	
	print <<<EDITUSERPAGE
		<br><center><a href="reports.php"><button value="Back" name="Back">Back To Reports Page</button></a></center>
EDITUSERPAGE;
	
	
	showFooter();
	
	
}

function reportAll(){
	
	//include 'dump_all_page_contents.php'; 
	showHeaderALL($_SESSION['username'], $_SESSION['access']);
	include 'connectToDB.php'; 
	include 'pagination_functionality.php';

	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

		if(!isset($_SESSION['order'])){
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}

	
	}else{

		if(!isset($_SESSION['order'])){
		
			$query = "SELECT * FROM refund WHERE 1=1 ORDER BY dt_request DESC LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		
		}else{

			$query = "SELECT * FROM refund WHERE 1=1 ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

		}
		

	}
	
	//$query = "SELECT * FROM refund WHERE 1=1 LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

	$result = mysqli_query($db,$query); 
	
	
	$arrayRefundUsers=array();
	
	$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 

		
	$ctr=0;
	while ($row = mysqli_fetch_array($resultUserIDs)){

		$arrayRefundUsers[$row['user_id']]=$row['first_name'].' '.$row['last_name'];
	}
	
	///////HEADINGS FROM THE REFUNDS PAGE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '<br><br>';
	print '<center>Displaying: <b>All Refunds Report</b></center>';
	print '<br /><br /><div align = "center">';
	print '<table border="1" cellpadding = "3">
	<tr>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?refund_id=y>Refund ID</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
	;	
	///////END HEADINGS FROM THE REFUNDS PAGE////////////////////?////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/*
		<td><b><center>Actions</center></b></td>
	*/
	
	$result_display_ctr=0;
	
	$current_date=date("Y-m-d H:i:s");  

	while ($row = mysqli_fetch_array($result)){
		
		
		$today_dt=$entered_dt=$interval=$refund_requested_by=$date_requested=$refund_assigned_to=$interval="";
		calculateInterval($row,$refund_requested_by,$date_requested,$today_dt,$entered_dt,$interval,$refund_assigned_to);
		//$refund_assigned_to=$row['assigned_to'];	
		
		$refund_assigned_to="";
		$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
		$resultUserIDs = mysqli_query($db,$queryUserIDs); 
		
	
		while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){//build up the assigned to username
			$refund_assigned_to=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
		}
	
		$currentRowSize=sizeof($row);

		if($result_display_ctr<$_SESSION['RowsPerPage']){

		$result_display_ctr++;
		
		
		if($interval->days>30 && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#FF0000>';
		}elseif(($interval->days>=15 && $interval->days<30) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=yellow>';
		}elseif(($interval->days<=1) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#009900>';
		}else{
			print '<tr>';
		}

		//print '<tr>
		print '<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['NG_enc_id'].'</a></td>
		<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>
		<td>'.$row['dt_request'].'</td>
		<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
		<td>'.$row['first_name'].' '.$row['last_name'].'</td>
		<td>'.$row['payable'].'</td>';
		print '<td>$ '.$row['amount'].'</td>';
		
		if(!$row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>NEW</td>';
		}elseif(!$row['accounting_approval'] && $row['billing_initial_approval']){
			print '<td>ACCOUNTING APPROVAL</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && $row['billing_final_approval']){
			print '<td>COMPLETED</td>';
		}elseif($row['status']=="REJECTED"){
			print '<td>REJECTED</td>';
		}elseif($row['status']=="VOIDED"){
			print '<td>VOIDED</td>';
		}elseif($row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}


		print '<td>'.$refund_assigned_to.'</td>';

		print	'</td></tr>';

	}

		instantiate_page_variables($row,$tempOrigStartPosition,$page,$URL_String_BACK,$URL_String_FORWARD);


	//print '<h3 align="center"><a href="addrefund.php">Create a New Refund Request</a></h3>';
	
}

	print '</table></div>';

	//echo 'the current row size is '.$currentRowSize;
	//echo '<br>';

if ($currentRowSize>$_SESSION['RowsPerPage']){ //only conditionally display the pagination

	//include 'dump_all_page_contents.php'; 
	displayPaginationReports($tempOrigStartPosition,$URL_String_BACK,$URL_String_FORWARD);


	//showFooter();

}else{

	print <<<EDITUSERPAGE
		<br><center><a href="reports.php"><button value="Back" name="Back">Back To Reports Page</button></a></center>
EDITUSERPAGE;
	
	showFooter();
	
}

}

function reportCompleted(){
	
	//include 'dump_all_page_contents.php'; 
	showHeaderALL($_SESSION['username'], $_SESSION['access']);
	include 'connectToDB.php'; 
	
	

	
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
		<br>
		<center><h3>Completed Refunds Reports</h3>
		<p>Please enter a custom date range you would like to generate the report for, or leave blank to generate all Completed Refunds. </p>

		<?php
			print <<<LOGINFORM
			<form action="{$_SERVER['PHP_SELF']}.?report_id=1" method="post">
			<table>
				<tr><td>
					<p>FROM Date: 
					<input type="text" name="datepickerSTART" id="datepickerSTART">
					&nbsp;
					TO Date: 
					<input type="text" name="datepickerEND" id="datepickerEND"></p></center>
				</td></tr>
				<br><br>
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan="2"><center><button name="generateReport" value="generateReport" type="submit">GENERATE REPORT</button></center></td></tr>
	<tr><td>			<br><br><br>
				<hr></td></tr>


			</table>
			</form>
			<center><a href="reports.php"><button value="Back" name="Back">Back To Reports Page</button></a></center>
LOGINFORM;
			
			
			
			
			
		?>	
		
		
		</body>
		</html>


<?php



if( isset($_POST['generateReport']) && sizeof($_POST['generateReport']) > 0){


if (isset($_POST['datepickerSTART']) && strlen($_POST['datepickerSTART']) > 1 && isset($_POST['datepickerEND'])){

		include 'pagination_functionality.php';

		$pieces_from = explode("/", $_POST['datepickerSTART']);
		$converted_date_from=date("Y-m-d", mktime(0, 0, 0, $pieces_from[0], $pieces_from[1], $pieces_from[2]));
		$entered_dt_from = new DateTime($converted_date_from);
		

		$pieces_to = explode("/", $_POST['datepickerEND']);
		$converted_date_to=date("Y-m-d", mktime(0, 0, 0, $pieces_to[0], $pieces_to[1], $pieces_to[2]));
		$entered_dt_to = new DateTime($converted_date_to);
		

		
		
	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

		if(!isset($_SESSION['order'])){
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE Date(modified_dt) >='".Date($converted_date_from)."' AND Date(modified_dt) <='".Date($converted_date_to)."' AND status='COMPLETED' 
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
	
			
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE Date(modified_dt) >='".Date($converted_date_from)."' AND Date(modified_dt) <='".Date($converted_date_to)."' AND status='COMPLETED' 
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
			

		}
		
	//	status !='deleted' AND status !='VOIDED' AND accounting_approval=1 AND billing_initial_approval=1 AND billing_final_approval=1 


	}else{

		if(!isset($_SESSION['order'])){
			

			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE Date(modified_dt) >='".Date($converted_date_from)."' AND Date(modified_dt) <='".Date($converted_date_to)."' AND status='COMPLETED'  
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
			

		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval 			
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE Date(modified_dt) >='".Date($converted_date_from)."' AND Date(modified_dt) <='".Date($converted_date_to)."'
			AND status='COMPLETED' ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
		}
		

	}
	

}else{


	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

		if(!isset($_SESSION['order'])){
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status='COMPLETED'
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
			
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status='COMPLETED'
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}


	}else{

		if(!isset($_SESSION['order'])){
		
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status='COMPLETED'  
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval 			
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status='COMPLETED'  
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

		}
		

	}


}

/*
echo 'the querY is <br>';
echo $query;
echo '<br>';
*/

$arrayRefundUsers=array();

$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
$resultUserIDs = mysqli_query($db,$queryUserIDs); 

	
$ctr=0;
while ($row = mysqli_fetch_array($resultUserIDs)){

	$arrayRefundUsers[$row['user_id']]=$row['first_name'].' '.$row['last_name'];
}

///////HEADINGS FROM THE REFUNDS PAGE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '<br><br>';
print '<center><b>COMPLETED:</b></center>';

if($_POST['datepickerSTART']){
	
	echo 'Reports for the Range: ';
	echo $_POST['datepickerSTART'].' Through '.$_POST['datepickerEND'];
	echo '<br>';
}

print '<br /><br /><div align = "center">';
print '<table border="1" cellpadding = "3">
<tr>
<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
<td><center><b><a href='.$_SERVER['PHP_SELF'].'?refund_id=y>Refund ID</a></b></center></td>
<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
;	
///////END HEADINGS FROM THE REFUNDS PAGE////////////////////?////////////////////////////////////////////////////////////////////////////////////////////////////////


$current_date=date("Y-m-d H:i:s");  

$result = mysqli_query($db,$query); 

//$row = mysqli_fetch_array($result);

//echo sizeof($row);

while ($row = mysqli_fetch_array($result)){
	

	$today_dt=$entered_dt=$interval=$refund_requested_by=$date_requested=$refund_assigned_to=$interval="";
	calculateInterval($row,$refund_requested_by,$date_requested,$today_dt,$entered_dt,$interval,$refund_assigned_to);
	//$refund_assigned_to=$row['assigned_to'];
	
	$refund_assigned_to="";
	$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 

	
	while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){//build up the assigned to username
		$refund_assigned_to=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
	}
	
	
	
	if($interval->days>30 && $row['status']!="COMPLETED"){
		print '<tr bgcolor=#FF0000>';
	}elseif(($interval->days>=15 && $interval->days<30) && $row['status']!="COMPLETED"){
		print '<tr bgcolor=yellow>';
	}elseif(($interval->days<=1) && $row['status']!="COMPLETED"){
		print '<tr bgcolor=#009900>';
	}else{
		print '<tr>';
	}

	//print '<tr>
	print '<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['NG_enc_id'].'</a></td>
	<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>
	<td>'.$row['dt_request'].'</td>
	<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
	<td>'.$row['first_name'].' '.$row['last_name'].'</td>
	<td>'.$row['payable'].'</td>';
	print '<td>$ '.$row['amount'].'</td>';
	
	if(!$row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
		print '<td>NEW</td>';
	}elseif(!$row['accounting_approval'] && $row['billing_initial_approval']){
		print '<td>ACCOUNTING APPROVAL</td>';
	}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && !$row['billing_final_approval']){
		print '<td>ACCOUNTING APPROVED</td>';
	}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && $row['billing_final_approval']){
		print '<td>COMPLETED</td>';
	}elseif($row['status']=="REJECTED"){
		print '<td>REJECTED</td>';
	}elseif($row['status']=="VOIDED"){
		print '<td>VOIDED</td>';
	}elseif($row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
		print '<td>ACCOUNTING APPROVED</td>';
	}


	print '<td>'.$refund_assigned_to.'</td>';

	print	'</td></tr>';

}	

print '</table></div>';

/*
print <<<EDITUSERPAGE
	<br><center><a href="reports.php"><button value="Back" name="Back">Back To Reports Page</button></a></center>
EDITUSERPAGE;
*/

//print '<h3 align="center"><a href="addrefund.php">Create a New Refund Request</a></h3>';

	
}
	
	
	showFooter();
	
	
}

function reportNew(){
	
	//include 'dump_all_page_contents.php'; 
	showHeaderALL($_SESSION['username'], $_SESSION['access']);
	include 'connectToDB.php'; 
	include 'pagination_functionality.php';
	
	//echo 'will print';
	//die();
	
		//Upon initial login to the app set some of the initial global settings
		$_SESSION['RowsPerPage']=10;
		$_SESSION['initialOffset']=0;
	///////////////////////////////////////////////////////////////////////////////
	instantiate_initialOffset();
	

	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

		if(!isset($_SESSION['order'])){
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND status='NEW' 
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND status='NEW' 
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}

	
	}else{

		if(!isset($_SESSION['order'])){
		
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND status='NEW'
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval 			
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND accounting_approval=0 AND status='NEW' 
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

		}
		

	}
	
	//echo 'the report query is <br>';
	//echo $query;
	
	$result = mysqli_query($db,$query); 
	$arrayRefundUsers=array();
	
	$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 

		
	$ctr=0;
	while ($row = mysqli_fetch_array($resultUserIDs)){

		$arrayRefundUsers[$row['user_id']]=$row['first_name'].' '.$row['last_name'];
	}
	
	///////HEADINGS FROM THE REFUNDS PAGE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '<br><br>';
	print '<center>Displaying: <b>NEW Refunds Report</b></center>';
	print '<br /><br /><div align = "center">';
	print '<table border="1" cellpadding = "3">
	<tr>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?refund_id=y>Refund ID</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
	;	
	///////END HEADINGS FROM THE REFUNDS PAGE////////////////////?////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/*
		<td><b><center>Actions</center></b></td>
	*/
	
	/*
	echo 'before <br> ';
	var_dump($_SESSION);
	echo '<br>';
		instantiate_initialOffset();
	echo 'after <br> ';

		var_dump($_SESSION);
	echo '<br>';
	*/

	
	$current_date=date("Y-m-d H:i:s");  

	while ($row = mysqli_fetch_array($result)){
		
		$today_dt=$entered_dt=$interval=$refund_requested_by=$date_requested=$refund_assigned_to=$interval="";
		calculateInterval($row,$refund_requested_by,$date_requested,$today_dt,$entered_dt,$interval,$refund_assigned_to);
		//$refund_assigned_to=$row['assigned_to'];	
		
		$currentRowSize=sizeof($row);
	
		
		$refund_assigned_to="";
		$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
		$resultUserIDs = mysqli_query($db,$queryUserIDs); 

		
		while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){//build up the assigned to username
			$refund_assigned_to=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
		}
	
	
		if($result_display_ctr<$_SESSION['RowsPerPage']){

					$result_display_ctr++;

		if($interval->days>30 && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#FF0000>';
		}elseif(($interval->days>=15 && $interval->days<30) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=yellow>';
		}elseif(($interval->days<=1) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#009900>';
		}else{
			print '<tr>';
		}

		//print '<tr>
		print '<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['NG_enc_id'].'</a></td>
		<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>
		<td>'.$row['dt_request'].'</td>
		<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
		<td>'.$row['first_name'].' '.$row['last_name'].'</td>
		<td>'.$row['payable'].'</td>';
		print '<td>$ '.$row['amount'].'</td>';
		
		if(!$row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>NEW</td>';
		}elseif(!$row['accounting_approval'] && $row['billing_initial_approval']){
			print '<td>ACCOUNTING APPROVAL</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && $row['billing_final_approval']){
			print '<td>COMPLETED</td>';
		}elseif($row['status']=="REJECTED"){
			print '<td>REJECTED</td>';
		}elseif($row['status']=="VOIDED"){
			print '<td>VOIDED</td>';
		}elseif($row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}


		print '<td>'.$refund_assigned_to.'</td>';

		print	'</td></tr>';

	}
	
	instantiate_page_variables($row,$tempOrigStartPosition,$page,$URL_String_BACK,$URL_String_FORWARD);


}	

	print '</table></div>';
	
	if ($currentRowSize>$_SESSION['RowsPerPage']){ //only conditionally display the pagination
	//include 'dump_all_page_contents.php'; 

		displayPaginationReports($row,$tempOrigStartPosition,$URL_String_BACK,$URL_String_FORWARD);

	}

	/*
	print <<<EDITUSERPAGE
		<br><center><a href="reports.php"><button value="Back" name="Back">Back To Reports Page</button></a></center>
EDITUSERPAGE;
		
	showFooter();
	*/
	
}

/*
BILLING INITIAL APPROVAL

BILLING FINAL APPROVAL

*/

function reportBillingInitial(){
	
	//include 'dump_all_page_contents.php'; 
	showHeaderALL($_SESSION['username'], $_SESSION['access']);
	include 'connectToDB.php'; 
	include 'pagination_functionality.php';


	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

		if(!isset($_SESSION['order'])){
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND status ='ACCOUNTING APPROVAL'
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND status ='ACCOUNTING APPROVAL'  
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}

	
	}else{

		if(!isset($_SESSION['order'])){
		
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND status ='ACCOUNTING APPROVAL' 
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval 			
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status ='ACCOUNTING APPROVAL'  
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

		}
		

	}
	
	$result = mysqli_query($db,$query); 
	$arrayRefundUsers=array();
	
	$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 

		
	$ctr=0;
	while ($row = mysqli_fetch_array($resultUserIDs)){

		$arrayRefundUsers[$row['user_id']]=$row['first_name'].' '.$row['last_name'];
	}
	
	///////HEADINGS FROM THE REFUNDS PAGE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '<br><br>';
	print '<center>Displaying: <b>PAR2 Initial Approval</b></center>';
	print '<br /><br /><div align = "center">';
	print '<table border="1" cellpadding = "3">
	<tr>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?refund_id=y>Refund IDs</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
	;	
	///////END HEADINGS FROM THE REFUNDS PAGE////////////////////?////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/*
		<td><b><center>Actions</center></b></td>
	*/
	
	
	$current_date=date("Y-m-d H:i:s");  

	while ($row = mysqli_fetch_array($result)){

		$today_dt=$entered_dt=$interval=$refund_requested_by=$date_requested=$refund_assigned_to=$interval="";
		calculateInterval($row,$refund_requested_by,$date_requested,$today_dt,$entered_dt,$interval,$refund_assigned_to);
		//$refund_assigned_to=$row['assigned_to'];
		
		$refund_assigned_to="";
		$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
		$resultUserIDs = mysqli_query($db,$queryUserIDs); 
		
	
	/*
		$date_requested=$row['dt_request'];

		$today_dt = new DateTime($current_date);
		$entered_dt = new DateTime($date_requested);
		$interval = date_diff($entered_dt,$today_dt);

		$refund_assigned_to="";
		$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
		$resultUserIDs = mysqli_query($db,$queryUserIDs); 
		
		*/
		while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){//build up the assigned to username
			$refund_assigned_to=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
		}
		
		
		if($interval->days>30 && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#FF0000>';
		}elseif(($interval->days>=15 && $interval->days<30) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=yellow>';
		}elseif(($interval->days<=1) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#009900>';
		}else{
			print '<tr>';
		}

		//print '<tr>
		print '<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['NG_enc_id'].'</a></td>
		<td>'.$row['refund_id'].'</td>
		<td>'.$row['dt_request'].'</td>
		<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
		<td>'.$row['first_name'].' '.$row['last_name'].'</td>
		<td>'.$row['payable'].'</td>';
		print '<td>$ '.$row['amount'].'</td>';
		
		if(!$row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>NEW</td>';
		}elseif(!$row['accounting_approval'] && $row['billing_initial_approval']){
			print '<td>ACCOUNTING APPROVAL</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && $row['billing_final_approval']){
			print '<td>COMPLETED</td>';
		}elseif($row['status']=="REJECTED"){
			print '<td>REJECTED</td>';
		}elseif($row['status']=="VOIDED"){
			print '<td>VOIDED</td>';
		}elseif($row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}


		print '<td>'.$refund_assigned_to.'</td>';

		print	'</td></tr>';

	}	

	print '</table></div>';
	
	print <<<EDITUSERPAGE
		<br><center><a href="reports.php"><button value="Back" name="Back">Back To Reports Page</button></a></center>
EDITUSERPAGE;
	
	//print '<h3 align="center"><a href="addrefund.php">Create a New Refund Request</a></h3>';
	
	showFooter();
	
	
}

function reportBillingFinal(){
	
	//include 'dump_all_page_contents.php'; 
	showHeaderALL($_SESSION['username'], $_SESSION['access']);
	include 'connectToDB.php'; 
	include 'pagination_functionality.php';


	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

		if(!isset($_SESSION['order'])){
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND status ='ACCOUNTING APPROVED' 
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND status ='ACCOUNTING APPROVED'  
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}

	
	}else{

		if(!isset($_SESSION['order'])){
		
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND status !='VOIDED' AND status ='ACCOUNTING APPROVED' 
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval 			
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status !='deleted' AND accounting_approval=1 AND status ='ACCOUNTING APPROVED'  
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

		}
		

	}
	
	$result = mysqli_query($db,$query); 
	$arrayRefundUsers=array();
	
	$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 

		
	$ctr=0;
	while ($row = mysqli_fetch_array($resultUserIDs)){

		$arrayRefundUsers[$row['user_id']]=$row['first_name'].' '.$row['last_name'];
	}
	
	///////HEADINGS FROM THE REFUNDS PAGE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '<br><br>';
	print '<center>Displaying: <b>COMPLETED Reports</b></center>';
	print '<br /><br /><div align = "center">';
	print '<table border="1" cellpadding = "3">
	<tr>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?refund_id=y>Refund ID</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
	;	
	///////END HEADINGS FROM THE REFUNDS PAGE////////////////////?////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/*
		<td><b><center>Actions</center></b></td>
	*/
	
	
	$current_date=date("Y-m-d H:i:s");  

	while ($row = mysqli_fetch_array($result)){
		
		
		$today_dt=$entered_dt=$interval=$refund_requested_by=$date_requested=$refund_assigned_to=$interval="";
		calculateInterval($row,$refund_requested_by,$date_requested,$today_dt,$entered_dt,$interval,$refund_assigned_to);	
		//$refund_assigned_to=$row['assigned_to'];
		
		$refund_assigned_to="";
		$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
		$resultUserIDs = mysqli_query($db,$queryUserIDs); 	
		
		/*
		$date_requested=$row['dt_request'];

		$today_dt = new DateTime($current_date);
		$entered_dt = new DateTime($date_requested);
		$interval = date_diff($entered_dt,$today_dt);

		$refund_assigned_to="";
		$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
		$resultUserIDs = mysqli_query($db,$queryUserIDs); 
		
		*/
		
		while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){//build up the assigned to username
			
			$refund_assigned_to=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
		}
		
		
		if($interval->days>30 && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#FF0000>';
		}elseif(($interval->days>=15 && $interval->days<30) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=yellow>';
		}elseif(($interval->days<=1) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#009900>';
		}else{
			print '<tr>';
		}

		//print '<tr>
		print '<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['NG_enc_id'].'</a></td>
		<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>
		<td>'.$row['dt_request'].'</td>
		<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
		<td>'.$row['first_name'].' '.$row['last_name'].'</td>
		<td>'.$row['payable'].'</td>';
		print '<td>$ '.$row['amount'].'</td>';
		
		if(!$row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>NEW</td>';
		}elseif(!$row['accounting_approval'] && $row['billing_initial_approval']){
			print '<td>ACCOUNTING APPROVAL</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}elseif($row['accounting_approval'] && $row['billing_initial_approval'] && $row['billing_final_approval']){
			print '<td>COMPLETED</td>';
		}elseif($row['status']=="REJECTED"){
			print '<td>REJECTED</td>';
		}elseif($row['status']=="VOIDED"){
			print '<td>VOIDED</td>';
		}elseif($row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>ACCOUNTING APPROVED</td>';
		}


		print '<td>'.$refund_assigned_to.'</td>';

		print	'</td></tr>';

	}	

	print '</table></div>';
	
	print <<<EDITUSERPAGE
		<br><center><a href="reports.php"><button value="Back" name="Back">Back To Reports Page</button></a></center>
EDITUSERPAGE;
	
	//print '<h3 align="center"><a href="addrefund.php">Create a New Refund Request</a></h3>';
	
	showFooter();
	
	
}


function reportVoided(){
	
	//include 'dump_all_page_contents.php'; 
	showHeaderALL($_SESSION['username'], $_SESSION['access']);
	include 'connectToDB.php'; 
	include 'pagination_functionality.php';


	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

		if(!isset($_SESSION['order'])){
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,rejected 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status ='VOIDED'
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,rejected 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status ='VOIDED'
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}

	
	}else{

		if(!isset($_SESSION['order'])){
		
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval,rejected 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status ='VOIDED'
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval,rejected 			
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status ='VOIDED'
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

		}
		

	}
	

	$result = mysqli_query($db,$query); 
	$arrayRefundUsers=array();
	
	$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 

		
	$ctr=0;
	while ($row = mysqli_fetch_array($resultUserIDs)){

		$arrayRefundUsers[$row['user_id']]=$row['first_name'].' '.$row['last_name'];
	}
	
	///////HEADINGS FROM THE REFUNDS PAGE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '<br><br>';
	print '<center>Displaying: <b>VOIDED Reports</b></center>';
	print '<br /><br /><div align = "center">';
	print '<table border="1" cellpadding = "3">
	<tr>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?refund_id=y>Refund IDs</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
	;	
	///////END HEADINGS FROM THE REFUNDS PAGE////////////////////?////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/*
		<td><b><center>Actions</center></b></td>
	*/
	
	
	$current_date=date("Y-m-d H:i:s");  

	while ($row = mysqli_fetch_array($result)){
		
		
		$date_requested=$row['dt_request'];

		$today_dt = new DateTime($current_date);
		$entered_dt = new DateTime($date_requested);
		$interval = date_diff($entered_dt,$today_dt);

		$refund_assigned_to="";
		$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
		$resultUserIDs = mysqli_query($db,$queryUserIDs); 
		
		while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){//build up the assigned to username
			$refund_assigned_to=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
		}
		
		
		if($interval->days>30 && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#FF0000>';
		}elseif(($interval->days>=15 && $interval->days<30) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=yellow>';
		}elseif(($interval->days<=1) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#009900>';
		}else{
			print '<tr>';
		}

		//print '<tr>
		print '<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['NG_enc_id'].'</a></td>
		<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>
		<td>'.$row['dt_request'].'</td>
		<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
		<td>'.$row['first_name'].' '.$row['last_name'].'</td>
		<td>'.$row['payable'].'</td>';
		print '<td>$ '.$row['amount'].'</td>';
		
		print '<td>VOIDED</td>';


		print '<td>'.$refund_assigned_to.'</td>';

		print	'</td></tr>';

	}	

	print '</table></div>';
	
	print <<<EDITUSERPAGE
		<br><center><a href="reports.php"><button value="Back" name="Back">Back To Reports Page</button></a></center>
EDITUSERPAGE;
	
	//print '<h3 align="center"><a href="addrefund.php">Create a New Refund Request</a></h3>';
	
	showFooter();
	
	
}


function reportRejected(){
	
	//include 'dump_all_page_contents.php'; 
	showHeaderALL($_SESSION['username'], $_SESSION['access']);
	include 'connectToDB.php'; 
	include 'pagination_functionality.php';


	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

		if(!isset($_SESSION['order'])){
			
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,rejected 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status ='REJECTED'
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,rejected 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status ='REJECTED'
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}

	
	}else{

		if(!isset($_SESSION['order'])){
		
			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval,rejected 
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status ='REJECTED'
			ORDER BY dt_request,U.last_name,status LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		
		}else{

			$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable,assigned_to,
			accounting_approval,billing_initial_approval,billing_final_approval,rejected 			
			FROM refund AS R 
			INNER JOIN 
			users AS U 
			ON R.created_by = U.user_id 
			WHERE status ='REJECTED'
			ORDER BY ".$_SESSION['order']." LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

		}
		

	}
	
	//ECHO 'THE QUERY NOW IS <BR>';
	//ECHO $query;

	$result = mysqli_query($db,$query); 
	$arrayRefundUsers=array();
	
	$queryUserIDs="SELECT user_id, first_name, last_name FROM users";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 

		
	$ctr=0;
	while ($row = mysqli_fetch_array($resultUserIDs)){

		$arrayRefundUsers[$row['user_id']]=$row['first_name'].' '.$row['last_name'];
	}
	
	///////HEADINGS FROM THE REFUNDS PAGE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '<br><br>';
	print '<center>Displaying: <b>REJECTED Reports</b></center>';
	print '<br /><br /><div align = "center">';
	print '<table border="1" cellpadding = "3">
	<tr>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?refund_id=y>Refund IDs</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
	<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
	;	
	///////END HEADINGS FROM THE REFUNDS PAGE////////////////////?////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/*
		<td><b><center>Actions</center></b></td>
	*/
	
	
	$current_date=date("Y-m-d H:i:s");  
	
	//$row = mysqli_fetch_array($result);
	

	while ($row = mysqli_fetch_array($result)){
		
		$today_dt=$entered_dt=$interval=$refund_requested_by=$date_requested=$refund_assigned_to=$interval="";
		calculateInterval($row,$refund_requested_by,$date_requested,$today_dt,$entered_dt,$interval,$refund_assigned_to);
		//$refund_assigned_to=$row['assigned_to'];
		
		$refund_assigned_to="";
		$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
		$resultUserIDs = mysqli_query($db,$queryUserIDs); 

		while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){//build up the assigned to username
			$refund_assigned_to=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
		}
		
		
		if($interval->days>30 && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#FF0000>';
		}elseif(($interval->days>=15 && $interval->days<30) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=yellow>';
		}elseif(($interval->days<=1) && $row['status']!="COMPLETED"){
			print '<tr bgcolor=#009900>';
		}else{
			print '<tr>';
		}

		//print '<tr>
		print '<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['NG_enc_id'].'</a></td>
		<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>
		<td>'.$row['dt_request'].'</td>
		<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
		<td>'.$row['first_name'].' '.$row['last_name'].'</td>
		<td>'.$row['payable'].'</td>';
		print '<td>$ '.$row['amount'].'</td>';
		

			print '<td>REJECTED</td>';


		print '<td>'.$refund_assigned_to.'</td>';

		print	'</td></tr>';

	}	

	print '</table></div>';
	
	print <<<EDITUSERPAGE
		<br><center><a href="reports.php"><button value="Back" name="Back">Back To Reports Page</button></a></center>
EDITUSERPAGE;
	
	//print '<h3 align="center"><a href="addrefund.php">Create a New Refund Request</a></h3>';
	
	showFooter();
	
	
}

function sendEmailAccountingApproved(){
	
	
	
 				$from = "Patient Refund <noreply@chcb.org>";
 				$to = "Jonathan Bowley <virtuoso2199@gmail.com>";
 				$subject = "New Patient Refund Request";
 				$body = "Hello,\n\nA new patient refund request has been submitted. Please login to the Patient Refund web application to review.";
 
 				$host = "ssl://smtpout.secureserver.net";
 				$port = "465";
 				$username = "jonathan@jonathanbowley.com";
 				$password = "paw52beh";
 
				/*
 				$headers = array ('From' => $from,
  				 'To' => $to,
   			 'Subject' => $subject);
			 	$smtp = Mail::factory('smtp',
   				array ('host' => $host,
     				'port' => $port,
     				'auth' => true,
     				'username' => $username,
     				'password' => $password));
 
 				$mail = $smtp->send($to, $headers, $body);
				
			
 				if (PEAR::isError($mail)) {
  				 echo("<p>" . $mail->getMessage() . "</p>");
 			 	} 
						*/
	
}

function sendEmailBillingInitialApproved(){
	
	
	
 				$from = "Patient Refund <noreply@chcb.org>";
 				$to = "Jonathan Bowley <virtuoso2199@gmail.com>";
 				$subject = "New Patient Refund Request";
 				$body = "Hello,\n\nA new patient refund request has been submitted. Please login to the Patient Refund web application to review.";
 
 				$host = "ssl://smtpout.secureserver.net";
 				$port = "465";
 				$username = "jonathan@jonathanbowley.com";
 				$password = "paw52beh";

	
}

function trackRefundChanges($_POST,$status_before,$status_after){
	$now = date("Y-m-d H:i:s");	
	$queryStatusChange = "INSERT INTO refund_changes (refund_id, status_before, status_after, date, name) VALUES ('{$_POST['refund_id']}','{$status_before}','{$status_after}','{$now}','{$_SESSION['userid']}'";
	$result = mysqli_query($db,$queryStatusChange);
	
}


?>