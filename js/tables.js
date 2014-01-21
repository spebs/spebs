google.setOnLoadCallback(drawTables);

function drawTables()
{
	drawNDTTable(ndtdata[0]);
	drawGlasnostTable(glasnostdata[0]);
}
	  
function drawNDTTable(data) 
{
	if (data.getNumberOfRows() == 0)
	{	
		document.getElementById('ndt-table').style.display = "none";
		document.getElementById('ndtnotenoughmeasurements').style.display = "block";
		return false;
	}
	
	document.getElementById('ndtnotenoughmeasurements').style.display = "none";
	document.getElementById('ndt-table').style.display = "block";
	
	var datarow;
	var datetimeformatter = new google.visualization.DateFormat({pattern: 'd/M/yyyy - H:mm:ss'});
	
	datetimeformatter.format(data,0);
	var table = new google.visualization.Table(document.getElementById('userdata'));
	var cssClassNames = {
		headerCell:'headerCell ',
		tableRow:'tableRow',
		oddTableRow:'oddTableRow',
		selectedTableRow:'selectedTableRow',
		hoverTableRow:'hoverTableRow'
			};
	table.draw(data, {showRowNumber: true, height:'218', width:'860px', cssClassNames: cssClassNames});
}

function drawGlasnostTable(data) 
{
	if (data.getNumberOfRows() == 0)
	{	
		document.getElementById('glasnost-table').style.display = "none";
		document.getElementById('glasnostnotenoughmeasurements').style.display = "block";
		return false;
	}
	
	document.getElementById('glasnostnotenoughmeasurements').style.display = "none";
	document.getElementById('glasnost-table').style.display = "block";
	
	var datarow;
	var datetimeformatter = new google.visualization.DateFormat({pattern: 'd/M/yyyy - H:mm:ss'});
	
	
	datetimeformatter.format(data,0);
	var table2 = new google.visualization.Table(document.getElementById('userdata2'));
	var cssClassNames = {
		headerCell:'headerCell ',
		tableRow:'tableRow',
		oddTableRow:'oddTableRow',
		selectedTableRow:'selectedTableRow',
		hoverTableRow:'hoverTableRow'
			};
	table2.draw(data, {showRowNumber: true, height:'218', width:'860px', 'allowHtml': true, cssClassNames: 		cssClassNames});
		
}

function update_tables(c)
{
		drawNDTTable(ndtdata[c]);
		drawGlasnostTable(glasnostdata[c]);
}

function change_connection(i)
{
	update_tables(i);
}
