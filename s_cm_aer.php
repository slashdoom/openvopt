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
    header("Location: s_cm.php");
    die();
  }
  else { // not logged in
    header("Location: login.php");
    die();
  }
?>

<!DOCTYPE html>
<html lang="us">

  <head>

    <title>OpenVoPT: Settings > Call Managers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

  </head>

  <body>

<?php

  // Error checking - If no action was found return to call managers management page
  if($_POST["action"] == "") {
    echo '<meta http-equiv="refresh" content ="0; url=s_cm.php">';
  }

  // Connect to SQL server
  $db_conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

  // Check SQL connection
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: ".mysqli_connect_error()."<br>\n";
  }

  // Remove option selected.
  if($_POST["action"] == "Remove") {
    $create_sql = "DELETE FROM hosts WHERE host='".$_POST["select"]."'";
    mysqli_query($db_conn,$create_sql);
    echo '<meta http-equiv="refresh" content ="0; url=s_cm.php">';
  }
?>

<?php include("menu.php"); ?>

  <div class="container">
  <h3><?php echo $_POST["action"].' Call Manager';?>:</h3>

<?php

  // Edit option selected
  if($_POST["action"] == "Edit") {
    $create_sql = "SELECT * FROM hosts WHERE host='".$_POST["select"]."'";

    $results=mysqli_query($db_conn,$create_sql);
    $row=mysqli_fetch_array($results);
  }
?>
    <div class="form-group">
    <form class="form-horizontal" method="POST" action="s_cm.php">

    <table>
      <tr>
        <td colspan="2">
          <div class="form-group">
            <label class="col-sm-2 control-label">Host:</label>
            <div class="col-sm-10">
              <input class="form-control" name="host" value="<?php if($_POST["action"] == "Edit") { echo $_POST["select"];}?>" <?php if($_POST["action"] == "Edit") { echo "READONLY";}?>>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <div class="form-group">
            <label class="col-sm-2 control-label">Version:</label>
            <div class="col-sm-10">
              <input type="radio" name="version" value="2c" <?php if($row['snmp_ver']=="2c") { echo "CHECKED"; } ?>> 2c 
              <input type="radio" name="version" value="3" <?php if($row['snmp_ver']=="3") { echo "CHECKED"; } ?>> 3
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <div class="form-group">
            <label class="col-sm-2 control-label">Community:</label>
            <div class="col-sm-10">
              <input class="form-control" name="community" value="<?php echo $row['snmp_community']; ?>">
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="form-group">
            <label class="col-sm-5 control-label">Username:</label>
            <div class="col-sm-7">
              <input class="form-control" name="username" value="<?php echo $row['snmp_name']; ?>">
            </div>
          </div>
        </td>
        <td>
          <div class="form-group">
	    <label class="col-sm-5 control-label">Level:</label>
            <div class="col-sm-7">
              <select name="level" class="form-control">
                <option value="" <?php if($row['snmp_level']=="") { echo "SELECTED"; } ?>></option>
                <option value="noAuthNoPriv" <?php if($row['snmp_level']=="noAuthNoPriv") { echo "SELECTED"; } ?>>noAuthNoPriv</option>
                <option value="AuthNoPriv" <?php if($row['snmp_level']=="AuthNoPriv") { echo "SELECTED"; } ?>>AuthNoPriv</option>
                <option value="AuthPriv" <?php if($row['snmp_level']=="AuthPriv") { echo "SELECTED"; } ?>>AuthPriv</option>
              </select>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="form-group">
            <label class="col-sm-5 control-label">Auth Protocol:</label>
            <div class="col-sm-7">
              <select name="auth_prot" class="form-control">
                <option value="" <?php if($row['snmp_auth_prot']=="") { echo "SELECTED"; } ?>></option>
                <option value="md5" <?php if($row['snmp_auth_prot']=="md5") { echo "SELECTED"; } ?>>md5</option>
                <option value="sha" <?php if($row['snmp_auth_prot']=="sha") { echo "SELECTED"; } ?>>sha</option>
              </select>
            </div>
          </div>
        </td>
        <td>
          <div class="form-group">
            <label class="col-sm-5 control-label">Priv Protocol:</label>
            <div class="col-sm-7">
              <select name="priv_prot" class="form-control">
                <option value="" <?php if($row['snmp_priv_prot']=="") { echo "SELECTED"; } ?>></option>
                <option value="aes" <?php if($row['snmp_priv_prot']=="aes") { echo "SELECTED"; } ?>>aes</option>
                <option value="des" <?php if($row['snmp_priv_prot']=="des") { echo "SELECTED"; } ?>>des</option>
              </select>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="form-group">
            <label class="col-sm-5 control-label">Auth Password:</label>
            <div class="col-sm-7">
              <input name="auth_pass" class="form-control" type="password" value="<?php echo $row['snmp_auth_pass']; ?>">
            </div>
          </div>
        </td>
        <td>
          <div class="form-group">
            <label class="col-sm-5 control-label">Priv Password:</label>
            <div class="col-sm-7">
              <input name="priv_pass" class="form-control" type="password" value="<?php echo $row['snmp_priv_pass']; ?>">
            </div>
          </div>
        </td>
      </tr>
    </table>

    <br>

    <button type="submit" class="btn btn-primary" name="action" value="<?php echo $_POST["action"];?>"><?php echo $_POST["action"];?></button>
    <button type="submit" class="btn btn-primary" name="action" value="Cancel">Cancel</button>

  </form>
  </div>
  </body>

</html>
