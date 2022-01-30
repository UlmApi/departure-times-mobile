<!DOCTYPE html>
<html>
<head>
<title>Busabfahrtszeiten</title>
<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<meta name='viewport' content='width=device-width' />
<script  src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
<script  src="js/content.js" type="text/javascript"></script>
<script type="text/javascript">
<?php
	$id = ($_GET['id']);
	$platform = (isset($_GET['platform'])?$_GET['platform']:null);
	echo "$(document).ready(init_app('api.php?id=$id&limit=21&platform=$platform'));"
?>
</script>
</head>
<body>

<div class="page">
	<div class="head">
		<table>
			<tr>
				<td id="stopname">Datalove &lt;3</td>
				<td id="timestamp">21:12</td>
			</tr>

		</table>
	</div>
	<div class="content"> 
		
		<table>
		<tr>
			<td class="head">#</td>
			<td class="head">Richtung</td>
			<td class="head">Min<img class="realtime" src="img/space.png"></td>
		</tr>
		<tr id="insertPoint"></tr>
		</table>
		<div id="load"><img src="img/load.gif"></div>
	</div>
	<div class="foot">
		Echtzeitdaten <img class="realtime" src="img/realtime.png"><br>
		Fahrplandaten <img class="realtime" src="img/norealtime.png"><br><br>
		HSG Datalove<br><a href="http://www.ulmapi.de/">www.ulmapi.de</a> :: <a href="mailto:datalove@lists.uni-ulm.de">Kontakt</a> :: <a href="https://github.com/UlmApi/departure-times-mobile">Sourcecode</a> 
<?php
		echo " :: <a href=\"qr.php?id=$id&platform=$platform\">QR-Code</a>";
?>
</div>

<?php


?>

</body>
</html>
