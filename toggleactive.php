<?php

// require_once('inc/header.php');

// if (!isAdmin($_SESSION['uname'])) header('Location: '.BASE_URL); // A non-admin has tried to access an admin only area, send them packing.

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$users = [];

if (isset($_GET['id'])) {
        $ids = explode(',', $_GET['id']);
}

print_r($ids);
/*
// if (isset($_GET['sure'])) {
//	echo "Resetting...";
	foreach ($ids AS $id) {
        	if ($id != '') {
	                $result = $mysqli->query("SELECT * FROM `users` WHERE `id`='".@$id."' LIMIT 1;");
	                $user = $result->fetch_assoc();

	                array_push($users, $user);

	                $result->free();
	        }
	}

	foreach ($users AS $user) {
		// Get Active Status from AD then change it.
//		if (isAccountLocked($user['username'])) {
//			ActivateUser($user['username']);
//			echo 'Activate User: '.$user['username'];
//		} else {
//			DeactivateUser($user['username']);
//			echo 'Deactivate User: '.$user['username'];
//		}
		echo $user['username'];
	}

	if ($mysqli->query($query)) {
//		echo "Success!";
		echo "<script>\n\nsetSearch('".$_POST['fullname']."');\nhidePopup();\n\n</script>\n";
	} else {
		// echo "Failed!\n";
		echo "Errno: ".$mysqli->errno."\n" .
		"Error: ".$mysqli->error."\n";
	}
*/
//} else {
//	echo "<p><center>Are you sure you want to reset passwords for <b>".count($ids)."</b> user(s)?</center></p>\n";
//	echo "<center><input type=\"button\" value=\"Yes\" onClick=\"showPopup('passreset.php?id=".$_GET['id']."&sure=true', 300, 125);\" /> <input type=\"button\" value=\"No\" onClick=\"hidePopup();\" /></center>\n";
//}

// require_once('inc/footer.php');

?>
