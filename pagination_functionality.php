<?php


function calculateInterval($row,&$refund_requested_by,&$date_requested,&$today_dt,&$entered_dt,&$interval,&$refund_assigned_to){
	
	$refund_requested_by="";
	$queryUserIDsRequested="SELECT first_name, last_name FROM users WHERE user_id= '{$row['created_by']}'";
	$resultUserIDsRequested = mysqli_query($db,$queryUserIDsRequested); 
	
	
	while ($rowUserIdsRequested=mysqli_fetch_array($resultUserIDsRequested)){//build up the assigned to username
		$refund_requested_by=$rowUserIdsRequested['first_name'].' '.$rowUserIdsRequested['last_name'];
	}

	$current_date=date("Y-m-d H:i:s");  
		
	$date_requested=$row['dt_request'];

	$today_dt = new DateTime($current_date);
	$entered_dt = new DateTime($date_requested);
	$interval = date_diff($entered_dt,$today_dt);
	
	//var_dump($interval);

	//die();
	$refund_assigned_to="";
	$queryUserIDs="SELECT first_name, last_name FROM users WHERE user_id= '{$row['assigned_to']}'";
	$resultUserIDs = mysqli_query($db,$queryUserIDs); 
	

	while ($rowUserIds=mysqli_fetch_array($resultUserIDs)){//build up the assigned to username
		$refund_assigned_to=$rowUserIds['first_name'].' '.$rowUserIds['last_name'];
	}
	
	
	
	
}


function instantiate_initialOffset(){
	
		//INSTANTIATE INITIAL OFFSET////////////////////////////////////////////////////////////////////////////////////////

	if ($_GET['page_number']>=1){
	
		//$_SESSION['initialOffset']+=($_SESSION['RowsPerPage']*$_GET['page_number']);
		$_SESSION['initialOffset']=($_SESSION['RowsPerPage']*$_GET['page_number']);

		
	}else{
		$_SESSION['initialOffset']=0;
	}
	
	//INSTANTIATE INITIAL OFFSET////////////////////////////////////////////////////////////////////////////////////////
}



function instantiate_page_variables($row,&$tempOrigStartPosition,&$page,&$URL_String_BACK,&$URL_String_FORWARD){


		if (sizeof($row)>$_SESSION['RowsPerPage']){

			if(isset($_GET['page_number']) && $_GET['page_number']>0){

				$tempOrigStartPosition=$_GET['page_number'];
			}else{

				$tempOrigStartPosition=0;
			}

			if($tempOrigStartPosition>0){
				
				$page=--$tempOrigStartPosition;
				++$tempOrigStartPosition;

				$URL_String_BACK=$_SERVER['HTTP_REFERER'];
				$theOrigURL=substr($URL_String,0,strpos($URL_String_BACK,'?'));
				$URL_String_BACK=$theOrigURL;
				
				$URL_String_BACK .="?page_number=".$page;
		
			}
			echo '&nbsp;&nbsp;&nbsp;&nbsp;';	
			
				$page=++$tempOrigStartPosition;		//1
				
				--$tempOrigStartPosition; //0
				$URL_String_FORWARD=$_SERVER['HTTP_REFERER'];

				$theOrigURL=substr($URL_String,0,strpos($URL_String_FORWARD,'?'));
				$URL_String_FORWARD=$theOrigURL;
				$URL_String_FORWARD .="?page_number=".$page;
		

		}
		
		
		// DEBUG//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/*
		echo 'ROW CONTENTS: <BR>';
		var_dump($row);
		ECHO '<BR><BR>';
		
		echo 'START POS CONTENTS: <BR>';
		var_dump($tempOrigStartPosition);
		ECHO '<BR><BR>';

		echo 'page CONTENTS: <BR>';
		var_dump($page);
		ECHO '<BR><BR>';

		
		echo 'url back CONTENTS: <BR>';
		var_dump($URL_String_BACK);
		ECHO '<BR><BR>';

		echo 'url forward CONTENTS: <BR>';
		var_dump($URL_String_FORWARD);
		ECHO '<BR><BR>';
		*/
		//END DEBUG//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		
}


function displayPaginationINDEX($row,$tempOrigStartPosition,$URL_String_BACK,$URL_String_FORWARD){


	$newOffSet=0;
	
	if ($_GET['page_number']>=1){
	
		$newOffSet=($_SESSION['RowsPerPage']*$_GET['page_number']);
		
	}else{
		$newOffSet=0;
	}
	

//echo sizeof($URL_String_FORWARD);

if(!$tempOrigStartPosition>0 && !sizeof($URL_String_FORWARD)){
	
	echo 'You currently have no active refunds!';
}

			print <<<EDITUSERPAGE

	</tbody>
  </table>
EDITUSERPAGE;
		echo '<br><br><br>';
			if($tempOrigStartPosition>0){
				
			print <<<EDITUSERPAGE
				<center><b><i><a href="{$URL_String_BACK}"> << PREVIOUS PAGE </a>
EDITUSERPAGE;

			}
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	

			if(sizeof($row)>=$newOffSet){

			print <<<EDITUSERPAGE
				<b><i><a href="{$URL_String_FORWARD}">NEXT PAGE >> </a></center>
EDITUSERPAGE;
			}

		echo '<br><br><br><br>';
	/*	
			print <<<EDITUSERPAGE
	<center><a href="unset_search.php"><button value="Back" name="Back">Back To Search Page</button></a></center>
EDITUSERPAGE;
*/

	//showFooter();
		
}	


function displayPagination($row,$tempOrigStartPosition,$URL_String_BACK,$URL_String_FORWARD){


	$newOffSet=0;
	
	if ($_GET['page_number']>=1){
	
		$newOffSet=($_SESSION['RowsPerPage']*$_GET['page_number']);
		
	}else{
		$newOffSet=0;
	}
	

			print <<<EDITUSERPAGE

	</tbody>
  </table>
EDITUSERPAGE;
		echo '<br><br><br>';
			if($tempOrigStartPosition>0){
				
			print <<<EDITUSERPAGE
				<center><b><i><a href="{$URL_String_BACK}"> << PREVIOUS PAGE </a>
EDITUSERPAGE;

			}
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	

			if(sizeof($row)>=$newOffSet){

			print <<<EDITUSERPAGE
				<b><i><a href="{$URL_String_FORWARD}">NEXT PAGE >> </a></center>
EDITUSERPAGE;
			}

		echo '<br><br><br><br>';
		
			print <<<EDITUSERPAGE
	<center><a href="unset_search.php"><button value="Back" name="Back">Back To Search Page</button></a></center>
EDITUSERPAGE;

	showFooter();
		
}		

function displayPaginationReports($row,$tempOrigStartPosition,$URL_String_BACK,$URL_String_FORWARD){


			$newOffSet=0;
			
			include 'dump_all_page_contents.php';

			if ($_GET['page_number']>=1){

				$newOffSet=($_SESSION['RowsPerPage']*$_GET['page_number']);
				
			}else{
				$newOffSet=0;
			}


			print <<<EDITUSERPAGE

	</tbody>
  </table>
EDITUSERPAGE;
		echo '<br><br><br>';
			if($tempOrigStartPosition>0){
				
			print <<<EDITUSERPAGE
				<center><b><i><a href="{$URL_String_BACK}"> << PREVIOUS PAGE </a>
EDITUSERPAGE;

			}
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	

			if(sizeof($row)>=$newOffSet){

			print <<<EDITUSERPAGE
				<b><i><a href="{$URL_String_FORWARD}">NEXT PAGE >> </a></center>
EDITUSERPAGE;
			}

		echo '<br><br><br><br>';
		
			print <<<EDITUSERPAGE
	<center><a href="reports.php"><button value="Back" name="Back">Back To Reports Page</button></a></center>
EDITUSERPAGE;

	showFooter();
		
}		
		
?>