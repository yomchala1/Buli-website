<?php
/**
 * offices.php - Institutional Leadership, Offices & Directorates
 */
$page_title = "Offices & Directorates";
require_once __DIR__ . "/includes/header.php";
?>
<section class="hero-benchmark">
    <h1> Offices &amp; Institutional Directorates</h1>
    <p>The governance structure of MGMBPTC is built on transparent, accountable, and results-driven leadership.</p>
    <div class="hero-cta-group">
        <a href="#dean" class="btn-hero-primary">Dean's Office</a>
        <a href="#vice-deans" class="btn-hero-secondary">Vice Deans</a>
        <a href="#directorates" class="btn-hero-secondary">Directorates</a>
    </div>
</section>

<!-- DEAN'S OFFICE -->
<section class="section" id="dean" style="margin-bottom:3rem;">
    <div class="page-header">
        <h2> <?php echo __("dean_office"); ?></h2>
    </div>
    <div class="grid-2" style="margin-top:1.5rem;">
        <div class="card" style="border-top:4px solid var(--accent-color);">
            <div style="font-size:3rem;text-align:center;margin-bottom:1rem;"></div>
            <h3 style="text-align:center;">Col. Dr. Mulugeta Haile</h3>
            <p style="text-align:center;color:var(--accent-color);font-weight:700;margin-bottom:.75rem;">Dean, MGMBPTC</p>
            <p style="color:#4a5568;font-size:.9rem;line-height:1.7;">The Dean provides overall academic, administrative, and strategic leadership of the college — implementing the vision of excellence in technical and vocational education.</p>
            <ul style="margin-left:1.25rem;margin-top:.75rem;font-size:.9rem;color:#4a5568;">
                <li>Strategic Planning &amp; Policy Implementation</li>
                <li>Institutional Accreditation &amp; Quality Assurance</li>
                <li>External Partnerships (MInT, MoE, GIZ)</li>
                <li>Budget Approval &amp; Resource Allocation</li>
            </ul>
            <p style="margin-top:1rem;"><strong></strong> dean@mgmbptc.edu.et</p>
        </div>
        <div class="card">
            <h3> Key Functions of the Dean's Office</h3>
            <?php
            $functions = [
                ["","Academic Excellence","Oversight of all TVET training programs, curricula review, and graduate quality standards."],
                ["","Industry Linkages","MOUs with industry partners, government agencies, and international organizations."],
                ["","Performance Management","Monitoring college KPIs, staff evaluations, and strategic objective achievement."],
                ["","Public Communication","Official communications, media relations, and community outreach programs."],
            ];
            foreach ($functions as $fn): ?>
            <div style="display:flex;gap:.75rem;background:#f9fafb;padding:.75rem;border-radius:6px;margin-top:.75rem;">
                <span style="font-size:1.5rem;"><?php echo $fn[0]; ?></span>
                <div>
                    <strong style="color:var(--primary-color);"><?php echo $fn[1]; ?></strong>
                    <p style="font-size:.85rem;color:#4a5568;margin:.2rem 0 0;"><?php echo $fn[2]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- VICE DEANS -->
<section class="section" id="vice-deans" style="margin-bottom:3rem;">
    <div class="page-header">
        <h2> <?php echo __("vice_deans"); ?></h2>
        <p>Three Vice Deans oversee core institutional domains to ensure operational excellence.</p>
    </div>
    <div class="pillars-grid" style="margin-top:1.5rem;">
        <?php
        $vice_deans = [
            ["","Vice Dean &ndash; Training &amp; Academic Affairs","Ato Bekele Tadesse","vd.academic@mgmbptc.edu.et",
                ["Curriculum development and quality management","Trainee enrollment and academic affairs","Assessment standards and grading oversight","Registrar services and transcript issuance"]],
            ["","Vice Dean &ndash; Technology Dev. &amp; Industry Extension","Ato Girma Alemu","vd.tech@mgmbptc.edu.et",
                ["Production Center and WBL management","Incubation and innovation hub oversight","Technology transfer and applied research","Industry and community service programs"]],
            ["","Vice Dean &ndash; Institutional Dev. &amp; Administration","Ato Tesfaye Berhanu","vd.admin@mgmbptc.edu.et",
                ["HR management and staff capacity building","Finance, procurement, and budgeting oversight","Property administration and facilities management","Ethics, compliance, and internal audit liaison"]],
        ];
        foreach ($vice_deans as $vd): ?>
        <div class="pillar-card">
            <div class="pillar-icon"><?php echo $vd[0]; ?></div>
            <h3><?php echo $vd[1]; ?></h3>
            <p style="color:var(--accent-color);font-weight:700;font-size:.9rem;"><?php echo $vd[2]; ?></p>
            <ul style="margin-left:1.25rem;margin-top:.5rem;font-size:.88rem;color:#4a5568;flex:1;">
                <?php foreach ($vd[4] as $r): ?>
                <li style="margin-bottom:.3rem;"><?php echo $r; ?></li>
                <?php endforeach; ?>
            </ul>
            <p style="margin-top:1rem;font-size:.82rem;color:#718096;"> <?php echo $vd[3]; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- DIRECTORATES -->
<section class="section" id="directorates" style="margin-bottom:3rem;">
    <div class="page-header">
        <h2> <?php echo __("directorates"); ?></h2>
        <p>Specialized directorates managing day-to-day operations, compliance, and administrative services.</p>
    </div>
    <div class="dept-grid" style="margin-top:1.5rem;">
        <?php
        $dirs = [
            ["","Human Resource Directorate","Recruitment, staff training, performance management, and HR policy development."],
            ["","Finance Directorate","Budget management, financial reporting, payroll processing, and donor fund accounting."],
            ["","Procurement Directorate","Purchasing supplies, equipment, and services in compliance with procurement regulations."],
            ["","Plan &amp; Budget Directorate","Annual work plans, strategic targets, MIS reporting, and budget allocation oversight."],
            ["","Internal Audit Directorate","Independent review of financial and operational controls to ensure institutional accountability."],
            ["","Ethics &amp; Anti-Corruption Directorate","Promoting institutional integrity, investigating misconduct, and enforcing code of conduct."],
            ["","Property Administration &amp; General Service","Infrastructure maintenance, vehicle fleet, security, and utility services."],
            ["","Communication Directorate","Media relations, institutional publications, website management, and public information."],
        ];
        foreach ($dirs as $d): ?>
        <div class="dept-card">
            <div>
                <span class="dept-badge">Directorate</span>
                <div style="font-size:2rem;margin-bottom:.5rem;"><?php echo $d[0]; ?></div>
                <h4 style="margin-bottom:.5rem;color:var(--primary-color);"><?php echo $d[1]; ?></h4>
                <p style="font-size:.88rem;color:#4a5568;"><?php echo $d[2]; ?></p>
            </div>
            <a href="contact.php" style="margin-top:.75rem;font-size:.82rem;color:var(--primary-color);font-weight:600;">Contact &rarr;</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ORG CHART REFERENCE -->
<section class="card" style="margin-bottom:3rem;text-align:center;">
    <h3> Organizational Hierarchy</h3>
    <div style="font-family:monospace;font-size:.85rem;color:#4a5568;margin-top:1rem;line-height:2.2;">
        <div><strong style="color:var(--primary-color);font-size:1rem;">DEAN</strong></div>
        <div>&boxv;</div>
        <div> Vice Dean (Academic) &mdash; Vice Dean (Tech/Industry) &mdash; Vice Dean (Admin)</div>
        <div>&boxv;</div>
        <div>&boxur; HR &mid; Finance &mid; Procurement &mid; Plan &amp; Budget &mid; Audit &mid; Ethics &mid; Property &mid; Communication</div>
    </div>
    <a href="staff.php" class="btn" style="margin-top:1.25rem;">View Full Staff Directory &rarr;</a>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
