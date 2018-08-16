<?php

/* ---------------------------------------------------------------------------------------------------------- */
/* Filename: header.php                                                                                       */
/* Author: James McCaughey                                                                                    */
/* E-Mail: jmccaughey@kirbyvillecisd.org                                                                      */
/*                                                                                                            */
/* This file starts the html5 and includes the CSS/JS for all the pages.                                      */
/* ---------------------------------------------------------------------------------------------------------- */

require_once('conf.inc.php'); // Require the conf.inc.php file for DB, settings, functions, variables and constants.

// For performance sake eventually need to switch to adding version number instead of a microtime stamp for cache-busting.
// $version = '1.0.0';

$version = number_format(microtime(true), 0, '', '');

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Kirbyville CISD - User Database</title>
		<link rel="stylesheet" media="screen" href="css/stylesheet.css?v=<?php echo $version; ?>" />
		<link rel="stylesheet" media="screen and (max-width: 512px) and (orientation: portrait)" href="css/mobile.css?v=<?php echo $version; ?>" />
		<link rel="stylesheet" media="screen and (max-width: 592px) and (orientation: landscape)" href="css/mobile.css?v=<?php echo $version; ?>" />
		<link rel="stylesheet" media="screen and (max-width: 960px) and (orientation: portrait)" href="css/tablet.css?v=<?php echo $version; ?>" />
		<link rel="stylesheet" media="screen and (max-width: 1024px) and (orientation: landscape)" href="css/tablet.css?v=<?php echo $version; ?>" />

		<meta property="og:title" content="Kirbyville CISD - User Database" />
		<meta property="og:image" content="/images/Kirbyville-Logo.png" />
		<meta property="og:url" content="https://kcisd-tech/login.php" />
		<meta property="og:description" content="Database used by the Kirbyville CISD Tech Team to keep track of user information." />

		<meta name="theme-color" content="#0c3c6c">
		<meta name="apple-mobile-web-app-status-bar-style" content="#0C3C6C">
		<meta name="appiconurlpath" content="https://igappblobs.azureedge.net/users/2421629/2700740">

		<link rel="manifest" href="/manifest.json" />

		<!-- <script async  src="https://cdn.rawgit.com/GoogleChrome/pwacompat/v2.0.1/pwacompat.min.js"></script> -->
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, viewport-fit=cover" />
		<script src="js/jquery-3.2.1.min.js?v=<?php echo $version; ?>"></script>
		<script src="js/jquery.blockUI.js?v=<?php echo $version; ?>"></script>
		<script src="js/javascript.js?v=<?php echo $version; ?>"></script>

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

?>
	</head>

<?php

// If the page is the index or login page then show the header otherwise show nothing.
if ($_SERVER['PHP_SELF'] == "/index.php" || $_SERVER['PHP_SELF'] == "/login.php") {
	echo "\t<body onLoad=\"noFrames();".($_SERVER['PHP_SELF'] == "/index.php" ? ' doSearch();' : '')."\">\n" .
		"\t\t<div id=\"topbar\">\n" .
		(isset($_SESSION['uname']) ? "\t\t\t<span>Welcome, ".(@$_SESSION['fullname'] ? @$_SESSION['fullname'] : @$_SESSION['uname'])."! <img src=\"images/gear_icon.png\" width=12 onClick=\"showPopup('conf.php', 360, 220);\" /> | <a href=\"?logout\">Log Out</a></span>\n" : "<span></span>") .
		(isset($_SESSION['uname']) ? "\t\t\t<a href=\"cert.php\">Install Trust Certificate</a>" : "") .
		"		</div>\n" .
		"		<div id=\"header\">\n" .
		"			<img src=\"images/Kirbyville-Logo.png\" />\n" .
		"			<span>User Database</span>\n" .
		"			<h1>Kirbyville C.I.S.D.</h1>\n" .
		"		</div>\n" .
		"\t\t<div id=\"subheader\">\n" .
		(isset($_SESSION['uname']) ? "\t\t\t<span><input type=\"text\" name=\"srch\" placeholder=\"Search\"".(isset($_GET['srch']) ? ' value="'.$_GET['srch'].'"' : '')." /> <input type=\"button\" id=\"searchbtn\" onClick=\"doSearch();\" value=\" \" /></span>\n" .
		(isset($_SESSION['uname']) && isAdmin($_SESSION['uname']) ? "\t\t\t<input value=\"New\" id=\"newbtn\" type=\"button\" onClick=\"showPopup('create.php', 340, 350);\" />\n" : "") : "") .
		(isset($_SESSION['uname']) && isAdmin($_SESSION['uname']) ? "\t\t\t<input value=\"PDF\" id=\"massPDF\" type=\"button\" onClick=\"massPDF();\" />\n" : "") .
		(isset($_SESSION['uname']) && isAdmin($_SESSION['uname']) ? "\t\t\t<input value=\"Delete\" id=\"delbtn\" type=\"button\" onClick=\"delusers();\" />\n" : "") .
		(isset($_SESSION['uname']) && isAdmin($_SESSION['uname']) ? "\t\t\t<input value=\"Toggle Active\" id=\"toggleActive\" type=\"button\" onClick=\"toggleActive();\" />\n" : "") .
		(isset($_SESSION['uname']) && isAdmin($_SESSION['uname']) ? "\t\t\t<!--<input value=\"Reset Password\" id=\"resetPass\" type=\"button\" onClick=\"resetPassword();\" />-->\n" : "") .
		"\t\t</div>\n\n" .
		"\t\t<div id=\"popup\">\n" .
		"\t\t\t<div id=\"titlebar\"><button onClick=\"hidePopup();\">X</button>Popup Window</div>\n" .
		"\t\t\t<iframe id=\"popupframe\"></iframe>\n" .
		"\t\t</div>\n";
}

?>
