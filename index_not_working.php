<?php
session_start();
//start session

//uncomment the next line to toggle session dumps on and off
//include 'dump_all_page_contents.php';
 /* include 'lib\refundFunctions.php'; uncommenting this centers the foooter */
   
include 'lib\functions.php'; /* commenting out this centers the footer */
include 'connectToDB.php'; 
//echo '<center>'; include 'validateLogin.php';   echo '</center>';

if(isset($_GET)){
	
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
		
		//echo 'session order is: <br>';
		//echo $_SESSION['order'].'<br><br>';
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
		
		//echo 'session order is: <br>';
		//echo $_SESSION['order'].'<br><br>';
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
		
		//echo 'session order is: <br>';
		//echo $_SESSION['order'].'<br><br>';
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
		
		//echo 'session order is: <br>';
		//echo $_SESSION['order'].'<br><br>';
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
		
		///echo 'session order is: <br>';
		//echo $_SESSION['order'].'<br><br>';
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
		
		//echo 'session order is: <br>';
		//echo $_SESSION['order'].'<br><br>';
	}
	
	
}

echo 'ive been reloaded';
include 'dump_all_page_contents.php';

if(!isset($_GET['report_id']) && !isset($_POST['report_id'])){

	if (array_key_exists('userid', $_SESSION)){	//If user is logged, check for access level 

	if($_SESSION['access']=='S' OR $_SESSION['access']=='A' OR $_SESSION['access']=='U'){
		
		//check for $_GET['refund_id']. If set, show edit page for that user. Otherwise, show list of users
		//if($_GET['refund_id']){
		echo 'it loads before here <br>';
		if(sizeof($_POST)==0){
			echo 'am i within ';
			if (!isset($_GET['refund_id']) || ( ($_GET['refund_id']!="") && ($_GET['refund_id']!=NULL))){

				if(isset($_GET['action']) && $_GET['action']=='edit'){
					
					echo 'stopping here edit ';
					die();
					//ncurses_clear();
					showEditPage();
				} elseif(isset($_GET['action']) && $_GET['action']=='delete'){
										echo 'stopping here delete ';
					die();
					showDelPage(); //this function doesn't seem to exist yet.
				} 
				elseif(isset($_GET['action']) && $_GET['action']=='reject'){
										echo 'stopping here reject ';
					die();

					showRejPage(); //this function doesn't seem to exist yet.
				}
				elseif(isset($_GET['action']) && $_GET['action']=='approve'){
										echo 'stopping here approve ';
					die();

					showApprovePage(); //this function doesn't seem to exist yet
				}elseif(isset($_GET['action']) && $_GET['action']=='assign'){
										echo 'stopping here assign ';
					die();

					showAssignPage(); 
				}
			}
		}
			echo 'shouldnt go here';

		//showPage();
		
	}
	}else{
			showLogin();

		echo 'boo';
	}

	
}else{
	
	echo 'hi';
	
	die();
}
/**/
 
?>