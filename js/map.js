/**
 * SPEBS 
 *
 * This script shows and updates main map according to user requests about metrics, ISPs and timezone selections.
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


/****************************  jQuery for autocompleting postalcodes and municipalities  ************************************************/

//***************************************************************
// jQuery's $ function causes conflict with prototype library used by google maps api
// so the following error appears
//***************************************************************
//Error: element.dispatchEvent is not a function
//Source file: http://ajax.googleapis.com/ajax/libs/prototype/1.6.1.0/prototype.js    Line: 4619
//***************************************************************
// solution: define no conflict mode for jQuery and use some other notation than $
//http://docs.jquery.com/Using_jQuery_with_Other_Libraries
//***************************************************************

var jQ = jQuery.noConflict();
jQ(document.getElementById("statsmap")).ready(function ()  
{
	initialize_map();
});


	var mm = null;
	var regionicons = [];
	var povs = [];
	var points = new Array();
	var circles = new Array();
	var swpoint;
	var nepoint;
	var areabounds;
	//var globalview = [38.42, 23.55, 6];
	var globalview = [38.02, 23.75, 6];
	var mypointid = new Array();
	var tool = 0;
	var toolname = 'n';
	var infos = new Array();
	var metrics = new Array();
	metrics[0] = ['downstream','upstream','loss','rtt','jitter'];
	metrics[1] = ['flash','bittorrent','emule','gnutella','pop','imap','http','ssh'];
	var values = [new Array(),new Array(),new Array(),new Array(),new Array()];
	var reverse = [1,1,-1,-1,-1];
	//var limit_values = [24.0,1.024,3000,1,100];
	// rtt update
	
	//var limit_values = [24.0,1.024,1,150,300];
	//last minute request
	var limit_values = [50.0,10,1,150,300];
	
	//var scale = [[0.1,0.5,0.8],[0.2,0.55,0.9],[1,0.8,0.2],[1,0.5,0.1],[1,0.5,0.2]];
	//tsarmpopoulos: 1/10/2010
	//var scale = [[0.1666667,0.333333,0.666667],[0.25,0.50,0.75],[1,0.8,0.2],[1,0.5,0.1],[1,0.5,0.2]];
	// rtt update
	
	//var scale = [[0.1666667,0.333333,0.666667],[0.25,0.50,0.75],[1,0.5,0.1],[1,0.5,0.1666667],[1,2/3,1/3]];
	//last minute request
	var scale = [[0.08,0.16,0.32,0.48,0.7],[0.025,0.05,0.075,0.1,0.3],[1,0.5,0.1],[1,0.5,0.1666667],[1,2/3,1/3]];
	
	//var colors = [["#ee0000","#880000"],["#ff7000","#ff3300"],["#ffbb00","#ffaa00"],["#00aa00","#007700"],["#777777","#999999"]];
	//christia: 14/7/2011
	var colors = [["#ca3030","#ca3030"],["#ffaa00","#ffaa00"],["#60c6ee","#60c6ee"],["#3c83bb","#3c83bb"],["#208A71","#208A71"],["#00B164","#00B164"],["#777777","#999999"],["#309f30","#309f30"]];
	//monochromatic points:  var colors = [["#D2B48C","#D2B48C"],["#D2B48C","#D2B48C"],["#D2B48C","#D2B48C"],["#D2B48C","#D2B48C"],["#777777","#999999"]];
	var legends = new Array();
	var isps = new Array();
	var exchanges = new Array();
	var type = "downstream";
	var day = "alldays";
	var parsedday = "-1";
	var time = "alltimes";
	var parsedtime = -1;
	var contract = -1;
	var parsedcontract = -1;
	var ispvisibilitychanged = false;
	var mapdragged = false;
	var infopoint = null;
	var infohtml = null;
	
	var points_category = new Array();
	var statsmap;
	var steps = new Array();
	
	var curtime = "";
	var cthere = false;
	var zoomforce = false;
	var curcenter = null;
	
	function initialize_map() 
	{
      var comp,li,statsmaptypes;
	  //if (GBrowserIsCompatible()) 
	  //{
		var mapOptions = 
						{
						  center: new google.maps.LatLng(38.02, 23.75),
						  zoom: 6,
						  minZoom: 6,
						  maxZoom: 17,
						  streetViewControl: false,
						  panControl: false,
						  mapTypeControl: false,
						  mapTypeId: google.maps.MapTypeId.ROADMAP
						 };

        statsmap = new google.maps.Map(document.getElementById("statsmap"), mapOptions);

        if (areabounds != null)
		{	
			//statsmap.fitBounds(areabounds);
			statsmap.setCenter(areabounds.getCenter());
			statsmap.setZoom(15);
		}
		else
			statsmap.setCenter(new google.maps.LatLng(globalview[0], globalview[1]), globalview[2]);
		
		google.maps.event.addListener(statsmap,"zoomend", function(oldLevel,newLevel)
					{
						x=oldLevel+newLevel;
						if ( x > 13)
                        {       
							zoomforce = true;
							mapdragend();
						}
						zoomforce = false;
					});
		google.maps.event.addListener(statsmap,"movestart",function(){curcenter = statsmap.getCenter();});
		google.maps.event.addListener(statsmap,"moveend",function()
						{
							if ((curcenter != null) && (!curcenter.equals(statsmap.getCenter())))
								mapdragend();
							curcenter = null;
						});
		
		for (i=0;i<scale.length;i++)
		{
			roundDecPlaces = 100;
			if (i==2) roundDecPlaces = 10;
			var s = new Array();
			for (j=0;j<scale[i].length;j++)
			{
			 s[j] = Math.round(scale[i][j]*limit_values[i]*roundDecPlaces)/roundDecPlaces;
			}
			steps[i] = s;
		}
		
		for (i=0;i<scale.length;i++)
		{
			leg = new Array();
			comp = '<';
			rcomp = '>';
			if (reverse[i] < 0)
			{	
				comp = '>';
				rcomp = '<';
			}
			leg[0] = comp+' '+steps[i][0]+"&nbsp;"+units[i];
			for (j=1;j<scale[i].length;j++)
			{
				if(reverse[i] > 0)
					leg[j] = steps[i][j-1]+' - '+steps[i][j]+"&nbsp;"+units[i];
				else
					leg[j] = steps[i][j]+' - '+steps[i][j-1]+"&nbsp;"+units[i];
			} 
			leg[j] = rcomp+' '+steps[i][j-1]+"&nbsp;"+units[i];
			
			legends[i] = leg;
		}
		
		show_legends();
		mm = new MarkerManager(statsmap);
		zoomforce = true;
		mapdragend();
		zoomforce = false;

		var boundaries = new google.maps.LatLngBounds(new google.maps.LatLng(41.432836,20.420000), new google.maps.LatLng(41.86454,23.126000));
		var fyromoverlay = new google.maps.GroundOverlay("images/mac04.png", boundaries);
		fyromoverlay.setMap(statsmap);
		
	  //} //if (GBrowserIsCompatible())
    }

	function show_legends()
	{
		var t=metric(type);
		var leghtm = "";
		if(tool==0)
		{
			
			if(reverse[t]>0)
				for (k=0;k<legends[t].length;k++)
					leghtm += "<div class=\"legend\"  style=\"background-color:"+colors[k][0]+";border-color:"+colors[k][1]+";\">"+legends[t][k]+"</div>";
			else
				for (k=legends[t].length-1;k>=0;k--)
					leghtm += "<div class=\"legend\"  style=\"background-color:"+colors[k][0]+";border-color:"+colors[k][1]+";\">"+legends[t][k]+"</div>";
			document.getElementById("maplegend").innerHTML = leghtm;
		}
		else
			document.getElementById("maplegend").innerHTML = "<div class=\"legend\"  style=\"background-color:"+colors[7][0]+";border-color:"+colors[7][1]+";\"> "+langnonthrottled+" </div><div class=\"legend\"  style=\"background-color:"+colors[1][0]+";border-color:"+colors[1][1]+";\"> "+langthrottled+" </div>";
	}
	
	function remove_legends()
	{
		document.getElementById("maplegend").innerHTML = "";
	}
	
	function show_tabs(prefix)
	{
		document.getElementById(prefix+"metrics").style.display = "inline";
	}
	
	function remove_tabs(prefix)
	{
		document.getElementById(prefix+"metrics").style.display = "none";
	}
	
	function update_points(newtype)
	{
		if (type != newtype || ispvisibilitychanged == true || mapdragged == true)
		{
			var selectedel = document.getElementById(type).getElementsByTagName('a')[0];
			var eltoselect = document.getElementById(newtype).getElementsByTagName('a')[0];
			
			selectedel.className = "";
			eltoselect.className = "selected";
			
			type = newtype;
			show_legends();
			mm.clearMarkers();
			remove_polygons();
			if (points.length>0)
			{	
				show_points(statsmap);
			}
			if (circles.length>0)
			{
				show_circles(statsmap);
			}
			if ((infopoint != null) && (infohtml != null))
			{
				iw = new google.maps.InfoWindow({position: infopoint, content: infohtml});
				iw.open(statsmap);
				infopoint = null;
				infohtml = null
			}
		}
		else return false;
	}
	
	function change_connection(i)
	{
		statsmap.setCenter(points[mypointid[i]][0]);
	}
	
	function change_tool(newtool)
	{
		var selectedel = document.getElementById(type).getElementsByTagName('a')[0];
		selectedel.className = "";
		tools = ['ndt','glasnost'];
		selectedel = document.getElementById(tools[tool]).getElementsByTagName('a')[0];
		selectedel.className = "";
		remove_tabs(toolname);
		show_tabs(newtool);
		toolname = (newtool == 'g')? 'g':'n';
		tool = (newtool == 'g')? 1:0;
		selectedel = document.getElementById(tools[tool]).getElementsByTagName('a')[0];
		selectedel.className = "selected";
		newtype = metrics[tool][0];
		type = newtype;
		show_legends();
		zoomforce = true;
		//mapdragend();
		get_new_map_data();
		zoomforce = false;
	}
	
	
	function change_day_time_contract(newday,newtime,newcontract)
	{
		if(newday !== '' && day != newday)
		{
			var selectedel = document.getElementById(day); 
			var eltoselect = document.getElementById(newday);
			selectedel.className = "";
			eltoselect.className = "selected";
			day = newday;
			parseday();
			zoomforce = true;
			//mapdragend();
			get_new_map_data();
			zoomforce = false;
		}
		else if(newtime !== '' && time != newtime)
		{
			var selectedel = document.getElementById(time);
			var eltoselect = document.getElementById(newtime);
			selectedel.className = "";
			eltoselect.className = "selected";
			time = newtime;
			parsetime();
			zoomforce = true;
			//mapdragend();
			get_new_map_data();
			zoomforce = false;
		}
		else if(newcontract !== '' && contract != newcontract)
		{
			//var selectedel = document.getElementById(contract);
			//var eltoselect = document.getElementById(newcontract);
			//selectedel.className = "";
			//eltoselect.className = "selected";
			contract = newcontract;
			parsecontract();
			zoomforce = true;
			//mapdragend();
			get_new_map_data();
			zoomforce = false;
		}
	}
	
	
	
	
	function value_cat(val,metric_id)
	{
		if(val instanceof Array)
		{
			if(val[1] <= 0)
				return 6;
			if(val[1]*pass>val[0])
				return 7;
			else
				return 1;
		}
		else
		{
			if(val < 0)
				return 6;
			if(tool == 0)
			{
				var step_val;
				var lim_value = limit_values[metric_id]*reverse[metric_id];
				val = val*reverse[metric_id];
				for(var i=steps[metric_id].length;i>0;i--)
				{
					if  (val >= steps[metric_id][i-1]*reverse[metric_id])
					{
						return i;
					}
				}
				return 0;
			}
			else if(tool == 1)
			{
				if(val == 0)
					return 7;
				else
					return 1;
				
			}
		}
	}
	
	function value_categories(vals,metric_id)
	{
		for(var j=0;j<vals.length;j++)
		{
			if (vals[j]<0)
				points_category[j] = 6; 
			else
				points_category[j] = value_cat(vals[j],metric_id);
		}	
	}
	
	function metric(m)
	{
		if (m=="downstream" || m=="flash")
			t=0;
		else if (m=="upstream" || m=="bittorrent")
			t=1;
		else if (m=="loss" || m=="emule")
			t=2;
		else if (m=="rtt" || m=="gnutella")
			t=3;
		else if (m=="jitter" || m=="pop")
			t=4;
		else if (m=="imap")
			t=5;
		else if (m=="http")
			t=6;
		else if (m=="ssh")
			t=7;
		return t ;
		
	}
	
	function show_points(map)
	{
		var cat,t;
		var markerOptions = new Array();
		var manymarkers = [];
		
		//*** Show colored balloons
		for(i=0;i<colors.length;i++)
		{
			var iconOptions = {};
			iconOptions.primaryColor = colors[i][0];
			iconOptions.cornerColor = "#ffffff";
			iconOptions.strokeColor = colors[i][1];
			markerOptions[i] = iconOptions;
		}

		t = metric(type);
		value_categories(values[t],t);
		
		for (i=0;i<points.length;i++)
		{
			if (isps[points[i][1]] == 1)
			{
				cat = points_category[i];
				moptions = markerOptions[cat];
				//if (i == mypointid)
				if(jQ.inArray(i, mypointid)>-1)
				{
					moptions.width = 40;
					moptions.height = 60;
				}
				else
				{
					moptions.width = 32;
					moptions.height = 32;
				}
				manymarkers.push(createMarker(map,i,moptions));
			}
		}
		mm.addMarkers(manymarkers,10);
		mm.refresh();
	}
	

	function createMarker(map, number, options) 
	{
		  var point = points[number][0];
		  var message = infos[number];
		  var icooptions = MapIconMaker.createMarkerIcon(options);
		  var marker = new google.maps.Marker({position: point, icon: icooptions.icon, shadow: icooptions.shadow, shape: icooptions.shape});
		  marker.value = number;
		  
		  google.maps.event.addListener(marker, "click", function() 
		  {
			var myHtml = message; 
			var iw = new google.maps.InfoWindow({position: point, content: myHtml});
			iw.open(map);
		  });
		  return marker;
	}
	
	function createRegionMarker(map,i,centerlat,centerlng,regiontype,color,message)
	{
		var w = 32;
		var h = 37;
		var icindx = regiontype % 2;
		var iconsz = new google.maps.Size(w,h);
		var markerIcon = {anchor: new google.maps.Point(w / 2, h), size: iconsz, url: regionicons[icindx][color]};
		
		var point = new google.maps.LatLng(centerlat,centerlng);
		
		var marker = new google.maps.Marker({position: point,icon: markerIcon});
		marker.value = i; 
		  
		  google.maps.event.addListener(marker, "click", function() 
		  {
			var myHtml = message; 
			var iw = new google.maps.InfoWindow({position: point, content: myHtml});
			iw.open(map);
		  });
		  return marker;
	}

	function createMultiPolygon(map,color,message,circleno)
	{
		var thispoly = new Array();
		thispoly = circles[circleno][6];
		var pls = new Array(thispoly.length);
		for (i=0;i<thispoly.length;i++)
		{
			pls[i] = google.maps.geometry.encoding.decodePath(thispoly[i][0]);
		}
		var poly = new google.maps.Polygon({ paths: pls, strokeColor: color, fillColor: color, strokeOpacity: 0.8, fillOpacity: 0.4, strokeWeight: 1});
		google.maps.event.addListener(poly, "click", function(event) 
		{
			var myHtml = message;
			iw = new google.maps.InfoWindow({position: event.latLng, content: myHtml});
			iw.open(map);
		});
		google.maps.event.addListener(poly, "mouseover", function() 
		{
			poly.setOptions({fillOpacity: 0.7});
		});
		google.maps.event.addListener(poly, "mouseout", function() 
		{
			poly.setOptions({fillOpacity: 0.4});
		});
		
		return poly;
	}
	
	function show_circles(map)
	{
		var aggregated_values,areapolygon,circle_info;
		var circlecenter,t;
		var polygons = [];
		for (p=0;p<circles.length;p++)
		{
			x = circles[p][5];
			aggregated_values = sumAreaISPVals(x.slice());
			t = metric(type);
			val_cat = value_cat(aggregated_values[t], t);
			circle_info = area_info(circles[p][2],aggregated_values);
			if(circles[p].length<7) //create circle
				//areapolygon = createCircle(map,circles[p][0],circles[p][1],circles[p][3],circles[p][4],colors[val_cat][0],circle_info);
				areapolygon = createRegionMarker(map,p,circles[p][0],circles[p][1],circles[p][4],val_cat,circle_info);
			else
				areapolygon = createMultiPolygon(map,colors[val_cat][0],circle_info,p);
			povs.push(areapolygon);
			povs[p].setMap(map);
		}
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
	

	
	function sumAreaISPVals(metric_vals)
	{
		var stats_metric_vals;
		if(tool == 0)
			stats_metric_vals = [0,0,0,0,0,0,0];
		else
			stats_metric_vals = [[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],0,0];
		var active_isps = 0;
		var this_isp_vals; 
		for (l=0;l<metric_vals.length;l++)
		{
			this_isp = metric_vals[l][0]; 
			if (isps[this_isp] == 1)
			{
				if(tool==0)
				{
					for (m=1;m<6;m++)
						stats_metric_vals[m-1] = (stats_metric_vals[m-1]*(active_isps) + metric_vals[l][m])/(active_isps+1);
				}
				else
				{
					for(m=1;m<9;m++)
					{
						stats_metric_vals[m-1][0] += metric_vals[l][m][0];
						stats_metric_vals[m-1][1] += metric_vals[l][m][1];
						stats_metric_vals[m-1][2] += metric_vals[l][m][2];
					}
				}
				stats_metric_vals[stats_metric_vals.length-2] += metric_vals[l][stats_metric_vals.length-1]; //measurements count
				stats_metric_vals[stats_metric_vals.length-1] += metric_vals[l][stats_metric_vals.length]; //connections count
				active_isps++;
			}
		}
		if (active_isps == 0)
		{	
			stats_metric_vals.length = 0;
			if(tool==0)
				stats_metric_vals = [-1,-1,-1,-1,0,0];
			else
				stats_metric_vals = [-1,-1,-1,-1,-1,-1,-1,-1,0,0];
		}
		return stats_metric_vals;
		
	}
	
	function area_info(area_name, vals)
	{
		var thisval=0;
		var info = '<div class="balloon"><div class="class="balloonaddrrow">'+area_name+'</div>';
		//alert(area_name+' connections='+vals[vals.length-1]);
		if(vals[vals.length-2]>0)
		{	
			for (n=0;n<vals.length-2;n++)
			{
				if(vals[n] instanceof Array)
				{	
					if(vals[n][1] == 0) 
						thisval = "&nbsp;-&nbsp;";
					else
						thisval = vals[n][0]+"&nbsp;"+langoutof+"&nbsp;"+vals[n][1]+"&nbsp;("+Math.round((vals[n][0]/vals[n][1])*100)+"%)";
				}
				else
					thisval = Math.round(vals[n]*10)/10+"&nbsp;"+units[n];
				if(n==0)
					classappend = " balloonline";
				else
					classappend = "";
				var balloonmetricclass = (tool == 0)? "balloonmetric":"balloonmetricsmall";
				var balloonmetricvalueclass = (tool == 0)? "balloonmetricvalue":"balloonmetricvaluelarge";
				if(tool == 1 && n == 0)
				{	
					info += '<div class="balloonmetric balloonline" style="width:100%;text-align:center">'+langthrottledconnections+"</div>";
					classappend = "";
				}
				info += '<div class="'+balloonmetricclass+classappend+'">'+metricnames[tool][n]+'&nbsp;&nbsp;&nbsp;</div><div class="'+balloonmetricvalueclass+classappend+'">'+thisval+"</div>";
			}
		}
		if(vals[vals.length-1]>-1)
			info += '<div class="balloonfooter">'+vals[vals.length-2]+' '+langmcount+' / '+vals[vals.length-1]+' '+langccount+'</div><div style="clear: both"></div></div>';
		else
			info += '<div class="infowindownote">'+langnotenaoughdata+'</div></div>';
		return info;
	}
	
	function updispvis(ispid)
	{
		var el = document.getElementById('isp'+ispid);
		if (el.className == "")
		{	
			el.className = "selected";
			isps[ispid] = 1;
		}
		else
		{	
			el.className = "";
			isps[ispid] = 0;
		}
		ispvisibilitychanged = true;
		update_points(type);
		ispvisibilitychanged = false;
	}
	
	function selectAllISPs(select)
	{
		ispscheckboxes = document.getElementsByName("ispselection");
		for (i=0;i<ispscheckboxes.length;i++)
		{	
			if(select)
				ispscheckboxes[i].className = "selected";
			else
				ispscheckboxes[i].className = "";
		}
		for (i=0;i<isps.length;i++)
		{
			if (select)
				isps[i] = 1;
			else
				isps[i] = 0;
		}
		ispvisibilitychanged = true;
		update_points(type);
		ispvisibilitychanged = false;
	}
	
	function mapdragend()
	{
		var thiszoom = statsmap.getZoom();
        if (!zoomforce && (thiszoom < 8))
            return false;
		
		google.maps.event.addListener(statsmap, 'idle', function() 
		{
				get_new_map_data();
			});//idle.... former bounds_changed
	
	}


	function get_new_map_data()
	{
			var bnds = statsmap.getBounds();
			var sw = bnds.getSouthWest();
			var ne = bnds.getNorthEast();
			var update = false;
			curtime = "";
			curtime = curtime+"Before AJAX ";appendtimestamp();
			
			new Ajax.Request('mappoints.php?z='+statsmap.getZoom()+'&blt='+sw.lat()+'&blg='+sw.lng()+'&trt='+ne.lat()+'&trg='+ne.lng()+'&wd='+parsedday+'&wh='+parsedtime+'&c='+parsedcontract+'&t='+toolname, { 
				method:'get',
				onComplete: function(transport, json)
				{
					try 
					{
						resp = JSON.parse(transport.responseText);
						curtime = curtime+"JSON parsing... ";appendtimestamp();
						if (resp != null)
						{
							//**** This prevents update of map if ajax response is empty
							/*
							if (resp["points"] != null)
								update = true;
							if (resp["circles"] != null)
								update = true;
							*/
							update = true;
							parse_pin_polyg_vals(resp);
							curtime = curtime+"JSON parsed and assigned to vars ";appendtimestamp();
							if (update)
							{
								mapdragged = true;
								parsecontract();
								update_points(type);
								curtime = curtime+"Map updated ";appendtimestamp();
								mapdragged = false;
							}
						}
						
					}
					catch(err) 
					{
						alert(err);
					}
				},
				onFailure: function()
				{ 
					alert('Something went AJAXly wrong...') 
				}
			});
	
	}

	
	function parseday()
	{
		switch(day)
		{
			
			case 'alldays': parsedday=-1;break;
			case 'working': parsedday=1; break;
			case 'nonworking': parsedday=0; break;
			default: parsedday=-1; break;
		}	
	}
	function parsetime()
	{
		switch(time)
		{
			case 'alltimes': parsedtime=-1; break;
			case 'p1': parsedtime=0; break;
			case 'p2': parsedtime=1; break;
			case 'p3': parsedtime=2; break;
			default: parsedtime=-1; break;
		}
	}
	
	function parsecontract()
	{
		/*
		if(contract == 'high')
			parsedcontract=0;
		else
			parsedcontract=-1;
		*/
		parsedcontract = contract;
	}
	
	function appendtimestamp()
	{
		var t=new Date()
		curtime = curtime+t.getHours()+":"+t.getMinutes()+":"+t.getSeconds()+":"+t.getMilliseconds()+"\n";
	}
	
	function parse_pin_polyg_vals(jsontext)
	{
		points.length = 0;
		values.length = 0;
		infos.length = 0;
		circles.length = 0;
		if (jsontext != null)
		{
			if (jsontext["points"] != null)
			{
				for (pi=0;pi<jsontext["points"].length;pi++)
					points[pi]=[eval(jsontext["points"][pi][0]),jsontext["points"][pi][1]];
				values = jsontext["values"];
				infos = jsontext["infos"];
				mypointid = jsontext["mypointid"]; 
				update = true;
			}
			if (jsontext["circles"] != null)
			{
				for (pi=0;pi<jsontext["circles"].length;pi++)
				{
					if(pi==0)
					{
						//x = eval(jsontext["circles"][pi][5][0]);
						//alert(x);
						//alert(jsontext["circles"][pi][1]+", "+jsontext["circles"][pi][2]+", "+jsontext["circles"][pi][3]+", "+x[1][0]);
					}
					if (jsontext["circles"][pi].length == 6)
						circles[pi]=[jsontext["circles"][pi][0],jsontext["circles"][pi][1],jsontext["circles"][pi][2],jsontext["circles"][pi][3],jsontext["circles"][pi][4],new Array()];
					else
						circles[pi]=[jsontext["circles"][pi][0],jsontext["circles"][pi][1],jsontext["circles"][pi][2],jsontext["circles"][pi][3],jsontext["circles"][pi][4],new Array(),jsontext["circles"][pi][6]];
					for (ispparameter=0;ispparameter<jsontext["circles"][pi][5].length;ispparameter++)
						circles[pi][5][ispparameter] = eval(jsontext["circles"][pi][5][ispparameter]);
				}
			}
		}
		
	}
	
	
