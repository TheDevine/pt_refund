<?php
//start session
ini_set('session.gc_maxlifetime', 600);
session_start();

//require('refundFunctions.php');

include 'lib\functions.php';
include 'connectToDB.php'; 
include 'validateLoginAdmin.php';    

?>

