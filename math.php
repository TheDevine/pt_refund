<?php

print <<<HEAD
<html><head><title>PHP Calculator!</title></head>
<body>
HEAD;

if($_POST['_submit_check']){
	if(validate_form()){
		process_form();
	} else {
		print "<h2>INVALID DATA DETECTED!</h2>";
		show_form();
	}
  	
} else {
	show_form();
}

print <<<FOOTER
</body></html>
FOOTER;

function validate_form(){
	if (is_numeric($_POST['nbr1']) and is_numeric($_POST['nbr2'])){
		return true;
	} else {
		return false;
	}

}

function show_form(){

print <<<MATH_FORM
<form method="POST" action={$_SERVER['PHP_SELF']}>
<table><tr><td><input name="nbr1" value="{$_POST['nbr1']}"/></td>
<td><select name="operator">
<option value="A" selected="selected">Add</option>
<option value ="S">Subtract</option>
<option value="M">Multiply</option>
<option value="D">Divide</option>
</select></td>
<td><input name="nbr2" value="{$_POST['nbr2']}"/></td></tr></table>
<button formmethod="post" formaction={$_SERVER['PHP_SELF']} name="Submit" /><input type="hidden" name ="_submit_check" value="1" /></form>
MATH_FORM;


}

function process_form(){
	//print results 
	$nbr1 = intval($_POST['nbr1']);
	$nbr2 = intval($_POST['nbr2']);
	$operator = $_POST['operator'];

	if ($operator == 'A'){
		$result = $nbr1 + $nbr2;
	} elseif ($operator == 'S'){
		$result = $nbr1 - $nbr2;
	} elseif ($operator == 'M'){
		$result = $nbr1 * $nbr2;
	} else {
		$result = $nbr1/$nbr2;
	}
	print 'Result: '.$result;

	//show form again for another round!
	show_form();

}

?>
