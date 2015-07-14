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

          // process phones list from switch
          foreach($host->getCDPCiscoPhones() as $phone) {

            // insert phone info into tracking table
            $insert_tracking="INSERT INTO tracking (phone,switch,switch_loc,switch_int,switch_int_alias) VALUES('".$phone["phone"]."','".$phone["switch"]."','".$phone["switch_loc"]."','".$phone["switch_int"]."','".$phone["switch_int_alias"]."')";
            mysqli_query($db_conn,$insert_tracking);

          }

          // set status as successful for switch
          $update_status="UPDATE hosts SET status='1' WHERE host='".$phone["switch"]."')";
          mysqli_query($db_conn,$update_status);

        }
        else {
          // set status as unsuccessful for switch
          $update_status="UPDATE hosts SET status='0' WHERE host='".$phone["switch"]."')";
          mysqli_query($db_conn,$update_status);
        }

      }

    }

  }

?>
