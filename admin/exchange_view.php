<?
	/*if(isset($_GET['p']) && !isset($_GET['v']))
		$ajaxtarget .= '?p=1';
	elseif(isset($_GET['v']) && !isset($_GET['p']))
		$ajaxtarget .= '?v=1';
	elseif(isset($_GET['v']) && isset($_GET['p']))
		$ajaxtarget .= '?v=1&p=1';
	else
		$ajaxtarget .= '?p=1';*/
require_once("../init.inc.php");

	$pagetitle = "Αστικά κέντρα";
	$scripts[] = "https://maps.googleapis.com/maps/api/js?key={$googleMapsV3Key}&libraries=geometry&sensor=false";
	$scripts[] = "../js/markermanager.js";
	$scripts[] = "http://ajax.googleapis.com/ajax/libs/prototype/1.6.1.0/prototype.js";
	$scripts[] = "../js/jquery_latest.js";
	$scripts[] = "../js/mapiconmaker.js";
	$scripts[] = "js/exchanges_map.js";

	include("header.php");
	
	$ajaxtarget = $home.'exchanges.php';
	$zoom = 11;
	
	?>
	<div id="dashboardtitle" style="width:800px;margin:auto;">
		 <h3>Απεικόνιση των αστικών κέντρων και των πολυγώνων τους</h3>
	</div>
		<div id="space" style="width: 800px; height: 200px;margin:auto;">
		<? 
			$message = "<p>Τα πραγματικά πολύγωνα είναι τα πολύγωνα που έχουν δοθεί από την ΕΕΤΤ (αφορούν την Αθήνα και τη Θεσσαλονική). Ο Voronoi χωρισμός του ελλαδικού χώρου, με βάση τα υπόλοιπα αστικά κέντρα, 
			σχηματίζει τα Voronoi πολύγωνα. Όλα τα σημεία εντός αυτών των πολυγώνουν έχουν ως κοντινότερο αστικό κέντρο (από το σύνολο με τα κέντρα χωρίς πραγματικό πολύγωνο) αυτό που ανήκει στο πολύγωνο.
			</p><p>Τα πολύγωνα δε φορτώνονται το ίδιο αποδοτικά όπως στον επίσημο χάρτη. Για το λόγο αυτό, ενώ είναι ενεργά όλα τα zoom level, μόνο στα αναλυτικότερα εμφανίζονται πολύγωνα.
			Τα πιο μακροσκοπικά επίπεδα zoom χρησιμεύουν για τη γρηγορότερη μετακίνηση του χάρτη μεταξύ απομακρυσμένων περιοχών.</p>";
			display_message($message); 
		?>
		</div>
		<div style="width: 1000px; height: 900px;margin:auto;">
			<!--div id="athens" style="width: 600px; height: 600px; float:left;"></div-->
			<div id="tools" style="float:left;margin:auto;">
				<div class="metricstab" id="real" style="width:150px;"><a class="selected" href="#" onclick="change_view('real');return false;">Real polygons</a></div>
				<div class="metricstab" id="voronoi" style="width:150px;"><a href="#" onclick="change_view('voronoi');return false;">Voronoi polygons</a></div>
				<!--div class="metricstab" id="voronoi2" style="width:150px;"><a href="#" onclick="change_view('voronoi2');return false;">Voronoi old polygons</a></div-->
				<div class="metricstab" id="all" style="width:150px;"><a href="#" onclick="change_view('all');return false;">All polygons</a></div>
				<div style="clear:both"></div>
			</div>
			<div id="space" style="width: 800px; height: 60px"></div>
			<div id="greece" style="width: 1000px; height: 800px; float:left; margin:auto;"></div>
		</div>
		
	

