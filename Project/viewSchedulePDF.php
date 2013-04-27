<?php
session_start();
include("includes/db.php");
//include("includes/global.php");
include("pdf.php");

 //require('getInfo.php');\
global $host, $user, $pass, $db, $port;
$title = "Schedule for Department";

$pdf = new PDF();
$pdf->SetFont('Arial', '', 30);
$pdf->AddPage();
$pdf->ScheduleTable($title);

$pdf->Output();
?>
