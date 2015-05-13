<?php

  include "config.inc.php";
  include "ldap-auth.inc.php";

    if(authenticate("user","testpw")) {
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

?>
