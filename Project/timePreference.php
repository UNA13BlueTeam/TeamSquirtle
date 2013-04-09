<?php include("includes/facultyHeader.php"); ?>
    <h1>Pick Courses</h1>
    <div class="purpleBox">
    <form class="" id="chooseCourse" name="chooseCourse" method="post" action="dochooseCourse.php" onSubmit="return InputCheck(this)">
    <h2 for="course">Courses:</h2>
    <select style="float:left" name="course">
        <?php
        $numofCourses = 5;
        for($num = 1;$num<=$numofCourses;$num++){
        echo"<option>CS455-0".$num."</option>";
        }
        ?>
    </select><br><br><hr>
    <h2 for="time">Time Preference:</h2><br>
    <input type="radio" name="time" id="early" value="p1" />Early: before 11:00 AM<br />
    <input type="radio" name="time" id="midday"  value="p2"/>Mid-Day: 11:00 AM - 2:00 PM <br />
    <input type="radio" name="time" id="lateafternoon" value="p3" />Late Afternoon: after 2:00 PM <br />
    <input type="radio" name="time" id="noPreferences" value="p4" />No Preferences 
    <br />
    <p style="float:left">
  <input type="submit" name="submit" value="  submit  " />
	<input type="reset" name="submit" value="  reset  "  />
    </p>   
    </form>
    </div>
<?php include ('includes/footer.php');
