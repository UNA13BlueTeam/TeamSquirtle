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
		private static $timesOfDay;	//array of strings of format 00:00 listing all available teaching times for matching daysOfWeek
		
		function __construct($min, $dow, $tod)
		{
			//echo "In  ClassTime construtor. <br>";
			$this->minutes = $min;
			$this->daysOfWeek = $dow;
			$this->timesOfDay = $tod;
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
			echo "Times of day: $this->timesOfDay <br><br>";
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
			echo "CLASS TIMES CLASS OUTPUT:<br>";
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