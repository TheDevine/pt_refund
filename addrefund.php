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

?>
		<html lang="en">
		<head>
		<meta charset="utf-8">
		<title>jQuery </title>

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
		
		$(document).ready(function() {
				

			var wrapper         = $(".input_fields_wrap"); //Fields wrapper
			var add_button      = $(".add_field_button"); //Add button ID
			var x = 1; //initial text box count
		
			$(add_button).click(function(e){ //on add input button click
			
				var display=x+1; //for alert box purposes
				//alert('Please enter the additional Encounter Number associated with this refund in the new Encounter Field: Encounter Number '+display);
				alert('Please enter the additional Encounter Number associated with this refund in the new Encounter Field. ');

				e.preventDefault();
				
				//Add the row, increment the counter
				x++; //text box increment
				$(wrapper).append('<tr id='+x+'><td>Additonal Encounter Number: </td><td><input type="text" name="encounters[]"/><a href="#" class="remove_field" id='+x+'>Remove</a></td></tr>'); //add input box
				//$(wrapper).append('<tr id='+x+'><td>Encounter Number: '+x+'</td><td><input type="text" name="encounters[]"/><a href="#" class="remove_field" id='+x+'>Remove</a></td></tr>'); //add input box

			});

			$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
				
				alert('Encounter has been removed!');
				//alert('This will remove Encounter Field Number '+$(this).parents('tr').contents());
				//Remove the row, decrement the counter
				e.preventDefault(); $(this).parents('tr').remove(); 
				
				//x--;
	
				})
				
		});

		
		</script>
		

		<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
		
		function removeFocusPersonal()
			{
			
			  document.add_refund.refund_type_personal.blur();
			  $(this).add_refund.refund_type_personal.blur();
			  document.getElementById('refund_type_personal').innerText;
			  document.getElementById('refund_type_personal').blur();
			}
			
			
			function removeFocusCommercial()
			{
			  document.add_refund.refund_type_commercial.blur();
			  $(this).add_refund.refund_type_personal.blur();
			}
				
				

		</script>
		
		
		</head>

		<body>
			
		</body>
		</html>
		
	<?php




if (array_key_exists('userid', $_SESSION)){	//If user is logged, check for access level 
	if($_SESSION['access']=='S' OR $_SESSION['access']=='U' OR $_SESSION['access']=='A'){
		
		//once user is authenticated, check to see if this form has been submitted
		if(isset($_POST['_submit_check'])){ //form has been submitted
		
			$errors=array();
			if(validateNewRefund($errors)=='valid') //if no errors, create user in db and show success message
			{
				
				$successfullUpload=0;

				if(!isset($_POST['urgent'])){
					$_POST['urgent']=0;
				}
				
				//echo 'ive reached the DB insert <br>';
				//include 'dump_all_page_contents.php';
		
				//create user in db
				$now = date("Y-m-d H:i:s");	
				
				$query = "INSERT INTO refund (NG_enc_id, created_by, dt_request, urgent, amount, payable, 
				addr_ln_1,addr_ln_2,city,state,zip,purpose,status,comments,assigned_to,refund_type) 
				VALUES ('{$_POST['encounters'][0]}','{$_SESSION['userid']}','{$now}',{$_POST['urgent']},
				'{$_POST['amount']}','{$_POST['payable']}','{$_POST['addr_ln_1']}','{$_POST['addr_ln_2']}',
				'{$_POST['city']}','{$_POST['state']}','{$_POST['zip']}','{$_POST['purpose']}','NEW','{$_POST['comments']}','{$_SESSION['userid']}','{$_POST['refund_type']}')";
				$result = mysqli_query($db,$query);
				$last_id = mysqli_insert_id($db);
				
				$auto_increment=$last_id++;
				//reset auto increment id in case of errors
				$query_resetAutoIncrement="ALTER TABLE refund AUTO_INCREMENT = '{$auto_increment}'";
				$result = mysqli_query($db,$query_resetAutoIncrement);
				
				
				
				//upload any attachments that have been added with the refund
				$successfulUpload=uploadFiles($last_id);
				
				if($successfulUpload==1){
					
					//echo '<br> do I get here';
					
				if(sizeof($_POST['encounters'])>1){

					foreach($_POST['encounters'] as $key => $value){
						
						if(strlen($value)>0){ //if there was actually a number entered into the encounter field

							$queryManyEncounters = "INSERT INTO 
							refund_manyEncounters 
							(Encounter_ID, refund_ID) 
							VALUES ('{$value}','{$last_id}')";
							
							$result = mysqli_query($db,$queryManyEncounters);
	
						}
				
					}
				
				}
				
				//die();

				//send notification that a new refund has been created: call mail_presets
				//RULE: ON Creation:
				//IF urgent status email both creator and Erika, otherwise just email Erika///////////////////////////////////////////

				$query = "SELECT username FROM users WHERE user_id='{$_SESSION['userid']}'";
				$result = mysqli_query($db,$query);

				
				$rowUserNames=mysqli_fetch_array($result);
				//dynamically build the to address from the username selected based on the recipients specified by the step in the process
				$to=$rowUserNames['username'].'@chcb.org'; //build the creator email
				
			
				if($_POST['urgent']){ //verify that this works as intended
				
	
					$status="A Refund for ".$_POST['payable']." with a Refund ID ".($last_id-1)." has been requested. <br>  This refund is marked as URGENT.";
				

					$from = "Patient Refund <noreply@chcb.org>";
					$subject = "Created Patient Refund Request";
					$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been created. Please login to the Patient Refund web application to review.";
					$body .="<br>Status: ".$status;
						
					echo '<br><br>';
					echo 'Sample EMAIL Feilds: ';
					echo '<br>';
					echo 'the from field is ';
					echo $from;
					echo '<br><br>';
					
					echo 'the to field is: ';
					echo $to;
					echo '<br><br>';
					
					echo 'the subject is: ';
					echo $subject;
					echo '<br><br>';
					
					echo 'the body of the email is something to the effect of: <br>';
					echo $body;
					
					echo '<br><br>';

					print '<h3 align="center"> Refund for  '.$_POST['payable'].' created!</h3>';
					print '<h4 align="center"><a href="index.php">Return to Refunds</a></h4>';
				
					mail_presets($to,$status); //creator
					mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)
					
					
				}else{
		

					$status="A Refund for ".$_POST['payable']." with a Refund ID ".($last_id-1)." has been requested.";
						
					$from = "Patient Refund <noreply@chcb.org>";
					$subject = "Created Patient Refund Request";
					$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been created. Please login to the Patient Refund web application to review.";
					$body .="<br>Status: ".$status;
						
						
						
					echo '<br><br>';
					echo 'Sample EMAIL Feilds: ';
					echo '<br>';
						
					echo 'the from field is: ';
					echo $from;
					echo '<br><br>';
					
					echo 'the to field is: ';
					echo $to;
					echo '<br><br>';
					
					echo 'the subject is: ';
					echo $subject;
					echo '<br><br>';

					
					echo 'the body of the email is something to the effect of: <br>';
					echo $body;
					
					echo '<br><br>';
					/*
					echo $status;
					echo '<br>';
					*/
					
				print '<h3 align="center"> Refund for  '.$_POST['payable'].' created!</h3>';
				print '<h4 align="center"><a href="index.php">Return to Refunds</a></h4>';


					mail_presets($to,$status);

					
				}
								
				//END Send Emails Upon Creation ////////////////////////////////////////////////////////////////////////////////////////
				
				//show success message
			//	print '<h3 align="center"> Refund for  '.$_POST['payable'].' created!</h3>';
			//	print '<h4 align="center"><a href="index.php">Return to Refunds</a></h4>';
							
			
			
				}else{ //unsuccessful upload, back everything out
				
					$query="DELETE FROM refund WHERE refund_id='{$last_id}'";
					$result = mysqli_query($db,$query);
					
					$auto_increment=$last_id;
					//reset auto increment id in case of errors
					$query_resetAutoIncrement="ALTER TABLE refund AUTO_INCREMENT = '{$auto_increment}'";
					$result = mysqli_query($db,$query_resetAutoIncrement);
	
					//showPage($_SESSION['username'], $_SESSION['access'],validateNewRefund($errors));
					
				}
	
			} else {
				
				//show errors at top of page
				print '<center><h2 class = "error"> THE following errors were encountered:</h2>';
				print '<ul><li>';
				print implode('</li><li>', $errors);
				print '</li></ul></center>';

				showPage($_SESSION['username'], $_SESSION['access'],validateNewRefund($errors));
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

function displayRefundUploads($refund_id){
	
	echo 'hhh';
	
	//standardize filename structure to set proof of concept in place
	//File 1 = File_
	$target_dir ="";
	$target_dir = "uploads/".$refund_id."/";

	echo 'how';

// Open a known directory, and proceed to read its contents
if (is_dir($target_dir)) {
	
	echo 'far';
    if ($dh = opendir($target_dir)) {
		
		
	echo 'go';
        while (($file = readdir($dh)) !== false) {
            echo "Filename: $file : filetype: " . filetype($target_dir . $file) . "\n";
        }
        closedir($dh);
    }
	
}

}


function uploadFiles($refundID_just_created){
	
	$off_by_one_targetDIR=$refundID_just_created-1;
	$target_dir = "uploads/".$off_by_one_targetDIR."/";

	$target_file="";
	$naming_convention="";
	$errors=array();
	$uploadOk = 1;
	
		
	if (!file_exists($target_dir )) { //if it doesnt exist, make it

		//make directory
		//if make fails error
		//else run the program
	}
	
	if (!file_exists($target_dir )) { //so we don't make the folder for each of the uploads

		if (!mkdir($target_dir, 0777, true)) {

				die('There was an error creating the folder in which to upload your documents.  Please make sure you have read write permissions on the 
				machine you are using.  If the error persists, please contact your local network administrator.');
			
			}
			
	}	
		//	else{
				
				//upload files with file naming conventions in place

				/*
				EncounterNoteRef72.pdf, 
				TransactionDetailsRef72.pdf, 
				and CommercialPaperworkRef72.pdf 
				*/

						
				if($_POST['refund_type']=='Commercial'){ //needs at least 3 documents
				
		
					$fileBaseName="commercial_paperwork";
					$target_file = $target_dir . basename($_FILES[$fileBaseName]["name"]);
					$handle=substr($_FILES[$fileBaseName]["name"],0,strpos($_FILES[$fileBaseName]["name"],'.'));
					
					$refundIDToMatch=$refundID_just_created-1;
					$naming_convention="CommercialPaperworkRef".$refundIDToMatch;
					$file_handle=$handle;
					
					if($file_handle!=$naming_convention){
						

						$errors[]='Your files must match the naming convention: <br>';
						$errors[]='CommercialPaperworkRefREFUND_ID.FileType <br>';
						$errors[]='Example for Refund ID 1: CommercialPaperworkRef1.pdf <br><br>';

						$uploadOk = 0;
						//break;
					}
					
					
					/* ENCOUNTER NOTE and TRANSACTION DETAIL PAGE */
					///////////////////////////////////////////////////////////////////////////////////
					for($x=1;$x<=3;$x++){//runs three times
						
							if($x==1)
								$fileBaseName="encounter_note";
							elseif($x==2)
								$fileBaseName="transaction_detail_page";
							else
								$fileBaseName="commercial_paperwork";

							$target_file = $target_dir . basename($_FILES[$fileBaseName]["name"]);
							
							$handle=substr($_FILES[$fileBaseName]["name"],0,strpos($_FILES[$fileBaseName]["name"],'.'));
							
							if($x==1){ //EncounterNoteRef
								
								$refundIDToMatch=$refundID_just_created-1;
								$naming_convention="EncounterNoteRef".$refundIDToMatch;
								$file_handle=$handle;
								
								if($file_handle!=$naming_convention){
									

									$errors[]='Your files must match the naming convention: <br>';
									$errors[]='EncounterNoteRefREFUND_ID.FileType <br>';
									$errors[]='Example for Refund ID 1: EncounterNoteRef1.pdf <br><br>';
									
									/*
									print '<center><h2 class = "error"> THE following errors were encountered:</h2>';
									print '<ul><li>';
									print implode('</li><li>', $errors);
									print '</li></ul></center>';
									*/
									
									//$errors=array();//reset the errors array

									$uploadOk = 0;
									//break;
								}
								
							}
							elseif($x==2){
								//TransactionDetailsRef72
								$refundIDToMatch=$refundID_just_created-1;
								$naming_convention="TransactionDetailsRef".$refundIDToMatch;
								$file_handle=$handle;
								
								if($file_handle!=$naming_convention){
									

									$errors[]='Your files must match the naming convention: <br>';
									$errors[]='TransactionDetailsRefREFUND_ID.FileType <br>';
									$errors[]='Example for Refund ID 1: TransactionDetailsRef1.pdf <br><br>';
									
									/*
									print '<center><h2 class = "error"> THE following errors were encountered:</h2>';
									print '<ul><li>';
									print implode('</li><li>', $errors);
									print '</li></ul></center>';
									*/
									
									//$errors=array();//reset the errors array

									$uploadOk = 0;
									//break;
								}
								
							}else{
								//CommercialPaperworkRef72
								$refundIDToMatch=$refundID_just_created-1;
								$naming_convention="CommercialPaperworkRef".$refundIDToMatch;
								$file_handle=$handle;
								
								if($file_handle!=$naming_convention){
									

									$errors[]='Your files must match the naming convention: <br>';
									$errors[]='CommercialPaperworkRefREFUND_ID.FileType <br>';
									$errors[]='Example for Refund ID 1: CommercialPaperworkRef1.pdf <br><br>';
									
									/*
									print '<center><h2 class = "error"> THE following errors were encountered:</h2>';
									print '<ul><li>';
									print implode('</li><li>', $errors);
									print '</li></ul></center>';
									*/
									
									//$errors=array();//reset the errors array

									$uploadOk = 0;
									//break;
								}
								
								
							}

						
							//echo 'the upload ok status is now <br>';
							//echo $uploadOk;
	
							if(strlen($_FILES[$fileBaseName]["name"])>0 && $uploadOk){
			
						
							$target_file = $target_dir . basename($_FILES[$fileBaseName]["name"]);
							
							$uploadOk = 1;
							$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

							
							// Check if file already exists
							if (file_exists($target_file)) {
			
								echo "Sorry, file already exists. <br>";
								$uploadOk = 0;
							}
							// Check file size
							if ($_FILES[$fileBaseName]["size"] > 500000) {
								echo "Sorry, your file is too large. <br>";
								$uploadOk = 0;
							}


							// Allow certain file formats
							if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
							&& $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "txt" && $imageFileType != "html"
							&& $imageFileType != "bmp" && $imageFileType != "tif" && $imageFileType != "tiff" && $imageFileType != "docx" && $imageFileType != "xlsx") {
				
								echo "Sorry, only JPG, JPEG, PNG, GIF, PDFs, DOCS, TXTs, HTML, xlsx, BMP and tif/tiff file types are allowed. <br>";
								$uploadOk = 0;
							}
							// Check if $uploadOk is set to 0 by an error
							if ($uploadOk == 0) {
								
								echo "Sorry, your file was not uploaded.";
								// if everything is ok, try to upload file
							} else {
									if (move_uploaded_file($_FILES[$fileBaseName]["tmp_name"], $target_file)) {
										echo "The file ". basename( $_FILES[$fileBaseName]["name"]). " has been uploaded. <br>";
									} else {
										echo "Sorry, the following error was encountered when attempting to upload your file. <br>";
										 print_r( error_get_last() );
									}
								}		

						}else{
							
							/*echo "Sorry, Three documents are required to create a Commercial Refund Request: <br>
							An encounter note, a transaction detail page and commercial paperwork document.<br> Please fix and re-upload.";
							*/
							/*
							$errors[]="Sorry, Three documents are required to create a Commercial Refund Request: <br>
							An encounter note, a transaction detail page and commercial paperwork document.<br> Please fix and re-upload.";
							*/
							//print_r( error_get_last() );
							$uploadOk = 0;
						}
						
					
					}//end for loop for the two required files of personal
					
					
						if($errors){
							
							print '<center><h2 class = "error"> THE following errores were encountered:</h2>';
							print '<ul><li>';
							print implode('</li><li>', $errors);
							print '</li></ul></center>';
							
							$uploadOk = 0;
							$errors=array();//reset the errors array
	
							include 'connectToDB.php';

							$refundToDelete=$refundID_just_created-1;
						
							$query_delete="DELETE FROM refund WHERE refund_id='{$refundToDelete}'";
							$result_delete = mysqli_query($db,$query_delete);
													
							$auto_increment=$refundToDelete;
							//reset auto increment id in case of errors

							$query_resetAutoIncrement="ALTER TABLE refund AUTO_INCREMENT = {$auto_increment} ";

							$result = mysqli_query($db,$query_resetAutoIncrement);
							

							$_SESSION['SAVE_POST']=$_POST;
								
							//showPage($_SESSION['username'], $_SESSION['access'],validateNewRefund($errors));		
					
							print '<h4 align="center"><a href="addrefund.php">Return to Add Refund Page</a></h4>';
						}
					
					
					///////////////////////////////////////////////////////////////////////////////////
					/* END ENCOUNTER NOTE and TRANSACTION DETAIL PAGE */
					
					
				}else{ //only requires encounter_note and transaction_detail_page
					
						for($x=1;$x<3;$x++){//runs twice
						
							if($x==1)
								$fileBaseName="encounter_note";
							else
								$fileBaseName="transaction_detail_page";

							$target_file = $target_dir . basename($_FILES[$fileBaseName]["name"]);
							
							$handle=substr($_FILES[$fileBaseName]["name"],0,strpos($_FILES[$fileBaseName]["name"],'.'));
							
							if($x==1){ //EncounterNoteRef
								
								$refundIDToMatch=$refundID_just_created-1;
								$naming_convention="EncounterNoteRef".$refundIDToMatch;
								$file_handle=$handle;
								
								if($file_handle!=$naming_convention){
									
									$errors[]='Your files must match the naming convention: <br>';
									$errors[]='EncounterNoteRefREFUND_ID.FileType <br>';
									$errors[]='Example for Refund ID 1: EncounterNoteRef1.pdf';
									/*
									print '<center><h2 class = "error"> THE following errors were encountered:</h2>';
									print '<ul><li>';
									print implode('</li><li>', $errors);
									print '</li></ul></center>';
									
									$errors=array();//reset the errors array
									*/

									$uploadOk = 0;
									//break;
								}
								
							}
							else{
								//TransactionDetailsRef72
								
								$refundIDToMatch=$refundID_just_created-1;
								$naming_convention="TransactionDetailsRef".$refundIDToMatch;
								$file_handle=$handle;
								
								if($file_handle!=$naming_convention){
									
									$errors[]='Your files must match the naming convention: <br>';
									$errors[]='TransactionDetailsRefREFUND_ID.FileType <br>';
									$errors[]='Example for Refund ID 1: TransactionDetailsRef1.pdf';
									
									/*
									print '<center><h2 class = "error"> THE following errors were encountered:</h2>';
									print '<ul><li>';
									print implode('</li><li>', $errors);
									print '</li></ul></center>';
									
									$errors=array();//reset the errors array
									*/

									$uploadOk = 0;
									//break;
								}
								
							}

						
						
							//die();
							
							/*
							EncounterNoteRef72.pdf, 
							TransactionDetailsRef72.pdf, 
							and CommercialPaperworkRef72.pdf 
							*/
						
			
						
							if(strlen($_FILES[$fileBaseName]["name"])>0 && $errors){
			
						
							$target_file = $target_dir . basename($_FILES[$fileBaseName]["name"]);
							
							$uploadOk = 1;

							
							$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

							
							// Check if file already exists
							if (file_exists($target_file)) {
			
								echo "Sorry, file already exists. <br>";
								$uploadOk = 0;
							}
							// Check file size
							if ($_FILES[$fileBaseName]["size"] > 500000) {
								echo "Sorry, your file is too large. <br>";
								$uploadOk = 0;
							}


							// Allow certain file formats
							if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
							&& $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "txt" && $imageFileType != "html"
							&& $imageFileType != "bmp" && $imageFileType != "tif" && $imageFileType != "tiff" && $imageFileType != "docx" && $imageFileType != "xlsx") {
				
								echo "Sorry, only JPG, JPEG, PNG, GIF, PDFs, DOCS, TXTs, HTML, xlsx, BMP and tif/tiff file types are allowed. <br>";
								$uploadOk = 0;
							}
							// Check if $uploadOk is set to 0 by an error
							if ($uploadOk == 0) {
								
								echo "Sorry, your file was not uploaded.";
								// if everything is ok, try to upload file
							} else {
									if (move_uploaded_file($_FILES[$fileBaseName]["tmp_name"], $target_file)) {
										echo "The file ". basename( $_FILES[$fileBaseName]["name"]). " has been uploaded. <br>";
									} else {
										echo "Sorry, the following error was encountered when attempting to upload your file. <br>";
										 print_r( error_get_last() );
									}
								}		

						}else{
							
							//echo "Sorry, both an encounter note and a transaction detail are required to uploaded and named appropriately. <br>";
							
							//$errors[]="Sorry, both an encounter note and a transaction detail are required to uploaded and named appropriately. <br>";
							//print_r( error_get_last() );
							//$uploadOk = 0;
							

							if($errors){
								print '<center><h2 class = "error"> THE following errores were encountered:</h2>';
								print '<ul><li>';
								print implode('</li><li>', $errors);
								print '</li></ul></center>';

								$uploadOk = 0;
								$errors=array();//reset the errors array
								//showPage($_SESSION['username'], $_SESSION['access'],validateNewRefund($errors));
								
								include 'connectToDB.php';
								
								$refundToDelete=$refundID_just_created-1;
							
								$query_delete="DELETE FROM refund WHERE refund_id='{$refundToDelete}'";
								$result_delete = mysqli_query($db,$query_delete);
							
								$auto_increment=$refundToDelete;
								//reset auto increment id in case of errors
								$query_resetAutoIncrement="ALTER TABLE refund AUTO_INCREMENT = {$auto_increment}";
								
		
								$result = mysqli_query($db,$query_resetAutoIncrement);
								
				
								//showPage($_SESSION['username'], $_SESSION['access'],validateNewRefund($errors));	
								$_SESSION['SAVE_POST']=$_POST;
								
								//include 'dump_all_page_contents.php';

								print '<h4 align="center"><a href="addrefund.php">Return to Add Refund Page</a></h4>';
								
							}

							
							
							
						}
						
					
					}//end for loop for the two required files of personal
					

			
				}//end else personal refund
				
				
				if($uploadOk){ //only evaluate extra uploads if the necessary uploads are successful
					//continue...
					//before was [file1]['name'], file2 etc..
	
				
						if(isset($_POST['file4']) || isset($_POST['file5']) ){
							
							$target_file = $target_dir . basename($_FILES[$fileBaseName]["name"]);
						
		
							for($x=4;$x<=5;$x++){//build up string of filenames
								
								$fileBaseName="file";
								$fileBaseName.=(string)$x;
								
								if(strlen($_FILES[$fileBaseName]["name"])>0){
					
								
									$target_file = $target_dir . basename($_FILES[$fileBaseName]["name"]);
									
									$uploadOk = 1;
									$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

									
									// Check if file already exists
									if (file_exists($target_file)) {
					
										echo "Sorry, file already exists. <br>";
										$uploadOk = 0;
									}
									// Check file size
									if ($_FILES[$fileBaseName]["size"] > 500000) {
										echo "Sorry, your file is too large. <br>";
										$uploadOk = 0;
									}


									// Allow certain file formats
									if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
									&& $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "txt" && $imageFileType != "html"
									&& $imageFileType != "bmp" && $imageFileType != "tif" && $imageFileType != "tiff" && $imageFileType != "docx" && $imageFileType != "xlsx") {
						
										echo "Sorry, only JPG, JPEG, PNG, GIF, PDFs, DOCS, TXTs, HTML, xlsx, BMP and tif/tiff file types are allowed. <br>";
										$uploadOk = 0;
									}
									// Check if $uploadOk is set to 0 by an error
									if ($uploadOk == 0) {
										
										echo "Sorry, your file was not uploaded.";
										// if everything is ok, try to upload file
									} else {
											if (move_uploaded_file($_FILES[$fileBaseName]["tmp_name"], $target_file)) {
												echo "The file ". basename( $_FILES[$fileBaseName]["name"]). " has been uploaded. <br>";
											} else {
												echo "Sorry, the following error was encountered when attempting to upload your file. <br>";
												 print_r( error_get_last() );
											}
										}		

								}

							}//last ifs for loop
						
						
						}//last if for file4 and file5
						
				}//END only evaluate extra uploads if the necessary uploads are successful
				
		// }//else of the mkdir
		
		
	

	

	//return upload outcome
	if($uploadOk==1){
		//echo 'return true';
		return 1;
	}else{
		//echo 'return false';
		return 0;
	}
	
	
} //end function



//Checks that new refund data submitted is valid or returns an array of errors
function validateNewRefund (&$errors){
	

	//$errors = array();
	$ctr_attachments=0;
	
	if ($_POST['refund_type']=="Commercial"){
		//check for at least three uploads
		foreach($_FILES as $key => $value){
			
			if($value['size']){//if size greater than 0, meaning something is attached, increment ctr_attachments;
				$ctr_attachments++;
			}
			
		}
		
		if($ctr_attachments<3){
			$errors[]='In order to complete the refund creation Commercial Refunds Require at least three documents to be attached.';	

		}

	}else{
		//check for at least two uploads

		foreach($_FILES as $key => $value){
			
			if($value['size']){//if size greater than 0, meaning something is attached, increment ctr_attachments;
				$ctr_attachments++;
			}
			
		}
		
		if($ctr_attachments<2){
			$errors[]='In order to complete the refund creation Personal Refunds Require at least two documents to be attached.';	

		}	
		
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


	if(sizeof($_POST['encounters'])>1){
		
			foreach($_POST['encounters'] as $key => $value){
				
			if(strlen($value<3)){
				
				$errors[]='Encounter Number: '.($key+1).' must be at least three digits long.';	

			}
	
		
	}
		
	}elseif(strlen($_POST['encounters'][0])<3){
		
		$errors[]='Encounter Numbers are required and must be at least three digits long.';	

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
		
		print "<table class = \"topMenu\">
		<tr><td><a href=\"reset_home.php\"  class = \"button\" >Home</td>
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

	/* commented out for debugging
	if($errors){
		//show errors at top of page
		print '<h2 class = "error"> The following errors were encountered:</h2>';
		print '<ul><li>';
		print implode('</li><li>', $errors);
		print '</li></ul>';
	}
	*/
	
	$array_of_statesFull= array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District of Columbia', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah','Virginia', 'Vermont', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming');

	$array_of_statesShort=array("AK", "AL","AZ","AR","CA", "CO", "CT", "DE","DC","FL", "GA", "HI","ID", "IL", "IN", "IA", "KS", "KY", "LA","ME","MD","MA","MI", "MN","MS","MO", "MT","NE","NV","NH", "ND", "NJ", "NM",  "NY", "NC", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VA", "VT", "WA","WV", "WI","WY");

	/*
	
	
			  <tr>
            <td>Type of Refund</td>
            <td>Commercial<input maxlength="50" name="refund_type_commercial" id="refund_type_commercial" type="checkbox" value ="1" onClick="{removeFocusPersonal()}"> &nbsp;&nbsp;&nbsp;&nbsp;
			Personal<input maxlength="50" name="refund_type_personal" id="refund_type_personal" type="checkbox" value ="1" onClick="{removeFocusCommercial()}"></td>

          </tr>
	
	*/
	
	//include 'connectToDB.php';

	if (isset($_POST['amount'])){
		
		
		$query ="SELECT refund_id from refund ORDER BY refund_id DESC LIMIT 1";
		$resultLargestRefundID= mysqli_query($db,$query); 
		
		while ($row = mysqli_fetch_array($resultLargestRefundID)){
			$nextRefundID=$row['refund_id'];
		}
		
		$nextRefundID+=1;

	print <<<ADDREFUNDPAGE


		<h2 align="center">Add a New Refund</h2>
		<a href="index.php">Back to Refunds</a>
	<br/><br/>
		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="add_refund" enctype="multipart/form-data">
		<button class="add_field_button">Add More Fields</button>

      <table style="width: 100%" border="1">
        <tbody>
		
		<tr>
			<td><b>Refund ID</b></td>
			<td><b>{$nextRefundID}</b></td>
		</tr>
		
	    <tr>
            <td>Type Of Refund</td>
            <td>

			<select name="refund_type">

ADDREFUNDPAGE;

		print "<option value=\"\"";
		print "></option>";	

			print "<option value='Commercial' selected>Commercial</option> ";
			print "<option value='Personal'>Personal</option> ";

		print <<<ADDREFUNDPAGE
		  </select>
		</td>
	  </tr>		  
		 
          <tr>
            <td>Urgent</td>
            <td><input maxlength="50" name="urgent" type="checkbox" value ="1"><br>
            </td>
          </tr>
          <tr>
          	<td>Amount</td>
			
          	<td>$<input maxlength="50" name="amount" type="text" value ="{$_POST['amount']}"><br />
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

			<select name="state">

ADDREFUNDPAGE;

		print "<option value=\"\"";
		print "></option>";	
		
		$loopstateCtr=0;

		foreach ($array_of_statesFull as $key => $value){
		
			if($_POST['state']==$array_of_statesShort[$loopstateCtr]){
				print "<option value=\"{$array_of_statesShort[$loopstateCtr]}\"";			
				print " selected >{$value}</option>";	
			}else{
				print "<option value=\"{$array_of_statesShort[$loopstateCtr]}\"";			
				print " >{$value}</option>";	
			}
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

  
ADDREFUNDPAGE;


	
print <<<ADDREFUNDPAGE


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
          	<td>Encounter Note</td>
          	<td><input type="file" name="encounter_note" ></td>
          </tr>
          <tr>
          	<td>Transaction Detail Page</td>
          	<td><input type="file" name="transaction_detail_page"></td>
          </tr>
          <tr>
          	<td>Commercial Paperwork</td>
          	<td><input type="file" name="commercial_paperwork"></td>
          </tr>
          <tr>
          	<td>Attachment 4</td>
          	<td><input type="file" name="file4"></td>
          </tr>
          <tr>
          	<td>Attachment 5</td>
          	<td><input type="file" name="file5"></td>
          </tr>
		  
		  	<tr>
				<td> Encounter Number: </td>
				<td><input name="encounters[]" type="text" value=""></td>
			</tr>
		  
        </tbody>
      </table>
      <input type="hidden" name="_submit_check" value="1" />
	  <br/>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Request Refund</button></form>
ADDREFUNDPAGE;
	}else{
		
		//include 'dump_all_page_contents.php';
		
		$_POST=$_SESSION['SAVE_POST'];
				
		$query ="SELECT refund_id from refund ORDER BY refund_id DESC LIMIT 1";
		$resultLargestRefundID= mysqli_query($db,$query); 
		
		while ($row = mysqli_fetch_array($resultLargestRefundID)){
			$nextRefundID=$row['refund_id'];
		}
		
		$nextRefundID+=1;
			
		print <<<ADDREFUNDPAGE

		<h2 align="center">Add a New Refund</h2>
			<a href="index.php">Back to Refunds</a>
	<br/><br/>

	<form method="POST" action="{$_SERVER['PHP_SELF']}" name="add_refund" enctype="multipart/form-data" >
      <table style="width: 100%" border="1" class="input_fields_wrap">
        <tbody>
		
		<tr>
			<td><b>Refund ID</b></td>
			<td><b>{$nextRefundID}</b></td>
		</tr>  
	        
		<tr>
            <td>Type Of Refund</td>
            <td>

			<select name="refund_type">

ADDREFUNDPAGE;

		print "<option value=\"\"";
		print "></option>";	

			print "<option value='Commercial' selected>Commercial</option> ";
			print "<option value='Personal'>Personal</option> ";

		print <<<ADDREFUNDPAGE
		  </select>
		</td>
	  </tr>		 		  

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
            <td><input  maxlength="10" name="zip" type="text" value="{$_POST['zip']}">
            </td>
          </tr>

ADDREFUNDPAGE;



print <<<ADDREFUNDPAGE

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
          	<td>Encounter Note</td>
          	<td><input type="file" name="encounter_note" ></td>
          </tr>
          <tr>
          	<td>Transaction Detail Page</td>
          	<td><input type="file" name="transaction_detail_page"></td>
          </tr>
          <tr>
          	<td>Commercial Paperwork</td>
          	<td><input type="file" name="commercial_paperwork"><font color=red>* Only Required if Commercial Refund</font></td>
          </tr>
          <tr>
          	<td>Attachment 4</td>
          	<td><input type="file" name="file4"></td>
          </tr>
          <tr>
          	<td>Attachment 5</td>
          	<td><input type="file" name="file5"></td>
          </tr>
		  <tr>

			<tr>
				<td> Encounter Number: </td>
				<td><input name="encounters[]" type="text" value=""></td>
			</tr>
			
			
		
	  </tr>
		  
        </tbody>
      </table><br>
	  <button class="add_field_button">Add Additional Encounter Numbers associated with this Refund</button><br><br>

      <input type="hidden" name="_submit_check" value="1" />
	  <br/><br>
     <center><button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Request Refund</button></center></form>
ADDREFUNDPAGE;

?>

<?php		
	}
	showFooter();

}


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

?>