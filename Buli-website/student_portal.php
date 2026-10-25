<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header('Location: student_login.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$student_id = $_SESSION['student_id'];

// Guard: force password change on first login
if ($pdo) {
    try {
        $chk = $pdo->prepare("SELECT must_change_password FROM students WHERE id = :id");
        $chk->execute([':id' => $student_id]);
        $row = $chk->fetch();
        if ($row && !empty($row['must_change_password'])) {
            header('Location: change_password.php');
            exit;
        }
    } catch (\PDOException $e) { /* silently skip */ }
}

$page_title = 'Student Dashboard';

$status_msg  = '';
$status_type = '';

if (empty($_SESSION['student_password_csrf'])) {
    $_SESSION['student_password_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_student_password'])) {
    $submitted_token = $_POST['csrf_token'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!is_string($submitted_token) || !hash_equals($_SESSION['student_password_csrf'], $submitted_token)) {
        $status_msg = 'Your session expired. Refresh the page and try again.';
        $status_type = 'error';
    } elseif (!is_string($current_password) || !is_string($new_password) || !is_string($confirm_password)
        || $current_password === '' || $new_password === '' || $confirm_password === '') {
        $status_msg = 'Please complete all password fields.';
        $status_type = 'error';
    } elseif (strlen($new_password) < 8) {
        $status_msg = 'Your new password must be at least 8 characters long.';
        $status_type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $status_msg = 'The new password and confirmation do not match.';
        $status_type = 'error';
    } elseif (!$pdo) {
        $status_msg = 'Password changes are unavailable while the database is disconnected.';
        $status_type = 'error';
    } else {
        try {
            $password_stmt = $pdo->prepare("SELECT password_hash FROM students WHERE id = :id");
            $password_stmt->execute([':id' => $student_id]);
            $student_account = $password_stmt->fetch();

            if (!$student_account || !password_verify($current_password, $student_account['password_hash'])) {
                $status_msg = 'Your current password is incorrect.';
                $status_type = 'error';
            } elseif (password_verify($new_password, $student_account['password_hash'])) {
                $status_msg = 'Choose a new password that is different from your current password.';
                $status_type = 'error';
            } else {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                if ($new_password_hash === false) {
                    $status_msg = 'Unable to securely create your new password. Please try again.';
                    $status_type = 'error';
                } else {
                    $update_password = $pdo->prepare("UPDATE students SET password_hash = :hash WHERE id = :id");
                    $update_password->execute([':hash' => $new_password_hash, ':id' => $student_id]);
                    $_SESSION['student_password_csrf'] = bin2hex(random_bytes(32));
                    $status_msg = 'Your password has been changed successfully.';
                    $status_type = 'success';
                }
            }
        } catch (\PDOException $e) {
            error_log('Student password change failed: ' . $e->getMessage());
            $status_msg = 'Unable to change your password due to a database error. Please try again.';
            $status_type = 'error';
        }
    }
}

//  Handle profile photo upload 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_photo'])) {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file    = $_FILES['profile_photo'];
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
        $max_sz  = 2 * 1024 * 1024;

        if (!in_array($file['type'], $allowed)) {
            $status_msg = 'Only JPG, PNG, WEBP or GIF images are allowed.';
            $status_type = 'error';
        } elseif ($file['size'] > $max_sz) {
            $status_msg = 'Image must be under 2 MB.';
            $status_type = 'error';
        } else {
            $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fname = 'student_' . $student_id . '_' . time() . '.' . $ext;
            $dest  = __DIR__ . '/uploads/profiles/' . $fname;
            if (!is_dir(__DIR__ . '/uploads/profiles/')) {
                mkdir(__DIR__ . '/uploads/profiles/', 0755, true);
            }
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                if ($pdo) {
                    try {
                        // Check column exists (graceful if not yet migrated)
                        $pdo->prepare("UPDATE students SET profile_photo = :p WHERE id = :id")
                            ->execute([':p' => 'uploads/profiles/' . $fname, ':id' => $student_id]);
                        $status_msg  = 'Profile photo updated successfully.';
                        $status_type = 'success';
                    } catch (\PDOException $e) {
                        $status_msg  = 'Photo saved but DB column missing — run migrate_new_features.sql first.';
                        $status_type = 'warning';
                    }
                } else {
                    $status_msg  = '[Demo] Photo saved locally. Database offline.';
                    $status_type = 'warning';
                }
            } else {
                $status_msg  = 'Failed to move uploaded file.';
                $status_type = 'error';
            }
        }
    } else {
        $status_msg  = 'Please select a photo to upload.';
        $status_type = 'error';
    }
}

//  Handle course registration 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_courses'])) {
    $semester       = trim($_POST['reg_semester'] ?? '');
    $course_ids     = $_POST['course_ids'] ?? [];

    if (empty($semester) || empty($course_ids)) {
        $status_msg  = 'Please select a semester and at least one course.';
        $status_type = 'error';
    } elseif ($pdo) {
        $added = 0; $skipped = 0;
        try {
            foreach ($course_ids as $cid) {
                $cid = (int)$cid;
                if (!$cid) continue;
                // Insert, ignore duplicate (unique key)
                $ins = $pdo->prepare(
                    "INSERT IGNORE INTO course_registrations (student_id, course_id, semester)
                     VALUES (:sid, :cid, :sem)"
                );
                $ins->execute([':sid' => $student_id, ':cid' => $cid, ':sem' => $semester]);
                if ($ins->rowCount()) $added++; else $skipped++;
            }
            $status_msg  = "Registered $added course(s) for $semester." . ($skipped ? " $skipped already registered." : '');
            $status_type = 'success';
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                $status_msg  = 'course_registrations table not found. Please run migrate_new_features.sql in phpMyAdmin first.';
            } else {
                $status_msg  = 'DB error: ' . $e->getMessage();
            }
            $status_type = 'error';
        }
    } else {
        $status_msg  = '[Demo Mode] Course registration requires a live database.';
        $status_type = 'warning';
    }
}

//  Handle drop a registered course 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drop_course'])) {
    $reg_id = (int)($_POST['reg_id'] ?? 0);
    if ($reg_id && $pdo) {
        try {
            $del = $pdo->prepare("DELETE FROM course_registrations WHERE id = :id AND student_id = :sid");
            $del->execute([':id' => $reg_id, ':sid' => $student_id]);
            $status_msg  = 'Course dropped successfully.';
            $status_type = 'success';
        } catch (\PDOException $e) {
            $status_msg  = 'Error: ' . $e->getMessage();
            $status_type = 'error';
        }
    }
}

require_once __DIR__ . '/includes/header.php';

//  Fetch data 
$student       = null;
$announcements = [];
$downloads     = [];
$student_grades= [];
$registrations = [];
$all_courses   = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute([':id' => $student_id]);
        $student = $stmt->fetch();

        $announcements = $pdo->query("SELECT * FROM news_events ORDER BY date_posted DESC LIMIT 5")->fetchAll();
        $downloads     = $pdo->query("SELECT * FROM downloads ORDER BY created_at DESC LIMIT 5")->fetchAll();

        $stmt_g = $pdo->prepare("
            SELECT g.*, c.course_code, c.course_title, c.credit_hours, c.department
            FROM grades g JOIN courses c ON g.course_id = c.id
            WHERE g.student_id = :sid ORDER BY g.semester ASC, c.course_code ASC
        ");
        $stmt_g->execute([':sid' => $student_id]);
        $student_grades = $stmt_g->fetchAll();

        // Course registrations
        try {
            $stmt_r = $pdo->prepare("
                SELECT cr.*, c.course_code, c.course_title, c.credit_hours, c.department
                FROM course_registrations cr JOIN courses c ON cr.course_id = c.id
                WHERE cr.student_id = :sid ORDER BY cr.semester, c.course_code
            ");
            $stmt_r->execute([':sid' => $student_id]);
            $registrations = $stmt_r->fetchAll();
        } catch (\PDOException $e) { /* table may not exist yet */ }

        // Available courses for the student's department
        $dept = $student['department'] ?? 'Information Technology';
        $stmt_c = $pdo->prepare("SELECT * FROM courses WHERE department = :dept OR department = 'All' ORDER BY course_code");
        $stmt_c->execute([':dept' => $dept]);
        $all_courses = $stmt_c->fetchAll();

    } catch (\PDOException $e) { /* silently skip */ }
}

//  Fallbacks 
if (!$student) {
    $student = [
        'full_name'     => $_SESSION['student_name'] ?? 'Demo Student',
        'email'         => 'student@mgmbptc.edu.et',
        'department'    => 'Information Technology',
        'student_id_no' => 'MGMB/2026/0001',
        'status'        => 'Active',
        'created_at'    => date('Y-m-d H:i:s'),
        'profile_photo' => null,
    ];
}
if (empty($announcements)) {
    $announcements = [
        ['title_en'=>'Welcome to your Student Portal','content_en'=>'Access all your academic resources, downloads and news here.','type'=>'announcement','date_posted'=>date('Y-m-d')],
        ['title_en'=>'Semester I Registration Guidelines','content_en'=>'Please finalize course registration before the deadline.','type'=>'announcement','date_posted'=>date('Y-m-d',strtotime('-2 days'))]
    ];
}
if (empty($downloads)) {
    $downloads = [
        ['title'=>'Student Handbook','category'=>'Regulations','file_name'=>'student_conduct.pdf'],
        ['title'=>'Academic Calendar 2026/27','category'=>'Academic Guides','file_name'=>'academic_calendar_2026.pdf'],
        ['title'=>'Level Registration Guide','category'=>'Academic Guides','file_name'=>'registration_guide.pdf']
    ];
}
// No fallback mock grades — students see only real teacher-reported grades from DB
// $student_grades stays empty if no DB grades exist yet

//  CGPA computation 
$semesters_data    = [];
$total_cum_credits = 0;
$total_cum_points  = 0;

foreach ($student_grades as $g) {
    $sem = $g['semester'];
    if (!isset($semesters_data[$sem])) {
        $semesters_data[$sem] = ['credits_completed'=>0,'quality_points'=>0,'courses'=>[]];
    }
    $semesters_data[$sem]['courses'][] = $g;
    if ($g['status'] === 'Completed') {
        $semesters_data[$sem]['credits_completed'] += (int)$g['credit_hours'];
        $semesters_data[$sem]['quality_points']    += (float)$g['grade_point'] * (int)$g['credit_hours'];
    }
}
$cgpa_progression = [];
foreach ($semesters_data as $sem => &$data) {
    if ($data['credits_completed'] > 0) {
        $data['gpa'] = round($data['quality_points'] / $data['credits_completed'], 2);
        $total_cum_credits += $data['credits_completed'];
        $total_cum_points  += $data['quality_points'];
        $data['cumulative_cgpa'] = round($total_cum_points / $total_cum_credits, 2);
        $cgpa_progression[$sem]  = $data['cumulative_cgpa'];
    } else {
        $data['gpa'] = 0.00;
        $data['cumulative_cgpa'] = count($cgpa_progression) > 0 ? end($cgpa_progression) : 0.00;
    }
}
unset($data);

$final_cgpa          = $total_cum_credits > 0 ? round($total_cum_points / $total_cum_credits, 2) : 0.00;
$total_credits_earned = $total_cum_credits;

$current_semester = 'Year 1, Sem I';
if (!empty($student_grades)) {
    $last_item = end($student_grades);
    $current_semester = $last_item['semester'];
}

$semester_options = ['Year 1, Sem I','Year 1, Sem II','Year 2, Sem I','Year 2, Sem II','Year 3, Sem I','Year 3, Sem II'];

// Group registrations by semester
$regs_by_sem = [];
foreach ($registrations as $r) {
    $regs_by_sem[$r['semester']][] = $r;
}
// Already-registered course IDs (all semesters) for checkbox disabling
$registered_course_ids = array_column($registrations, 'course_id');
?>

<style>
    /*  Container  */
    .portal-container { max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem; }

    /*  Header  */
    .portal-header {
        display: flex; justify-content: space-between; align-items: center;
        background: linear-gradient(135deg, #1e3f20, #2b6cb0);
        color: #fff; padding: 1.25rem 2rem; border-radius: 8px;
        margin-bottom: 1.75rem; box-shadow: 0 4px 12px rgba(0,0,0,.12);
    }
    .ph-left { display: flex; align-items: center; gap: 1rem; }
    .ph-avatar {
        width: 54px; height: 54px; border-radius: 50%;
        border: 3px solid rgba(255,255,255,.5);
        object-fit: cover; flex-shrink: 0;
        background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; font-weight: 700; color: #fff; overflow: hidden;
    }
    .ph-avatar img { width:100%; height:100%; object-fit:cover; }
    .portal-header h2 { font-size: 1.35rem; margin: 0; }
    .portal-header p  { font-size: .85rem; color: #ebf8ff; margin: .15rem 0 0; }
    .logout-btn { background:#e53e3e; color:#fff; padding:.45rem 1.1rem; border-radius:4px; font-weight:700; text-decoration:none; font-size:.85rem; }
    .logout-btn:hover { background:#c53030; color:#fff; }

    /*  Alert  */
    .sp-alert { padding:.85rem 1rem; border-radius:5px; margin-bottom:1.5rem; font-size:.93rem; font-weight:500; }
    .sp-alert-success { background:#c6f6d5; color:#22543d; border:1px solid #9ae6b4; }
    .sp-alert-error   { background:#fed7d7; color:#742a2a; border:1px solid #feb2b2; }
    .sp-alert-warning { background:#fefcbf; color:#744210; border:1px solid #f6e05e; }

    /*  Layout  */
    .dashboard-grid { display: grid; grid-template-columns: 260px 1fr; gap: 1.75rem; }
    @media(max-width:900px){ .dashboard-grid { grid-template-columns:1fr; } }

    /*  Sidebar profile card  */
    .profile-card {
        background: #fff; border: 1px solid #cbd5e0; border-radius: 8px;
        padding: 1.5rem; align-self: start;
        box-shadow: 0 2px 6px rgba(0,0,0,.04);
    }
    .profile-card h3 {
        color: #1e3f20; border-bottom: 2px solid #1e3f20;
        padding-bottom: .5rem; margin-bottom: 1rem; font-size: 1rem;
    }
    .profile-photo-wrap { text-align: center; margin-bottom: 1rem; }
    .profile-photo-wrap img {
        width: 100px; height: 100px; border-radius: 50%;
        object-fit: cover; border: 4px solid #1e4d24;
    }
    .profile-photo-wrap .avatar-placeholder {
        width: 100px; height: 100px; border-radius: 50%;
        background: linear-gradient(135deg, #1e4d24, #2b6cb0);
        color: #fff; font-size: 2.4rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto;
    }
    .profile-detail { margin-bottom: .85rem; }
    .profile-detail span { display:block; font-size:.73rem; color:#718096; text-transform:uppercase; font-weight:700; }
    .profile-detail strong { font-size:.92rem; color:#2d3748; }
    .status-badge { display:inline-block; padding:.2rem .65rem; border-radius:4px; font-size:.82rem; font-weight:700; background:#c6f6d5; color:#22543d; }

    /* photo upload inside sidebar */
    .photo-upload-mini { margin-top:1rem; padding-top:1rem; border-top:1px solid #e2e8f0; }
    .photo-upload-mini label { font-size:.78rem; font-weight:700; color:#2d3748; display:block; margin-bottom:.3rem; }
    .photo-upload-mini input[type=file] { font-size:.78rem; width:100%; margin-bottom:.5rem; }
    .btn-photo { width:100%; background:#1e4d24; color:#fff; padding:.5rem; border:none; border-radius:5px; font-size:.82rem; font-weight:700; cursor:pointer; }
    .btn-photo:hover { background:#153618; }
    .password-change-mini { margin-top:1rem; padding-top:1rem; border-top:1px solid #e2e8f0; }
    .password-change-mini label { display:block; font-size:.78rem; font-weight:700; color:#2d3748; margin-bottom:.3rem; }
    .password-change-mini input { width:100%; padding:.5rem; border:1px solid #cbd5e0; border-radius:5px; font:inherit; font-size:.82rem; margin-bottom:.7rem; }
    .password-change-mini input:focus { outline:none; border-color:#2b6cb0; box-shadow:0 0 0 3px rgba(43,108,176,.12); }

    /*  Tabs  */
    .portal-tabs { display:flex; gap:.3rem; border-bottom:2px solid #e2e8f0; margin-bottom:1.5rem; flex-wrap:wrap; }
    .portal-tab-btn {
        background:none; border:none; padding:.8rem 1.25rem;
        font-size:.88rem; font-weight:700; color:#4a5568; cursor:pointer;
        border-radius:6px 6px 0 0; border:1px solid transparent;
        margin-bottom:-2px; transition:all .18s;
    }
    .portal-tab-btn:hover  { color:#1e3f20; background:#f7fafc; }
    .portal-tab-btn.active { color:#1e3f20; background:#fff; border-color:#cbd5e0 #cbd5e0 #fff #cbd5e0; }
    .portal-tab-content { display:none; }
    .portal-tab-content.active { display:block; }

    /*  Content cards  */
    .content-card { background:#fff; border:1px solid #cbd5e0; border-radius:8px; padding:1.75rem; box-shadow:0 2px 6px rgba(0,0,0,.04); margin-bottom:1.5rem; }
    .content-card h3 { color:#1e3f20; border-bottom:2px solid #1e3f20; padding-bottom:.5rem; margin-bottom:1.25rem; font-size:1.1rem; }

    /* Announcements */
    .announcement-item { border-bottom:1px solid #e2e8f0; padding:1rem 0; }
    .announcement-item:last-child { border-bottom:none; }
    .announcement-item h4 { color:#2d3748; font-size:.97rem; margin-bottom:.2rem; }
    .announcement-item p  { color:#4a5568; font-size:.88rem; line-height:1.5; }
    .announcement-meta   { font-size:.75rem; color:#718096; margin-top:.4rem; }

    /* Downloads */
    .download-list { list-style:none; }
    .download-item { display:flex; justify-content:space-between; align-items:center; padding:.7rem 0; border-bottom:1px solid #e2e8f0; }
    .download-item:last-child { border-bottom:none; }
    .download-item-info h4 { color:#2d3748; font-size:.92rem; }
    .download-item-info span { font-size:.73rem; color:#718096; background:#edf2f7; padding:.1rem .35rem; border-radius:3px; display:inline-block; }
    .btn-dl { background:#3182ce; color:#fff; padding:.28rem .75rem; border-radius:4px; text-decoration:none; font-size:.78rem; font-weight:700; }
    .btn-dl:hover { background:#2b6cb0; color:#fff; }

    /* Academics summary */
    .academics-summary-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem; }
    @media(max-width:600px){ .academics-summary-grid { grid-template-columns:1fr; } }
    .acad-card { background:#f7fafc; border:1px solid #e2e8f0; border-radius:6px; padding:1.2rem 1rem; text-align:center; }
    .acad-card h4 { font-size:1.75rem; color:#1e3f20; margin-bottom:.2rem; font-weight:800; }
    .acad-card p  { font-size:.78rem; font-weight:600; color:#718096; text-transform:uppercase; }

    /* Grade tables */
    .semester-grade-block { border:1px solid #e2e8f0; border-radius:6px; margin-bottom:1.4rem; overflow:hidden; }
    .semester-header { background:#edf2f7; padding:.7rem 1.2rem; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; font-weight:700; color:#2d3748; font-size:.9rem; }
    table.acad-table { width:100%; border-collapse:collapse; }
    table.acad-table th,
    table.acad-table td { padding:.6rem 1rem; text-align:left; border-bottom:1px solid #edf2f7; font-size:.88rem; }
    table.acad-table th { background:#fafafa; color:#4a5568; font-weight:600; }
    .badge-grade { display:inline-block; padding:.13rem .45rem; font-weight:700; border-radius:3px; font-size:.8rem; }
    .grade-A  { background:#c6f6d5; color:#22543d; }
    .grade-B  { background:#ebf8ff; color:#2b6cb0; }
    .grade-C  { background:#feebc8; color:#744210; }
    .grade-I, .grade-IP { background:#edf2f7; color:#4a5568; }

    /*  Course Registration  */
    .reg-form-card { background:#f0fff4; border:1px solid #9ae6b4; border-radius:8px; padding:1.5rem; margin-bottom:1.5rem; }
    .reg-form-card h4 { color:#22543d; margin-bottom:1rem; font-size:1rem; }
    .reg-row { display:flex; gap:1rem; align-items:flex-end; flex-wrap:wrap; margin-bottom:1rem; }
    .reg-row .fg { flex:1; min-width:200px; }
    .fg label { display:block; font-weight:600; font-size:.85rem; color:#2d3748; margin-bottom:.3rem; }
    .ctrl { width:100%; padding:.6rem .75rem; border:1px solid #cbd5e0; border-radius:5px; font-size:.9rem; background:#fff; }
    .ctrl:focus { outline:none; border-color:#1e4d24; box-shadow:0 0 0 3px rgba(30,77,36,.12); }
    .course-checkboxes { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:.6rem; margin-bottom:1rem; }
    .course-check-item {
        display:flex; align-items:flex-start; gap:.5rem;
        padding:.6rem .85rem; border-radius:5px;
        border:1px solid #cbd5e0; background:#fff; font-size:.87rem; cursor:pointer;
    }
    .course-check-item:has(input:checked) { border-color:#1e4d24; background:#f0fff4; }
    .course-check-item.already-reg { background:#f7fafc; opacity:.65; cursor:not-allowed; }
    .course-check-item input { margin-top:.15rem; flex-shrink:0; }
    .course-check-item .cc-code { font-weight:700; color:#1e4d24; font-size:.82rem; }
    .course-check-item .cc-title { color:#2d3748; }
    .course-check-item .cc-cr { font-size:.75rem; color:#718096; }
    .btn-register { background:#1e4d24; color:#fff; padding:.65rem 1.5rem; border:none; border-radius:5px; font-size:.93rem; font-weight:700; cursor:pointer; }
    .btn-register:hover { background:#153618; }

    /* Registrations list */
    .reg-sem-block { border:1px solid #e2e8f0; border-radius:6px; margin-bottom:1.2rem; overflow:hidden; }
    .reg-sem-header { background:#2b6cb0; color:#fff; padding:.65rem 1.2rem; font-weight:700; font-size:.9rem; }
    .reg-table { width:100%; border-collapse:collapse; }
    .reg-table th { background:#f0f7ff; color:#2b6cb0; padding:.6rem 1rem; font-size:.82rem; text-align:left; border-bottom:1px solid #bee3f8; }
    .reg-table td { padding:.6rem 1rem; font-size:.88rem; border-bottom:1px solid #e2e8f0; }
    .reg-table tr:last-child td { border-bottom:none; }
    .badge-reg { font-size:.73rem; font-weight:700; padding:.15rem .45rem; border-radius:3px; }
    .br-registered { background:#c6f6d5; color:#22543d; }
    .br-dropped     { background:#fed7d7; color:#742a2a; }
    .br-completed   { background:#ebf8ff; color:#2b6cb0; }
    .btn-drop { background:#e53e3e; color:#fff; padding:.25rem .65rem; border:none; border-radius:4px; font-size:.78rem; cursor:pointer; }
    .btn-drop:hover { background:#c53030; }
    .cr-total { font-size:.82rem; color:#718096; font-weight:600; margin-top:.4rem; }
</style>

<div class="portal-container">

    <!-- Header -->
    <div class="portal-header">
        <div class="ph-left">
            <div class="ph-avatar">
                <?php if (!empty($student['profile_photo']) && file_exists(__DIR__ . '/' . $student['profile_photo'])): ?>
                    <img src="<?php echo htmlspecialchars($student['profile_photo']); ?>" alt="Photo">
                <?php else: ?>
                    <?php echo strtoupper(mb_substr($student['full_name'], 0, 1)); ?>
                <?php endif; ?>
            </div>
            <div>
                <h2>Welcome back, <?php echo htmlspecialchars($student['full_name']); ?>!</h2>
                <p>MGMBPTC Student Portal &mdash; <?php echo htmlspecialchars($student['department']); ?></p>
            </div>
        </div>
        <a href="student_logout.php" class="logout-btn">Log Out</a>
    </div>

    <!-- Alert -->
    <?php if ($status_msg): ?>
        <div class="sp-alert sp-alert-<?php echo $status_type; ?>">
            <?php echo htmlspecialchars($status_msg); ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-grid">

        <!--  Sidebar  -->
        <div class="profile-card">
            <h3> My Profile</h3>

            <!-- Photo -->
            <div class="profile-photo-wrap">
                <?php if (!empty($student['profile_photo']) && file_exists(__DIR__ . '/' . $student['profile_photo'])): ?>
                    <img src="<?php echo htmlspecialchars($student['profile_photo']); ?>" alt="Profile Photo">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <?php echo strtoupper(mb_substr($student['full_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="profile-detail"><span>Full Name</span><strong><?php echo htmlspecialchars($student['full_name']); ?></strong></div>
            <div class="profile-detail"><span>Email</span><strong style="word-break:break-all;"><?php echo htmlspecialchars($student['email']); ?></strong></div>
            <div class="profile-detail"><span>Department</span><strong><?php echo htmlspecialchars($student['department']); ?></strong></div>
            <div class="profile-detail"><span>Student ID</span><strong><?php echo htmlspecialchars($student['student_id_no'] ?: 'Pending'); ?></strong></div>
            <div class="profile-detail"><span>Current Semester</span><strong><?php echo htmlspecialchars($current_semester); ?></strong></div>
            <div class="profile-detail"><span>Status</span><span class="status-badge"><?php echo htmlspecialchars($student['status']); ?></span></div>

            <!-- Photo upload -->
            <div class="photo-upload-mini">
                <form method="POST" action="student_portal.php" enctype="multipart/form-data">
                    <label> Update Profile Photo</label>
                    <input type="file" name="profile_photo" accept="image/*">
                    <button type="submit" name="upload_photo" class="btn-photo">Upload Photo</button>
                </form>
                <p style="font-size:.7rem; color:#a0aec0; margin-top:.35rem; text-align:center;">JPG / PNG / WEBP &bull; max 2 MB</p>
            </div>

            <div class="password-change-mini">
                <h3>Change Your Password</h3>
                <form method="POST" action="student_portal.php">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['student_password_csrf']); ?>">
                    <label for="currentStudentPassword">Current Password</label>
                    <input type="password" id="currentStudentPassword" name="current_password" autocomplete="current-password" required>
                    <label for="newStudentPassword">New Password</label>
                    <input type="password" id="newStudentPassword" name="new_password" minlength="8" autocomplete="new-password" required>
                    <label for="confirmStudentPassword">Confirm New Password</label>
                    <input type="password" id="confirmStudentPassword" name="confirm_password" minlength="8" autocomplete="new-password" required>
                    <button type="submit" name="change_student_password" class="btn-photo">Change Password</button>
                </form>
                <p style="font-size:.7rem; color:#718096; margin-top:.4rem;">Use at least 8 characters.</p>
            </div>

            <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid #e2e8f0;">
                <p style="font-size:.8rem; color:#4a5568;">
                    For support, visit the Registrar or <a href="contact.php">contact us</a>.
                </p>
            </div>
        </div>

        <!--  Main Panel  -->
        <div>
            <!-- Tabs -->
            <div class="portal-tabs">
                <button class="portal-tab-btn active" onclick="switchPortalTab(event,'tabHome')"> Notice Board</button>
                <button class="portal-tab-btn"        onclick="switchPortalTab(event,'tabAcademics')"> My Grades</button>
                <button class="portal-tab-btn"        onclick="switchPortalTab(event,'tabRegister')"> Register Courses</button>
                <button class="portal-tab-btn"        onclick="switchPortalTab(event,'tabCourses')"> My Registrations</button>
            </div>

            <!--  TAB 1: Notice Board  -->
            <div class="portal-tab-content active" id="tabHome">
                <div class="content-card" style="margin-bottom:1.5rem;">
                    <h3>Latest Notice Board</h3>
                    <?php foreach ($announcements as $item): ?>
                        <div class="announcement-item">
                            <h4><?php echo htmlspecialchars($item['title_en'] ?? $item['title_am']); ?></h4>
                            <p><?php echo htmlspecialchars($item['content_en'] ?? $item['content_am']); ?></p>
                            <div class="announcement-meta">
                                <strong><?php echo ucfirst($item['type']); ?></strong> &bull;
                                <?php echo date('d M Y', strtotime($item['date_posted'] ?? $item['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="content-card">
                    <h3>Resources &amp; Downloads</h3>
                    <ul class="download-list">
                        <?php foreach ($downloads as $item): ?>
                            <li class="download-item">
                                <div class="download-item-info">
                                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <span><?php echo htmlspecialchars($item['category']); ?></span>
                                </div>
                                <a href="downloads.php" class="btn-dl">Download</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!--  TAB 2: Academic Record  -->
            <div class="portal-tab-content" id="tabAcademics">
                <div class="content-card">
                    <h3> My Grade Report</h3>

                    <?php if (empty($student_grades)): ?>
                        <!-- No grades yet -->
                        <div style="text-align:center; padding:3rem 1rem; color:#718096;">
                            <div style="font-size:3rem; margin-bottom:.75rem;"></div>
                            <p style="font-size:1rem; font-weight:600;">No grades recorded yet.</p>
                            <p style="font-size:.88rem;">Grades will appear here once your teacher submits them.</p>
                        </div>

                    <?php else: ?>

                        <!-- Summary bar -->
                        <div class="academics-summary-grid">
                            <div class="acad-card">
                                <h4><?php echo number_format($final_cgpa,2); ?></h4>
                                <p>Cumulative GPA</p>
                            </div>
                            <div class="acad-card">
                                <h4><?php echo $total_credits_earned; ?></h4>
                                <p>Credits Completed</p>
                            </div>
                            <div class="acad-card" style="border-top:3px solid #2b6cb0;">
                                <h4><?php echo count($student_grades); ?></h4>
                                <p>Total Courses</p>
                            </div>
                        </div>

                        <!-- Info notice -->
                        <div style="background:#ebf8ff; border:1px solid #bee3f8; border-radius:6px; padding:.75rem 1rem; margin-bottom:1.5rem; font-size:.85rem; color:#2b6cb0;">
                             Grades below are officially reported by your assigned teacher/instructor. Contact the Registrar if you find any discrepancy.
                        </div>

                        <!-- CGPA chart -->
                        <?php if (count($cgpa_progression) > 0): ?>
                        <div style="border:1px solid #e2e8f0; border-radius:6px; padding:1.25rem; margin-bottom:1.75rem; background:#fafafb;">
                            <h4 style="color:#1e3f20; font-size:.95rem; margin-bottom:1rem; text-align:center;"> CGPA Progression by Semester</h4>
                            <div style="max-width:520px; margin:0 auto;">
                                <canvas id="cgpaChart"></canvas>
                            </div>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                            <script>
                            new Chart(document.getElementById('cgpaChart').getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels: <?php echo json_encode(array_keys($semesters_data)); ?>,
                                    datasets: [{
                                        label: 'GPA per Semester',
                                        data: <?php echo json_encode(array_map(fn($s)=>$s['gpa'], $semesters_data)); ?>,
                                        borderColor: '#1e3f20',
                                        backgroundColor: 'rgba(30,63,32,.1)',
                                        tension: 0.3, fill: true, pointRadius: 6,
                                        pointBackgroundColor: '#1e3f20'
                                    }]
                                },
                                options: {
                                    scales: { y: { min: 0, max: 4, ticks: { stepSize: 0.5 } } },
                                    plugins: { legend: { display: true } }
                                }
                            });
                            </script>
                        </div>
                        <?php endif; ?>

                        <!-- Grade sheets per semester -->
                        <h4 style="color:#1e3f20; margin-bottom:1rem; font-size:1rem;"> Grade Sheets by Semester</h4>

                        <?php foreach ($semesters_data as $sem => $data): ?>
                            <div class="semester-grade-block">
                                <div class="semester-header">
                                    <span> <?php echo htmlspecialchars($sem); ?></span>
                                    <span>
                                        <?php if ($data['credits_completed'] > 0): ?>
                                            Semester GPA: <strong><?php echo number_format($data['gpa'],2); ?></strong>
                                            &nbsp;|&nbsp; CGPA: <strong><?php echo number_format($data['cumulative_cgpa'],2); ?></strong>
                                        <?php else: ?>
                                            <span style="color:#9b59b6; font-weight:700;">In Progress</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <table class="acad-table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Course Title</th>
                                            <th>Cr.</th>
                                            <th>Grade</th>
                                            <th>Grade Point</th>
                                            <th>Quality Pts</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['courses'] as $c):
                                            $gc = 'grade-' . substr($c['grade'],0,1);
                                            $is_ongoing = $c['status'] !== 'Completed';
                                        ?>
                                            <tr <?php echo $is_ongoing ? 'style="background:#faf5ff;"' : ''; ?>>
                                                <td><code><?php echo htmlspecialchars($c['course_code']); ?></code></td>
                                                <td><?php echo htmlspecialchars($c['course_title']); ?></td>
                                                <td style="text-align:center;"><?php echo $c['credit_hours']; ?></td>
                                                <td>
                                                    <span class="badge-grade <?php echo $gc; ?>">
                                                        <?php echo htmlspecialchars($c['grade']); ?>
                                                    </span>
                                                </td>
                                                <td style="text-align:center;"><?php echo number_format((float)$c['grade_point'],2); ?></td>
                                                <td style="text-align:center;">
                                                    <?php echo $is_ongoing ? '—' : number_format($c['credit_hours'] * $c['grade_point'],2); ?>
                                                </td>
                                                <td>
                                                    <span style="font-size:.75rem; font-weight:700; padding:.15rem .45rem; border-radius:3px;
                                                        background:<?php echo $is_ongoing ? '#e9d8fd' : '#c6f6d5'; ?>;
                                                        color:<?php echo $is_ongoing ? '#44337a' : '#22543d'; ?>;">
                                                        <?php echo htmlspecialchars($c['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <?php if ($data['credits_completed'] > 0): ?>
                                    <tfoot>
                                        <tr style="background:#f0fff4; font-weight:700;">
                                            <td colspan="2" style="padding:.6rem 1rem; font-size:.85rem;">Semester Total</td>
                                            <td style="text-align:center; padding:.6rem 1rem;"><?php echo $data['credits_completed']; ?></td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align:center; padding:.6rem 1rem;"><?php echo number_format($data['quality_points'],2); ?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                    <?php endif; ?>
                                </table>
                            </div>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </div>
            </div>

            <!--  TAB 3: Register Courses  -->
            <div class="portal-tab-content" id="tabRegister">
                <div class="content-card">
                    <h3> Register for Courses</h3>
                    <p style="color:#718096; font-size:.87rem; margin-bottom:1.25rem;">
                        Select a semester, then tick the courses you want to register for. Courses already registered are marked and cannot be double-registered.
                    </p>

                    <?php if (!$pdo): ?>
                        <div class="sp-alert sp-alert-warning">
                             Database offline &mdash; course registration requires a live database connection.
                        </div>
                    <?php else: ?>
                    <form method="POST" action="student_portal.php">
                        <div class="reg-row">
                            <div class="fg">
                                <label for="regSemester"> Select Semester *</label>
                                <select name="reg_semester" id="regSemester" class="ctrl" required>
                                    <option value="">— Choose semester —</option>
                                    <?php foreach ($semester_options as $so): ?>
                                        <option value="<?php echo $so; ?>"><?php echo $so; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <label style="font-weight:700; font-size:.9rem; color:#2d3748; display:block; margin-bottom:.6rem;">
                             Available Courses (<?php echo htmlspecialchars($student['department']); ?> + Common)
                        </label>
                        <div class="course-checkboxes">
                            <?php foreach ($all_courses as $c):
                                $already = in_array($c['id'], $registered_course_ids); ?>
                                <label class="course-check-item <?php echo $already ? 'already-reg' : ''; ?>">
                                    <input type="checkbox" name="course_ids[]"
                                           value="<?php echo $c['id']; ?>"
                                           <?php echo $already ? 'disabled checked' : ''; ?>>
                                    <div>
                                        <div class="cc-code"><?php echo htmlspecialchars($c['course_code']); ?></div>
                                        <div class="cc-title"><?php echo htmlspecialchars($c['course_title']); ?></div>
                                        <div class="cc-cr"><?php echo $c['credit_hours']; ?> credit hours &bull; <?php echo htmlspecialchars($c['department']); ?></div>
                                        <?php if ($already): ?>
                                            <div style="font-size:.72rem; color:#22543d; font-weight:700;"> Already Registered</div>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" name="register_courses" class="btn-register">
                             Confirm Registration
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <!--  TAB 4: My Registrations  -->
            <div class="portal-tab-content" id="tabCourses">
                <div class="content-card">
                    <h3> My Course Registrations</h3>

                    <?php if (empty($registrations)): ?>
                        <div style="text-align:center; padding:3rem 1rem; color:#718096;">
                            <div style="font-size:2.5rem; margin-bottom:.75rem;"></div>
                            <p>No courses registered yet.</p>
                            <p style="font-size:.85rem;">Go to the <strong>Register Courses</strong> tab to enroll for a semester.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($regs_by_sem as $sem => $items):
                            $sem_credits = array_sum(array_column($items, 'credit_hours')); ?>
                            <div class="reg-sem-block">
                                <div class="reg-sem-header">
                                     <?php echo htmlspecialchars($sem); ?>
                                    &nbsp;&bull;&nbsp; <?php echo count($items); ?> courses
                                </div>
                                <table class="reg-table">
                                    <thead>
                                        <tr><th>Code</th><th>Course</th><th>Credits</th><th>Dept.</th><th>Status</th><th>Registered</th><th>Action</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $r): ?>
                                            <tr>
                                                <td><code><?php echo htmlspecialchars($r['course_code']); ?></code></td>
                                                <td><?php echo htmlspecialchars($r['course_title']); ?></td>
                                                <td><?php echo $r['credit_hours']; ?></td>
                                                <td style="font-size:.8rem; color:#718096;"><?php echo htmlspecialchars($r['department']); ?></td>
                                                <td>
                                                    <span class="badge-reg br-<?php echo strtolower($r['status']); ?>">
                                                        <?php echo htmlspecialchars($r['status']); ?>
                                                    </span>
                                                </td>
                                                <td style="font-size:.8rem; color:#718096;"><?php echo date('d M Y', strtotime($r['registered_at'])); ?></td>
                                                <td>
                                                    <?php if ($r['status'] === 'Registered'): ?>
                                                    <form method="POST" action="student_portal.php"
                                                          onsubmit="return confirm('Drop this course?');" style="display:inline;">
                                                        <input type="hidden" name="reg_id" value="<?php echo $r['id']; ?>">
                                                        <button type="submit" name="drop_course" class="btn-drop">Drop</button>
                                                    </form>
                                                    <?php else: ?>
                                                        <span style="color:#a0aec0; font-size:.8rem;">—</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <p class="cr-total" style="padding:.5rem 1rem;">Total credit hours this semester: <strong><?php echo $sem_credits; ?></strong></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div><!-- end main panel -->
    </div><!-- end dashboard-grid -->
</div>

<script>
function switchPortalTab(e, id) {
    document.querySelectorAll('.portal-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.portal-tab-content').forEach(p => p.classList.remove('active'));
    e.currentTarget.classList.add('active');
    document.getElementById(id).classList.add('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
