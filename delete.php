<?php

require_once('inc/header.php');

if (!isAdmin($_SESSION['uname'])) header('Location: '.BASE_URL); // A non-admin has tried to access an admin only area, send them packing.

if ($mysqli->query("DELETE FROM `users` WHERE `id`='".$_GET['id']."';")) {
	echo "User deleted.\n";
} else {
	echo "Errno: ".$mysqli->errno."\n";
	echo "Error: ".$mysqli->error."\n";
}

require_once('inc/footer.php');

?>
