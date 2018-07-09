<?php

require_once('inc/header.php');

if (!isAdmin($_SESSION['uname'])) header('Location: '.BASE_URL); // A non-admin has tried to access an admin only area, send them packing.

$count = 0;

if ($_GET['id'] == '') echo "<script> hidePopup(); </script>\n";

if (isset($_GET['id'])) {
	$ids = explode(',', $_GET['id']);
}

if (isset($_GET['sure'])) {
	foreach ($ids AS $id) {
		if ($mysqli->query("DELETE FROM `users` WHERE `id`='".$id."';")) {
			$count++;
		}
	}

	if ($count > 0) {
		echo "<b>".$count."</b> user(s) deleted.\n";
	} else {
		echo "Errno: ".$mysqli->errno."\n";
		echo "Error: ".$mysqli->error."\n";		
	}
} else {
	echo "<p><center>Are you sure you want to delete <b>".count($ids)."</b> user(s)?</center></p>\n";
	echo "<center><input type=\"button\" value=\"Yes\" onClick=\"showPopup('delete.php?id=".$_GET['id']."&sure=true', 300, 125);\" /> <input type=\"button\" value=\"No\" onClick=\"hidePopup();\" /></center>\n";
}

require_once('inc/footer.php');

?>
