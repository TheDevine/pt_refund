<?php

//start session
ini_set('session.gc_maxlifetime', 600);
session_start();

require('lib\refundFunctions.php');
include 'connectToDB.php'; 

//uncomment the next line to toggle session dumps on and off
//include 'dump_all_page_contents.php';    
//include 'lib\functions.php';
//include 'validateLogin.php';   

/*
if(isset($_POST)){
include 'dump_all_page_contents.php';    
}
*/

if (array_key_exists('userid', $_SESSION)){	//If user is logged in show page

	showSearchPage($_SESSION['username'],$_SESSION['access']);
	
	/*
	The POST Contents are:

	array (size=5)
	'refund_search_term' => string 'created_by' (length=10)
	'search_value' => string 'Default' (length=7)
	'_search_submit' => string '1' (length=1)
	'refund_id' => string '' (length=0)
	'Search' => string 'search' (length=6)
	*/
	
	
	if(isset($_POST['_search_submit']) && $_POST['_search_submit']!="" && $_POST['_search_submit']!=NULL){ 
	
		$userIDSearched="";
		
		if( isset($_POST['refund_search_term']) && strpos($_POST['refund_search_term'],'_by') ){
			
			echo '<center> Please further specify by selecting a name from the drop down below: </center>';
			echo '<br>';
			
			//$query = "SELECT user_id from users WHERE first_name LIKE '%{$firstName}%' AND last_name LIKE '%{$lastName}%'";
			//$result = mysqli_query($db,$query);	
			
			$query_users = 'SELECT user_id, first_name, last_name FROM users';
			$result_users = mysqli_query($db,$query_users);
	
			
				print <<<EDITUSERPAGE
<center><h2 align="center">Search Refunds</h2>
<a href="refunds.php">Back to Refunds</a>
<br/><br/>
	Please select the term you would like to search for from the Drop down menu, <br> then type value you are interested in matching pertaining to that term and click "Search Refunds" 
<br><br>	

		<form method="POST" action="{$_SERVER['PHP_SELF']}" name="search_refunds">
      <table style="width: 100%" border="1">
        <tbody>
          <tr> Select A Name: &nbsp;&nbsp;&nbsp;&nbsp;
              <select name="refund_search_term">
EDITUSERPAGE;




			while($row_users = mysqli_fetch_array($result_users)){
				print "<option value=\"{$row_users['user_id']}\"";
				print $selected; ">{$row_users['first_name']} {$row_users['last_name']}</option>";	

			}
	print <<<EDITUSERPAGE
              </select>
              <br>
            </td>
          </tr>
  
        </tbody>
      </table>
      <input type="hidden" name="_assign_submit" value="1" />
      <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
	  <br/>
      <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}" value="submit" name="Submit">Assign Refund</button>

	  </form>
EDITUSERPAGE;
				
				
		}

		else{
		if((isset($_POST['search_value']) && (strlen($_POST['search_value'])>=1) || is_numeric($_POST['search_value']) ) && ( isset($_POST['refund_search_term']) && strlen($_POST['refund_search_term'])>1 )){
			
				if($_POST['refund_search_term']=='created_by' || $_POST['refund_search_term']=='approved_by' || $_POST['refund_search_term']=='assigned_to' || $_POST['refund_search_term']=='modified_by'){		
				
					$theName; $firstName; $lastName;
					$theName=explode(' ',$_POST['search_value']);
					
					$firstName=$theName[0];
					$lastName=$theName[1];

					$query = "SELECT user_id from users WHERE first_name LIKE '%{$firstName}%' AND last_name LIKE '%{$lastName}%'";
					$result = mysqli_query($db,$query);
					
					while ($row = mysqli_fetch_array($result)){
						$userIDSearched=$row['user_id'];
					}
					
					$query = "SELECT * from refund WHERE {$_POST['refund_search_term']} LIKE '%{$userIDSearched}%'";
					$result = mysqli_query($db,$query);

				}elseif($_POST['refund_search_term']=='urgent' 
				|| $_POST['refund_search_term']=='voided' 
				|| $_POST['refund_search_term']=='accounting_approval' 
				|| $_POST['refund_search_term']=='billing_initial_approval'
				|| $_POST['refund_search_term']=='billing_final_approval'
				|| $_POST['refund_search_term']=='rejected'
				){
					
					if($_POST['search_value']=='yes' 
					|| $_POST['search_value']=='Yes'
					|| $_POST['search_value']=='y'
					|| $_POST['search_value']=='Y'
					|| $_POST['search_value']==1
					|| $_POST['search_value']=='Default'
					|| $_POST['search_value']==' '
					){
						$query = "SELECT * from refund WHERE {$_POST['refund_search_term']}=1";
					}else{
						$query = "SELECT * from refund WHERE {$_POST['refund_search_term']}=0";
					}
					$result = mysqli_query($db,$query);
				
				}else{
						
					if ($_POST['refund_search_term']=="amount"){
						$query = "SELECT * from refund WHERE {$_POST['refund_search_term']}='{$_POST['search_value']}' ";
						$result = mysqli_query($db,$query);						
						
					}else{	
						$query = "SELECT * from refund WHERE {$_POST['refund_search_term']} LIKE '%{$_POST['search_value']}%'";
						$result = mysqli_query($db,$query);
					}
				}

			
			/*
			echo 'the query is: ';
			echo '<br>';
			echo $query;
			//die();
			*/

				
			$current_date=date("Y-m-d H:i:s");  
			
			$row = mysqli_fetch_array($result);
			
			if (sizeof($row)){

				if($result){
					print '<center><b>Results of Searched Refunds:</b></center>';
					print '<br /><br /><div align = "center">';
					print '<table border="1" cellpadding = "3">
					<tr>
					<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
					<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
					<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
					<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
					<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
					<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
					<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
					<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
					;	
					
				}
				
			}else{
				echo '<font color=red><center>There were no results found for the search criteria specified.  <br> If you would like to search again please modify your search values, and resubmit. </center></font>';
			}


	$result = mysqli_query($db,$query);

	while ($row = mysqli_fetch_array($result)){

	
		$refund_requested_by="";
		$queryUserIDsRequested="SELECT first_name, last_name FROM users WHERE user_id= '{$row['created_by']}'";
		$resultUserIDsRequested = mysqli_query($db,$queryUserIDsRequested); 
		
		while ($rowUserIdsRequested=mysqli_fetch_array($resultUserIDsRequested)){//build up the assigned to username
			$refund_requested_by=$rowUserIdsRequested['first_name'].' '.$rowUserIdsRequested['last_name'];
		}
				
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
		<td>'.$row['dt_request'].'</td>
		<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
		<td>'.$refund_requested_by.'</td>
		<td>'.$row['payable'].'</td>';
		print '<td>$ '.$row['amount'].'</td>';
		
		if(!$row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>NEW</td>';
		}elseif(!$row['accounting_approval'] && $row['billing_initial_approval']){
			print '<td>BILLING INITIALLY APPROVED</td>';
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
					
		}

		
		
		}
		//end below
}

//showFooter();	

	
} elseif($_POST['username']) { //if user has attempted to login, validate login
   
   
	if(validateLogin($_POST['username'],$_POST['password'])){
		showSearchPage($_SESSION['username'], $_SESSION['access']);	
	} else {
		showLogin('Login invalid. Please try again');	
	}

} else { 		//Else show login screen
	showLogin();
}
//showFooter();	




/*
if(isset($_POST['_search_submit']) && $_POST['_search_submit']!="" && $_POST['_search_submit']!=NULL){ 

		//include 'dump_all_page_contents.php'; 
		//include 'connectToDB.php'; 
		
		if((isset($_POST['search_value']) && strlen($_POST['search_value'])>1) && ( isset($_POST['refund_search_term']) && strlen($_POST['refund_search_term'])>1 )){
			
			$query = "SELECT * from refund WHERE {$_POST['refund_search_term']} = {$_POST['search_value']}";
			$result = mysqli_query($db,$query);

			if($result){
				print '<br><br>';
				print '<center><b>Results of Searched Refunds:</b></center>';
				print '<br /><br /><div align = "center">';
				print '<table border="1" cellpadding = "3">
				<tr>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Date Requested</a></b></center></td>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_date=y>Urgent</a></b></center></td>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?requested_by=y>Requested By</a></b></center></td>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?payable_order=y>Payable To</a></b></center></td>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?amount_order=y>Amount</a></b></center></td>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Status</a></b></center></td>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?status_order=y>Assigned To</a></b></center></td>'
				;	
				
			
			}
			
			
	$current_date=date("Y-m-d H:i:s");  
	
	$result = mysqli_query($db,$query);


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
		<td>'.$row['dt_request'].'</td>
		<td>'. ($row['urgent'] ? 'Yes' : 'No') .'</td>
		<td>'.$row['first_name'].' '.$row['last_name'].'</td>
		<td>'.$row['payable'].'</td>';
		print '<td>$ '.$row['amount'].'</td>';
		
		if(!$row['accounting_approval'] && !$row['billing_initial_approval'] && !$row['billing_final_approval']){
			print '<td>NEW</td>';
		}elseif(!$row['accounting_approval'] && $row['billing_initial_approval']){
			print '<td>BILLING INITIALLY APPROVED</td>';
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
					
		}

}


*/



?>