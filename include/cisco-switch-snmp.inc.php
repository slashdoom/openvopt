<?php

/************************************************************
* FILENAME:    cisco-switch-snmp.inc.php
* DESCRIPTION: class for getting Cisco VoIP phone info from
*              the CDP tables of Cisco switches with SNMP.
* AUTHOR:      Patrick K. Ryon (Slashdoom)
* LICENSE:     BSD 3-clause (see LICENSE file)
************************************************************/

  include("snmp-fix.inc.php");

  class ciscoswitchsnmp {

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

    // determine if the host is a Cisco device
    function isCisco() {

      $sysdescr=snmpget_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"SNMPv2-MIB::sysDescr.0",$this->timeout,$this->retries);

      // SNMP check for cisco in sysDescr
      if (strpos(strtolower($sysdescr),"cisco") !== false) {
        return True;   // device is Cisco
      }
      else {
        return False;  // device is NOT Cisco
      }

    }

    // Return SNMP sysLocation
    function _location($host) {
      // SNMP check for cisco in sysLocation
      $syslocation=snmpget_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"SNMPv2-MIB::sysLocation.0",$this->timeout,$this->retries);

      return $syslocation; // clean up SNMP "STRING: " text

    }

    // Find Cisco IP Phones from SNMP CDP
    function getCDPCiscoPhones() {

      $results=array();  // create array for results

      echo $this->host;

      $location=$this->_location($this->host);  // get SNMP location of host device

      // collect SNMP walk of CDP neighbors table
      $neighbors=snmpwalk_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"SNMPv2-SMI::enterprises.9.9.23.1.2.1.1.8",$this->timeout,$this->retries);

        foreach($neighbors as $neighbor) {  // process each CDP neighbor

          if(strpos($neighbor["value"],"Cisco IP Phone") !== false || strpos($neighbor["value"],"Cisco IP Conference Station") !== false) { // if CDP neighbor is a Cisco IP phone get more detail, otherwise skip
            // get port id from OID with RegEx
            preg_match("/.9.9.23.1.2.1.1.8.(\d+)./",$neighbor["oid"],$portid);
            // get port name, description and phone model
            $desc = snmpget_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"IF-MIB::ifDescr.$portid[1]",$this->timeout,$this->retries);
            $alias = snmpget_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"IF-MIB::ifAlias.$portid[1]",$this->timeout,$this->retries);
            $cdpphone = snmpwalk_fix($this->version,$this->host,$this->community,$this->sec_name,$this->sec_level,$this->auth_prot,$this->auth_pass,$this->priv_prot,$this->priv_pass,"SNMPv2-SMI::enterprises.9.9.23.1.2.1.1.6.$portid[1]",$this->timeout,$this->retries);
            // build results array
            $results[]=array("phone"=>$cdpphone[0]["value"],"switch"=>$this->host,"switch_loc"=>$location,"switch_int"=>$desc,"switch_int_alias"=>$alias);
          }

        }

        return $results;
    }

  }

?>
