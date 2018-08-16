<?php

/*
ini_set('display_errors', 1);

require_once('/var/www/html/vendor/autoload.php');

$client = new Google_Client();
$client->setApplicationName("KCISD Password Self-Service");
$client->setDeveloperKey("AIzaSyDxPoiuVEKU2g_aSAvZlomg4hqUHk-RBRI");

$service = new Google_Service_Directory($client);

try {
	$results = $service->users->get("jmccaughey@kirbyvillecisd.org");
} catch(Error $ex) {
	print_r($ex->getMessage());
}

echo "<pre>";
print_r($results);
*/

/*
// $client->userApplicationDefaultCredentials();
$httpClient = $client->authorize();

$response = $httpClient->get('https://www.googleapis.com/admin/directory/v1/users/jmccaughey@kirbyvillecisd.org');

print_r($response);
*/

require_once('inc/conf.inc.php');

/*
$user = isset($_GET['user']) ? $_GET['user'] : 'jdm';

echo '<h1>'.$user.'</h1>';

echo '<plaintext>';

print_r(getUserDN($user));

echo "\n\n";

print_r(getADUser($user));
*/

$i = 0;
$emails = [];

/*
function getADUserMail($username) {
	global $logon_server;

	$ds = ldap_connect($logon_server);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
	$bd = ldap_bind($ds,LDAP_USER, LDAP_PASS);
	$filter="samaccountname=".$username;
	$result=ldap_search($ds,LDAP_DN,$filter);
	$entries=ldap_get_entries($ds, $result);
	ldap_unbind($ds);

	return $entries;
}
*/

$results = $mysqli->query("SELECT `username` FROM `users` WHERE `position` REGEXP '^[^0-9]+$';") or die("MySQL Error: ".$mysqli->error);
while ($data=$results->fetch_assoc()) {
	$user = getADUser($data['username']);
	if (isset($user[0]['mail'][0]) && isAccountLocked($data['username'])) {
		array_push($emails, $user[0]['mail'][0]);
		$i++;
		echo $i.". ".$user[0]['givenname'][0]." ".$user[0]['sn'][0]." ".$user[0]['mail'][0]."<br />";
	}
	
	// array_push($emails, $data['username']."@kirbyvillecisd.org");
}

$fp = fopen('emails.csv', 'w');
fputs($fp, join("\r\n", $emails));
fclose($fp);

?>
