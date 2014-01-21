<?

session_start();
$key = $_SESSION['key'];
$key = preg_replace('/[0-9]+:/', '', $key);
activate_user($key);

include("header.php");		

?>

<table cellpadding="50">
<tr>
<td>
<?= $lang_registration_activation_confirmation ?>
<p>
</td>
</tr>
</table>

<?
include("footer.php");		
?>
