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
				
				$last_id = mysqli_insert_id($db);

				//upload any attachments that have been added with the refund
				uploadFiles();
				

				//send notification that a new refund has been created: call mail_presets
				//RULE: ON Creation:
				//IF urgent status email both creator and Erika, otherwise just email Erika///////////////////////////////////////////

				$query = "SELECT username FROM users WHERE user_id='{$_SESSION['userid']}'";
				$result = mysqli_query($db,$query);
				
				//echo 'the query is <br>';
				//echo $query;
				//echo '<br>';
				
				//var_dump($result);
				
				$rowUserNames=mysqli_fetch_array($result);
				//dynamically build the to address from the username selected based on the recipients specified by the step in the process
				$to=$rowUserNames['username'].'@chcb.org'; //build the creator email
				
				//echo 'the to value  is <br>';
				//echo $to;
				//echo '<br>';
				
				if($_POST['urgent']){ //verify that this works as intended
				
					$status="A Refund for ".$_POST['payable']." with a Refund ID ".$last_id." has been requested. <br>  This refund is marked as URGENT.";
				

					$from = "Patient Refund <noreply@chcb.org>";
					$subject = "Updated Patient Refund Request";
					$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been updated. Please login to the Patient Refund web application to review.";
					$body .="<br>Status: ".$status;
						
						
					echo 'the from field is ';
					echo $from;
					echo '<br><br>';
					
					echo 'the to field is: ';
					echo $to;
					echo '<br><br>';
					
					echo 'the subject is: ';
					echo $subject;
					echo '<br><br>';
					
					echo 'the body of the email is something to the effect of: ';
					echo $body;
					
					echo '<br><br>';

				
					mail_presets($to,$status); //creator
					mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)
				}else{
		
					$status="A Refund for ".$_POST['payable']." with a Refund ID ".$last_id." has been requested.";
						
					$from = "Patient Refund <noreply@chcb.org>";
					$subject = "Updated Patient Refund Request";
					$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been updated. Please login to the Patient Refund web application to review.";
					$body .="<br>Status: ".$status;
						
						
					echo 'the from field is: ';
					echo $from;
					echo '<br><br>';
					
					echo 'the to field is: ';
					echo $to;
					echo '<br><br>';
					
					echo 'the subject is: ';
					echo $subject;
					echo '<br><br>';

					
					echo 'the body of the email is something to the effect of: ';
					echo $body;
					
					echo '<br><br>';
					/*
					echo $status;
					echo '<br>';
					*/
					

					mail_presets($to,$status);
					
				}
								
				//END Send Emails Upon Creation ////////////////////////////////////////////////////////////////////////////////////////
				
				//show success message
				print '<h3 align="center"> Refund for  '.$_POST['payable'].' created!</h3>';
				print '<h4 align="center"><a href="index.php">Return to Refunds</a></h4>';
							
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

		
		
		  'file1' => 
    array (size=5)
      'name' => string 'Firstqueries.txt' (length=16)
      'type' => string 'text/plain' (length=10)
      'tmp_name' => string 'C:\wamp\tmp\php54CD.tmp' (length=23)
      'error' => int 0
      'size' => int 5287
	  
	  1.	PDFs
2.	Word
3.	Excel
4.	Txt
5.	HTML
6.	JPG/JPEG
7.	BMP
8.	PNG
9.	TIF/TIFF

		
		
	*/
	
	
	$target_dir = "uploads/";
	$target_file="";
	
	for($x=1;$x<5;$x++){//build up string of filenames
		
		$fileBaseName="file";
		$fileBaseName.=(string)$x;
		
		//echo $fileBaseName;
		//echo '<br>';
		
		
	
			$target_file = $target_dir . basename($_FILES[$fileBaseName]["name"]);
			
			//echo 'the targeted filename is ';
			//echo $target_file;
			
			
			//die();
			
			$uploadOk = 1;
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	
			/*
			echo '<br> the image file type is: <br>';
			echo $imageFileType;
			echo '<br>';
			
			echo $_POST["submit"];
			echo 'that was post submit <br>';

			echo 'the size is <br>';
			echo $_FILES[$fileBaseName]["size"];
			
			echo '<br>';
			*/
			
					
			/**/

			/*  was applicable previously, but current requirements allow uploading of both images and txt
			// Check if image file is a actual image or fake image
			if(isset($_FILES[$fileBaseName]["size"]) && $_FILES[$fileBaseName]["size"]>0 ) {

				$check = getimagesize($_FILES[$fileBaseName]["tmp_name"]);
				
				echo 'check is <br>';
				echo $check;

				if($check !== false) {
					echo "File is an image - " . $check["mime"] . ".";
					$uploadOk = 1;
				} else {
					echo "File is not an image.";
					$uploadOk = 0;
				}
			}
			
			*/
			
			
			// Check if file already exists
			if (file_exists($target_file)) {
				//echo "Sorry, file already exists. <br>";
				$uploadOk = 0;
			}
			// Check file size
			if ($_FILES[$fileBaseName]["size"] > 500000) {
				//echo "Sorry, your file is too large. <br>";
				$uploadOk = 0;
			}


			// Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "txt" && $imageFileType != "html"
			&& $imageFileType != "bmp" && $imageFileType != "tif" && $imageFileType != "tiff") {
				//echo "Sorry, only JPG, JPEG, PNG, GIF, PDFs, DOCS, TXTs, HTML, BMP and tif/tiff file types are allowed. <br>";
				$uploadOk = 0;
			}
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				//echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
			} else {
					if (move_uploaded_file($_FILES[$fileBaseName]["tmp_name"], $target_file)) {
						//echo "The file ". basename( $_FILES[$fileBaseName]["name"]). " has been uploaded. <br>";
					} else {
						//echo "Sorry, the following error was encountered when attempting to upload your file. <br>";
						 print_r( error_get_last() );
					}
				}		


	
	
	}
	
}
	
	

//Checks that new refund data submitted is valid or returns an array of errors
function validateNewRefund (){
	
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
		
		print "<table class = \"topMenu\">
		<tr><td><a href=\"index.php\"  class = \"button\" >Home</td>
		<td><a href=\"index.php\" class = \"button\">Refunds</a></td>
		<td><a href=\"reports.php\"  class = \"button\">Reports</a></td>
		<td><a href=\"unset_search.php\"  class = \"button\">Search</a></td>
		<td><a href=\"mngaccount.php\"  class = \"button\">My Account</a></td>";
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
	(addrefund.php version)
	<br />
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
	Enhanced by Derek Devine (addrefund.php version) 
	(addrefund.php version)
	<br />
	&copy; Community Health Centers of Burlington, Inc. 2014</div></center>
	</body>
	</html>
FOOTER;
*/

}



function showPage($username='', $accessLvl = '', $errors = ''){

	showHeader($username, $accessLvl);
	global $db;
	
	include 'dropDownListValues.php';


	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	
	$array_of_statesFull= array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District of Columbia', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming');

	$array_of_statesShort=array("AK", "AL", "AR", "AZ", "CA", "CO", "CT", "DC", "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA", "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE", "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VA", "VT", "WA", "WI", "WV", "WY");
	//echo 'the array of states size is <br>';
	
	//var_dump($array_of_statesFull);
	
	//die();
	
	//echo $array_of_statesFull.size();

			/*
			while($days_ctr<=365){

				print "<option value=\"{$days_ctr}\"";
				print ">{$days_ctr}</option>";	

				$days_ctr++;

			}
			*/
			
	

	if (isset($_POST['amount'])){
		
	print <<<ADDREFUNDPAGE
		<h2 align="center">Add a New Refund</h2>
		<a href="index.php">Back to Refunds</a>
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
            <td>Check Payabless To:</td>
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
            <td>

			<input maxlength="2" name="state" type="text" value="{$_POST['state']}">
			<select name="state">

ADDREFUNDPAGE;

		print "<option value=\"\"";
		print "></option>";	
		
		$loopstateCtr=0;

		foreach ($array_of_statesFull as $key => $value){
		
			print "<option value=\"{$array_of_statesShort[$loopstateCtr]}\"";			
			print ">{$value}</option>";	
			$loopstateCtr++;
		}
		
		print <<<ADDREFUNDPAGE
		  </select>
		</td>
	  </tr>	
	
          <tr>
            <td>Zip</td>
            <td><input  maxlength="10" name="zip" type="text" value="{$_POST['zip']}">
            </td>
          </tr>
 <tr><td> Encounter Number: </td>
		  
ADDREFUNDPAGE;

print <<<ADDREFUNDPAGE

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
			<a href="index.php">Back to Refunds</a>
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
		  </tr>	
			
          <tr>
            <td>State</td>
            <td>

			<select name="state">

ADDREFUNDPAGE;


		print "<option value=\"\"";
		print "></option>";	
		$loopstateCtr=0;

		foreach ($array_of_statesFull as $key => $value){
		
			print "<option value=\"{$array_of_statesShort[$loopstateCtr]}\"";			
			print ">{$value}</option>";	
			$loopstateCtr++;

		}
		
		print <<<ADDREFUNDPAGE
		  </select>
		</td>
	  </tr>	
	
		  
          <tr>
            <td>Zip</td>
            <td><input  maxlength="10" name="zip" type="text" value="">
            </td>
          </tr>
		  
 <tr><td> Encounter Number: </td>
		  

            <td><input name="enc_nbr" type="text" value="">
           


ADDREFUNDPAGE;

print <<<ADDREFUNDPAGE

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