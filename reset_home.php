<?php

session_start();
$_SERVER['HTTP_REFERER']="";
unset($_SERVER['HTTP_REFERER']);
$_SERVER['HTTP_REFERER']="/pt_refund/index.php";


Header('Location: index.php');


?>