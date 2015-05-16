<!DOCTYPE html>
<meta charset="utf-8">
<style type="text/css">
    h1 { font-size: 1.2em }
    footer { font-size: 0.72em }
    #connections {
        position: relative; 
        width: 96%; 
        min-height: 520px
    }
</style>
<body onload="getLocation()">

<?php 
    require_once 'assets/IP2Location.php';
    $loc = new IP2Location('databases/IP2LOCATION-LITE-DB5.BIN');

    $ipList = [];
?>  

<script src="assets/d3.v3.min.js" charset="utf-8"></script>
<script src="assets/topojson.v1.min.js"></script>
<script src="assets/datamaps.world.min.js"></script>

<div id="connections"></div>

<?php
    
    $output = '';
    $heading = '<h1>Visual NetStat, Active Connections</h1>';
    $regex_filer = '/\s+ESTABLISHED/'; 
    $regex_match = '/\s+([^:]+):\S+\s+ESTABLISHED/'; 

    if (stripos(PHP_OS, 'Win')) $cmd = 'netstat -f';
    else { //netstat addon for *nix systems
        $cmd = 'netstat -W';
        if (isset($_GET['trace']) && $_GET['trace'] != '') {
            
            $trap = preg_replace('/[^a-z0-9.-]/i', '', $_GET['trace']);
            if ($trap !== $_GET['trace']) die('Invalid.');

            $heading = '<h1>Visual TraceRoute, ' . $_GET['trace'] . '</h1>';
            $cmd = 'mtr -r -w -c 1 ' . $_GET['trace'] . '';
            $regex_filer = '/\|--/'; 
            $regex_match = '/\|--\s(\S+)\s/'; 
        }
    }

    echo $heading, '<pre>';

    exec($cmd, $output);

    $output = preg_grep($regex_filer, $output);

    foreach ($output as $line) {
        if (preg_match($regex_match, $line, $matches) === 1) {
            $ipList[$matches[1]] = 1;
        }
        if (stripos(PHP_OS, 'Win')) echo $line;
        else echo str_replace($matches[1], '<a href="?trace=' . $matches[1] . '">' . $matches[1] . '</a>', $line);
        echo "\n";
    }
    
    if (count($ipList) < 1) die("Could not read any connection status.")
?>
</pre>

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
        height: 580,
        fills: {
          defaultFill: '#ABDDA4', 
          dest: '#9872d1', 
          start: '#d3b15f'
        },
        
    });
      
    map.arc([
        <?php
            $lastPos = ['position.coords.latitude', 'position.coords.longitude'];
            foreach ($ipList as $ip=>$nil) {
                $record = $loc->lookup($ip, IP2Location::ALL);
                
                if (!is_numeric($record->latitude) || $record->latitude == '' || $record->longitude == '') {
                    unset($ipList[$ip]);
                    continue;
                }

                if (isset($_GET['trace']) && $_GET['trace'] != '') {
                    echo "{ origin: {latitude: " . $lastPos[0] . ", longitude: " . $lastPos[1] . "}, \n";
                } else {
                    echo "{ origin: {latitude: position.coords.latitude, longitude: position.coords.longitude}, \n";
                }
                
                echo '  destination: {latitude: ' . $record->latitude . ', longitude: ' . $record->longitude . "}\n},\n";
                $ipList[$ip] = [
                                 'loc' => $record->cityName . ', ' 
                                          . $record->regionName . ', ' 
                                          . $record->countryCode,
                                 'lat' => $record->latitude,
                                 'lon' => $record->longitude
                               ];
                $lastPos = [$record->latitude, $record->longitude];
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
           echo "{ip: '" . $_SERVER['SERVER_NAME'] . "', ";
           echo "region: 'YOUR LOCATION', ";
           echo "latitude: position.coords.latitude, ";
           echo "longitude: position.coords.longitude, ";
           echo "radius: 3, fillKey: 'start'}";
       ?>
    ], {
        popupTemplate: function(geo, data) {
            return "<div class='hoverinfo'>IP: " + data.ip + "<br>" + data.region + "</div>";
        }
    });
}
</script>

<footer>
    <hr>
    <p>This site or product includes IP2Location LITE data available from <a href="http://lite.ip2location.com">http://lite.ip2location.com</a></p>
    <p>Many thanks to <a href="http://d3js.org/">D3js</a> and <a href="http://datamaps.github.io/">DataMaps</a> for releasing the open source code that powers this nice app!</p>
</footer>

</body>
</html>