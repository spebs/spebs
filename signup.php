<?
/**
 * SPEBS 
 *
 * Regiatrtion form for new user as well as editing profile form for already registered ones.
 *
 * MODES:
 *     Variable $command is examined for deciding about which operation this is about. Initially $command is set to: 
 *       1. "signup" for registering new user
 *       2. "edit" for registered user who wishes to update her profile
 *
 *     When form submission happens $command is set to:
 *       3. "register" for registering new user
 *       4. "update" for updating existing profile
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

	require_once("visualizations.lib.php");
	require_once('recaptchalib.php');
	$publickey  = $recaptchaPublicKey;
	$privatekey = $recaptchaPrivateKey;
     
/*** Process create new account or update existing account request **/
$show_form = true;
unset($field_values);
if (isset($_POST['command']))
	$command = $_POST['command'];
$submit_button_caption = $lang_create_new_account;

/*********  Outside Greece registration prohibited   **********/
if(!valid_greek_ip() && ($action == "signup" || $command == "signup"))
{
	$command = "prohibited";
	$message = $lang_not_greek_ip;
}

if($command == "signup")
{
	$command = "register";
	if(isset($_SESSION['user_id']))
	{	
		$command = "prohibited";
		$message = $lang_already_registered;
	}
}
elseif($command == "register")
{
	if(isset($_SESSION['user_id']))
	{	
		$command = "prohibited";
		$message = $lang_already_registered;
	}
	else
	{	
		$field_values = register_new_user($_POST);
		if (!is_array($field_values))
		{	
			$message = $lang_registration_completed;
			$show_form = false;
		}
		else
		{	
			$error_message = $lang_registration_failed."<br/>".$field_values['error'];
			$command = "register";
		}
	}

}
elseif (!isset($_SESSION['user_id']) && $command != "prohibited")
{
/*********   if command not set, assume an attempt to create new account *********/
	$command = "register";
}
/*********   Existing account editing request *********/
elseif($command == "edit")
{		
    $field_values = get_field_values_for_user($_SESSION['user_id']);
	$command = "update";
	$submit_button_caption = $lang_update_account;
}
/*********   Apply changes to existing account *********/
elseif($command == "update")
{
		$field_values = update_user($_SESSION['user_id'],$_POST);
		if (!is_array($field_values))
		{	
			$message = $lang_account_editing_completed;
			$show_form = false;

		}
		else
		{
			$error_message = $lang_account_editing_failed."<br/>".$field_values['error'];
			$command = "update";
			$submit_button_caption = $lang_update_account;
		}
}
/*********   Illegal action *********/
else
	redirect_home();

/****************************************     Main section        *******************************************************/
//Set form values
if ($show_form && isset($field_values))
	foreach ($field_values AS $key => $val)
		$$key = $val;

if (!(isset($java_scripts))) $java_scripts = array("js/jquery_latest.js","js/jquery.validate.js","js/spebs.validate.js");
include("header.php");		

if($command == "prohibited")
{
	display_error_message($message);
}
else
{
// Show/Hide password field 
?>
<script type="text/javascript">

function MyFunction() 
{
	if (document.getElementById) 
	{
		var rows = [
			document.getElementById('id1'),
			document.getElementById('id2'),
			document.getElementById('id3')
			];
	}
	if (rows[0].className=="myinvisibleclass" )
	{
		for (var i=0; i<rows.length-1; i++)
				rows[i].className="formfield myvisibleclass";
		rows[rows.length-1].innerHTML ="<a href=# onClick=MyFunction();>"+'<?= $lang_change_pass_request_cancel?>'+"</a>";
	}
	else 
	{
		for (var i=0; i<rows.length-1; i++)
				rows[i].className="myinvisibleclass";
		rows[rows.length-1].innerHTML ="<a href=# onClick=MyFunction();>"+ '<?= $lang_change_pass_request?>'+"</a>";
	}
}
</script>
<?
if (isset($message))
	display_message($message);

if (isset($error_message))
	display_error_message($error_message);
	
/****************************************     Display form        *******************************************************/

if($show_form)
{
?>

<form id="signupForm" action="/<?= $relative_path?>" method="post">

<div id="signupform">
<?
/****************** Remove name fields *************

<div class="formfield">
  <td class="right" width="200px;"><?= $lang_firstname; ?>
  </div>
  <td width="500px;">
  <input class="textbox w15em" type="text" name="firstname" id="firstname" maxlength="32" value="<?= $firstname?>">
  </div>
</div>
<div class="formfield">
  <div class="formlabel"><?= $lang_lastname; ?>
  </div>
  <div class="forminput">
  <input class="textbox w15em" type="text" name="lastname" id="lastname" maxlength="32" value="<?= $lastname?>">
  </div>
</div>
*****************************************************/ 
?>
<div class="formfield">
  <div class="formlabel">
	<?= $lang_email; ?>
  </div>
  <div class="forminput">
	<input class="textbox w15em" type="text" name="email" id="email" maxlength="100" value="<?= isset($email)? $email:"" ?>">
  </div>
</div>
<? 
	$invisible = ($command == "update")?  "myinvisibleclass":"formfield myvisibleclass";
	$changepasslink = ($command == "update")? '<div class="formfield"><div class="forminput" id="id3"><a href="#" onClick="MyFunction();">'
												.$lang_change_pass_request.' </a>
												</div></div>':"";
	$password = isset($password)? $password:"";
	$password_confirm = isset($password_confirm)? $password_confirm:"";
	echo  
		$changepasslink
		.'<div id="id1" class="'.$invisible.'">
			<div class="formlabel">'
				.$lang_password
			.'</div>
			<div class="forminput">
				<input class="textbox w15em" type="password" name="password" id="password" maxlength="32" value="'.$password.'" autocomplete="off">
			</div>
		</div>
		<div id="id2" class="'.$invisible.'">
			<div class="formlabel">'
				.$lang_password_confirm
			.'</div>
			<div class="forminput">
				<input class="textbox w15em" type="password" name="password_confirm" id="password" maxlength="32" value="'.$password_confirm.'" autocomplete="off">
			</div>
		</div>';
 
?>
<hr width="75%" />
<?
// Location form fields are displayed through the following function of visualizations.php. Among them the address map (addressmap.js) is included.
	show_give_address_map();
?>

<div class="formfield">
  <div class="formlabel">
	<?= $lang_connected_through; ?>
  </div>
  <div class="forminput">
<?
		if (!isset($isp)) 
			$selected = "selected";
		else 
			$selected = "";
?>
	  <select class="w16em" name="isp" id="isp">
		<option value="-1" <?= $selected ?>>-- <?= $lang_select; ?> --</option>
<?
			$isps = getISPlist();
			foreach($isps as $key => $thisisp)
			{
				$selected = "";
				if (isset($isp) && $isp == $thisisp['isp_id']) 
					$selected = "selected";
				else 
					$selected = "";
				echo "<option value=\"{$thisisp['isp_id']}\" $selected>{$thisisp['name']}</option>";
			}
?>
	  </select>
  </div>
</div>
<div class="formfield">
  <div class="formlabel">
	<?= $lang_bandwidth_purchased; ?>
  </div>
  <div class="forminput">
<?
	if (!isset($bandwidth) || $bandwidth == -1) 
		$selected = "selected";
	else 
		$selected = "";

?>
  <select class="w16em" name="bandwidth" id="bandwidth">
	<option value="-1" <?= $selected ?>>-- <?= $lang_select; ?> --</option>
	<?
		foreach($bandwidths as $key => $bw)
		{
			$selected = "";
			if (isset($bandwidth) && $bandwidth == $key) $selected = "selected";
			if ($bw['d'] > 999)
			{
				$d = $bw['d']/1000;
				$d .= " Mbps";
			}
			else
				$d = $bw['d']." Kbps";
			if ($bw['u'] >999)
			{	
				$u = $bw['u']/1000;
				$u .= " Mbps";
			}
			else
				$u = $bw['u']." Kbps";
				
			echo "<option value=\"$key\"  $selected>$d D/L - $u U/L</option>";
		}
	?>
  </select>
  </div>
</div>
<br/><hr width="75%" />


<?
$contactby_checked = "CHECKED";
if (isset($contact) && !$contact) 
{
	$contactby_checked = "";
} 

if($command != "update") 
{ 
// show terms and recaptcha 
?>

<div class="formfield" style="height:120px;padding-bottom:20px;">
  <div class="formlabel" style="height:120px;">
	<?= $lang_terms; ?>
  </div>
  <div class="forminput" style="height:120px;">
        <textarea rows="5" cols="50" readonly="readonly" name="terms" id="terms"><?= $lang_terms_text; ?></textarea>
		<br/>
	<input type="checkbox" id="agree" name="agree" value="yes" <?= (isset($aggree_checked))? $aggree_checked:"" ?>/>
	<span><?= $lang_terms_accept; ?></span> 
  </div>
</div>

<div class="formfield" style="height:20px;padding-bottom:20px;">
  <div class="formlabel" style="height:20px;">
	<?= $lang_contactby; ?>
  </div>
  <div class="forminput" style="height:20px;">
	<input type="checkbox" id="contactby" name="contactby" value="yes" <?= $contactby_checked?>/>
	<span><?= $lang_contactby_accept; ?></span>
  </div>
</div>

<div class="formfield" style="height:130px;">
  <div class="formlabel" style="height:130px;">
	<?= $lang_human; ?>
  </div>
 <script type="text/javascript">var RecaptchaOptions = {custom_translations:{<?= $lang_captcha_custom_translations?>}};</script>
  <div class="forminput" style="height:130px;">
	<? echo recaptcha_get_html($publickey); ?>
  </div>
</div>

<br/><hr width="75%"/>

<? 
} else {
?>

<div class="formfield" style="height:20px;padding-bottom:20px;">
  <div class="formlabel" style="height:20px;">
	<?= $lang_contactby; ?>
  </div>
  <div class="forminput" style="height:20px;">
	<input type="checkbox" id="contactby" name="contactby" value="yes" <?= $contactby_checked?>/>
	<span><?= $lang_contactby_accept; ?></span>
  </div>
</div>


<?
} 
// terms and recaptcha 
?>

	<div class="formfield">
		<div class="formlabel">
		</div>
		<div class="forminput">
			<input id="command" name="command" type="hidden" value="<?= $command?>" />
			<input id="enter" style="width:16em;" type="submit" value="<?= $submit_button_caption; ?>" />
			<input id="enter" style="width:16em;" type="button" value="<?= $lang_cancel_button; ?>" onclick="location.href='<?= $home;?>'"/>
		</div>
	</div>
</div><!-- #signupform -->

</form>
<?
}//if($show_form)
?>
<?
}
include("footer.php");	
?>

