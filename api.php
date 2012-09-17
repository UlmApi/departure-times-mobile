<?php
/*api.php 
* Autor: S. Fuchs simon.fuchs@uni-ulm.de
* Holt von www.ding.eu die Abfahrtszeiten der Buslinien  und gibt sie als JSON aus
* Beispielausgabe:
* {
*	  "info" : {
*		  
*     "departures": [
*         {
*             "line": "-1",
*             "direction": "Zeit DING Server",
*             "countdown": "0",
*             "realtime": "0",
*             "timetable": "11:22"
*        },
*         {
*             "line": "5",
*             "direction": "Ludwigsfeld",
*             "countdown": "5",
*             "realtime": "1",
*             "timetable": "11:27"
*         }
*     ]
* }
*  
* Version log:
* 1.4 30.10.2011
*  - quickfix: if ding sends a lot of lines with countdown==0, ignore them
* 1.3 20.10.2011
*  - fix: added sort
* 1.2 30.05.2011
*  - fix: if DING Servers are down, show a custom error message and the lokal server time
* 1.1 01.03.2011
*  - fix in GetFromHost, if host is not reachable
* 1.0 17.02.2011
*  - initial release
*/

// Read GET Params:
$maxBus = ($_GET['limit']);
//$maxBus = 1;
$id = "900".($_GET['id']);
$platform = ($_GET['platform']);
if (!$platform) {
	$platform = false;
} else {
	if (strpos($platform,";")) // ; als trenner
		$platform = explode (";",$platform);	
	else // . als trenner
		$platform = explode (".",$platform);
	
}




// Configuration:
// error message in case ding servers a down:
$errormsg['line'] = "&nbsp;";
$errormsg['direction'] = "DING Server not reachable";
$errormsg['countdown'] = "&nbsp;";
$errormsg['realtime'] = "0";
$errormsg['timetable'] = "&nbsp;";	



// disable all warnings
set_error_handler("my_warning_handler", E_ALL);
function my_warning_handler($errno, $errstr) {
}

// adresse basteln
$departures = array();
$info = array();
$adresse = "http://www.ding.eu/ding2/XML_DM_REQUEST?laguage=de&typeInfo_dm=stopID&nameInfo_dm=".$id."&deleteAssignedStops_dm=1&useRealtime=1&mode=direct";
try {
	// xml holen
	$dingxml = file_get_contents($adresse);
	$xml = new SimpleXMLElement($dingxml);
	//print_r($xml);
	// serverzeit vom ding aus dem XML holen
	$timestampH=$xml->itdDepartureMonitorRequest->itdDateTime->itdTime['hour'];
	$timestampM=$xml->itdDepartureMonitorRequest->itdDateTime->itdTime['minute'];
	if ($timestampM<10) $timestampM="0$timestampM";
	$timestamp = "$timestampH:$timestampM";
	$info["stopid"]=$id;
	$info["stopname"]=(string) $xml->itdDepartureMonitorRequest->itdOdv->itdOdvName->odvNameElem[0];
	$info["stopplace"]=(string) $xml->itdDepartureMonitorRequest->itdOdv->itdOdvPlace->odvPlaceElem[0];
	$info["timestamp"]=$timestamp;
	//$info["platform"]=$platform;
	//anzahl der gefundenen lines, dient als fehlererkennung
	$linecount = 0;
	// alle buslinien durchlaufen und abbrechen wenn genug gesammelt
	for ($i=0;$linecount<$maxBus;$i++) {
		$dep = $xml->itdDepartureMonitorRequest->itdDepartureList->itdDeparture[$i];
		// falls alles durchsucht wurde:
		if (!$dep) break;
		// realtime ankunftszeit schauen ob verfuegbar
		$zeitH = $dep->itdRTDateTime->itdTime['hour'];
		$zeitM = $dep->itdRTDateTime->itdTime['minute'];
		if ($zeitM=="" && $zeitH=="") { // wenn RT zeit nicht verfuegbar
			$zeitH = $dep->itdDateTime->itdTime['hour'];
			$zeitM = $dep->itdDateTime->itdTime['minute'];
		}
		if (trim($zeitM)<10) $zeitM="0$zeitM";		
		if (trim($zeitH)<10) $zeitH="0$zeitH";	
		$timetable = "$zeitH:$zeitM";
		
		
		// rest
		$nummer = $dep->itdServingLine['number'];
		// muss die platform berücksichtigt werden?
		if ($platform) {
			if (!in_array($dep['platform'],$platform)) continue; // falsche platform => raus!
		}
		// fehlersuche: Züge rausparsen, da steht "Gleis" im platFormName
		if ($nummer=="" || strpos((string)$dep['platformName'],"leis")>0 )  {
			continue;
		} else {
			$linecount++;
		}
		$richtung = $dep->itdServingLine['direction'];
		$countdown = $dep['countdown'];

		// quickfix simon fuchs: 
		// ding stellt manchmal alte busse zur verfügung
		// nachteil quickfix: normale busse mit countdown == 0 werden auch ausgeblendet
		if ($countdown == 0) {	
			$maxBus++;			// weil sonst zu wenig anzeigt werden wuerden
			continue;		
		}		
		$realtime = $dep->itdServingLine['realtime'];
		// debug auflistung
		// echo $dep->itdServingLine['number'] ." ". $dep->itdServingLine['direction'] ."<b> ". $dep['countdown'] . "</b>";
		
		// daten der linie eintragen
		$data['line'] = "$nummer";
		$data['direction'] = "$richtung";
		$data['countdown'] = "$countdown";
		$data['realtime'] = "$realtime";
		$data['timetable'] = "$timetable";
		$data['platform'] = (string)$dep['platform'];
		
		array_push($departures, $data);
	}
	// im fehlerfall error msg ausgeben
	if ($linecount==0) {
		$data['line'] = "&nbsp;";
		$data['direction'] = "no Departures found";
		$data['countdown'] = "&nbsp;";
		$data['realtime'] = "0";
		$data['timetable'] = "&nbsp;";
		array_push($departures, $data);
	}

} catch (Exception $e) {
/*	// serverzeit in departures array eintragen
	$data['line'] = "-1";
	$data['direction'] = "Zeit DING Server";
	$data['countdown'] = "-1";
	$data['realtime'] = "0";
	$data['timetable'] = date("H:m");
	array_push($departures, $data);
*/
	// fehlerbehandlung, da ding server oft down sein können
	array_push($departures, $errormsg);
	//echo "<br>" . $e->getMessage();
	

}
// sortieren
usort($departures,"compare");

// ausgabe in JSON
$json['info']=$info;
$json['departures']=$departures;
//print_r($json);
echo json_encode($json);

// comparfunction used by usort
function compare($a,$b) {
	if ($a['countdown'] >=  $b['countdown']) {
		return 1;
	} else {
		return -1;
	}
}



?>
