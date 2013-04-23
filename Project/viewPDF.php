<?php
include("includes/db.php");
include("includes/global.php");

 //require('getInfo.php');\
global $host, $user, $pass, $db, $port, $deptName;
$title = 'Schedule for Department of '.$deptName;

$pdf = new PDF();
$pdf->SetFont('Arial', '', 30);
$pdf->AddPage();
$pdf->ScheduleTable($title);

$pdf->Output();
?>
