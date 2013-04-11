<?php
	class Course
	{	
		private static $name;	//string
		private static $daySections;		//int
		private static $nightSections; 	//int
		private static $internetSections;	//int
		private static $totalSections;		//int
		private static $prereqs;			//array of Course class objects
		private static $creditHours;		//int
		private static $classSize;			//int
		private static $classType;			//char  C or L
		
		function __construct()
		{
			echo "In Course constructor. <br>";
			self::$daySections = 0;
			self::$nightSections = 0;
			self::$internetSections = 0;
			self::$prereqs = array();
			Course::setTotalSections();
		}
		
		public static function setTotalSections()
		{
			self::$totalSections = self::$daySections + self::$nightSections + self::$internetSections;
			echo "totalSections is " . self::$totalSections . "<br>";
		}
	}
	
	class Faculty
	{
		private static $firstName;	//string
		private static $lastName;	//string
		private static $email;		//string
		private static $yearsOfService;	//int
		private static $requiredMinHours;	//int
		
		function __construct()
		{
			echo "In Faculty constructor. <br>";
			self::$yearsOfService = 0;
			self::$requiredMinHours = 0;
		}
	}
	
	class ClassTime
	{
		private static $minutes;	//int
		private static $daysOfWeek;	//int
		private static $availableTimes;	//array of strings of format 00:00 listing all available teaching times for matching daysOfWeek
		
		function __construct()
		{
			echo "In  ClassTime construtor. <br>";
			self::$minutes = 0;
			self::$availableTimes = array();
		}
	}
	
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
?>