<?php

ob_start(); // Start output buffering.

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

define('BASE_URI', '/var/www/html/');
define('BASE_URL', 'https://kcisd-tech/');
define('DB', '/var/www/mysql.inc.php');

require_once('/var/www/ldap.inc.php'); // Get our Username/Password for LDAP.

define('FILENAME', substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/')));
define('PATH', '/');

$ldap_servers = array('kcisd-dc2', 'kcisd-dc3'); // List of our logon servers.
$logon_server = findLogon($ldap_servers); // Pick the logon server we will use.

function findLogon($arr) {
	$retVal = false;
	foreach ($arr as $addr) {
		$fp = fsockopen($addr, 389, $errno, $errstr, 0.02);
		if (!$fp) {
			// Failed to connect to LDAP on the server.
		} else {
			// The first LDAP server to respond to our request is chosen as our logon server.
			$retVal = $addr;
			break;
			fclose($fp);
		}
	}
	return $retVal;
}

function isAccountLocked($user) {
	global $logon_server;

	$ds = ldap_connect($logon_server);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
	$bd = ldap_bind($ds, LDAP_USER, LDAP_PASS);
	$filter="samaccountname=".$user;
	$result=ldap_search($ds,LDAP_DN,$filter, array('useraccountcontrol'));
	$entries=ldap_get_entries($ds, $result);
	ldap_unbind($ds);

	$bitFlags = array(
		"TRUSTED_TO_AUTH_FOR_DELEGATION"=>16777216,
		"PASSWORD_EXPIRED"=>8388608,
		"DONT_REQ_PREAUTH"=>4194304,
		"USE_DES_KEY_ONLY"=>2097152,
		"NOT_DELEGATED"=>1048576,
		"TRUSTED_FOR_DELEGATION"=>524288,
		"SMARTCARD_REQUIRED"=>262144,
		"MNS_LOGON_ACCOUNT"=>131072,
		"DONT_EXPIRE_PASSWORD"=>65536,
		"SERVER_TRUST_ACCOUNT"=>8192,
		"WORKSTATION_TRUST_ACCOUNT"=>4096,
		"INTERDOMAIN_TRUST_ACCOUNT"=>2048,
		"NORMAL_ACCOUNT"=>512,
		"TEMP_DUPLICATE_ACCOUNT"=>256,
		"ENCRYPTED_TEXT_PWD_ALLOWED"=>128,
		"PASSWD_CANT_CHANGE"=>64,
		"PASSWD_NOTREQD"=>32,
		"LOCKOUT"=>16,
		"HOMEDIR_REQUIRED"=>8,
		// "ACCOUNT_DISABLED"=>2,  // We don't need to remove this so we can evaluate it.
		// "SCRIPT"=>1
	);

	$val = (isset($entries[0])) ? $entries[0]['useraccountcontrol'][0] : 0;

	foreach ($bitFlags AS $k=>$v) {
		if ($val >= $v) {
			$val = $val - $v;
		}
	}

	// Take the bitmask field useraccountcontrol and start with the largest attribute PASSWORD_EXPIRED and loop through the values if the value is smaller than
	// useraccountcontrol then subtract it from useraccountcontrol and move on until useraccountcontrol is 2 or 0.  If it is 2 or 3 then it is a locked account.

	return ($val >= 2) ? false : true;
}

require_once(DB);

$total = 0;
$active = 0;
$inactive = 0;

$results = $mysqli->query("SELECT `username` FROM `users`;");

while ($row=$results->fetch_assoc()) {
	if (isAccountLocked($row['username']) == true) {
		// Account inactive. Write back to database.
		$mysqli->query("UPDATE `users` SET `active`=0 WHERE `username`='".$row['username']."';");
		$inactive++;
	} else {
		// Account active. Write back to database.
		$mysqli->query("UPDATE `users` SET `active`=1 WHERE `username`='".$row['username']."';");
		$active++;
	}
	$total++;
}

echo "Active: ".$active.", Inactive: ".$inactive.", Total: ".$total;

$mysqli->close();

ob_end_flush();

?>
