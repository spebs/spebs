<?php
include("../init.inc.php");
if(!isset($_SESSION['profile']) || $_SESSION['profile']<3)
	die("Go away!");
if(isset($_REQUEST['e']))
	delete_user($_REQUEST['e']);
else echo "Which user? (e=.....)";
?>