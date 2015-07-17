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

/*
The POST Contents are:

array (size=4)
  'refund_search_term' => string 'Names' (length=5)
  '_search_submit' => string '1' (length=1)
  'refund_id' => string '' (length=0)
  'Continue' => string 'continue' (length=8)

The REQUEST Contents are:

array (size=4)
  'refund_search_term' => string 'Names' (length=5)
  '_search_submit' => string '1' (length=1)
  'refund_id' => string '' (length=0)
  'Continue' => string 'continue' (length=8)

*/

/*

Listed Order is Matching On (approximate sart points):
refund_search_term 49 (now about 220)
refund_search_termName 64 (now 231)
refund_search_termValue 170 (now 344)
refund_search_termStatus 283 (now 450)
refund_search_termDate 415 (now 577)
*/

//include 'dump_all_page_contents.php'; 



if(!isset($_SESSION['initialOffset']) || $_SESSION['initialOffset']==""){
	$_SESSION['initialOffset']=0; //set the initial offset to the beginning of the result set if no start is specified
}

if(sizeof($_POST)!=0 && isset($_POST)){
	

	echo 'HERE <br>';
	//include 'dump_all_page_contents.php'; 
	//die();
/*
The REQUEST Contents are:

array (size=1)
  'page_number' => string '1' (length=1)

The GET Contents are:

array (size=1)
  'page_number' => string '1' (length=1)


  The GET Contents are:

array (size=1)
  'page_number' => string '1?page_number=1?page_number=1' (length=29)

  
  
*/
}

if(sizeof($_GET)!=0 && isset($_GET)){


	echo 'THERE <br>';
	//include 'dump_all_page_contents.php'; 

}


//////To Drill Down Into Encounters//////////////////////////////////////////////////////////////////////////////////////
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

if(sizeof($_SESSION['SAVE_POST'])>0 && sizeof($_POST)==0){
	//save post array trick, so later when clicking on re-ordering of the columns will not reload to default else and show first search selection screen again
	//$_SESSION['SAVE_POST']=$_POST;
	$_POST=$_SESSION['SAVE_POST'];
}
////////END TO Drill Down ////////////////////////////////////////////////////////////////////////////////////////////////////

//If the Action hasn't been set yet, then this means they are landing on the page coming from the front end and need to select from
//the drop down
if (!isset($_GET['action'])){//add this if statement so regular page isn't shown/duplicated underneath encounter details if selected
	echo 'hiiiii';
	
	
	///Could functionalize this later
	
	if(isset($_GET)){
		
		
		
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
	
	
	
	///

	
	
if( isset($_POST['refund_search_term']) && strlen($_POST['refund_search_term'])>1 && $_POST['refund_search_term']!=NULL){

	if(!isset($_SESSION['SAVE_POST']) && $_POST['Search']=='search'){//if they've searched at least once save the post session contents
			$_SESSION['SAVE_POST']=$_POST;

	}
//_search_by_names_submit

//include 'dump_all_page_contents.php';    
//die();
if($_POST['refund_search_term']=="Names"){
	searchByNames($_SESSION['username'],$_SESSION['access'],$_POST['startPos']);
}elseif($_POST['refund_search_term']=="Values"){
	searchByValues($_SESSION['username'],$_SESSION['access'],$_POST['startPos']);
}elseif($_POST['refund_search_term']=="Status"){
	searchByStatus($_SESSION['username'],$_SESSION['access'],$_POST['startPos']);
}else{
	searchByDates($_SESSION['username'],$_SESSION['access'],$_POST['startPos']);
}
}elseif(isset($_POST['refund_search_termName']) && strlen($_POST['refund_search_termName'])>1 && $_POST['refund_search_termName']!=NULL){

	//$query = "SELECT * from refund WHERE {$_POST['refund_search_term']}='{$_POST['search_value']}' ";
	//$result = mysqli_query($db,$query);			
	
	
	if (isset($_SESSION['order']) && strlen($_SESSION['order'])>0){
		
		$query = "SELECT * from refund 
		WHERE {$_POST['refund_search_termName']} 
		LIKE '%{$_POST['search_valueName']}%' 
		ORDER BY {$_SESSION['order']}
		LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

	}else{
		$query = "SELECT * from refund 
		WHERE {$_POST['refund_search_termName']} 
		LIKE '%{$_POST['search_valueName']}%'
		ORDER BY refund_id ASC
		LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
	}
	
		echo 'the query is: ';
		echo '<br>';
		echo $query;
	
	$result = mysqli_query($db,$query);

	$row = mysqli_fetch_array($result);
		
		if (sizeof($row)){

			if($result){
				print '<center><b>Results of Searched Refunds:</b></center>';
				print '<br /><br /><div align = "center">';
				print '<table border="1" cellpadding = "3">
				<tr>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Refund ID</a></b></center></td>
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
	
	/*
	
	if($interval->days>30 && $row['status']!="COMPLETED"){
		print '<tr bgcolor=#FF0000>';
	}elseif(($interval->days>=15 && $interval->days<30) && $row['status']!="COMPLETED"){
		print '<tr bgcolor=yellow>';
	}elseif(($interval->days<=1) && $row['status']!="COMPLETED"){
		print '<tr bgcolor=#009900>';
	}else{
		print '<tr>';
	}
	
	*/
	
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
		
		//if(!isset($_GET['status_order'])){
		print <<<EDITUSERPAGE
		<center><a href="unset_search.php"><button value="Back" name="Back">Back To Search Page</button></a></center>
EDITUSERPAGE;
		//}
		
		/*
		else{
		print <<<EDITUSERPAGE
		<center><a href="search_landing.php"><button value="Back" name="Back">Back To Search Page</button></a></center>
EDITUSERPAGE;
		}
		*/

	echo '<br>';
	echo '<br>';

//die();

}elseif(isset($_POST['refund_search_termValue']) && strlen($_POST['refund_search_termValue'])>1 && $_POST['refund_search_termValue']!=NULL){
//$_POST['refund_search_termValue'] represents their selection from the drop down menu
	
	//trick below
	if(!isset($_SESSION['SAVE_POST']) && $_POST['Search']=='search'){//if they've searched at least once save the post session contents
			$_SESSION['SAVE_POST']=$_POST;
	}
	

	/*
	var_dump($_SESSION);

	echo 'start range <br>';
	echo $_SESSION['RowsPerPage'];
	*/
	
	//actual assignment will be dynamically retrieved from the value in the table
	//$_SESSION['RowsPerPage']=15;
	//set initial offset and calculate current offset;
	//$_SESSION['initialOffset']=0;

	//echo $_SESSION['initialOffset'];
	
	if ($_POST['match_exactly']){
		//$query = "SELECT * from refund WHERE {$_POST['refund_search_termValue']} = '{$_POST['search_value']}'";
		
		if (isset($_SESSION['order']) && strlen($_SESSION['order'])>0){
			$query = "SELECT * from refund 
			WHERE {$_POST['refund_search_termValue']} = '{$_POST['search_value']}' 
			ORDER BY {$_SESSION['order']}
			LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];

		}else{

			$query = "SELECT * from refund 
			WHERE {$_POST['refund_search_termValue']} = '{$_POST['search_value']}' 
			ORDER BY refund_id ASC 
			LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}
			
	
		
	}else{
		//$query = "SELECT * from refund WHERE {$_POST['refund_search_termValue']} LIKE '%{$_POST['search_value']}%'";
		
		if (isset($_SESSION['order']) && strlen($_SESSION['order'])>0){
			$query = "SELECT * from refund 
			WHERE {$_POST['refund_search_termValue']} 
			LIKE '%{$_POST['search_value']}%' 
			ORDER BY {$_SESSION['order']} 
			LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
			
			$query = "SELECT * from (refund 
			WHERE {$_POST['refund_search_termValue']} 
			LIKE '%{$_POST['search_value']}%' 
			ORDER BY {$_SESSION['order']} 
			LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
			
			

			
		}else{
			$query = "SELECT * from (refund 
			WHERE {$_POST['refund_search_termValue']} 
			LIKE %{$_POST['search_value']}%
			ORDER BY refund_id ASC
			LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
		}		
		
		
	}
	$result = mysqli_query($db,$query);

	
	//echo 'the query search term value iSSS <br>';
	//echo $query;
	
	
	$row = mysqli_fetch_array($result);
	
				
		if (sizeof($row)){

			if($result){
				print '<center><b>Results of Searched Refunds:</b></center>';
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
				
			}
			
		}else{
			echo '<font color=red>
			<center>There were no results found for the search criteria specified.  
			<br> If you would like to search again please modify your search values and resubmit. 
			</center></font>';
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
		<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>
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

/*			
		print <<<EDITUSERPAGE
	<center><a href="{$_SERVER['HTTP_REFERER']}"><button value="Back" name="Back">Back To Search Page</button></a></center>
EDITUSERPAGE;
*/





	print <<<EDITUSERPAGE
	<center><a href="unset_search.php"><button value="Back" name="Back">Back To Search Page</button></a></center>
EDITUSERPAGE;
	echo '<br>';
	echo '<br>';

}elseif(isset($_POST['refund_search_termStatus']) && strlen($_POST['refund_search_termStatus'])>1 && $_POST['refund_search_termStatus']!=NULL){

	if(!isset($_SESSION['SAVE_POST']) && $_POST['Search']=='search'){//if they've searched at least once save the post session contents
			$_SESSION['SAVE_POST']=$_POST;

	}

	//include 'dump_all_page_contents.php';  

	/*
		urgent
		accounting approval
		billing initial approval
		billing final approval
		rejected
		voided	
	*/
	

	
	if ($_POST['refund_search_termStatus_Value']){//determine whether to search for inclusion or exclusion of search status type
		
		if (isset($_SESSION['order']) && strlen($_SESSION['order'])>0){

		$query = "SELECT * from refund WHERE {$_POST['refund_search_termStatus']}=1' ORDER BY {$_SESSION['order']}";
		ECHO '<center> <b>Refunds where the status is '.$_POST['refund_search_termStatus'].'</b></center><br>';
		}else{
		$query = "SELECT * from refund WHERE {$_POST['refund_search_termStatus']}=1'";
		ECHO '<center> <b>Refunds where the status is '.$_POST['refund_search_termStatus'].'</b></center><br>';
		}

	}else{
		if (isset($_SESSION['order']) && strlen($_SESSION['order'])>0){
			$query = "SELECT * from refund WHERE {$_POST['refund_search_termStatus']}=0' ORDER BY {$_SESSION['order']}";
			ECHO '<center> <b>Refunds where the status is NOT '.$_POST['refund_search_termStatus'].'</b></center><br>';
		}else{
			$query = "SELECT * from refund WHERE {$_POST['refund_search_termStatus']}=0";
			ECHO '<center> <b>Refunds where the status is NOT '.$_POST['refund_search_termStatus'].'</b></center><br>';
		}

	}
	$result = mysqli_query($db,$query);

	
	//echo 'the query search term status is <br>';
	//echo $query;
	
	$row = mysqli_fetch_array($result);
	

		if (sizeof($row)){

			if($result){
				//print '<center><b>Results of Searched Refunds:</b></center>';
				print '<br /><br /><div align = "center">';
				print '<table border="1" cellpadding = "3">
				<tr>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Refund ID</a></b></center></td>
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
	<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>
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

	print <<<EDITUSERPAGE
	<center><a href="unset_search.php"><button value="Back" name="Back">Back To Search Page</button></a></center>
EDITUSERPAGE;

	echo '<br>';
	echo '<br>';

}elseif(isset($_POST['refund_search_termDate']) && strlen($_POST['refund_search_termDate'])>1 && $_POST['refund_search_termDate']!=NULL){
	
	if(!isset($_SESSION['SAVE_POST']) && $_POST['Search']=='search'){//if they've searched at least once save the post session contents
			$_SESSION['SAVE_POST']=$_POST;

	}		
	
if(isset($_GET['report_id']) && sizeof($_GET['report_id'])>0){

	if($_GET['report_id']==1){
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
	
}


	if ($_GET['page_number']>=1){
	
		$_SESSION['initialOffset']+=($_SESSION['RowsPerPage']*$_GET['page_number']);
		
	}else{
		$_SESSION['initialOffset']=0;
	}
		
	//include 'dump_all_page_contents.php';  

	$pieces_from = explode("/", $_POST['datepickerSTART']);
	$converted_date_from=date("Y-m-d", mktime(0, 0, 0, $pieces_from[0], $pieces_from[1], $pieces_from[2]));
	//$entered_dt_from = new DateTime($converted_date_from);
	

	$pieces_to = explode("/", $_POST['datepickerEND']);
	$converted_date_to=date("Y-m-d", mktime(0, 0, 0, $pieces_to[0], $pieces_to[1], $pieces_to[2]));
	//$entered_dt_to = new DateTime($converted_date_to);
	
	
	if (isset($_SESSION['order']) && strlen($_SESSION['order'])>0){
	
		$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, created_by, status,refund_id, payable,assigned_to 
		FROM refund AS R 
		INNER JOIN 
		users AS U 
		ON R.created_by = U.user_id 
		WHERE Date(dt_request) >='".Date($converted_date_from)."' 
		AND Date(dt_request) <= '".Date($converted_date_to)."' 
		ORDER BY {$_SESSION['order']}
		LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
	
	}else{

		$query = "SELECT NG_enc_id, U.first_name, U.last_name, dt_request,amount, created_by, status,refund_id, payable,assigned_to 
		FROM refund AS R 
		INNER JOIN 
		users AS U 
		ON R.created_by = U.user_id 
		WHERE Date(dt_request) >='".Date($converted_date_from)."' 
		AND Date(dt_request) <= '".Date($converted_date_to)."' 
		ORDER BY refund_id, dt_request,U.last_name,status
		LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];		
			
	}
	

	$result = mysqli_query($db,$query);
	
	$row = mysqli_fetch_array($result);
	//var_dump($row);
	echo "LINE: 413ish";
	
	//echo 'first time we are dealing with pagination and offsets';
	
	//setting to control number of results per page (Default=15)/////////////////////////////////////////////////////////////////////
	//add this as a config page/section
	//stored in a table, retrieved and stored in the session variable during life of the application
	
	/*
	//actual assignment will be dynamically retrieved from the value in the table
	$_SESSION['RowsPerPage']=15;
	//set initial offset and calculate current offset;
	$_SESSION['initialOffset']=0;
	*/
	
	//var_dump($_POST);
	
	/*
	if ($_GET['page_number']>1){
	
		$_SESSION['initialOffset']+=($_SESSION['RowsPerPage']*$_GET['page_number']);
		
	}else{
		$_SESSION['initialOffset']=0;
	}
	*/
			echo 'the initial offest is now '.$_SESSION['initialOffset'];

	//END initial config of Pagination//////////////////////////////////////////////////////////////////////////////////////////////
	
	if (sizeof($row)){

			if($result){
				print '<center><b>Results of Searched Refunds:</b></center>';
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
				
			}
			
		}else{
			echo '<font color=red><center>There were no results found for the search criteria specified.  <br> If you would like to search again please modify your search values, and resubmit. </center></font>';
		}
	
		$result = mysqli_query($db,$query);

		$result_display_ctr=0;

		/*
		var_dump($result);

		echo 'the query is <br>';
		echo $query;
		*/
//while ($row = mysqli_fetch_array($result)){





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
	
	//$_SESSION['RowsPerPage']
	

	
	//var_dump($row[0]);
	
		echo 'bobididop';
	echo '<br>';
	
	echo 'value of session rows per page is ';
	echo '<br> '.$_SESSION['RowsPerPage'].'<br><br>';
	
	echo 'the value of result_display_ctr is '.$result_display_ctr;

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
	
	/*
	echo 'the size of the row ';
	echo sizeof($row);
	*/
	
		if (sizeof($row)>$_SESSION['RowsPerPage']){

				//echo 'where?';
			
			
			//$_GET['page_number'];
			//$tempOrigStartPosition=$_GET['page_number'];
			
			if(isset($_GET['page_number']) && $_GET['page_number']>0){

				$tempOrigStartPosition=$_GET['page_number'];
			}else{

				$tempOrigStartPosition=0;
			}
			/*
			echo 'the temp orig start pos is <br>';

			echo $tempOrigStartPosition;
			echo '<br>';*/
			
			if($tempOrigStartPosition>0){
				
				$page=--$tempOrigStartPosition;
				++$tempOrigStartPosition;

				$URL_String_BACK=$_SERVER['HTTP_REFERER'];
							
				$theOrigURL=substr($URL_String,0,strpos($URL_String_BACK,'?'));
				$URL_String_BACK=$theOrigURL;
				
				$URL_String_BACK .="?page_number=".$page;
	/*				
			print <<<EDITUSERPAGE
				<a href="{$URL_String_BACK}">Previous PAGE </a>
EDITUSERPAGE;
*/

			}
			echo '&nbsp;&nbsp;&nbsp;&nbsp;';	
			
				//$tempOrigStartPosition=$_POST['startPos'];

				$page=++$tempOrigStartPosition;		
				
				--$tempOrigStartPosition;
				$URL_String_FORWARD=$_SERVER['HTTP_REFERER'];


				$theOrigURL=substr($URL_String,0,strpos($URL_String_FORWARD,'?'));
				$URL_String_FORWARD=$theOrigURL;
				//$URL_String=$_SERVER['HTTP_REFERER'];
				$URL_String_FORWARD .="?page_number=".$page;
/*
			print <<<EDITUSERPAGE
				<a href="{$URL_String_FORWARD}">Next PAGE </a>
EDITUSERPAGE;
*/

						print <<<EDITUSERPAGE

	</tbody>
  </table></div>
EDITUSERPAGE;
		}
		


}			


		
			if($tempOrigStartPosition>0){
				
			print <<<EDITUSERPAGE
				<a href="{$URL_String_BACK}">PREVIOUS PAGE </a>
EDITUSERPAGE;

			}
			echo '&nbsp;&nbsp;&nbsp;&nbsp;';	
			

			print <<<EDITUSERPAGE
				<a href="{$URL_String_FORWARD}">NEXT PAGE </a>
EDITUSERPAGE;




		print <<<EDITUSERPAGE
	<center><a href="unset_search.php"><button value="Back" name="Back">Back To Search Page</button></a></center>
EDITUSERPAGE;

	echo '<br>';
	echo '<br>';


}
elseif (array_key_exists('userid', $_SESSION)){	//If user is logged in show page


if(isset($_POST['_search_submit']) && $_POST['_search_submit']!="" && $_POST['_search_submit']!=NULL){ 

	showSearchPage($_SESSION['username'],$_SESSION['access']);

	//echo 'am i down here';
	
	$userIDSearched="";
	
	if( isset($_POST['refund_search_term']) && strpos($_POST['refund_search_term'],'_by') ){
		
		echo '<center> Please further specify by selecting a name from the drop down below: </center>';
		echo '<br>';
		
		//$query = "SELECT user_id from users WHERE first_name LIKE '%{$firstName}%' AND last_name LIKE '%{$lastName}%'";
		//$result = mysqli_query($db,$query);	
		
		$query_users = 'SELECT user_id, first_name, last_name FROM users';
		$result_users = mysqli_query($db,$query_users);

			//print '&nbsp;&nbsp<a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=approve">Approve</a></td></tr>';
		
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
  <input type="hidden" name="startPos"  value=0 />

  <br/>
  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}".?startPos='0' value="submit" name="Submit">Assign Refund</button>

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
				
				$query = "SELECT * from refund 
				WHERE {$_POST['refund_search_term']} 
				LIKE '%{$userIDSearched}%' 
				ORDER BY refund_id
				LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
				
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
					$query = "SELECT * from refund WHERE {$_POST['refund_search_term']}=1 LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
				}else{
					$query = "SELECT * from refund WHERE {$_POST['refund_search_term']}=0 LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
				}
				$result = mysqli_query($db,$query);
			
			}else{
					
				if ($_POST['refund_search_term']=="amount"){
					
					$query = "SELECT * from refund 
					WHERE {$_POST['refund_search_term']}='{$_POST['search_value']}' 
					LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
					
					$result = mysqli_query($db,$query);		


				//$query = "SELECT * from refund WHERE {$_POST['refund_search_term']} LIKE '%{$userIDSearched}%' LIMIT ".$_SESSION['initialOffset'],$_SESSION['RowsPerPage'];
					
					
				}else{	
					$query = "SELECT * from refund 
					WHERE {$_POST['refund_search_term']} 
					LIKE '%{$_POST['search_value']}%'
					LIMIT ".$_SESSION['initialOffset'].",".$_SESSION['RowsPerPage'];
					
					$result = mysqli_query($db,$query);
				}
			}

		
		
		echo 'the query is: ';
		echo '<br>';
		echo $query;
		//die();
		

			
		$current_date=date("Y-m-d H:i:s");  
		
		$row = mysqli_fetch_array($result);
		
		if (sizeof($row)){

			if($result){
				print '<center><b>Results of Searched Refunds:</b></center>';
				print '<br /><br /><div align = "center">';
				print '<table border="1" cellpadding = "3">
				<tr>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Encounter Number</a></b></center></td>
				<td><center><b><a href='.$_SERVER['PHP_SELF'].'?encounter_num=y>Refund ID</a></b></center></td>
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
	<td><a href="'.$_SERVER['PHP_SELF'].'?refund_id='.$row['refund_id'].'&action=edit">'.$row['refund_id'].'</a></td>
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

			echo 'the query is: ';
		echo '<br>';
		echo $query;
	
	}else{//this else is for the initial search landing page

	//echo 'dont want to show this else';
	//die();
	
	showHeaderSearchLanding($username, $accessLvl);

//echo 'in the else <br>';

/*
Names
Values
Dates
*/

$typeOFRefundSearch=array('Dates','Names','Status','Values');


print <<<EDITUSERPAGE
<center><h2 align="center">Select Search type:</h2>

<center>Please selected the type of search you would like to perform <br> and click 'Continue' to proceed to the next Screen <br> to further
specify your search: </center>	

<br><br>	

	<form method="POST" action="{$_SERVER['PHP_SELF']}".?startPos='0' name="search_refunds">
  <table style="width: 100%" border="0">
	<tbody>
	  <tr> Search By: &nbsp;&nbsp;&nbsp;&nbsp;
		  <select name="refund_search_term">
EDITUSERPAGE;

foreach($typeOFRefundSearch as $key => $value){
	print "<option value=\"{$value}\">{$value}</option>";	
	//print $selected; ">{$value}</option>";	
}

print <<<EDITUSERPAGE
		  </select>
		</td>
	  </tr>


	</tbody>
  </table>
  <input type="hidden" name="_search_submit" value="1" />
  <input type="hidden" name="refund_id" value = "{$_GET['refund_id']}">
    <input type="hidden" name="startPos"  value=0 />

  <br/><br>
  <button formmethod="post" formaction="{$_SERVER['PHP_SELF']}"?startPos='0' value="continue" name="Continue">Continue</button>

  </form>
EDITUSERPAGE;
	 echo '<br> <a href="refunds.php">Back to Refunds</a> <br><br> ';

	}


showFooter();	


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

//echo "<br><center><a href="{$_SERVER['HTTP_REFERER']}"><button value="Back" name="Back">Back To Search Page</button></a></center>";

}


?>