<?php
  $root = realpath($_SERVER["DOCUMENT_ROOT"]);

  include($root."/include/config.inc.php");

  session_start();

  // detect session login status
  if ($_SESSION['access'] == 2) { // admin user
    $db_user = $db_rw_user;
    $db_pass = $db_rw_pass;
  }
  elseif ($_SESSION['access'] == 1) { // read only user
    $db_user = $db_ro_user;
    $db_pass = $db_ro_pass;
  }
  else { // not logged in
    header("Location: login.php");
    die();
  }
?>

<!DOCTYPE html>
<html lang="en">

  <head>

    <title>OpenVoPT: Settings > Call Managers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

  </head>

  <body>

<?php

  include("menu.php");

  // Connect to SQL server
  $db_conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

  // Check SQL connection
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: ".mysqli_connect_error()."<br>\n";
  }
  else {
    // Complete Add Call Managers Action
    if($_POST["action"] == "Add") {
      // Check that host doesn't exist
      $add_sql_check="SELECT * FROM hosts WHERE host='".$_POST["host"]."'";
      if(mysqli_num_rows(mysqli_query($db_conn,$add_sql_check)) > 0) {
        echo "\n".'<font color="red"><b>Host already exists.  Please edit or remove before adding.</b></font>'."<br><br>\n";					
      }
      else {
        $add_sql="INSERT INTO hosts (host,type,snmp_ver,snmp_community,snmp_name,snmp_level,snmp_auth_prot,snmp_auth_pass,snmp_priv_prot,snmp_priv_pass) VALUES ('".$_POST["host"]."','cm','".$_POST["version"]."','".$_POST["community"]."','".$_POST["username"]."','".$_POST["level"]."','".$_POST["auth_prot"]."','".$_POST["auth_pass"]."','".$_POST["priv_prot"]."','".$_POST["priv_pass"]."')";
        mysqli_query($db_conn,$add_sql);
      }
    }

    // Complete Edit Call Managers Action
    if($_POST["action"] == "Edit") {
      $edit_sql="UPDATE hosts SET type='cm',snmp_ver='".$_POST["version"]."',snmp_community='".$_POST["community"]."',snmp_name='".$_POST["username"]."',snmp_level='".$_POST["level"]."',snmp_auth_prot='".$_POST["auth_prot"]."',snmp_auth_pass='".$_POST["auth_pass"]."',snmp_priv_prot='".$_POST["priv_prot"]."',snmp_priv_pass='".$_POST["priv_pass"]."' WHERE host='".$_POST["host"]."'";				
      mysqli_query($db_conn,$edit_sql);
    }

    echo '<div class="container">'."\n";
    echo '<h3>Call Managers:</h3>'."\n\n";

    echo '<div class="form-group">'."\n";
    echo '<div class="container">'."\n";
    echo '<form method="POST" action="s_cm_aer.php">'."\n\n";
    echo '<select name="select" size="10" class="col-sm-5 control-label">'."\n\n";

    // Create SQL query
    $create_sql = "SELECT host FROM hosts WHERE type='cm' ORDER BY host";
    $results=mysqli_query($db_conn,$create_sql);

    // Build Call Manager List
    while($row=mysqli_fetch_array($results)) {
      echo '<option value="'.$row['host'].'">'.$row['host'].'</option>'."\n";
    }
    echo '</select>'."\n";
    echo '</div>'."\n";
    echo '<div class="container">'."\n";
  }

?>

    <br>

    <button type="submit" name="action" class="btn btn-primary" value="Add">Add</button>
    <button type="submit" name="action" class="btn btn-primary" value="Edit">Edit</button>
    <button type="submit" name="action" class="btn btn-primary" value="Remove">Remove</button>

    </form>
    </div>
    </div>

  </body>

</html>
