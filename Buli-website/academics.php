<?php
$page_title = 'Academic Programs';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="hero-benchmark">
    <h1> <?php echo __('academics_title'); ?></h1>
    <p>World-class TVET education benchmarked to Ethiopian NTQF standards — Level I through Level V — equipping graduates for Ethiopia's industrial growth.</p>
    <div class="hero-cta-group">
        <a href="#departments" class="btn-hero-primary">View Departments</a>
        <a href="admission.php" class="btn-hero-secondary">Apply Now</a>
    </div>
</section>

<!-- ===================================================
     NTQF LEVELS BREAKDOWN
     =================================================== -->
<section class="section" id="ntqf" style="margin-bottom:2.5rem;">
    <div class="page-header">
        <h2> National TVET Qualification Framework (NTQF)</h2>
        <p>MGMBPTC programs align with the Ethiopian Ministry of Education occupational standards.</p>
    </div>
    <div class="pillars-grid" style="margin-top:1.5rem;">
        <?php
        $levels = [
            ['I','Certificate I','Entry-level operational skills. Focus on basic technical tasks, tool handling, and workplace safety.','6–12 months','','Grade 8 or relevant work experience'],
            ['II','Certificate II','Improved task competence. Includes maintenance basics and simple diagnosis.','6–12 months','','Level I or Grade 10'],
            ['III','Diploma','Specialized technical competencies. Supervisory and QA roles in the field.','12 months','','Level II or Grade 10+'],
            ['IV','Advanced Diploma','Complex system analysis, planning, and team leadership.','12 months','','Level III or Grade 12'],
            ['V','Higher Diploma','Applied engineering management and research. Industry-ready leadership.','12–18 months','','Level IV or equivalent'],
        ];
        foreach ($levels as $l): ?>
        <div class="pillar-card" style="border-top:4px solid var(--accent-color);">
            <div class="pillar-icon"><?php echo $l[4]; ?></div>
            <h3>Level <?php echo $l[0]; ?> &mdash; <?php echo $l[1]; ?></h3>
            <p style="flex:1;"><?php echo $l[2]; ?></p>
            <p style="font-size:.82rem;color:#718096;margin-top:.5rem;"><strong>Duration:</strong> <?php echo $l[3]; ?></p>
            <p style="font-size:.82rem;color:#718096;"><strong>Entry:</strong> <?php echo $l[5]; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===================================================
     DEPARTMENTS GRID (AATPTC Benchmark)
     =================================================== -->
<section class="section" id="departments" style="margin-bottom:2.5rem;">
    <div class="page-header">
        <h2> Academic Departments</h2>
        <p>Our departments offer comprehensive TVET programs from foundational to advanced levels in high-demand technical and business fields.</p>
    </div>
    <div class="dept-grid" style="margin-top:1.5rem;">
        <?php
        $departments = [
            ['','Information Technology','ICT','Level 3, 4, 5','Hardware &amp; Networking, Database Administration, Web &amp; Mobile Development, ICT Support, Cybersecurity Basics.'],
            ['','Automotive Technology','Automotive','Level 1, 2, 3, 4, 5','Vehicle Engine Diagnostics, Transmission Systems, Auto Electricity, Heavy Machinery Maintenance &amp; Body Repair.'],
            ['','Electrical &amp; Electronics','Electro-Tech','Level 2, 3, 4, 5','Industrial Control, Power Systems, Electronic Servicing, Solar &amp; Renewable Energy Installation.'],
            ['','Manufacturing &amp; Mechanical','Manufacturing','Level 1, 2, 3, 4','CNC Machining, General Welding, Lathe &amp; Milling, Metal Fabrication, CAD/CAM Prototyping.'],
            ['','Construction Technology','Construction','Level 1, 2, 3, 4, 5','Structural Building, Road Maintenance, Plumbing, Surveying, Masonry &amp; Electrical Installation.'],
            ['','Woodwork Technology','Woodwork','Level 1, 2, 3, 4','Furniture Design, Interior Wood Finishing, Cabinet Making, Wood Carving.'],
            ['','Garment &amp; Textile','Fashion','Level 1, 2, 3, 4','Garment Production, Fashion Design, Tailoring, Industrial Sewing, Textile Processing.'],
            ['','Business &amp; Commerce','Business','Level 2, 3, 4, 5','Marketing, Accounting &amp; Finance, HR Management, Customer Services, Secretarial Science.'],
        ];
        foreach ($departments as $dept): ?>
        <div class="dept-card">
            <div>
                <span class="dept-badge"><?php echo $dept[2]; ?></span>
                <div style="font-size:2rem;margin-bottom:.5rem;"><?php echo $dept[0]; ?></div>
                <h4 style="margin-bottom:.3rem;color:var(--primary-color);"><?php echo $dept[1]; ?></h4>
                <p style="font-size:.78rem;font-weight:700;color:var(--accent-color);margin-bottom:.5rem;"><?php echo $dept[3]; ?></p>
                <p style="font-size:.85rem;color:#4a5568;"><?php echo $dept[4]; ?></p>
            </div>
            <a href="admission.php" style="margin-top:.75rem;font-size:.82rem;color:var(--primary-color);font-weight:600;">Apply &rarr;</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===================================================
     DEPARTMENTS TABLE WITH TVET LEVELS
     =================================================== -->
<section class="card" style="margin-bottom:2.5rem;">
    <h3> Department Overview Table</h3>
    <p style="margin-bottom:1.5rem;font-size:.9rem;color:#4a5568;">Complete list of departments, offered NTQF Levels, training duration, and entry requirements.</p>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Levels</th>
                    <th>Duration</th>
                    <th>Key Specializations</th>
                    <th>Mode</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rows = [
                    ['Information Technology (IT)','III, IV, V','1 yr / each','Hardware Servicing, Database, Web Dev, ICT Support','Regular, Extension'],
                    ['Automotive Technology','I, II, III, IV, V','6–12 mo / each','Engine, Electrical, Body, Heavy Machinery','Regular, Extension'],
                    ['Electrical &amp; Electronics','II, III, IV, V','6–12 mo / each','Power Systems, Control, Solar Installation','Regular, Summer'],
                    ['Manufacturing &amp; Mechanical','I, II, III, IV','6–12 mo / each','CNC Machining, Welding, Fabrication','Regular'],
                    ['Construction &amp; Civil Technology','I, II, III, IV, V','6–12 mo / each','Building, Plumbing, Masonry, Survey','Regular, Extension'],
                    ['Woodwork Technology','I, II, III, IV','6–12 mo / each','Furniture, Cabinetry, Interior Finishing','Regular'],
                    ['Garment &amp; Textile','I, II, III, IV','6–12 mo / each','Fashion Design, Sewing, Textile','Regular, Extension'],
                    ['Business &amp; Commerce','II, III, IV, V','1 yr / each','Accounting, Marketing, HR, Secretarial','Regular, Extension, Summer'],
                ];
                foreach ($rows as $r): ?>
                <tr>
                    <td><strong><?php echo $r[0]; ?></strong></td>
                    <td><?php echo $r[1]; ?></td>
                    <td><?php echo $r[2]; ?></td>
                    <td style="font-size:.85rem;"><?php echo $r[3]; ?></td>
                    <td style="font-size:.82rem;"><?php echo $r[4]; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- ===================================================
     VOCATIONAL GUIDANCE & COUNSELING (AATPTC Benchmark)
     =================================================== -->
<section class="section" id="guidance" style="margin-bottom:2.5rem;">
    <div class="page-header">
        <h2> Vocational Guidance &amp; Counseling</h2>
        <p>Our dedicated Vocational Guidance team helps every trainee make informed career decisions based on their interests, aptitude, and market demand.</p>
    </div>
    <div class="grid-2" style="margin-top:1.5rem;">
        <div class="card" style="border-left:4px solid var(--accent-color);">
            <h3> Career Aptitude Assessment</h3>
            <p style="color:#4a5568;line-height:1.7;">All newly enrolled trainees undergo a structured aptitude assessment to identify the best-matching department and level. Our counselors work one-on-one with students who are undecided or switching programs.</p>
            <ul style="margin-left:1.25rem;margin-top:.75rem;font-size:.9rem;color:#4a5568;">
                <li>Interest inventory and skills mapping sessions</li>
                <li>Individual career pathway consultations</li>
                <li>Industry demand data shared with students</li>
                <li>Resume &amp; professional skills workshop</li>
            </ul>
        </div>
        <div class="card">
            <h3> Counseling Services</h3>
            <p style="color:#4a5568;line-height:1.7;">Beyond academics, our counselors support students' mental well-being and personal development throughout their training period.</p>
            <div style="display:flex;flex-direction:column;gap:.75rem;margin-top:1rem;">
                <?php
                $counsel_services = [
                    ['','Individual Counseling','Confidential one-on-one sessions for personal challenges and academic concerns.'],
                    ['','Group Workshops','Peer support circles and group study skill sessions.'],
                    ['','Job Placement Assistance','CV preparation, interview coaching, and industry referral support.'],
                    ['','Scholarship Guidance','Information on GIZ, KOICA, MoE, and other funded training scholarships.'],
                ];
                foreach ($counsel_services as $cs): ?>
                <div style="display:flex;gap:.75rem;background:#f9fafb;padding:.75rem;border-radius:6px;">
                    <span style="font-size:1.5rem;"><?php echo $cs[0]; ?></span>
                    <div>
                        <strong style="color:var(--primary-color);"><?php echo $cs[1]; ?></strong>
                        <p style="font-size:.83rem;color:#4a5568;margin:.2rem 0 0;"><?php echo $cs[2]; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     SHORT-TERM PROGRAMS (Community Training)
     =================================================== -->
<section class="section" style="margin-bottom:2.5rem;">
    <div class="page-header">
        <h2> Short-Term Industry Training</h2>
        <p>Flexible 1–3 month professional up-skilling courses designed for employed workers and community members.</p>
    </div>
    <div class="dept-grid" style="margin-top:1.5rem;">
        <?php
        $short_courses = [
            ['','Web Development &amp; Coding','8 Weeks','HTML, CSS, JavaScript basics, and PHP fundamentals.'],
            ['','Solar System Installation','6 Weeks','Off-grid and on-grid solar panel installation, wiring, and maintenance.'],
            ['','Car Engine Diagnostics','8 Weeks','Modern OBD-II scan tools, fault code diagnosis, and tune-up procedures.'],
            ['','AutoCAD Drafting','10 Weeks','2D/3D technical drawing, architectural plans, and blueprint reading.'],
            ['','Business Accounting &amp; QuickBooks','8 Weeks','Financial statements, payroll processing, and digital accounting software.'],
            ['','Network Administration &amp; Security','10 Weeks','LAN/WAN setup, router configuration, firewall management, and cybersecurity basics.'],
        ];
        foreach ($short_courses as $sc): ?>
        <div class="dept-card">
            <div>
                <span class="dept-badge">Short Course</span>
                <div style="font-size:2rem;margin-bottom:.5rem;"><?php echo $sc[0]; ?></div>
                <h4 style="margin-bottom:.3rem;color:var(--primary-color);"><?php echo $sc[1]; ?></h4>
                <p style="font-size:.78rem;font-weight:700;color:var(--accent-color);margin-bottom:.4rem;">Duration: <?php echo $sc[2]; ?></p>
                <p style="font-size:.85rem;color:#4a5568;"><?php echo $sc[3]; ?></p>
            </div>
            <a href="admission.php" style="margin-top:.75rem;font-size:.82rem;color:var(--primary-color);font-weight:600;">Enroll &rarr;</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
