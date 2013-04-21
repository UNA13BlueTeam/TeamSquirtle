<?php include("includes/header.php"); ?>

    <h1>Pick Courses</h1>
    <!-- <div class="purpleBox"> -->
    <form class="" id="chooseCourse" name="chooseCourse" method="post" action="doPickCourses.php">
    <!-- <h2 for="course">Courses:</h2> -->
		<!-- <select style="float:left" name="course"> -->
			<?php
				$link = mysqli_connect($host, $user, $pass, $db, $port);
				
				$predef = array();
				$predefQuery = "SELECT DISTINCT courseName FROM courses";
				$predefResult = mysqli_query($link, $predefQuery);
				while($row = mysqli_fetch_row($predefResult))
				{
					array_push($predef, $row[0]);
				}
				// print_r($predef);
				echo('<table class="manage" id="coursePref">');
					echo('
							<tr>
								<th>Course</th>
								<th>Preferred Time</th>
							</tr>
						');
				$num;
				for($num = 0; $num < count($predef); $num++)
				{
					// echo "<option>".$predef[$num]."</option>";
					echo ('
							<tr>
								<td><input type="checkbox" name="course'.$num.'" value="'.$predef[$num].'"> '.$predef[$num].'</td>
								<td>
									<input type="radio" name="time'.$num.'" id="time" value="early" />Early: before 11:00 AM<br />
									<input type="radio" name="time'.$num.'" id="time"  value="midDay"/>Mid-Day: 11:00 AM - 2:00 PM <br />
									<input type="radio" name="time'.$num.'" id="time" value="lateAfternoon" />Late Afternoon: after 2:00 PM <br />
									<input type="radio" name="time'.$num.'" id="time" value="night" />Night: 6:00 PM <br />
									<input type="radio" name="time'.$num.'" id="time" value="noPreference" checked />No Preferences <br />
								</td>
							</tr>
						');
				}
				echo('<input type="hidden" name="numCourses" value="'.$num.'" />');
			?>
		</table>
		<!-- </select> -->
		<br><br><hr>
    <!-- <h2 for="time">Time Preference:</h2><br> -->
		<!-- <input type="radio" name="time" id="time" value="early" />Early: before 11:00 AM<br />
		<input type="radio" name="time" id="time"  value="midDay"/>Mid-Day: 11:00 AM - 2:00 PM <br />
		<input type="radio" name="time" id="time" value="lateAfternoon" />Late Afternoon: after 2:00 PM <br />
		<input type="radio" name="time" id="time" value="night" />Night: 6:00 PM <br />
		<input type="radio" name="time" id="time" value="noPreference" />No Preferences <br /> -->
    <p style="float:left">
		<input type="submit" name="submit" value="  submit  " />
		<input type="reset" name="submit" value="  reset  "  />
    </p>   
    </form>
    <!-- </div> -->
<?php include ('includes/footer.php'); ?>