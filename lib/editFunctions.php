<?php
//Common functions for all CHCB Patient Refund Pages


function showFooter(){

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
	echo $query;
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

function validateLoginFooter($username ='', $password=''){
	global $_SESSION;	
	global $db;
	global $_POST;	

	//check password is correct for supplied username
	$query = 	"SELECT user_id, password, first_name,last_name,access_lvl FROM users WHERE username = '{$_POST['username']}'";
	echo $query;
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

	showFooter();
}




//Check that new user data submitted is valid that returns array of errors
function validateRefundChanges(){

	$errors = array();

	if (strlen($_POST['dt_required'])<8){
		$errors[]='Please Enter a Valid Date'; //add better date validation logic	
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


	/*
	echo 'the size of the errors is array is: ';
	echo sizeof($errors);
	die();
	*/
	
	if(sizeof($errors)) {
		return $errors;
	} else {
		return 'valid';	
	}

} 	

/*
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
		
		print "<table class = \"topMenu\"><tr><td><a href=\"index.php\"  class = \"button\" >Home</td><td><a href=\"refunds.php\" class = \"button\" id = \"selected\">Refunds</a></td><td><a href=\"reports.php\"  class = \"button\">Reports</a></td><td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
	if ($accessLvl == 'S'){
		print '<td><a href="admin.php" class = "button" >Admin</a></td></tr></table>';	
	}else {
		print '</tr></table>';
	}
		
	}


}

*/

/*

function showEditPage($username='', $accessLvl = '', $errors = ''){ //page where user will actually edit user information

	showHeader($username, $accessLvl);
	global $db;


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}

	$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, status, dt_required, payable, addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}'";
	$result = mysqli_query($db,$query); 
	$row = mysqli_fetch_array($result);

	print <<<EDITUSERPAGE
<h2 align="center">Edit Refund</h2>
<a href="refunds.php">Back to Refunds</a>
<br/><br/>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="update_refund">
      <table style="width: 100%" border="1">
        <tbody>
          <tr>
            <td>Date Required</td>
            <td><input name="dt_required" type="text" value ="{$row['dt_required']}"><br>
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
            <td><input name="status" type="text" readonly value="{$row['status']}"></td>
          </tr>
        </tbody>
      </table>
      <input type="hidden" name="_edit_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <br/>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Update Refund</button></form>
EDITUSERPAGE;
	showFooter();

}

*/

function showDelPage($username='', $accessLvl = '', $errors = ''){ //page where user will actually delete user information

	showHeader($username, $accessLvl);
	global $db;


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}

	/*
	$query = "SELECT NG_enc_id, U.first_name, U.last_name, status, dt_request, dt_required, payable, addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments 
	FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id 
	WHERE refund_id = '{$_GET['refund_id']}'";
	*/
	
	/*
	include 'dump_all_page_contents.php';
	echo 'the requested user id is: <br>';
	echo $_REQUEST['user_id'];
	*/
	
	$query = "SELECT U.first_name, U.last_name,U.username
	From users AS U
	WHERE U.user_id = '{$_REQUEST['user_id']}'";
	
	$result = mysqli_query($db,$query); 
	$row = mysqli_fetch_array($result);
	
	/*
	echo $query;
	echo '<br>';
	var_dump($row);*/

	//die();
	
	
	print <<<EDITUSERPAGE
<h2 align="center">Delete User</h2>
<a href="edituser.php">Back to Users List</a>
<br/><br/>
	<form method="POST" action="{$_SERVER['PHP_SELF']}" name="delete_user">
	
	  Please confirm that you would like to completely delete the following user from the system: <br><br> <b>User:</b> 
	  {$row['first_name']} {$row['last_name']} <br> 
	  <b> Username: </b> {$row['username']}
	
      <input type="hidden" name="_del_submit" value="1" />
      <input type="hidden" name="user_id_to_delete" value = "{$_REQUEST['user_id']}">
	  <br/><br/>
	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="delete" name="Delete">Delete User</button>
	 </form>
EDITUSERPAGE;
	showFooter();

}

function showApprovePage($username='', $accessLvl = '', $errors = ''){ //page where user will actually approve user information

	showHeader($username, $accessLvl);
	global $db;


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}

	$query = "SELECT NG_enc_id, U.first_name, U.last_name, status, dt_request, dt_required, payable, addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}'";
	$result = mysqli_query($db,$query); 
	$row = mysqli_fetch_array($result);

	print <<<EDITUSERPAGE
<h2 align="center">Approve Refund</h2>
<a href="refunds.php">Back to Refunds</a>
<br/><br/>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="update_refund">
      <table style="width: 100%" border="1">
        <tbody>
          <tr>
            <td>Date Required</td>
            <td><input name="dt_required" type="text" readonly value ="{$row['dt_required']}"><br>
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
            <td><input name="status" type="text" readonly value="{$row['status']}"></td>
          </tr>
        </tbody>
      </table>
      <input type="hidden" name="_app_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <br/>
	  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="approve" name="Approve">Approve Refund</button>
	  </form>
EDITUSERPAGE;
	showFooter();

}

/*

      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Update Refund</button>

*/

/*

function showPage($username='', $accessLvl = '', $errors = ''){ //page where user will select user to edit
	global $db;
	showHeader($username, $accessLvl);


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	


	//include_once 'dump_all_page_contents.php';   //uncomment this to show the first edit screen
	//die();
	
	if($accessLvl == 'U'){//is access is only at the user level, then must match the refunds pulled to display only the current users created refunds

	$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable 
	FROM refund AS R 
	INNER JOIN 
	users AS U 
	ON R.created_by = U.user_id 
	WHERE status !='deleted' AND status !='VOIDED' 
	ORDER BY dt_request,U.last_name,status".$_SESSION['order'];
	
		echo $query;
		//die();
	
	}else{
	$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable 
	FROM refund AS R 
	INNER JOIN 
	users AS U 
	ON R.created_by = U.user_id 
	WHERE status !='deleted' ORDER BY dt_request,U.last_name,status".$_SESSION['order'];
		
		echo $query;
		//die();
	}
	

	$result = mysqli_query($db,$query); 
	
	
	print '<br /><br /><div align = "center">';
	print '<table border="1" cellpadding = "3"><tr><td><b><a href='.$_SERVER['PHP_SELF'].'?encounter=y>Encounter Number</a></b></td><td><b>Date Requested</b></td><td><b>Requested By</b></td><td><b>Payable To</b></td><td><b>Amount</b></td><td><b>Status</b></td><td><b>Actions</b></td>';	
	while ($row = mysqli_fetch_array($result)){
		print '<tr><td>'.$row['NG_enc_id'].'</td><td>'.$row['dt_request'].'</td><td>'.$row['first_name'].' '.$row['last_name'].'</td><td>'.$row['payable'].'</td>';
		print '<td>'.$row['amount'].'</td><td>'.$row['status'].'</td>';
		print '<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">Edit</a>&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=delete">Void</a>';
		if($accessLvl == 'S' OR $accessLvl == 'A'){
			print '&nbsp;&nbsp<a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=approve">Approve</a></td></tr>';
		} else {
			print	'</td></tr>';
		}
		
	}	
	
	print '</table></div>';
	print '<h3 align="center"><a href="addrefund.php">Create a New Refund Request</a></h3>';
	
	showFooter();

}

*/


function showReportsPage($username='', $accessLvl = '', $errors = ''){

	showHeader($username, $accessLvl);


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
  print '<h2 align="center">Reports</h2>';
  print '<center><a href='."refunds.php".'?report_id=1>All Refunds</a></center>';
 // print '<center><a href='.$_SERVER['PHP_SELF'].'?report_id=1>All Refunds</a></center>';
	
	showFooter();

}




?>