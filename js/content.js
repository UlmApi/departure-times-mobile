function init_app(url){
	updateBus(url);
	
	setTimeout(function() {
		init_app(url);
	}, 10000); // 10 seconds refresh
}

function updateBus(url) {
//	if (document.getElementById("load"))
//		if (document.getElementById("load").style.display == 'none')
//			document.getElementById("load").style.display = 'block';
	$.getJSON(url, function(data) {
		// data arrived => kill the load.gif
		document.getElementById("load").style.display = 'none';
		// Time and Stopname
		document.getElementById("stopname").innerHTML = data.info.stopname;
		document.getElementById("timestamp").innerHTML = data.info.timestamp+ " Uhr";
		i = 0;
		$.each(data.departures, function(key, deps){
			i++;
			// insert realtimeclass 
			if (deps.realtime=='1') {
				classRealtime = "realtime";
				stern = "<img class=\"realtime\" src=\"img/realtime.png\">";
			} else {
				classRealtime = "notrealtime";
				stern = "<img class=\"realtime\" src=\"img/norealtime.png\">";
			}
			// sometimes, lines are canceled:
			if (deps.delay == "-9999") { // line is canceled
				classCanceled = "canceled";
			} else {
				classCanceled = "";
			}
			// html stuff
			text = '<tr id="liste'+i+'"><td class="'+classCanceled+'">' + deps.line + '</td>';
			text = text + '<td class="'+classCanceled+'">' + deps.direction + '</td>';

			
			if (deps.countdown < 20) {
				text = text + '<td class="'+classRealtime+' '+classCanceled+'">' + deps.countdown  + stern+'</td>';
			} else {
				text = text + '<td class="'+classRealtime+' '+classCanceled+'">' + deps.timetable  +  stern+'</td>';
			}
			
			text = text + '</tr>';
			// switch seamless: delete the old, insert the new one
			$('#liste'+i).remove();
			$('#insertPoint').before(text);
			
		});
		// bugfix: if there are fewer elements then before, kill them too
		i++;
		while(true) {
			$('#liste'+i).remove();
			i++;
			if (i==40) break; // TODO: figure i out
		}

		
		
	});
	
}
