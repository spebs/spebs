<?php
include("init.inc.php");

//$postal_codes = get_results("select distinct postal_code, concat(postal_code,',',m.name_el,',','Ελλάδα') codename from tk t join municipalities m on t.municipality_id=m.id where t.longitude is null order by postal_code limit 20", true);
$postal_codes = get_results("select postal_code from tk_new where longitude is null limit 20", true);
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
</head>
<body>
<script>

var reversegeourl = 'http://maps.googleapis.com/maps/api/geocode/json';
//?latlng=38.0062983,23.7905602&sensor=false&language=el'

function sleep(ms) 
{
	ms += new Date().getTime();
	while (new Date() < ms){}
} 

var regions = new Array(<?$flag=false;
	foreach($postal_codes as $pc)
	{
		if($flag) 
			echo ",";
		//echo '{postal_code:'.$pc['postal_code'].', codename:"'.$pc['codename'].'"}';
		echo '{postal_code:'.$pc['postal_code'].', codename:""}';
		$flag=true;
	}
?>);

for(i=0;i<regions.length;i++)
{
sleep(1000);
(function() 
{
var rid=i;
//$.getJSON( reversegeourl, {address: regions[i]['codename'], sensor: false, region: 'gr'})
$.getJSON( reversegeourl, {sensor: false, components: 'postal_code:'+regions[i]['postal_code']+'|country:gr'})
.done(function( data ) 
	{
		$.each( data.results, function( key, val ) 
			{
				if(data.status == "OK")
				{	
				//	$( '<div id="region'+rid+'">UPDATE tk SET longitude = '+val.geometry.location.lng+', latitude = '+val.geometry.location.lat+' WHERE postal_code='+regions[rid]['postal_code']+';</div><br/>' ).appendTo( "body" );
				//	$( '<div id="region'+rid+'">INSERT INTO tk_new VALUES ('+regions[rid]['postal_code']+','+val.geometry.location.lng+','+val.geometry.location.lat+') ;</div><br/>' ).appendTo( "body" );
				$( '<div id="region'+rid+'">UPDATE tk_new SET longitude = '+val.geometry.location.lng+', latitude = '+val.geometry.location.lat+' where postal_code = '+regions[rid]['postal_code']+' ;</div><br/>' ).appendTo( "body" );
				}
				else if(data.status == "ZERO_RESULTS")
				{	
				$( '<div id="region'+rid+'">UPDATE tk_new SET longitude = "NOT FOUND", latitude = "NOT FOUND" where postal_code = '+regions[rid]['postal_code']+' ;</div><br/>' ).appendTo( "body" );
				}
				
			});
	});
}
)();

}
</script>

<?
?>
</body>
</html>