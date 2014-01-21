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
	<link rel="stylesheet" type="text/css" href="css/tools.css"/>
	<link rel="stylesheet" type="text/css" href="css/help.css"/>
<?	
	if (isset($java_scripts))
		foreach($java_scripts as $i => $scr)
			echo '<script src="'.$scr.'" type="text/javascript"></script>';
?>

</head>
<body>
<?php include_once("analyticstracking.php") ?>
