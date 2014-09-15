<?php

  function show_devices ($devices) {
    #print_r($devices);

    print '<h3>All devices</h3> <ul id="devices">';

    foreach ($devices as $key => $d) {
      print '<li><a href="'.
        $_SERVER['PHP_SELF'].
        '?sid='.
        $d['sid'].
        '">'.
        $d['sname'].
        '</a></li>';
    }
    print '</ul>';
  }

  function show_requests ($requests, $sid ='') {
    print '<h3>Lates requests by each device</h3>
      <ul id="requests">';

    foreach ($requests as $key => $d) {
      print '<li><a href="'.
        $_SERVER['PHP_SELF'].
        '?rid='.
        $d['rid'].
        '">'.
        $d['rdate'].
        '</a> '.
        $d['sname'].
        ' | Battery: '.$d['battery'].
        ' | Charging: '.$d['charging'].
        '</li>';
    }
    print '</ul>';
  }

  function show_log ($log) {
    print '<h3>Ten most recent check-ins</h3> <ul id="log">';
    foreach ($log as $key => $d) {
      print '
        <li><a href="'.
        $_SERVER['PHP_SELF'].
        '?rid='.
        $d['rid'].
        '">'.
        $d['rdate'].
        '</a>
        <a href="'.
        $_SERVER['PHP_SELF'].
        '?sid='.
        $d['sid'].
        '">'.
        $d['sname'].
        '</a>'.
        ' | Battery: '.$d['battery'].
        ' | Charging: '.$d['charging'].
        '</li>';

    }
    print '</ul>';
  }

  function show_map ($dev, $req) {
    return show_osm($dev, $req);
  }

  function show_osm ($devices, $requests) {
    $php_array = get_coordinates ($requests);

    print '
   <div id="map" style="width:400px;height:400px;"></div>

<script src="http://openlayers.org/api/OpenLayers.js"></script>
<script>
  map = new OpenLayers.Map("map");
  map.addLayer(new OpenLayers.Layer.OSM());
  var lat            = '. $php_array[0][0] .';
  var lon            = '. $php_array[0][1] .';
  var zoom           = 18;
  var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
  var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
  var markers = new OpenLayers.Layer.Markers( "Markers" );
  map.addLayer(markers);
  var position; ';

  foreach ($php_array as $pos) {
    print "
      position = new OpenLayers.LonLat(".$pos[1].", ".$pos[0].").transform( fromProjection, toProjection);
      markers.addMarker(new OpenLayers.Marker(position));
      ";
  }

  print '
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

';

  }

?>
