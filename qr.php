<!DOCTYPE html>
<html>
<head>
<title>Busabfahrtszeiten</title>

<style>

@import url(http://fonts.googleapis.com/css?family=Ubuntu:400,700);

body {
	font-family: 'Ubuntu', sans-serif;
	font-weight: 400;
	color:#000
}

div.qr {
	text-align:center;
	font-size: 33pt;
}

table {
	margin: 0 auto;
	}

div.credits td {
	width:150px;
	text-align:center;
	padding: 10px;
}


</style>

</head>
<body>


<div class="qr">

Live-Busabfahrtszeiten<br>für diese Haltestelle<br>

<?php
$url=$_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
if(isset($_GET['id'])) {
	$url .= "/".$_GET['id'];
	}
if(isset($_GET['platform']) && $_GET['platform'] != "") {
	$url .= "/".$_GET['platform'];
	}

echo "<img src=\"https://chart.googleapis.com/chart?chs=480x480&cht=qr&chl=http://$url\"><br>$url";

?>
<br>
<br>
</div>

<div class="credits">
<table>
<tr>
<td><img src="img/fs-et.png" width="100px"></td>
<td><img src="http://www.ulmapi.de/images/ulmapi.png" width="100px"></td>
<td><span style="font-size:60pt;">[<3]</span></td>

</tr>
<tr>
<td>www.fs-et.de</td>
<td>www.ulmapi.de</td>
<td>DataLove</td>

</tr>
</div>

</body>
</html>
