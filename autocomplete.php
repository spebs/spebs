<?php
/**
 * SPEBS 
 *
 * This script responds to AJAX requests from signup.php for autocompleting postal code-municipality pair.
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
require_once("init.inc.php");
require_once("visualizations.lib.php");


//parameter q is the variable passed by jQuery API containing the so far value of the field to autocomplete, municipality on postal code in our case
$keyprefix = NULL;
if(isset($_REQUEST['q']))
	$keyprefix = (!empty($_REQUEST['q'])) ? $_REQUEST['q']:"";

//parameter m is passed when postal code is to be autocompleted, i.e. having focus and accepting input	
$municipality = NULL;
if(isset($_REQUEST['m']))
	$municipality = (!empty($_REQUEST['m'])) ? get_municipality_code($_REQUEST['m']):0;
//There may exist several municipalities with the same name
//so build appropriate prepared statements attributes
$m_vals = "?";
$m_types = "i";
if(is_array($municipality))
{
	for($i=1;$i<count($municipality);$i++)
	{	
		$m_vals .= ",?";
		$m_types .= "i";
	}
}

//p is passed when municipality is to be autocompleted, i.e. having focus and accepting input	
$p = NULL;
if(isset($_REQUEST['p']))
	$p = (!empty($_REQUEST['p'])) ? $_REQUEST['p']:0;

//pc is passed when municipality has to take an acceptable value for that postal_code	
$pc = (isset($_REQUEST['pc'])) ? $_REQUEST['pc']:NULL;

//mn is passed when postal_code has to take an acceptable value for that municipality	
$mn = NULL;
if(isset($_REQUEST['mn']))
	$mn = (!empty($_REQUEST['mn'])) ? get_municipality_code($_REQUEST['mn']):0;
if(is_array($mn))
	$first_m_code = $mn[0];
else
	$first_m_code = $mn;

//$x['q'] = $keyprefix;
//$x['m'] = $municipality;
//$x['pc'] = $pc;
//$x['p'] = $p;

if (isset($keyprefix))
	$likekp = $keyprefix.'%';
//autocomlete of postal code taking into account municipality if already set
if (isset($keyprefix) && isset($municipality))
{
	if (strlen($keyprefix)<3)
		$dbattr = "prefix";
	else
		$dbattr = "postal_code";
	$dbattr = "postal_code";
	if (is_array($municipality) || (is_int($municipality) && $municipality>0))
	{	
		$postal_codes_q = "SELECT DISTINCT $dbattr code FROM tk WHERE municipality_id IN ($m_vals) AND $dbattr like ? ORDER BY $dbattr";
		if (is_array($municipality))
			$bound_vars = array_merge($municipality,(array)$likekp);
		else
			$bound_vars = array($municipality,$likekp);
		$bound_types = $m_types.'s';
	}
	else
	{	
		$postal_codes_q = "SELECT DISTINCT $dbattr code FROM tk WHERE $dbattr like ? ORDER BY $dbattr";
		$bound_vars = array($likekp);
		$bound_types = 's';
	}
	
$x[] = $postal_codes_q;

	if($res = execute_prepared_query($postal_codes_q,$bound_vars,$bound_types,true))
	{
		foreach($res AS $row)
			echo $row['code']."\n";
	}
	else die(db_error());
}
//autocomlete of municipality taking into account postal_code if already set
elseif (isset($keyprefix) && isset($p))
{
	if($p>0)
	{
		$municipalities = "SELECT DISTINCT m.name_".$lang_lang_short." mname FROM tk t JOIN municipalities m on t.municipality_id=m.id WHERE t.postal_code = ? AND (m.name_el like ? OR m.name_en like ?) ORDER BY m.name_".$lang_lang_short."";
		$res = execute_prepared_query($municipalities,array($p,$likekp,$likekp),'iss',true);
	}
	else
	{
		$municipalities = "SELECT DISTINCT name_".$lang_lang_short." mname FROM municipalities WHERE name_el like ? OR name_en like ? ORDER BY name_".$lang_lang_short."";
		$res = execute_prepared_query($municipalities,array($likekp,$likekp),'ss',true);
	}
	
	if($res)
	{
		foreach ($res AS $row)
			echo $row['mname']."\n";
	}
	else die(db_error());
	//$x[] = $municipalities;
}
//fill in municipality based on given postal code 
elseif (isset($pc))
{
	$change_municipality = true;
	if (isset($municipality) && !empty($municipality))
	{	
		if(db_cross_valid_multiple($municipality,$pc,"municipality_id","postal_code",array("i","i"),"tk"))
		{
			$change_municipality = false;
		}
	}
	
	if($change_municipality)
	{
		$municipality_q = "SELECT DISTINCT m.name_".$lang_lang_short." FROM tk JOIN municipalities m ON municipality_id=m.id WHERE postal_code=?";
		$m = get_prepared_single_value($municipality_q,array($pc),'i');
		echo $m;
		if(empty($m))
			echo "unknown";
		//$x[] = $municipality_q;
	}
}
elseif (isset($mn) && $mn>0)
{
	$change_pc = true;
	if (isset($p) && !empty($p))
	{	
		if(db_cross_valid_multiple($mn,$p,"municipality_id","postal_code",array("i","i"),"tk"))
		{
			$change_pc = false;
		}
	}
	
	if($change_pc)
	{
		$pc_q = "SELECT postal_code FROM tk WHERE municipality_id=?";
		$pcp = get_prepared_single_value($pc_q,array($first_m_code),'i');
		echo $pcp;
		if(empty($pcp))
			echo "unknown";
	//	$x[] = $pc_q;
	}
}
else
{
	echo ""; 
	//$x[] = "default case";
}
	
?>
