<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<meta http-equiv="refresh" content="600">
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>

function geraGrafico(dt,dt1,dt2,dt3)
{
	
	var table = new google.visualization.Table(document.getElementById('table'));
	table.draw(dt, {allowHtml: true,cssClassNames: {tableCell:'normal1'}});

        var table = new google.visualization.Table(document.getElementById('table1'));
	table.draw(dt3, {allowHtml: true,cssClassNames: {tableCell:'normal1'}});
	
	
	var grouped_table = new google.visualization.Table(document.getElementById('grouped_table'));
	grouped_table.draw(dt1, {cssClassNames: {tableCell:'normal'},allowHtml: true,sortAscending:false,sortColumn:5});
	
	
      	var chart = new google.visualization.ColumnChart(document.getElementById('visualization'));
      	
      	chart.draw(dt2,{ height:200,
      	legend:{position:'right'},animation:{duration: 3000,easing: 'out'},vAxis:{viewWindow:{min:0,max:18000}},backgroundColor:{stroke:"gray",strokeWidth:"1"},chartArea:{left:80,top:15,width:"80%",height:"80%"},reverseCategories:true,series: {6:{color: 'green'},5:{color: 'red'},4:{color: 'brown'},3:{color: 'orange'},2:{color: 'purple'},1:{color: 'navy'},0:{color: 'pink'}}});
      
	chart.draw(dt1,{ height:200,
      	legend:{position:'right'},animation:{duration: 3000,easing: 'out'},vAxis:{viewWindow:{min:0,max:18000}},backgroundColor:{stroke:"gray",strokeWidth:"1"},chartArea:{left:80,top:15,width:"80%",height:"80%"},reverseCategories:true,series: {6:{color: 'green'},5:{color: 'red'},4:{color: 'brown'},3:{color: 'orange'},2:{color: 'purple'},1:{color: 'navy'},0:{color: 'pink'}}});        
}
google.load('visualization', '1', {packages: ['table','corechart']});

</script>


<style>
#conteudo
{
    background-image:url(http://a3.twimg.com/profile_images/461783373/aaanima_normal.jpg); background-position:top right; background-repeat:no-repeat;
}

TD.normal
{
	font-size:13pt;
	text-align:center;
}

TD.normal1
{
	font-size:10pt;
	text-align:center;
}


#mensagem
{
	background-color:green; color:white;
	width:100%;
	height:100px;
	font-size:40px;
	text-align:center;
	vertical-align: middle;
	padding-top:50px;
	margin-top:50px;
}
</style>

</head>
<body>
<div id=conteudo>

<h1> Liberação Graduação <?=$strTitulo." (".$strBaseOracle.")"?></h1>

</div>

</body>
</html>
