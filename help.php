<?php
/**
 * SPEBS 
 *
 * This script contains renders help with section titles and pieces of content in order.
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
$hide_help = true;
include("header.php");
?>
	
<div class="help_container">
<div class="title1"><?= $lang_help ?></div>
<br>
<?php
$help_sections = array("introduction","general","settings","measurement","glasnost","show","faq");
for ($i=0;$i<count($help_sections);$i++)
{
	$section = $help_sections[$i];
	$lang_title = ${"lang_help_".$section."_title"};
	$lang_content = ${"lang_help_".$section."_content"};
	$iplus = $i+1;
	echo "<a name=\"$section\"><div class=\"title2\">$iplus. $lang_title</div></a>";
	if (!is_array($lang_content))
		echo $lang_content;
	else
	{
		if ($lang_content[0] != "intro")
			$correction_val = 1; // to show 0th element of array as 1st section
		else
			$correction_val = 0;
		for ($j=0;$j<count($lang_content);$j++)
		{
			$subsection = $lang_content[$j];
			$jplus = $j+$correction_val;
			
			if ($subsection != "intro")
			{
				$lang_subtitle = ${"lang_help_".$section."_".$subsection."_title"};
				echo "<a name=\"$subsection\"><div class=\"title3\">$iplus.$jplus $lang_subtitle</div></a>";
			}
			$lang_subcontent = ${"lang_help_".$section."_".$subsection."_content"};
			echo $lang_subcontent;
		}
	
	}
}
echo "</div>";//<!-- .help_container -->
include("footer.php");
?>
