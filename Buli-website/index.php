<?php
$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

/** @var string $lang */
$current_lang = $lang ?? 'en';
$title_field = ($current_lang === 'am') ? 'title_am' : 'title_en';
$content_field = ($current_lang === 'am') ? 'content_am' : 'content_en';

$announcements = [];
$news = [];

/** @var PDO|null $pdo */
if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $ann_stmt = $pdo->query("SELECT * FROM news_events WHERE type = 'announcement' ORDER BY date_posted DESC LIMIT 3");
        $announcements = $ann_stmt->fetchAll();

        $news_stmt = $pdo->query("SELECT * FROM news_events WHERE type = 'news' ORDER BY date_posted DESC LIMIT 3");
        $news = $news_stmt->fetchAll();
    } catch (\PDOException $e) {
        // Fallback to static
    }
}

// Fallback static arrays if database query returns empty
if (empty($announcements)) {
    $announcements = [
        ['id' => 1, 'date_posted' => '2026-07-10', 'title_en' => 'Registration Open for Semester I (2026/2027)', 'title_am' => 'ለእረኛ (Regular) ተማሪዎች ምዝገባ ተጀምሯል (2018/2019 ዓ.ም)'],
        ['id' => 2, 'date_posted' => '2026-07-05', 'title_en' => 'Workshop on Innovation & Technology Transfer starts next Tuesday.', 'title_am' => 'የቴክኖሎጂ ፈጠራ ሴሚናር በቅርቡ ይጀምራል'],
        ['id' => 3, 'date_posted' => '2026-06-28', 'title_en' => 'Graduation ceremony date and venue updates.', 'title_am' => 'የምረቃ ስነ-ስርዓት ዝግጅት'],
    ];
}

if (empty($news)) {
    $news = [
        ['id' => 1, 'date_posted' => '2026-07-12', 'title_en' => 'College Partners with Ministry of Innovation', 'title_am' => 'ከፈጠራና ቴክኖሎጂ ሚኒስቴር ጋር ስልታዊ ስምምነት ተፈረመ', 'content_en' => 'MGMBPTC has signed a new MOU with the Ministry of Innovation and Technology to support student startups and hardware prototyping.', 'content_am' => 'ኮሌጃችን ከፈጠራና ቴክኖሎጂ ሚኒስቴር (MInT) ጋር ስምምነት ተፈራርሟል።'],
        ['id' => 2, 'date_posted' => '2026-06-20', 'title_en' => 'Evening ICT Short-Course Registrations Now Open', 'title_am' => 'የክህሎት ማሻሻያ፡ የማታ የኮምፒውተር ስልጠናዎች', 'content_en' => 'Registration for evening programs in Database Management, Web Development, and Network Administration is now open.', 'content_am' => 'የአካባቢውን ማህበረሰብ ፍላጎት ለማሟላት የአይቲ ክፍል የማታ አጫጭር ስልጠናዎችን አስፋፍቷል።'],
        ['id' => 3, 'date_posted' => '2026-05-15', 'title_en' => 'Automotive Department Receives Advanced Lab Equipment', 'title_am' => 'የሞተርና አውቶሞቲቭ ወርክሾፕ እድሳት ተደረገለት', 'content_en' => 'Our automotive workshops received advanced vehicle diagnostic units and electric engines donated by industry partners.', 'content_am' => 'የአውቶሞቲቭ ቴክኖሎጂ ወርክሾፓችን ዘመናዊ የምርመራ ማሽኖችን ተቀብሏል።'],
    ];
}
?>

<!-- ===================================================
     HERO IMAGE SLIDER (AATPTC Benchmark Style)
     =================================================== -->
<section class="hero-slider-container">
    <div class="hero-slider" id="heroSlider">
        <!-- Slide 1 -->
        <div class="hero-slide active" style="background: linear-gradient(135deg, rgba(26,61,31,0.92) 0%, rgba(30,77,36,0.85) 60%, rgba(13,115,119,0.85) 100%), url('includes/images.jpg') center/cover;">
            <div class="hero-slide-content">
                <span class="hero-slide-badge"> Official TVET Polytechnic College</span>
                <h1><?php echo __('hero_title'); ?></h1>
                <p><?php echo __('hero_desc'); ?></p>
                <div class="hero-cta-group">
                    <a href="admission.php" class="btn-hero-primary"> <?php echo __('btn_apply'); ?></a>
                    <a href="academics.php" class="btn-hero-secondary"> Explore Academics</a>
                    <a href="innovation.php" class="btn-hero-secondary"> <?php echo __('nav_incubation'); ?></a>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="hero-slide" style="background: linear-gradient(135deg, rgba(13,115,119,0.92) 0%, rgba(26,68,128,0.88) 60%, rgba(26,61,31,0.92) 100%), url('includes/images.jpg') center/cover;">
            <div class="hero-slide-content">
                <span class="hero-slide-badge"> Innovation &amp; Technology Hub</span>
                <h1>State-of-the-Art Prototyping &amp; Technology Transfer</h1>
                <p>Fostering student startups, hardware engineering, and commercial production supported by the Ministry of Innovation and Technology (MInT).</p>
                <div class="hero-cta-group">
                    <a href="innovation.php#incubation" class="btn-hero-primary"> Incubation Center</a>
                    <a href="innovation.php#production" class="btn-hero-secondary"> Production Unit</a>
                </div>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="hero-slide" style="background: linear-gradient(135deg, rgba(201,162,39,0.92) 0%, rgba(192,86,33,0.88) 60%, rgba(26,61,31,0.92) 100%), url('includes/images.jpg') center/cover;">
            <div class="hero-slide-content">
                <span class="hero-slide-badge"> Industry Extension &amp; Linkages</span>
                <h1>Direct Pathway from Training to Industry Employment</h1>
                <p>Establishing MOUs with top manufacturing firms, enterprise mentorship, and hands-on apprenticeship programs for TVET trainees.</p>
                <div class="hero-cta-group">
                    <a href="academics.php#guidance" class="btn-hero-primary">Vocational Guidance</a>
                    <a href="offices.php" class="btn-hero-secondary"> Institutional Offices</a>
                </div>
            </div>
        </div>

        <!-- Slide 4 -->
        <div class="hero-slide" style="background: linear-gradient(135deg, rgba(26,68,128,0.92) 0%, rgba(30,77,36,0.88) 60%, rgba(201,162,39,0.92) 100%), url('includes/images.jpg') center/cover;">
            <div class="hero-slide-content">
                <span class="hero-slide-badge"> Semester I Enrollment Open</span>
                <h1>National TVET Certification — Level I to Level V</h1>
                <p>Register now for regular daytime classes, evening extension courses, or customized short-term industry up-skilling programs.</p>
                <div class="hero-cta-group">
                    <a href="admission.php" class="btn-hero-primary"> Online Registration</a>
                    <a href="admission.php#fees" class="btn-hero-secondary"> View Fees &amp; Calendar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Slider Navigation Buttons -->
    <button class="slider-prev" onclick="moveSlide(-1)" aria-label="Previous Slide" style="display:none;"></button>
    <button class="slider-next" onclick="moveSlide(1)" aria-label="Next Slide" style="display:none;"></button>

    <!-- Slider Dot Indicators -->
    <div class="slider-dots">
        <span class="slider-dot active" onclick="setSlide(0)"></span>
        <span class="slider-dot" onclick="setSlide(1)"></span>
        <span class="slider-dot" onclick="setSlide(2)"></span>
        <span class="slider-dot" onclick="setSlide(3)"></span>
    </div>
</section>

<script>
(function() {
    let currentSlideIndex = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dot');
    let autoSlideTimer;

    function showSlide(index) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        currentSlideIndex = index;

        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === currentSlideIndex);
        });
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentSlideIndex);
        });
    }

    window.moveSlide = function(dir) {
        showSlide(currentSlideIndex + dir);
        resetTimer();
    };

    window.setSlide = function(i) {
        showSlide(i);
        resetTimer();
    };

    function startTimer() {
        autoSlideTimer = setInterval(() => {
            showSlide(currentSlideIndex + 1);
        }, 5000);
    }

    function resetTimer() {
        clearInterval(autoSlideTimer);
        startTimer();
    }

    startTimer();
})();
</script>

<!-- ===================================================
     QUICK STATS COUNTER
     =================================================== -->
<section class="stats-list" style="margin-bottom: 2.5rem;">
    <div class="stat-item"><h4>4,000+</h4><p>Active Trainees</p></div>
    <div class="stat-item"><h4>12+</h4><p>TVET Departments</p></div>
    <div class="stat-item"><h4>50+</h4><p>Industry Partners</p></div>
    <div class="stat-item"><h4>150+</h4><p>Qualified Instructors</p></div>
    <div class="stat-item"><h4>3</h4><p>Flagship Centers</p></div>
</section>

<!-- ===================================================
     3 CORE INSTITUTIONAL PILLARS (AATPTC Benchmark)
     =================================================== -->
<section class="pillars-section">
    <div class="page-header">
        <h2><?php echo __('core_pillars'); ?></h2>
        <p>Three flagship service areas that define MGMBPTC's institutional excellence and community impact.</p>
    </div>
    <div class="pillars-grid">
        <div class="pillar-card">
            <h3><?php echo __('tvet_training_title'); ?></h3>
            <p><?php echo __('tvet_training_desc'); ?></p>
            <a href="academics.php" class="btn"><?php echo __('btn_explore'); ?></a>
        </div>
        <div class="pillar-card">
            <h3><?php echo __('incubation_title'); ?></h3>
            <p><?php echo __('incubation_desc'); ?></p>
            <a href="innovation.php#incubation" class="btn"><?php echo __('btn_visit'); ?></a>
        </div>
        <div class="pillar-card">
            <h3><?php echo __('production_title'); ?></h3>
            <p><?php echo __('production_desc'); ?></p>
            <a href="innovation.php#production" class="btn"><?php echo __('btn_visit'); ?></a>
        </div>
    </div>
</section>

<!-- ===================================================
     NOTICES + NEWS SPLIT GRID
     =================================================== -->
<div class="grid-2" style="margin-bottom: 2.5rem;">
    <!-- Announcement Board -->
    <section class="card notice-board">
        <h3> <?php echo __('announcements'); ?></h3>
        <ul class="notice-list">
            <?php foreach ($announcements as $ann): ?>
                <li class="notice-item">
                    <span class="notice-date"><?php echo date('M d, Y', strtotime($ann['date_posted'])); ?></span>
                    <a href="news.php#notice<?php echo $ann['id']; ?>">
                        <?php echo htmlspecialchars($ann[$title_field]); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="news.php" class="btn" style="margin-top:1rem;display:inline-block;">All Notices &rarr;</a>
    </section>

    <!-- Quick Links Card -->
    <section class="card">
        <h3> <?php echo __('quick_links'); ?></h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:1rem;">
            <?php
            $qlinks = [
                ['','Departments','academics.php'],
                ['','Apply Online','admission.php'],
                ['','Library','library-services.php'],
                ['','Student Portal','student_login.php'],
                ['','Downloads','downloads.php'],
                ['','Contact Us','contact.php'],
                ['','Innovation Hub','innovation.php'],
                ['','Staff Directory','staff.php'],
            ];
            foreach ($qlinks as $ql): ?>
            <a href="<?php echo $ql[2]; ?>" style="display:flex;align-items:center;gap:.5rem;padding:.6rem .8rem;border:1px solid var(--border-color);border-radius:6px;font-size:.88rem;font-weight:600;color:var(--text-color);transition:border-color .2s,background .2s;" onmouseover="this.style.borderColor='var(--primary-color)';this.style.background='#f0f7f0'" onmouseout="this.style.borderColor='var(--border-color)';this.style.background='transparent'">
                <span><?php echo $ql[0]; ?></span><?php echo $ql[1]; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<!-- ===================================================
     ACADEMIC DEPARTMENTS GRID (AATPTC Benchmark)
     =================================================== -->
<section style="margin-bottom:2.5rem;">
    <div class="page-header">
        <h2><?php echo __('departments_title'); ?></h2>
        <p>Explore our industry-aligned TVET departments offering hands-on training from Level I to Level V.</p>
    </div>
    <div class="dept-grid">
        <?php
        $departments = [
            ['','Information Technology','ICT','Hardware & Networking, Database Administration, Web & Mobile Development.','academics.php'],
            ['','Automotive Technology','Automotive','Engine Diagnostics, Transmission Systems, Auto Electronics & Fuel Systems.','academics.php'],
            ['','Electrical & Electronics','Electro-Tech','Industrial Control, Power Systems, Electronic Servicing & Solar Installation.','academics.php'],
            ['','Mechanical & Manufacturing','Manufacturing','CNC Machining, Lathe Operation, Welding, and Sheet Metal Fabrication.','academics.php'],
            ['','Construction Technology','Construction','Structural Construction, Finishing Works, Plumbing, and Electrical Installation.','academics.php'],
            ['','Woodwork Technology','Woodwork','Furniture Design, Interior Wood Finishing, Cabinet Making.','academics.php'],
            ['','Garment & Textile','Fashion','Garment Production, Fashion Design, and Textile Processing.','academics.php'],
            ['','Business & Commerce','Business','Marketing, Accounting & Finance, HR Management, Secretarial Science.','academics.php'],
        ];
        foreach ($departments as $dept): ?>
        <div class="dept-card">
            <div>
                <span class="dept-badge"><?php echo $dept[2]; ?></span>
                <h4 style="margin-bottom:.4rem;color:var(--primary-color);"><?php echo $dept[1]; ?></h4>
                <p style="font-size:.85rem;color:#4a5568;"><?php echo $dept[3]; ?></p>
            </div>
            <a href="<?php echo $dept[4]; ?>" style="margin-top:.75rem;font-size:.82rem;color:var(--primary-color);font-weight:600;"><?php echo __('btn_explore'); ?> &rarr;</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===================================================
     LATEST NEWS SECTION
     =================================================== -->
<section class="card" style="margin-bottom:2.5rem;">
    <h3> <?php echo __('latest_news'); ?></h3>
    <div class="grid-3" style="margin-top:1rem;">
        <?php foreach ($news as $item): ?>
        <div class="card" style="background-color:var(--bg-light);">
            <span class="notice-date" style="display:block;margin-bottom:.5rem;"><?php echo date('M d, Y', strtotime($item['date_posted'])); ?></span>
            <h4><?php echo htmlspecialchars($item[$title_field]); ?></h4>
            <p style="font-size:.9rem;margin-bottom:1rem;"><?php
                $text = $item[$content_field];
                echo htmlspecialchars(strlen($text) > 120 ? substr($text, 0, 117) . '...' : $text);
            ?></p>
            <a href="news.php#news<?php echo $item['id']; ?>" style="font-weight:700;font-size:.9rem;"><?php echo __('btn_read_more'); ?> &rarr;</a>
        </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:1.5rem;">
        <a href="news.php" class="btn">View All News &rarr;</a>
    </div>
</section>

<!-- ===================================================
     TRAINEE TESTIMONIALS (AATPTC Benchmark)
     =================================================== -->
<section class="testimonials-section">
    <div class="page-header">
        <h2> <?php echo __('testimonials_title'); ?></h2>
    </div>
    <div class="testimonials-grid">
        <?php
        $testimonials = [
            ['The practical skills I learned in the IT department — especially web development and hardware servicing — helped me start my own small tech business in Holeta.', 'Abebe Girma', 'IT Dept. Graduate, Level V'],
            ['I highly recommend the evening TVET automotive program to any full-time worker seeking new skills. The instructors are highly knowledgeable and patient.', 'Tigist Muleta', 'Evening Program Trainee, Automotive'],
            ['The Innovation Hub gave us real equipment to prototype our solar irrigation pump. We won second place at the regional TVET expo!', 'Dawit Bekele', 'Electrical Engineering, Level IV'],
            ['MGMBPTC gave me more than a certificate — it gave me a career. I now work as a junior network engineer at a Telecom firm in Addis.', 'Hana Tesfaye', 'ICT Graduate, Level IV'],
        ];
        foreach ($testimonials as $t): ?>
        <div class="testimonial-card">
            <p class="testimonial-quote">"<?php echo $t[0]; ?>"</p>
            <div class="testimonial-author"><?php echo $t[1]; ?></div>
            <div class="testimonial-role"><?php echo $t[2]; ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===================================================
     INDUSTRY PARTNERS BANNER (AATPTC Benchmark)
     =================================================== -->
<section class="partners-section">
    <div class="page-header">
        <h2><?php echo __('partners_title'); ?></h2>
    </div>
    <div class="partners-grid">
        <?php
        $partners = ['Ministry of Innovation & Tech (MInT)','Ministry of Education (MoE)','GIZ Ethiopia','Oromia TVET Bureau','KOICA','Ethiopian Industry Park Dev.','Local SME Association','Holeta City Administration'];
        foreach ($partners as $p): ?>
        <div class="partner-item"><?php echo $p; ?></div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
