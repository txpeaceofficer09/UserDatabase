<?php

require_once('inc/header.php');

if ($mysqli->query("DELETE FROM `users` WHERE `id`='".$_GET['id']."';")) {
	echo "User deleted.\n";
} else {
	echo "Errno: ".$mysqli->errno."\n";
	echo "Error: ".$mysqli->error."\n";
}

require_once('inc/footer.php');

?>
