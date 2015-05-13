<?php

/************************************************************
* FILENAME:    cisco-cm-snmp.inc.php
* DESCRIPTION: class for getting Cisco VoIP phone info from
*              Cisco Call Managers with SNMP.
* AUTHOR:      Patrick K. Ryon (Slashdoom)
* LICENSE:     BSD 3-clause (see LICENSE file)
************************************************************/

  include("snmp-fix.inc.php");

  class ciscocmsnmp {

    var $version = "";    // SNMP Version
    var $host = "";       // hostname of the SNMP agent
    var $community = "";  // community name (for v1 & 2c)
    var $sec_name = "";   // security name, usually some kind of username (for v3)
    var $sec_level = "";  // security level (noAuthNoPriv|authNoPriv|authPriv) (for v3)
    var $auth_prot = "";  // authentication protocol (MD5 or SHA) (for v3)
    var $auth_pass = "";  // authentication pass phrase (for v3)
    var $priv_prot = "";  // privacy protocol (DES or AES) (for v3)
    var $priv_pass = "";  // privacy pass phrase (for v3)
    var $timeout = "";    // microseconds until the first timeout
    var $retries = "";    // number of times to retry if timeouts occur

    // Setup SNMP Credentials
    function setSNMP($version,$host,$community,$sec_name,$sec_level,$auth_prot,$auth_pass,$priv_prot,$priv_pass,$timeout,$retries) {

      $this->version=$version;       // set version 2c or 3

      if ($version == '1' || $version == '2c') {
        $this->host=$host;           // set SNMP 2c host
        $this->community=$community; // set SNMP 2c community
        $this->timeout=$timeout;     // set SNMP timeout settings
        $this->retries=$retries;     // set SNMP retry settings
        return True;                 // confirm success
      }

      elseif ($version == '3') {
        $this->host=$host;            // set SNMP 3 host
        $this->sec_name=$sec_name;    // set SNMP 3 username
        $this->sec_level=$sec_level;  // set SNMP 3 security level (noAuthNoPriv, authNoPriv or authPriv)
        $this->auth_prot=$auth_prot;  // set SNMP 3 auth protocol (none, MD5 or SHA)
        $this->auth_pass=$auth_pass;  // set SNMP 3 auth password
        $this->priv_prot=$priv_prot;  // set SNMP 3 priv password (none, DES or AES)
        $this->priv_pass=$priv_pass;  // set SNMP 3 priv password
        $this->timeout=$timeout;      // set SNMP timeout settings
        $this->retries=$retries;      // set SNMP retry settings
        return True;                  // confirm success
      }

      else {
        return False;  // error occurred
      }

    }

    // determine if the host is a Cisco device running UCOS
    function isUCOS() {

      $sysdescr=snmpget_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"SNMPv2-MIB::sysDescr.0",$this->timeout,$this->retries);

      // SNMP check for UCOS in sysDescr
      if ((strpos(strtolower($sysdescr),"software:ucos") !== false) || (strpos(strtolower($sysdescr),"linux release") !== false)) {
        return True;   // device is a Call Manager
      }
      else {
        return False;  // device is NOT a Call Manager
      }

    }

    // Find Cisco IP Phones from SNMP CDP
    function getCMCiscoPhones() {

      $results=array();  // create array for results

      // collect SNMP walk of ccmPhoneName table
      $phones=snmpwalk_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"SNMPv2-SMI::enterprises.9.9.156.1.2.1.1.20",$this->timeout,$this->retries);

      foreach($phones as $phone) {  // process each CM phone

        if(strpos($phone["value"], "SEP") !== false) { // if OID is a Cisco IP phone get more detail, otherwise skip
          // get phone id from OID with RegEx
          preg_match("/.9.9.156.1.2.1.1.20.(\d+)/",$phone["oid"],$phoneid);
          // get username, description, status
          $extension=str_replace('"','',snmpgetnext_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"SNMPv2-SMI::enterprises.9.9.156.1.2.5.1.2.".$phoneid[1],$this->timeout,$this->retries));
          $username=str_replace('"','',snmpget_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"SNMPv2-SMI::enterprises.9.9.156.1.2.1.1.5.".$phoneid[1],$this->timeout,$this->retries));
          $description=str_replace('"','',snmpget_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"SNMPv2-SMI::enterprises.9.9.156.1.2.1.1.4.".$phoneid[1],$this->timeout,$this->retries));
          $status=snmpget_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"SNMPv2-SMI::enterprises.9.9.156.1.2.1.1.7.".$phoneid[1],$this->timeout,$this->retries);
          // translate CM phone status
          switch ($status) {
            case 1:
              $status='unknown';
              break;
            case 2:
              $status='registered';
              break;
            case 3:
              $status='unregistered';
              break;
            case 4:
              $status='rejected';
              break;
            case 5:
              $status='partiallyregistered';
              break;
          }
          // build results array
          $results[]=array("phone"=>$phone["value"],"username"=>$username,"extension"=>$extension,"description"=>$description,"status"=>$status);
        }
      }
     return $results;
    }
  }

?>
