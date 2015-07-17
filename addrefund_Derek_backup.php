<?php

//start session
ini_set('session.gc_maxlifetime', 600);
session_start();

/*
require('refundFunctions.php');
require_once "Mail.php";

$db = mysqli_connect('localhost','ptrefund','x22m3y2k','pt_refund'); //connect to database
   if(!$db){die("Can't connect: ". mysqli_connect_error());}
*/
//include 'lib\refundFunctions.php';
include 'connectToDB.php'; 

if (array_key_exists('userid', $_SESSION)){	//If user is logged, check for access level 
	if($_SESSION['access']=='S' OR $_SESSION['access']=='U' OR $_SESSION['access']=='A'){
		//once user is autheticated, check to see if this form has been submitted
		if(isset($_POST['_submit_check'])){ //form has been submitted
			//check for errors
			if(validateNewRefund()=='valid') //if no errors, create user in db and show success message
			{
				//create user in db
				
				if(!isset($_POST['urgent'])){
					$_POST['urgent']=0;
				}
				$now = date("Y-m-d H:i:s");	
				$query = "INSERT INTO refund (NG_enc_id, created_by, dt_request, urgent, amount, payable, 
				addr_ln_1,addr_ln_2,city,state,zip,purpose,status,comments,assigned_to) 
				VALUES ('{$_POST['enc_nbr']}','{$_SESSION['userid']}','{$now}',{$_POST['urgent']},
				'{$_POST['amount']}','{$_POST['payable']}','{$_POST['addr_ln_1']}','{$_POST['addr_ln_2']}',
				'{$_POST['city']}','{$_POST['state']}','{$_POST['zip']}','{$_POST['purpose']}','NEW','{$_POST['comments']}','{$_SESSION['userid']}')";
				$result = mysqli_query($db,$query);
				
				
				//actually upload any attachments that have been added with the refund
				uploadFiles();
				
				/*
				echo $query;
				var_dump($result);
				die();
				*/
				
				//send notification that a new refund has been created
				 
 
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
 			
				
				//show success message
				print '<h3 align="center"> Refund for  '.$_POST['payable'].' created!</h3>';
				print '<h4 align="center"><a href="refunds.php">Return to Refunds</a></h4>';
							
			} else {
				showPage($_SESSION['username'], $_SESSION['access'],validateNewRefund());
			}
			
			//if errors exist, show page again & fill in values
		
		} else { //form has not been submitted
			showPage($_SESSION['username'],$_SESSION['access']); //show page if user is logged in
		}
		
	} else {
			showLogin('The current user is not authorized to view this page.');	//unauthenticated users get OWNED!!
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

function uploadFiles(){
	
	/*
		array (size=17)
		'amount' => string '321' (length=3)
		'payable' => string 'me' (length=2)
		'addr_ln_1' => string '16 P' (length=4)
		'addr_ln_2' => string '' (length=0)
		'city' => string 'Burlington' (length=10)
		'state' => string 'VT' (length=2)
		'zip' => string '05401' (length=5)
		'enc_nbr' => string '123456' (length=6)
		'purpose' => string 'blah' (length=4)
		'comments' => string 'blahblah' (length=8)
		'file1' => string '6_17_notes.txt' (length=14)
		'file2' => string '' (length=0)
		'file3' => string '' (length=0)
		'file4' => string '' (length=0)
		'file5' => string '' (length=0)
		'_submit_check' => string '1' (length=1)
		'Submit' => string 'submit' (length=6)

	*/
	
	
	$target_dir = "uploads/";
	$target_file="";
	
	for($x=1;$x<5;$x++){
		
		$fileBaseName="file";
		$fileBaseName.=(string)$x;
		
		echo $fileBaseName;
		echo '<br>';
	}
	
	/*

	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	if($check !== false) {
		echo "File is an image - " . $check["mime"] . ".";
		$uploadOk = 1;
	} else {
		echo "File is not an image.";
		$uploadOk = 0;
	}
	}
	// Check if file already exists
	if (file_exists($target_file)) {
	echo "Sorry, file already exists.";
	$uploadOk = 0;
	}
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 500000) {
	echo "Sorry, your file is too large.";
	$uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	&& $imageFileType != "gif" ) {
	echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
	$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
	} else {
		echo "Sorry, there was an error uploading your file.";
	}
	}
	*/

}
	

//Checks that new refund data submitted is valid or returns an array of errors
function validateNewRefund (){
	
			//		uploadFiles();


	/*if (strlen($_POST['dt_required'])<8){
		$errors[]='Please Enter a Valid Date'; //add better date validation logic	
	}*/
	
	
	/*
	
		if(isset($_FILES['uploaded_file'])) {
		$errors     = array();
		$maxsize    = 2097152;
		$acceptable = array(
			'application/pdf',
			'image/jpeg',
			'image/jpg',
			'image/gif',
			'image/png'
		);

		if(($_FILES['uploaded_file']['size'] >= $maxsize) || ($_FILES["uploaded_file"]["size"] == 0)) {
			$errors[] = 'File too large. File must be less than 2 megabytes.';
		}

		if(!in_array($_FILES['uploaded_file']['type'], $acceptable)) && (!empty($_FILES["uploaded_file"]["type"]))) {
			$errors[] = 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
		}

		if(count($errors) === 0) {
			move_uploaded_file($_FILES['uploaded_file']['tmpname'], '/store/to/location.file');
		} else {
			foreach($errors as $error) {
				echo '<script>alert("'.$error.'");</script>';
			}

			die(); //Ensure no more processing is done
		}
		}	
	
	*/

	var_dump($_POST);
	
	echo 'that was a dump of the post';
	
	die();
	
	$errors = array();

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
		
		print "<table class = \"topMenu\"><tr><td><a href=\"index.php\"  class = \"button\" >Home</td><td><a href=\"refunds.php\" class = \"button\">Refunds</a></td><td><a href=\"reports.php\"  class = \"button\">Reports</a></td><td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
	if ($accessLvl == 'S'){
		print '<td><a href="admin.php" id = "selected">Admin</a></td></tr></table>';	
	}else {
		print '</tr></table>';
	}
		
	}

}

function showFooter(){

print <<<FOOTER
	<br><br>
	</div> 
	<center><div class="footer">
	Created by Jonathan Bowley<br />
	Enhanced by Derek Devine (addrefund.php version) <br />
	&copy; Community Health Centers of Burlington, Inc. 2014</div></center>
	</body>
	</html>
FOOTER;

}



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
	

	if (isset($_POST['amount'])){
		
	print <<<ADDREFUNDPAGE
		<h2 align="center">Add a New Refund</h2>
		<a href="refunds.php">Back to Refunds</a>
	<br/><br/>
	
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="add_refund" enctype="multipart/form-data">
      <table style="width: 100%" border="1">
        <tbody>
          <tr>
            <td>Urgent</td>
            <td><input maxlength="50" name="urgent" type="checkbox" value ="1"><br>
            </td>
          </tr>
          <tr>
          	<td>Amount</td>
			
          	<td><input maxlength="50" name="amount" type="text" value ="{$_POST['amount']}"><br />
          </tr>
          <tr>
            <td>Check Payable To:</td>
            <td><input name="payable" type="text" value="{$_POST['payable']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 1</td>
            <td><input name="addr_ln_1" type="text" value="{$_POST['addr_ln_1']}">
            </td>
          </tr>
          <tr>
            <td>Address Line 2</td>
            <td><input name="addr_ln_2" type="text" value="{$_POST['addr_ln_2']}">
            </td>
          </tr>
          <tr>
            <td>City</td>
            <td><input  name="city" type="text" value="{$_POST['city']}">
            </td>
          <tr>
            <td>State</td>
            <td><input maxlength="2" name="state" type="text" value="{$_POST['state']}">
            </td>
          </tr>
          <tr>
            <td>Zip</td>
            <td><input  maxlength="10" name="zip" type="text" value="{$_POST['zip']}">
            </td>
          </tr>
          <tr>
            <td>Encounter Number</td>
            <td><input name="enc_nbr" type="text" value="{$_POST['enc_nbr']}">
            </td>
          </tr>
          <tr>
            <td>Purpose</td>
            <td><input name="purpose" type="text" value="{$_POST['purpose']}">
            </td>
          </tr>
          <tr>
            <td>Comments</td>
            <td><textarea name="comments"  cols="20" rows="4" value="{$_POST['comments']}" ></textarea>
            </td>
          </tr>
          <tr>
          	<td>Attachment 1</td>
          	<td><input type="file" name="file1" ></td>
          </tr>
          <tr>
          	<td>Attachment 2</td>
          	<td><input type="file" name="file2"></td>
          </tr>
          <tr>
          	<td>Attachment 3</td>
          	<td><input type="file" name="file3"></td>
          </tr>
          <tr>
          	<td>Attachment 4</td>
          	<td><input type="file" name="file4"></td>
          </tr>
          <tr>
          	<td>Attachment 5</td>
          	<td><input type="file" name="file5"></td>
          </tr>
        </tbody>
      </table>
      <input type="hidden" name="_submit_check" value="1" />
	  <br/>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Request Refund</button></form>
ADDREFUNDPAGE;
	}else{
		print <<<ADDREFUNDPAGE

		<h2 align="center">Add a New Refund</h2>
			<a href="refunds.php">Back to Refunds</a>
	<br/><br/>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="add_refund" enctype="multipart/form-data">
      <table style="width: 100%" border="1">
        <tbody>
          <tr>
            <td>Urgent</td>
            <td><input maxlength="50" name="urgent" type="checkbox" value ="1"><br>
            </td>
          </tr>
          <tr>
          	<td>Amount</td>
			
          	<td><input maxlength="50" name="amount" type="text" value =""><br />
          </tr>
          <tr>
            <td>Check Payable To:</td>
            <td><input name="payable" type="text" value="">
            </td>
          </tr>
          <tr>
            <td>Address Line 1</td>
            <td><input name="addr_ln_1" type="text" value="">
            </td>
          </tr>
          <tr>
            <td>Address Line 2</td>
            <td><input name="addr_ln_2" type="text" value="">
            </td>
          </tr>
          <tr>
            <td>City</td>
            <td><input  name="city" type="text" value="">
            </td>
          <tr>
            <td>State</td>
            <td><input maxlength="2" name="state" type="text" value="">
            </td>
          </tr>
          <tr>
            <td>Zip</td>
            <td><input  maxlength="10" name="zip" type="text" value="">
            </td>
          </tr>
          <tr>
            <td>Encounter Number</td>
            <td><input name="enc_nbr" type="text" value="">
            </td>
          </tr>
          <tr>
            <td>Purpose</td>
            <td><input name="purpose" type="text" value="">
            </td>
          </tr>
          <tr>
            <td>Comments</td>
            <td><textarea name="comments"  cols="20" rows="4" value="" ></textarea>
            </td>
          </tr>
          <tr>
          	<td>Attachment 1</td>
          	<td><input type="file" name="file1" ></td>
          </tr>
          <tr>
          	<td>Attachment 2</td>
          	<td><input type="file" name="file2"></td>
          </tr>
          <tr>
          	<td>Attachment 3</td>
          	<td><input type="file" name="file3"></td>
          </tr>
          <tr>
          	<td>Attachment 4</td>
          	<td><input type="file" name="file4"></td>
          </tr>
          <tr>
          	<td>Attachment 5</td>
          	<td><input type="file" name="file5"></td>
          </tr>
        </tbody>
      </table>
      <input type="hidden" name="_submit_check" value="1" />
	  <br/>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Request Refund</button></form>
ADDREFUNDPAGE;
		
	}
	showFooter();

}


?>