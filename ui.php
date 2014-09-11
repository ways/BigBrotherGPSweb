<?php

  function show_devices ($devices) {
    #print_r($devices);

    print '<ul id="devices">';

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

?>
