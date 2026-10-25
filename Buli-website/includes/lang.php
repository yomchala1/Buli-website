<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default language is English
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Handle language toggle via GET request
if (isset($_GET['lang'])) {
    $requested_lang = $_GET['lang'];
    if ($requested_lang === 'am' || $requested_lang === 'en') {
        $_SESSION['lang'] = $requested_lang;
    }
    // Redirect back to clean URL if possible, otherwise page will just reload with session set
    $uri = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: " . $uri);
    exit;
}

$lang = $_SESSION['lang'];

// Translation mapping dictionary
$translations = [
    'en' => [
        'college_name' => 'Major General Mulugeta Buli Polytechnic College',
        'college_short' => 'M.G.M.B.P.T.C',
        'motto' => 'Shine Through Education and Technology',
        
        // Navigation links
        'nav_home' => 'Home',
        'nav_about' => 'About Us',
        'nav_academics' => 'Academics',
        'nav_admission' => 'Admissions',
        'nav_news' => 'News & Events',
        'nav_research' => 'Research',
        'nav_staff' => 'Staff Directory',
        'nav_services' => 'Student Services',
        'nav_contact' => 'Contact Us',
        'nav_gallery' => 'Gallery',
        'nav_download' => 'Downloads',
        'nav_mission' => 'Mission & Vision',
        'nav_history' => 'History & Profile',
        'nav_studentservice' => 'Student Services',
        'nav_innovation' => 'Innovation & Extension',
        'nav_offices' => 'Offices & Directorates',
        'nav_incubation' => 'Incubation Center',
        'nav_production' => 'Production Unit (WBL)',
        
        // General text & CTA Buttons
        'btn_read_more' => 'Read More',
        'btn_apply' => 'Apply Online',
        'btn_submit' => 'Submit',
        'btn_send' => 'Send Message',
        'btn_explore' => 'Explore Program',
        'btn_visit' => 'Visit Hub',
        
        // Home page & Benchmark Pillars
        'hero_title' => 'Welcome to Major General Mulugeta Buli Polytechnic College',
        'hero_desc' => 'Empowering youth through high-quality technical and vocational training, technological innovation, and industry-oriented production.',
        'announcements' => 'Announcements & Notice Board',
        'latest_news' => 'Latest News & Events',
        'upcoming_events' => 'Upcoming Events',
        'quick_links' => 'Quick Links',
        'core_pillars' => 'Core Institutional Pillars',
        'tvet_training_title' => 'Technical & Vocational Training',
        'tvet_training_desc' => 'Offering market-driven TVET Level I to V certification and short-term capacity building courses.',
        'incubation_title' => 'Incubation & Startup Hub',
        'incubation_desc' => 'Nurturing student innovations, prototypes, and technical entrepreneurship in engineering & ICT.',
        'production_title' => 'Production & Trade Unit',
        'production_desc' => 'Work-Based Learning (WBL) production center offering commercial and community technical services.',
        'departments_title' => 'Academic Departments',
        'testimonials_title' => 'Student & Trainee Testimonials',
        'partners_title' => 'Our Key Partners & Collaborators',
        
        // About & Offices
        'about_title' => 'About Our College',
        'mission' => 'Mission',
        'vision' => 'Vision',
        'core_values' => 'Core Values',
        'offices_title' => 'Institutional Leadership & Directorates',
        'dean_office' => 'Office of the Dean',
        'vice_deans' => 'Vice Deans',
        'directorates' => 'Directorates & Administration',
        
        // Contact page
        'contact_title' => 'Contact Us',
        'address' => 'Address',
        'phone' => 'Phone',
        'email' => 'Email',
        'name_label' => 'Full Name',
        'subject_label' => 'Subject',
        'message_label' => 'Message'
    ],
    'am' => [
        'college_name' => 'ሜጀር ጄኔራል ሙሉጌታ ቡሊ ፖሊቴክኒክ ኮሌጅ',
        'college_short' => 'ሜ.ጄ.ሙ.ቡ.ፖ.ኮ',
        'motto' => 'በትምህርት እና በቴክኖሎጂ እንበራለን',
        
        // Navigation links
        'nav_home' => 'ዋና ገጽ',
        'nav_about' => 'ስለ ኮሌጁ',
        'nav_academics' => 'አካዳሚክስ',
        'nav_admission' => 'ምዝገባ እና መግቢያ',
        'nav_news' => 'ዜና እና ሁነቶች',
        'nav_research' => 'ምርምርና ፈጠራ',
        'nav_staff' => 'የሰራተኞች ማውጫ',
        'nav_services' => 'የተማሪዎች አገልግሎት',
        'nav_contact' => 'አድራሻችን',
        'nav_gallery' => 'ማዕከለ-ስዕላት',
        'nav_download' => 'ማውረጃዎች',
        'nav_mission' => 'ተልዕኮ እና ራዕይ',
        'nav_history' => 'ታሪክ እና መግለጫ',
        'nav_studentservice' => 'የተማሪዎች አገልግሎት',
        'nav_innovation' => 'ኢኖቬሽን እና ኢንዱስትሪ ሽግግር',
        'nav_offices' => 'ቢሮዎች እና ዳይሬክቶሬቶች',
        'nav_incubation' => 'የፈጠራ ማዕከል',
        'nav_production' => 'የምርት ማዕከል (WBL)',
        
        // General text & CTA Buttons
        'btn_read_more' => 'ተጨማሪ ያንብቡ',
        'btn_apply' => 'በኦንላይን ያመልክቱ',
        'btn_submit' => 'አስገባ',
        'btn_send' => 'መልዕክት ላክ',
        'btn_explore' => 'ክፍሉን ይጎብኙ',
        'btn_visit' => 'ማዕከሉን ይጎብኙ',
        
        // Home page & Benchmark Pillars
        'hero_title' => 'እንኳን ወደ ሜጀር ጄኔራል ሙሉጌታ ቡሊ ፖሊቴክኒክ ኮሌጅ በደህና መጡ',
        'hero_desc' => 'ወጣቶችን በከፍተኛ የቴክኒክ እና ሙያ ስልጠና፣ በቴክኖሎጂ ፈጠራ እና በምርት ስራዎች ማብቃት።',
        'announcements' => 'ማስታወቂያዎች እና የማስታወቂያ ሰሌዳ',
        'latest_news' => 'የቅርብ ጊዜ ዜናዎች እና ሁነቶች',
        'upcoming_events' => 'የሚመጡ ሁነቶች',
        'quick_links' => 'ፈጣን ሊንኮች',
        'core_pillars' => 'የኮሌጁ ዋና ዋና ዘበኞች',
        'tvet_training_title' => 'የቴክኒክ እና ሙያ ስልጠና',
        'tvet_training_desc' => 'ከደረጃ 1 እስከ 5 የመደበኛ እና የአጭር ጊዜ የክህሎት ማሻሻያ ስልጠናዎች።',
        'incubation_title' => 'የቴክኖሎጂ እና የፈጠራ ማዕከል',
        'incubation_desc' => 'የተማሪዎችን የፈጠራ ስራዎች እና የቴክኖሎጂ ስራ ፈጠራ ፕሮጀክቶችን ማበልፀጊያ።',
        'production_title' => 'የምርት እና የአገልግሎት ማዕከል',
        'production_desc' => 'በተግባር ላይ የተመሰረተ (WBL) የዕቃ ምርት እና የማህበረሰብ ቴክኒክ አገልግሎቶች።',
        'departments_title' => 'የስልጠና ክፍሎች',
        'testimonials_title' => 'የተማሪዎች እና ሰልጣኞች አስተያየት',
        'partners_title' => 'የስልታዊ አጋሮቻችን',
        
        // About & Offices
        'about_title' => 'ስለ ኮሌጃችን',
        'mission' => 'ተልዕኮ',
        'vision' => 'ራዕይ',
        'core_values' => 'ዕሴቶች',
        'offices_title' => 'የኮሌጁ አመራር እና ዳይሬክቶሬቶች',
        'dean_office' => 'የዲን ጽህፈት ቤት',
        'vice_deans' => 'ምክትል ዲኖች',
        'directorates' => 'ዳይሬክቶሬቶች',
        
        // Contact page
        'contact_title' => 'ያግኙን',
        'address' => 'አድራሻ',
        'phone' => 'ስልክ',
        'email' => 'ኢሜይል',
        'name_label' => 'ሙሉ ስም',
        'subject_label' => 'ርዕሰ ጉዳይ',
        'message_label' => 'መልዕክት'
    ]
];


// Translation Helper function
function __($key) {
    global $translations, $lang;
    return $translations[$lang][$key] ?? $translations['en'][$key] ?? $key;
}
?>
