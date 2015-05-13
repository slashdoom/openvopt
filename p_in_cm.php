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

    <title>OpenVoPT: Phones > Phones in Call Manager</title>
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
    echo '<th>Phone</th>'."\n";
    echo '<th>Extension</th>'."\n";
    echo '<th>Description</th>'."\n";
    echo '<th>Status</th>'."\n";
    echo '</tr>'."\n";
    echo '</thead>'."\n";
    echo '<tbody>'."\n";
    // Create SQL query
    $cm_current_sql="SELECT * FROM phones ORDER BY phone";
    $results=mysqli_query($db_conn,$cm_current_sql);
    // Build Currect Phone List
    while($row=mysqli_fetch_array($results)) {
      echo '<tr>'."\n";
      echo '<td><a href="p_on_sw.php?phone='.$row['phone'].'">'.$row['phone'].'</a></td>'."\n";
      echo '<td>'.$row['extension'].'</td>'."\n";
      echo '<td>'.$row['description'].'</td>'."\n";
      echo '<td>'.$row['status'].'</td>'."\n";
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
