-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 21, 2026 at 01:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `buli_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admissions`
--

CREATE TABLE `admissions` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `department` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `program_type` varchar(100) DEFAULT NULL,
  `program_mode` varchar(50) DEFAULT NULL,
  `file_transcript` varchar(255) DEFAULT NULL,
  `file_id_card` varchar(255) DEFAULT NULL,
  `file_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`id`, `full_name`, `email`, `phone`, `department`, `status`, `created_at`, `program_type`, `program_mode`, `file_transcript`, `file_id_card`, `file_photo`) VALUES
(2, 'xyz', 'xyz@g.c', '+25111111111', 'Automotive Technology', 'Approved', '2026-07-16 18:17:30', NULL, NULL, NULL, NULL, NULL),
(3, 'cvgsajk', 'cr7goat@gmail.com', '+25111111111', 'Information Technology', 'Approved', '2026-07-21 11:11:20', 'TVET (Level I - V)', 'Regular', 'uploads/admissions/transcript_1784632280_3945.jpg', 'uploads/admissions/id_1784632280_8655.jpg', 'uploads/admissions/photo_1784632280_2926.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_title` varchar(150) NOT NULL,
  `credit_hours` int(11) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_title`, `credit_hours`, `department`) VALUES
(1, 'IT-101', 'Introduction to Computing', 3, 'Information Technology'),
(2, 'IT-102', 'Fundamentals of Programming (C++)', 4, 'Information Technology'),
(3, 'IT-103', 'Database Management Systems', 3, 'Information Technology'),
(4, 'IT-201', 'Network Administration & Security', 4, 'Information Technology'),
(5, 'IT-202', 'Web Application Development', 3, 'Information Technology'),
(6, 'IT-203', 'Hardware & System Troubleshooting', 3, 'Information Technology'),
(7, 'IT-301', 'System Analysis & Design', 3, 'Information Technology'),
(8, 'IT-302', 'Mobile Application Programming', 4, 'Information Technology'),
(9, 'AUT-101', 'Basic Workshop Technology', 3, 'Automotive Technology'),
(10, 'AUT-102', 'Engine Overhauling & Repair', 4, 'Automotive Technology'),
(11, 'AUT-201', 'Auto Electricity & Electronics', 4, 'Automotive Technology'),
(12, 'AUT-202', 'Transmission & Brake Systems', 3, 'Automotive Technology'),
(13, 'COM-101', 'Technical English Communication', 2, 'All'),
(14, 'COM-102', 'Entrepreneurship & Job Creation', 2, 'All'),
(15, 'COM-103', 'Occupational Safety & Health (OSH)', 2, 'All');

-- --------------------------------------------------------

--
-- Table structure for table `downloads`
--

CREATE TABLE `downloads` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT 'General',
  `file_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `downloads`
--

INSERT INTO `downloads` (`id`, `title`, `description`, `category`, `file_name`, `created_at`) VALUES
(1, 'Student Application Form', 'Official form for new student admissions.', 'Application Forms', 'application_form.pdf', '2026-07-16 06:43:29'),
(2, 'TVET Level Registration Guide', 'Step-by-step guide for level registration.', 'Academic Guides', 'registration_guide.pdf', '2026-07-16 06:43:29'),
(3, 'College Academic Calendar 2026/27', 'Official academic calendar for the new year.', 'Academic Guides', 'academic_calendar_2026.pdf', '2026-07-16 06:43:29'),
(4, 'Student Code of Conduct', 'Rules, regulations and student discipline manual.', 'Regulations', 'student_conduct.pdf', '2026-07-16 06:43:29'),
(5, 'Research Publication Vol. 3', 'MGMBPTC annual research and innovation publication.', 'Publications', 'research_vol3.pdf', '2026-07-16 06:43:29'),
(6, 'Short-Course Registration Form', 'Form for enrolling in evening short-term programs.', 'Application Forms', 'short_course_form.pdf', '2026-07-16 06:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('photo','video') DEFAULT 'photo',
  `date_taken` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `type`, `date_taken`, `created_at`) VALUES
(1, 'Annual Graduation Ceremony 2026', 'Graduates celebrating at the main pavilion.', 'photo', '2026-07-01', '2026-07-16 06:43:29'),
(2, 'IT Workshop Lab Session', 'Students during a hands-on ICT practical session.', 'photo', '2026-06-15', '2026-07-16 06:43:29'),
(3, 'Innovation Exhibition 2026', 'Student projects showcased at the national TVET expo.', 'photo', '2026-05-20', '2026-07-16 06:43:29'),
(4, 'Automotive Workshop', 'Automotive trainees working on engine diagnostics.', 'photo', '2026-04-10', '2026-07-16 06:43:29'),
(5, 'College Campus Tour', 'Video overview of the college facilities and campus.', 'video', '2026-03-05', '2026-07-16 06:43:29'),
(6, 'Solar Energy Installation Training', 'Students installing a solar panel unit on campus.', 'photo', '2026-02-18', '2026-07-16 06:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `grade` varchar(5) NOT NULL,
  `grade_point` decimal(3,2) NOT NULL,
  `status` varchar(20) DEFAULT 'Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `course_id`, `semester`, `grade`, `grade_point`, `status`) VALUES
(1, 999, 1, 'Year 1, Sem I', 'A', 4.00, 'Completed'),
(2, 999, 2, 'Year 1, Sem I', 'B+', 3.50, 'Completed'),
(3, 999, 13, 'Year 1, Sem I', 'A-', 3.75, 'Completed'),
(4, 999, 15, 'Year 1, Sem I', 'A', 4.00, 'Completed'),
(5, 999, 3, 'Year 1, Sem II', 'A-', 3.75, 'Completed'),
(6, 999, 4, 'Year 1, Sem II', 'B', 3.00, 'Completed'),
(7, 999, 14, 'Year 1, Sem II', 'A+', 4.00, 'Completed'),
(8, 999, 5, 'Year 2, Sem I', 'A', 4.00, 'Completed'),
(9, 999, 6, 'Year 2, Sem I', 'B-', 2.75, 'Completed'),
(10, 999, 7, 'Year 2, Sem I', 'IP', 0.00, 'Ongoing');

-- --------------------------------------------------------

--
-- Table structure for table `news_events`
--

CREATE TABLE `news_events` (
  `id` int(11) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `title_am` varchar(255) NOT NULL,
  `content_en` text NOT NULL,
  `content_am` text NOT NULL,
  `type` enum('news','event','announcement') NOT NULL,
  `date_posted` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_events`
--

INSERT INTO `news_events` (`id`, `title_en`, `title_am`, `content_en`, `content_am`, `type`, `date_posted`, `created_at`) VALUES
(1, 'Registration Open for Semester I (2026/2027)', 'ለእረኛ (Regular) ተማሪዎች ምዝገባ ተጀምሯል (2018/2019 ዓ.ም)', 'Formal enrollment for regular TVET Levels 1 through 5 is officially open. Please submit your academic records to the Registrar office before September 15. See Admissions page.', 'ለመደበኛ የቲቪቲ (TVET) ደረጃ 1 እስከ 5 ምዝገባ በይፋ ተጀምሯል። እባክዎን የትምህርት ማስረጃዎችዎን እስከ መስከረም 15 ድረስ ለሬጅስትራር ቢሮ ያቅርቡ።', 'announcement', '2026-07-10', '2026-07-15 08:40:36'),
(2, 'Innovation Seminar Starting Soon', 'የቴክኖሎጂ ፈጠራ ሴሚናር በቅርቡ ይጀምራል', 'The Technology Transfer Office will host a 3-day workshop for students and local manufacturing SME operators starting next Tuesday in the main auditorium.', 'የቴክኖሎጂ ሽግግር ቢሮ ለተማሪዎች እና ለአገር ውስጥ አነስተኛ እና አጋዥ ማኑፋክቸሪንግ አንቀሳቃሾች የሚሆን የ3 ቀን አውደ ጥናት በሚቀጥለው ማክሰኞ በዋናው አዳራሽ ያካሂዳል።', 'announcement', '2026-07-05', '2026-07-15 08:40:36'),
(3, 'Graduation Ceremony Logistics', 'የምረቃ ስነ-ስርዓት ዝግጅት', 'The annual graduation ceremony has been scheduled for August 20, 2026, at the College Sports Pavilion. Graduating candidates must verify their academic clearances.', 'አመታዊ የምረቃ ስነ-ስርዓት ነሐሴ 20 ቀን 2026 ዓ.ም በኮሌጁ ስፖርት ፓቪሊዮን ውስጥ እንዲካሄድ ተወስኗል። ተመራቂዎች የትምህርት ማረጋገጫዎቻቸውን ማጠናቀቅ አለባቸው።', 'announcement', '2026-06-28', '2026-07-15 08:40:36'),
(4, 'Strategic Partnership Signed with Ministry of Innovation', 'ከፈጠራና ቴክኖሎጂ ሚኒስቴር ጋር ስልታዊ ስምምነት ተፈረመ', 'Our college officially signed a memorandum of understanding with the Ethiopian Ministry of Innovation and Technology (MInT) to establish a joint innovation hub inside the college grounds. The hub will grant students access to modern prototyping tools, 3D printers, and industrial design software.', 'ኮሌጃችን ከፈጠራና ቴክኖሎጂ ሚኒስቴር (MInT) ጋር በጋራ የፈጠራ ማዕከል በኮሌጁ ውስጥ ለማቋቋም የመግባቢያ ስምምነት ተፈራርሟል። ማዕከሉ ለተማሪዎች የ3ዲ ማተሚያዎችን እና የዲዛይን ሶፍትዌሮችን ጨምሮ ዘመናዊ መሣሪያዎችን ተጠቃሚ ያደርጋል።', 'news', '2026-07-12', '2026-07-15 08:40:36'),
(5, 'Continuous Skills Upgrade: Evening ICT Trainings', 'የክህሎት ማሻሻያ፡ የማታ የኮምፒውተር ስልጠናዎች', 'To address local community demands, the IT department has expanded its evening short courses. Over 150 local workers are currently enrolled in our SQL Database Management and Javascript Fundamentals certificates. Classes are held Monday to Thursday between 6:00 PM and 8:30 PM.', 'የአካባቢውን ማህበረሰብ ፍላጎት ለማሟላት የአይቲ ክፍል የማታ አጫጭር ስልጠናዎችን አስፋፍቷል። በአሁኑ ወቅት ከ150 በላይ የአካባቢው ሰራተኞች በኤስኪውኤል (SQL) እና በጃቫስክሪፕት ስልጠናዎች ላይ ተመዝግበዋል።', 'news', '2026-06-20', '2026-07-15 08:40:36'),
(6, 'Automotive Workshop Facility Upgraded', 'የሞተርና አውቶሞቲቭ ወርክሾፕ እድሳት ተደረገለት', 'Our automotive technology workshop has received state-of-the-art diagnostic machinery. The equipment, donated by industrial partners, includes electronic computer scanning diagnostics and model electric engine trainers, ensuring our curriculum remains aligned with modern automotive shifts.', 'የአውቶሞቲቭ ቴክኖሎጂ ወርክሾፓችን ዘመናዊ የምርመራ ማሽኖችን ተቀብሏል። በኢንዱስትሪ አጋሮች የተለገሱት እነዚህ መሳሪያዎች የኤሌክትሮኒክስ ስካነር ምርመራዎችን እና የኤሌክትሪክ ሞተሮችን ያካትታሉ።', 'news', '2026-05-15', '2026-07-15 08:40:36'),
(7, 'Annual Graduation Day Ceremony', 'አመታዊ የምረቃ ቀን ስነ-ስርዓት', 'Celebrating our graduates of TVET levels and certifications class of 2026.', 'የ2026 ዓ.ም የቲቪቲ ደረጃ እና ሰርተፍኬት ተመራቂዎችን በደማቅ ሁኔታ እናከብራለን።', 'event', '2026-08-20', '2026-07-15 08:40:36'),
(8, 'Short-Course Certification Examinations', 'የአጫጭር ስልጠናዎች የምስክር ወረቀት ፈተና', 'Final assessments for participants of the short-term ICT & electrical networks classes.', 'የአጭር ጊዜ የአይቲ እና የኤሌክትሪክ ኔትወርክ ተማሪዎች የመጨረሻ ፈተና አሰጣጥ።', 'event', '2026-09-10', '2026-07-15 08:40:36'),
(9, 'Semester I Classes Commencement', 'የአንደኛ ሴሚስተር ትምህርት መጀመሪያ', 'First day of academic lectures and practical laboratory classes for all enrolled students.', 'ለትምህርት ለተመዘገቡ ተማሪዎች በሙሉ የአንደኛ ሴሚስተር መደበኛ ትምህርት እና የተግባር ስልጠናዎች መጀመሪያ ቀን።', 'event', '2026-10-05', '2026-07-15 08:40:36');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `department` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `full_name`, `email`, `password_hash`, `role`, `department`, `created_at`) VALUES
(1, 'Admin User', 'admin@mgmbptc.edu.et', '$2y$10$LiPNQKk2c9eI6Y3HRrKCW.xATRwd/3t/xX.zPuQBReQOPo8.yq9hq', 'admin', 'Administration', '2026-07-16 06:43:29'),
(2, 'Tsegaye Mengistu', 'tsegaye@mgmbptc.edu.et', '$2y$10$OTMOh6nN6CAohqsir/LRROSArwnilU5h.tAkE0iRu3cX5NsoK0aGq', 'staff', 'Information Technology', '2026-07-16 06:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `student_id_no` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `admission_ref_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `full_name`, `email`, `password_hash`, `department`, `student_id_no`, `status`, `created_at`, `must_change_password`, `admission_ref_id`) VALUES
(1, 'xyz', 'xyz@g.c', '$2y$10$Dc9IZGUWXynZy83zlUGdxuRZ8bQVKL3vHKn69lon9Y7ZJH3jnUBza', 'Automotive Technology', 'MGMB/2026/0001', 'Active', '2026-07-16 18:17:49', 0, 2),
(2, 'cvgsajk', 'cr7goat@gmail.com', '$2y$10$IZ3EpZSlQTrDfsTS6y8Jwev3UXN11IyRKdWa/1R8Fn0w60/OyQB2q', 'Information Technology', 'MGMB/2026/0002', 'Active', '2026-07-21 11:12:25', 0, 3),
(999, 'Demo Student', 'student@mgmbptc.edu.et', '$2y$10$inkYfR/axK5UK7i/oLil/eNSIBQHL/19EN3Ikw4vEykwxrbR1oO86', 'Information Technology', 'MGMB/2026/0001', 'Active', '2026-07-21 11:43:18', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admissions`
--
ALTER TABLE `admissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `downloads`
--
ALTER TABLE `downloads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `news_events`
--
ALTER TABLE `news_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admissions`
--
ALTER TABLE `admissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `downloads`
--
ALTER TABLE `downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `news_events`
--
ALTER TABLE `news_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
