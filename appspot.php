<?php

include("init.inc.php");

$tool = (isset($_REQUEST['t']) && $_REQUEST['t'] == 'g')? "glasnost":"ndt";

echo file_get_contents(sprintf($naming_server,$tool));
?>
