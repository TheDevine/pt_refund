/*
//Check that new user data submitted is valid that returns array of errors
function validateRefundChanges(){


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
		
		print "<table class = \"topMenu\"><tr><td><a href=\"index.php\"  class = \"button\" >Home</td><td><a href=\"refunds.php\" class = \"button\" id = \"selected\">Refunds</a></td><td><a href=\"reports.php\"  class = \"button\">Reports</a></td><td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
	if ($accessLvl == 'S'){
		print '<td><a href="admin.php" class = "button" >Admin</a></td></tr></table>';	
	}else {
		print '</tr></table>';
	}
		
	}


}




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

	$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request, dt_required, payable, addr_ln_1, addr_ln_2, city, state, zip, purpose, amount, status, comments FROM refund AS R INNER JOIN users AS U ON R.created_by= U.user_id WHERE refund_id = '{$_GET['refund_id']}'";
	$result = mysqli_query($db,$query); 
	$row = mysqli_fetch_array($result);

	print <<<EDITUSERPAGE
<h2 align="center">Edit Refund</h2>
<a href="refunds.php">Back to Refunds</a>
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
        </tbody>
      </table>
      <input type="hidden" name="_edit_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Update Refund</button></form>
EDITUSERPAGE;
	showFooter();

}



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

	$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, status,refund_id, payable FROM refund AS R INNER JOIN users AS U ON R.created_by = U.user_id WHERE status !='deleted' ORDER BY dt_request,U.last_name,status";
	$result = mysqli_query($db,$query); 
	print '<br /><br /><div align = "center">';
	print '<table border="1" cellpadding = "3"><tr><td><b>Encounter Number</b></td><td><b>Date Requested</b></td><td><b>Requested By</b></td><td><b>Payable To</b></td><td><b>Amount</b></td><td><b>Status</b></td><td><b>Actions</b></td>';	
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
	print '<h3 align="center"><a href="addrefund.php">New Refund Request</a></h3>';
	
	showFooter();

}

*/