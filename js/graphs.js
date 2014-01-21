
var conns = new Array;

periodselection = 'lyear';
c = 0;
google.setOnLoadCallback(drawCharts);

function drawCharts()
{
	drawUpDownChart(nmupdowndata[0]);
	drawRttChart(nmrttdata[0]);
	drawLossChart(nmlossdata[0]);
	drawJitterChart(nmjitterdata[0]);
}
	  
function drawUpDownChart(data) 
{
	if (data.getNumberOfRows() < 3)
	{	
		document.getElementById('metrics').style.display = "none";
		document.getElementById('graphs').style.display = "none";
		document.getElementById('notenoughmeasurements').style.display = "block";
		return false;
	}
	
	document.getElementById('notenoughmeasurements').style.display = "none";
	document.getElementById('metrics').style.display = "block";
	document.getElementById('graphs').style.display = "block";
	var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('updownchart_div'));
	var assignedOptions = {};
	assignedOptions.displayAnnotations=true;
	var colors = new Array(2)
	colors[0]='darkorange';
	colors[1]='yellowgreen';
	assignedOptions.colors=colors;
	assignedOptions.displayZoomButtons = false;
	assignedOptions.fill = 20;
	assignedOptions.displayRangeSelector = false;
	assignedOptions.displayAnnotations = false;
	assignedOptions.allValuesSuffix = " Mbps";
	assignedOptions.annotationsWidth = 40;
	assignedOptions.legendPosition = 'sameRow';
	assignedOptions.thickness = 2;
	assignedOptions.displayLegendValues = true;
	assignedOptions.dateFormat = 'd MMMM yyyy - H:mm';
	chart.draw(data,assignedOptions);
	var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('updownchart_div'));
	chart.draw(data,assignedOptions);
}

function drawRttChart(data) 
{
	if (data.getNumberOfRows() < 3)
		return false;
	
	var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('rttchart_div'));
	var assignedOptions = {};
	assignedOptions.displayAnnotations=true;
	var colors = new Array(1)
	colors[0]='#0d9562';
	assignedOptions.colors=colors;
	assignedOptions.displayZoomButtons = false;
	assignedOptions.fill = 20;
	assignedOptions.displayRangeSelector = false;
	assignedOptions.displayAnnotations = false;
	assignedOptions.allValuesSuffix = " msec";
	assignedOptions.annotationsWidth = 40;
	assignedOptions.thickness = 2;
	assignedOptions.displayLegendValues = true;
	assignedOptions.legendPosition = 'sameRow';
	assignedOptions.min = 0;
	//assignedOptions.max = 1000;
	assignedOptions.dateFormat = 'd MMMM yyyy - H:mm';
	chart.draw(data,assignedOptions);
	var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('rttchart_div'));
	chart.draw(data,assignedOptions);
}

function drawLossChart(data) 
{
	if (data.getNumberOfRows() < 3)
		return false;
	
	var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('losschart_div'));
	var assignedOptions = {};
	assignedOptions.displayAnnotations=true;
	var colors = new Array(1)
	colors[0]='red';
	assignedOptions.colors=colors;
	assignedOptions.displayZoomButtons = false;
	assignedOptions.fill = 20;
	assignedOptions.displayRangeSelector = false;
	assignedOptions.displayAnnotations = false;
	assignedOptions.allValuesSuffix = "%";
	assignedOptions.min = 0;
	assignedOptions.max = 0.05;
	assignedOptions.annotationsWidth = 40;
	assignedOptions.legendPosition = 'sameRow';
	assignedOptions.thickness = 2;
	assignedOptions.displayLegendValues = true;
	assignedOptions.dateFormat = 'd MMMM yyyy - H:mm';
	chart.draw(data,assignedOptions);
	var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('losschart_div'));
	chart.draw(data,assignedOptions);
}

function drawJitterChart(data) 
{
	if (data.getNumberOfRows() < 3)
		return false;
	
	var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('jitterchart_div'));
	var assignedOptions = {};
	assignedOptions.displayAnnotations=true;
	var colors = new Array(1)
	colors[0]='blue';
	assignedOptions.colors=colors;
	assignedOptions.displayZoomButtons = false;
	assignedOptions.fill = 20;
	assignedOptions.displayRangeSelector = false;
	assignedOptions.displayAnnotations = false;
	assignedOptions.allValuesSuffix = "msec";
	assignedOptions.min = 0;
	//assignedOptions.max = 200;
	assignedOptions.annotationsWidth = 40;
	assignedOptions.legendPosition = 'sameRow';
	assignedOptions.thickness = 2;
	assignedOptions.displayLegendValues = true;
	assignedOptions.dateFormat = 'd MMMM yyyy - H:mm';
	chart.draw(data,assignedOptions);
	var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('jitterchart_div'));
	chart.draw(data,assignedOptions);
}

function update_graphs(newperiodselection, newc)
{
		if (periodselection == newperiodselection && c == newc)
			return false;
		
		if (periodselection != newperiodselection)
		{
			var selectedel = document.getElementById(periodselection);
			var eltoselect = document.getElementById(newperiodselection);
			selectedel.style.backgroundColor = "#ffffff";
			eltoselect.style.backgroundColor = "#f0f0f0";
			periodselection = newperiodselection;
		}
		if(c != newc)
		{
			c = newc;
		}
		
		if (newperiodselection == 'lyear')
		{
			drawUpDownChart(nmupdowndata[c]);
			drawRttChart(nmrttdata[c]);
			drawLossChart(nmlossdata[c]);
			drawJitterChart(nmjitterdata[c]);
		}
		else if (newperiodselection == 'lmeasurements')
		{
			drawUpDownChart(mmupdowndata[c]);
			drawRttChart(mmrttdata[c]);
			drawLossChart(mmlossdata[c]);
			drawJitterChart(mmjitterdata[c]);
		}
		else return false;
		
}

function change_connection(i)
{
	update_graphs(periodselection, i);
}
