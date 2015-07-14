<?php

/************************************************************
* FILENAME:    create_db.php
* DESCRIPTION: This script creates the OpenVoPT DB.
* AUTHOR:      Patrick K. Ryon (Slashdoom)
* LICENSE:     BSD 3-clause (see LICENSE file)
************************************************************/

  // Get program and DB options
  $root = realpath($_SERVER["DOCUMENT_ROOT"]);

  include($root."/include/config.inc.php");

  // Connect to SQL server
  $db_conn = mysqli_connect($db_host, $db_rw_user, $db_rw_pass);

  // Check SQL connection
  if (mysqli_connect_errno())
  {
    echo "Failed to connect to MySQL: ".mysqli_connect_error()."<br>\n";
    $db_stat = false;
  }
  else {
    echo "Successfully connected to MySQL: ".$db_host."<br>\n";
    $db_stat = true;
  }

  // Execute code ONLY if connections were successful
  if ($db_stat) {

    // Create SQL database
    $create_sql = "CREATE DATABASE ".$db_name;

    // Check SQL database
    if (mysqli_query($db_conn,$create_sql))  {
      echo "CREATE DATABASE ".$db_name." -  Successful.<br>\n";

      // Build SQL query to create table hosts
      $create_table_hosts = "CREATE TABLE ".$db_name.".hosts ( ";
      $create_table_hosts .= "id INT NOT NULL AUTO_INCREMENT , PRIMARY KEY(id) , ";
      $create_table_hosts .= "host VARCHAR( 50 ) NOT NULL , ";
      $create_table_hosts .= "type VARCHAR( 2 ) NOT NULL , ";
      $create_table_hosts .= "snmp_ver VARCHAR( 2 ) NOT NULL , ";
      $create_table_hosts .= "snmp_community VARCHAR( 50 ) , ";
      $create_table_hosts .= "snmp_name VARCHAR( 50 ) , ";
      $create_table_hosts .= "snmp_level VARCHAR( 10 ) , ";
      $create_table_hosts .= "snmp_auth_prot VARCHAR( 3 ) , ";
      $create_table_hosts .= "snmp_auth_pass VARCHAR( 50 ) , ";
      $create_table_hosts .= "snmp_priv_prot VARCHAR( 3 ) , ";
      $create_table_hosts .= "snmp_priv_pass VARCHAR( 50 ) , ";
      $create_table_hosts .= "status TINYINT( 1 ) ";
      $create_table_hosts .= ")";

      // Execute create hosts table SQL query and check results
      if (mysqli_query($db_conn,$create_table_hosts))  {
        echo "CREATE TABLE hosts -  Successful.<br>\n";
      }
      else {
        echo "CREATE TABLE hosts - Failed. ".mysqli_error($db_conn)."<br>\n";
      }

      // Build SQL query to create table phones
      $create_table_phones = "CREATE TABLE ".$db_name.".phones ( ";
      $create_table_phones .= "id BIGINT NOT NULL AUTO_INCREMENT , PRIMARY KEY(id) , ";
      $create_table_phones .= "phone VARCHAR( 20 ) NOT NULL , ";
      $create_table_phones .= "extension VARCHAR( 10 ) , ";
      $create_table_phones .= "username VARCHAR( 50 ) , ";
      $create_table_phones .= "description VARCHAR( 50 ) , ";
      $create_table_phones .= "status VARCHAR( 20 ) ";
      $create_table_phones .= ")";

      // Execute create phones table SQL query and check results
      if (mysqli_query($db_conn,$create_table_phones)) {
        echo "CREATE TABLE phones -  Successful.<br>\n";
      }
      else {
        echo "CREATE TABLE phones - Failed. ".mysqli_error($db_conn)."<br>\n";
      }

      // Build SQL query to create table tracking
      $create_table_tracking = "CREATE TABLE ".$db_name.".tracking ( ";
      $create_table_tracking .= "id BIGINT NOT NULL AUTO_INCREMENT , PRIMARY KEY(id) , ";
      $create_table_tracking .= "datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP , ";
      $create_table_tracking .= "phone VARCHAR( 50 ) , ";
      $create_table_tracking .= "switch VARCHAR( 50 ) , ";
      $create_table_tracking .= "switch_loc VARCHAR( 50 ) , ";
      $create_table_tracking .= "switch_int VARCHAR( 50 ) , ";
      $create_table_tracking .= "switch_int_alias VARCHAR( 50 ) ";
      $create_table_tracking .= ")";

      // Execute create tracking table SQL query and check results
      if (mysqli_query($db_conn,$create_table_tracking))  {
        echo "CREATE TABLE tracking -  Successful.<br>\n";
      }
      else {
        echo "CREATE TABLE tracking - Failed. ".mysqli_error($db_conn)."<br>\n";
      }
    }
    else {
      echo "Create DATABASE ".$db_name."' - Failed.  ".mysqli_error($db_conn)."<br>\n";
    }
  }

?>
