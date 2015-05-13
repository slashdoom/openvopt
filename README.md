# OpenVoPT

OpenVoPT (VoIP Phone Tracker) is a web interface and collection of php scripts 
to help in tracking the movement of Cisco VoIP phones on Cisco based networks.

We found that after we implemented a NAC solution and our user support group
was able to move VoIP phones around without contacting the network/phone group
we had difficulty keeping our E911 locations up to date.  This helps us monitor
such moves and track VoIP phones in general.

# License
Project licensed under the BSD 3-clause license.  See LICENSE file for more
information.

This repository contains Bootstrap 3 and jQuery for webpage styling and menu
systems.  These are included for convienance only and not subject to the
license and copywrite of this project but to those of their respective owners.

See:
http://getbootstrap.com \\
http://jquery.com \\

# Requires
php5 (mostly statndard but does need MySQL and LDAP modules). \\
Web server such as Apache2 or NGINX. \\
MySQL/MariaDB. \\

# Installation

After you've cloned this repo modify the file include/config.inc.php
Once this file has valid $db_rw_user, $db_rw_pass, $db_host and
$db_name variables configured you should be able to run scripts/create_db.php
Point your php5-enabled web server at the base directory.
Add scripts/scheduled.php as a daily cron job.
