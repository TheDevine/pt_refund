<?php

session_start();
unset($_SESSION['SAVE_POST']);
//echo 'hello';
//include 'dump_all_page_contents.php'; 
//die();

Header('Location: search_landing.php');


?>