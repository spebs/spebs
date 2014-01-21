<?
/**
 * SPEBS 
 *
 * This script consists the main library of functions found in all php scripts of this project. 
 *
 *
 * @copyright (c) 2011 ΕΕΤΤ
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE 
 *
 *
 * @author ICCS, NOC Team, National Technical University of Athens

 * Originally written  by Aggeliki Dimitriou <A.Dimitriou@noc.ntua.gr> 
 *                        Panagiotis Christias <P.Christias@noc.ntua.gr> 
 *                        Athanasios Douitsis <A.Douitsis@noc.ntua.gr> 
 *                        Chrysa Papagianni <C.Papagianni@noc.ntua.gr> 
**/




/**************************************    DB   Main API  **********************************************/

/**
	connectDB is called in init.php and assigns to global $spebs_db the new connection on success
**/

function connectDB() 
{
	global $dbu, $dbpsw, $database;
	$db = new mysqli('localhost', $dbu, $dbpsw, $database);
	if ($db -> connect_error) 
		die('Connect Error (' . $spebs_db->connect_errno . ') '. $spebs_db->connect_error);
	else
	{	
		$r = $db -> query("SET NAMES utf8");
		return $db;
	}
}
function disconnectDB() 
{
	global $spebs_db;
	$spebs_db -> close();
}
/**
*
*	Simple query executions using mysqli but not prepared statements
*
**/

function execute_query($q)
{
	global $spebs_db;
	$res = $spebs_db -> query($q);
	if($spebs_db->errno != 0)
	{
		error_log("QUERY EXECUTE error ".$spebs_db->errno.": ".$spebs_db->error." (Q = \"$q\")");
		return false;
	}
	return $res;
}

function get_results($q, $type = MYSQLI_ASSOC)
{
	$res = execute_query($q);
	if ($res && $res -> num_rows > 0)
		return $res -> fetch_all($type);
	else 
		return null;
}



function db_error()
{
	global $spebs_db;
	return $spebs_db -> error;
}

function get_single_value($q) 
{
	if($res = execute_query($q))
		return ($row = $res -> fetch_row())? $row[0]:NULL;
	else die(db_error());
}
/**
*
*	Safe query executions using mysqli prepared statements
*
**/

function execute_prepared_query($prep_q, $bound_vars, $bound_var_types, $result_expected = false)
{
	global $spebs_db;
	$pstmt = $spebs_db -> prepare($prep_q);	
	if($spebs_db->errno != 0)
	{
		error_log("PREPARE STATEMENT error ".$spebs_db->errno.": ".$spebs_db->error." (Q = \"$prep_q\")");
		return false;
	}
	
	$bound_varrefs = array();
	$bound_varrefs[0] = $bound_var_types;
	$i=1;
	foreach($bound_vars AS $thisvar)
	{	
		${"x$i"} = $thisvar;
		$bound_varrefs[$i] = &${"x$i"};
		$i++;
	}
	$bind_action = call_user_func_array(array($pstmt, 'bind_param'), $bound_varrefs);
	if(!$bind_action)
	{	
		error_log("CALL_USER_FUNC_ARRAY error ");
		return false;
	}
	if($pstmt->errno != 0)
	{
		error_log("PREPARED QUERY BIND error ".$pstmt->errno.": ".$pstmt->error);
		return false;
	}	
	if($pstmt -> execute())
	{
		if(!$result_expected)
		{
			if(isset($pstmt -> insert_id) && is_int($pstmt -> insert_id) && $pstmt -> insert_id > 0)
				$res = $pstmt -> insert_id;
			else
				$res = true;
				
			$pstmt -> close();
			return $res;
		}
		else
		{
			$results = array();
			$row = array();
			$metadata = $pstmt -> result_metadata();
			foreach($metadata -> fetch_fields() AS $field)
			{	
				$row[$field -> name] = NULL;
				$bound_resrefs[] = &$row[$field -> name];
			}
			$bind_action = call_user_func_array(array($pstmt, 'bind_result'), $bound_resrefs);
			$i = 0;
			while($pstmt -> fetch())
			{	
				foreach($row AS $k => $v)
				$results[$i][$k] = $v;
				$i++;
			}
			
			$pstmt -> close();
			return $results;
		}
		
	}
	else
	{
		error_log($pstmt->errno.": ".$pstmt->error);
		$pstmt -> close();	
		return false;
	}
}

function get_prepared_single_value($prep_q, $bound_vars, $bound_var_types) 
{
	$res = execute_prepared_query($prep_q, $bound_vars, $bound_var_types, true);
	if(empty($res))
		return "";
	elseif($res)
	{	
		$keys = array_keys($res[0]);
		return $res[0][$keys[0]];
	}
	else die(db_error());
}

function get_prepared_single_attribute($prep_q, $bound_vars, $bound_var_types) 
{
	$res = execute_prepared_query($prep_q, $bound_vars, $bound_var_types, true);
	if(empty($res))
		return array();
	elseif(is_array($res))
	{	
		$keys = array_keys($res[0]);
		$result = array();
		for($i=0;$i<count($res);$i++)
			array_push($result,$res[$i][$keys[0]]);
		return $result;
	}
	else die(db_error());
}

function get_prepared_insert_id($prep_q, $bound_vars, $bound_var_types) 
{
	$res = execute_prepared_query($prep_q, $bound_vars, $bound_var_types, false);
	if(is_int($res))
		return $res;
	else 
		die(db_error());
}

/********************************    Login and session management   **********************************/

function login($username, $password, $remember=false) 
{
	$udata = array();
	$query  = "SELECT user_id FROM user WHERE email= ? AND password=SHA1(?) AND active=1";
	$uid = get_prepared_single_value($query,array($username,$password),'ss');
	if (is_int($uid) && $uid > 0)
	{	
		$query  = "UPDATE user SET last_login = ? WHERE user_id = ?";
		$result = execute_prepared_query($query,array(date('Y-m-d H:i:s'),$uid),'ss');
		add_log('LOGIN',$uid);
		
		$udata['username'] = $username;
		$udata['user_id'] = get_user_id($username);
		$udata['connection_id'] = get_connection_id($uid);
		$udata['profile'] = get_user_profile($uid);	
	
		return $udata;
	}
	else
		return false;
}

function update_user_session($set, $oldsession = null)
{
	global $_SESSION;
	$newsess = ($set)? session_id():"NULL";
	if(!$set && !is_null($oldsession))
	{
		$query  = "DELETE FROM rememberme_session WHERE user_id = ? AND last_session = ?";
		return  execute_prepared_query($query, array($_SESSION['user_id'],$oldsession), 'is');
	}
	else if($set && is_null($oldsession))
	{	
		$query  = "INSERT INTO rememberme_session(user_id,email,last_login,last_session) VALUES(?,?,NOW(),?)";
		return  execute_prepared_query($query, array($_SESSION['user_id'],$_SESSION['username'],$newsess), 'iss');
	}
	else if($set && !is_null($oldsession))
	{	
		$query  = "UPDATE rememberme_session SET last_session = ? WHERE user_id = ? AND last_session = ?";
		return  execute_prepared_query($query, array($newsess, $_SESSION['user_id'],$oldsession), 'sis');
	}
	
}

function check_remembered_user($key) 
{
	global $rememberme_duration;
	$udata = array();
	$keydata = extract_user_data_from_key($key);
	if(is_array($keydata) && count($keydata) == 4 && $keydata[0] > 0)
	{
		$query = "SELECT user_id FROM rememberme_session WHERE user_id=? AND email=? AND last_login=? AND last_session=?";
		$uid = get_prepared_single_value($query,$keydata,'isss');
		if($uid > 0)
		{
			$session_cookie_not_expired = get_prepared_single_value("SELECT DATE_ADD(last_login, INTERVAL $rememberme_duration DAY)>NOW() FROM rememberme_session WHERE user_id=$uid AND last_session = ?",array($keydata[3]),'s');
			if($session_cookie_not_expired == 1)
			{

				$query  = "UPDATE rememberme_session SET last_login = NOW() WHERE user_id = ? AND last_session = ?";
				$result = execute_prepared_query($query,array($uid,$keydata[3]),'is');
				add_log('USER RETURNED',$uid);


				$udata['username'] = $keydata[1];
				$udata['lastsession'] = $keydata[3];
				$udata['user_id'] = $uid;
				$udata['connection_id'] = get_connection_id($uid);
				$udata['profile'] = get_user_profile($uid);	
				return $udata;
			}
			else
			{	
				$expired_session_cookie_deletion = "DELETE FROM rememberme_session WHERE user_id = ? AND last_session = ?";
				execute_prepared_query($expired_session_cookie_deletion,array($uid,$keydata[3]),'is');
			}
			
		}
	}
	
	return false;
}

function is_loggedin()
{
	return isset($_SESSION['username']);
}
/**************************    Basic db actions: retrieve user and connection data      **********************/


function get_user_id($username)
{
	$query  = "SELECT user_id FROM user WHERE email=?";
	return get_prepared_single_value($query,array($username),'s'); 
}

function get_user_details($user_id)
{
	$query = "SELECT firstname, lastname, email, contact contactby FROM user where user_id=?";
	$res = execute_prepared_query($query,array($user_id),'i',true);
	if(!empty($res))
		return $res[0];
	else 
		return false;
}

function get_user_session_key()
{
	global $_SESSION;
	$uid = $_SESSION['user_id'].""; 
	$query = "SELECT CONCAT(user_id,'|',email,'|',last_login) FROM user where user_id=?";
	$s = get_prepared_single_value($query,array($uid),'i');
	$session = session_id();
	$senc = produce_encoded_str($s.'|sessionID='.$session);
	return $senc;
}

function extract_user_data_from_key($key)
{
	$str = produce_decoded_str($key);
	$data = explode('|',$str);
	if(count($data) == 4)
		$data[3] = str_replace('sessionID=','',$data[3]);
	return $data;
}


function get_user_connection_details($user_id, $conn_id = NULL)
{
	if(is_null($conn_id))
	{
		$query = "SELECT street, str_number, postal_code, municipality, m.name_el, isp_id, purchased_bandwidth_dl_kbps, purchased_bandwidth_ul_kbps, c.latitude, c.longitude, c.description, c.status 
			FROM user_connection uc NATURAL JOIN connection c JOIN municipalities m ON municipality = id WHERE user_id=? AND status=1";
		$res = execute_prepared_query($query,array($user_id),'i',true);
	}
	else
	{
		$query = "SELECT street, str_number, postal_code, municipality, m.name_el, isp_id, purchased_bandwidth_dl_kbps, purchased_bandwidth_ul_kbps, c.latitude, c.longitude, c.description, c.status 
			FROM user_connection uc NATURAL JOIN connection c JOIN municipalities m ON municipality = id WHERE user_id=? AND uc.connection_id=?";
		$res = execute_prepared_query($query,array($user_id, $conn_id),'ii',true);
	}
	if(!empty($res))
		return $res[0];
	else 
		return false;
}

function get_alluser_connections_details($user_id)
{
	$query = "SELECT c.connection_id, street, str_number, postal_code, municipality, m.name_el, isp_id, purchased_bandwidth_dl_kbps, purchased_bandwidth_ul_kbps, c.latitude, c.longitude, c.description, c.status 
		FROM user_connection uc NATURAL JOIN connection c JOIN municipalities m ON municipality = id WHERE user_id=? AND status>0 ORDER BY status=1 DESC, creation_time";
	$res = execute_prepared_query($query,array($user_id),'i',true);
	if(!empty($res))
		return $res;
	else 
		return false;
}

function get_alluser_connections($user_id)
{
	$query = "SELECT c.connection_id, c.description
		FROM user_connection uc NATURAL JOIN connection c JOIN municipalities m ON municipality = id WHERE user_id=? AND status>0 ORDER BY status=1 DESC, creation_time";
	$res = execute_prepared_query($query,array($user_id),'i',true);
	if(!empty($res))
		return $res;
	else 
		return false;
}

function get_connection_id($userid)
{
	$query  = "SELECT connection_id FROM user_connection NATURAL JOIN connection WHERE user_id=? AND status=1";
	return get_prepared_single_value($query, array($userid),'i'); 
}

function get_user_profile($userid)
{
	$query  = "SELECT profile FROM user WHERE user_id=?";
	return get_prepared_single_value($query, array($userid),'i'); 
}

function get_user_contact($userid)
{
	$query  = "SELECT contact FROM user WHERE user_id=?";
	return get_prepared_single_value($query, array($userid),'i'); 
}

function get_user_isp($connection_id)
{
	$query  = "SELECT isp_id FROM connection WHERE connection_id='$connection_id'";
	return get_single_value($query);
}

function get_cursession_data()
{
	//global $_SESSION;
	$data = array('username'=> '', 'user_id' => '', 'connection_id' => '', 'user_level' => '', 'down_mbps' => -1, 'up_mbps' => -1);
	if(isset($_SESSION['username']))
		$data['username'] = $_SESSION['username'];
	if(isset($_SESSION['user_id']))
		$data['user_id'] = $_SESSION['user_id'];
	if(isset($_SESSION['connection_id']))
	{	
		$data['connection_id'] = $_SESSION['connection_id'];
		$query  = "SELECT purchased_bandwidth_dl_kbps/1000 down_mbps, purchased_bandwidth_ul_kbps/1000 up_mbps FROM connection WHERE connection_id=?";
		$res = execute_prepared_query($query,array($_SESSION['connection_id']),'i',true);
		foreach($res[0] AS $k => $v )
			$data[$k] = $v;
	}
	if(isset($_SESSION['user_level']))
		$data['user_level'] = $_SESSION['profile'];
	return $data;
}

function set_cursession_data($key, $value)
{
	$_SESSION[$key] = $value;
}

function getUserLocation($user_id)
{
	if(isset($_SESSION['connection_id']))
	{
		$query  = "SELECT connection.longitude, connection.latitude, address, postal_code, municipality, prefecture, periphery,peripheries.country 
			FROM user_connection uc
			NATURAL JOIN connection 
			JOIN municipalities ON municipality=municipalities.id
			JOIN prefectures ON prefecture=prefectures.id
			JOIN peripheries ON periphery=peripheries.id
			WHERE uc.connection_id=?";
			$parameter = array($_SESSION['connection_id']);
	}
	else
	{
		$query  = "SELECT connection.longitude, connection.latitude, address, postal_code, municipality, prefecture, periphery,peripheries.country 
			FROM user_connection 
			NATURAL JOIN connection 
			JOIN municipalities ON municipality=municipalities.id
			JOIN prefectures ON prefecture=prefectures.id
			JOIN peripheries ON periphery=peripheries.id
			WHERE user_id=? AND status=1";
			$parameter = array($user_id);
	}
	if($res = execute_prepared_query($query, $parameter, 'i',true))
	  return $res[0];
	else die(db_error());
}

function getUserISP($user_id)
{
   return get_single_value("SELECT isp_id FROM user_connection NATURAL JOIN connection NATURAL JOIN isp WHERE user_id=$user_id AND status=1");
}

function getUserConnections($user_id) 
{
	$query = "SELECT c.* FROM user_connection uc NATURAL JOIN connection c WHERE user_id=$user_id";
    $res = execute_query($query);    
	while($row = $res -> fetch_assoc())
		$r[] = $row;
	return $r;
} 


function get_municipality_code($municipality_name, $postal_code = NULL)
{
	$code = NULL;
	if(is_null($postal_code))
	{
		$pquery  = "SELECT id FROM municipalities WHERE name_el = ? OR name_en = ? OR name_el_no_accents = ? OR name_el_gen_caps = ?";
		$res = get_prepared_single_attribute($pquery,array($municipality_name,$municipality_name,$municipality_name,$municipality_name),'ssss');
	}
	else
	{
		$pquery  = "SELECT id FROM tk t JOIN municipalities m on t.municipality_id=m.id WHERE t.postal_code = ? AND (m.name_el = ? OR m.name_en = ? OR m.name_el_no_accents = ? OR m.name_el_gen_caps = ?)";
		$res = get_prepared_single_attribute($pquery,array($postal_code,$municipality_name,$municipality_name,$municipality_name,$municipality_name),'issss');
	}
	if(is_array($res))
	{
		if(count($res) == 1)
			$code = $res[0];
		else
			$code = $res;
	}
	return (isset($code))? $code:-1;
}

function getMeasurements($userid) {

        $measurements_array = array();
		$query="SELECT created,reporting_host,connection_id,measurement_tool,version,upstream_bw,downstream_bw,rtt FROM generic_measurement WHERE user_id=\"".$userid."\"";
        $result = execute_query($query);
		while($row = $result -> fetch_assoc())
		{
			$created = $row['created'];
			$reporting_host = preg_replace('/:[0-9]+$/', '', $row['reporting_host']);
			$connection_id = $row['connection_id'];
			$measurement_tool = $row['measurement_tool'];
			$version = $row['version'];
			$upstream_bw = sprintf("%.3f", $row['upstream_bw']);
			$downstream_bw = sprintf("%.3f", $row['downstream_bw']);
			$rtt = $row['rtt'];
			$new_measurements_array = array($created,$reporting_host,$connection_id,$measurement_tool,$version,$upstream_bw,$downstream_bw,$rtt);
			array_push($measurements_array, $new_measurements_array);
        }
        return $measurements_array;

}

function user_measurements($user_id)
{
	$q = "SELECT count(*) c
				FROM generic_measurements_stats 
				WHERE connection_id = (SELECT connection_id FROM user_connection NATURAL JOIN connection WHERE user_id=? and status=1)";
	return get_prepared_single_value($q,array($user_id),"i");
}

function count_users_with_measurements($tool = 'ndt') 
{
    $query['ndt'] = "SELECT COUNT(DISTINCT user_id) FROM generic_measurement";
	$query['glasnost'] = "SELECT COUNT(DISTINCT user_id) FROM glasnost_measurement";
	$query['total'] = "SELECT COUNT(user_id) FROM ((SELECT distinct(user_id) FROM generic_measurement) UNION (SELECT DISTINCT(user_id) FROM glasnost_measurement) )x";
	
	$q = $query['ndt'];
	if($tool == 'glasnost' || $tool == 'total')
	$q = $query[$tool];
		
	return get_single_value($q);
}

function count_registered_users() 
{
    $q = "SELECT COUNT(DISTINCT user_id) FROM user_connection uc  JOIN connection c ON uc.connection_id=c.connection_id WHERE c.status=1";
	return get_single_value($q);
}
function connection_measurements($connection_id)
{
	$q = "SELECT count(*) c
				FROM generic_measurements_stats 
				WHERE connection_id = ?";
	return get_prepared_single_value($q,array($connection_id),"i");
}

function connection_glasnost_measurements($connection_id)
{
	$q = "SELECT count(*) c
				FROM glasnost_measurement
				WHERE connection_id = ?";
	return get_prepared_single_value($q,array($connection_id),"i");
}

function get_field_values_for_user($userid, $connid = NULL)
{
	//global $firstname,$lastname, $email, $street, $street_num, $postal_code, $municipality, $bandwidth, $isp;
	$fv = array();
	list($fv['firstname'],$fv['lastname'], $fv['email'], $fv['contact']) = array_values(get_user_details($userid));
	$res = get_alluser_connections_details($userid);
	$fv['connections'] = array();
	for($i=0;$i<count($res);$i++)
	{
		list ($c['connection_id'], $c['street'], $c['street_num'], $c['postal_code'], $c['municipality_code'], $c['municipality'],$c['isp'], $bdown, $bup, $c['addrlat'], $c['addrlng'], $c['description'], $c['status']) = array_values($res[$i]);
		$c['bandwidth'] = which_bandwidth_combination($bup, $bdown);
		array_push($fv['connections'], $c);
	}
	return $fv;
}

function getISPlist()
 {
	$query = "SELECT * FROM isp order by name";
	$res = execute_query($query);    
	//return $res -> fetch_all(MYSQLI_ASSOC) 
	while($row = $res -> fetch_assoc())
		$r[] = $row;
	return $r;
 }

 function get_isp_from_ipv4($ip) 
{
    $query = "SELECT isp_id FROM ipv4_to_isp WHERE inet_aton(?) between ip_start and ip_stop";
	return get_prepared_single_value($query,array($ip),'s');
}


/************************************         Utilities     *****************************************/


function which_bandwidth_combination($ul, $dl)
{
	global $bandwidths;
	foreach ($bandwidths AS $key => $bs)
		if ($bs['u'] == $ul && $bs['d'] == $dl)
			return $key;
	return -1;
}

function convert_username_to_encrypted_id($username) 
{
	$cur_userid = get_user_id($username);
	$fmt = sprintf("%16d",$cur_userid);
	return produce_encoded_str($fmt);
}

function produce_encoded_str($user) {

	global $enc_key,$enc_phrase;

	$string = sprintf($enc_phrase,$user);

	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

	$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $enc_key, $string, MCRYPT_MODE_CBC, $iv);

	#####print strlen($crypttext.$iv)."\n";
	return base64_encode($crypttext.$iv);
}

function produce_decoded_str($crypttext) {

	global $enc_key,$enc_phrase;
        
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

	$text = base64_decode($crypttext);
	$encstr = substr($text,0,strlen($text)-$iv_size);
	$iv = substr($text,0-$iv_size);
	$str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $enc_key, $encstr, MCRYPT_MODE_CBC, $iv);

	$enc_phrase_parts = explode('%s',$enc_phrase);
	//$enc_phrase_parts[] = '\0';
	$str = str_replace($enc_phrase_parts,"",$str);
	$str = preg_replace('/\x0/','',$str);
	return $str;
}


/****
*
* Execute a query and transform measurement results into javascript array compatible with google visualizations API
*
*****/
function transform2google_data($dbquery, $coltypes, $varnames, $activecols = NULL)
{
	$res = execute_query($dbquery);
	$firstrow = true;
	$rowindex=0;
	$gdata = "";
	if (!is_array($varnames))
		$varnames = array($varnames);
	
	if ($activecols == NULL)
		for ($j=0;$j<count($coltypes);$j++)
			$activecols[0][$j] = $j;
	
	foreach($varnames as $varid => $varname)
	{
		$gdata .= "$varname = new google.visualization.DataTable();";
	}
			
	while ($row = $res -> fetch_assoc())
	{
		$rowvals = array_values($row);
		
		if ($firstrow) //Store column titles 
		{
			$rowkeys = array_keys($row);
			foreach($varnames as $varid => $varname)
			{
				//$gdata .= "var $varname = new google.visualization.DataTable();";
				foreach($activecols[$varid] as $activecol)
				{
					$k = $rowkeys[$activecol];  //db answer key that this js datatable (varname) needs to contain
					$x = "lang_$k";
					global $$x;
					$gdata .= "$varname.addColumn('{$coltypes[$activecol]}', '".$$x."');";
				}
			}
		
		}
		$firstrow = false;
		reset($row);
		foreach($varnames as $varid => $varname)
		{
			$gdata .= "$varname.addRow();";
			foreach($activecols[$varid] as $datacol => $activecol)
			{
				$value = $rowvals[$activecol];
				if ($coltypes[$activecol] == 'string')
					$value = "'$value'";
				if($coltypes[$activecol] == 'boolean')
				{
					
					$cellClass = "{'className': '".$value."_style'}";
					$gdata .= "$varname.setCell($rowindex,$datacol,$value,null,$cellClass);";
				}
				else
					$gdata .= "$varname.setCell($rowindex,$datacol,$value);";
			}
		}
		$rowindex++;
	}
		
	return $gdata;
}

function add_log($action, $user_id  = NULL)
{
	global $_SERVER;
	$query  = "INSERT INTO access_logs (ip, user_id, actiontime, action) VALUES (?,?,?,?)";
	if($result = execute_prepared_query($query,array($_SERVER['REMOTE_ADDR'],$user_id,date('Y-m-d H:i:s'),$action),"siss"))
		return true;
	else return false;
}

function add_ndt_log($type, $message)
{
	global $_SERVER;
	$query  = "INSERT INTO ndt_logs (ip, user_id, connection_id, severity, message) VALUES (?,?,?,?,?)";
	if($result = execute_prepared_query($query,array($_SERVER['REMOTE_ADDR'],$_SESSION['user_id'],$_SESSION['connection_id'],$type, $message),"siiss"))
		return true;
	else return false;
}

function log_ndt_info($message)
{
	return add_ndt_log('INFO', $message);
}

function log_ndt_debug($message)
{
	return add_ndt_log('DEBUG', $message);
}

function log_ndt_warning($message)
{
	return add_ndt_log('WARNING', $message);
}

function log_ndt_error($message)
{
	return add_ndt_log('ERROR', $message);
}

/***
*
* Produce links for changing language on top of page
*
*****/
function lang_link($l)
{
	global $lang_lang, $lang_otherlang, $action;
	$langindex = ($l+1) % 2;
	$qstr = preg_replace('/(^|&)(l=\d)($|&)/','',$_SERVER['QUERY_STRING']);
	
	//Correct to show login page on lang change right after login. Actually, ignore action=login.
	if ($action == 'dashboard')
		$qstr = preg_replace('/action=login/','',$qstr);
	
	if (!empty($qstr)) $qstr = '&amp;'.$qstr;
	$link = "<a href=\"?l=$langindex".$qstr."\">{$lang_lang[$langindex]}</a>";
	return $link;
}

/*****
*
*  Construct csv files with measurements for download.
*/

function create_csv_from_array($arr, $withkeys = false)
{
	$csv = "";

	if (is_array($arr))
	{
		foreach ($arr as $singlerow)
		{
			if ($withkeys && !isset($keys))
			{
				$keys = array_keys($singlerow);
				$csv .= comma_seperated_vals($keys,';')."\n";
			}
			$csv .= comma_seperated_vals($singlerow,';')."\n";
		}
		return $csv;
	}
	else
	{ 
		return "";
	}
}

function create_csv_file_from_array($arr, $filename, $withkeys = false)
{
	$f = fopen($filename, "w");
	if (is_array($arr))
	{
		foreach ($arr as $singlerow)
		{
			if ($withkeys && !isset($keys))
			{
				$keys = array_keys($singlerow);
				fwrite($f,comma_seperated_vals($keys,';')."\n");
			}
			fwrite($f,comma_seperated_vals($singlerow,';')."\n");
		}
		fclose($f);
		return $filename;
	}
	else
	{ 
		fclose($f);
		return NULL;
	}
}

function output_csv_from_dbquery($q, $withkeys = false)
{
	global $spebs_db;
	
	$res = $spebs_db -> query($q);
	
	while($singlerow = $res -> fetch_assoc())
	{
		if ($withkeys && !isset($keys))
		{
			$keys = array_keys($singlerow);
			echo comma_seperated_vals($keys,';')."\n";
		}
		echo comma_seperated_vals($singlerow,';')."\n";
	}
	
}

function comma_seperated_vals($tuple="", $seperator = ",")
{
	$many_vals = false;
	$result_string = "";
	if (empty($tuple))
		return "";
	else
	{
		foreach ($tuple as $k => $element)
		{
			if ($many_vals)
				$result_string .= $seperator;
			$many_vals = true;
			$result_string .= $element;
		}
	}
	
	return $result_string;
}
/*****
*
* **************************** Functions for registering and editing user accounts  **************************
*
*
******/
function adduser($firstname,$lastname,$email,$password, $contact=1,$profile=1) 
{
	global $spebs_db;
	$pquery  = "INSERT INTO user (firstname,lastname,email,password,profile,creation_time,active,contact) VALUES(?,?,?,SHA1(?), ?, ?,0,?)";
	$newuid = get_prepared_insert_id($pquery, array($firstname,$lastname,$email,$password,$profile,date('Y-m-d H:i:s'),$contact), 'ssssisi');
	
	if(is_numeric($newuid) && $newuid>0)
	{
		send_registration_confirmation($newuid, $firstname.' '.$lastname, $email, $password);
		add_log('REGISTRATION',$newuid);
		return $newuid;
	}
	else
		return false;
}

function addconnection($user_id, $connection_name, $mainconnection, $isp_id, $purchased_bandwidth_dl_kbps, $purchased_bandwidth_ul_kbps,
	$street, $street_num, $postal_code, $municipality, $country, $longitude, $latitude, $current_connection_id = NULL)
{
	global $spebs_db, $lang_connection;
	$address = "$street $street_num, $postal_code, $municipality";
	
	//OLD:  deactivate any existing user connections before adding the new one
	//$pquery = "UPDATE connection set status=-1 WHERE connection_id IN (SELECT connection_id FROM user_connection WHERE user_id=?)";
	//$res = execute_prepared_query($pquery,array($user_id),'i');
	
	//NEW:  deactivate current connection if it was seriouly changed
	if(!(is_null($current_connection_id) || empty($current_connection_id)))
	{	
		$pquery = "UPDATE connection set status=-1 WHERE connection_id =?";
		error_log("DEACTIVATE:  ".$pquery." ($current_connection_id)");
		$res = execute_prepared_query($pquery,array($current_connection_id),'i');
	}
	
	if (is_null($current_connection_id) || empty($current_connection_id) || $res)
	{
		$mid = get_municipality_code(greek_municipality($municipality),$postal_code);
		
		if($mainconnection)
		{
			$query = "UPDATE user_connection uc, connection c SET status=2 WHERE user_id=? AND uc.connection_id=c.connection_id AND status=1";
			execute_prepared_query($query,array($user_id),'i');
			$status = 1;
		}
		else
			$status = 2;
		
		if(empty($connection_name))
		{
			$counter = get_prepared_single_value("SELECT count(*) c FROM user_connection WHERE user_id=?",array($user_id),"i");
			$connection_name = "$lang_connection ".(++$counter);
		}
		
		$query = "INSERT INTO connection (description, isp_id, purchased_bandwidth_dl_kbps, purchased_bandwidth_ul_kbps, street, str_number, address, postal_code, 
		municipality, country, longitude, latitude, exchange_id, distance_to_exchange, max_bw_ondistance, max_vdslbw_ondistance, creation_time, status) 
		VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		
		$exchange_info = find_exchange($latitude,$longitude);
		$pars = array($connection_name, $isp_id, $purchased_bandwidth_dl_kbps, $purchased_bandwidth_ul_kbps, $street, $street_num, $address, $postal_code, $mid, $country, $longitude, $latitude, $exchange_info['exchange_id'], $exchange_info['distance_m'], $exchange_info['max_bandwidth'], $exchange_info['max_vbandwidth'], date('Y-m-d H:i:s'), $status);
		$connection_id = get_prepared_insert_id($query, $pars, 'siiissssisddiiiisi');
		
		if(is_int($connection_id) && $connection_id>0)
		{
			add_log('NEW CONNECTION '.$connection_id, $user_id);
			$query = "INSERT INTO user_connection (user_id, connection_id) VALUES (?, ?)";
			if(execute_prepared_query($query, array($user_id, $connection_id), 'ii'))
			{
				adjust_postal_code_location($postal_code,$latitude,$longitude,$mid);
				return $connection_id;
			}
			else 
				return false;
		}
		else 
			return false;
	}
	else
		return false;
}

function adjust_postal_code_location($pc, $lat, $long,$municipality)
{
	$query = "SELECT * FROM postal_codes WHERE code = ? ";
	$res = execute_prepared_query($query,array($pc),'i',true);
	if($res !== false)
	{	
		if(!empty($res))
		{
			$longitude = ($res[0]['longitude']*$res[0]['occurences'] + $long)/($res[0]['occurences']+1);
			$latitude = ($res[0]['latitude']*$res[0]['occurences'] + $lat)/($res[0]['occurences']+1);
			$occurences = $res[0]['occurences']+1;
			$query = "UPDATE postal_codes SET longitude=?, latitude=?, occurences=? WHERE code=?";
			$pars = array($longitude, $latitude, $occurences, $pc);
			$vartypes = 'ddii';
		}
		else
		{
			$nl = "";
			$nn = "";
			$q = "SELECT name_el, name_en FROM tk WHERE postal_code = ?";
			$r = execute_prepared_query($q,array($pc),'i',true);
			if(!empty($r))
			{
				$nl = $r[0]['name_el'];
				$nn = $r[0]['name_en'];
			}
			$query = "INSERT INTO postal_codes (code,longitude,latitude,municipality,occurences, name_el, name_en ) VALUES 
					(?,?,?,?,1, ?, ?)";
			$pars = array($pc,$long,$lat,$municipality,$nl,$nn);
			$vartypes = 'iddiss';
		
		}
		
		if($result = execute_prepared_query($query,$pars,$vartypes))
			return true;
	}
	else 
		return false;
}

function update_connection($connection_id, $user_id, $connection_name, $mainconnection, $isp_id, $purchased_bandwidth_dl_kbps, $purchased_bandwidth_ul_kbps,
	$street, $street_num, $postal_code, $municipality, $country, $longitude, $latitude)
{
	$address = "$street $street_num, $postal_code, $municipality";
	
	if($mainconnection)
	{
		$query = "UPDATE user_connection uc, connection c SET status=2 WHERE user_id=? AND uc.connection_id=c.connection_id AND status=1";
		execute_prepared_query($query,array($user_id),'i');
		$status = 1;
	}
	else
	{
		$mainc = get_prepared_single_value("SELECT c.connection_id FROM user_connection uc NATURAL JOIN connection c WHERE user_id=? AND status=1",array($user_id),'i');
		error_log("SELECT c.connection_id FROM user_connection uc NATURAL JOIN connection c WHERE user_id=$user_id AND status=1 \n".$mainc." == ".$connection_id." ?");
		if($mainc == $connection_id)//set the first created connection as main
		{
			$firstc = get_prepared_single_value("SELECT c.connection_id FROM user_connection uc, connection c WHERE user_id=? AND status>0 ORDER BY creation_time LIMIT 1",array($user_id),'i');
			error_log("Set $firstc as main");
			execute_query("UPDATE connection SET status=1 WHERE connection_id=$firstc");
		}
		$status = 2;
	}
	//$connection_id = get_connection_id($user_id);
	$query = "UPDATE connection SET 
		description = ?, isp_id = ?, purchased_bandwidth_dl_kbps=?, purchased_bandwidth_ul_kbps=?,
		street=?, str_number=?, address=?, postal_code=?,municipality='".get_municipality_code($municipality,$postal_code)."', 
		country=?, longitude=?, latitude=?, exchange_id=?, distance_to_exchange=?, max_bw_ondistance=?, max_vdslbw_ondistance=?, status=?
	WHERE connection_id=?";
	
	$exchange_info = find_exchange($latitude,$longitude);
	$pars = array($connection_name,$isp_id,$purchased_bandwidth_dl_kbps,$purchased_bandwidth_ul_kbps,$street,$street_num,$address,$postal_code,$country,$longitude,$latitude,$exchange_info['exchange_id'],$exchange_info['distance_m'],$exchange_info['max_bandwidth'],$exchange_info['max_vbandwidth'],$status,$connection_id);
	$vartypes = 'siiisssssddiiiiii';
	
	//error_log($query."\n $connection_name,$isp_id,$purchased_bandwidth_dl_kbps,$purchased_bandwidth_ul_kbps,$street,$street_num,$address,$postal_code,$country,$longitude,$latitude,{$exchange_info['exchange_id']},{$exchange_info['distance_m']},{$exchange_info['max_bandwidth']},{$exchange_info['max_vbandwidth']},$status,$connection_id)");
	if($result = execute_prepared_query($query,$pars,$vartypes))
	{
		add_log('UPDATE CONNECTION '.$connection_id, $user_id);
		return true;
	}
	else
		return false;
}

function register_new_user($details)
{
	global $bandwidths;
	$valid_details = valid_form_data($details);
	if (!is_array($valid_details))
	{	
		/**** Remove firstname and lastname fields ***/
		if ($user_id = adduser("","",$details['email'],$details['password']))
		{	
			add_log('REGISTER USER ',$user_id);
			return addconnection($user_id, $details['connectionname'], isset($details['mainconnection']), $details['isp'], $bandwidths[$details['bandwidth']]['d'], $bandwidths[$details['bandwidth']]['u'],
			$details['street'], $details['street_num'], $details['postal_code'], $details['municipality'], "Ελλάδα",
			$details['addrlng'], $details['addrlat']);
		}
	}
	else 
		return $valid_details;
}

function update_user($user_id, $details)
{
	global $bandwidths,$_SESSION;
	$checkpassw = (!empty($details['password']))? true:false;
	$valid_details = valid_form_data($details,false,$checkpassw);
	if (!is_array($valid_details))
	{
		$passwupdate = (!empty($details['password']))? ", password = SHA1(?)":"";
		/**** Remove name field ***/
		$contactby = (isset($details['contactby']) && $details['contactby'] == 'yes')? 1:0;
		$pars = (!empty($details['password']))? array($details['email'],$contactby,$details['password']):array($details['email'],$contactby);
		$vartypes = (!empty($details['password']))? 'sis':'si';
		$query  = "UPDATE user SET firstname = ' ', lastname = ' ', email = ?, contact = ? $passwupdate WHERE user_id=$user_id";
		if($result = execute_prepared_query($query,$pars,$vartypes));
			add_log('UPDATE USER', $user_id);
	
		if(isset($details['connectionid']) && $details['connectionid']>0)
		{	
			//user has changed connection either translocating or by changing contract or isp
			if (connection_changed($user_id,$details))
			{	
				$new_connid = addconnection($user_id, $details['connectionname'], isset($details['mainconnection']), $details['isp'], $bandwidths[$details['bandwidth']]['d'], $bandwidths[$details['bandwidth']]['u'],
				$details['street'], $details['street_num'], $details['postal_code'], $details['municipality'], "Ελλάδα",
				$details['addrlng'], $details['addrlat']);
				if($_SESSION['connection_id'] == $details['connectionid'])
					$_SESSION['connection_id'] = $new_connid;
			}
			//user tries to correct some non significant connection details
			else 
			{	
				update_connection($details['connectionid'], $user_id, $details['connectionname'], isset($details['mainconnection']), $details['isp'], $bandwidths[$details['bandwidth']]['d'], $bandwidths[$details['bandwidth']]['u'],
				$details['street'], $details['street_num'], $details['postal_code'], $details['municipality'], "Ελλάδα",
				$details['addrlng'], $details['addrlat']);
			}
			return true;
		}
		//regustered user creates new connection
		else
		{
			$new_connid = addconnection($user_id, $details['connectionname'], isset($details['mainconnection']), $details['isp'], $bandwidths[$details['bandwidth']]['d'], $bandwidths[$details['bandwidth']]['u'],
			$details['street'], $details['street_num'], $details['postal_code'], $details['municipality'], "Ελλάδα",
			$details['addrlng'], $details['addrlat']);
		}
	}
	else
	{
		$res = get_alluser_connections_details($user_id);
		$valid_details['connections'] = array();
		for($i=0;$i<count($res);$i++)
		{
			list ($c['connection_id'], $c['street'], $c['street_num'], $c['postal_code'], $c['municipality_code'], $c['municipality'],$c['isp'], $bdown, $bup, $c['addrlat'], $c['addrlng'], $c['description'], $c['status']) = array_values($res[$i]);
			$c['bandwidth'] = which_bandwidth_combination($bup, $bdown);
			array_push($valid_details['connections'], $c);
		}
		return $valid_details;
	}
	 
}

function connection_changed($user_id,$details)
{
	global $bandwidths;
	$municipality = get_municipality_code($details['municipality'],$details['postal_code']);
	
	$query = "SELECT connection_id FROM connection 
			WHERE connection_id=?"
			." AND isp_id=?" 
			." AND purchased_bandwidth_dl_kbps=?"
			." AND purchased_bandwidth_ul_kbps=?"
			." AND street=?"
			." AND str_number=?"
			." AND municipality = $municipality"
			." AND status>0";
	$pars = array($details['connectionid'],$details['isp'],$bandwidths[$details['bandwidth']]['d'],$bandwidths[$details['bandwidth']]['u'],$details['street'],$details['street_num']);
	$vartypes = 'iiiiss';
	$conn_id = get_prepared_single_value($query,$pars,$vartypes);
	if (is_int($conn_id) && $conn_id > 0)
		return false;
	else
		return true;
}

function activate_user($encrypted_user_id) 
{
	$query="SELECT user_id FROM user WHERE sha1(sha1(user_id))=?" ;
	$uid = get_prepared_single_value($query,array($encrypted_user_id),'s');
	if(is_int($uid) && $uid>0)
	{
		$query="UPDATE user SET active=1 WHERE user_id=?" ;
		if($res = execute_prepared_query($query,array($uid),'i'))
		{
			add_log('ACTIVATE USER', $uid);
			return true;
		}
		else 
			return false;
	}
	else 
		return false;
	
}



function send_registration_confirmation($user_id, $name, $email, $password)
{
	global $automatic_mail_headers, $lang_registration_confirmation_mail_body, $lang_registration_confirmation_mail_title, $home;
	$headers = 'From: HYPERION <'.$automatic_mail_headers['from'].'>' . "\r\n" .
		'Reply-To: HYPERION <'.$automatic_mail_headers['reply-to'].'>' . "\r\n" .
		'Content-type: text/plain; charset=UTF-8'  . "\r\n" . 
		'X-Mailer: PHP/' . phpversion();

	// The message
	$message = sprintf($lang_registration_confirmation_mail_body,$home, sha1(sha1($user_id)));
	// In case any of our lines are larger than 70 characters, we should use wordwrap()
	$message = wordwrap($message, 70);

	// Send
	//mail($name . " <" . $email . ">", 'HYPERION: Registration confirmation', $message, $headers);
	mail($name . " <" . $email . ">", '=?ISO-8859-7?Q?=D5=D0=C5=D1=C9=D9=CD=3A_=C5=F0=E9=E2=E5=E2=E1=DF?==?ISO-8859-7?Q?=F9=F3=E7_=E5=E3=E3=F1=E1=F6=DE=F2?=', $message, $headers);
}

function send_rand_password($user_id, $email, $password)
{
	global $automatic_mail_headers, $lang_forgot_password_mail_body,$lang_forgot_password_mail_body_with_activation, $lang_forgot_password_mail_title, $home;
	$headers = 'From: HYPERION <'.$automatic_mail_headers['from'].'>' . "\r\n" .
		'Reply-To: HYPERION <'.$automatic_mail_headers['reply-to'].'>' . "\r\n" .
		'Content-type: text/plain; charset=UTF-8' . "\r\n" . 
		'X-Mailer: PHP/' . phpversion();
	
	// The message
	$query  = "SELECT user_id FROM user WHERE email= ? AND active=1";
	$uid = get_prepared_single_value($query,array($email),'s');
	if (is_int($uid) && $uid > 0)
		$message = sprintf($lang_forgot_password_mail_body , $password, $home);
	else //not activated user account
		$message = sprintf($lang_forgot_password_mail_body_with_activation,$password,$home, sha1(sha1($user_id)));
	
	// In case any of our lines are larger than 70 characters, we should use wordwrap()
	$message = wordwrap($message, 70);

	// Send
        //mail($email, 'HYPERION: New password', $message, $headers);
        mail($email, '=?ISO-8859-7?Q?=D5=D0=C5=D1=C9=D9=CD=3A_=CD=DD=EF=F2_=EA=F9=E4?==?ISO-8859-7?Q?=E9=EA=FC=F2_=E1=F3=F6=E1=EB=E5=DF=E1=F2?=', $message, $headers);
}

function str_rand($length = 8, $seeds = 'alphanum')
{
    // Possible seeds
    $seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
    $seedings['numeric'] = '0123456789';
    $seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
    $seedings['hexidec'] = '0123456789abcdef';
    
    // Choose seed
    if (isset($seedings[$seeds]))
    {
        $seeds = $seedings[$seeds];
    }
    
    // Seed generator
    list($usec, $sec) = explode(' ', microtime());
    $seed = (float) $sec + ((float) $usec * 100000);
    mt_srand($seed);
    
    // Generate
    $str = '';
    $seeds_count = strlen($seeds);
    
    for ($i = 0; $length > $i; $i++)
    {
        $str .= $seeds{mt_rand(0, $seeds_count - 1)};
    }
    
    return $str;
} 
function update_user_password($user_id, $password)
{
	if ((isset($password)) && (isset($user_id)))
	{
		$query  = "UPDATE user SET password=SHA1(?) WHERE user_id=?";
		if($result = execute_prepared_query($query,array($password,$user_id),'si'))
		{
			add_log('UPDATE USER PASSWORD',$user_id);
			return true;
		}
		else die(db_error());
	}
	else
		return false;

}

/*
*
*
***************************************** Various data types validation. ********************************************
*
*
*/
function valid_form_data($form_values,$checkrecaptcha=true,$checkpassword=true)
{
	global $recaptchaPrivateKey, $recaptchPublicKey;
	require_once('recaptchalib.php');
	
	global $lang_firstname,$lang_lastname,$lang_email,$lang_password,$lang_street,$lang_street_num,$lang_municipality,$lang_postal_code,
		$lang_isp,$lang_connection,$lang_connection_name,$lang_bandwidth,$lang_invalid_input,$bandwidths,$lang_not_matching_municipality,$lang_email_exists;

	$invalid = false;
	$invalid_fields = "";

	if($checkrecaptcha)
	{
		$resp = recaptcha_check_answer ($recaptchaPrivateKey,
			$_SERVER["REMOTE_ADDR"],
			$_POST["recaptcha_challenge_field"],
			$_POST["recaptcha_response_field"]);

		if (!$resp->is_valid) {
			$invalid = true;
			$invalid_fields .= " RECAPTCHA";
			echo "<!-- RECAPTCHA_ERROR: " ."The reCAPTCHA wasn't entered correctly. Go back and try it again." .
				"(reCAPTCHA said: " . $resp->error . ")" . " -->";
		}
	}

	/*********************** Remove name field
	if (!string_valid(trim($form_values['firstname'])))
	{
		$form_values['firstname'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_firstname";
	}
	if (!string_valid(trim($form_values['lastname'])))
	{
		$form_values['lastname'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_lastname";
	}
	*********************************/
	$newuser = ($form_values['command'] == 'register')? true:false;
	if (!email_valid(trim($form_values['email']), $newuser))
	{
		$form_values['email'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_email";
		$lang_email = str_replace(array($lang_email_exists,'(',')'), "", $lang_email);
	}
	if ($form_values['password'] != $form_values['password_confirm'])
	{
		$form_values['password'] = "";
		$form_values['password_confirm'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_password";
	}
	if (isset($form_values['connectionid']) && !empty($form_values['connectionid']) && !db_cross_valid($form_values['connectionid'], $_SESSION['user_id'], "connection_id", "user_id", "ii", "user_connection"))
	{
		$form_values['connectionid'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= "$lang_connection";
	}
	if (trim($form_values['connectionname']) != "" && !string_valid(trim($form_values['connectionname'])))
	{
		$form_values['connectionname'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_connection_name";
	}
	if (!string_valid(trim($form_values['street'])))
	{
		$form_values['street'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_street";
	}
	if (!strnum_valid(trim($form_values['street_num'])))
	{
		$form_values['street_num'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_street_num";
	}
	if (!string_valid(trim($form_values['municipality'])) || !db_valid(greek_municipality(trim($form_values['municipality'])),"name_el",'s',"municipalities"))
	{
		$form_values['municipality'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_municipality";
	}
	elseif(!string_valid(trim($form_values['postal_code'])))
	{
		$form_values['postal_code'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_postal_code";
	}
	elseif(!coord_valid(trim($form_values['addrlat'])) || !coord_valid(trim($form_values['addrlng'])))
	{
		$form_values['addrlat'] = "";
		$form_values['addrlng'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_coordinates";
	}
	
	elseif(get_municipality_code(greek_municipality(trim($form_values['municipality'])),intval(trim($form_values['postal_code'])))<=0)
	{ 
		$form_values['postal_code'] = "";
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_postal_code ($lang_not_matching_municipality {$form_values['municipality']})";
	}
	if (!db_valid(trim($form_values['isp']),"isp_id",'i',"isp"))
	{
		$form_values['isp'] = -1;
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_isp";
	}
	if (trim($form_values['bandwidth'])<0 || trim($form_values['bandwidth'])>count($bandwidths)-1)
	{
		$form_values['bandwidth'] = -1;
		if ($invalid) 
			$invalid_fields .= ",";
		$invalid = true;
		$invalid_fields .= " $lang_bandwidth";
	}
	
	if ($invalid)
	{	
		$form_values['error'] = "$lang_invalid_input $invalid_fields";
		return $form_values;
	}
	else
		return true;
}

function db_valid($value, $field, $fieldtype, $table)
{
	$query = "SELECT count(*) from $table where $field = ?"; 
	$v = get_prepared_single_value($query,array($value),$fieldtype); 
	if(is_int($v) && $v>0)
		return true;
	else 
		return false;
}

function db_cross_valid($value1, $value2, $field1, $field2, $fieldtypes, $table)
{
	$query = "SELECT count(*) from $table where $field1 = ? AND $field2 = ?";
	$v = get_prepared_single_value($query,array($value1,$value2),$fieldtypes); 
	if(is_int($v) && $v>0)
		return true;
	else 
		return false;
}

function db_cross_valid_multiple($value1, $value2, $field1, $field2, $fieldtypes, $table)
{
	$f1_vals = "?";
	$f2_vals = "?";
	$f_types = $fieldtypes[0];
	if(is_array($value1))
		for($i=1;$i<count($value1);$i++)
		{	
			$f1_vals .= ",?";
			$f_types .= $fieldtypes[0];
		}
	$f_types .= $fieldtypes[1];
	if(is_array($value2))
		for($i=1;$i<count($value2);$i++)
		{
			$f2_vals .= ",?";
			$f_types .= $fieldtypes[1];
		}
	$query = "SELECT $field1, $field2 from $table where $field1 IN ($f1_vals) AND $field2 IN ($f2_vals)";
	$q_vals = array_merge((array)$value1, (array)$value2);
	$res = execute_prepared_query($query,$q_vals,$f_types,true); 
	
	if(!empty($res))
		return $res[0];
	else 
		return false;
}


function greek_municipality($m)
{
	$q = "SELECT name_el FROM municipalities WHERE name_el_no_accents = ? OR name_en = ? OR name_el_gen_caps = ? OR name_el = ?";
	return get_prepared_single_value($q,array($m,$m,$m,$m),'ssss');
}

function string_valid($str)
{
	//mb_internal_encoding("UTF-8");
	//echo "<hr><b>$str</b><br>set internal encoding to UTF-8 ";
	//mb_regex_encoding("UTF-8");
	//echo "<br>set regex encoding to UTF-8 ";
	//$pattern = "/^[\w\s\xce-\xcf]+$/u";
	//$pattern = "/^[\w\s]+$/";
	//$pattern = "/^[\w\sα-ωΑ-Ω]+$/";
	$pattern = "/^[\w\s\xce84-\xcf8e.,']+$/";
	//$enc = mb_detect_encoding($str);
	//echo "<br>string encoding = ".mb_detect_encoding($str)."<br>strlen = ".strlen($str)." mb_strlen = ".mb_strlen($str)."<br>regex encoding = ".mb_regex_encoding()."<br>internal encoding = ".mb_internal_encoding()."<br>";
	//if (mb_ereg_match($pattern,$str))
	if (preg_match($pattern,$str)>0)	
		return true;
	else 
		return false;
}

function strnum_valid($str)
{
	$pattern = "/^[0-9]+[\w\xce84-\xcf8e]*$/";
	if (preg_match($pattern,$str) > 0)
		return true;
	else 
		return false;
}
function coord_valid($coordinate)
{
	if (is_numeric($coordinate))
		if($coordinate>15 && $coordinate<50)
			return true;
	return false;
}
function ipv4_valid($ip)
{
	$pattern = "/^[\d]+.[\d]+.[\d]+.[\d]+$/u";
	if (preg_match($pattern,$ip) > 0)
		return true;
	else 
		return false;
}

function email_valid($email_addr, $newuser = true)
{
        global $_SESSION, $lang_email, $lang_email_exists;
        $pattern = "/^[\w.\-_]+@[\w_\-]+(\.[\w_\-]+)+$/";
        if (preg_match($pattern,$email_addr) > 0)
        {
                // check if new email already exists OR  profile update and email not changed
                if (!db_valid($email_addr,"email",'s',"user") || (!$newuser && ($email_addr == $_SESSION['username'])))
                        return true;
                else
                {
                    if(db_valid($email_addr,"email",'s',"user"))
                            $lang_email .= " ($lang_email_exists)";
                    return false;
                }
        }
        else
                return false;
}

function address_format($address)
{
	$chunks = explode(', ',$address);
	return (count($chunks) == 3)? $chunks[0].'<br/>'.$chunks[1].', '.$chunks[2]:$chunks[0].'<br/>'.$chunks[1];
}

/***
*
* Find user's IP and see if it belongs to any known block
* in the future, try to use $_SERVER['HTTP_CLIENT_IP'] and $_SERVER['HTTP_X_FORWARDED_FOR']
*	
***/
function valid_isp()
{
	$isp_id = NULL;
	$ip = $_SERVER['REMOTE_ADDR']; 
	$isp_id = get_isp_from_ipv4($ip);
	//$user_isp = get_user_isp($_SESSION['connection_id']);
	return 	($isp_id != NULL);
}

function valid_greek_ip()
{
	$ip = $_SERVER['REMOTE_ADDR']; 
	if(ipv4_valid($ip))
	{
		$isp_id = get_isp_from_ipv4($ip);
		//$user_isp = get_user_isp($_SESSION['connection_id']);
		return 	($isp_id != NULL);
	}
	return false;
}

function redirect_home()
{
	global $home;
	header($home);
}

function polygonToArray($polygon)
{
	$polygon = preg_replace('/[\(\)a-zA-Z]+/','',$polygon);
	$p = explode(',',$polygon);
	return  $p;
}
function polygonToGPolygon($polygon)
{
	$p = polygonToArray($polygon);
	//print_r($p);
	//$gp = "new google.maps.Polygon({paths:[";
	$gp = "[";
	
	$first = true;
	foreach($p AS $v)
	{
		if(!$first)
			$gp .= ",";
		list($vlng,$vlat) = explode(' ',$v);
		$gp .= "new google.maps.LatLng($vlat,$vlng)";
		$first = false;
	}	
	//$gp .= "]})";
	$gp .= "]";
	return  $gp;
}
function find_exchange($lat,$lng)
{
	if(isset($lat) && isset($lng) && is_numeric($lat) && is_numeric($lng))
	{ 
		$query = "SELECT id, longitude, latitude, AsText(SHAPE) pol, polygon_type type
			FROM local_exchange le 
			WHERE Contains(le.SHAPE,POINT(?,?))
			ORDER BY polygon_type<>'real'";
		$result = execute_prepared_query($query,array($lng,$lat),'dd',true);
		return select_exchange($result,$lng,$lat);
	}
}
function select_exchange($exchanges,$lng,$lat)
{
	require_once("point_in_polygon.php");
	$pointLocation = new pointLocation();
	$p = null;
	foreach($exchanges AS $e)
	{
		if($pointLocation->pointInPolygon("$lng $lat", polygonToArray($e['pol'])) != "outside")
		{	
			$p = array('e'=>$e['id'], 'n'=>$e['longitude'], 't'=>$e['latitude']);
			break;
		}
	}
	
	if(is_array($p))
		$exch = $p;
	else
	{
		return array('exchange_id'=>"NULL",'distance_m'=>"NULL",'max_bandwidth'=>"NULL");
	}
	$distance1 = distanceOnGrid($lat,$lng,$exch['t'],$exch['n']);
	$distance2 = distance($lat,$lng,$exch['t'],$exch['n']);
	$bw1 = maxBandwidth4Distance($distance1);
	$bw2 = maxBandwidth4DistanceVDSL($distance2);
	return array('exchange_id'=>$exch['e'],'distance_m'=>$distance1,'max_bandwidth'=>$bw1,'max_vbandwidth'=>$bw2);
}

function distance($lat1, $lon1, $lat2, $lon2) 
{ 
  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $meters = round($dist * 60 * 1.1515 * 1609.344);
  
  return $meters;
}


function distanceOnGrid($lat1, $lon1, $lat2, $lon2) 
{ 
  $sum = distance($lat1, $lon1, $lat1, $lon2) + distance($lat1, $lon2, $lat2, $lon2);
  return $sum;
}

function maxBandwidth4Distance($dist) 
{ 
		$bw_steps_every_300m = array(	
			24000,	// 0
			23500,	// 300
			22500,	// 600
			22000,	// 900
			20000,	// 1200
			18000,	// 1500
			17000,	// 1800
			15000,	// 2100
			14000,	// 2400
			12000,	// 2700
			9500,	// 3000
			8000,	// 3300
			6500,	// 3600
			5000,	// 3900
			4000,	// 4200
			3500,	// 4500
			2500,	// 4800
			2000,	// 5100
			1500,	// 5400
			1000,	// 5700
			750,	// 6000
			500,	// 6300
			128);	// 6600
		$i = ceil($dist*1.00/300.0);
		if ($i >= count($bw_steps_every_300m))
			return 128;
		else
			return $bw_steps_every_300m[$i];
}
function maxBandwidth4DistanceVDSL($dist) 
{ 
		$bw_steps_every_250m = array(
			50000, //0
			42000, //250
			36000, //500
			33000, //750
			28000, //1000
			21000, //1250
			17000  //1500
			);
		$i = ceil($dist*1.00/250.0);
		if ($i >= count($bw_steps_every_250m))
			return 0;
		else
			return $bw_steps_every_250m[$i];
}

function meters2Km($meters)
{
	global $lang_km;//, $max_distance_from_exchange_meters;
	$i = $meters/1000;
	$f = round($meters/1000.0,1);
	//$maxkm = round($max_distance_from_exchange_meters/1000.0,1);
	
	//if ($f > $max_distance_from_exchange_meters)
		//return "&gt; $maxkm ".$lang_km;
	//else
		return ($i == $f)? $i." ".$lang_km:$f." ".$lang_km;
}
function kbps2Mbps($kbps, $digits=1, $letters=4)
{
	
	$f = $kbps/1000;
	$roundf = round($f,$digits);
	if($digits == 0 && $f<>$roundf)
	{	
		$unit = ($letters == 4)? " Kbps":"K";
		$v = $kbps;
	}
	else
	{	
		$unit = ($letters == 4)? " Mbps":"M";
		$v = $roundf;
	}
	return $v.$unit;
}

/*****************************          Display messages          ************************************/
 
function display_message($message)
{
 
 echo "<div class=\"message\">$message</div>";
}

function info_message($message)
{
 return "<div class=\"infomessage\">$message</div>";
}

function display_info_message($message)
{
 echo "<div class=\"infomessage\">$message</div>";
}

function top_info_message($message)
{
 return "<div class=\"topinfomessage\">$message</div>";
}

function display_top_info_message($message)
{
 echo "<div class=\"topinfomessage\">$message</div>";
}

function show_notification($title, $message)
{
	$html .= '<div id="disclaimer"><h2>'
			.$title
			.'</h2>'
			.info_message($message)
			.'</div>';
	echo $html;
}

function show_disclaimer()
{
	global $lang_disclaimer_title, $lang_disclaimer_message;
	show_notification($lang_disclaimer_title, $lang_disclaimer_message);
}

//****  Disclaimer   *********************
/*function show_disclaimer() {
	global $lang_disclaimer_title, $lang_disclaimer_message;
	
	$html .= '<div id="disclaimer"><h2>'
			.$lang_disclaimer_title
			.'</h2>'
			.info_message($lang_disclaimer_message)
			.'</div>';
	echo $html;
}*/


function display_error_message($error)
{
 echo "<p class=\"errormessage\">$error</p>";
}



/***
*
* User deletion. Not used anywhere but may come in handy...
*
*****/
function delete_user($email)
{
	$uid = get_user_id($email);
	if (!$uid)
		echo "Sorry! <font color=\"red\">:-/</font> This user doesn't exist.";
	elseif ($uid > 10)
	{
		echo "So you wanted to delete user ".$uid."...<br>";
		if ($uid)
			$ucs = getUserConnections($uid);
		foreach ($ucs as $k => $uc)
		{	
			echo "User connection ".$uc['connection_id'].":<br/>";
			$q = "DELETE FROM generic_measurement WHERE connection_id=?"; 
			if(execute_prepared_query($q,array($uc['connection_id']),'i'))
			{	$q = "DELETE FROM generic_measurements_stats WHERE connection_id={$uc['connection_id']}"; 
				if(execute_query($q))
					echo "Connection NDT measurements deleted.<br/>";
			}
			$q = "DELETE FROM glasnost_measurement WHERE connection_id=?"; 
			if(execute_prepared_query($q,array($uc['connection_id']),'i'))
			{	$q = "DELETE FROM glasnost_measurements_stats WHERE connection_id={$uc['connection_id']}"; 
				if(execute_query($q))
					echo "Connection Glasnost measurements deleted.<br/>";
			}
			$q = "DELETE FROM connection WHERE connection_id=?"; 
			if(execute_prepared_query($q,array($uc['connection_id']),'i'))
				echo "Connection itself deleted.<hr/>";
		}
		$q = "DELETE FROM user_connection WHERE user_id=?"; 
		if(execute_prepared_query($q,array($uid),'i'))
		{
			$q = "DELETE FROM user WHERE user_id=?";
			if(execute_prepared_query($q,array($uid),'i'))
			{	
				echo "Finally user $uid is deleted.<br/><font color=\"green\">DONE :-)</font><hr/>";
				$q = "DELETE FROM access_logs WHERE user_id=$uid"; 
				execute_prepared_query($q,array($uid),'i');
			}
		
		}
	}
	else
		echo "Sorry! <font color=\"red\">:-/</font> Not allowed to delete user $uid.";
}


/***
*
* Nice readable print of arrays. For debugging reasons only...
*
*****/
function angela_print_array($arr, $spaces = 0)
{
	$sp=$spaces+1;
	
	if (is_array($arr))
		foreach ( $arr as $arrkey => $arrval )
		{
			for ($i=0; $i<$spaces; $i++) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
			echo "[$arrkey] => <br>\n";
			angela_print_array($arrval, $sp);
		}
	else
	{
		for ($i=0; $i<$spaces; $i++) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
		if (!isset($arr))
			echo "-- empty --<br>";
		else
			echo "$arr <br>";
	}
}
#F2FFFB