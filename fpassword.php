<?
/**
 * SPEBS 
 *
 * The script displays password forgotten page. 
 *
 *
 * @copyright (c) 2011 еетт
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
	require_once('parameters.inc.php');
	require_once('recaptchalib.php');
	$publickey  = $recaptchaPublicKey;
	$privatekey = $recaptchaPrivateKey;
	$java_scripts = array("js/jquery_latest.js","js/jquery.validate.js");
	include("header.php");
	$path = $relative_path;
	if (isset($_REQUEST['chk'])) 
	{
       	$resp = recaptcha_check_answer ($privatekey,
        $_SERVER["REMOTE_ADDR"],
        $_POST["recaptcha_challenge_field"],
        $_POST["recaptcha_response_field"]);

        if (!$resp->is_valid) 
		{
            $error_message = $lang_recaptcha_error; 
        }
		else 
		{

			$uid = get_user_id($_REQUEST['username']);
         	if ((isset($uid)) && is_int($uid) && $uid>0) 
			{
            	$tmp = str_rand();
            	if(update_user_password($uid, $tmp))
				{
                	send_rand_password($uid, $_REQUEST['username'], $tmp);
					$message  = $lang_password_change_success;	
				}
				else 
					$error_message	= $lang_password_change_failed."  ". $lang_credentials_not_set;
            }
			else 
			{
				$error_message  = $lang_password_change_failed."  " .$lang_username_error;
			}
		}
    }

?>
<script type="text/javascript">
$().ready(function() 
{
$("#passreset").validate({
		rules: {
			username: {
				required: true,
				email:    true
			}
		},
		messages: {
			username: "  <?= $lang_wrong_email_format ?>"
		}});	
});
</script>

	<br/><br/>
	<div id="forgotpasswordcontent">
		<div id="forgotpasswordheader">
			<?= $lang_reset_password ?>
		</div>
		<div style="margin:10px;">
		<!--?= $lang_description ? -->
	<!--/div-->
	<!--div style="width:680; float:center; text-align:left; height:470px; padding-top:50px; line-height:200%"-->
<?
	$introduction_text = (isset($error_message))? $error_message : $lang_description_forgot_password ;
if(isset($error_message))
	display_error_message($error_message);
else
{
	$introduction_text = (isset($message))? $message : $lang_description_forgot_password ;
}
?>
<? if (!isset($message) || isset($error_message)) //in ths case display form
{
?>
		<form id="passreset" name="passreset" action="/<?= $relative_path?>?action=fpassword&chk=1" method="post">
			<?= $introduction_text;?>
			<p>
			<label for="username"><?= $lang_email ?></label>
			<input class="textbox w20em" type="text" name ="username" id="username"  maxlength="100">
			<script type="text/javascript">var RecaptchaOptions = {custom_translations:{<?= $lang_captcha_custom_translations?>}};</script>
			<? echo recaptcha_get_html($publickey); ?>
			<br/>
<? 
	echo "<input id=\"enter\" type=\"submit\" value=\" $lang_submit \" />";
?>
			</p>
		</form>
	</div>
<?
}// end if we should display form
if(isset($message)) //success ... inform user and return
	echo "<br/><br/><br/><a href=\"{$_SERVER['PHP_SELF']}\">$lang_return</a>";
?>

</div>
	

<?

	include("footer.php");
?>	
