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

//include 'dump_all_page_contents.php'; 


if(isset($_GET)){
	
	//echo 'coming here first';
	//die();
	
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
	
	//echo '<br> I was coming in here';
	//include 'validateLogin.php';   //show the whole list of refunds if an action isn't set--meaning they haven't selected whether to edit, void or approve yet.
}


if(!isset($_GET['report_id']) && !isset($_POST['report_id'])){


if (array_key_exists('userid', $_SESSION)){	//If user is logged, check for access level 

//echo '<br> inside <br><br>';

	if($_SESSION['access']=='S' OR $_SESSION['access']=='A' OR $_SESSION['access']=='U'){
		
		//check for $_GET['refund_id']. If set, show edit page for that user. Otherwise, show list of users
		//if($_GET['refund_id']){
		
		if(sizeof($_POST)==0){
					//echo 'inside ACCESS levels ';
					
					//include 'dump_all_page_contents.php'; 

			if (!isset($_GET['refund_id']) || ( ($_GET['refund_id']!="") && ($_GET['refund_id']!=NULL))){
				
				//echo '<br> never gets here <br>';
				//include 'dump_all_page_contents.php'; 

				if(isset($_GET['action']) && $_GET['action']=='edit'){
			
					showEditPage();
				} elseif(isset($_GET['action']) && $_GET['action']=='delete'){

					showDelPage(); //this function doesn't seem to exist yet.
				} 
				elseif(isset($_GET['action']) && $_GET['action']=='reject'){
					//echo 'refunds page ';
					//die();
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
		
		//echo 'boo im at the beginning of assign_submit <br>';
		//die();
			if(isset($_POST['assignee']) && $_POST['assignee']!=""){ //need to re-evaluate


				echo 'Executing the UPDATE <br>';
				
				$now = date("Y-m-d H:i:s");			

				//update the record in the DB as voided
				//$query = "UPDATE refund SET modified_by='{$_SESSION['userid']}', modified_dt='{$now}', assigned_to='{$_POST['assignee']}' WHERE refund_id = '{$_POST['refund_id']}' ";
				$query = "UPDATE refund SET assigned_to='{$_POST['assignee']}' WHERE refund_id = '{$_POST['refund_id']}' ";

				$result = mysqli_query($db,$query);

				if (mysqli_error($result)){
					print mysqli_error($result);
				}
				
				//select info to build up email for creator notification //////////////////////////////////////////////////////////////////////////////////////
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
					$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has been re-assigned to ".$_POST['assignee'].". <br>  This refund was marked as URGENT.";
						
					mail_presets($to,$status); //creator
					mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)
					
				}else{
		
					//$last_id = mysqli_insert_id($db);
					$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has been re-assigned to ".$_POST['assignee'];
						
					mail_presets($to,$status); //creator
					
				}
				

					$status="Re-Assigned";
					
					///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

					//SEND THE warning emails////////////////////////////////////////////////////////////////////////////////////////////////////////////
					if ($interval->days>=30 && $row['status']!="COMPLETED"){
						mail_presets($_SESSION['mailto_over30'],$status);
					}elseif($interval->days>=15 && $row['status']!="COMPLETED"){
						mail_presets($_SESSION['mailto_over15'],$status);
					}elseif(($interval->days<=1) && $row['status']!="COMPLETED"){//for initial creation email
						//mail_presets($_SESSION['mailto_over15']);
						//need to mail to whoever is set to receive these emails
					}
					//SEND THE warning emails////////////////////////////////////////////////////////////////////////////////////////////////////////////

					//email the recipients their notifications///////////////////////////////////////////////////////////////////////////////////
			
				
				//show successful assign message
				print '<h3 align="center"> Refund for  '.$_POST['payable'].' has been successfully re-assigned!</h3>';
				print '<h4 align="center"><a href="index.php">Return to Refunds Page</a></h4>';
				
				////
				

					
					//Header('Location: '.$_SERVER['PHP_SELF'].$refresh_id);
					//trick to reload the page after DB update?
			
			}//end of assignee

			
				/*
				//START Derek Hack////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//Hack inserted because app previously wasn't immediately displaying changes, forces a refresh of page if the form has been submitted.
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				if(isset($_POST['_assign_submit']) && $_POST['refund_id']){ //need to reevaluate
					
					echo 'I came inside of the if';
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
				*/
			
		
				
				
		} //end isset($_POST['_assign_submit'])
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//once user is authenticated, check to see if this form has been submitted
		elseif(isset($_POST['_edit_submit']) && $_POST['_edit_submit']!="" && $_POST['_edit_submit']!=NULL){ 
	
		//Edit user form has been submitted so it's time to update the database

			//uncomment the next line to toggle session dumps on and off
			//include 'dump_all_page_contents.php';
			
			if(validateRefundChanges()=='valid'){ //if no errors, update user in db and show success message
			
			
				echo 'passed validateRefundChanges() within refunds.php line 268 <br>';

				//check for errors
				echo 'if no errors, update user in db and show success messages <BR>';
				//update user in db
				$now = date("Y-m-d H:i:s");				
				$query = "UPDATE refund SET NG_enc_id = '{$_POST['enc_nbr']}', dt_required = '{$_POST['dt_required']}', 
				amount = '{$_POST['amount']}', payable='{$_POST['payable']}', addr_ln_1 ='{$_POST['addr_ln_1']}', 
				addr_ln_2='{$_POST['addr_ln_2']}', city ='{$_POST['city']}', state='{$_POST['state']}', zip='{$_POST['zip']}', 
				purpose='{$_POST['purpose']}', modfied_by={$_SESSION['userid']}, modified_dt='{$now}',
				comments ='{$_POST['comments']}' WHERE refund_id = {$_POST['refund_id']} ";
				

				$result = mysqli_query($db,$query);
				if (@mysqli_error($result)){
					print mysqli_error($result);
				}
				
				
				echo 'the QQuery is <br>';
				echo $query;
				
				///GET DEPT NAME//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					//get the name of the department, so we can select the recipients of that department from email_recipients
					$query_dept_id="SELECT dept_id FROM users WHERE user_id={$_SESSION['userid']}";
					$result_dept_id = mysqli_query($db,$query_dept_id);
					$rowquery_dept_id=mysqli_fetch_array($result_dept_id);

					
					$query_name="SELECT name FROM departments WHERE dept_id={$rowquery_dept_id['dept_id']}";
					$result_name = mysqli_query($db,$query_name);
					$rowquery_dept_name=mysqli_fetch_array($result_name);				
				
				///GET DEPT NAME//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
							
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
				$status="UPDATED/EDITED";
				
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

				mail_presets($to,$status);
				
	
				
				/*
				//SEND OFF EMAIL HERE
				
				//send notification that a new refund has been created
				//commented out for the time being 
				
				*/
				
				//show success message
				print '<h3 align="center"> Refund for  '.$_POST['payable'].' updated!</h3>';
				print '<h4 align="center"><a href="refunds.php">Return to Refunds Page</a></h4>';
				echo '<br>';
				
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
			
				showEditPage($_SESSION['username'], $_SESSION['access'],validateRefundChanges());
			}
			
			//if errors exist, show page again & fill in values
		
		} 
		
		
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		elseif(isset($_POST['_del_submit']) && $_POST['_del_submit']!="" && $_POST['_del_submit']!=NULL){ 
			
			$now = date("Y-m-d H:i:s");		

			$query_status="SELECT status FROM refund WHERE refund_id={$_POST['refund_id']}";
			$result_status = mysqli_query($db,$query_status);
			$rowquery_status=mysqli_fetch_array($result_status);
			
		
			//update the record in the DB as voided
			$query = "UPDATE refund SET 
			status='VOIDED', 
			modfied_by={$_SESSION['userid']}, 
			modified_dt='{$now}',voided =1 
			WHERE refund_id = {$_POST['refund_id']} ";
			
			$result = mysqli_query($db,$query);
			
			//TRACK CHANGES
				$status_before=$rowquery_status['status'];
				$status_after='VOIDED';
				trackRefundChanges($_POST,$status_before,$status_after);		
			//TRACK THE CHANGES		
			
			if (@mysqli_error($result)){
				print mysqli_error($result);
			}
			
					///GET DEPT NAME//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					//get the name of the department, so we can select the recipients of that department from email_recipients
					$query_dept_id="SELECT dept_id FROM users WHERE user_id={$_SESSION['userid']}";
					$result_dept_id = mysqli_query($db,$query_dept_id);
					$rowquery_dept_id=mysqli_fetch_array($result_dept_id);

					
					$query_name="SELECT name FROM departments WHERE dept_id={$rowquery_dept_id['dept_id']}";
					$result_name = mysqli_query($db,$query_name);
					$rowquery_dept_name=mysqli_fetch_array($result_name);				
				
				///GET DEPT NAME//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	

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
				$status="VOIDED/DELETED";
				
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

				mail_presets($to,$status);	
				
	

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
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

				$query_status="SELECT status FROM refund WHERE refund_id={$_POST['refund_id']}";
				$result_status = mysqli_query($db,$query_status);
				$rowquery_status=mysqli_fetch_array($result_status);				

				$query_reject = "UPDATE refund SET 
				status='REJECTED', 
				modified_by='{$_SESSION['userid']}', 
				modified_dt='{$now}',
				rejected =1 
				WHERE refund_id = '{$_POST['refund_id']}' ";
				
				$result_reject = mysqli_query($db,$query_reject);
				
				
				//TRACK CHANGES
					$status_before=$rowquery_status['status'];
					$status_after='REJECTED';
					trackRefundChanges($_POST,$status_before,$status_after);		
				//TRACK THE CHANGES		
				

				if (@mysqli_error($result_reject)){
					print mysqli_error($result_reject);
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
				
				//dfsds
				//send notification that a refund has been rejected: call mail_presets
				//RULE: ON Rejection:
				//IF urgent status email both creator and Erika, otherwise just email Erika///////////////////////////////////////////

				/*
				$query = "SELECT username FROM users WHERE user_id='{$_SESSION['userid']}'";
				$result = mysqli_query($db,$query);
				$rowUserNames=mysqli_fetch_array($result);
				//dynamically build the to address from the username selected based on the recipients specified by the step in the process
				$to=$rowUserNames['username'].'@chcb.org'; //build the creator email
				*/

				if($is_urgent){ //verify that this works as intended
				
					//$last_id = mysqli_insert_id($db);
					$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has been rejected. <br>  This refund was marked as URGENT.";
										

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
	
						
					mail_presets($to,$status); //creator
					mail_presets("ebrown@chcb.org",$status); //email erika (ebrown@chcb.org)
				}else{
		
					//$last_id = mysqli_insert_id($db);
					$status="The Refund for ".$payable_to." with a Refund ID of ".$_POST['refund_id']." has been rejected.";
					

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

					
					mail_presets($to,$status);
					
				}
								
				//END Send Emails Upon Rejection ////////////////////////////////////////////////////////////////////////////////////////
				
				
				//$status="REJECTED";
				
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


			
		

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//die();

			
				//START Derek Hack////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//Hack inserted because app previously wasn't immediately displaying changes, forces a refresh of page if the form has been submitted.
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				//Refresh the page if you just edited a refund, but before you hit the back to refunds page
				if(isset($_POST['_rej_submit'])){
					
					//echo 'do I enter';
					
					//include 'dump_all_page_contents.php'; 
					//build up the redirect string to redirect to the edit page of the refund you are currently editing (needs to reference correct refund_id)
					//format:
					//?refund_id=3&action=delete
					$refresh_id="?refund_id=";
					$refresh_id.=$_POST['refund_id'];
					$refresh_id.="&action=reject";
				
					echo $refresh_id;
					
					//@Header('Location: '.$_SERVER['PHP_SELF'].$refresh_id);
					
				}
				//END Derek Hack//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			
			//show successful reject message
			print '<h3 align="center"> Refund for  '.$_POST['payable'].' has been successfully RRejected!</h3>';
			print '<h4 align="center"><a href="refunds.php">Return to Refunds Page</a></h4>';
			
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
					
					
					if($billing_initial_approval && $accounting_approval){
				
						$query = "UPDATE refund 
							SET status='COMPLETED', 
							modfied_by={$_SESSION['userid']}, 
							modified_dt='{$now}',
							billing_final_approval=1,
							voided =0 
						WHERE refund_id = {$_POST['refund_id']} ";
					
					}	
					elseif(!$billing_initial_approval){ //this means they are on the first round of billing approvals
						$query = "UPDATE refund 
							SET  
							status='BILLING APPROVED 1',
							modfied_by={$_SESSION['userid']}, 
							modified_dt='{$now}',
							billing_initial_approval=1,
							voided =0 
						WHERE refund_id = {$_POST['refund_id']} ";
					
						
					} 
									
			
				
			}else{//this means they are an admin with either Approver or SuperUser status, either way they have override approval abilities for purposes of this app
					$query = "UPDATE refund 
						SET status='APPROVED', 
						modfied_by={$_SESSION['userid']}, 
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
					//ECHO 'BOO';
					//DIE();
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

//showPage();
if(!isset($_REQUEST['action'])){//if action is set, it means you've clicked on a refund from one of the main screens and drilled down, so dont show main page again
	
	//echo 'comes right here';
	//die();
	
	//showPage($_SESSION['username'], $_SESSION['access']);	//valid user! Show page!
}
//include 'dump_all_page_contents.php'; 

//echo 'you are on line 1015 of refunds.php <br>';

}

elseif($_POST['username']) { //this means user_id isnt defined in the session variable yet = so they arent logged in
							   //if user has attempted to login (as per the post variable username being set), so attempt to validate login

	echo 'elseif post username <br>';

	if(validateLogin($_POST['username'],$_POST['password'])){
		showPage($_SESSION['username'], $_SESSION['access']);	//valid user! Show page!
	} else {
		showLogin('Login invalid. Please try again');	
	}

} elseif(!isset($_GET['report_id']) && !isset($_POST['report_id'])) { //Else show login screen (no user is not logged in and no login attempt has been made)
	echo '2nd to last elseif <br>';

	//echo 'Hello ET';
	showLogin();
}else{
	
	//issues this command when doing specific reports such as searching all refunds--returned from refunds.php?report_id= page
	//echo 'came down here <br>';
}
	


?>