<?php

/************************************************************
* FILENAME:    report_sender.php
* DESCRIPTION: This script send out a report of current phone
               locations.
* AUTHOR:      Patrick K. Ryon (Slashdoom)
* LICENSE:     BSD 3-clause (see LICENSE file)
************************************************************/

  $root = realpath($_SERVER["DOCUMENT_ROOT"]);

  include($root."/include/config.inc.php");
  include($root."/include/smtp-mail.inc.php");

  // Connect to SQL server
  $db_conn = mysqli_connect($db_host, $db_rw_user, $db_rw_pass, $db_name);

  // Check SQL connection
  if (mysqli_connect_errno())
  {
    echo "Failed to connect to MySQL: ".mysqli_connect_error()."<br>\n";
  }
  else {

    // Build SQL query for tracking phone list
    $phonelist_sql="SELECT DISTINCT phone,switch_loc,switch_int,switch_int_alias
                    FROM tracking 
                    WHERE datetime >= ( CURDATE() - INTERVAL 3 DAY ) 
                    ORDER BY phone";
    $phonelist=mysqli_query($db_conn,$phonelist_sql);

    $mailsubj = 'Phone Tracking Report';
    $report = 'PHONE,SWITCH_LOCATION,SWITCH_INTERFACE,SWITCH_INTERFACE_ALIAS';

    while($phone=mysqli_fetch_array($phonelist)) {
      // tirm SQL results
      $sphone            = preg_replace('~[\r\n]+~', '', $phone['phone']);
      $sswitch_loc       = preg_replace('~[\r\n]+~', '', $phone['switch_loc']);
      $sswitch_int       = preg_replace('~[\r\n]+~', '', $phone['switch_int']);
      $sswitch_int_alias = preg_replace('~[\r\n]+~', '', $phone['switch_int_alias']);
      // build report line
      $report .= $sphone.",".$sswitch_loc.",".$sswitch_int.",".$sswitch_int_alias."\n";
    }

    $random_hash = md5(date('r', time()));
    $mailbody = '';

    // Create e-mail body
    $mailbody .= "Attached is a report from OpenVoPT.  It shows the current (within the past 3 days) locations of all phones detected on switch ports.\r\n\r\n";

    $mailbody .= $newphone;
    $mailbody .= "\r\n\r\n";

    // Enter moved phone messages into e-mail body
    //if(strlen($movedphone) > 0) {
    //  $mailbody .= "OpenVoPT has detected the following moved phones...\r\n\r\n";
    //  $mailbody .= "***************************************************************************\n";
    //  $mailbody .= $movedphone;
    //  $mailbody .= "\r\n\r\n";
    //}

    // Enter devices with status down into e-mail body
    //if(strlen($devicesdown) > 0) {
    //  $mailbody .= "OpenVoPT was not able to poll the following devices...\r\n\r\n";
    //  $mailbody .= "***************************************************************************\n";
    //  $mailbody .= $devicesdown."\n";
    //  $mailbody .= "***************************************************************************\n";
    //  $mailbody .= "\r\n\r\n";
    //}

    // Send e-mail if new phones or moves were found
    //if($changes) {
    //  $smtp = new smtpmail();
    //  $smtp->smtpconfig($smtp_server,$smtp_port,$smtp_user,$smtp_pass,$smtp_from,$smtp_to,$mailsubj,$mailbody);
    //  $mail = $smtp->smtpsend();
    //}

  }

?>
