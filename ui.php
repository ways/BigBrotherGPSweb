<?php

  function show_header ( $basepath = '' ) {
    print '
      <html>
        <head>
          <title>BigBrother</title>

          <meta name="HandheldFriendly" content="true" />
          <meta name="viewport" content="width=480, user-scalable=yes" />
          <meta http-equiv="refresh" content="600">
          <link rel="stylesheet" type="text/css" href="'. $basepath .'style.css">
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
          <meta http-equiv="expires" content="'. date(DATE_RFC2822, mktime() + 600) .'">

          <link rel="apple-touch-icon-precomposed" href="'. $basepath .'img/icon_b.jpeg">
          <link rel="Shortcut icon" type="image/x-icon" href="'. $basepath .'img/icon_b.jpeg">

          <script src="http://openlayers.org/en/v3.0.0/build/ol.js" type="text/javascript"></script>
        </head>
      <body onload="init()">';
  }

  function show_menu ( $basepath = '' ) {
    print '
      <div id="menu">
        <img src="'. $basepath .'img/icon_b.jpeg" />
        <a href="http://'. $_SERVER['SERVER_NAME'] . dirname ($_SERVER['SCRIPT_NAME']) . '/' . $basepath .'" id="home">BigBrotherGPS Map</a> |
        <a href="http://'. $_SERVER['SERVER_NAME'] . dirname ($_SERVER['SCRIPT_NAME']) . '/' . $basepath .'admin/">Admin</a> |
        <a href="http://'. $_SERVER['SERVER_NAME'] . dirname ($_SERVER['SCRIPT_NAME']) . '/' . $basepath .'about/">About</a>
      </div>';
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

  function show_map ($dev, $req, $type = '') {
    if ('ol' == $type)
      return show_ol($dev, $req);
    else
      return show_osm($dev, $req);
  }

  function show_osm ($devices, $requests) {
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

  function show_ol ($devices, $requests) {
    # http://openlayers.org/en/v3.0.0/examples/icon.html

    print '<div id="map" style="width:460px;height:460px;"></div>
  <script>

function init() {
    map = new OpenLayers.Map("map");
    var mapnik         = new OpenLayers.Layer.Stamen("toner-background");
    var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
    var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
    var position       = new OpenLayers.LonLat(30,55.515).transform( fromProjection, toProjection);
    var zoom           = 4; 

    map.addLayer(mapnik);
    map.setCenter(position, zoom );


var markers = new OpenLayers.Layer.Markers( "Markers" );
map.addLayer(markers);

var icon1 = new OpenLayers.Icon("http://www.openlayers.org/dev/img/marker.png", size, offset);
var icon2 = new OpenLayers.Icon("http://www.openlayers.org/dev/img/marker-gold.png", size, offset);
var icon3 = new OpenLayers.Icon("http://www.openlayers.org/dev/img/marker-green.png", size, offset);

        var lonLat1 = new OpenLayers.LonLat(0.1062,51.5171).transform(
                        new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
                        map.getProjectionObject() // to Spherical Mercator Projection
                        );


        var lonLat2 = new OpenLayers.LonLat(2.3470,48.8742).transform(
                        new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
                        map.getProjectionObject() // to Spherical Mercator Projection
                        );

                        var lonLat3 = new OpenLayers.LonLat(7.2692,43.7028).transform(
                        new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
                        map.getProjectionObject() // to Spherical Mercator Projection
                        );

var marker1 = new OpenLayers.Marker(lonLat1, icon1);
    var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    marker1.icon.size = size;
marker1.icon.offset = offset;

var feature = new OpenLayers.Feature(markers, lonLat1);
feature.closeBox = true;
feature.popupClass = OpenLayers.Class(OpenLayers.Popup.AnchoredBubble, { autoSize: true });
feature.data.popupContentHTML = "<p>Marker1<p>";
feature.data.overflow = "hidden";

marker1.feature = feature;


var markerClick = function (evt) {
    if (this.popup == null) {
        this.popup = this.createPopup(this.closeBox);
        map.addPopup(this.popup);
        this.popup.show();
    } else {
        this.popup.toggle();
    }
    OpenLayers.Event.stop(evt);
};
marker1.events.register("mousedown", feature, markerClick);

var marker2 = new OpenLayers.Marker(lonLat2);
        var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);



var feature = new OpenLayers.Feature(markers, lonLat2);

feature.closeBox = true;
feature.popupClass = OpenLayers.Class(OpenLayers.Popup.AnchoredBubble, { autoSize: true });
feature.data.popupContentHTML = "<p>Marker2<p>";
feature.data.overflow = "hidden";
marker2.feature = feature;

var markerClick = function (evt) {
    if (this.popup == null) {
        this.popup = this.createPopup(this.closeBox);
        map.addPopup(this.popup);
        this.popup.show();
    } else {
        this.popup.toggle();
    }
    OpenLayers.Event.stop(evt);
};
marker2.events.register("mousedown", feature, markerClick);



var marker3 = new OpenLayers.Marker(lonLat3);
        var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    marker3.icon.size = size;
marker3.icon.offset = offset;
var feature = new OpenLayers.Feature(markers, lonLat3);
feature.closeBox = true;
feature.popupClass = OpenLayers.Class(OpenLayers.Popup.AnchoredBubble, { autoSize: true });
feature.data.popupContentHTML = "<p>Marker 3<p>";
feature.data.overflow = "hidden";

marker3.feature = feature;

var markerClick = function (evt) {
    if (this.popup == null) {
        this.popup = this.createPopup(this.closeBox);
        map.addPopup(this.popup);
        this.popup.show();
    } else {
        this.popup.toggle();
    }
    OpenLayers.Event.stop(evt);
};
marker3.events.register("mousedown", feature, markerClick);

markers.addMarker(marker1);

markers.addMarker(marker2);

markers.addMarker(marker3);

  }
</script>

    ';

  }

  function show_settings ($settings) {
    print '<h3>Settings</h3>';
    print_r($settings);
  }

?>
