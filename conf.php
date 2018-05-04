<?php

require_once('inc/header.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$config = "<?php\n\n" .
			"define('SYNC_SEARCH', ".$_POST['sync_search'].");\n" .
			"define('MOBILE_SYNC_SEARCH', ".$_POST['mobile_sync_search'].");\n" .
			"define('SHOW_LOGGED_ON_USERS', ".$_POST['show_logged_on_users'].");\n" .
			"define('MOBILE_SHOW_LOGGED_ON_USERS', ".$_POST['mobile_show_logged_on_users'].");\n" .
			"\n?>";

	$fp = fopen('conf/'.strtolower($_SESSION['uname']).'.php', 'w');
	fputs($fp, $config);
	fclose($fp);

	echo "<script> hidePopup(); </script>\n";
}

?>

<form action="conf.php" method="POST">
	<div class="formblockleft">Synchronous Search</div>
	<div class="formblockright">
		<select name="sync_search">
			<option value="1"<?php echo SYNC_SEARCH ? 'selected' : ''; ?>>Yes</option>
			<option value="0"<?php echo SYNC_SEARCH ? '' : 'selected'; ?>>No</option>
		</select>
	</div>

	<div class="formblockleft">Mobile Synchronous Search</div>
	<div class="formblockright">
		<select name="mobile_sync_search">
			<option value="1"<?php echo MOBILE_SYNC_SEARCH ? 'selected' : ''; ?>>Yes</option>
			<option value="0"<?php echo MOBILE_SYNC_SEARCH ? '' : 'selected'; ?>>No</option>
		</select>
	</div>
	
	<div class="formblockleft">Show Logged On Users</div>
	<div class="formblockright">
		<select name="show_logged_on_users">
			<option value="1"<?php echo SHOW_LOGGED_ON_USERS ? 'selected' : ''; ?>>Yes</option>
			<option value="0"<?php echo SHOW_LOGGED_ON_USERS ? '' : 'selected'; ?>>No</option>
		</select>
	</div>

	<div class="formblockleft">Mobile Show Logged On Users</div>
	<div class="formblockright">
		<select name="mobile_show_logged_on_users">
			<option value="1"<?php echo MOBILE_SHOW_LOGGED_ON_USERS ? 'selected' : ''; ?>>Yes</option>
			<option value="0"<?php echo MOBILE_SHOW_LOGGED_ON_USERS ? '' : 'selected'; ?>>No</option>
		</select>
	</div>

	<div class="formblock">
		<input type="submit" value="Save" /> 
	</div>
</form>

<?php

require_once('inc/footer.php');

?>