# UserDatabase

You'll need 2 files added and modify the inc/conf.inc.php variables/constants to match your environment.

The two files that need to be added are mysql.inc.php and ldap.inc.php.


mysql.inc.php:

$mysqli = new mysqli('localhost', 'USERNAME', 'PASSWORD', 'DATABASE');


ldap.inc.php:

define('LDAP_USER', 'DOMAIN\ADMIN');

define('LDAP_PASS', 'PASSWORD');

define('LDAP_ADMIN_GROUP', 'SECURITYGROUP');

define('LDAP_TEACHER_GROUP', 'SECURITYGROUP');

Modify the files for your environment.  The two files are required from the conf.inc.php file.


In the ldap.inc.php file, the LDAP_ADMIN_GROUP and LDAP_TEACHER_GROUP are security groups that users must be
a member of to have access to the program.  A member of the LDAP_ADMIN_GROUP will be able to edit/create
users.  The LDAP_TEACHER_GROUP members have access to records of users who have a numeric `position`. In our
school district we use position to show an employee's position or the year a student should graduate.
