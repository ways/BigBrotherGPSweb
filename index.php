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
    $ip = $_SERVER['REMOTE_ADDR'];
    $lat = clean_input($_POST['latitude']);
    $lon = clean_input($_POST['longitude']);
    $secret = clean_input($_POST['secret']);
    @$acc = clean_input($_POST['accuracy']);
    @$battery = clean_input($_POST['battlevel']);
    @$charging = clean_input($_POST['charging']);
    @$provider = clean_input($_POST["provider"]);
    @$bearing = clean_input($_POST["bearing"]);
    @$speed = clean_input($_POST["speed"]);
    @$time = clean_input($_POST["time"]);
    @$deviceid = clean_input($_POST["deviceid"]);
    @$subscriberid = clean_input($_POST["subscriberid"]);

  } else if ( isset ( $_GET['latitude'] )) { 
    $ip = $_SERVER['REMOTE_ADDR'];
    $lat = clean_input($_GET['latitude']); 
    $lon = clean_input($_GET['longitude']);
    $secret = clean_input($_GET['secret']); 
    @$acc = clean_input($_GET['accuracy']);
    @$battery = clean_input($_GET['battlevel']); 
    @$charging = clean_input($_GET['charging']);
    @$provider = clean_input($_GET["provider"]); 
    @$bearing = clean_input($_GET["bearing"]);
    @$speed = clean_input($_GET["speed"]); 
    @$time = clean_input($_GET["time"]);
    @$deviceid = clean_input($_GET["deviceid"]); 
    @$subscriberid = clean_input($_GET["subscriberid"]);
  }

  if ( $lat ) { # If we've got a request:
    openlog('bigbrothergpsweb', LOG_NDELAY, LOG_USER);
    $msg = "Error! Something wrong with setup or data: " . $secret;

    if ( # If adding request went OK
      add_request (
        $lat, $lon, $acc, $secret, $ip, $battery, $charging,
        $provider, $bearing, $speed, $time, $deviceid, $subscriberid
      ) ) {
      $msg = "200 OK. Logged request at ".date('Y-m-d H:i')." from $secret.";
      print $msg;
      if ($syslog)
        syslog(LOG_NOTICE, $msg);

    } else { # If adding request failed.
      print $msg;
      syslog(LOG_NOTICE, $msg);
    }
    exit (0);
  }

  # If not a request from app, go on:

  $sid = 0; # Selecting one device
  if ( isset ($_GET['sid']))
    $sid = clean_input($_GET['sid']);

  $rid = 0; # Selecting one request
  if ( isset ($_GET['rid']))
    $rid = clean_input($_GET['rid']);

  if ($verbose) {
    print 'Post:<br/>';
    print_r($_POST);
  }

  show_header ();
  show_menu ();

  $requests = list_requests ($sid, $rid);
  $devices = list_secrets ();

  show_map ($devices, $requests, $rid);
  show_requests ($requests);
  show_devices ($devices);

  show_log ( list_latest_requests() );

  include ('html_footer.html');
?>
