<?php

require_once('inc/conf.inc.php');
require('fpdf.php');

$result = $mysqli->query("SELECT * FROM `users` WHERE `id`='".@$_GET['id']."' LIMIT 1;");
$user = $result->fetch_assoc();

$result->free();
$mysqli->close();

$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AddPage();

if (is_numeric($user['position'])) {
$pdf->SetFont('Arial','',16);
$pdf->Cell(40,10, $user['fullname'], 0, 1);
$pdf->Ln();
$pdf->MultiCell(180,7, "\t\t\t\t\t\t\t\t\t\tThis is your User Name and Password, which you have agreed not to share with anyone.  You will be held legally and monetarily responsible for any damage to hardware or software occurring from the use of the user account.", 0, 1);
$pdf->Ln();
$pdf->Cell(180,10,"Signature _____________________________________________", 0, 1);
$pdf->Cell(180,10,"Date _________________________________________________", 0, 1);
$pdf->Ln();
$pdf->Cell(180,10, "Computer Logon Information", 0, 1, 'C');
$pdf->SetFont('Arial','B',16);
$pdf->Cell(20);
$pdf->Cell(40,10, "User Name : ", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(40,10, $user['username'], 0, 1);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(20);
$pdf->Cell(40,10, "Password : ", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(40,10, $user['password'], 0, 1);
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(180,10, "Google Apps Login Information", 0, 1, 'C');
$pdf->SetFont('Arial','B',16);
$pdf->Cell(20);
$pdf->Cell(40,10, "User Name : ", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(40,10, $user['emailaddress'], 0, 1);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(20);
$pdf->Cell(70,10, "Temporary Password : ", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(40,10, "wildcatz", 0, 1);
$pdf->Ln();
$pdf->Cell(180,7,"Login to Google Apps at www.Google.com and click \"Sign In\"",0,1);
$pdf->Cell(180,7,"in the upper right corner.", 0, 0);	
} else {
$pdf->SetFont('Arial','',16);
$pdf->Cell(40,10, $user['fullname'], 0, 1);
$pdf->Ln();
$pdf->MultiCell(180,7, "\t\t\t\t\t\t\t\t\t\tThis is your User Name and Password, which you have agreed not to share with anyone.  You will be held legally and monetarily responsible for any damage to hardware or software occurring from the use of the user account.", 0, 1);
$pdf->Ln();
$pdf->Cell(180,10,"Signature _____________________________________________", 0, 1);
$pdf->Cell(180,10,"Date _________________________________________________", 0, 1);
$pdf->Ln();
$pdf->Cell(180,10, "Computer and Eduphoria Logon Information", 0, 1, 'C');
$pdf->SetFont('Arial','B',16);
$pdf->Cell(20);
$pdf->Cell(40,10, "User Name : ", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(40,10, $user['username'], 0, 1);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(20);
$pdf->Cell(40,10, "Password : ", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(40,10, $user['password'], 0, 1);
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(180,10, "Google Apps & E-Mail Logon Information", 0, 1, 'C');
$pdf->SetFont('Arial','B',16);
$pdf->Cell(20);
$pdf->Cell(40,10, "User Name : ", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(40,10, $user['emailaddress'], 0, 1);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(20);
$pdf->Cell(70,10, "Temporary Password : ", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(40,10, "kirbyvillecisd", 0, 1);
$pdf->Ln();
$pdf->Cell(180,7,"You will be asked to change the password at your first login",0,1, 'C');
$pdf->Cell(180,7,"Access Email at: http://mail.kirbyvillecisd.org", 0, 0, 'C');
}

$pdf->Output();

// require_once('inc/footer.php');

?>
