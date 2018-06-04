<?php

/* ---------------------------------------------------------------------------------------------------------- */
/* Filename: header.php                                                                                       */
/* Author: James McCaughey                                                                                    */
/* E-Mail: jmccaughey@kirbyvillecisd.org                                                                      */
/*                                                                                                            */
/* This file starts the html5 and includes the CSS/JS for all the pages.                                      */
/* ---------------------------------------------------------------------------------------------------------- */

require_once('conf.inc.php'); // Require the conf.inc.php file for DB, settings, functions, variables and constants.

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Kirbyville CISD - User Database</title>
		<link rel="stylesheet" media="screen" href="css/stylesheet.css" />
		<link rel="stylesheet" media="screen and (max-width: 512px) and (orientation: portrait)" href="css/mobile.css" />
		<link rel="stylesheet" media="screen and (max-width: 592px) and (orientation: landscape)" href="css/mobile.css" />
		<link rel="stylesheet" media="screen and (max-width: 960px) and (orientation: portrait)" href="css/tablet.css" />
		<link rel="stylesheet" media="screen and (max-width: 1024px) and (orientation: landscape)" href="css/tablet.css" />
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, viewport-fit=cover" />
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="js/jquery.blockUI.js"></script>
		<script src="js/javascript.js"></script>

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
		(isAdmin($_SESSION['uname']) ? "\t\t\t<input value=\"New\" id=\"newbtn\" type=\"button\" onClick=\"showPopup('create.php', 340, 350);\" />\n" : "") : "") .
		"\t\t</div>\n\n" .
		"\t\t<div id=\"popup\">\n" .
		"\t\t\t<div id=\"titlebar\"><button onClick=\"hidePopup();\">X</button>Popup Window</div>\n" .
		"\t\t\t<iframe id=\"popupframe\"></iframe>\n" .
		"\t\t</div>\n";
}

?>
