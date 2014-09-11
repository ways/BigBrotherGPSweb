<?php

  require_once ('config.php');
  require_once ('database.php');
  require_once ('ui.php');

  $secret = '';

  if ( $testdata or isset ($_GET['testdata']) ) {
    $_POST["latitude"] = 'here';
    $_POST["longitude"] = 'here';
    $_POST["accuracy"] = 'here';
    $_POST["secret"] = 'testname';
  }

  if ( isset ( $_POST['latitude'] )) {
    $lat = clean_input($_POST['latitude']);
    $lon = clean_input($_POST['longitude']);
    $acc = clean_input($_POST['accuracy']);
    $secret = clean_input($_POST['secret']);

    $ip = $_SERVER['REMOTE_ADDR'];

    if ( add_request ($lat, $lon, $acc, $secret, $ip) ) {
      print ("Got your location at ".date('Y-m-d H:i')."!");
    } else {
      print ("Error! Something wrong with setup or data");
    }

    exit (0);
  }

  # If not a request from app, go on:
  if ( isset ($_GET['secret']))
    $secret = clean_input($_GET['secret']);

  include ('html_header.html');

  $requests = list_requests ($secret, $request_count);
  $devices = list_secrets ();

  $php_array = get_coordinates ($requests);

  if ($verbose) {
    print 'Post:<br/>';
    print_r($_POST);
  }

?>

<div id="map" style="width:400px;height:400px;"></div>

<script src="http://openlayers.org/api/OpenLayers.js"></script>
<script>
  map = new OpenLayers.Map("map");
  map.addLayer(new OpenLayers.Layer.OSM());
  var lat            = <?php print $php_array[0][0]; ?>;
  var lon            = <?php print $php_array[0][1]; ?>;
  var zoom           = 18;
  var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
  var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
  var markers = new OpenLayers.Layer.Markers( "Markers" );
  map.addLayer(markers);
  var position;

<?php
  foreach ($php_array as $pos) {
    print "
      position = new OpenLayers.LonLat(".$pos[1].", ".$pos[0].").transform( fromProjection, toProjection);
      markers.addMarker(new OpenLayers.Marker(position));
      ";
  }
?>

    // create layer switcher widget in top right corner of map.
    var layer_switcher= new OpenLayers.Control.LayerSwitcher({});
    map.addControl(layer_switcher);
    //Set start centrepoint and zoom    
    var lonLat = new OpenLayers.LonLat( lon,lat )
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
    var zoom=15;
    map.setCenter (lonLat, zoom);  

  </script>

<?php
    show_requests ($requests);
    show_devices ($devices);
?>

</pre>
</body>
</html>
