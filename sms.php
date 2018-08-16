<style>

input {
	display: block;
}

textarea {
	display: block;
	width: 400px;
	height: 200px;
}

</style>
<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$addr = filter_var($_POST['phone'],FILTER_SANITIZE_NUMBER_INT) . '@' . $_POST['carrier'];
	mail($addr, "SMS Alert", $_POST['message'], 'From: no-reply@kirbyvillecisd.org' . "\r\n" . 'Reply-To: no-reply@kirbyvillecisd.org' . "\r\n" . 'X-Mailer: PHP/' . phpversion());
}

$carrier = array(
	"AT&T"=>"txt.att.net",
	"Boost Mobile"=>"myboostmobile.com",
	"Cricket Wireless"=>"mms.cricketwireless.net",
	"Metro PCS"=>"mymetropcs.com",
	"Sprint"=>"messaging.sprintpcs.com",
	"T-Mobile"=>"tmomail.net",
	"Verizon"=>"vtext.com",
	"Virgin Mobile"=>"vmobl.com"
);

?>

<form action="sms.php" method="POST">
	<input type="number" maxlength="10" placeholder="Phone Number" name="phone" />
	<select name="carrier">
<?php

foreach ($carrier AS $k=>$v) {
	echo "\t\t<option value=\"$v\">$k</option>\r\n";
}

?>
	</select>
	<textarea name="message" placeholder="Message..."></textarea>
	<input type="submit" />
</form>
