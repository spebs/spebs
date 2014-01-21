<?
	require_once("init.inc.php");
	add_log('LOGOUT',$_SESSION['user_id']);
	$mlablang = $_SESSION['mlablang'];
	update_user_session(false,session_id());
	setcookie('bbtsess','',-1);
	session_destroy();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>SPEBS</title>
	<meta HTTP-EQUIV="REFRESH" content="0; url=<?= $home."?l=$mlablang"?>">
</head>
<body>
</body>
</html>
