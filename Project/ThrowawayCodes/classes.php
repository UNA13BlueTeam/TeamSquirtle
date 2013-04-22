<?php
	class Course
	{	
		private static $name;	//string
		private static $daySections;		//int
		private static $nightSections; 	//int
		private static $internetSections;	//int
		private static $totalSections;		//int
		private static $classSize;			//int
		private static $classType;			//char  C or L
		private static $creditHours;		//int
		private static $inSession;			//array of Class Times
		
		// Course constructor
		function __construct($courseName, $dsect, $nsect, $isect, $size, $type, $hours)
		{
			//echo "In Course constructor. <br>";
			$this->name = $courseName;
			$this->daySections = $dsect;
			$this->nightSections = $nsect;
			$this->internetSections = $isect;
			$this->totalSections = $dsect + $nsect;
			$this->classSize = $size;
			$this->classType = $type;
			$this->creditHours = $hours;
			$this->inSession = array();
		}
		
		// Getter
		public function __get($property) 
		{
			return $this->$property;
		}
		// Setter
		public function __set($property, $value) 
		{
			$this->$property = $value;
		}
		
		function addInSessionTimes($time)
		{
			array_push($this->inSession, $time);
			echo "Times $this->name are already in session: ";
			print_r($this->inSession);
			echo "<br>";
		}
		
		function printer()
		{
			echo "COURSE CLASS OUTPUT:<br>";
			echo "Course name: $this->name <br>";
			echo "Day sections: $this->daySections <br>";
			echo "Night sections: $this->nightSections <br>";
			echo "Internet sections: $this->internetSections <br>";
			echo "Class size: $this->classSize <br>";
			echo "Class type: $this->classType <br>";
			echo "Credit hours: $this->creditHours <br> <br>";
		}
	}
	
	class ClassTime
	{
		private static $minutes;	//int
		private static $daysOfWeek;	//string
		private static $early;	//array of strings of format 00:00 listing all available early morning teaching times for matching daysOfWeek
		private static $midDay;	//array of strings of format 00:00 listing all available mid-day teaching times for matching daysOfWeek
		private static $lateAfternoon;	//array of strings of format 00:00 listing all available late afternoon teaching times for matching daysOfWeek
		private static $night;	//array of strings of format 00:00 listing all available night teaching times for matching daysOfWeek
		
		function __construct($min, $dow, $tod)
		{
			//echo "In  ClassTime construtor. <br>";
			$this->minutes = $min;
			$this->daysOfWeek = $dow;
			$this->early = array();
			$this->midDay = array();
			$this->lateAfternoon = array();
			$this->night = array();
			
			// Split times of day into an array
			$tod = preg_split('/\s+/', trim($tod));
			
			// Separate days of week into early, mid-day, late afternoon, and night sections
			for($i = 0; $i < count($tod); $i++)
			{
				// Splits the string by the ':', then puts it back together without it.
				$temp = preg_split('/[:]/', trim($tod[$i]));
				$newString = trim($temp[0]).trim($temp[1]);
				
				// Early morning class from 00:00 - 10:59
				if(($newString > 0) and ($newString < 1100))
				{
					array_push($this->early, $tod[$i]);
				}
				
				// Mid-day class from 11:00 - 14:00
				else if(($newString >= 1100) and ($newString <= 1400))
				{
					array_push($this->midDay, $tod[$i]);
				}
				
				// Late afternoon class from 14:01 - 17:59
				else if(($newString > 1400) and ($newString < 1800))
				{
					array_push($this->lateAfternoon, $tod[$i]);
				}
				
				// Night class from 18:00 - 24:00
				else if(($newString >= 1800) and ($newString <= 2400))
				{
					array_push($this->night, $tod[$i]);
				}
			}
			
		}
		
		// Getter
		public function __get($property) 
		{
			return $this->$property;
		}
		// Setter
		public function __set($property, $value) 
		{
			$this->$property = $value;
		}
		
		function printer()
		{
			echo "CLASS TIMES CLASS OUTPUT:<br>";
			echo "Minutes: $this->minutes <br>";
			echo "Days of week: $this->daysOfWeek <br>";
			
			echo "Early time:  "; print_r($this->early);
			echo "<br>Mid-day time:  "; print_r($this->midDay);
			echo "<br>Late Afternoon time:  "; print_r($this->lateAfternoon);
			echo "<br>Night time:  "; print_r($this->night);
			echo "<br><br>";
			
		}
	}
	
	class FacultyMin
	{
		private static $userName;
		private static $requiredMinHours;
		private static $currentHours;
		
		function __construct($name, $hours)
		{
			$this->userName = $name;
			$this->requiredMinHours = $hours;
			$this->currentHours = 0;
		}
		
		// Getter
		public function __get($property) 
		{
			return $this->$property;
		}
		// Setter
		public function __set($property, $value) 
		{
			$this->$property = $value;
		}
		
		function printer()
		{
			echo "<br>FACULTY MIN OUTPUT:<br>";
			echo "Username: $this->userName <br>";
			echo "Required minimum hours: $this->requiredMinHours <br>";
			echo "Current Hours: $this->currentHours <br><br>";
		}		
	}
	
	
	
	class FacultyPref
	{
		private static $userName;		//string
		private static $yearsOfService;	//int
		private static $timeOfSubmission; 	//int
		private static $timePref;			//string
		private static $requiredMinHours;	//int
		
		function __construct($user, $yos, $tos, $pref, $minHours)
		{
			//echo "In Faculty constructor. <br>";
			$this->userName = $user;
			$this->yearsOfService = $yos;
			$this->timeOfSubmission = $tos;
			$this->timePref = $pref;
			$this->requiredMinHours = $minHours;
		}
		
		// Getter
		public function __get($property) 
		{
			return $this->$property;
		}
		// Setter
		public function __set($property, $value) 
		{
			$this->$property = $value;
		}
		
		function printer()
		{
			echo "<br>FACULTY PREF OUTPUT:<br>";
			echo "Username: $this->userName <br>";
			echo "Years of service: $this->yearsOfService <br>";
			echo "Time of submission: $this->timeOfSubmission <br>";
			echo "Time preference: $this->timePref <br>";
			echo "Required minimum hours: $this->requiredMinHours <br><br>";
		}
	}
	
	class Room
	{
		private static $roomType;	//string
		private static $roomSize;	//int
		private static $roomName;	//string
		private static $unavailableTimes;	//array of strings of format MWF/00:00, initially empty
		
		function __construct($type, $size, $name)
		{
			//echo "In Rooms constructor. <br>";
			$this->roomType = $type;
			$this->roomSize = $size;
			$this->roomName = $name;
			$this->unavailableTimes = array();
		}
		
		// Getter
		public function __get($property) 
		{
			return $this->$property;
		}
		// Setter
		public function __set($property, $value) 
		{
			$this->$property = $value;
		}
		
		function addUnavailableTimes($time)
		{
			array_push($this->unavailableTimes, $time);
			echo "Times $this->roomName is being used:";
			print_r($this->unavailableTimes);
			echo "<br>";
		}
		
		function printer()
		{
			echo "ROOMS OUTPUT:<br>";
			echo "Type: $this->roomType <br>";
			echo "Size: $this->roomSize <br>";
			echo "Name: $this->roomName <br>";
			echo "Unavailable Times: ";
			for($i = 0; $i < count($this->unavailableTimes); $i++)
			{
				echo $this->unavailableTimes[$i]."<br>";
			}
		}
	}
	/*
	class ScheduledCourse
	{
		private static $courseName;	//string
		private static $daysOfWeek;	//string MWF
		private static $timeOfDay;		//string 00:00
		private static $room;			//string
		private static $professor;		//string
		private static $prereqs;		//array of Course type
		private static $creditHours;	//int
		
		function __construct()
		{
			echo "In ScheduledCourse constructor. <br>";
			self::$creditHours = 0;
			self::$prereqs = array();
		}
	}
	
	class facultyPreferences
	{
		$professor;	//string
		$course;	//string
		$preferredTimeOfDay;	//string
		$yearsOfService;	//string
		$timeOfSubmission;	//time-date string
		$priority;	//int
		
		function __construct()
		{
			echo "In facultyPreferences constructor. <br>";
			self::$priority = -1;
		}		
	} */
?>