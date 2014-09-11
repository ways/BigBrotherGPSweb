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

  function show_requests ($requests) {
    #print_r($requests);

    print '<h3>Lates requests</h3> <ul id="requests">';

    foreach ($requests as $key => $d) {
      print '<li><a href="'.
        $_SERVER['PHP_SELF'].
        '?rid='.
        $d['rid'].
        '">'.
        $d['rdate'].
        '</a> '.
        $d['sname'].
        '</li>';
    }
    print '</ul>';
  }

?>
