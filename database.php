<?php

  function clean_input ($in) {
    global $mysqli;

    return mysqli_real_escape_string ( $mysqli, $in );
  }

  function get_secret ($sname) {
    global $mysqli, $verbose;

    $query = 
      'SELECT * 
       FROM secrets
       WHERE sname LIKE "'.$sname.'"
       order by sid ASC
      ';

    if($verbose)
      print 'get_secret'.$query;

    $result = mysqli_query( $mysqli, $query );
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
      if($verbose)
        print_r($row);
      return $row['sid'];
    }

    if ($verbose)
      print 'No hit. Insert and try again.';

    $query =
      'INSERT INTO secrets (sname)
       VALUES ("'.$sname.'")
      ';
    $result = mysqli_query( $mysqli, $query );

    return get_secret($sname);
  }

  function list_secrets ($sid = '', $sname = '') {
    global $mysqli, $verbose;
    $out = array();

    $query =
      'SELECT *
       FROM secrets
      ';

    if ($sname)
      $query .= '
        WHERE sname LIKE "'.$sname.'"
      ';
    else if ($sid)
      $query .= '
       WHERE sid = "'.$sid.'"
      ';

    $query .= '
       order by sname
    ';

    $result = mysqli_query( $mysqli, $query );
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
      $out[ $row['sid'] ] = $row;

      if (1 > strlen($row['sname']))
        $out [ $row['sid'] ]['sname'] = '(No name)';
    }

    return $out;
  }

  function add_request (
    $lat = '',
    $lon = '',
    $acc = '',
    $sname = '',
    $ip,
    $battery = 0,
    $charging = 0,
    $provider = '',
    $bearing = -1,
    $speed = -1,
    $time = '',
    $deviceid = '',
    $subscriberid = ''
  ) {
    global $mysqli, $verbose;

    $sid = get_secret ($sname);

    $query = 
      "
      INSERT INTO
      requests (latitude, longitude, accuracy, sid, rip, battery, charging, provider, bearing, speed, time, deviceid, subscriberid)
      values ('$lat', '$lon', '$acc', '$sid', '$ip', '$battery', '$charging', '$provider', '$bearing', '$speed', '$time', '$deviceid', '$subscriberid')
      ";

    if($verbose)
      print 'get_secret'.$query;

    $result = mysqli_query( $mysqli, $query )
      or die('Err add_request!');

    return true;
  }

  function list_requests ($sid = '', $rid = '') {
    # List the 1 one lates request from each device/secret

    global $mysqli, $verbose, $stale_time;
    $out = array();

    if ($verbose)
      print 'list_requests';

    if ($sid)
      return list_latest_requests ($sid);

    $query = "
      SELECT s.* , r.*
      FROM secrets s
      INNER JOIN requests r 
        ON r.rid = (
          SELECT rid
          FROM requests
          WHERE sid = s.sid
      ";

    if ($rid)
      $query .= "
        AND rid = $rid
      ";

    $query .= "
        AND rdate > FROM_UNIXTIME( ( unix_timestamp( ) - $stale_time ) )
          ORDER BY rid DESC
          LIMIT 1
        )
      ORDER BY rdate DESC
      ";

    if ($verbose)
      print $query;

    $result = mysqli_query( $mysqli, $query ) or die('Err!');
    $i = 0;
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
      $out[$i] = $row;
      $i++;
    }

    return $out;
  }

  function list_latest_requests ($sid = '', $count = 1000) {
    # List the $count latest requests from one device/secret.

    global $mysqli, $verbose;
    $out = array();

    if ($verbose)
      print 'list_latest_requests';

    $query = "
      SELECT r.*, s.*
      FROM requests AS r
      LEFT JOIN secrets AS s
      ON r.sid = s.sid
      ";

    if ($sid)
      $query .= "
        WHERE r.sid = $sid
      ";

    $query .= "
      ORDER BY rdate DESC
      LIMIT $count
    ";

    if ($verbose)
      print $query;

    $result = mysqli_query( $mysqli, $query ) or die('Err!');
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
      $out[ $row['rid'] ] = $row;

      if (1 > strlen($row['sname']))
        $out [ $row['rid'] ]['sname'] = '(No name)';
    }

    return $out;
  }

  function get_coordinates ($requests) {
    # Format requests as coordinates ++ for use in javascript

    $out = array();

    foreach ($requests as $key => $r) {
      #print_r($r);
      $lat = floatval($r['latitude']);
      $lon = floatval($r['longitude']);

      #print 'lat'.$lat.'lon'.$lon;

      if ( ( 0 == $lat ) ||
        (0 == $lon ) )
        continue;
      $out[] = 
        array ( 
          floatval($r['latitude']), 
          floatval($r['longitude']),
          $r['sname'],
          $r['battery'],
          $r['charging'],
          $r['type'],
          $r['rdate'],
          $r['provider'],
          $r['bearing'],
          $r['speed']
        );
    }
    return $out;
  }

  function list_settings ($key) {
    global $mysqli, $verbose;
    $out = array ();

    $query =
      'SELECT *
       FROM settings
      ';

    if($key)
      $query .= '
        WHERE name LIKE "'.$key.'"
      ';

    $result = mysqli_query( $mysqli, $query );
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
      if($verbose)
        print_r($row);
      $out[] = $row;
    }

    return $out;
  }

  function list_users ($uid = 0, $uname = '') {
    global $mysqli, $verbose;
    $out = array ();

    $query =
      'SELECT *
       FROM users
      ';

    if($uname)
      $query .= '
        WHERE uname LIKE "'.$uname.'"
      ';

    $result = mysqli_query( $mysqli, $query );
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
      if($verbose)
        print_r($row);
      if(0 < count( $uname) )
        return $row;
      $out[$row['uid']] = $row;
    }

    return $out;
  }

  # Connect to db mysqli("localhost", "user", "password", "database");
  $mysqli = new mysqli($host, $user, $password, $database);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: 
      (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

?>
