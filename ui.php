<?php

  function show_header ( $basepath = '' ) {
    global $websitetitle;

    print '
      <html>
        <head>
          <title>'. $websitetitle .'</title>

	  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
          <meta name="HandheldFriendly" content="true" />
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

          <meta http-equiv="refresh" content="60">
          <meta http-equiv="expires" content="'. date(DATE_RFC2822, mktime() + 600) .'">

          <link rel="stylesheet" type="text/css" href="'. $basepath .'style.css">
          <link rel="apple-touch-icon-precomposed" href="'. $basepath .'img/icon.png">
          <link rel="Shortcut icon" type="image/x-icon" href="'. $basepath .'img/icon.png">
          <link rel="apple-touch-icon-precomposed" sizes="114x114" href="'. $basepath .'img/icon.png" />

          <link rel="stylesheet" href="'. $basepath .'include/leaflet.css" />
          <script src="'. $basepath .'include/leaflet.js"></script>
        </head>
      <body>';
  }

  function show_menu ( $basepath = '' ) {
    global $websitetitle;

    print '
      <div class="more"><a href="#more">[ Jump down ]</a></div>
      <div id="menu">
        <a href="http://'. $_SERVER['SERVER_NAME'] . dirname ($_SERVER['SCRIPT_NAME']) . '/' . $basepath .'" id="home" title="'. $websitetitle .'"><img src="'. $basepath .'img/icon.png" /></a>
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
    global $requestfresh, $requeststale;

    print '
      <div class="more"><a id="more" href="#menu">[ Jump up ]</a></div>
      <h3>Lates requests by each device </h3>
      <table id="requests">';

    foreach ($requests as $key => $d) {
      $icon = 'img/' . $d['type'] . '.svg';
      $colorclass = '';

      ### TODO: check these:
      $rdate = strtotime ($d['rdate']);
      if ( mktime()-$requestfresh < $rdate ) # Max 10 minutes old
        $colorclass = 'fresh';
      else if ( mktime()-$requeststale > $rdate ) # Max 60 minutes old
        $colorclass = 'stale';
      else # If above 60 minutes old
        $colorclass = 'dead';

      $battery = 'img/battery86.svg';
      if ($d['charging'])
        $battery = 'img/battery79.svg';
      else if (100/5*4 < intval ($d['battery']))
        $battery = 'img/battery84.svg'; #####
      else if (100/5*3 < intval ($d['battery']))
        $battery = 'img/battery82.svg'; ####
      else if (100/5*2 < intval ($d['battery']))
        $battery = 'img/battery80.svg'; ###
      else if (100/5 < intval ($d['battery']))
        $battery = 'img/battery83.svg'; ##
      else if (5 < intval ($d['battery']))
        $battery = 'img/battery81.svg'; #
      else
        $battery = 'img/battery86.svg'; 

      $speed = '';
      if (0 < intval ($d['speed'])) {
        $speed = '<img title="Speed: '. $d['speed']. ' m/s" src="img/odometer.svg" />';
        if (0 <= intval ($d['bearing']))
          $speed .= ' '. $d['bearing'] .'&deg';
      }

      $provider = '';
      if ('gps' == $d['provider'])
        $provider = '<img title="Provider: ' . $d['provider'] 
          .'" src="img/receiving1.svg" />';
      else if ('network' == $d['provider'])
        $provider = '<img title="Provider: ' . $d['provider'] 
          .'" src="img/antenna1.svg" />';
      else
        $provider = '<small>Provider: '. $d['provider'] .'</small>';

      print '
        <tr>
        <td>
          <a href="'. $_SERVER['PHP_SELF']. '?rid='.
          $d['rid']. '">'.
          date("H:i", $rdate). '</a>
        </td>
        <td>
          <a href="'.
          $_SERVER['PHP_SELF'].
          '?sid='.
          $d['sid'].
          '" class="'. $colorclass .'">
          <img src="'. $icon .'" />'.
          $d['sname'].
          '</a>
        </td>
        <td>
          <img src="'. $battery .'" 
            title="'. $d["battery"] .', charging: '. $d["charging"].'"
          />
        </td>
        <td>
          '. $provider .'
        </td>
        <td>
          '. $speed .'
        </td>
        </tr>';
    }
    print '</table>';
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
        ' | Provider: '.$d['provider'].
        ' | Bearing: '.$d['bearing'].
        ' | Speed: '.$d['speed'].
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
  var zoom           = 17;
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

    $defaultzoom = 16;
    $requestcoordinates = get_coordinates ($requests);
    ?>

    <div id="map"></div>
    <script src="include/leaflet.js"></script>

    <script>

       function onLocationFound(e) {
         var radius = e.accuracy / 2;
         L.marker(e.latlng).addTo(map)
           .bindPopup("You are within " + radius + " meters from this point");
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

      print "var map = L.map('map').setView([". $requestcoordinates[0][0] .
        ",". $requestcoordinates[0][1] ."], ". $defaultzoom .");";
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
        print 'map.locate({setView: false, maxZoom: '. $defaultzoom .'});';
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
