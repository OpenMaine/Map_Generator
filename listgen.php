<?php

	//header("Content-Type: text/plain; charset=ISO-8859-1");
	set_time_limit(0);


	// IMPORT LIST
// 	$txt = file_get_contents('maps.csv');
// 	$items = explode("\r", $txt);
// 	$keys = str_getcsv($items[0], ",", '"');
// 	unset($items[0]);
// 	foreach($items as $item){
// 		$row = array_combine( $keys , str_getcsv($item, ",", '"') );
// 		foreach($row as $k => $v){
// 			$text = trim($v);
// 			$row[$k] = $text;
// 		}
		
// 		$csv[] = $row;	
			
// 	}


// //	print_r($csv);

// 	$turfs = array(
// 		"???" => array()
// 	);


// 	foreach($csv as $k => $row){

// 		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . 
// 				urlencode($row['Street#'] . ' ' . $row['Street Name'] . ' Portland ME') .
// 				'&key=%20AIzaSyCZlSd7CYYktdeZIeELO0dmIZfp-Ca5vZA';

// 		$addr_data = json_decode(file_get_contents($url));
		
// 		$location = (array) $addr_data -> results[0] -> geometry -> location;
// 		$row['location'] = $location;


// 		$address_components = $addr_data -> results[0] -> address_components;

// 		$neighborhood = false;
// 		foreach($address_components as $c){
// 			if($c -> types[0] == 'neighborhood'){
// 				$neighborhoodName = $c -> long_name;
// 				break;
// 			}
// 		}

// 		if($neighborhoodName){

// 			$n = &$turfs[$neighborhoodName];

// 			$n['addresses'][] = $row;;

// 			if(!isset($n['minLat'])){
// 				$n['minLat'] = $location['lat'];
// 				$n['maxLat'] = $location['lat'];
// 				$n['minLng'] = $location['lng'];
// 				$n['maxLng'] = $location['lng']; 
// 			}
// 			else {
// 				if($location['lat'] < $n['minLat']) $n['minLat'] = $location['lat'];
// 				if($location['lat'] > $n['maxLat']) $n['maxLat'] = $location['lat'];
// 				if($location['lng'] < $n['minLng']) $n['minLng'] = $location['lng'];
// 				if($location['lng'] > $n['maxLng']) $n['maxLng'] = $location['lng'];
// 			}
// 		}
// 		else {
// 			$turfs["???"]['addresses'][] = $row;
// 		}

// 	}

// 	ksort($turfs);

// 	echo json_encode($turfs, JSON_PRETTY_PRINT);
// 	exit();


	$turfs = json_decode(file_get_contents("list.js"));

	function sort_turf($address1, $address2){
		if($address1['Street'] != $address2['Street']){
			return ($address1['Street'] < $address2['Street']) ? -1 : 1;
		}
		return ($address1['Num'] < $address2['Num']) ? -1 : 1;
	}
?>


<html>
	<head>
		<style>
			td 		{ padding: 10px 30px; font-size: 12px; border: solid 1px #ccc;  }
			.map 	{ height: 600px; width: 800px; margin-bottom: 15px; }
			table 		{ page-break-after: always;      border-collapse: collapse;}
		</style>
		<script type="text/javascript">
			var turfs = [];
			function initMaps() {
        
		       	for(var i = 0; i < turfs.length; i++){

		       		var turf = turfs[i];

		       		var center = {
		       			lat : (turf.minLat + turf.maxLat) / 2,
		       			lng :  (turf.minLng + turf.maxLng) / 2,
		       		};

			        var map = new google.maps.Map(document.getElementById('map_' + i), {
			          zoom: 15,
			          center: center
			        });

			        if(turf.addresses){
			        	for(var addressIndex = 0; addressIndex < turf.addresses.length; addressIndex++){
				      		var point = turf.addresses[addressIndex].location;
							var marker = new google.maps.Marker({
								position: point,
								map: map,
			          			icon : "dot.png"	
							});    	
				        }
			        }
		          
		        }

      		}

		</script>
	</head>

	<body>
		<?php			

			$turfIndex = 0;

			foreach($turfs as $turfName => &$t){

				$turf = (array) $t;

				
				if(!isset($turf['addresses']) || count($turf['addresses']) == 0)  continue;

				// echo $turfName;

				// print_r($turf);

				// exit();


				//usort($turf['addresses'],"sort_turf");

				echo '	<script>turfs[' . $turfIndex . '] = ' . json_encode($turf) . '</script>
						<br /><br /><br />
						<h3>' . $turfName . '</h3>
						<div id="map_' . $turfIndex . '" class="map"></div>';



				echo '<table cellspacing="0">';
				foreach($turf['addresses'] as $address){

					$address = (array) $address;


					$phone = $address['Phone'];
					// if(strlen($phone) != 0){
					// 	$phone = $phone[0] . $phone[1] . $phone[2] . '-' . $phone[3] . $phone[4] . $phone[5] . '-' . $phone[6] . $phone[7] . $phone[8] . $phone[9];
					// }

					echo '	<tr>
								<td>' . $address['Street#'] . '</td>
								<td>' . $address['Street Name'] . '</td>
								<td>' . $address['First Name'] . ' ' . $address['Last Name'] . '</td>
								<td>' . $address['Phone'] . ' ' . $address['Notes'] . '</td>
							</tr>';
				}
				echo '</table>';

				$turfIndex++;

			}
		?>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC2vxa7kFjwQpUh8rG9bAbruvjsrOjl8us&callback=initMaps"></script>
	</body>
</html>
