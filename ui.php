<?php

  function show_header ( $basepath = '' ) {
    global $websitetitle;

    print '
      <html>
        <head>
          <title>'. $websitetitle .'</title>

          <meta name="HandheldFriendly" content="true" />
          <meta name="viewport" content="width=480, user-scalable=yes" />

          <meta http-equiv="refresh" content="600">
          <link rel="stylesheet" type="text/css" href="'. $basepath .'style.css">
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
          <meta http-equiv="expires" content="'. date(DATE_RFC2822, mktime() + 600) .'">

          <link rel="apple-touch-icon-precomposed" href="'. $basepath .'img/icon_b.jpeg">
          <link rel="Shortcut icon" type="image/x-icon" href="'. $basepath .'img/icon_b.jpeg">

          <link rel="stylesheet" href="'. $basepath .'include/leaflet.css" />
          <script src="'. $basepath .'include/leaflet.js"></script>

          <script src="http://openlayers.org/en/v3.0.0/build/ol.js" type="text/javascript"></script>
        </head>
      <body>';
  }

  function show_menu ( $basepath = '' ) {
    global $websitetitle;
    print '
      <div class="more"><a href="#more">[ Jump down ]</a></div>
      <div id="menu">
        <img src="'. $basepath .'img/icon_b.jpeg" />
        <a href="http://'. $_SERVER['SERVER_NAME'] . dirname ($_SERVER['SCRIPT_NAME']) . '/' . $basepath .'" id="home">'. $websitetitle .'</a> |
        <a href="http://'. $_SERVER['SERVER_NAME'] . dirname ($_SERVER['SCRIPT_NAME']) . '/' . $basepath .'admin/">Admin</a> |
        <a href="http://'. $_SERVER['SERVER_NAME'] . dirname ($_SERVER['SCRIPT_NAME']) . '/' . $basepath .'about/">About</a>
      </div>
    ';
  }

  function show_devices ($devices) {
    #print_r($devices);

    print '<table id="devicelist">
      <tr><th id="devicehead" colspan="2">Devices</th></tr>
      <tr><th>Name</th><th>Type</td></tr>';

    foreach ($devices as $key => $d) {
      print '<tr><td><a href="'.
        $_SERVER['PHP_SELF'].
        '?sid='.
        $d['sid'].
        '">'.
        $d['sname'].
        '</a></td><td>'.
        $d['type'].
        '</td></tr>';
    }
    print '</table>';
  }

  function show_requests ($requests, $sid ='') {
    print '
      <div class="more"><a id="more" href="#menu">[ Jump up ]</a></div>
      <h3>Lates requests by each device </h3>
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

  function show_map ($dev, $req, $rid) {
    return show_leafletjs ($dev, $req, $rid);
    #return show_osm($dev, $req);
  }

  function show_osm ($devices, $requests) {
    # This function is no longer in use, and can over time be deleted.

    $php_array = get_coordinates ($requests);

    print '
   <div id="map" style="width:460px;height:460px;"></div>

<script src="http://openlayers.org/api/OpenLayers.js"></script>
<script>
  map = new OpenLayers.Map("map");
  map.addLayer(new OpenLayers.Layer.OSM());
  var lat            = '. $php_array[0][0] .';
  var lon            = '. $php_array[0][1] .';
  var zoom           = 13;
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
    map.setCenter (lonLat, zoom);

  </script>

';
  }

  function show_leafletjs ($devices, $requests, $rid = 0) {
    # http://leafletjs.com/download.html

    $requestcoordinates = get_coordinates ($requests);
    ?>

    <div id="map"></div>
    <script src="include/leaflet.js"></script>

    <script>

       function onLocationFound(e) {
         var radius = e.accuracy / 2;
         L.marker(e.latlng).addTo(map)
           .bindPopup("You are within " + radius + " meters from this point").openPopup();
         L.circle(e.latlng, radius).addTo(map);
       }

       function onLocationError(e) {
         /*alert(e.message);*/
       }

    <?php
      #print_r($requestcoordinates);
      /*          floatval($r['latitude']),
          floatval($r['longitude']),
          $r['sname'],
          $r['battery'],
          $r['charging'],
          $r['type'],
          rdate */

      print "var map = L.map('map').setView([". $requestcoordinates[0][0] .",". $requestcoordinates[0][1] ."], 14);";
    ?>
      L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png", {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);

      var LeafIcon = L.Icon.extend({
        options: {
          iconSize:     [50, 50],
          iconAnchor:   [25, 25],
          popupAnchor:  [5, -5]
        }
      });

      var carxIcon = new LeafIcon({iconUrl: 'img/car.svg'}),
        xIcon = new LeafIcon({iconUrl: 'img/unknown.svg'}),
        personxIcon = new LeafIcon({iconUrl: 'img/person.svg'}),
        phonexIcon = new LeafIcon({iconUrl: 'img/phone.svg'}),
        laptopxIcon = new LeafIcon({iconUrl: 'img/laptop.svg'});

    <?php
      foreach ($requestcoordinates as $r) {
        print '
          L.marker(
            ['.$r[0].',
            '.$r[1].'],
            {icon: '. $r[5] .'xIcon}).bindPopup( "'.
            $r[2] .
            ' at '.
            $r[6] .
            ' (batt: '. $r[3] .')"'.
            ').addTo(map);
        ';
      }
    ?>

      map.on('locationfound', onLocationFound);
      map.on('locationerror', onLocationError);

    <?php
      # Disable geolocation if looking for a specific request
      if (0 == $rid ) {
        print 'map.locate({setView: true, maxZoom: 15});';
      }

    ?>

      </script>

    <?php
  }

  function show_settings ($settings) {
    print '<h3>Settings</h3>';
    print_r($settings);
  }

?>
