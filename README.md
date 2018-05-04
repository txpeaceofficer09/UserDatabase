# UserDatabase

You'll need 2 files added and modify the inc/conf.inc.php variables/constants to match your environment.

The two files that need to be added are mysql.inc.php and ldap.inc.php.


mysql.inc.php:

$mysqli = new mysqli('localhost', 'USERNAME', 'PASSWORD', 'DATABASE');


ldap.inc.php:

define('LDAP_USER', 'DOMAIN\ADMIN');

define('LDAP_PASS', 'PASSWORD');


Modify the files for your environment.  The two files are required from the conf.inc.php file.
