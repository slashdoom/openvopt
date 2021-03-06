<?php

/************************************************************
* FILENAME:    switch-poller.php
* DESCRIPTION: This script polls switches for Cisco VoIP
*              phones.
* AUTHOR:      Patrick K. Ryon (Slashdoom)
* LICENSE:     BSD 3-clause (see LICENSE file)
************************************************************/

  $root = realpath($_SERVER["DOCUMENT_ROOT"]);

  include($root."/include/config.inc.php");
  include($root."/include/cisco-switch-snmp.inc.php");

  // Connect to SQL server
  $db_conn = mysqli_connect($db_host, $db_rw_user, $db_rw_pass, $db_name);

  // Check SQL connection
  if (mysqli_connect_errno())
  {
    echo "Failed to connect to MySQL: ".mysqli_connect_error()."<br>\n";
  }
  else {
    // Get switch list from SQL
    $create_sql = "SELECT * FROM hosts WHERE type='sw'";
    $switchlist=mysqli_query($db_conn,$create_sql);

    // process each switch
    while($switch=mysqli_fetch_array($switchlist)) {

      $host = new ciscoswitchsnmp;

      // set SNMP
      if ($host->setSNMP($switch['snmp_ver'],$switch['host'],$switch['snmp_community'],$switch['snmp_name'],$switch['snmp_level'],$switch['snmp_auth_prot'],$switch['snmp_auth_pass'],$switch['snmp_priv_prot'],$switch['snmp_priv_pass'],'10','3')) {

        // process only if switch is Cisco
        if ($host->isCisco()) {

          // set status as successful for switch
          $update_status="UPDATE hosts SET status='1' WHERE host='".$switch['host']."'";
          mysqli_query($db_conn,$update_status);

          // process phones list from switch
          foreach($host->getCDPCiscoPhones() as $phone) {

            // trim SQL results
            $sphone            = preg_replace('~[\r\n]+~', '', $phone['phone']);
            $sswitch           = preg_replace('~[\r\n]+~', '', $phone['switch']);
            $sswitch_loc       = preg_replace('~[\r\n]+~', '', $phone['switch_loc']);
            $sswitch_int       = preg_replace('~[\r\n]+~', '', $phone['switch_int']);
            $sswitch_int_alias = preg_replace('~[\r\n]+~', '', $phone['switch_int_alias']);
            $sswitch_int_alias = preg_replace("~[\']~", "''", $sswitch_int_alias);
            // insert phone info into tracking table
            $insert_tracking="INSERT INTO tracking (phone,switch,switch_loc,switch_int,switch_int_alias) 
                              VALUES('".$sphone."','"
                                       .$sswitch."','"
                                       .$sswitch_loc."','"
                                       .$sswitch_int."','"
                                       .$sswitch_int_alias."')";
            mysqli_query($db_conn,$insert_tracking);

          }

        }
        else {
          // set status as unsuccessful for switch
          $update_status="UPDATE hosts SET status='0' WHERE host='".$switch['host']."'";
          mysqli_query($db_conn,$update_status);
        }

      }

    }

  }

?>
