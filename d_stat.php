
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

    <title>OpenVoPT: Status > Device Polling Status</title>
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
    echo '<div class="container">'."\n";
    echo '<h3>Phones currently in Call Managers:</h3>'."\n\n";
    
    echo '<table class="table table-hover">'."\n";
    echo '<thead>'."\n";
    echo '<tr>'."\n";
    echo '<th>Device</th>'."\n";
    echo '<th>Type</th>'."\n";
    echo '<th>Polling Status</th>'."\n";
    echo '</tr>'."\n";
    echo '</thead>'."\n";
    echo '<tbody>'."\n";
        // Create SQL query
    $devicestat_sql="SELECT * FROM hosts ORDER BY host";
    $results=mysqli_query($db_conn,$devicestat_sql);
    // Build Device Status List
    while($row=mysqli_fetch_array($results)) {
      if ($row['extension'] == 1) {
        echo '<tr class="success">'."\n";
        $status = 'Success';
      }
      else {
        echo '<tr class="danger">'."\n";
        $status = 'Failure';
      }
      if ($row['type'] == 'sw') {
        $type = 'Switch';
      }
      if ($row['type'] == 'cm') {
        $type = 'Call Manager';
      }
      echo '<td>'.$row['host'].'</td>'."\n";
      echo '<td>'.$type.'</td>'."\n";
      echo '<td>'.$status.'</td>'."\n";
      echo '</tr>'."\n";
    }
    echo '</tbody>'."\n";
    echo '</table>'."\n";
    echo '</div>'."\n";
  }
  
  ?>

    <hr>

  </body>

</html>
