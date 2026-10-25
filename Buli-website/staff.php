<?php
/**
 * staff.php — Major General Mulu Geta Buli Polytechnic College
 * Official Staff & Faculty Directory
 * Integrated with Impeccable Design System (PRODUCT.md & DESIGN.md)
 */
$page_title = 'Staff Directory | Major General Mulu Geta Buli Polytechnic College';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ====================================================
     INLINE COMPONENT STYLES FOR STAFF DIRECTORY
     ==================================================== -->
<style>
/* Staff Directory Hero Section */
.staff-hero {
  background: linear-gradient(135deg, var(--green-dark) 0%, var(--green) 60%, var(--teal) 100%);
  color: var(--white);
  padding: 3.5rem 1.5rem 3rem;
  border-radius: var(--radius-lg);
  margin-bottom: 2rem;
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-lg);
  border-bottom: 4px solid var(--gold-bright);
}
.staff-hero::before {
  content: "";
  position: absolute;
  top: -50%;
  right: -20%;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(230,184,48,0.15) 0%, rgba(0,0,0,0) 70%);
  pointer-events: none;
}
.staff-hero-inner {
  max-width: 900px;
  margin: 0 auto;
  text-align: center;
  position: relative;
  z-index: 2;
}
.staff-hero-badge {
  display: inline-block;
  background: var(--gold-bright);
  color: var(--green-dark);
  font-size: 0.78rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  padding: 0.3rem 0.9rem;
  border-radius: 20px;
  margin-bottom: 1rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.staff-hero h1 {
  font-size: clamp(2rem, 4vw, 2.85rem);
  font-weight: 800;
  color: var(--white);
  line-height: 1.2;
  margin-bottom: 1rem;
  text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}
.staff-hero p {
  font-size: 1.1rem;
  color: rgba(255,255,255,0.92);
  max-width: 720px;
  margin: 0 auto 1.75rem;
  line-height: 1.65;
}
.staff-hero-stats {
  display: flex;
  justify-content: center;
  gap: 2rem;
  flex-wrap: wrap;
  padding-top: 1rem;
  border-top: 1px solid rgba(255,255,255,0.15);
}
.hero-stat-item {
  text-align: center;
}
.hero-stat-number {
  font-family: var(--font-display);
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--gold-bright);
  display: block;
}
.hero-stat-label {
  font-size: 0.8rem;
  color: rgba(255,255,255,0.8);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

/* Filter & Search Bar */
.staff-controls-card {
  background: var(--bg-white);
  border: 1.5px solid var(--border-color);
  border-radius: var(--radius-lg);
  padding: 1.5rem;
  margin-bottom: 2.5rem;
  box-shadow: var(--shadow-md);
}
.staff-controls-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr auto;
  gap: 1rem;
  align-items: center;
}
@media (max-width: 900px) {
  .staff-controls-grid {
    grid-template-columns: 1fr 1fr;
  }
}
@media (max-width: 580px) {
  .staff-controls-grid {
    grid-template-columns: 1fr;
  }
}
.search-input-wrap {
  position: relative;
}
.search-input-wrap input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.6rem;
  border: 1.5px solid var(--gray-300);
  border-radius: var(--radius-md);
  font-size: 0.95rem;
  font-family: var(--font-body);
  transition: border-color 0.2s, box-shadow 0.2s;
  background: var(--bg-white);
}
.search-input-wrap input:focus {
  outline: none;
  border-color: var(--green);
  box-shadow: 0 0 0 3.5px rgba(30,77,36,0.15);
}
.search-icon {
  position: absolute;
  left: 0.9rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-500);
  font-size: 1rem;
  pointer-events: none;
}
.select-filter {
  padding: 0.75rem 1rem;
  border: 1.5px solid var(--gray-300);
  border-radius: var(--radius-md);
  font-size: 0.9rem;
  font-family: var(--font-body);
  color: var(--gray-800);
  background: var(--bg-white);
  cursor: pointer;
  transition: border-color 0.2s;
}
.select-filter:focus {
  outline: none;
  border-color: var(--green);
  box-shadow: 0 0 0 3.5px rgba(30,77,36,0.15);
}
.btn-reset-filter {
  background: var(--gray-100);
  color: var(--gray-700);
  border: 1.5px solid var(--gray-300);
  padding: 0.75rem 1.2rem;
  border-radius: var(--radius-md);
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
}
.btn-reset-filter:hover {
  background: var(--gray-300);
  color: var(--gray-900);
}

/* Category Filter Pills */
.category-pills {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-top: 1.2rem;
  padding-top: 1rem;
  border-top: 1px solid var(--gray-100);
}
.pill-btn {
  background: var(--gray-100);
  color: var(--gray-700);
  border: 1px solid var(--gray-300);
  padding: 0.4rem 0.9rem;
  border-radius: 20px;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}
.pill-btn:hover, .pill-btn.active {
  background: var(--green-dark);
  color: var(--white);
  border-color: var(--green-dark);
}
.pill-btn.active {
  background: var(--green);
  box-shadow: 0 2px 6px rgba(30,77,36,0.25);
}

/* Live Counter */
.filter-summary {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  color: var(--gray-600);
}
.count-badge {
  background: var(--green-pale);
  color: var(--green-dark);
  font-weight: 700;
  padding: 0.2rem 0.6rem;
  border-radius: 12px;
  font-size: 0.85rem;
}

/* Section Styling */
.staff-section {
  margin-bottom: 3.5rem;
}
.staff-section-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 2.5px solid var(--gold-bright);
}
.staff-section-icon {
  width: 38px;
  height: 38px;
  background: var(--green-pale);
  color: var(--green-dark);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  flex-shrink: 0;
}
.staff-section-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--gray-900);
  margin: 0;
}

/* Grid & Cards */
.staff-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.staff-card {
  background: var(--bg-white);
  border: 1.5px solid var(--border-color);
  border-radius: var(--radius-md);
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position: relative;
  transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.22s ease, border-color 0.22s ease;
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}
.staff-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--green-dark) 0%, var(--gold-bright) 100%);
  opacity: 0.85;
  transition: height 0.2s;
}
.staff-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
  border-color: var(--green-mid);
}
.staff-card:hover::before {
  height: 6px;
}

/* Avatar Container */
.staff-avatar-wrap {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: 1rem;
  position: relative;
  border: 3px solid var(--gold-bright);
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  background: var(--green-pale);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.staff-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.staff-avatar-initials {
  font-family: var(--font-display);
  font-size: 2.2rem;
  font-weight: 800;
  color: var(--green-dark);
}

/* Card Content Details */
.role-badge {
  display: inline-block;
  background: var(--green-pale);
  color: var(--green-dark);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 0.25rem 0.7rem;
  border-radius: 12px;
  margin-bottom: 0.6rem;
  border: 1px solid rgba(30,77,36,0.15);
}
.role-badge.dean {
  background: var(--gold-pale);
  color: #8f6b00;
  border-color: rgba(201,162,39,0.3);
}

.staff-name {
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 0.3rem;
  line-height: 1.3;
}
.staff-dept-tag {
  font-size: 0.84rem;
  font-weight: 600;
  color: var(--teal);
  margin-bottom: 0.4rem;
}
.staff-specialization {
  font-size: 0.83rem;
  color: var(--gray-600);
  line-height: 1.4;
  margin-bottom: 1.1rem;
  flex: 1;
}

/* Contact Action Button */
.staff-email-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  width: 100%;
  padding: 0.55rem 0.9rem;
  background: var(--green-dark);
  color: var(--white);
  border-radius: var(--radius-sm);
  font-size: 0.82rem;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.2s, transform 0.15s;
}
.staff-email-btn:hover {
  background: var(--green);
  color: var(--gold-bright);
  text-decoration: none;
}

/* No Results Container */
.no-results-box {
  background: var(--bg-white);
  border: 2px dashed var(--gray-300);
  border-radius: var(--radius-lg);
  padding: 3rem 1.5rem;
  text-align: center;
  display: none;
  margin: 2rem 0;
}
.no-results-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  display: block;
}
.no-results-box h3 {
  font-size: 1.25rem;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
}
.no-results-box p {
  color: var(--gray-600);
  max-width: 420px;
  margin: 0 auto 1.25rem;
}
</style>

<div class="content-wrapper">

    <!-- ====================================================
         HERO SECTION
         ==================================================== -->
    <section class="staff-hero">
        <div class="staff-hero-inner">
            <span class="staff-hero-badge">Major General Mulu Geta Buli Polytechnic College</span>
            <h1>Faculty &amp; Staff Directory</h1>
            <p>Meet our dedicated team of administrators, department heads, certified TVET instructors, and support personnel driving excellence in technical education and technology transfer.</p>
            <div class="staff-hero-stats">
                <div class="hero-stat-item">
                    <span class="hero-stat-number">120+</span>
                    <span class="hero-stat-label">Certified Trainers</span>
                </div>
                <div class="hero-stat-item">
                    <span class="hero-stat-number">8</span>
                    <span class="hero-stat-label">Academic Departments</span>
                </div>
                <div class="hero-stat-item">
                    <span class="hero-stat-number">100%</span>
                    <span class="hero-stat-label">Industry Certified</span>
                </div>
                <div class="hero-stat-item">
                    <span class="hero-stat-number">NTQF 1–5</span>
                    <span class="hero-stat-label">Qualified Assessors</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ====================================================
         SEARCH & FILTER CONTROL SUITE
         ==================================================== -->
    <section class="staff-controls-card">
        <div class="staff-controls-grid">
            <div class="search-input-wrap">
                <span class="search-icon"></span>
                <input type="text" id="staffSearch" placeholder="Search by name, role, specialization, or email..." oninput="filterStaff()">
            </div>
            
            <div>
                <select id="staffDeptFilter" class="select-filter" style="width:100%;" onchange="filterStaff()">
                    <option value="">All Departments</option>
                    <option value="Administration">Administration &amp; Executive</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Automotive Technology">Automotive Technology</option>
                    <option value="Electrical & Electronics">Electrical &amp; Electronics</option>
                    <option value="Mechanical & Manufacturing">Mechanical &amp; Manufacturing</option>
                    <option value="Construction Technology">Construction Technology</option>
                    <option value="Garment & Textile">Garment &amp; Textile Technology</option>
                    <option value="Student Affairs">Student Affairs &amp; Registrar</option>
                    <option value="Library Services">Library Services</option>
                </select>
            </div>

            <div>
                <select id="staffRoleSelect" class="select-filter" style="width:100%;" onchange="filterStaff()">
                    <option value="">All Categories</option>
                    <option value="leadership">Executive Leadership</option>
                    <option value="dept-head">Department Heads</option>
                    <option value="academic">Academic Faculty</option>
                    <option value="admin">Administrative Staff</option>
                </select>
            </div>

            <div>
                <button type="button" class="btn-reset-filter" onclick="resetFilter()">Reset Filters</button>
            </div>
        </div>

        <div class="category-pills">
            <button type="button" class="pill-btn active" onclick="setCategoryPill('', this)">All Staff</button>
            <button type="button" class="pill-btn" onclick="setCategoryPill('leadership', this)"> Leadership</button>
            <button type="button" class="pill-btn" onclick="setCategoryPill('dept-head', this)"> Dept Heads</button>
            <button type="button" class="pill-btn" onclick="setCategoryPill('academic', this)"> Academic Instructors</button>
            <button type="button" class="pill-btn" onclick="setCategoryPill('admin', this)"> Administrative</button>
        </div>
    </section>

    <!-- Filter Summary Bar -->
    <div class="filter-summary">
        <div>
            Showing <span class="count-badge" id="visibleCount">18</span> personnel in staff directory
        </div>
        <div id="activeFilterTag" style="font-size:0.85rem; color:var(--gray-500);"></div>
    </div>

    <!-- No Results Placeholder -->
    <div class="no-results-box" id="noResultsBox">
        <span class="no-results-icon"></span>
        <h3>No Staff Members Found</h3>
        <p>We couldn't find any staff matching your current search parameters. Try adjusting your query or resetting the filters.</p>
        <button type="button" class="btn-primary" onclick="resetFilter()">Clear All Filters</button>
    </div>

    <!-- ====================================================
         SECTION 1 — EXECUTIVE LEADERSHIP
         ==================================================== -->
    <section class="staff-section" id="section-leadership" data-category="leadership">
        <div class="staff-section-header">
            <div class="staff-section-icon"></div>
            <h2 class="staff-section-title">Executive Leadership Team</h2>
        </div>
        <div class="staff-grid">

            <div class="staff-card" data-name="dr abebe kebede" data-dept="Administration" data-category="leadership" data-spec="educational leadership administration management">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/dean.jpg" alt="Dr. Abebe Kebede" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">AK</div>
                </div>
                <span class="role-badge dean">College Dean</span>
                <h3 class="staff-name">Dr. Abebe Kebede</h3>
                <div class="staff-dept-tag">Executive Administration</div>
                <p class="staff-specialization">Ph.D. in Educational Administration &amp; Institutional Governance. Overseeing strategic direction and TVET quality standards.</p>
                <a href="mailto:dean@mgmbptc.edu.et" class="staff-email-btn"> dean@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="ato tsegaye mengistu" data-dept="Administration" data-category="leadership" data-spec="curriculum development tvet academic affairs">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/academic.jpg" alt="Ato Tsegaye Mengistu" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">TM</div>
                </div>
                <span class="role-badge dean">Academic Vice Dean</span>
                <h3 class="staff-name">Ato Tsegaye Mengistu</h3>
                <div class="staff-dept-tag">Academic Affairs</div>
                <p class="staff-specialization">M.Sc. in Technical Education. Leading curriculum design, NTQF Level 1–5 compliance, and academic quality assurance.</p>
                <a href="mailto:academic@mgmbptc.edu.et" class="staff-email-btn"> academic@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="w/ro aster almaz" data-dept="Administration" data-category="leadership" data-spec="human resources finance administration">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/admin.jpg" alt="W/ro Aster Almaz" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">AA</div>
                </div>
                <span class="role-badge dean">Administrative Vice Dean</span>
                <h3 class="staff-name">W/ro Aster Almaz</h3>
                <div class="staff-dept-tag">Finance &amp; Operations</div>
                <p class="staff-specialization">M.A. in Human Resource Management. Directing administrative operations, institutional finance, and personnel affairs.</p>
                <a href="mailto:admin@mgmbptc.edu.et" class="staff-email-btn"> admin@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="ato workneh gemeda" data-dept="Administration" data-category="leadership" data-spec="industry liaison technology transfer wbl production">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/liaison.jpg" alt="Ato Workneh Gemeda" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">WG</div>
                </div>
                <span class="role-badge dean">Industry Liaison Director</span>
                <h3 class="staff-name">Ato Workneh Gemeda</h3>
                <div class="staff-dept-tag">Technology Transfer &amp; WBL</div>
                <p class="staff-specialization">M.Sc. in Industrial Engineering. Managing enterprise partnerships, cooperative training, and Work-Based Learning (WBL).</p>
                <a href="mailto:liaison@mgmbptc.edu.et" class="staff-email-btn"> liaison@mgmbptc.edu.et</a>
            </div>

        </div>
    </section>

    <!-- ====================================================
         SECTION 2 — DEPARTMENT HEADS
         ==================================================== -->
    <section class="staff-section" id="section-dept-heads" data-category="dept-head">
        <div class="staff-section-header">
            <div class="staff-section-icon"></div>
            <h2 class="staff-section-title">Department Heads</h2>
        </div>
        <div class="staff-grid">

            <div class="staff-card" data-name="ato yohannes bekele" data-dept="Information Technology" data-category="dept-head" data-spec="database network engineering software development">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/it.jpg" alt="Ato Yohannes Bekele" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">YB</div>
                </div>
                <span class="role-badge">Department Head</span>
                <h3 class="staff-name">Ato Yohannes Bekele</h3>
                <div class="staff-dept-tag">Information Technology</div>
                <p class="staff-specialization">B.Sc. in Computer Science, Cisco CCNA Certified. Specializes in network architecture, database design, and software engineering.</p>
                <a href="mailto:it@mgmbptc.edu.et" class="staff-email-btn"> it@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="ato mulugeta tadesse" data-dept="Automotive Technology" data-category="dept-head" data-spec="automotive diagnostics engine overhaul vehicle systems">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/auto.jpg" alt="Ato Mulugeta Tadesse" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">MT</div>
                </div>
                <span class="role-badge">Department Head</span>
                <h3 class="staff-name">Ato Mulugeta Tadesse</h3>
                <div class="staff-dept-tag">Automotive Technology</div>
                <p class="staff-specialization">B.Sc. in Automotive Engineering. Master assessor in computer-aided vehicle diagnostics and electronic engine tuning.</p>
                <a href="mailto:auto@mgmbptc.edu.et" class="staff-email-btn"> auto@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="w/t kidist daniel" data-dept="Electrical & Electronics" data-category="dept-head" data-spec="embedded systems plc automation solar energy">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/electrical.jpg" alt="W/t Kidist Daniel" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">KD</div>
                </div>
                <span class="role-badge">Department Head</span>
                <h3 class="staff-name">W/t Kidist Daniel</h3>
                <div class="staff-dept-tag">Electrical &amp; Electronics</div>
                <p class="staff-specialization">B.Sc. in Electrical Engineering. Expert in PLC automation, microcontroller programming, and solar PV installations.</p>
                <a href="mailto:electrical@mgmbptc.edu.et" class="staff-email-btn"> electrical@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="ato dawit solomon" data-dept="Construction Technology" data-category="dept-head" data-spec="building construction structural drafting surveying">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/construction.jpg" alt="Ato Dawit Solomon" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">DS</div>
                </div>
                <span class="role-badge">Department Head</span>
                <h3 class="staff-name">Ato Dawit Solomon</h3>
                <div class="staff-dept-tag">Construction Technology</div>
                <p class="staff-specialization">B.Sc. in Civil Engineering. Oversees structural drafting, reinforced concrete technology, and land surveying modules.</p>
                <a href="mailto:construction@mgmbptc.edu.et" class="staff-email-btn"> construction@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="w/t hiwot berhanu" data-dept="Mechanical & Manufacturing" data-category="dept-head" data-spec="cad cam cnc machining manufacturing engineering">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/mechanical.jpg" alt="W/t Hiwot Berhanu" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">HB</div>
                </div>
                <span class="role-badge">Department Head</span>
                <h3 class="staff-name">W/t Hiwot Berhanu</h3>
                <div class="staff-dept-tag">Mechanical &amp; Manufacturing</div>
                <p class="staff-specialization">B.Sc. in Mechanical Engineering. Specialist in CAD/CAM 3D modeling, precision milling, and CNC metal machining.</p>
                <a href="mailto:mechanical@mgmbptc.edu.et" class="staff-email-btn"> mechanical@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="w/ro selamawit hailu" data-dept="Garment & Textile" data-category="dept-head" data-spec="apparel production textile technology fashion design">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/garment.jpg" alt="W/ro Selamawit Hailu" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">SH</div>
                </div>
                <span class="role-badge">Department Head</span>
                <h3 class="staff-name">W/ro Selamawit Hailu</h3>
                <div class="staff-dept-tag">Garment &amp; Textile Technology</div>
                <p class="staff-specialization">B.Sc. in Textile Engineering. Lead trainer for industrial apparel manufacturing, pattern drafting, and quality control.</p>
                <a href="mailto:garment@mgmbptc.edu.et" class="staff-email-btn"> garment@mgmbptc.edu.et</a>
            </div>

        </div>
    </section>

    <!-- ====================================================
         SECTION 3 — ACADEMIC FACULTY & INSTRUCTORS
         ==================================================== -->
    <section class="staff-section" id="section-academic" data-category="academic">
        <div class="staff-section-header">
            <div class="staff-section-icon"></div>
            <h2 class="staff-section-title">Academic Faculty &amp; TVET Trainers</h2>
        </div>
        <div class="staff-grid">

            <div class="staff-card" data-name="ato berhanu assefa" data-dept="Information Technology" data-category="academic" data-spec="web development full stack python php javascript">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/inst_it.jpg" alt="Ato Berhanu Assefa" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">BA</div>
                </div>
                <span class="role-badge">Senior Instructor</span>
                <h3 class="staff-name">Ato Berhanu Assefa</h3>
                <div class="staff-dept-tag">Information Technology</div>
                <p class="staff-specialization">Full-Stack Web Development, PHP/MySQL, JavaScript, and cloud infrastructure Level IV instructor.</p>
                <a href="mailto:b.assefa@mgmbptc.edu.et" class="staff-email-btn"> b.assefa@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="w/ro tigist alemu" data-dept="Electrical & Electronics" data-category="academic" data-spec="industrial wiring renewable solar energy circuits">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/inst_elec.jpg" alt="W/ro Tigist Alemu" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">TA</div>
                </div>
                <span class="role-badge">Senior Instructor</span>
                <h3 class="staff-name">W/ro Tigist Alemu</h3>
                <div class="staff-dept-tag">Electrical &amp; Electronics</div>
                <p class="staff-specialization">Industrial building installation, motor control circuits, and renewable solar power systems trainer.</p>
                <a href="mailto:t.alemu@mgmbptc.edu.et" class="staff-email-btn"> t.alemu@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="ato kassahun desta" data-dept="Automotive Technology" data-category="academic" data-spec="diesel engine overhaul transmission suspension">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/inst_auto.jpg" alt="Ato Kassahun Desta" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">KD</div>
                </div>
                <span class="role-badge">TVET Instructor</span>
                <h3 class="staff-name">Ato Kassahun Desta</h3>
                <div class="staff-dept-tag">Automotive Technology</div>
                <p class="staff-specialization">Heavy-duty diesel engine overhaul, transmission systems, and automotive hydraulic braking specialist.</p>
                <a href="mailto:k.desta@mgmbptc.edu.et" class="staff-email-btn"> k.desta@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="w/t bethlehem girma" data-dept="Construction Technology" data-category="academic" data-spec="quantity surveying building cost estimation autocad">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/inst_const.jpg" alt="W/t Bethlehem Girma" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">BG</div>
                </div>
                <span class="role-badge">TVET Instructor</span>
                <h3 class="staff-name">W/t Bethlehem Girma</h3>
                <div class="staff-dept-tag">Construction Technology</div>
                <p class="staff-specialization">Quantity surveying, architectural AutoCAD drafting, and construction material estimation instructor.</p>
                <a href="mailto:b.girma@mgmbptc.edu.et" class="staff-email-btn"> b.girma@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="ato million haile" data-dept="Mechanical & Manufacturing" data-category="academic" data-spec="welding technology sheet metal fabrication lathe operator">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/inst_mech.jpg" alt="Ato Million Haile" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">MH</div>
                </div>
                <span class="role-badge">Technical Trainer</span>
                <h3 class="staff-name">Ato Million Haile</h3>
                <div class="staff-dept-tag">Mechanical &amp; Manufacturing</div>
                <p class="staff-specialization">Advanced TIG/MIG arc welding, precision lathe machine operation, and industrial metal fabrication.</p>
                <a href="mailto:m.haile@mgmbptc.edu.et" class="staff-email-btn"> m.haile@mgmbptc.edu.et</a>
            </div>

        </div>
    </section>

    <!-- ====================================================
         SECTION 4 — ADMINISTRATIVE & SUPPORT STAFF
         ==================================================== -->
    <section class="staff-section" id="section-admin" data-category="admin">
        <div class="staff-section-header">
            <div class="staff-section-icon"></div>
            <h2 class="staff-section-title">Administrative &amp; Support Staff</h2>
        </div>
        <div class="staff-grid">

            <div class="staff-card" data-name="ato solomon tesfaye" data-dept="Student Affairs" data-category="admin" data-spec="registrar student records enrollment transcript certification">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/registrar.jpg" alt="Ato Solomon Tesfaye" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">ST</div>
                </div>
                <span class="role-badge">Head Registrar</span>
                <h3 class="staff-name">Ato Solomon Tesfaye</h3>
                <div class="staff-dept-tag">Student Affairs &amp; Registrar</div>
                <p class="staff-specialization">Manages student admissions, academic records, transcript issuance, and national TVET certification registry.</p>
                <a href="mailto:registrar@mgmbptc.edu.et" class="staff-email-btn"> registrar@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="w/ro meseret zewde" data-dept="Library Services" data-category="admin" data-spec="digital library repository cataloging information research">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/librarian.jpg" alt="W/ro Meseret Zewde" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">MZ</div>
                </div>
                <span class="role-badge">Head Librarian</span>
                <h3 class="staff-name">W/ro Meseret Zewde</h3>
                <div class="staff-dept-tag">Library Services</div>
                <p class="staff-specialization">Directs campus print catalog and digital e-learning repository. Assists students with research databases.</p>
                <a href="mailto:library@mgmbptc.edu.et" class="staff-email-btn"> library@mgmbptc.edu.et</a>
            </div>

            <div class="staff-card" data-name="ato fikadu tulu" data-dept="Information Technology" data-category="admin" data-spec="ict infrastructure network manager server admin portal support">
                <div class="staff-avatar-wrap">
                    <img src="images/staff/ict_admin.jpg" alt="Ato Fikadu Tulu" class="staff-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="staff-avatar-initials" style="display:none;">FT</div>
                </div>
                <span class="role-badge">ICT Systems Manager</span>
                <h3 class="staff-name">Ato Fikadu Tulu</h3>
                <div class="staff-dept-tag">ICT Directorate</div>
                <p class="staff-specialization">Oversees campus fiber networks, Wi-Fi coverage, student/staff portal servers, and digital lab infrastructure.</p>
                <a href="mailto:ict@mgmbptc.edu.et" class="staff-email-btn"> ict@mgmbptc.edu.et</a>
            </div>

        </div>
    </section>

</div>

<!-- ====================================================
     CLIENT-SIDE FILTER & SEARCH LOGIC
     ==================================================== -->
<script>
let currentPillCategory = '';

function filterStaff() {
    const searchVal   = document.getElementById('staffSearch').value.toLowerCase().trim();
    const deptVal     = document.getElementById('staffDeptFilter').value.toLowerCase().trim();
    const roleVal     = document.getElementById('staffRoleSelect').value.toLowerCase().trim();
    
    // Effective category is either from pill or select box
    const effectiveCategory = currentPillCategory || roleVal;

    const cards = document.querySelectorAll('.staff-card');
    let visibleCount = 0;

    cards.forEach(function(card) {
        const name  = (card.dataset.name || '').toLowerCase();
        const dept  = (card.dataset.dept || '').toLowerCase();
        const cat   = (card.dataset.category || '').toLowerCase();
        const spec  = (card.dataset.spec || '').toLowerCase();

        // Check search query against name, department, or specialization
        const matchSearch = !searchVal || name.includes(searchVal) || dept.includes(searchVal) || spec.includes(searchVal);
        
        // Check department dropdown match
        const matchDept   = !deptVal || dept.includes(deptVal);
        
        // Check category (leadership, dept-head, academic, admin)
        const matchCat    = !effectiveCategory || cat === effectiveCategory;

        if (matchSearch && matchDept && matchCat) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Update section visibility
    document.querySelectorAll('.staff-section').forEach(function(sec) {
        const secCat = sec.dataset.category || '';
        const hasVisibleCard = Array.from(sec.querySelectorAll('.staff-card')).some(c => c.style.display !== 'none');
        
        // Show section if it has matching visible cards
        sec.style.display = hasVisibleCard ? 'block' : 'none';
    });

    // Update UI counters and empty state
    document.getElementById('visibleCount').textContent = visibleCount;
    document.getElementById('noResultsBox').style.display = visibleCount === 0 ? 'block' : 'none';

    // Active Filter Tag Text
    const activeTagEl = document.getElementById('activeFilterTag');
    const filters = [];
    if (searchVal) filters.push(Query: "${searchVal}");
    if (deptVal) filters.push(Dept: ${document.getElementById('staffDeptFilter').options[document.getElementById('staffDeptFilter').selectedIndex].text});
    if (effectiveCategory) filters.push(Category: ${effectiveCategory.toUpperCase()});
    
    activeTagEl.textContent = filters.length > 0 ? 'Active filters — ' + filters.join(' | ') : '';
}

function setCategoryPill(cat, btnElement) {
    currentPillCategory = cat;
    
    // Update select dropdown if applicable
    document.getElementById('staffRoleSelect').value = cat;

    // Toggle active pill UI class
    document.querySelectorAll('.pill-btn').forEach(btn => btn.classList.remove('active'));
    btnElement.classList.add('active');

    filterStaff();
}

function resetFilter() {
    document.getElementById('staffSearch').value = '';
    document.getElementById('staffDeptFilter').selectedIndex = 0;
    document.getElementById('staffRoleSelect').selectedIndex = 0;
    currentPillCategory = '';

    document.querySelectorAll('.pill-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector('.pill-btn').classList.add('active'); // First pill ("All Staff")

    filterStaff();
}

// Initial filter calculation on page load
document.addEventListener('DOMContentLoaded', function() {
    filterStaff();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>