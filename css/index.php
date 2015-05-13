<!DOCTYPE html>
<html>

  <head>

    <title>OpenVoPT: Redirect</title>

  </head>

  <body>

<?php

  $root = realpath($_SERVER["DOCUMENT_ROOT"]);

  include($root."/include/config.inc.php");

  echo '<meta http-equiv="refresh" content ="0; url=http://'.$url.'">'."\n"

?>

  </body>

</html>
