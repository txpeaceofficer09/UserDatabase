<?php

require_once('inc/header.php');

echo "\t\t<section>&nbsp;</section>\n";


if ( (SHOW_LOGGED_ON_USERS == true && !isMobile()) || (MOBILE_SHOW_LOGGED_ON_USERS == true && isMobile()) ) {
	$result = $mysqli->query("SELECT `name` FROM `active_users` WHERE `date` > date_sub(now(), interval 5 minute);");
	while ($row=$result->fetch_assoc()) {
		if (!isset($active_users)) {
			$active_users = '<a href="mailto:'.$row['name'].'@kirbyvillecisd.org">'.$row['name'].'</a>';
		} else {
			$active_users .= ', <a href="mailto:'.$row['name'].'@kirbyvillecisd.org">'.$row['name'].'</a>';
		}
	}

	echo "\t\t<footer><h3>Active Users:</h3>".( isset($active_users) ? $active_users : "<i>No Active Users</i>")."</footer>\n";
}

require_once('inc/footer.php');

?>