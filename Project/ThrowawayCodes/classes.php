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
	
	
	
	class Faculty
	{
		private static $userName;		//string
		private static $yearsOfService;	//int
		private static $timeOfSubmission; 	//int
		private static $timePref;			//string
		private static $requiredMinHours;	//int
		
		function __construct($user, $yos, $tos, $pref, $minHours)
		{
			echo "In Faculty constructor. <br>";
			$this->username = $user;
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
			echo "FACULTY OUTPUT:<br>";
			echo "Username: $this->username <br>";
			echo "Years of service: $this->yearsOfService <br>";
			echo "Time of submission: $this->timeOfSubmission <br>";
			echo "Time preference: $this->timePref <br>";
			echo "Required minimum hours: $this->requiredMinHours <br><br>";
		}
	}
	/*
	class Room
	{
		private static $building;	//string
		private static $roomNumber; //string
		private static $roomSize;	//int
		private static $unavailableTimes;	//array of strings of format MWF/00:00, initially empty
		
		function __construct()
		{
			echo "In Room constructor. <br>";
			self::$roomSize = 0;
			self::$unavailableTimes = array();
		}
	}
	
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
	}
	
	$course = new Course;
	$faculty = new Faculty;
	$classTime = new ClassTime;
	$room = new Room;
	$scheduledCourse = new ScheduledCourse;
	
	*/
?>