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
      print ("200 OK at ".date('Y-m-d H:i')." from $secret.");
    } else {
      print ("Error! Something wrong with setup or data");
    }

    exit (0);
  }

  # If not a request from app, go on:

  if ( isset ($_GET['secret']))
    $secret = clean_input($_GET['secret']);

  if ($verbose) {
    print 'Post:<br/>';
    print_r($_POST);
  }

  include ('html_header.html');

  $requests = list_requests ($secret, $request_count);
  $devices = list_secrets ();

  show_map ($devices, $requests);
  show_requests ($requests);
  show_devices ($devices);

  show_log ( list_latest_requests() );

  include ('html_footer.html');
?>
