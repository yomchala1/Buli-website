<?php
$page_title = 'e-Learning Portal';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .lms-hero {
        background: linear-gradient(135deg, #1e3f20 0%, #2b6cb0 100%);
        color: #fff;
        padding: 4rem 2rem;
        text-align: center;
        border-radius: 8px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .lms-hero h2 {
        font-size: 2.2rem;
        margin-bottom: 0.75rem;
        font-weight: 800;
    }
    .lms-hero p {
        font-size: 1.1rem;
        color: #ebf8ff;
        max-width: 700px;
        margin: 0 auto 1.5rem auto;
    }
    .lms-badge {
        background-color: #ecc94b;
        color: #1a202c;
        padding: 0.3rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: bold;
        display: inline-block;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .lms-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
    }
    @media(max-width: 900px) {
        .lms-grid {
            grid-template-columns: 1fr;
        }
    }
    .course-card-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    @media(max-width: 600px) {
        .course-card-grid {
            grid-template-columns: 1fr;
        }
    }
    .course-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .course-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    .course-dept {
        font-size: 0.75rem;
        font-weight: bold;
        color: #2b6cb0;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }
    .course-title {
        font-size: 1.1rem;
        color: #2d3748;
        font-weight: bold;
        margin-bottom: 0.75rem;
    }
    .course-desc {
        font-size: 0.85rem;
        color: #718096;
        line-height: 1.5;
        margin-bottom: 1.25rem;
    }
    .btn-enroll {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-color: #1e3f20;
        color: #fff;
        font-size: 0.85rem;
        font-weight: bold;
        text-decoration: none;
        border-radius: 4px;
        text-align: center;
        transition: background 0.2s;
    }
    .btn-enroll:hover {
        background-color: #2b6cb0;
    }
    .lms-login-card {
        background: #fff;
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        align-self: start;
    }
    .lms-login-card h3 {
        color: #1e3f20;
        font-size: 1.25rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.5rem;
    }
    .lms-form-group {
        margin-bottom: 1.25rem;
    }
    .lms-form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.35rem;
    }
    .lms-input {
        width: 100%;
        padding: 0.6rem 0.75rem;
        border: 1px solid #cbd5e0;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    .lms-input:focus {
        outline: none;
        border-color: #2b6cb0;
    }
    .btn-lms-login {
        width: 100%;
        padding: 0.65rem;
        background-color: #ecc94b;
        color: #1a202c;
        border: none;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
        font-size: 0.95rem;
        transition: background 0.2s;
    }
    .btn-lms-login:hover {
        background-color: #d4af37;
    }
    .moodle-notice {
        font-size: 0.8rem;
        color: #718096;
        margin-top: 1rem;
        text-align: center;
        line-height: 1.4;
    }
</style>

<div class="lms-hero">
    <span class="lms-badge">Moodle &amp; Chamilo Powered</span>
    <h2>MGMBPTC e-Learning Center</h2>
    <p>Welcome to Major General Mulugeta Buli Polytechnic College's Digital Learning Environment. Access lecture notes, quizzes, slide presentations, and submit assignments online.</p>
</div>

<div class="lms-grid">
    <!-- Courses Column -->
    <div>
        <h3 style="color: #1e3f20; font-size: 1.3rem; margin-bottom: 1.25rem;">Featured Active Courses</h3>
        
        <div class="course-card-grid">
            <div class="course-card">
                <div>
                    <div class="course-dept">Information Technology</div>
                    <div class="course-title">Network Administration (Level IV)</div>
                    <p class="course-desc">Learn about subnetting, routing protocols, switches, DHCP server setups, and configuring Windows and Linux network services.</p>
                </div>
                <a href="#" onclick="alert('Please sign in using your portal credentials to access this course.'); return false;" class="btn-enroll">Access Course</a>
            </div>
            
            <div class="course-card">
                <div>
                    <div class="course-dept">Automotive Technology</div>
                    <div class="course-title">Diesel Engine Overhauling</div>
                    <p class="course-desc">A deep dive into diesel engine cycles, component measurements, cylinder head repairs, block boring, and piston clearances.</p>
                </div>
                <a href="#" onclick="alert('Please sign in using your portal credentials to access this course.'); return false;" class="btn-enroll">Access Course</a>
            </div>

            <div class="course-card">
                <div>
                    <div class="course-dept">Electrical &amp; Electronics</div>
                    <div class="course-title">PLC Programming and Automation</div>
                    <p class="course-desc">Introduction to ladder logic, timers, counters, and wiring inputs/outputs to Siemens S7 industrial PLC modules.</p>
                </div>
                <a href="#" onclick="alert('Please sign in using your portal credentials to access this course.'); return false;" class="btn-enroll">Access Course</a>
            </div>

            <div class="course-card">
                <div>
                    <div class="course-dept">Manufacturing &amp; Mechanical</div>
                    <div class="course-title">CAD/CAM Modeling (SolidWorks)</div>
                    <p class="course-desc">Practical guide on creating 3D parts, assemblies, engineering drafts, and exporting G-code for CNC milling machines.</p>
                </div>
                <a href="#" onclick="alert('Please sign in using your portal credentials to access this course.'); return false;" class="btn-enroll">Access Course</a>
            </div>
        </div>
    </div>

    <!-- Login Column -->
    <div>
        <div class="lms-login-card">
            <h3>LMS Login</h3>
            <form onsubmit="alert('LMS Integration: Authentication is handled via the Student Portal. Please log into the Student Portal first.'); return false;">
                <div class="lms-form-group">
                    <label>LMS Username / Student ID</label>
                    <input type="text" class="lms-input" placeholder="e.g. MGMB/2026/0001" required>
                </div>
                <div class="lms-form-group">
                    <label>LMS Password</label>
                    <input type="password" class="lms-input" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-lms-login">Sign In to LMS</button>
            </form>
            <div class="moodle-notice">
                 <strong>Single Sign-On (SSO):</strong> You can use your standard Student Portal credentials to log in. Contact the Registrar for registration inquiries.
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
