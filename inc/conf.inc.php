<?php

/* ---------------------------------------------------------------------------------------------------------- */
/* Filename: conf.inc.php                                                                                     */
/* Author: James McCaughey                                                                                    */
/* E-Mail: jmccaughey@kirbyvillecisd.org                                                                      */
/*                                                                                                            */
/* This file brings in the database connection and sets up the functions, variables and constants that will   */
/* be used throughout the program.                                                                            */
/* ---------------------------------------------------------------------------------------------------------- */

/* -- START CACHE CONTROL / OUTPUT BUFFERING SECTION -------------------------------------------------------- */
/*  Start output buffering and disable browser caching so users see current content.                          */
/* ---------------------------------------------------------------------------------------------------------- */

// ini_set('display_errors', '1'); // Display errors on the page.
set_time_limit(10);

ob_start(); // Start output buffering.

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

/* -- END CACHE CONTROL / OUTPUT BUFFERING SECTION ---------------------------------------------------------- */

/* -- START CONSTANTS SECTION ------------------------------------------------------------------------------- */
/*  Define a few variables we will need in our program.                                                       */
/* ---------------------------------------------------------------------------------------------------------- */

define('BASE_URI', '/var/www/html/');
define('BASE_URL', 'https://kcisd-tech/');
define('DB', '/var/www/mysql.inc.php');

require_once('/var/www/ldap.inc.php'); // Get our Username/Password for LDAP.

define('FILENAME', substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/')));
define('PATH', '/');

/* -- END CONSTANTS SECTION --------------------------------------------------------------------------------- */

/* -- START VARIABLES SECTION ------------------------------------------------------------------------------- */
/*  Define a few variables we will need in our program.                                                       */
/* ---------------------------------------------------------------------------------------------------------- */

$ldap_servers = array('dc2', 'kcisd-dc2', 'kcisd-dc3'); // List of our logon servers.
$logon_server = findLogon($ldap_servers); // Pick the logon server we will use.

$sname = session_name(); // Get the name of the session cookie.
 
$debug = FALSE;
$contact_email = 'jmccaughey@kirbyvillecisd.org';

$page_title = 'Kirbyville CISD - User Database';

/* -- END VARIABLES SECTION --------------------------------------------------------------------------------- */

require_once(DB); // Initialize the database connection.

/* -- START FUNCTIONS SECTION ------------------------------------------------------------------------------- */
/*  Define a few functions to make our life easier and save on recycling code blocks.                         */
/* ---------------------------------------------------------------------------------------------------------- */

// Completely wipe the session and log the user out.
function logout()
{
	$sname = session_name(); // Get the name of the session cookie.
	session_destroy(); // Destroy the apparently compromised session.

	// As per the example on http://www.php.net/manual/en/function.session-destroy.php,
	// this will destroy our session cookie. I left the init_get() check in place so
	// I could expand the code later to incorporate other session id storage methods, but
	// for now it should be noted that the script does not support any other method.
	if (ini_get("session.use_cookies"))
	{
		$params = session_get_cookie_params(); // Get the parameters of the cookie so we can make sure we over-write the cookie.
		if ( isset($_COOKIE[$sname]) ) setcookie($sname, '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']); // Set the session id cookie to '' to eliminate it.
	}

	header('Location: https://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?msg=4'); // Redirect the user back to the referring page.
}

// Return domain without the sub-domain.
function domain($domain)
{
	if ( strtolower($domain) == 'localhost' ) return $domain;
	preg_match('/([a-z0-9_\-]{0,}\.[a-z]{2,2}\.[a-z]{2,2}|[a-z0-9_\-]{3,}\.[a-z]{2,3})[\/:0-9]{0,}$/', $domain, $matches);
	return (isset($matches[0])) ? $matches[0] : $domain;
}

// Get a user's full name from Active Directory (LDAP).
function getFullName($user) {
	global $logon_server;

	$ds = ldap_connect($logon_server);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
	$bd = ldap_bind($ds,LDAP_USER, LDAP_PASS);
	$dn="OU=Campuses,DC=kcisd,DC=local";
	$filter="samaccountname=".$user;
	// $result=ldap_search($ds,$dn,$filter, array('sn', 'givenname'));
	$result=ldap_search($ds,LDAP_DN,$filter, array('sn', 'givenname'));
	$entries=ldap_get_entries($ds, $result);
	ldap_unbind($ds);

	return $entries[0]['givenname'][0].' '.$entries[0]['sn'][0];
}

// Find an available logon server.
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

// Return whether user is an administrator (Member of the TechnologyDepartment Security Group)
// I used the TechnologyDepartment rather than Domain Admin because there are other accounts and things that are given Domain Admin privileges, but
// have no need to access the Users Database.  This restricts it to just the people that are specifically given access.
function isAdmin($user) {
	global $logon_server;

	$ds = ldap_connect($logon_server);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
	$bd = ldap_bind($ds, LDAP_USER, LDAP_PASS);
	$result = ldap_search($ds, LDAP_DN, "samaccountname=".LDAP_ADMIN_GROUP);
	$entries = ldap_get_entries($ds, $result);
	$table = $entries[0]['member'];

	$retVal = false;

	for ($i=0; $i < $table['count']; $i++) {
		if ($user == substr($table[$i], strpos($table[$i], '=')+1, strpos($table[$i], ',')-(strpos($table[$i], '=')+1))) {
			$retVal = true;
			break;
		}
	}
	return $retVal;
}

function isTeacher($user) {
	global $logon_server;

	$ds = ldap_connect($logon_server);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
	$bd = ldap_bind($ds, LDAP_USER, LDAP_PASS);
        $result = ldap_search($ds, LDAP_DN, "samaccountname=".LDAP_TEACHER_GROUP);
        $entries = ldap_get_entries($ds, $result);
        $table = $entries[0]['member'];

        $retVal = false;

        for ($i=0; $i < $table['count']; $i++) {
                if ($user == substr($table[$i], strpos($table[$i], '=')+1, strpos($table[$i], ',')-(strpos($table[$i], '=')+1))) {
                        $retVal = true;
                        break;
                }
        }
        return $retVal;
}

function isAccountLocked($user) {
	$ds = ldap_connect('dc2.kcisd.local');
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

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

/* -- END FUNCTIONS SECTION --------------------------------------------------------------------------------- */

/* -- START ERROR HANDLING SECTION -------------------------------------------------------------------------- */
/*  Create the function and set our error handler.                                                            */
/* ---------------------------------------------------------------------------------------------------------- */

function my_error_handler($num, $msg, $file, $line, $vars) {
	global $debug, $contact_email; // Globalize our variables.

	$message = "An error occurred in script '$file' on line $line: $msg"; // Build our error message.

	if (isset($_SESSION) && isset($_SESSION['uname'])) $message .= "\n\nThe error occured for ".$_SESSION['uname']."."; // Add the user who experienced the error if someone is logged in.

	if ($debug) {
		// If debug = TRUE then:
		echo '<div class="error">'.$message.'</div>'; // Show our error message for debugging purposes.
		debug_print_backtrace(); // Print a backtrace.
	} else {
		// If debug = FALSE then:
		error_log($message, 1, $contact_email); // E-mail the error to contact_email.

		if ( ($num != E_NOTICE) && ($num < 2048) ) {
			// If the error number is not a NOTICE or STRICT then show an error message.
			echo '<div class="error">A system error occurred. We apologize for the inconvenience.</div>';
		}
	}
}
set_error_handler('my_error_handler'); // Set our custom error handler to handle errors.

/* -- END ERROR HANDLING SECTION ---------------------------------------------------------------------------- */

/* -- START SESSION HANDLING SECTION ------------------------------------------------------------------------ */
/*  Create and handle user sessions.                                                                          */
/* ---------------------------------------------------------------------------------------------------------- */

if ( isset($_COOKIE[$sname]) && !isset($_SESSION) ) session_start(); // If there is a session cookie and no session, start one.

if ( isset($_SESSION) )
{
	session_cache_limiter('nocache'); // Keep pages from being cached while user is logged in.

	// Check client IP to make sure it matches what is stored in the session.  (Helps prevent session hi-jacking.)
	if ( isset($_SESSION['REMOTE_ADDR']) && !empty($_SESSION['REMOTE_ADDR']) && $_SESSION['REMOTE_ADDR'] != $_SERVER['REMOTE_ADDR'] )
	{
		logout(); // The session is apparently compromised, so log the user out.
	}
}

// If user has selected to logout, then handle logout.
if ( isset($_GET['logout']) )
{
	logout();
}

$sname = session_name(); // Get the name of the session cookie.
if ( !isset($_SESSION) ) session_start(); // If a session has not been started then start one.
$sid = ( isset($_COOKIE[$sname]) ) ? session_id($_COOKIE[$sname]) : session_id();  // Get an encrypted session identifier.
setcookie($sname, $sid, time()+86400, "/", ".".domain($_SERVER['SERVER_NAME']), false, true); // Set the .domain cookie so we can work across all sub-domains.

// Check client IP to make sure it matches what is stored in the session.
if ( isset($sname) && isset($_COOKIE[$sname]) && isset($_SESSION) && isset($_SESSION['REMOTE_ADDR']) && !empty($_SESSION['REMOTE_ADDR']) && $_SESSION['REMOTE_ADDR'] != $_SERVER['REMOTE_ADDR'] )
{
	session_destroy(); // Destroy the apparently compromised session.
	if ( isset($_COOKIE['PHPSESSID']) ) unset($_COOKIE['PHPSESSID']); // Delete the session id cookie if it is set.
	header('Location: '.BASE_URL); // Redirect the user back to the main page.
}

$_SESSION['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR']; // Put the client IP in the session to check against later to help prevent session hijacking.

// This should handle taking the person back to their last search rather than back to the main index if they have to re-login.
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != PATH.'index.php?logout' && $_SERVER['PHP_SELF'] == PATH.'login.php') $_SESSION['referer'] = $_SERVER['HTTP_REFERER'];

if ($logon_server == false) $_SESSION['error'] = "No logon servers available.  Try refreshing <b>(F5)</b> the page."; // Advise no logon servers were found.

// Try to log the user in.
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['uname']) && isset($_POST['pword']))
{
	// Log a user in.
	$ldap = ldap_connect($logon_server);
	// $ldap = ldap_connect('dc2.kcisd.local');
	ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
	$domain = 'kcisd\\';
	if ($bind = @ldap_bind($ldap, $domain.$_POST['uname'], $_POST['pword']))
	{
		/*
		if (in_array($_POST['uname'], $admins)) {
			$_SESSION['uname'] = $_POST['uname'];
			$_SESSION['fullname'] = getFullName($_POST['uname']);
		}
		*/
		if (isAdmin($_POST['uname'])) {
			$_SESSION['uname'] = $_POST['uname'];
			$_SESSION['fullname'] = getFullName($_POST['uname']);
			// $_SESSION['msg'] = "You have successfully logged in.";

			$defaults = join("",file('conf/default.php'));

			if (!file_exists('conf/'.strtolower($_POST['uname']).'.php')) {
				$fp = fopen('conf/'.strtolower($_POST['uname']).'.php', 'w');
				fputs($fp, $defaults);
				fclose($fp);

				chmod('conf/'.strtolower($_POST['uname']).'.php', 0660);
			}

		} elseif (isTeacher($_POST['uname'])) {
			$_SESSION['uname'] = $_POST['uname'];
			$_SESSION['fullname'] = getFullName($_POST['uname']);

			$defaults = join("",file('conf/default.php'));

			if (!file_exists('conf/'.strtolower($_POST['uname']).'.php')) {
                                $fp = fopen('conf/'.strtolower($_POST['uname']).'.php', 'w');
                                fputs($fp, $defaults);
                                fclose($fp);

                                chmod('conf/'.strtolower($_POST['uname']).'.php', 0660);
                        }

		}
		header('Location: '.BASE_URL);  // Send the user back to the main page and clean up the address bar now that we have handled the login.
	}
	else
	{
		$err_message = "Login failed.";
		// error_log($_POST['uname']." tried to login using password [".$_POST['pword']."] and it failed.", 1, $contact_email); // E-mail the error to contact_email.
	}
	// if (ldap_error($ldap) == 'Success') $_SESSION['msg'] = 3;
}

// Clear out inactive users.
$mysqli->query("DELETE FROM `active_users` WHERE `date` < date_sub(now(), interval 5 minute);");

if (isset($_SESSION['uname']) && isset($sname)) {
	if ($mysqli->query("SELECT count(DISTINCT `sid`) AS `count` FROM `active_users` WHERE `name` LIKE '".$_SESSION['uname']."';")->fetch_object()->count > 0) { // UPDATE
		$mysqli->query("UPDATE `active_users` SET `date`='".date('Y-m-d H:i:s')."' WHERE `sid`='".$_COOKIE[$sname]."';");
	} else { // INSERT
		$mysqli->query("INSERT INTO `active_users` (`sid`, `name`) VALUES ('".$_COOKIE[$sname]."', '".$_SESSION['uname']."');");
	}
}

if (!isset($_SESSION['uname']) && $_SERVER['PHP_SELF'] != PATH.'login.php') header("Location: ".PATH."login.php");
if (isset($_SESSION['uname']) && $_SERVER['PHP_SELF'] == PATH.'login.php') header("Location: ".PATH."index.php");

if (isset($_SESSION['uname']) && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == PATH.'login.php' && isset($_SESSION['referer']) && $_SESSION['referer'] != $_SERVER['PHP_SELF']) header("Location: ".$_SESSION['referer']);

if (isset($_SESSION) && isset($_SESSION['uname']) && file_exists('conf/'.strtolower($_SESSION['uname']).'.php')) {
	require_once('conf/'.strtolower($_SESSION['uname']).'.php');
} else {
	require_once('conf/default.php');
}

/* -- END SESSION HANDLING SECTION -------------------------------------------------------------------------- */

/* -- START SSL REDIRECT SECTION ---------------------------------------------------------------------------- */
/*  Handle redirecting to HTTPS if we are in HTTP.                                                            */
/* ---------------------------------------------------------------------------------------------------------- */

if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

/* -- END SSL REDIRECT SECTION ------------------------------------------------------------------------------ */
