var jQ = jQuery.noConflict();
		jQ(document.getElementById("greece")).ready(function ()  
		{
			initialize();
		});
		
		var connuserid=13;
		//var newtext = "";
		
		var lex_centers = new Array();
		var lex_polygons = new Array();
		var vlex_centers = new Array();
		var vlex_polygons = new Array();
		var povs = [];
	
		
		var grmap;
		var type = 'real';
		
		function initialize() 
		{
		  var mapOptions = 
							{
							  center: new google.maps.LatLng(37.9768509787241, 23.7267439143598),
							  zoom:  11,
							  minZoom: 6,
							  maxZoom: 17,
							  streetViewControl: false,
							  panControl: false,
							  mapTypeControl: false,
							  mapTypeId: google.maps.MapTypeId.ROADMAP
							 };

		  
		 	grmap = new google.maps.Map(document.getElementById("greece"), mapOptions);
			mm = new MarkerManager(grmap);
			google.maps.event.addListener(grmap,"zoomend", function(oldLevel,newLevel)
			{
				mapdragend();
			});
			google.maps.event.addListener(grmap,"moveend",function()
			{
				mapdragend();
			});
			mapdragend();
			
		}

		
		function mapdragend()
		{
			google.maps.event.addListener(grmap, 'idle', refresh_map);			
		}
		
		function refresh_map() 
		{
			var bnds = grmap.getBounds();
			var sw = bnds.getSouthWest();
			var ne = bnds.getNorthEast();
			
			if(type == 'real')
				t='p=1';
			else if(type=='voronoi')
				t='v=1';
			//else if(type=='voronoi2')
				//t='v=2';
			else
				t='p=1&v=1'
			//grmap.clearOverlays();
								
			if(grmap.getZoom()>=10)
			{	
				new Ajax.Request('exchanges.php'+'?'+t+'&blt='+sw.lat()+'&blg='+sw.lng()+'&trt='+ne.lat()+'&trg='+ne.lng(), 
				{ 
					method:'get',
					onComplete: function(transport, json)
					{
						try 
						{
							resp = JSON.parse(transport.responseText);
							if (resp != null)
							{	
								parse_pin_polyg_vals(resp);
								show_everything();
							}
						}
						catch(err) 
						{
							alert(err);
						}
					},
					onSuccess: function()
					{ 
						
					},
					onFailure: function()
					{ 
						alert('Something went AJAXly wrong...') 
					}
				});
			}
			else
			{
				clear_map();
			}
		}
		
		function parse_pin_polyg_vals_encoded(jsontext)
		{
			lex_centers.length = 0;
			lex_polygons.length = 0;
			if (jsontext != null)
			{
				if (jsontext["points"] != null)
				{
					for (pi=0;pi<jsontext["points"].length;pi++)
						lex_centers[pi]=[jsontext["points"][pi][1],eval(jsontext["points"][pi][0])];
				}
				if (jsontext["polys"] != null)
				{
					for (pi=0;pi<jsontext["polys"].length;pi++)
						lex_polygons[pi]=[jsontext["polys"][pi][0],jsontext["polys"][pi][1],jsontext["polys"][pi][2]];
				}
			}
			
		}
		
		function parse_pin_polyg_vals(jsontext)
		{
			lex_centers.length = 0;
			lex_polygons.length = 0;
			vlex_centers.length = 0;
			vlex_polygons.length = 0;
			
			if (jsontext != null)
			{
				if (jsontext["points"] != null)
				{
					for (pi=0;pi<jsontext["points"].length;pi++)
						lex_centers[pi]=[jsontext["points"][pi][0],eval(jsontext["points"][pi][1])];
				}
				if (jsontext["polys"] != null)
				{
					for (pi=0;pi<jsontext["polys"].length;pi++)
					{	
						lex_polygons[pi]=[jsontext["polys"][pi][0],eval(jsontext["polys"][pi][1])];
					}
				}
				if (jsontext["vpoints"] != null)
				{
					for (pi=0;pi<jsontext["vpoints"].length;pi++)
						vlex_centers[pi]=[jsontext["vpoints"][pi][0],eval(jsontext["vpoints"][pi][1])];
				}
				if (jsontext["polys"] != null)
				{
					for (pi=0;pi<jsontext["vpolys"].length;pi++)
					{	
						vlex_polygons[pi]=[jsontext["vpolys"][pi][0],eval(jsontext["vpolys"][pi][1])];
					}
				}
			}
			
		}
		
		
		function createPolygon_encoded(map,polypoints, polylevels, color, message)
		{
			
			var polydetails = new Array();
			//alert("id "+message);
			//alert("polypoints====\n"+polypoints);
			//alert("polylevels====\n"+polylevels);
			
			//polydetails[0] = {points: polypoints, levels: polylevels, color: color, opacity: 0.7, weight: 1, numLevels: 18, zoomFactor: 2}; 
			//var poly = GPolygon.fromEncoded({ polylines: polydetails, fill: true, color: color, opacity: 0.2, outline: true});
			pls = google.maps.geometry.encoding.decodePath(polypoints);
			var poly = new google.maps.Polygon({ paths: pls, strokeColor: color, fillColor: color, strokeOpacity: 0.7, fillOpacity: 0.2, strokeWeight: 1});
			
			map.addOverlay(poly);
			google.maps.event.addListener(poly, "click", function(event) 
			{
				var myHtml = message;
				//map.openInfoWindowHtml(clickedpoint, myHtml);
				iw = new google.maps.InfoWindow({position: event.latLng, content: myHtml});
				iw.open(map);
			});
			google.maps.event.addListener(poly, "mouseover", function() 
			{
				//poly.setFillStyle({opacity: 0.5});
				poly.setOptions({fillOpacity: 0.5});
			});
			google.maps.event.addListener(poly, "mouseout", function() 
			{
				//poly.setFillStyle({opacity: 0.2});
				poly.setOptions({fillOpacity: 0.2});
			});
			map.addOverlay(poly);
		}
		
			
		function createPolygon(map,polyoints,color,message)
		{
			
			//poly.setStrokeStyle({color: color, opacity: 0.9, weight: 1});
			//poly.setFillStyle({color: color, opacity: 0.2, weight: 1}); 
			var poly = new google.maps.Polygon({ paths: polyoints, strokeColor: color, fillColor: color, strokeOpacity: 0.9, fillOpacity: 0.2, strokeWeight: 1});
			
			google.maps.event.addListener(poly, "click", function(event) 
			{
				var myHtml = "Local exchange polygon "+message;
				//map.openInfoWindowHtml(clickedpoint, myHtml);
				iw = new google.maps.InfoWindow({position: event.latLng, content: myHtml});
				iw.open(map);
			});
			google.maps.event.addListener(poly, "mouseover", function() 
			{
				//poly.setFillStyle({opacity: 0.5});
				poly.setOptions({fillOpacity: 0.7});
			});
			google.maps.event.addListener(poly, "mouseout", function() 
			{
				//poly.setFillStyle({opacity: 0.2});
				poly.setOptions({fillOpacity: 0.2});
			});
			povs.push(poly);
			poly.setMap(map);
			
		}
		
		function remove_polygons()
		{
			var i;
			if (povs.length > 0)
			{	
				for (i=0;i<povs.length;i++)
					povs[i].setMap(null);
				povs.length = 0;
			}
		}
		
		function clear_map()
		{
			mm.clearMarkers();
			remove_polygons();
		}
		
		function show_everything()
		{
			var p;
			clear_map();
			cl = (type == 'voronoi' || type == 'all')? "#ff7000":"#007000";
			mm.addMarkers(getMarkers(grmap,lex_centers, "#3c83dd",32),10);
			mm.addMarkers(getMarkers(grmap,vlex_centers, cl,32),10);
			mm.refresh();
			for(i=0;i<lex_polygons.length;i++)
			{
				createPolygon(grmap,lex_polygons[i][1], "#3c83bb", lex_polygons[i][0]);
			}
			for(i=0;i<vlex_polygons.length;i++)
			{
				cl = (type == 'voronoi' || type == 'all')? "#ffaa00":"#00aa00";
				createPolygon(grmap,vlex_polygons[i][1], cl, vlex_polygons[i][0]);
			}
		}
		
		
		function change_view(polytype)
		{
			var selectedel = document.getElementById(type).getElementsByTagName('a')[0];
			selectedel.className = "";
			selectedel = document.getElementById(polytype).getElementsByTagName('a')[0];
			selectedel.className = "selected";
			type = polytype;
			//mapdragend();
			refresh_map();
		}
		
		
		
		function getMarkers(map,markerpoints, color, size)
		{
			
			var iconOptions = {};
			iconOptions.width = size;
			iconOptions.height = size;
			iconOptions.primaryColor = color;
			iconOptions.cornerColor = "#ffffff";
			iconOptions.strokeColor = color;
			
			m = new Array();
			for (i = 0; i < markerpoints.length; i++) 
			{
				m.push(createMarker(map, markerpoints[i][1], iconOptions, markerpoints[i][0]));
				//map.addOverlay(m);
				//m.setMap(map);
			}
			return m;

		}
		
		
		function createMarker(map, point, options, message) 
		{
		  var icooptions = MapIconMaker.createMarkerIcon(options);
		  var marker = new google.maps.Marker({position: point, icon: icooptions.icon, shadow: icooptions.shadow, shape: icooptions.shape});
		 
		  google.maps.event.addListener(marker, "click", function() 
		  {
			var myHtml = "Local exchange "+message;
			var iw = new google.maps.InfoWindow({position: point, content: myHtml});
			iw.open(map);
		  });
		  return marker;
		}
		
