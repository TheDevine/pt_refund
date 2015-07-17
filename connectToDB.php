<?php
 
ini_set('session.gc_maxlifetime', 600);
if (!isset($_SESSION))
  {
    session_start();
  }

//require('refundFunctions.php');
//include 'dump_all_page_contents.php';
 
 $db = mysqli_connect('localhost','root','','pt_refund'); //connect to database
   if(!$db){die("Can't connect: ". mysqli_connect_error());}

 ?>