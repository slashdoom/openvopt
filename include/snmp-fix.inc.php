<?php

/************************************************************
* FILENAME:    snmp-fix.inc.php
* DESCRIPTION: This script contains functions to call snmpget
*              snmpgetnext and snmpwalk and return their
*              results.  I wish I could just use the built-in
*              PHP SNMP libraries but they unfortunately seem
*              buggy.
* AUTHOR:      Patrick K. Ryon (Slashdoom)
* LICENSE:     BSD 3-clause (see LICENSE file)
************************************************************/

function snmpget_fix($version,$host,$community,$sec_name,$sec_level,$auth_prot,$auth_pass,$priv_prot,$priv_pass,$oid,$timeout,$retries) {

  $cli="snmpget -O aqv";

  // for snmp version 1 and 2c add version and community string
  if ($version == '1' || strtolower($version) == '2c' ) {
    $cli .= " -v ".$version." -c ".$community;
  }

  // for snmp version 3 add version and security parameters
  if ($version == '3') {
    $cli .= " -v ".$version." -u ".$sec_name." -l ".$sec_level;
    if(strlen($auth_prot) > 0) { $cli .= " -a ".$auth_prot; }
    if(strlen($auth_pass) > 0) { $cli .= " -A ".$auth_pass; }
    if(strlen($priv_prot) > 0) { $cli .= " -x ".$priv_prot; }
    if(strlen($priv_pass) > 0) { $cli .= " -X ".$priv_pass; }
  }

  // add timeout settings if present to command
  if(strlen($timeout) > 0) {
    $cli .= " -t ".$timeout;
  }

  // add retry information if present to command
  if(strlen($retries) > 0) {
    $cli .= " -r ".$retries;
  }

  // add host to command
  $cli .= " ".$host;

  // add oid if present to command
  if(strlen($oid) > 0) {
    $cli .= " ".$oid;
  }

  // execute command and get output
  $snmpoutput = shell_exec($cli);

  // if valid snmpwalk return output in array
  if(strpos($snmpoutput,"snmpget") === false || strpos($snmpoutput,"No Such Instance") === false) { return $snmpoutput; }
  else { return false; }

}



function snmpgetnext_fix($version,$host,$community,$sec_name,$sec_level,$auth_prot,$auth_pass,$priv_prot,$priv_pass,$oid,$timeout,$retries) {

  $cli="snmpgetnext -O aqv";

  // for snmp version 1 and 2c add version and community string
  if ($version == '1' || strtolower($version) == '2c' ) {
    $cli .= " -v ".$version." -c ".$community;
  }

  // for snmp version 3 add version and security parameters
  if ($version == '3') {
    $cli .= " -v ".$version." -u ".$sec_name." -l ".$sec_level;
    if(strlen($auth_prot) > 0) { $cli .= " -a ".$auth_prot; }
    if(strlen($auth_pass) > 0) { $cli .= " -A ".$auth_pass; }
    if(strlen($priv_prot) > 0) { $cli .= " -x ".$priv_prot; }
    if(strlen($priv_pass) > 0) { $cli .= " -X ".$priv_pass; }
  }

  // add timeout settings if present to command
  if(strlen($timeout) > 0) {
    $cli .= " -t ".$timeout;
  }

  // add retry information if present to command
  if(strlen($retries) > 0) {
    $cli .= " -r ".$retries;
  }

  // add host to command
  $cli .= " ".$host;

  // add oid if present to command
  if(strlen($oid) > 0) {
    $cli .= " ".$oid;
  }

  // execute command and get output
  $snmpoutput = shell_exec($cli);

  // if valid snmpget return output in array
  if(strpos($snmpoutput,"snmpgetnext") === false) { return $snmpoutput; }
  else { return false; }

}



function snmpwalk_fix($version,$host,$community,$sec_name,$sec_level,$auth_prot,$auth_pass,$priv_prot,$priv_pass,$oid,$timeout,$retries) {

  $cli="snmpwalk -O anq";

  // for snmp version 1 and 2c add version and community string
  if ($version == '1' || strtolower($version) == '2c' ) {
    $cli .= " -v ".$version." -c ".$community;
  }

  // for snmp version 3 add version and security parameters
  if ($version == '3') {
    $cli .= " -v ".$version." -u ".$sec_name." -l ".$sec_level;
    if(strlen($auth_prot) > 0) { $cli .= " -a ".$auth_prot; }
    if(strlen($auth_pass) > 0) { $cli .= " -A ".$auth_pass; }
    if(strlen($priv_prot) > 0) { $cli .= " -x ".$priv_prot; }
    if(strlen($priv_pass) > 0) { $cli .= " -X ".$priv_pass; }
  }

  // add timeout settings if present to command
  if(strlen($timeout) > 0) {
    $cli .= " -t ".$timeout;
  }

  // add retry information if present to command
  if(strlen($retries) > 0) {
    $cli .= " -r ".$retries;
  }

  // add host to command
  $cli .= " ".$host;

  // add oid if present to command
  if(strlen($oid) > 0) {
    $cli .= " ".$oid;
  }

  // execute command and get output
  $snmpoutput = shell_exec($cli);

  // if valid snmpget return output in array
  if(strpos($snmpoutput,"snmpwalk") === false && strpos($snmpoutput,"No Such Instance") === false) {
    $walk_array = [];
    $line_array = explode(PHP_EOL, $snmpoutput);
    foreach ($line_array as $in_line) {
      if (strlen($in_line) > 0) {
        preg_match('/(.*) "(.*)"/',$in_line,$out_line);
        $walk_array[] = ['oid' => $out_line[1], 'value' => $out_line[2]];
      }
    }
    return $walk_array;
  }
  else { return false; }

}

?>
