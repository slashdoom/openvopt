<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />

    <title>OpenVoPT: Login</title>
  </head>

  <body>

<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

  // initialize session
  session_start();

  $root = realpath($_SERVER["DOCUMENT_ROOT"]);

  include($root."/include/ldap-auth.inc.php");
  include($root."/include/config.inc.php");

  // check to see if user is logging out
  if(isset($_GET['out'])) {
    // destroy session
    session_unset();
    $_SESSION = array();
    unset($_SESSION['user'],$_SESSION['access']);
    session_destroy();
  }

  // check to see if login form has been submitted
  if(isset($_POST['userLogin'])) {
    // run information through authenticator
    echo 'submitted';
    echo $_POST['userLogin'];
    echo $_POST['userPassword'];
    if(ldap_user_auth($_POST['userLogin'], $_POST['userPassword'], $ldap_fqdn, $ldap_port, $ldap_search_base, $ldap_rw_group, $ldap_ro_group, $ldap_search_user, $ldap_search_pass)) {
      echo 'auth passes';
      // authentication passed
      header("Location: index.php");
      die();
    }
    else {
      // authentication failed
      echo 'auth failed';
      $error = 1;
    }
  }
  // output error to user
  if (isset($error)) echo "<br>Login failed: Incorrect user name, password, or rights.<br>";

  // output logout success
  //if (isset($_GET['out'])) echo "  Logout successful"."</br>";
?>

  <div class="container">
  <h2>OpenVoPT Login</h2>
  <form class="form-horizontal" role= "form" action="login.php" method="post">
    <div class="form-group">
      <label class="control-label col-sm-2" for="username">User:</label>
      <div class="col-sm-10">
        <input type="username" class="form-control" name="userLogin" placeholder="Enter username">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="password">Password:</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" name="userPassword" placeholder="Enter password">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
      </div>
    </div>
  </form>

</body>
