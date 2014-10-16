<?php
  session_start ();

  require_once ('../config.php');
  require_once ('../database.php');
  require_once ('../ui.php');

  show_header ('../');
  show_menu ('../');

  #Authentication:
  if ( !isset($_SESSION['admin']) ) {
    if ( !isset($_POST['pwd']) ) {
      include ('login.php');
    } else {
      $user = list_users ('admin');
      print_r( $user ); 
      print_r($_POST);
      if ( $user['upassword'] == $_POST['pwd'] ) {
        $_SESSION['admin'] = 'yes';
        print 'Access granted.';
      } else {
        print 'Wrong password!';
      }
    }
  } 

  if ( isset($_SESSION['admin']) ) {
    $devices = list_secrets ();
    $settings = list_settings ();

    show_settings ($settings);
    show_devices ($devices);
  }

  include ('../html_footer.html');
?>