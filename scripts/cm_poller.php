<?php

/************************************************************
* FILENAME:    cm-poller.php
* DESCRIPTION: This script polls Call Managers for Cisco VoIP
*              phones.
* AUTHOR:
* LICENSE:
************************************************************/

  $root = realpath($_SERVER["DOCUMENT_ROOT"]);

  include($root."/include/config.inc.php");
  include($root."/include/cisco-cm-snmp.inc.php");

  // Connect to SQL server
  $db_conn = mysqli_connect($db_host, $db_rw_user, $db_rw_pass, $db_name);

  // Check SQL connection
  if (mysqli_connect_errno())
  {
    echo "Failed to connect to MySQL: ".mysqli_connect_error()."<br>\n";
  }
  else {
    // Execute clear phones table SQL query and check results
    $delete_phones = "DELETE FROM phones";
    if (mysqli_query($db_conn,$delete_phones)) {
      // Get switch list from SQL
      $create_sql = "SELECT * FROM hosts WHERE type='cm'";
      $switchlist=mysqli_query($db_conn,$create_sql);

      // process each cm
      while($switch=mysqli_fetch_array($switchlist)) {

        $host = new ciscocmsnmp;

        // set SNMP
        if ($host->setSNMP($switch['snmp_ver'],$switch['host'],$switch['snmp_community'],$switch['snmp_name'],$switch['snmp_level'],$switch['snmp_auth_prot'],$switch['snmp_auth_pass'],$switch['snmp_priv_prot'],$switch['snmp_priv_pass'],'10','3')) {

          // process only if cm runs Cisco UCOS
          if ($host->isUCOS()) {

            // process phones list from cm
            foreach($host->getCMCiscoPhones() as $phone) {

              // insert cm phone info into phone table
              $insert_tracking="INSERT INTO phones (phone,extension,username,description,status) VALUES('".$phone["phone"]."','".$phone["extension"]."','".$phone["username"]."','".$phone["description"]."','".$phone["status"]."')";
              mysqli_query($db_conn,$insert_tracking);

            }

          }

        }

      }
    }

  }

?>
