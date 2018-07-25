<?php

require_once('inc/header.php');

if (!isAdmin($_SESSION['uname'])) header('Location: '.BASE_URL); // A non-admin has tried to access an admin only area, send them packing.

$count = 0;

if ($_GET['id'] == '') echo "<script> hidePopup(); </script>\n";

if (isset($_GET['id'])) {
	$ids = explode(',', $_GET['id']);
}

$users = [];

foreach ($ids AS $id) {
	$users[$id] = $mysqli->query('SELECT `username` FROM `users` WHERE `id`="'.$id.'" LIMIT 1;')->fetch_object()->username;
}

if (isset($_GET['sure'])) {
	putenv('LDAPTLS_REQCERT=allow');
        $ds = ldap_connect($logon_server);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
	ldap_start_tls($ds);
        $bd = ldap_bind($ds, LDAP_USER, LDAP_PASS);

	foreach ($ids AS $id) {
		if ($id != '') {
			if ($mysqli->query("DELETE FROM `users` WHERE `id`='".$id."';")) {
				ldap_delete($ds, getUserDN($users[$id]));
				$count++;
			}
		}
	}

	if ($count > 0) {
		echo "<b>".$count."</b> user(s) deleted.\n";
	} else {
		echo "Errno: ".$mysqli->errno."\n";
		echo "Error: ".$mysqli->error."\n";
	}

	ldap_unbind($ds);
} else {
	echo "<p><center>Are you sure you want to delete <b>".count($ids)."</b> user(s)?</center></p>\n";
	echo "<center><input type=\"button\" value=\"Yes\" onClick=\"showPopup('delete.php?id=".$_GET['id']."&sure=true', 300, 125);\" /> <input type=\"button\" value=\"No\" onClick=\"hidePopup();\" /></center>\n";
}

require_once('inc/footer.php');

?>
