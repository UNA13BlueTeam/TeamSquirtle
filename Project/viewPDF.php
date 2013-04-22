<?php
define('FPDF_FONTPATH','fpdf17/font/');
include("includes/db.php");
 require('fpdf17/fpdf.php');
 //require('getInfo.php');\

 $admin = 'Schedule of Department';
class PDF extends FPDF {
  function PlantTable($admin) {
   $this->Cell(40, 10, $admin, 15);
   $this->Ln();
   $this->SetXY(10, 45);
   $this->SetFont('', 'B', '10');
   $this->SetFillColor(128, 128, 128);
   $this->SetTextColor(255);
   $this->SetDrawColor(92, 92, 92);
   $this->SetLineWidth(.3);
   $this->Cell(45, 7, "Course", 1, 0, 'C', true);
   $this->Cell(45, 7, "Time", 1, 0, 'C', true);
   $this->Cell(45, 7, "Location", 1, 0, 'C', true);
   $this->Cell(45, 7, "Instructor", 1, 0, 'C', true);
   $this->Ln();
   $this->SetFillColor(224, 235, 255);
   $this->SetTextColor(0);
   $this->SetFont('');
   $fill = false;
   $host = "127.0.0.1";
   $user = "leonie";
   $pass = "4EzvHW5C";
   $db = "teamsquirtle";
   $port = 45000;
   $link = mysqli_connect($host, $user, $pass, $db, $port);
   $scheduledQuery = "SELECT * FROM scheduledCourses ORDER BY course ASC";
   $results = mysqli_query($link, $scheduledQuery);
   while($row = mysqli_fetch_assoc($results)){
    $this->Cell(45, 6, $row['course']."-".$row['section'], 'LR', 0, 'L', $fill);
    $this->Cell(45, 6, $row['timeSlot'], 'LR', 0, 'L', $fill);
    $this->Cell(45, 6, $row['roomName'], 'LR', 0, 'L', $fill);
    $adminUser = $row['facultyUser'];
    $getName = "SELECT lastName, firstName FROM users WHERE username = '$adminUser'";
    $resultGetName = mysqli_query($link, $getName);
    $name = mysqli_fetch_assoc($resultGetName);
    $this->Cell(45, 6, $name['firstName']." ".$name['lastName'], 'LR', 0, 'L', $fill);
    $this->Ln();
    $fill =! $fill;
   }
   $this->Cell(180, 0, '', 'T');
  }
 }
 $pdf = new PDF();
 $pdf->SetFont('Arial', '', 30);
 $pdf->AddPage();
 $pdf->PlantTable($admin);

 $pdf->Output();
?>