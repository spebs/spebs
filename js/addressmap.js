/**
 * SPEBS 
 *
 * This script produces the map for defining address exact coordinates and responds to autocomplete events regarding
 * municipalities and postal codes. See also autocomplete.php
 *
 *
 * @copyright (c) 2011 ΕΕΤΤ
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE 
 *
 *
 * @author ICCS, NOC Team, National Technical University of Athens

 * Originally written  by Aggeliki Dimitriou <A.Dimitriou@noc.ntua.gr> 
 *                        Panagiotis Christias <P.Christias@noc.ntua.gr> 
 *                        Athanasios Douitsis <A.Douitsis@noc.ntua.gr> 
 *                        Chrysa Papagianni <C.Papagianni@noc.ntua.gr> 
**/
$(document.getElementById("addrlng")).ready(function () 
{
	initialize_map();
});
$(document.getElementById("postal_code")).ready(function()
{
	$("#postal_code").autocomplete("autocomplete.php",
		{
			extraParams: 
			{
				m: $.trim($('#municipality').val())
			},
			minChars: 0,
			max: 1000,
			cacheLength: 1
		}
	);
	
	$("#postal_code").blur(function()
	{
		$.post("autocomplete.php",
				{
					pc: $.trim($("#postal_code").val()), 
					m: $.trim($("#municipality").val())
				}, 
				function(data)
				{
					if(data == "unknown")
					{
						$("#postal_code").val("");
					}
					else if(!is_empty(data))
					{
						$("#municipality").val(data);
						showInputAddres();
					}
				}
		);
	});
	
	$("#municipality").autocomplete("autocomplete.php",{
		extraParams: {p: function() 
						{ 
							return $.trim($("#postal_code").val()); 
						}
					},
			minChars: 0,
			max: 1000,
			cacheLength: 1}
		);
	
	$("#municipality").blur(function()
	{
		$.post("autocomplete.php",
				{
					mn: $.trim($("#municipality").val()), 
					p: $.trim($("#postal_code").val())
				}, 
					function(data)
					{
						if(data == "unknown")
						{
							$("#municipality").val("");
						}
						else if(!is_empty(data))
						{
							$("#postal_code").val(data);
							showInputAddres();
						}
					}
				);
	});
	$("#street").blur(function()
	{
		showInputAddres();
	});
	$("#street_num").blur(function()
	{
		showInputAddres();
	});
});

	var swpoint;
	var nepoint;
	var areabounds;
	var mypointid;
	
	var grmap;
	var addressmarker = null;
	
	function initialize_map() 
	{
      //if (GBrowserIsCompatible()) 
	  //{
 		var mapOptions = 
						{
						  center: new google.maps.LatLng(38.30, 23.95),
						  zoom: 6,
						  minZoom: 6,
						  maxZoom: 17,
						  streetViewControl: false,
						  panControl: false,
						  mapTypeControl: false,
						  mapTypeId: google.maps.MapTypeId.ROADMAP
						 };
		grmap = new google.maps.Map(document.getElementById("greece"), mapOptions);
        
		curlat = $.trim(document.getElementById("addrlat").value);
		curlng = $.trim(document.getElementById("addrlng").value);
		showInputAddres();
		
      //}
    }

	function reset_map() 
	{
      //if (GBrowserIsCompatible()) 
	  //{
        grmap.setCenter(new google.maps.LatLng(38.30, 23.95));
		grmap.setZoom(6);
        
		//grmap.clearOverlays();
		if(addressmarker != null)
			addressmarker.setMap(null);
		
		$("#addrlat").val("");
		$("#addrlng").val("");
      //}
    }
	
	function is_empty(str)
	{
		if (str==null || str.length == 0)
			return true;
		else
			return false;
	}
	
	function showInputAddres() 
	{	
		var street = $.trim(document.getElementById("street").value);
		var streetno = $.trim(document.getElementById("street_num").value);
		var pcode = $.trim(document.getElementById("postal_code").value);
		var municipality = $.trim(document.getElementById("municipality").value);
		//if (is_empty(street) || is_empty(streetno) || is_empty(pcode) || is_empty(municipality))
		if (is_empty(street) ||  is_empty(pcode) || is_empty(municipality))
			return false;
		
		var address = street+' '+streetno+','+pcode+','+municipality+', Greece';
		var alt = pcode+','+municipality+', Greece';
		showAddres(address,alt);
		if(is_empty($("#addrlat").val()))
			reset_map();
	}
	
	function showAddres(address,alternativeAddress) 
	{
		mapload = false;
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({address: address}, function(results, status) 
		{
			if (addressmarker == null)
			{	
				mapload = true;
				addressmarker = new google.maps.Marker({draggable:true});
			}
			if (!results || status != google.maps.GeocoderStatus.OK) 
			{ 
				if(!is_empty(alternativeAddress))
					showAddres(alternativeAddress,""); 
				return false;
			} 
			else 
			{
				var point = results[0].geometry.location;
				
				//var point = new GLatLng(place.Point.coordinates[1],
									//place.Point.coordinates[0]);
				
				addressmarker.setPosition(point);
				addressmarker.setMap(grmap);
				//addressmarker = new GMarker(point,{draggable:true})
				//grmap.addOverlay(addressmarker);
				grmap.setCenter(point);
				grmap.setZoom(15);
				document.getElementById("addrlat").value = point.lat();
				document.getElementById("addrlng").value = point.lng();
		
				google.maps.event.addListener(addressmarker, "dragend", function(dragendevent) {
					document.getElementById("addrlat").value = dragendevent.latLng.lat();
					document.getElementById("addrlng").value = dragendevent.latLng.lng();
				});
				
				if(mapload && !is_empty(curlat) && !is_empty(curlng))
				{		
					var adjustedPoint = new google.maps.LatLng(curlat,curlng);
					moveMarker(adjustedPoint);
				}
				
			}
		
		});
		
			
	}
	
	
	function showClickedPoint(point)
	{
		addressmarker.setMap(null);
		addressmarker = new GMarker({position: point, draggable:true})
		addressmarker.setMap(grmap);
		grmap.setCenter(point);
		grmap.setZoom(15);
		document.getElementById("addrlat").value = point.lat();
		document.getElementById("addrlng").value = point.lng();
	}
	
	function moveMarker(point)
	{
		grmap.setCenter(point);
		grmap.setZoom(15);
		addressmarker.setPosition(point);
		document.getElementById("addrlat").value = point.lat();
		document.getElementById("addrlng").value = point.lng();
	}
	
	function changeConnection(connid, description, status, street, str_number, postal_code, municipality, addrlat, addrlng, isp, bandwidth)
	{
			$("#connectionid").val(connid);
			$("#connectionname").val(description);
			if(status)
				$("#mainconnection").attr('checked','checked');
			else
				$("#mainconnection").removeAttr('checked');
			$("#street").val(street);
			$("#street_num").val(str_number);
			$("#postal_code").val(postal_code);
			$("#municipality").val(municipality);
			$("#addrlat").val(addrlat);
			$("#addrlng").val(addrlng);
			$("#isp").val(isp);
			$("#bandwidth").val(bandwidth);
			
			if (addressmarker == null)
			{	
				addressmarker = new google.maps.Marker({draggable:true});
			}
			
			var adjustedPoint = new google.maps.LatLng(addrlat,addrlng);
			moveMarker(adjustedPoint);
			addressmarker.setMap(grmap);
	}
	
	function resetConnection()
	{
			$("#connectionid").val("");
			$("#connectionname").val("");
			$("#mainconnection").removeAttr('checked');
			$("#street").val("");
			$("#street_num").val("");
			$("#postal_code").val("");
			$("#municipality").val("");
			$("#addrlat").val("");
			$("#addrlng").val("");
			$("#isp").val("");
			$("#bandwidth").val("");
			reset_map();
	}
	
