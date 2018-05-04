<?php require_once('conf.inc.php'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Kirbyville CISD - <?php echo ($_SERVER['PHP_SELF'] == '/devices.php') ? 'Devices' : 'Users'; ?></title>
		<link rel="stylesheet" type="text/css" href="stylesheet.css" />
		<link rel="stylesheet" type="text/css" media="screen and (max-width: 960px)" href="mobile.css" />
		<link rel="stylesheet" type="text/css" media="screen and (orientation: portrait)" href="mobile.css" />
		<meta charset="utf-8" />
<?php

 if (isMobile()) {
	echo "\t\t<meta name=\"viewport\" content=\"width=500px, initial-scale=0.7, user-scalable=0, viewport-fit=cover\" />\n";
} else {
	echo "\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n";
}

?>
		<script src="jquery-3.2.1.min.js"></script>
		<script src="jquery.blockUI.js"></script>
		<script src="javascript.js"></script>
<?php

if ( (SYNC_SEARCH == true && !isMobile()) || (MOBILE_SYNC_SEARCH == true && isMobile()) ) {
	echo "\t\t<script>\n\n" .
		"$(document).ready(function(){\n" .
		"\t$(\"input[name=srch]\").keyup(function(){\n" .
		"\t\tdoSearch();\n" .
		"\t});\n" .
		"});\n\n" .
		"\t\t</script>\n";
} else {
	echo "\t\t<script>\n\n" .
		"$(document).ready(function(){\n" .
		"\t$(\"input[name=srch]\").keyup(function(e){\n" .
		"\t\tif (e.which == 13) {\n" .
		"\t\t\tdoSearch();\n" .
		"\t\t}\n" .
		"\t});\n" .
		"});\n\n" .
		"\t\t</script>\n";
}
//}

?>
	</head>

<?php

if ($_SERVER['PHP_SELF'] == "/index.php" || $_SERVER['PHP_SELF'] == "/login.php") {
	echo "\t<body onLoad=\"noFrames();".($_SERVER['PHP_SELF'] == "/index.php" ? ' doSearch();' : '')."\">\n" .
		"\t\t<div id=\"topbar\">\n" .
		(isset($_SESSION['uname']) ? "\t\t\t<span>Welcome, ".(@$_SESSION['fullname'] ? @$_SESSION['fullname'] : @$_SESSION['uname'])."! <img src=\"images/gear_icon.png\" width=12 onClick=\"showPopup('conf.php', 360, 220);\" /> | <a href=\"?logout\">Log Out</a></span>\n" : "<span></span>") .
		"		</div>\n" .
		"		<div id=\"header\">\n" .
		"			<img src=\"images/Kirbyville-Logo.png\" />\n" .
		"			<span>User Database</span>\n" .
		"			<h1>Kirbyville C.I.S.D.</h1>\n" .
		"		</div>\n" .
		"\t\t<div id=\"subheader\">\n" .
		(isset($_SESSION['uname']) ? "\t\t\t<span><input type=\"text\" name=\"srch\" placeholder=\"Search\"".(isset($_GET['srch']) ? ' value="'.$_GET['srch'].'"' : '')." /> <input type=\"button\" id=\"searchbtn\" onClick=\"doSearch();\" value=\" \" /></span>\n" .
		"\t\t\t<input value=\"New\" id=\"newbtn\" type=\"button\" onClick=\"showPopup('create.php', 340, 350);\" />\n" : "") .
		"\t\t</div>\n\n" .
		"\t\t<div id=\"popup\">\n" .
		"\t\t\t<div id=\"titlebar\"><button onClick=\"hidePopup();\">X</button>Popup Window</div>\n" .
		"\t\t\t<iframe id=\"popupframe\"></iframe>\n" .
		"\t\t</div>\n";
}

?>
