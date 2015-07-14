<?php

/************************************************************
* FILENAME:    alert-sender.php
* DESCRIPTION: This script checkers for phones moves and
*              sends out alert if found.
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
    $phonelist_sql="SELECT DISTINCT phone FROM tracking WHERE datetime >= ( CURDATE() - INTERVAL 1 DAY )";
    $phonelist=mysqli_query($db_conn,$phonelist_sql);

    $mailsubj = 'Phone Tracking Report';

    $changes = false;
    $newphone = '';
    $movedphone = '';

    while($phone=mysqli_fetch_array($phonelist)) {

      // Build SQL query for phone history
      $phonehist_sql="SELECT * FROM tracking WHERE phone='".$phone[0]."' ORDER BY datetime DESC LIMIT 0,1";
      $phonehist=mysqli_query($db_conn,$phonehist_sql);

      $recordone = mysqli_fetch_assoc($phonehist);

      // Build SQL query for phone history
      $phonehist_sql="SELECT * FROM tracking WHERE phone='".$phone[0]."' ORDER BY datetime DESC LIMIT 1,1";
      $phonehist=mysqli_query($db_conn,$phonehist_sql);

      $recordtwo = mysqli_fetch_assoc($phonehist);

      if(($recordone["switch"] !== $recordtwo["switch"]) || ($recordone["switch_int"] !== $recordtwo["switch_int"])) {

        $changes = true;

        // Build SQL query for CM phone info
        $cmphone_sql="SELECT * FROM phones WHERE phone='".$phone[0]."' LIMIT 0,1";
        $cmphone=mysqli_query($db_conn,$cmphone_sql);
        $cmrecord=mysqli_fetch_assoc($cmphone);

        // Build new phone e-mail messages
        if(strlen($recordtwo["switch"].$recordtwo["switch_int"]) == 0) {
          $newphone .= "Phone:\t\t".trim($phone[0],"\n")."\n";
          $newphone .= "Description:\t".trim($cmrecord["description"],"\n")."\n";
          $newphone .= "\tSwitch:\t\t".trim($recordone["switch"],"\n")." (Location: ".trim($recordone["switch_loc"],"\n").")\n";
          $newphone .= "\tInterface:\t".trim($recordone["switch_int"],"\n")."\n";
          $newphone .= "\tDescription:\t".trim($recordone["switch_int_alias"],"\n")."\n";
          $newphone .= "***************************************************************************\n";
        }
        // Build moved phone e-mail messages
        else {
          $movedphone .= "Phone:\t\t".trim($phone[0],"\n")."\n";
          $movedphone .= "Description:\t".trim($cmrecord["description"],"\n")."\n";
          $movedphone .= "Current:\n";
          $movedphone .= "\tSwitch:\t\t".trim($recordone["switch"],"\n")." (Location: ".trim($recordone["switch_loc"],"\n").")\n";
          $movedphone .= "\tInterface:\t".trim($recordone["switch_int"],"\n")."\n";
          $movedphone .= "\tDescription:\t".trim($recordone["switch_int_alias"],"\n")."\n";
          $movedphone .= "Previous:\n";
          $movedphone .= "\tSwitch:\t\t".trim($recordtwo["switch"],"\n")." (Location: ".trim($recordtwo["switch_loc"],"\n").")\n";
          $movedphone .= "\tInterface:\t".trim($recordtwo["switch_int"],"\n")."\n";
          $movedphone .= "\tDescription:\t".trim($recordtwo["switch_int_alias"],"\n")."\n";
          $movedphone .= "***************************************************************************\n";
        }

      }

    }

    $mailbody = '';

    // Enter new phone messages into e-mail body
    if(strlen($newphone) > 0) {
      $mailbody .= "OpenVoPT has detected the following previously not found phones...\r\n\r\n";
      $mailbody .= "***************************************************************************\n";
      $mailbody .= $newphone;
      $mailbody .= "\r\n\r\n";
    }

    // Enter moved phone messages into e-mail body
    if(strlen($movedphone) > 0) {
      $mailbody .= "OpenVoPT has detected the following moved phones...\r\n\r\n";
      $mailbody .= "***************************************************************************\n";
      $mailbody .= $movedphone;
      $mailbody .= "\r\n\r\n";
    }

    // Send e-mail if new phones or moves were found
    if($changes) {
      $smtp = new smtpmail();
      $smtp->smtpconfig($smtp_server,$smtp_port,$smtp_user,$smtp_pass,$smtp_from,$smtp_to,$mailsubj,$mailbody);
      $mail = $smtp->smtpsend();
    }

  }

?>
