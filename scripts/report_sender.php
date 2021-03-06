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
    $phonelist_sql="SELECT DISTINCT phone,switch,switch_loc,switch_int,switch_int_alias
                    FROM tracking 
                    WHERE datetime >= ( CURDATE() - INTERVAL 3 DAY ) 
                    ORDER BY phone";
    $phonelist=mysqli_query($db_conn,$phonelist_sql);

    $mailsubj = 'Phone Tracking Report';
    $report = '"PHONE","SWITCH","SWITCH LOCATION","SWITCH INTERFACE","SWITCH INTERFACE ALIAS"'."\n";

    while($phone=mysqli_fetch_array($phonelist)) {
      // trim SQL results
      $sphone            = '"'.preg_replace('~[\r\n]+~', '', $phone['phone']).'"';
      $sswitch           = '"'.preg_replace('~[\r\n]+~', '', $phone['switch']).'"';
      $sswitch_loc       = '"'.preg_replace('~[\r\n]+~', '', $phone['switch_loc']).'"';
      $sswitch_int       = '"'.preg_replace('~[\r\n]+~', '', $phone['switch_int']).'"';
      $sswitch_int_alias = '"'.preg_replace('~[\r\n]+~', '', $phone['switch_int_alias']).'"';
      // build report line
      $report .= $sphone.",".$sswitch.",".$sswitch_loc.",".$sswitch_int.",".$sswitch_int_alias."\n";
    }

    $mailbody = '';

    // Create e-mail body
    $mailbody .= "Attached is a report from OpenVoPT.  It shows the current (within the past 3 days) locations of all phones detected on switch ports.\r\n\r\n";

    $mailattach  = "Content-Type: text/csv;"."\r\n";
    $mailattach .= "Content-Transfer-Encoding: base64;"."\r\n";
    $mailattach .= 'Content-Disposition: attachment; filename="phone_report.csv";'."\r\n\r\n";
    $mailattach .= rtrim(chunk_split(base64_encode($report)));

    // Send e-mail 
    $smtp = new smtpmail();
    $smtp->smtpconfig($smtp_server,$smtp_port,$smtp_user,$smtp_pass,$smtp_from,$smtp_to,$mailsubj,$mailbody,$mailattach);
    $mail = $smtp->smtpsend();

  }

?>
