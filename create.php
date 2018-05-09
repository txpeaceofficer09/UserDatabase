<?php

require_once('inc/header.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	foreach ($_POST AS $k=>$v) {
		$_POST[$k] = $mysqli->real_escape_string($v);
	}
	if ( is_numeric($_POST['position']) && $_POST['position'] - date('Y') > 7 ) {
		// User is a student in 5th or lower.
		$calculatedpassword = strtolower(substr($_POST['lastname'], 0, 1)).$_POST['lunchcode'];
	} else {
		// User is not a student or is not a student in 5th or lower.
		$calculatedpassword = substr($_POST['firstname'], 0, 1).strtolower(substr($_POST['lastname'],0, -1)).rand(100,999);		
	}
	if ($_POST['middlename'] != '') {
		$fullname = $_POST['firstname']." ".$_POST['middlename']." ".$_POST['lastname'];
	} else {
		$fullname = $_POST['firstname']." ".$_POST['lastname'];
	}
	$_POST['emailid'] = str_replace(' ', '', $_POST['emailid']); // Do not allow spaces in e-mail ID.
	
	if ($_POST['password'] == '' or $_POST['password'] == '0' ) $_POST['password'] = $calculatedpassword;

	if ($mysqli->query("SELECT count(*) AS `count` FROM `positions` WHERE `position` LIKE '".$_POST['position']."' LIMIT 1")->fetch_object()->count != 1) {
		$mysqli->query("INSERT INTO `positions` (`position`) VALUES ('".$_POST['position']."');");
	}
	
	if ($mysqli->query("INSERT INTO `users` (`firstname`, `middlename`, `lastname`, `fullname`, `position`, `lunchcode`, `username`, `password`, `calculatedpassword`, `emailid`, `emailaddress`, `createdon`, `room`, `active`) VALUES ('".$_POST['firstname']."', '".$_POST['middlename']."', '".$_POST['lastname']."', '".$fullname."', '".$_POST['position']."', '".$_POST['lunchcode']."', '".$_POST['username']."', '".$_POST['password']."', '".$calculatedpassword."', '".$_POST['emailid']."', '".$_POST['emailid']."@kirbyvillecisd.org', '".date('Y-m-d')."', '".$_POST['room']."', '".$_POST['active']."');")) {
		// echo "Success!";
		echo "<script>\n\nsetSearch('".$fullname."');\nhidePopup();\n\n</script>\n";
	} else {
		echo "Errno: ".$mysqli->errno."\n";
		echo "Error: ".$mysqli->error."\n";
	}
}

?>

<form action="create.php" method="POST">
	<div class="formblockleft">First Name</div>
	<div class="formblockright"><input type="text" name="firstname" /></div>

	<div class="formblockleft">Middle Name</div>
	<div class="formblockright"><input type="text" name="middlename" /></div>

	<div class="formblockleft">Last Name</div>
	<div class="formblockright"><input type="text" name="lastname" /></div>

	<div class="formblockleft">Position</div>
	<div class="formblockright">
		<input type="text" name="position" list="positions" />
		<datalist id="positions">
<?php

$result = $mysqli->query("SELECT `position` FROM `positions` ORDER BY `position` ASC;");
while ($row=$result->fetch_assoc()) {
	if (!is_numeric($row['position'])) {
		echo "\t\t\t<option>".$row['position']."</option>\n";
	}
}

?>
		</datalist>
	</div>

	<div class="formblockleft">Lunch Code</div>
	<div class="formblockright"><input type="text" name="lunchcode" /></div>

	<div class="formblockleft">Username</div>
	<div class="formblockright"><input type="text" name="username" /></div>

	<div class="formblockleft">Password</div>
	<div class="formblockright"><input type="text" name="password" /></div>

	<div class="formblockleft">E-Mail ID</div>
	<div class="formblockright"><input type="text" name="emailid" /></div>

	<div class="formblockleft">Room</div>
	<div class="formblockright"><input type="text" name="room" /></div>

	<!--
	<div class="formblockleft">Active</div>
	<div class="formblockright">
		<select name="active">
			<option value="1">Yes</option>
			<option value="0">No</option>
		</select>
	</div>
	-->
	<input type="hidden" name="active" value="1" />
	
	<div class="formblock"><input type="submit" value="Save" /></div>
</form>

<?php require_once('inc/footer.php'); ?>
