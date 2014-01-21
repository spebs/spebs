<?
/**
 * SPEBS 
 *
 * This script is included by all pages display SPEBS main header section, i.e. logo, title, variou links etc.
 *
 *
 * @copyright (c) 2011 ΕΕΤΤ
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

?>
		<div id="bottom">
			<a target="_blank" href="http://www.measurementlab.net/"><img style="border:0; float:left;" src="images/mlab_logo.jpg"></a>
			<a target="_blank" href="http://www.eett.gr/"><img style="border:0; float:right;" src="images/eett_logo_en.png"></a>
		</div>
	</div><!-- #basiccontainer-->
</div><!--div #main -->
</div><!--div #shadow -->

<div id="footer">
		<div class="small" style="text-align: center; padding-top: 4px;">
			<a href="?action=info"><?= $lang_about?></a>
			&nbsp; &middot; &nbsp;
			<a href="/news"><?= $lang_news?></a>
			&nbsp; &middot; &nbsp;
			<a href="?action=terms"><?= $lang_terms?></a>
			&nbsp; &middot; &nbsp;
			<a href="mailto:support@broadbandtest.eett.gr"><?= $lang_contact?></a>
			&nbsp; &middot; &nbsp;
			v1.0
			&nbsp; &middot; &nbsp;
			&copy; 2011-2012 ΕΕΤΤ
		</div>
</div>
</body>
</html>	
