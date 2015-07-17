<?php

/*********************************************************************************************************************************
//WORKFLOW FOR REFUND APPLICATION
// Refund Arrives -> goes into queue of creater.
// creater -> approves if they are in billing o/w, they assign to someone in accounting or an admin
// after someone who is billing has approved -> they assign to someone who is in accounting
// after the person in accounting approves -> it's officially approved and moves over to exist only under the refund screen
// from there it can be modified if needed further by an admin.
/*********************************************************************************************************************************/

//start session
ini_set('session.gc_maxlifetime', 600);
session_start();

//require('refundFunctions.php');
include 'lib\refundFunctions.php';
include 'connectToDB.php'; 

//include 'validateLogin.php';   //uncomment this to show the first edit screen
//require_once "Mail.php"; (File doesn't exist)

//echo 'Dump all session contents: ';
//include 'dump_all_page_contents.php'; 

//die();

if(isset($_GET)){
	
	////ORDERING OF THE COLUMNS OF THE MAIN REFUNDS PAGE//////////////////////////////////////////////////////////////////////////////////////////////
	if( isset($_GET['encounter_date']) && $_GET['encounter_date']=='y'){
		
		if(!isset($_SESSION['order_ct_date'])){
			$_SESSION['order_ct_date']=0;
		}else{
			$_SESSION['order_ct_date']++;
		}
		if ($_SESSION['order_ct_date']%2==0)
			$_SESSION['order']=" dt_request DESC";
		else
			$_SESSION['order']=" dt_request ASC";
	
	}
	
		if( isset($_GET['urgent']) && $_GET['urgent']=='y'){
		
		if(!isset($_SESSION['order_ct_urgent'])){
			$_SESSION['order_ct_urgent']=0;
		}else{
			$_SESSION['order_ct_urgent']++;
		}
		if ($_SESSION['order_ct_urgent']%2==0)
			$_SESSION['order']=" urgent DESC";
		else
			$_SESSION['order']=" urgent ASC";
		}
	
	
	if(isset($_GET['encounter_num']) && $_GET['encounter_num']=='y'){
		
		if(!isset($_SESSION['order_ct_num'])){
			$_SESSION['order_ct_num']=0;
		}else{
			$_SESSION['order_ct_num']++;
		}
		
		if ($_SESSION['order_ct_num']%2==0)
			$_SESSION['order']=" NG_enc_id DESC";
		else
			$_SESSION['order']=" NG_enc_id ASC";
		

	}
	
		if(isset($_GET['refund_id']) && $_GET['refund_id']=='y'){
		
		if(!isset($_SESSION['order_ct_refund_id'])){
			$_SESSION['order_ct_refund_id']=0;
		}else{
			$_SESSION['order_ct_refund_id']++;
		}
		
		if ($_SESSION['order_ct_refund_id']%2==0)
			$_SESSION['order']=" refund_id DESC";
		else
			$_SESSION['order']=" refund_id ASC";
		
		//echo 'session order is: <br>';
		//echo $_SESSION['order'].'<br><br>';
	}
	
		
	if(isset($_GET['payable_order']) && $_GET['payable_order']=='y'){
		
		if(!isset($_SESSION['order_ct_payable'])){
			$_SESSION['order_ct_payable']=0;
		}else{
			$_SESSION['order_ct_payable']++;
		}
		
		if ($_SESSION['order_ct_payable']%2==0)
			$_SESSION['order']=" payable DESC";
		else
			$_SESSION['order']=" payable ASC";
		

	}
	
	if(isset($_GET['requested_by']) && $_GET['requested_by']=='y'){
		
		if(!isset($_SESSION['order_ct_request'])){
			$_SESSION['order_ct_request']=0;
		}else{
			$_SESSION['order_ct_request']++;
		}
		
		if ($_SESSION['order_ct_request']%2==0)
			$_SESSION['order']=" U.last_name DESC";
		else
			$_SESSION['order']=" U.last_name ASC";

	}
	
	if(isset($_GET['amount_order']) && $_GET['amount_order']=='y'){
		
		if(!isset($_SESSION['order_ct_amount'])){
			$_SESSION['order_ct_amount']=0;
		}else{
			$_SESSION['order_ct_amount']++;
		}
		
		if ($_SESSION['order_ct_amount']%2==0)
			$_SESSION['order']=" amount DESC";
		else
			$_SESSION['order']=" amount ASC";

	}
	
		
	if(isset($_GET['status_order']) && $_GET['status_order']=='y'){
		
		if(!isset($_SESSION['order_ct_status'])){
			$_SESSION['order_ct_status']=0;
		}else{
			$_SESSION['order_ct_status']++;
		}
		
		if ($_SESSION['order_ct_status']%2==0)
			$_SESSION['order']=" status DESC";
		else
			$_SESSION['order']=" status ASC";
		

	}
	
	/////////////////////////END ORDERING/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
}


if (!isset($_GET['action']) && !isset($_GET['report_id']) && !isset($_POST['report_id'])){
	include 'validateLogin.php';   //show the whole list of refunds if an action isn't set--meaning they haven't selected whether to edit, void or approve yet.
}


if(!isset($_GET['report_id']) && !isset($_POST['report_id'])){


if (array_key_exists('userid', $_SESSION)){	//If user is logged, check for access level 

	if($_SESSION['access']=='S' OR $_SESSION['access']=='A' OR $_SESSION['access']=='U'){
		
		//check for $_GET['refund_id']. If set, show edit page for that user. Otherwise, show list of users
		//if($_GET['refund_id']){
		
		if(sizeof($_POST)==0){
			if (!isset($_GET['refund_id']) || ( ($_GET['refund_id']!="") && ($_GET['refund_id']!=NULL))){

				if(isset($_GET['action']) && $_GET['action']=='edit'){
					showEditPage();
				} elseif(isset($_GET['action']) && $_GET['action']=='delete'){
					showDelPage(); //this function doesn't seem to exist yet.
				} 
				elseif(isset($_GET['action']) && $_GET['action']=='reject'){
					showRejPage(); //this function doesn't seem to exist yet.
				}
				elseif(isset($_GET['action']) && $_GET['action']=='approve'){
					showApprovePage(); //this function doesn't seem to exist yet
				}elseif(isset($_GET['action']) && $_GET['action']=='assign'){
					showAssignPage(); 
				}
			}
		}
		
		
		
		/*
		if(isset($_POST['_search_submit']) && $_POST['_search_submit']!="" && $_POST['_search_submit']!=NULL){ 
		include 'dump_all_page_contents.php'; 

		}
		*/
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
		if(isset($_POST['_assign_submit']) && $_POST['_assign_submit']!="" && $_POST['_assign_submit']!=NULL){ 
		
		//$db = mysqli_connect('localhost','root','','pt_refund'); 

			$now = date("Y-m-d H:i:s");		
			
			//LOGIC FOR AMTS GREATER THAN $500/////////////////////////////////////////////////////////////////////////////////////////
			$refund_amt=0;
			$query = "SELECT amount FROM refund WHERE refund_id = {$_POST['refund_id']} ";
			$result = mysqli_query($db,$query);
			
					while ($row = mysqli_fetch_array($result)){
						$refund_amt=$row['amount'];
					}
					
			if($refund_amt>500){ //assign to Jean for a secondary PAR2 Approval 
					//update the record in the DB as voided
					$query = "UPDATE refund 
					SET
					modfied_by={$_SESSION['userid']}, 
					modified_dt='{$now}',
					assigned_to='1',
					WHERE refund_id = {$_POST['refund_id']} ";
					
					$result = mysqli_query($db,$query);
				
				
			}	else{	

					//update the record in the DB as voided
					$query = "UPDATE refund 
					SET
					modfied_by={$_SESSION['userid']}, 
					modified_dt='{$now}',
					assigned_to={$_POST['assignee']}
					WHERE refund_id = {$_POST['refund_id']} ";
					
					$result = mysqli_query($db,$query);
						
			}
			
			//LOGIC FOR AMTS GREATER THAN $500/////////////////////////////////////////////////////////////////////////////////////////
			
			if (mysqli_error($result)){
				print mysqli_error($result);
			}
			
				
				//START Derek Hack////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//Hack inserted because app previously wasn't immediately displaying changes, forces a refresh of page if the form has been submitted.
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				if(isset($_POST['_assign_submit']) && $_POST['refund_id']){ //need to reevaluate
					
					//echo 'I came inside of the if';
					//die();
					//include 'dump_all_page_contents.php'; 
					//build up the redirect string to redirect to the edit page of the refund you are currently editing (needs to reference correct refund_id)
					//format:
					//?refund_id=3&action=delete
					$refresh_id="?refund_id=";
					$refresh_id.=$_POST['refund_id'];
					$refresh_id.="&action=assign";
				
					Header('Location: '.$_SERVER['PHP_SELF'].$refresh_id);
					
				}
				//END Derek Hack//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			
			
			//show successful void message
			print '<h3 align="center"> Refund for  '.$_POST['payable'].' has been successfully re-assigned!</h3>';
			print '<h4 align="center"><a href="refunds.php">Return to Refunds Page</a></h4>';
				
				
		}
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		
		

		//once user is authenticated, check to see if this form has been submitted
		elseif(isset($_POST['_edit_submit']) && $_POST['_edit_submit']!="" && $_POST['_edit_submit']!=NULL){ 
		

		//var_dump($_POST);
		
		//Edit user form has been submitted so it's time to update the database

			//uncomment the next line to toggle session dumps on and off
			//include 'dump_all_page_contents.php';
			
			if(validateRefundChanges()=='valid'){ //if no errors, update user in db and show success message
			

			//	echo 'passed validateRefundChanges() within accounting_refunds.php line 259';

			//	die();

		
				
				//check for errors
				echo 'if no errors, update user in db and show success message';
				//update user in db
				$now = date("Y-m-d H:i:s");			

				/*
				$query = "UPDATE refund SET NG_enc_id = '{$_POST['enc_nbr']}', dt_required = '{$_POST['dt_required']}', 
				amount = '{$_POST['amount']}', payable='{$_POST['payable']}', addr_ln_1 ='{$_POST['addr_ln_1']}', 
				addr_ln_2='{$_POST['addr_ln_2']}', city ='{$_POST['city']}', state='{$_POST['state']}', zip='{$_POST['zip']}', 
				purpose='{$_POST['purpose']}', status='UPDATED', modfied_by={$_SESSION['userid']}, modified_dt='{$now}',
				comments ='{$_POST['comments']}' WHERE refund_id = {$_POST['refund_id']} ";
				*/
				$query = "UPDATE refund SET NG_enc_id = '{$_POST['enc_nbr']}', dt_required = '{$_POST['dt_required']}', 
				amount = '{$_POST['amount']}', payable='{$_POST['payable']}', addr_ln_1 ='{$_POST['addr_ln_1']}', 
				addr_ln_2='{$_POST['addr_ln_2']}', city ='{$_POST['city']}', state='{$_POST['state']}', zip='{$_POST['zip']}', 
				purpose='{$_POST['purpose']}', modfied_by={$_SESSION['userid']}, modified_dt='{$now}',
				comments ='{$_POST['comments']}' WHERE refund_id = {$_POST['refund_id']} ";
				
				$result = mysqli_query($db,$query);
				if (@mysqli_error($result)){
					print mysqli_error($result);
				}
				
				//now select accounting email recipients and notify them
				
				
				$rec_1="";$rec_2="";$rec_3="";
				
				$query = "SELECT recipient_1,recipient_2,recipient_3 FROM email_recipients WHERE step='accounting' ";
				
				$result = mysqli_query($db,$query);
				
				$rowUserIds=mysqli_fetch_array($result);
					$rec_1=$rowUserIds['recipient_1'];
					$rec_2=$rowUserIds['recipient_2'];
					$rec_3=$rowUserIds['recipient_3'];


				
				if($rec_1!="" && $rec_1!=NULL){
					//select their names and email them

					$query = "SELECT username FROM users WHERE user_id='{$rec_1}'";
					$result = mysqli_query($db,$query);
					
					$rowUserIds=mysqli_fetch_array($result);
					
					$to=$rowUserIds['username'].'@chcb.org';
					
					echo 'the to is <br>';
					echo $to.'<br>';
					
				
					//OTHER EMAIL PARAMETERS////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
					//send notification that a new refund has been created
					//commented out for the time being 

					$from = "Patient Refund <noreply@chcb.org>";
					$subject = "Updated Patient Refund Request";
					$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been updated. Please login to the Patient Refund web application to review.";

					$host = "ssl://smtpout.secureserver.net";
					$port = "465";
					$username = "jonathan@jonathanbowley.com";
					$password = "paw52beh";
					//OTHER EMAIL PARAMETERS////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
					/*
					$headers = array ('From' => $from,'To' => $to,'Subject' => $subject);
					$smtp = Mail::factory('smtp',
					array ('host' => $host,
						'port' => $port,
						'auth' => true,
						'username' => $username,
						'password' => $password));
					*/

					//uncomment below to actually mail
					//$mail = $smtp->send($to, $headers, $body);
					
					/*
					if (PEAR::isError($mail)) {
					 echo("<p>" . $mail->getMessage() . "</p>");
					} 				
					*/
					
				}
				if($rec_2!="" && $rec_2!=NULL){
					//select their names and email them

					$query = "SELECT username FROM users WHERE user_id='{$rec_2}'";
					$result = mysqli_query($db,$query);
					
					$rowUserIds=mysqli_fetch_array($result);
					
					$to=$rowUserIds['username'].'@chcb.org';
					
				
					//OTHER EMAIL PARAMETERS////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
					//send notification that a new refund has been created
					//commented out for the time being 

					$from = "Patient Refund <noreply@chcb.org>";
					$subject = "Updated Patient Refund Request";
					$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been updated. Please login to the Patient Refund web application to review.";

					$host = "ssl://smtpout.secureserver.net";
					$port = "465";
					$username = "jonathan@jonathanbowley.com";
					$password = "paw52beh";
					//OTHER EMAIL PARAMETERS////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
					/*
					$headers = array ('From' => $from,'To' => $to,'Subject' => $subject);
					$smtp = Mail::factory('smtp',
					array ('host' => $host,
						'port' => $port,
						'auth' => true,
						'username' => $username,
						'password' => $password));
					*/

					//uncomment below to actually mail
					//$mail = $smtp->send($to, $headers, $body);
					
					/*
					if (PEAR::isError($mail)) {
					 echo("<p>" . $mail->getMessage() . "</p>");
					} 				
					*/
					
				}
				if($rec_3!="" && $rec_3!=NULL){
					//select their names and email them

					$query = "SELECT username FROM users WHERE user_id='{$rec_3}'";
					$result = mysqli_query($db,$query);
					
					$rowUserIds=mysqli_fetch_array($result);
					
					$to=$rowUserIds['username'].'@chcb.org';
					
				
					//OTHER EMAIL PARAMETERS////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
					//send notification that a new refund has been created
					//commented out for the time being 

					$from = "Patient Refund <noreply@chcb.org>";
					$subject = "Updated Patient Refund Request";
					$body = "Hello,\n\n patient refund request # {$_POST['refund_id']} has been updated. Please login to the Patient Refund web application to review.";

					$host = "ssl://smtpout.secureserver.net";
					$port = "465";
					$username = "jonathan@jonathanbowley.com";
					$password = "paw52beh";
					//OTHER EMAIL PARAMETERS////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
					/*
					$headers = array ('From' => $from,'To' => $to,'Subject' => $subject);
					$smtp = Mail::factory('smtp',
					array ('host' => $host,
						'port' => $port,
						'auth' => true,
						'username' => $username,
						'password' => $password));
					*/

					//uncomment below to actually mail
					//$mail = $smtp->send($to, $headers, $body);
					
					/*
					if (PEAR::isError($mail)) {
					 echo("<p>" . $mail->getMessage() . "</p>");
					} 				
					*/
					
				}
 

				
				//show success message
				print '<h3 align="center"> Refund for  '.$_POST['payable'].' updated!</h3>';
				print '<h4 align="center"><a href="refunds.php">Return to Refunds Page</a></h4>';
				echo '<br>';
				
				
				//echo 'am i here';
				
				//die();
				//echo "I'm now about to refresh the page <br>";
			
				//START Derek Hack////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//Hack inserted because app previously wasn't immediately displaying changes, forces a refresh of page if the form has been submitted.
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				if(isset($_POST['_edit_submit']) && $_POST['Submit']=="submit"){
					
					//include 'dump_all_page_contents.php'; 
					//build up the redirect string to redirect to the edit page of the refund you are currently editing (needs to reference correct refund_id)
					//format:
					//?refund_id=3&action=edit
					$refresh_id="?refund_id=";
					$refresh_id.=$_POST['refund_id'];
					$refresh_id.="&action=edit";
				
					@Header('Location: '.$_SERVER['PHP_SELF'].$refresh_id);
					
				}
				//END Derek Hack//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						
			} else { //if submitted with errors and not approved/deleted
				//echo 'Im in the else if submitted with errors and not approved/deleted';

				showEditPage($_SESSION['username'], $_SESSION['access'],validateRefundChanges());
			}
			
			//if errors exist, show page again & fill in values
		
		} 
		
		
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		elseif(isset($_POST['_del_submit']) && $_POST['_del_submit']!="" && $_POST['_del_submit']!=NULL){ 
		
			$now = date("Y-m-d H:i:s");			

			//update the record in the DB as voided
			$query = "UPDATE refund SET status='VOIDED', modfied_by={$_SESSION['userid']}, modified_dt='{$now}',voided =1 WHERE refund_id = {$_POST['refund_id']} ";
			$result = mysqli_query($db,$query);
			
			if (@mysqli_error($result)){
				print mysqli_error($result);
			}
			
			
				//START Derek Hack////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//Hack inserted because app previously wasn't immediately displaying changes, forces a refresh of page if the form has been submitted.
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				if(isset($_POST['_del_submit']) && $_POST['Void']=="void"){
					
					//include 'dump_all_page_contents.php'; 
					//build up the redirect string to redirect to the edit page of the refund you are currently editing (needs to reference correct refund_id)
					//format:
					//?refund_id=3&action=delete
					$refresh_id="?refund_id=";
					$refresh_id.=$_POST['refund_id'];
					$refresh_id.="&action=delete";
				
					Header('Location: '.$_SERVER['PHP_SELF'].$refresh_id);
					
				}
				//END Derek Hack//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			
			
			//show successful void message
			print '<h3 align="center"> Refund for  '.$_POST['payable'].' has been successfully voided!</h3>';
			print '<h4 align="center"><a href="refunds.php">Return to Refunds Page</a></h4>';
			
			//echo "I'm about to void you.";
			//die();
		}
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		elseif(isset($_POST['_rej_submit']) && $_POST['_rej_submit']!="" && $_POST['_rej_submit']!=NULL){ 
		
		
				$now = date("Y-m-d H:i:s");			
				
				//select info to build up email for creator notification of rejection//////////////////////////////////////////////////////////////////////////////////////
				$created_by="";	
				$status="";
				$query = "SELECT created_by FROM refund WHERE refund_id = {$_POST['refund_id']} ";
				$result = mysqli_query($db,$query);
					while ($row = mysqli_fetch_array($result)){
						$created_by=$row['created_by'];
					}
					
				$queryUsername = "SELECT username FROM users WHERE user_id='{$created_by}'";
				$resultUsername = mysqli_query($db,$queryUsername);

				$rowUsername=mysqli_fetch_array($resultUsername);
				$to=$rowUsername['username'].'@chcb.org';
				$status="rejected";
				//mail_presets($to,$status);

				//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
							
		
				$query_reject = "UPDATE refund 
					SET status='REJECTED', 
					modfied_by={$_SESSION['userid']}, 
					modified_dt='{$now}',
				WHERE refund_id = {$_POST['refund_id']} ";
				
				$result_reject = mysqli_query($db,$query_reject);
						
				if (@mysqli_error($result_reject)){
					print mysqli_error($result_reject);
				}
			
			
				//START Derek Hack////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//Hack inserted because app previously wasn't immediately displaying changes, forces a refresh of page if the form has been submitted.
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				if(isset($_POST['_rej_submit'])){
					
					//include 'dump_all_page_contents.php'; 
					//build up the redirect string to redirect to the edit page of the refund you are currently editing (needs to reference correct refund_id)
					//format:
					//?refund_id=3&action=delete
					$refresh_id="?refund_id=";
					$refresh_id.=$_POST['refund_id'];
					$refresh_id.="&action=reject";
				
					@Header('Location: '.$_SERVER['PHP_SELF'].$refresh_id);
					
				}
				//END Derek Hack//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			
			//show successful reject message
			print '<h3 align="center"> Refund for  '.$_POST['payable'].' has been successfully rejected!</h3>';
			print '<h4 align="center"><a href="refunds.php">Return to Refunds Page</a></h4>';
			
			
			//email PAR 2 creator that the email was rejected
			
			
			
			
			
			//echo "I'm about to reject you.";
			//die();

		}
		
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		elseif(isset($_POST['_app_submit']) && $_POST['_app_submit']!="" && $_POST['_app_submit']!=NULL){ 
		
			//global $db;

			$now = date("Y-m-d H:i:s");			
			//update the record in the DB as Approved
			
			//approved by this user id
			//$_SESSION['user_id'];
			
			$query = "SELECT dept_id from users WHERE user_id={$_SESSION['userid']}";
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

					$queryCheckStatus = "SELECT billing_initial_approval,billing_final_approval WHERE refund_id = {$_POST['refund_id']} ";
					$resultCheckStatus = mysqli_query($db,$queryCheckStatus);

					while ($rowCheckStatus = @mysqli_fetch_array($resultCheckStatus)){
						$billing_initial_approval=$rowCheckStatus['billing_initial_approval'];
						$billing_final_approval=$rowCheckStatus['billing_final_approval'];
					}
					
					if($billing_initial_approval && $billing_final_approval){
				
						$query = "UPDATE refund 
							SET status='COMPLETED', 
							modfied_by={$_SESSION['userid']}, 
							modified_dt='{$now}',
							accounting_approval=1,
							voided =0 
						WHERE refund_id = {$_POST['refund_id']} ";
					
					}else{
						$query = "UPDATE refund 
							SET status='ACCOUNTING APPROVAL',
							modfied_by={$_SESSION['userid']}, 
							modified_dt='{$now}',
							accounting_approval=1,
							voided =0 
						WHERE refund_id = {$_POST['refund_id']} ";
					
						
					}
				
			}
			elseif($department_name=="Billing"){
				
					$billing_initial_approval=0;
					$accounting_approval=0;
					$billing_final_approval=0;
				
					$queryCheckStatus = "SELECT accounting_approval, billing_initial_approval,billing_final_approval WHERE refund_id = {$_POST['refund_id']} ";
					$resultCheckStatus = mysqli_query($db,$queryCheckStatus);

					while ($rowCheckStatus = @mysqli_fetch_array($resultCheckStatus)){
						$billing_initial_approval=$rowCheckStatus['billing_initial_approval'];
						$accounting_approval=$rowCheckStatus['accounting_approval'];
						$billing_final_approval=$rowCheckStatus['billing_final_approval'];
					}
					
					
			
						//LOGIC FOR AMTS GREATER THAN $500/////////////////////////////////////////////////////////////////////////////////////////
						$refund_amt=0;
						$query = "SELECT amount FROM refund WHERE refund_id = {$_POST['refund_id']} ";
						$result = mysqli_query($db,$query);
				
						while ($row = mysqli_fetch_array($result)){
							$refund_amt=$row['amount'];
						}
						
						if($refund_amt>500){ //Check for both PAR2 Approvals
		
							if($billing_initial_approval && $accounting_approval && $billing_final_approval){
						
								$query = "UPDATE refund 
									SET status='COMPLETED', 
									modfied_by={$_SESSION['userid']}, 
									modified_dt='{$now}',
									voided =0 
								WHERE refund_id = {$_POST['refund_id']} ";
							
							}	
							
						}elseif($billing_initial_approval && $accounting_approval){
							
								$query = "UPDATE refund 
									SET status='COMPLETED', 
									modfied_by={$_SESSION['userid']}, 
									modified_dt='{$now}',
									voided =0 
								WHERE refund_id = {$_POST['refund_id']} ";
							
							
						}elseif($billing_initial_approval){
							$query = "UPDATE refund 
									SET status='Billing Approval', 
									modfied_by={$_SESSION['userid']}, 
									modified_dt='{$now}',
									voided =0 
								WHERE refund_id = {$_POST['refund_id']} ";
						}

									
			
				
			}else{//this means they are an admin with either Approver or SuperUser status, either way they have override approval abilities for purposes of this app
					
					/*
					$query = "UPDATE refund 
						SET status='APPROVED', 
						modfied_by={$_SESSION['userid']}, 
						modified_dt='{$now}',
						voided =0				
					WHERE refund_id = {$_POST['refund_id']} ";
					*/
					
					
					//LOGIC FOR AMTS GREATER THAN $500/////////////////////////////////////////////////////////////////////////////////////////
						$refund_amt=0;
						$query = "SELECT amount FROM refund WHERE refund_id = {$_POST['refund_id']} ";
						$result = mysqli_query($db,$query);
				
						while ($row = mysqli_fetch_array($result)){
							$refund_amt=$row['amount'];
						}
						
						if($refund_amt>500){ //Check for both PAR2 Approvals
		
							if($billing_initial_approval && $accounting_approval && $billing_final_approval){
						
								$query = "UPDATE refund 
									SET status='COMPLETED', 
									modfied_by={$_SESSION['userid']}, 
									modified_dt='{$now}',
									voided =0 
								WHERE refund_id = {$_POST['refund_id']} ";
							
							}	
							
						}elseif($billing_initial_approval && $accounting_approval){
							
								$query = "UPDATE refund 
									SET status='COMPLETED', 
									modfied_by={$_SESSION['userid']}, 
									modified_dt='{$now}',
									voided =0 
								WHERE refund_id = {$_POST['refund_id']} ";
							
							
						}elseif($billing_initial_approval){
							$query = "UPDATE refund 
									SET status='Billing Approval', 
									modfied_by={$_SESSION['userid']}, 
									modified_dt='{$now}',
									voided =0 
								WHERE refund_id = {$_POST['refund_id']} ";
						}
	
					
					
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
			print '<h4 align="center"><a href="refunds.php">Return to Refunds Page</a></h4>';
			
			//echo "I'm about to approve you.";
			//die();
		}
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		
		
	
		//elseif(!$_GET['refund_id']) { //form has not been submitted
		elseif(!isset($_GET['refund_id']) || ( ($_GET['refund_id']!="") && ($_GET['refund_id']!=NULL))) { 
					//form has not been submitted, OR refund_id isnt empty and isnt null				
					//showPage($_SESSION['username'],$_SESSION['access']); //show page only if user is a super user
					//var_dump(isset($_GET['refund_id']));
					//include 'dump_all_page_contents.php';    
					//die();
		}
		else{
			
			//echo 'im in the trailing else, of logins with defined (known) access levels.';
			//die();
		}
		
	} else { //access types that arent defined (random users trying to access this page by manually typing into URL)
			echo 'all other users types OWNED!!';
			showLogin('The current user is not authorized to view this page.');	//all other users types OWNED!!
	}
	
} 

}

elseif($_POST['username']) { //this means user_id isnt defined in the session variable yet = so they arent logged in
							   //if user has attempted to login (as per the post variable username being set), so attempt to validate login

	if(validateLogin($_POST['username'],$_POST['password'])){
		showPage($_SESSION['username'], $_SESSION['access']);	//valid user! Show page!
	} else {
		showLogin('Login invalid. Please try again');	
	}

} elseif(!isset($_GET['report_id']) && !isset($_POST['report_id'])) { //Else show login screen (no user is not logged in and no login attempt has been made)

	//echo 'Hello ET';
	showLogin();
}
	


?>