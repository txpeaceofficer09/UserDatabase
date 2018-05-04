<?php require_once('inc/header.php'); ?>

<?php

if (isset($err_message)) {
	echo "<div class=\"error\">".$err_message."</div>";
}

?>

		<form action="index.php" method="POST">
			<div id="login_form">
				<div class="imgcontainer">
					<img src="images/img_avatar2.png" alt="Avatar" class="avatar">
				</div>

				<div class="container">
					<label><b>Username</b></label>
					<input type="text" placeholder="Enter Username" name="uname" required>

					<label><b>Password</b></label>
					<input type="password" placeholder="Enter Password" name="pword" required>
        
					<button type="submit">Login</button>
					<!--<input type="checkbox" checked="checked"> Remember me-->
				</div>

				<div class="container" style="background-color:#f1f1f1">
					<b>Domain:</b> KCISD<br />
					<b>Logon Server:</b> <?php echo $logon_server; ?>
					<!--<button type="button" class="cancelbtn">Cancel</button>
					<span class="psw">Forgot <a href="#">password?</a></span>-->
				</div>
			</div>
		</form>

<?php require_once('inc/footer.php'); ?>