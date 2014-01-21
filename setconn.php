<?php
/**
 * SPEBS 
 *
 * This script responds to AJAX requests originating from map operations (pan, zoom or drag)
 *   
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

if(isset($_SESSION['user_id']) && isset($_POST['c']) && is_numeric($_POST['c']))
{
		$c = $_POST['c'];
		$userconnections  = get_alluser_connections($_SESSION['user_id']);
		if(isset($userconnections[$c]))
		{	
			$_SESSION['connection_id'] = $userconnections[$c]['connection_id'];
		}
}

