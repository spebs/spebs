<?
require_once("../init.inc.php");
if(!isset($_SESSION['profile']) || $_SESSION['profile']<3)
		die("<html><head>
			<title>404 Not Found</title>
			</head><body>
			<h1>Not Found</h1>
			<p>The requested URL was not found on this server.</p>
			</body></html>");
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title><?= $pagetitle?></title>
		<link rel="stylesheet" type="text/css" href="../css/spebs.css"/>
<?
	foreach ($scripts as $scr)
	{
		echo "<script src=\"$scr\"></script>";
	}
?>

	</head>
<body>


<div id="shadow" style="width:1000px;margin:auto;">
<div id="toplinks" style="width:1000px;height:30px;font-size:13px;padding-top:6px;">
<? 
	echo $toplinks;
?>
</div>
<div id="header" style="width:1000px;margin:auto;">
		<div id="maintitle" style="width:1000px; margin:auto;">
			<div id="logo" style="float:left;">
				<a href="<?= $home?>"><img src="../images/logo.png"></a>
			</div>
			<div class="title" style="width:500px; float:right;">
				<?= $lang_spebs?>
			</div>
			
		</div>
	</div>
	<div style="clear:both;">&nbsp;</div>
    <div id="main" style="width:1000px">
		<div id="basiccontainer" style="width:1000px;margin:auto;">

