<?
/**
 * SPEBS 
 *
 * This script is included by all pages to display SPEBS main header section, i.e. logo, title, various links etc.
 *
 *
 * @copyright (c) 2011 EETT
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE 
 *
 *
 * @author ICCS, NOC Team, National Technical University of Athens
 */

/*
* Originally written  by Aggeliki Dimitriou <A.Dimitriou@noc.ntua.gr> 
*                        Panagiotis Christias <P.Christias@noc.ntua.gr> 
*                        Athanasios Douitsis <A.Douitsis@noc.ntua.gr> 
*                        Chrysa Papagianni <C.Papagianni@noc.ntua.gr> 
*/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?= $lang_spebs_short?></title>
	<link rel="stylesheet" type="text/css" href="css/spebs.css"/>
	<link rel="stylesheet" type="text/css" href="css/help.css"/>
	
<?	
	if (isset($java_scripts))
		foreach($java_scripts as $i => $scr)
			echo '<script src="'.$scr.'" type="text/javascript"></script>';
?>

</head>
<body>
<?php include_once("analyticstracking.php") ?>
<div id="shadow">
<div id="header">
	<div id="toplinks">
		<div id="langlinks">
			<?= lang_link(1); ?>
			<span class="gray">|</span>
			<?= lang_link(0); ?>
		</div>
		<div id="loginhelplinks">
		<?
			$show_username = isset($_SESSION['username']);
			$show_loginout = isset($loginlogout);
			$show_register = !isset($_SESSION['username']) && !($action == "signup" || $action == "login");
			$show_myaccount = isset($_SESSION['username']) && $action != "myaccount";
			$links = "";
			$links .= ($show_username)? $_SESSION['username']." ":""; 
			 $links .= (!empty($links) && $show_loginout)? '<span class="gray">|</span> ':"";
			$links .=  ($show_loginout)?'<a href="?action='.$loginlogout.'"> '.${"lang_$loginlogout"}.' </a>':""; 
			 $links .= (!empty($links) && $show_register)? '<span class="gray">|</span> ':"";
			$links .= ($show_register)?  '<a href="?action=signup"> '.$lang_register.' </a>':"";
			 $links .= (!empty($links) && $show_myaccount)? '<span class="gray">|</span> ':"";
			$links .= ($show_myaccount)?'<a href="?action=myaccount"> '.$lang_settings.' </a>':"";
			 $links .= (!empty($links) && !$hide_help)? '<span class="gray">|</span> ':"";
			$links .= (!$hide_help)? '<a href="?action=help#'.$helpsection.'" target=\"_blank\">'.$lang_help.' </a>':"";
			echo $links;
		?>
		</div>
	
	</div>

	<div id="maintitle">
		<div id="logo">
			<a href="<?= $home?>"><img src="images/spebs_logo_105.png"></a>
		</div>
		<div class="title">
			<?= $lang_spebs?>
		</div>
	</div>
	
</div>
<div id="main">
	<div id="basiccontainer">

