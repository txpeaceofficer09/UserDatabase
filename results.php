<?php

require_once('inc/conf.inc.php');
// require_once('inc/header.php');

// Check and make sure we are still logged in.  If not stop everything and advise the user to log back in.
if (!isset($_SESSION['uname']) && $_SERVER['PHP_SELF'] != '/login.php') die("<div id=\"error\">Your session has ended.  Please, <b><a href=\"".BASE_URL."login.php\">Login</a></b> again.</div>");

// Define all the columns to display.
$cols = array(
	'firstname'=>'First Name',
	'middlename'=>'Middle Name',
	'lastname'=>'Last Name',
	'fullname'=>'Full Name',
	'position'=>'Position',
	'lunchcode'=>'Lunch Code',
	'active'=>'Active',
	'calculatedpassword'=>'Calculated Password',
	'username'=>'Username',
	'password'=>'Password',
	'emailid'=>'E-Mail ID',
	'createdon'=>'Created On',
	//'emailaddress'=>'E-Mail Address'
);

$resultsperpage = 250;  // How many results do we show on each page?
$offset = isset($_GET['pg']) ? ($_GET['pg'] * $resultsperpage)-$resultsperpage : 0; // Define the offset for the LIMIT clause so we can display the correct page of results.

$sortdir = isset($_GET['sortdir']) ? $_GET['sortdir'] : 'ASC';  // Define the direction that results are sorted [ ASC or DESC ].
$sortarrow = ($sortdir == 'DESC') ? '<span class="sortarrow">ꜜ</span>' : '<span class="sortarrow">ꜛ</span>';  // Set the sort arrow up to be displayed later.
$_GET['sort'] = isset($_GET['sort']) ? $_GET['sort'] : 'fullname'; // Make sure we know which column to sort by.
$_GET['srch'] = isset($_GET['srch']) ? urldecode($_GET['srch']) : ''; // Make sure the search string has been setup or set it.
$_GET['pg'] = isset($_GET['pg']) ? $_GET['pg'] : 1; // Make sure we know which page of results to show.

foreach($_GET AS $k=>$v) {
	$_GET[$k] = $mysqli->real_escape_string($v);
}

if (isTeacher($_SESSION['uname']) && !isAdmin($_SESSION['uname'])) {
	// Define our SQL query to get our search results.
	$sql = "SELECT * FROM `users` WHERE ( (`firstname` LIKE '%".$_GET['srch']."%' or `middlename` LIKE '%".$_GET['srch']."%' or `lastname` LIKE '%".$_GET['srch']."%' or `fullname` LIKE '%".$_GET['srch']."%' or `username` LIKE '%".$_GET['srch']."%' or `emailid` LIKE '%".$_GET['srch']."%' or `emailaddress` LIKE '%".$_GET['srch']."%' or `position` LIKE '%".$_GET['srch']."%' or CONCAT(`firstname`, ' ', `lastname`) LIKE '%".$_GET['srch']."%' or CONCAT(`firstname`, ' ', `middlename`) LIKE '%".$_GET['srch']."%') and (concat('', `position` * 1) = `position`) ) ORDER BY `".($_GET['sort'] ? $_GET['sort'] : 'fullname')."` ".$sortdir." LIMIT ".$offset.",".$resultsperpage.";";

	// Get the number of users who match our search query and the total number of users in the database.
	$num_results = $mysqli->query("SELECT COUNT(*) AS `count` FROM `users` WHERE ( (`firstname` LIKE '%".$_GET['srch']."%' or `middlename` LIKE '%".$_GET['srch']."%' or `lastname` LIKE '%".$_GET['srch']."%' or `fullname` LIKE '%".$_GET['srch']."%' or `username` LIKE '%".$_GET['srch']."%' or `emailid` LIKE '%".$_GET['srch']."%' or `emailaddress` LIKE '%".$_GET['srch']."%' or `position` LIKE '%".$_GET['srch']."%' or CONCAT(`firstname`, ' ', `lastname`) LIKE '%".$_GET['srch']."%' or CONCAT(`firstname`, ' ', `middlename`) LIKE '%".$_GET['srch']."%') and (concat('', `position` * 1) = `position`) );")->fetch_object()->count;
	$total_results = $mysqli->query('SELECT COUNT(*) AS `count` FROM `users`;')->fetch_object()->count;
} else {
	// Define our SQL query to get our search results.
	$sql = "SELECT * FROM `users` WHERE (`firstname` LIKE '%".$_GET['srch']."%' or `middlename` LIKE '%".$_GET['srch']."%' or `lastname` LIKE '%".$_GET['srch']."%' or `fullname` LIKE '%".$_GET['srch']."%' or `username` LIKE '%".$_GET['srch']."%' or `emailid` LIKE '%".$_GET['srch']."%' or `emailaddress` LIKE '%".$_GET['srch']."%' or `position` LIKE '%".$_GET['srch']."%' or CONCAT(`firstname`, ' ', `lastname`) LIKE '%".$_GET['srch']."%' or CONCAT(`firstname`, ' ', `middlename`) LIKE '%".$_GET['srch']."%') ORDER BY `".($_GET['sort'] ? $_GET['sort'] : 'fullname')."` ".$sortdir." LIMIT ".$offset.",".$resultsperpage.";";

	// Get the number of users who match our search query and the total number of users in the database.
	$num_results = $mysqli->query("SELECT COUNT(*) AS `count` FROM `users` WHERE `firstname` LIKE '%".$_GET['srch']."%' or `middlename` LIKE '%".$_GET['srch']."%' or `lastname` LIKE '%".$_GET['srch']."%' or `fullname` LIKE '%".$_GET['srch']."%' or `username` LIKE '%".$_GET['srch']."%' or `emailid` LIKE '%".$_GET['srch']."%' or `emailaddress` LIKE '%".$_GET['srch']."%' or `position` LIKE '%".$_GET['srch']."%' or CONCAT(`firstname`, ' ', `lastname`) LIKE '%".$_GET['srch']."%' or CONCAT(`firstname`, ' ', `middlename`) LIKE '%".$_GET['srch']."%';")->fetch_object()->count;
	$total_results = $mysqli->query('SELECT COUNT(*) AS `count` FROM `users`;')->fetch_object()->count;
}

// Execute our query and return an error if things go wrong.
if (!$result = $mysqli->query($sql)) {
	echo "Errno: ".$mysqli->errno."\n";
	echo "Error: ".$mysqli->error."\n";
	exit;
}

// Time to display our pagination at the top
if ($num_results >= $resultsperpage) {
	echo "\t\t<div class=\"pagination\"><b>Page:</b>\n";

	for ($i=1;$i<=ceil($num_results/$resultsperpage);$i++) {
		if ($_GET['pg'] == $i) {
			echo "\t\t\t".(($_GET['pg'] == $i) ? '<b>['.$i.']</b>' : $i)." \n";
		} else {
			echo "\t\t\t<a href=\"javascript:changeSort('".$_GET['srch']."', '".$_GET['sort']."', '".$sortdir."', ".$i.");\">".(($_GET['pg'] == $i) ? '<b>'.$i.'</b>' : $i)."</a> \n";
		}
	}

	echo "\t\t</div>\n";
}

// echo "<form action=\"masspdf.php\"><div><input type=\"submit\" onClick=\"this.submit();\" value=\"Mass PDF\" /></div>\n";

// Start our table to display our results.
echo "\n\t\t<table>\n\t\t\t<tr>\n";
echo "<th class=\"chkbox\"></th>";
// Display our column headers we defined earlier.
foreach($cols as $key => $val) {
	echo "<th class=\"".$key."\" onClick=\"changeSort('".$_GET['srch']."', '".$key."', '".($sortdir == 'DESC' ? 'ASC' : 'DESC')."', ".($_GET['pg'] ? $_GET['pg'] : 1).");\">".($_GET['sort'] == $key ? $sortarrow : '<span class="sortarrow">&nbsp;</span>').$val."</th>";
}

echo "\t\t\t</tr>\n";

// Time to display our results.
if ($result->num_rows == 0){
	echo "<tr><td colspan=\"19\"><i><center>Nothing to see here.</center></i></td></tr>\n"; // We didn't get any results for our search so let's tell the user.
} else {
	// Process all the users in the search results.
	while ($user=$result->fetch_assoc()){
		// echo "\t\t\t<tr ".($user['active'] ? 'class="active" ' : '')."onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\">\n"; // Setup the row and get it ready to click on.
		echo "\t\t\t<tr ".((isAccountLocked($user['username']) == true) ? 'class="active" ' : '').">\n"; // Setup the row and get it ready to click on.

		// Add the user's grade level to their position [ graduation_year (grade_level) ].
		if (is_numeric($user['position']) && $user['position'] > date('Y')) {
			$y = date('m') > 5 ? 13 : 12;
			 $grade = $y - ($user['position'] - date('Y'));
			 if ($grade == 0) $grade = 'K';
			 if ($grade < 0) $grade = 'Pre-K';
			//$userpos = $user['position'].' ('.( ($y - ($user['position'] - date('Y'))) == 0 ? 'K' : ($y - ($user['position'] - date('Y'))) ).')';
			 $userpos = $user['position'].' ('.$grade.')';
		} else {
			$userpos = $user['position'];
		}

	if ($user['emailid'] == '') {
		$user['emailid'] = substr($user['emailaddress'], 0, strpos($user['emailaddress'], '@'));
		// $mysqli->query("UPDATE `users` SET `emailid`='".$user['emailid']."' WHERE `id`='".$user['id']."' LIMIT 1;");
	}
		echo "\t\t\t\t<td class=\"chkbox\"><input type=\"checkbox\" name=\"".$user['id']."\" /></td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"firstname\">".$user['firstname']."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"middlename\">".$user['middlename']."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"lastname\">".$user['lastname']."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"fullname\">".$user['fullname']."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"position\">".$userpos."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"lunchcode\">".($user['lunchcode'] ? str_pad($user['lunchcode'], 4, '0', STR_PAD_LEFT) : '')."</td>\n";
	//	echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"active\">".($user['active'] ? '<b>Yes</b>' : 'No').((isAccountLocked($user['username']) == true) ? "+" : "-")."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"active\">".((isAccountLocked($user['username']) == true) ? '<b>Yes</b>' : 'No')."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"calculatedpassword\">".$user['calculatedpassword']."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"username\">".$user['username']."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"password\">".$user['password']."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"emailid\">".$user['emailid']."</td>\n";
		echo "\t\t\t\t<td".(isAdmin($_SESSION['uname']) ? " onClick=\"showPopup('edit.php?id=".$user['id']."', 360, 480);\" " : " ")."class=\"createdon\">".$user['createdon']."</td>\n";
		echo "\t\t\t</tr>\n";
	}
}
echo "\t\t</table>\n";
// echo "</form>\n";

// Time to do the pagination at the bottom.
if ($num_results >= $resultsperpage) {
	echo "\t\t<div class=\"pagination\"><b>Page:</b>\n";

	for ($i=1;$i<=ceil($num_results/$resultsperpage);$i++) {
		if ($_GET['pg'] == $i) {
			echo "\t\t\t".(($_GET['pg'] == $i) ? '<b>['.$i.']</b>' : $i)." \n";
		} else {
			echo "\t\t\t<a href=\"javascript:changeSort('".$_GET['srch']."', '".$_GET['sort']."', '".$sortdir."', ".$i.");\">".(($_GET['pg'] == $i) ? '<b>'.$i.'</b>' : $i)."</a> \n";
		}
	}

	echo "\t\t</div>\n";
}

// Display the footer with the number of results and total number of users in the database.
echo "\t\t<footer>\n";

printf("Showing <b>%s</b> - <b>%s</b> out of <b>%s</b> results.<br />", number_format(($result->num_rows == 0 ? 0 : $offset + 1)), number_format($offset + $result->num_rows), number_format($num_results));
printf("<b>%s</b> users in database.", number_format($total_results));

echo "\t\t</footer>\n";

// require_once('inc/footer.php');

if (isset($mysqli)) $mysqli->close();

ob_end_flush();

?>
