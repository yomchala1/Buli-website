<?php
$page_title = 'News & Events';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$title_field = ($lang === 'am') ? 'title_am' : 'title_en';
$content_field = ($lang === 'am') ? 'content_am' : 'content_en';

$announcements = [];
$events = [];
$news = [];

if ($pdo) {
    try {
        $announcements = $pdo->query("SELECT * FROM news_events WHERE type = 'announcement' ORDER BY date_posted DESC")->fetchAll();
        $events = $pdo->query("SELECT * FROM news_events WHERE type = 'event' ORDER BY date_posted ASC")->fetchAll();
        $news = $pdo->query("SELECT * FROM news_events WHERE type = 'news' ORDER BY date_posted DESC")->fetchAll();
    } catch (\PDOException $e) {
        // Fallback to static
    }
}

// Fallbacks if empty
if (empty($announcements)) {
    $announcements = [
        [
            'id' => 1,
            'date_posted' => '2026-07-10',
            'title_en' => 'Registration Open for Semester I (2026/2027)',
            'title_am' => 'ለእረኛ (Regular) ተማሪዎች ምዝገባ ተጀምሯል (2018/2019 ዓ.ም)',
            'content_en' => 'Formal enrollment for regular TVET Levels 1 through 5 is officially open. Please submit your academic records to the Registrar office before September 15. See Admissions.',
            'content_am' => 'ለመደበኛ የቲቪቲ (TVET) ደረጃ 1 እስከ 5 ምዝገባ በይፋ ተጀምሯል። እባክዎን የትምህርት ማስረጃዎችዎን እስከ መስከረም 15 ድረስ ለሬጅስትራር ቢሮ ያቅርቡ።'
        ],
        [
            'id' => 2,
            'date_posted' => '2026-07-05',
            'title_en' => 'Innovation Seminar Starting Soon',
            'title_am' => 'የቴክኖሎጂ ፈጠራ ሴሚናር በቅርቡ ይጀምራል',
            'content_en' => 'The Technology Transfer Office will host a 3-day workshop for students and local manufacturing SME operators starting next Tuesday in the main auditorium.',
            'content_am' => 'የቴክኖሎጂ ሽግግር ቢሮ ለተማሪዎች እና ለአገር ውስጥ አነስተኛ እና አጋዥ ማኑፋክቸሪንግ አንቀሳቃሾች የሚሆን የ3 ቀን አውደ ጥናት በሚቀጥለው ማክሰኞ በዋናው አዳራሽ ያካሂዳል።'
        ],
        [
            'id' => 3,
            'date_posted' => '2026-06-28',
            'title_en' => 'Graduation Ceremony Logistics',
            'title_am' => 'የምረቃ ስነ-ስርዓት ዝግጅት',
            'content_en' => 'The annual graduation ceremony has been scheduled for August 20, 2026, at the College Sports Pavilion. Graduating candidates must verify their academic clearances.',
            'content_am' => 'አመታዊ የምረቃ ስነ-ስርዓት ነሐሴ 20 ቀን 2026 ዓ.ም በኮሌጁ ስፖርት ፓቪሊዮን ውስጥ እንዲካሄድ ተወስኗል። ተመራቂዎች የትምህርት ማረጋገጫዎቻቸውን ማጠናቀቅ አለባቸው።'
        ]
    ];
}

if (empty($events)) {
    $events = [
        [
            'id' => 7,
            'date_posted' => '2026-08-20',
            'title_en' => 'Annual Graduation Day Ceremony',
            'title_am' => 'አመታዊ የምረቃ ቀን ስነ-ስርዓት',
            'content_en' => 'Celebrating our graduates of TVET levels and certifications class of 2026.',
            'content_am' => 'የ2026 ዓ.ም የቲቪቲ ደረጃ እና ሰርተፍኬት ተመራቂዎችን በደማቅ ሁኔታ እናከብራለን።'
        ],
        [
            'id' => 8,
            'date_posted' => '2026-09-10',
            'title_en' => 'Short-Course Certification Examinations',
            'title_am' => 'የአጫጭር ስልጠናዎች የምስክር ወረቀት ፈተና',
            'content_en' => 'Final assessments for participants of the short-term ICT & electrical networks classes.',
            'content_am' => 'የአጭር ጊዜ የአይቲ እና የኤሌክትሪክ ኔትወርክ ተማሪዎች የመጨረሻ ፈተና አሰጣጥ።'
        ],
        [
            'id' => 9,
            'date_posted' => '2026-10-05',
            'title_en' => 'Semester I Classes Commencement',
            'title_am' => 'የአንደኛ ሴሚስተር ትምህርት መጀመሪያ',
            'content_en' => 'First day of academic lectures and practical laboratory classes for all enrolled students.',
            'content_am' => 'ለትምህርት ለተመዘገቡ ተማሪዎች በሙሉ የአንደኛ ሴሚስተር መደበኛ ትምህርት እና የተግባር ስልጠናዎች መጀመሪያ ቀን።'
        ]
    ];
}

if (empty($news)) {
    $news = [
        [
            'id' => 4,
            'date_posted' => '2026-07-12',
            'title_en' => 'Strategic Partnership Signed with Ministry of Innovation',
            'title_am' => 'ከፈጠራና ቴክኖሎጂ ሚኒስቴር ጋር ስልታዊ ስምምነት ተፈረመ',
            'content_en' => 'Our college officially signed a memorandum of understanding with the Ethiopian Ministry of Innovation and Technology (MInT) to establish a joint innovation hub inside the college grounds. The hub will grant students access to modern prototyping tools, 3D printers, and industrial design software.',
            'content_am' => 'ኮሌጃችን ከፈጠራና ቴክኖሎጂ ሚኒስቴር (MInT) ጋር በጋራ የፈጠራ ማዕከል በኮሌጁ ውስጥ ለማቋቋም የመግባቢያ ስምምነት ተፈራርሟል። ማዕከሉ ለተማሪዎች የ3ዲ ማተሚያዎችን እና የዲዛይን ሶፍትዌሮችን ጨምሮ ዘመናዊ መሣሪያዎችን ተጠቃሚ ያደርጋል።'
        ],
        [
            'id' => 5,
            'date_posted' => '2026-06-20',
            'title_en' => 'Continuous Skills Upgrade: Evening ICT Trainings',
            'title_am' => 'የክህሎት ማሻሻያ፡ የማታ የኮምፒውተር ስልጠናዎች',
            'content_en' => 'To address local community demands, the IT department has expanded its evening short courses. Over 150 local workers are currently enrolled in our SQL Database Management and Javascript Fundamentals certificates. Classes are held Monday to Thursday between 6:00 PM and 8:30 PM.',
            'content_am' => 'የአካባቢውን ማህበረሰብ ፍላጎት ለማሟላት የአይቲ ክፍል የማታ አጫጭር ስልጠናዎችን አስፋፍቷል። በአሁኑ ወቅት ከ150 በላይ የአካባቢው ሰራተኞች በኤስኪውኤል (SQL) እና በጃቫስክሪፕት ስልጠናዎች ላይ ተመዝግበዋል።'
        ],
        [
            'id' => 6,
            'date_posted' => '2026-05-15',
            'title_en' => 'Automotive Workshop Facility Upgraded',
            'title_am' => 'የሞተርና አውቶሞቲቭ ወርክሾፕ እድሳት ተደረገለት',
            'content_en' => 'Our automotive technology workshop has received state-of-the-art diagnostic machinery. The equipment, donated by industrial partners, includes electronic computer scanning diagnostics and model electric engine trainers, ensuring our curriculum remains aligned with modern automotive shifts.',
            'content_am' => 'የአውቶሞቲቭ ቴክኖሎጂ ወርክሾፓችን ዘመናዊ የምርመራ ማሽኖችን ተቀብሏል። በኢንዱስትሪ አጋሮች የተለገሱት Equipment የኤሌክትሮኒክስ ስካነር ምርመራዎችን እና የኤሌክትሪክ ሞተሮችን ያካትታል።'
        ]
    ];
}
?>

<div class="page-header">
    <h2><?php echo __('latest_news'); ?> & Announcements</h2>
</div>

<p style="margin-bottom: 2rem;">
    Stay updated with the latest happenings, academic calendars, notice releases, and achievements of Major General Mulugeta Buli Polytechnic College.
</p>

<!-- Double column: Announcements vs News -->
<div class="grid-2">
    <!-- Notice Board Archive -->
    <div class="card notice-board" id="announcements-section">
        <h3><?php echo __('announcements'); ?></h3>
        <div class="notice-list">
            <?php foreach ($announcements as $ann): ?>
                <div class="notice-item" id="notice<?php echo $ann['id']; ?>">
                    <span class="notice-date"><?php echo date('M d, Y', strtotime($ann['date_posted'])); ?></span>
                    <h4><?php echo htmlspecialchars($ann[$title_field]); ?></h4>
                    <p style="font-size: 0.9rem; margin-top: 0.25rem;">
                        <?php echo htmlspecialchars($ann[$content_field]); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Upcoming Events List -->
    <div class="card" id="events-section">
        <h3><?php echo __('upcoming_events'); ?></h3>
        
        <?php foreach ($events as $ev): ?>
            <div style="margin-bottom: 1.25rem; border-left: 4px solid var(--primary-color); padding-left: 1rem;" id="event<?php echo $ev['id']; ?>">
                <span class="notice-date" style="font-weight: bold; color: var(--primary-color);">
                    <?php echo date('M d, Y', strtotime($ev['date_posted'])); ?>
                </span>
                <h4 style="margin: 0.25rem 0;"><?php echo htmlspecialchars($ev[$title_field]); ?></h4>
                <p style="font-size: 0.9rem;"><?php echo htmlspecialchars($ev[$content_field]); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Main News Articles Grid -->
<section class="card" style="margin-bottom: 2rem;">
    <h3>Featured Articles</h3>
    
    <div class="grid-3" style="margin-top: 1.5rem; margin-bottom: 0;">
        <?php foreach ($news as $item): ?>
            <div class="card" style="background-color: var(--bg-light);" id="news<?php echo $item['id']; ?>">
                <span class="notice-date" style="margin-bottom: 0.5rem; display: block;">
                    <?php echo date('M d, Y', strtotime($item['date_posted'])); ?>
                </span>
                <h4><?php echo htmlspecialchars($item[$title_field]); ?></h4>
                <p style="font-size: 0.9rem; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($item[$content_field]); ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
