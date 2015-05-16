<!DOCTYPE html>
<meta charset="utf-8">
<body onload="getLocation()">
  
<script src="assets/d3.v3.min.js"></script>
<script src="assets//topojson.v1.min.js"></script>
<script src="assets/datamaps.world.min.js"></script>

<div id="connections" style="position: relative; width: 96%; min-height: 450px"></div>

<pre><?php
    require_once 'assets/IP2Location.php';
    $loc = new IP2Location('databases/IP2LOCATION-LITE-DB5.BIN');

    $ipList = [];
    $output = '';
    exec('netstat -W', $output);

    $output = preg_grep('/\s+ESTABLISHED/', $output);

    foreach ($output as $line) {
        if (preg_match('/\s+([^:]+):\S+\s+ESTABLISHED/', $line, $matches) === 1) {
            $ipList[$matches[1]] = 1;
        }
        echo $line, "\n";
    }
    
    if (count($ipList) < 1) die("Couldn't get your established connections.")
?></pre>

<script>

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(gotUserCoords);

    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function gotUserCoords(position) {
    var map = new Datamap({
        scope: 'world',
        element: document.getElementById('connections'),
        projection: 'mercator',
        height: 500,
        fills: {
          defaultFill: '#ABDDA4',
          dest: '#9872d1'
        },
        
    });
      
    map.arc([
        <?php
            foreach ($ipList as $ip=>$nil) {
                $record = $loc->lookup($ip, IP2Location::ALL);
                
                if (!is_numeric($record->latitude) || $record->latitude == '' || $record->longitude == '') {
                    unset($ipList[$ip]);
                    continue;
                }

                echo "{ origin: {latitude: position.coords.latitude, longitude: position.coords.longitude}, \n";
                echo '  destination: {latitude: ' . $record->latitude . ', longitude: ' . $record->longitude . "}\n},\n";
                $ipList[$ip] = [
                                 'loc' => $record->cityName . ', ' 
                                          . $record->regionName . ', ' 
                                          . $record->countryCode,
                                 'lat' => $record->latitude,
                                 'lon' => $record->longitude
                               ];
            }
        ?>
    ], {strokeWidth: 1});
       
      
    map.bubbles([
       <?php
           foreach ($ipList as $ip=>$data) {
               echo "{ip: '$ip', ";
               echo "region: '" . $data['loc'] . "', ";
               echo "latitude: " . $data['lat'] . ", ";
               echo "longitude: " . $data['lon'] . ", ";
               echo "radius: 4, fillKey: 'dest'}, \n";
           }
       ?>
    ], {
        popupTemplate: function(geo, data) {
            return "<div class='hoverinfo'>IP: " + data.ip + "<br>" + data.region + "</div>";
        }
    });
}
</script>
</body>
</html>