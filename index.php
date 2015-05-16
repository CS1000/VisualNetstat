<!DOCTYPE html>
<meta charset="utf-8">
<body onload="getLocation()">
  
  <script src="assets/d3.v3.min.js"></script>
  <script src="assets//topojson.v1.min.js"></script>
  <script src="assets/datamaps.world.min.js"></script>

<div id="connections" style="position: relative; width: 99%"></div>

<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(userCoords);

    } else {
        alert("Geolocation is not supported by this browser.");
    }
}
function userCoords(position) {
    var map = new Datamap({
        scope: 'world',
        element: document.getElementById('connections'),
        projection: 'mercator',
        height: 500,
        fills: {
          defaultFill: '#ABDDA4',
          dest: '#9872d1'
        },
        
      })
      
      map.arc([
       {
        origin: {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude
        },
        destination: {
            latitude: 37.618889,
            longitude: -122.375
        }
      },
      {
          origin: {
              latitude: position.coords.latitude,
              longitude: position.coords.longitude
          },
          destination: {
              latitude: 25.793333,
              longitude: -0.290556
          }
      }
      ], {strokeWidth: 1});
       
      
       //bubbles, custom popup on hover template
     map.bubbles([
       {name: 'SO', latitude: 21.32, longitude: 5.32, radius: 4, fillKey: 'dest'},
       {name: 'YHOO', latitude: -25.32, longitude: 120.32, radius: 4, fillKey: 'dest'},
       {name: 'GMAI', latitude: 21.32, longitude: -84.32, radius: 4, fillKey: 'dest'},

     ], {
       popupTemplate: function(geo, data) {
         return "<div class='hoverinfo'>It is " + data.name + "</div>";
       }
     });
}
</script>

<script>
//basic map config with custom fills, mercator projection




</script>


<?php
require_once 'assets/IP2Location.php';
$loc = new IP2Location('databases/IP2LOCATION-LITE-DB5.BIN');

$ipList = [];

$f = file('netstat.log');
$f = preg_grep('/\s+ESTABLISHED/', $f);

foreach ($f as $line) {
    if (preg_match('/\s+([^:]+):\S+\s+ESTABLISHED/', $line, $matches) === 1) {
        $ipList[$matches[1]] = 1;
    }
}
unset($ipList['localhost']);

foreach ($ipList as $ip=>$nil) {
    $record = $loc->lookup($ip, IP2Location::ALL);
    /*
echo 'Latitude: ' . $record->latitude . '<br />';
echo 'Longitude: ' . $record->longitude . '<br />';
    */

    foreach ($record as $bit=>$val) {
        if ($bit === 'isp') break;
        echo $bit, ": \t", $val, '<br>';
    }
    print '<br>';
}

?>

</body>
</html>