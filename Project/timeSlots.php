<?php include("includes/header.php"); ?>

<h1>Manage Class Time</h1><br />
<a href="addTimeSlot.php"><p style = "font-size:30px">Add Class time</p></a><br />
<hr /><br />
<legend style="font-size:30px">Remove Class Time</legend>
<form name="removeSlot" method="post" action="removeSlot.php" >
	<table border = "2">
		<tr>
			<td><p style="font-size:30px">minutes</p></td>
			<td><p style="font-size:30px">Days of Week</p></td>
			<td><p style="font-size:30px">Start Time</p></td>
		</tr>
		<tr>
			<td>50</td>
			<td>MWF</td>
			<td>
				<label><input type="checkbox" name="t1" />08:00</label>
				<label><input type="checkbox" name="t2" />09:00</label>
				<label><input type="checkbox" name="t3" />10:00</label><br />
				<label><input type="checkbox" name="t4" />11:00</label>
				<label><input type="checkbox" name="t5" />12:00</label>
				<label><input type="checkbox" name="t6" />13:00</label>
			</td>
		</tr>
		<tr>
			<td>50</td>
			<td>MTWR</td>
			<td>
				<label><input type="checkbox" name="t7" />08:00</label>
				<label><input type="checkbox" name="t8" />09:00</label>
				<label><input type="checkbox" name="t9" />10:00</label><br />

			</td>
		</tr>
	</table>
	<p>
		<input type="submit" name="submit" value="  submit  " />
		<input type="reset" name="submit" value="  reset  "  />
	</p>
</form>

<?php include("includes/footer.php"); ?>