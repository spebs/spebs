<?
	include("header.php");
?>	
	<div style="position:relative; width:550px; float:left; top:50px; padding-left:50px; height:300px; font-size:12pt;">
		<?= $lang_description ?>
	</div>
	<div style="position:relative; width:280; float:left; margin:auto; text-align:right; height:170px; padding-top:50px; line-height:200%">
		<form action="/<?= $relative_path.'?'.$_SERVER['QUERY_STRING'] ?>" method="post">
			<label for="username"><?= $lang_email?></label>
			<input class="textbox w12em" type="text" name ="username" id="username" maxlength="32">
			<br/>
			<label for="password"><?= $lang_password?></label>
			<input class="textbox w12em" type="password" name="password" id="password" maxlength="32">
			<br/>
			<label for="remember"><?= $lang_rememberme; ?></label>
			<input type="checkbox" id="remember" name="remember" value="yes"/>
			<br/>
			<br/>
			<input id="enter" type="submit" value="<?= $lang_sign_in; ?>" />
		</form>

	<?
		//if($_SESSION['action']=="invalid") {
		if($action=="invalid") {
	?>
	<strong style="color: #cc2222;"><?= $lang_wrong_credentials; ?></strong>
	<?
	}
	?>

	<br/>

	<a href="/<?= $relative_path?>?action=signup"><?= $lang_new_account; ?></a>
	<br>
	<a href="/<?= $relative_path?>?action=fpassword"><?= $lang_forgot_password; ?></a>
	</div>


<?
	include("footer.php");
?>	
