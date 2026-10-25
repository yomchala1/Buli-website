<?php
require_once __DIR__ . '/lang.php';
$current_page = basename($_SERVER['PHP_SELF']);

// Map pages to concise dropdown titles & active status
$about_pages = [
    'about.php'   => __('about_title'),
    'mission.php' => __('nav_mission'),
    'history.php' => __('nav_history'),
    'gallery.php' => __('nav_gallery')
];
$about_active = isset($about_pages[$current_page]);
$about_label  = $about_active ? $about_pages[$current_page] : __('nav_about');

$admission_pages = [
    'admission.php' => __('nav_admission')
];
$admission_active = isset($admission_pages[$current_page]);
$admission_label  = $admission_active ? $admission_pages[$current_page] : __('nav_admission');

$academics_pages = [
    'academics.php' => __('nav_academics')
];
$academics_active = isset($academics_pages[$current_page]);
$academics_label  = $academics_active ? $academics_pages[$current_page] : __('nav_academics');

$news_pages = [
    'news.php'     => 'News & Updates',
    'research.php' => 'Research',
    'downloads.php'=> 'Downloads'
];
$news_active = isset($news_pages[$current_page]);
$news_label  = $news_active ? $news_pages[$current_page] : __('nav_news');

$services_pages = [
    'health-services.php'   => 'Health Services',
    'housing.php'           => 'Housing & Hostel',
    'career-development.php'=> 'Career Dev',
    'clubs.php'             => 'Clubs & Orgs',
    'library-services.php'  => 'Library'
];
$services_active = isset($services_pages[$current_page]);
$services_label  = $services_active ? $services_pages[$current_page] : __('nav_studentservice');
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?php echo __('college_name'); ?> | <?php echo isset($page_title) ? $page_title : __('motto'); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

    <!-- ====================================================
         TOP TICKER & ANNOUNCEMENT BAR
         ==================================================== -->
    <div class="top-ticker-bar" id="topTicker">
        <div class="top-ticker-content">
            <span class="ticker-badge">Notice</span>
            <span class="ticker-text">
                Registration for Semester I TVET Regular &amp; Evening Programs is Now Open!
            </span>
        </div>
        <div class="top-ticker-links">
            <span class="ticker-date"><?php echo date('M d, Y'); ?></span>
            <a href="news.php">Latest Updates</a>
            <a href="downloads.php">Documents</a>
            <a href="contact.php">Contact</a>
        </div>
    </div>

    <!-- ====================================================
         MAIN HEADER & STICKY NAVIGATION BAR
         ==================================================== -->
    <header class="main-header" id="mainHeader">
        <div class="header-container">
            <!-- Brand Identity -->
            <div class="logo-area">
                <a href="index.php" class="logo-link" title="<?php echo __('college_name'); ?>">
                    <div class="logo-img-wrap">
                        <img src="includes/images.jpg" alt="College Crest" class="logo-img" onerror="this.onerror=null;this.src='img.jpg';">
                    </div>
                    <div class="logo-text">
                        <h1 class="brand-title"><?php echo __('college_name'); ?></h1>
                        <p class="tagline"><?php echo __('motto'); ?></p>
                    </div>
                </a>
            </div>

            <!-- Top Utility Controls -->
            <div class="top-utils">
                <!-- Search Form -->
                <form action="search.php" method="GET" class="header-search-form" role="search">
                    <input type="text" name="q" placeholder="Search..." required class="header-search-input" aria-label="Search site">
                    <button type="submit" class="header-search-btn" title="Search" aria-label="Submit Search">Search</button>
                </form>

                <!-- Language Switcher Pill -->
                <div class="lang-switcher" aria-label="Language Selector">
                    <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>" title="English">EN</a>
                    <span class="separator">|</span>
                    <a href="?lang=am" class="<?php echo $lang === 'am' ? 'active' : ''; ?>" title="አማርኛ">አማ</a>
                </div>

                <!-- Login Portal Dropdown -->
                <div class="login-dropdown">
                    <button class="login-btn" type="button" aria-haspopup="true" aria-expanded="false">
                        Login
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a href="student_login.php" role="menuitem"><span class="icon"></span> Student Portal</a>
                        <a href="staff_login.php" role="menuitem"><span class="icon"></span> Staff Portal</a>
                        <a href="admin.php" role="menuitem"><span class="icon"></span> Admin Panel</a>
                        <a href="elearning.php" role="menuitem"><span class="icon"></span> e-Learning Hub</a>
                    </div>
                </div>

                <!-- Mobile Hamburger Button -->
                <button class="nav-toggle" id="navToggle" aria-label="Toggle Mobile Menu" aria-expanded="false">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>

        <!-- Desktop Navigation Bar -->
        <nav class="navbar" id="navbar">
            <div class="nav-container">
                <ul class="nav-links">
                    <li>
                        <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                            <?php echo __('nav_home'); ?>
                        </a>
                    </li>
                    <li class="dropdown">
                        <button class="dropbtn <?php echo $about_active ? 'active' : ''; ?>" type="button"><?php echo htmlspecialchars($about_label); ?></button>
                        <div class="dropdown-content">
                            <a href="about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">
                                <?php echo __('about_title'); ?>
                            </a>
                            <a href="mission.php" class="<?php echo $current_page == 'mission.php' ? 'active' : ''; ?>">
                                <?php echo __('nav_mission'); ?>
                            </a>
                            <a href="history.php" class="<?php echo $current_page == 'history.php' ? 'active' : ''; ?>">
                                <?php echo __('nav_history'); ?>
                            </a>
                            <a href="gallery.php" class="<?php echo $current_page == 'gallery.php' ? 'active' : ''; ?>">
                                <?php echo __('nav_gallery'); ?>
                            </a>
                        </div>
                    </li>
                    <li class="dropdown">
                        <button class="dropbtn <?php echo $admission_active ? 'active' : ''; ?>" type="button"><?php echo htmlspecialchars($admission_label); ?></button>
                        <div class="dropdown-content">
                            <a href="admission.php" class="<?php echo $current_page == 'admission.php' ? 'active' : ''; ?>">
                                Admission Overview &amp; Apply
                            </a>
                            <a href="admission.php#requirements">
                                Entry Requirements &amp; Levels
                            </a>
                            <a href="admission.php#fees">
                                Training Fees &amp; Guide
                            </a>
                        </div>
                    </li>
                    <li class="dropdown">
                        <button class="dropbtn <?php echo $academics_active ? 'active' : ''; ?>" type="button"><?php echo htmlspecialchars($academics_label); ?></button>
                        <div class="dropdown-content">
                            <a href="academics.php" class="<?php echo $current_page == 'academics.php' ? 'active' : ''; ?>">
                                Departments &amp; Curricula
                            </a>
                            <a href="academics.php#guidance">
                                Vocational Counseling Center
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="innovation.php" class="<?php echo $current_page == 'innovation.php' ? 'active' : ''; ?>">
                            <?php echo __('nav_innovation'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="offices.php" class="<?php echo $current_page == 'offices.php' ? 'active' : ''; ?>">
                            <?php echo __('nav_offices'); ?>
                        </a>
                    </li>
                    <li class="dropdown">
                        <button class="dropbtn <?php echo $news_active ? 'active' : ''; ?>" type="button"><?php echo htmlspecialchars($news_label); ?></button>
                        <div class="dropdown-content">
                            <a href="news.php" class="<?php echo $current_page == 'news.php' ? 'active' : ''; ?>">
                                News &amp; Announcements
                            </a>
                            <a href="research.php" class="<?php echo $current_page == 'research.php' ? 'active' : ''; ?>">
                                Research &amp; Innovation
                            </a>
                            <a href="downloads.php" class="<?php echo $current_page == 'downloads.php' ? 'active' : ''; ?>">
                                Download Center
                            </a>
                        </div>
                    </li>
                    <li class="dropdown">
                        <button class="dropbtn <?php echo $services_active ? 'active' : ''; ?>" type="button"><?php echo htmlspecialchars($services_label); ?></button>
                        <div class="dropdown-content">
                            <a href="health-services.php" class="<?php echo $current_page == 'health-services.php' ? 'active' : ''; ?>">Health Services</a>
                            <a href="housing.php" class="<?php echo $current_page == 'housing.php' ? 'active' : ''; ?>">Student Housing &amp; Hostel</a>
                            <a href="career-development.php" class="<?php echo $current_page == 'career-development.php' ? 'active' : ''; ?>">Career Development</a>
                            <a href="clubs.php" class="<?php echo $current_page == 'clubs.php' ? 'active' : ''; ?>">Clubs &amp; Organizations</a>
                            <a href="library-services.php" class="<?php echo $current_page == 'library-services.php' ? 'active' : ''; ?>">Library Services</a>
                        </div>
                    </li>
                    <li>
                        <a href="staff.php" class="<?php echo $current_page == 'staff.php' ? 'active' : ''; ?>">
                            <?php echo __('nav_staff'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- ====================================================
         MOBILE NAVIGATION DRAWER OVERLAY
         ==================================================== -->
    <div class="mobile-drawer-overlay" id="mobileDrawerOverlay"></div>

    <aside class="mobile-drawer" id="mobileDrawer" aria-label="Mobile Navigation Drawer">
        <div class="mobile-drawer-header">
            <div class="mobile-drawer-brand">
                <img src="includes/images.jpg" alt="College Logo" class="mobile-drawer-logo" onerror="this.onerror=null;this.src='img.jpg';">
                <div>
                    <div class="mobile-drawer-title"><?php echo __('college_name'); ?></div>
                    <div class="mobile-drawer-tagline"><?php echo __('motto'); ?></div>
                </div>
            </div>
            <button class="mobile-drawer-close" id="mobileDrawerClose" aria-label="Close Menu"></button>
        </div>

        <!-- Mobile Search -->
        <div class="mobile-drawer-search">
            <form action="search.php" method="GET">
                <input type="text" name="q" placeholder="Search website..." required class="mobile-search-input">
                <button type="submit" class="mobile-search-btn"></button>
            </form>
        </div>

        <!-- Mobile Accordion Menu -->
        <nav class="mobile-nav">
            <ul class="mobile-nav-list">
                <li>
                    <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                         <?php echo __('nav_home'); ?>
                    </a>
                </li>

                <!-- About Submenu -->
                <li class="mobile-dropdown">
                    <div class="mobile-dropdown-header">
                        <a href="about.php" class="<?php echo $about_active ? 'active' : ''; ?>"> <?php echo __('nav_about'); ?></a>
                        <button class="mobile-accordion-toggle" aria-label="Toggle Submenu"></button>
                    </div>
                    <ul class="mobile-submenu">
                        <li><a href="about.php"><?php echo __('about_title'); ?></a></li>
                        <li><a href="mission.php"><?php echo __('nav_mission'); ?></a></li>
                        <li><a href="history.php"><?php echo __('nav_history'); ?></a></li>
                        <li><a href="gallery.php"><?php echo __('nav_gallery'); ?></a></li>
                    </ul>
                </li>

                <!-- Admission Submenu -->
                <li class="mobile-dropdown">
                    <div class="mobile-dropdown-header">
                        <a href="admission.php" class="<?php echo $admission_active ? 'active' : ''; ?>"> <?php echo __('nav_admission'); ?></a>
                        <button class="mobile-accordion-toggle" aria-label="Toggle Submenu"></button>
                    </div>
                    <ul class="mobile-submenu">
                        <li><a href="admission.php">Overview &amp; Online Application</a></li>
                        <li><a href="admission.php#requirements">Entry Requirements</a></li>
                        <li><a href="admission.php#fees">Training Fees &amp; Guide</a></li>
                    </ul>
                </li>

                <!-- Academics Submenu -->
                <li class="mobile-dropdown">
                    <div class="mobile-dropdown-header">
                        <a href="academics.php" class="<?php echo $academics_active ? 'active' : ''; ?>"> <?php echo __('nav_academics'); ?></a>
                        <button class="mobile-accordion-toggle" aria-label="Toggle Submenu"></button>
                    </div>
                    <ul class="mobile-submenu">
                        <li><a href="academics.php">Departments &amp; Curricula</a></li>
                        <li><a href="academics.php#guidance">Vocational Counseling Center</a></li>
                    </ul>
                </li>

                <li>
                    <a href="innovation.php" class="<?php echo $current_page == 'innovation.php' ? 'active' : ''; ?>">
                         <?php echo __('nav_innovation'); ?>
                    </a>
                </li>
                <li>
                    <a href="offices.php" class="<?php echo $current_page == 'offices.php' ? 'active' : ''; ?>">
                         <?php echo __('nav_offices'); ?>
                    </a>
                </li>

                <!-- News Submenu -->
                <li class="mobile-dropdown">
                    <div class="mobile-dropdown-header">
                        <a href="news.php" class="<?php echo $news_active ? 'active' : ''; ?>"> <?php echo __('nav_news'); ?></a>
                        <button class="mobile-accordion-toggle" aria-label="Toggle Submenu"></button>
                    </div>
                    <ul class="mobile-submenu">
                        <li><a href="news.php">News &amp; Announcements</a></li>
                        <li><a href="research.php">Research &amp; Innovation</a></li>
                        <li><a href="downloads.php">Download Center</a></li>
                    </ul>
                </li>

                <!-- Services Submenu -->
                <li class="mobile-dropdown">
                    <div class="mobile-dropdown-header">
                        <a href="services.php" class="<?php echo $services_active ? 'active' : ''; ?>"> <?php echo __('nav_studentservice'); ?></a>
                        <button class="mobile-accordion-toggle" aria-label="Toggle Submenu"></button>
                    </div>
                    <ul class="mobile-submenu">
                        <li><a href="health-services.php">Health Services</a></li>
                        <li><a href="housing.php">Housing &amp; Hostel</a></li>
                        <li><a href="career-development.php">Career Development</a></li>
                        <li><a href="clubs.php">Clubs &amp; Organizations</a></li>
                        <li><a href="library-services.php">Library Services</a></li>
                    </ul>
                </li>

                <li>
                    <a href="staff.php" class="<?php echo $current_page == 'staff.php' ? 'active' : ''; ?>">
                         <?php echo __('nav_staff'); ?>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Mobile Drawer Footer Actions -->
        <div class="mobile-drawer-footer">
            <div class="mobile-portals">
                <a href="student_login.php" class="mobile-portal-btn student"> Student Portal</a>
                <a href="staff_login.php" class="mobile-portal-btn staff"> Staff Portal</a>
            </div>
            <div class="mobile-lang-row">
                <span>Language:</span>
                <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">English</a> |
                <a href="?lang=am" class="<?php echo $lang === 'am' ? 'active' : ''; ?>">አማርኛ</a>
            </div>
        </div>
    </aside>

    <!-- Main Content Container -->
    <main class="content-wrapper">
