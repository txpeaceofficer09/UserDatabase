<?php

require_once('inc/header.php');

if (!isAdmin($_SESSION['uname'])) header('Location: '.BASE_URL); // A non-admin has tried to access an admin only area, send them packing.

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	foreach ($_POST AS $k=>$v) {
		$_POST[$k] = $mysqli->real_escape_string($v);
	}
	if (isset($_POST['calculatedpassword'])) {
		$query = "UPDATE `users` SET `alphakey`='".$_POST['alphakey']."', `firstname`='".$_POST['firstname']."', `middlename`='".$_POST['middlename']."', `lastname`='".$_POST['lastname']."', `fullname`='".$_POST['fullname']."', `position`='".$_POST['position']."', `lunchcode`='".$_POST['lunchcode']."', `username`='".$_POST['username']."', `password`='".$_POST['password']."', `emailid`='".$_POST['emailid']."', `emailpassword`='".$_POST['emailpassword']."', `emailaddress`='".$_POST['emailaddress']."', `room`='".$_POST['room']."', `active`='".$_POST['active']."', `calculatedpassword`='".$_POST['calculatedpassword']."' WHERE `id`='".$_GET['id']."';";
	} else {
		$query = "UPDATE `users` SET `alphakey`='".$_POST['alphakey']."', `firstname`='".$_POST['firstname']."', `middlename`='".$_POST['middlename']."', `lastname`='".$_POST['lastname']."', `fullname`='".$_POST['fullname']."', `position`='".$_POST['position']."', `lunchcode`='".$_POST['lunchcode']."', `username`='".$_POST['username']."', `password`='".$_POST['password']."', `emailid`='".$_POST['emailid']."', `emailpassword`='".$_POST['emailpassword']."', `emailaddress`='".$_POST['emailaddress']."', `room`='".$_POST['room']."', `active`='".$_POST['active']."' WHERE `id`='".$_GET['id']."';";
	}
	if ($mysqli->query($query)) {
		// echo "Success!\n";
		echo "<script>\n\nsetSearch('".$_POST['fullname']."');\nhidePopup();\n\n</script>\n";
		// echo "<script>\n\nsetSearch('".$_POST['emailid']."');\nhidePopup();\n\n</script>\n";
	} else {
		// echo "Failed!\n";
		echo "Errno: ".$mysqli->errno."\n" .
			"Error: ".$mysqli->error."\n";
	}
}

$sql = "SELECT * FROM `users` WHERE `id`='".$_GET['id']."' LIMIT 1;"; 
if (!$result = $mysqli->query($sql)) {
	echo "Errno: ".$mysqli->errno."\n";
	echo "Error: ".$mysqli->error."\n";
	exit;
}
$user = $result->fetch_assoc();

if (is_numeric($user['position'])) {
	$y = date('m') > 5 ? 13 : 12;
	$userpos = $y - ($user['position'] - date('Y'));
}

if ( (isset($userpos) && $userpos > 5 && strlen($user['calculatedpassword']) == 5) || (isset($_GET['recalc']) && $_GET['recalc'] == true) ) {
	$newpass = substr($user['firstname'], 0, 1).strtolower(substr($user['lastname'],0, -1)).rand(100,999);
}

?>

<form action="edit.php?id=<?php echo $user['id']; ?>" method="POST">
	<!--<div class="formblockleft">Alpha Key</div>
	<div class="formblockright"><input type="text" name="alphakey" value="<?php echo $user['alphakey']; ?>" /></div>-->
	<input type="hidden" name="alphakey" value="<?php echo $user['alphakey']; ?>" />

	<div class="formblockleft">First Name</div>
	<div class="formblockright"><input type="text" name="firstname" value="<?php echo $user['firstname']; ?>" /></div>

	<div class="formblockleft">Middle Name</div>
	<div class="formblockright"><input type="text" name="middlename" value="<?php echo $user['middlename']; ?>" /></div>

	<div class="formblockleft">Last Name</div>
	<div class="formblockright"><input type="text" name="lastname" value="<?php echo $user['lastname']; ?>" /></div>

	<div class="formblockleft">Full Name</div>
	<div class="formblockright"><input type="text" name="fullname" value="<?php echo $user['fullname']; ?>" /></div>

	<div class="formblockleft">Position</div>
	<div class="formblockright"><input type="text" name="position" value="<?php echo $user['position']; ?>" /></div>

	<div class="formblockleft">Lunch Code</div>
	<div class="formblockright"><input type="text" name="lunchcode" value="<?php echo $user['lunchcode']; ?>" /></div>

	<div class="formblockleft">Username</div>
	<div class="formblockright"><input type="text" name="username" value="<?php echo $user['username']; ?>" /></div>

	<div class="formblockleft">Password</div>
	<div class="formblockright"><input type="text" name="password" value="<?php echo isset($newpass) ? $newpass : $user['password']; ?>" /></div>

	<div class="formblockleft">Calculated Password</div>
	<div class="formblockright"><?php echo (isset($newpass)) ? $newpass.'<input type="hidden" name="calculatedpassword" value="'.$newpass.'" />' : $user['calculatedpassword']; ?> <input type="button" onClick="showPopup('edit.php?id=<?php echo $_GET['id']; ?>&recalc=true', 360, 480);" value="Recalculate" /></div>

	<div class="formblockleft">E-Mail ID</div>
	<div class="formblockright"><input type="text" name="emailid" value="<?php echo $user['emailid']; ?>" /></div>

	<div class="formblockleft">E-Mail Password</div>
	<div class="formblockright"><input type="text" name="emailpassword" value="<?php echo $user['emailpassword']; ?>" /></div>

	<div class="formblockleft">E-Mail Address</div>
	<div class="formblockright"><input type="text" name="emailaddress" value="<?php echo $user['emailaddress']; ?>" /></div>

	<div class="formblockleft">Created On</div>
	<div class="formblockright"><?php echo $user['createdon']; ?></div>

	<div class="formblockleft">Room</div>
	<div class="formblockright"><input type="text" name="room" value="<?php echo $user['room']; ?>" /></div>

	<!--
	<div class="formblockleft">Active</div>
	<div class="formblockright">
		<select name="active">
			<option value="1"<?php echo $user['active'] ? 'selected' : ''; ?>>Yes</option>
			<option value="0"<?php echo $user['active'] ? '' : 'selected'; ?>>No</option>
		</select>
	</div>
	-->
	<input type="hidden" name="active" value="<?php echo $user['active']; ?>" />
	
	<div class="formblock">
		<input type="submit" value="Save" /> 
		<!--<input type="button" value="PDF" onClick="window.open('pdf.php?id=<?php echo $user['id']; ?>');" />--> 
		<input type="button" value="PDF" onClick="showPopup('pdf.php?id=<?php echo $user['id']; ?>', 800, 600);" />
		<input value="Delete" type="button" onClick="showPopup('delete.php?id=<?php echo $user['id']; ?>', 300, 200);" />
	</div>
</form>

<?php require_once('inc/footer.php'); ?>
