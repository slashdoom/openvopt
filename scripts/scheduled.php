<?php

/************************************************************
* FILENAME:    scheduled.php
* DESCRIPTION: This script combines and runs the OpenVoPT
*              cron scripts.
* AUTHOR:      Patrick K. Ryon (Slashdoom)
* LICENSE:     BSD 3-clause (see LICENSE file)
************************************************************/

  $root = realpath($_SERVER["DOCUMENT_ROOT"]);

  echo "Running switch_poller.\n";
  shell_exec("php ".$root."/scripts/switch_poller.php");
  echo "Complete.\n\n";

  echo "Running cm_poller.\n";
  shell_exec("php ".$root."/scripts/cm_poller.php");
  echo "Complete.\n\n";

  echo "Running alert_sender.\n";
  shell_exec("php ".$root."/scripts/alert_sender.php");
  echo "Complete.\n\n";
?>
