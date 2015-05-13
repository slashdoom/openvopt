<?php

/************************************************************
* FILENAME:    ldap-auth.inc.php
* DESCRIPTION: This script contains the function to
*              authenticate ldap users and establish a web
*              session.
* AUTHOR:      Patrick K. Ryon (Slashdoom)
* LICENSE:     BSD 3-clause (see LICENSE file)
************************************************************/

function ldap_user_auth($login_user, $login_pass, $ldap_fqdn, $ldap_port, $ldap_search_base, $ldap_rw_group, $ldap_ro_group, $ldap_search_user, $ldap_search_pass) {

  // initialize session
  $access = 0;

  // connect to ldap
  $ldap_conn_stat = ldap_connect($ldap_fqdn, $ldap_port);
  if ($ldap_conn_stat === FALSE) {
    // Couldn't connect to LDAP service
    // establish session variables
    $_SESSION['user']   = '';
    $_SESSION['access'] = $access;
    return false;
  }

  // bind as search user
  $ldap_bind_stat = ldap_bind($ldap_conn_stat, $ldap_search_user, $ldap_search_pass);
  if ($ldap_bind_stat === FALSE) {
    die("Couldn't bind to LDAP as application user");
  }

  // find the user dn
  // See the note above about the need to LDAP-escape $username!
  $ldap_query = "(&(sAMAccountName=" . $login_user . ")(objectClass=person))";
  $ldap_search_stat = ldap_search(
    $ldap_conn_stat, $ldap_search_base, $ldap_query, array('dn','memberOf')
  );

  if ($ldap_search_stat === FALSE) {
    // Search on LDAP failed
    // establish session variables
    $_SESSION['user']   = '';
    $_SESSION['access'] = $access;
    return false;
  }

  // pull the search results
  $ldap_result = ldap_get_entries($ldap_conn_stat, $ldap_search_stat);
  if ($ldap_result === FALSE) {
    // Couldn't pull search results from LDAP
    // establish session variables
    $_SESSION['user']   = '';
    $_SESSION['access'] = $access;
    return false;
  }

  if ((int) @$ldap_result['count'] > 0) {
    $login_userdn = $ldap_result[0]['dn'];
    foreach($ldap_result[0]['memberof'] as $grps) {
      // if rw group set access level and break loop
      if (strpos($grps, $ldap_rw_group)) { $access = 2; break; }
      // if ro group set access level
      if (strpos($grps, $ldap_ro_group)) { $access = 1; }
    }
  }

  // atttemp auth with login credentials
  $auth_status = ldap_bind($ldap_conn_stat, $login_userdn, $login_pass);
  if ($auth_status && $access > 0) {
    // establish session variables
    $_SESSION['user']   = $login_user;
    $_SESSION['access'] = $access;
    return true;
  }
  else {
    // establish session variables
    $_SESSION['user']   = '';
    $_SESSION['access'] = $access;
    return false;
  }
}
?>
