<?php 
require('fpdf17/fpdf.php');
define('FPDF_FONTPATH','fpdf17/font/');
class PDF extends FPDF
{
	function __construct()
    {
    	parent::FPDF();
    }

    function ScheduleTable($title)
    {
        $this->Multicell(0, 11, $title, 15);
        $this->Ln();
        $this->SetXY(10, 62);
        $this->SetFont('', 'B', '10');
        $this->SetFillColor(128, 128, 128);
        $this->SetTextColor(255);
        $this->SetDrawColor(92, 92, 92);
        $this->SetLineWidth(.3);
        $this->Cell(50, 7, "Course", 1, 0, 'C', true);
        $this->Cell(130, 7, "Information", 1, 0, 'C', true);
        $this->Ln();
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica');
        $fill = false;
        global $host, $user, $pass, $db, $port;
        $link = mysqli_connect($host, $user, $pass, $db, $port);
        $scheduledQuery = "SELECT * FROM scheduledCourses ORDER BY course ASC";
        $results = mysqli_query($link, $scheduledQuery);
        while($row = mysqli_fetch_assoc($results))
        {
            $this->Cell(50, 6, $row['course'], 'LR', 0, 'L', $fill);
            $this->Cell(130, 6, "", 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill =! $fill;
            $courseQuery = "SELECT * FROM scheduledCourses WHERE course = '".$row['course']."'";
            $subresults = mysqli_query($link, $courseQuery);
            while($subrow = mysqli_fetch_assoc($subresults)){

                $adminUser = $row['facultyUser'];
                $getName = "SELECT lastName, firstName FROM users WHERE username = '$adminUser'";
                $resultGetName = mysqli_query($link, $getName);
                $name = mysqli_fetch_assoc($resultGetName);
                $this->Cell(50, 6, "", 'LR', 0, 'L', $fill);
                $this->Cell(130, 6, $subrow['section']."    ".$subrow['timeSlot']."     ".$name['firstName']." ".$name['lastName']."     ".$subrow['roomName'], 'LR', 0, 'L', $fill);
                $this->Ln();
                $fill =! $fill;
            }
        }
        $this->Cell(180, 0, '', 'T');
    }
}
?>