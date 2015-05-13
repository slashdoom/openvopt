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

    <title>OpenVoPT: Phones > Phones on Switches</title>
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
    $phone=$_GET['phone'];

    echo '<div class="container">'."\n";
    echo '<h3>Phones found on switches:</h3>'."\n\n";

    // Build SQL query for phone list
    $cm_phone_sql="SELECT * FROM phones ORDER BY phone";
    $results=mysqli_query($db_conn,$cm_phone_sql);

    echo '<div class="form-group">'."\n";
    echo '<div class="col-sm-10">'."\n";
    echo '<form method="GET" action="p_on_sw.php">'."\n";
    echo '<select name="phone" class="form-control">'."\n";

    // Build drop-down phone list
    while($row=mysqli_fetch_array($results)) {
      if ($row['phone'] == $phone) { $sel=' SELECTED'; }
      else { $sel=''; }
      echo '<option value="'.$row['phone'].'"'.$sel.'>'.$row['phone'].'</option>'."\n";
    }
    echo '</select>'."\n";
    echo '</div>'."\n";
    echo '<button type="submit" class="btn btn-primary">Search</button>'."\n";
    echo '</form>'."\n";
    echo '</div>'."\n";

    // build table if phone name is returned
    if (strlen($phone) > 0) {
      echo '<table class="table table-hover">'."\n";
      echo '<thead>'."\n";
      echo '<tr>'."\n";

      // Build SQL query for phone histroy
      $history_sql="SELECT * FROM tracking WHERE phone='".$phone."'ORDER BY datetime DESC";
      $results=mysqli_query($db_conn,$history_sql);

      echo '<th>DATETIME</th>'."\n";
      echo '<th>Phone</th>'."\n";
      echo '<th>Switch Name</th>'."\n";
      echo '<th>Switch Location</th>'."\n";
      echo '<th>Interface</th>'."\n";
      echo '<th>Interface Description</th>'."\n";
      echo '</tr>'."\n";
      echo '</thead>'."\n";
      echo '<tbody>'."\n";

      // Build Phone History List
      while($row=mysqli_fetch_array($results)) {
        echo '<tr>'."\n";
        echo '<td>'.$row['datetime'].'</td>'."\n";
        echo '<td>'.$row['phone'].'</td>'."\n";
        echo '<td>'.$row['switch'].'</td>'."\n";
        echo '<td>'.$row['switch_loc'].'</td>'."\n";
        echo '<td>'.$row['switch_int'].'</td>'."\n";
        echo '<td>'.$row['switch_int_alias'].'</td>'."\n";
        echo '</tr>'."\n";
      }
      echo '</tbody>'."\n";
      echo '</table>'."\n";
      echo '</div>'."\n";
      echo '</div>'."\n";

    }
  }

?>

    <hr>

  </body>

</html>
