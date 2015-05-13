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
    <title>OpenVoPT: Summary</title>
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
    $cm_count_sql="SELECT * FROM hosts WHERE type='cm'";
    $cm_reg_sql="SELECT * FROM phones WHERE status='registered'";
    $cm_unreg_sql="SELECT * FROM phones WHERE status='unregistered'";
    $cm_total_sql="SELECT * FROM phones";

    echo '<div class="container">'."\n";

    echo '<h3>OpenVoPT Summary:</h2>'."\n\n";

    echo '<br>'."\n";

    echo '<table width="250px">'."\n";
    echo '<tr>'."\n";
    echo '<td colspan="2"><b>Call Manager Statistics</b></td>'."\n";
    echo '</tr>'."\n";
    echo '<tr>'."\n";
    echo '<td>Call Manager Servers Monitored:'."\n";
    echo '<td width="20px">'.mysqli_num_rows(mysqli_query($db_conn,$cm_count_sql)).'</td>'."\n";
    echo '</tr>'."\n";
    echo '<tr>'."\n";
    echo '<td>Registered Phones:</td>'."\n";
    echo '<td>'.mysqli_num_rows(mysqli_query($db_conn,$cm_reg_sql)).'</td>'."\n";
    echo '</tr>'."\n";
    echo '<tr>'."\n";
    echo '<td>Unregistered Phones:</td>'."\n";
    echo '<td>'.mysqli_num_rows(mysqli_query($db_conn,$cm_unreg_sql)).'</td>'."\n";
    echo '</tr>'."\n";
    echo '<tr>'."\n";
    echo '<td>Total Phones:</td>'."\n";
    echo '<td>'.mysqli_num_rows(mysqli_query($db_conn,$cm_total_sql)).'</td>'."\n";
    echo '</tr>'."\n";
    echo '</table>'."\n";

    echo '<br>'."\n";

    $sw_count_sql="SELECT * FROM hosts WHERE type='sw'";
    $sw_disc_sql="SELECT DISTINCT phone FROM tracking";

    echo '<table width="250px">'."\n";
    echo '<tr>'."\n";
    echo '<td colspan="2"><b>Switch Statistics</b></td>'."\n";
    echo '</tr>'."\n";
    echo '<td>Switches Monitored:</td>'."\n";
    echo '<td width="20px">'.mysqli_num_rows(mysqli_query($db_conn,$sw_count_sql)).'</td>'."\n";
    echo '</tr>'."\n";
    echo '<tr>'."\n";
    echo '<td>Discovered Phones:</td>'."\n";
    echo '<td>'.mysqli_num_rows(mysqli_query($db_conn,$sw_disc_sql)).'</td>'."\n";
    echo '</tr>'."\n";
    echo '</table>'."\n";
    echo '</div>'."\n";
  }

?>

  </body>

</html>
