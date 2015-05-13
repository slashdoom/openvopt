<?php

/************************************************************
* FILENAME:    clear_tracking_table.php
* DESCRIPTION: This script clears the OpenVoTP tracking
*              table.
* AUTHOR:      Patrick K. Ryon (Slashdoom)
* LICENSE:     BSD 3-clause (see LICENSE file)
************************************************************/

  // Get program and DB options
  $root = realpath($_SERVER["DOCUMENT_ROOT"]);

  include($root."/include/config.inc.php");

  // Connect to SQL server
  $db_conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

  // Check SQL connection
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: ".mysqli_connect_error()."\n";
    $db_stat = false;
  }
  else {
    echo "Successfully connected to MySQL: ".$db_host."\n";
    $db_stat = true;
  }

  // Execute code ONLY if connections were successful
  if ($db_stat) {

    // Create SQL database
    $delete_tracking = "DELETE FROM tracking";

    // Execute clear tracking table SQL query and check results
    if (mysqli_query($db_conn,$delete_tracking))  {
      echo "Clear tracking -  Successful.\n";
    }
    else {
      echo "Clear tracking - Failed. ".mysqli_error($db_conn)."\n";
    }

  }

?>
