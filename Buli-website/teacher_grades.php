<?php
session_start();

// Only logged-in staff (teachers) can access this page
if (!isset($_SESSION['staff_logged_in'])) {
    header('Location: staff_login.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$staff_name = $_SESSION['staff_name'];
$staff_dept = $_SESSION['staff_dept'];
$staff_id   = $_SESSION['staff_id'];

$status_msg  = '';
$status_type = '';

//  HANDLE: Save / Update a grade 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grade'])) {
    $student_id  = (int)($_POST['student_id']  ?? 0);
    $course_id   = (int)($_POST['course_id']   ?? 0);
    $semester    = trim($_POST['semester']      ?? '');
    $grade       = strtoupper(trim($_POST['grade'] ?? ''));
    $grade_point = (float)($_POST['grade_point'] ?? 0);
    $status_val  = $_POST['grade_status']       ?? 'Completed';

    // Validate
    $allowed_grades = ['A+','A','A-','B+','B','B-','C+','C','C-','D','F','IP','W'];
    if ($student_id && $course_id && $semester && in_array($grade, $allowed_grades)) {
        if ($pdo) {
            try {
                // Check if grade record already exists for this student + course + semester
                $check = $pdo->prepare(
                    "SELECT id FROM grades WHERE student_id = :sid AND course_id = :cid AND semester = :sem"
                );
                $check->execute([':sid' => $student_id, ':cid' => $course_id, ':sem' => $semester]);
                $existing = $check->fetch();

                if ($existing) {
                    // Update existing
                    $upd = $pdo->prepare(
                        "UPDATE grades SET grade = :grade, grade_point = :gp, status = :st
                         WHERE id = :id"
                    );
                    $upd->execute([
                        ':grade' => $grade,
                        ':gp'    => $grade_point,
                        ':st'    => $status_val,
                        ':id'    => $existing['id']
                    ]);
                    $status_msg  = 'Grade updated successfully.';
                    $status_type = 'success';
                } else {
                    // Insert new
                    $ins = $pdo->prepare(
                        "INSERT INTO grades (student_id, course_id, semester, grade, grade_point, status)
                         VALUES (:sid, :cid, :sem, :grade, :gp, :st)"
                    );
                    $ins->execute([
                        ':sid'   => $student_id,
                        ':cid'   => $course_id,
                        ':sem'   => $semester,
                        ':grade' => $grade,
                        ':gp'    => $grade_point,
                        ':st'    => $status_val
                    ]);
                    $status_msg  = 'Grade saved successfully.';
                    $status_type = 'success';
                }
            } catch (\PDOException $e) {
                $status_msg  = 'Database error: ' . $e->getMessage();
                $status_type = 'error';
            }
        } else {
            $status_msg  = '[Demo Mode] Grade validated. (Database disconnected — no data saved)';
            $status_type = 'warning';
        }
    } else {
        $status_msg  = 'Please fill in all fields correctly. Grade must be a valid letter grade.';
        $status_type = 'error';
    }
}

//  HANDLE: Request grade change 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_grade_change'])) {
    $grade_id  = (int)($_POST['req_grade_id'] ?? 0);
    $new_grade = strtoupper(trim($_POST['req_new_grade'] ?? ''));
    $new_gp    = (float)($_POST['req_new_gp']    ?? 0);
    $reason    = trim($_POST['req_reason']        ?? '');
    $old_grade = trim($_POST['req_old_grade']     ?? '');
    $old_gp    = (float)($_POST['req_old_gp']    ?? 0);
    $r_student = (int)($_POST['req_student_id']   ?? 0);
    $r_course  = (int)($_POST['req_course_id']    ?? 0);
    $r_semester= trim($_POST['req_semester']       ?? '');

    $allowed_grades = ['A+','A','A-','B+','B','B-','C+','C','C-','D','F','IP','W'];

    if ($grade_id && $new_grade && $reason && in_array($new_grade, $allowed_grades)) {
        if ($pdo) {
            try {
                // Check no pending request already exists for this grade
                $chk = $pdo->prepare("SELECT id FROM grade_edit_requests WHERE grade_id=:gid AND status='Pending'");
                $chk->execute([':gid' => $grade_id]);
                if ($chk->fetch()) {
                    $status_msg  = 'A pending change request already exists for this grade. Wait for admin to review it.';
                    $status_type = 'warning';
                } else {
                    $ins = $pdo->prepare(
                        "INSERT INTO grade_edit_requests
                         (grade_id, staff_id, student_id, course_id, semester, old_grade, new_grade, old_gp, new_gp, reason)
                         VALUES (:gid,:sid,:stid,:cid,:sem,:og,:ng,:ogp,:ngp,:rsn)"
                    );
                    $ins->execute([
                        ':gid'  => $grade_id,
                        ':sid'  => $staff_id,
                        ':stid' => $r_student,
                        ':cid'  => $r_course,
                        ':sem'  => $r_semester,
                        ':og'   => $old_grade,
                        ':ng'   => $new_grade,
                        ':ogp'  => $old_gp,
                        ':ngp'  => $new_gp,
                        ':rsn'  => $reason,
                    ]);
                    $status_msg  = 'Grade change request submitted. Waiting for admin approval.';
                    $status_type = 'success';
                }
            } catch (\PDOException $e) {
                if (strpos($e->getMessage(), "doesn't exist") !== false) {
                    $status_msg  = 'grade_edit_requests table not found. Run migrate_grade_requests.sql in phpMyAdmin first.';
                } else {
                    $status_msg  = 'DB error: ' . $e->getMessage();
                }
                $status_type = 'error';
            }
        } else {
            $status_msg  = '[Demo] Request validated but DB is offline.';
            $status_type = 'warning';
        }
    } else {
        $status_msg  = 'Please select a new grade and provide a reason for the change.';
        $status_type = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_grade'])) {
    $grade_id = (int)($_POST['grade_id'] ?? 0);
    if ($grade_id > 0 && $pdo) {
        try {
            $del = $pdo->prepare("DELETE FROM grades WHERE id = :id");
            $del->execute([':id' => $grade_id]);
            $status_msg  = 'Grade record deleted.';
            $status_type = 'success';
        } catch (\PDOException $e) {
            $status_msg  = 'Delete error: ' . $e->getMessage();
            $status_type = 'error';
        }
    }
}

//  FETCH DATA 
$students = [];
$courses  = [];
$grades   = [];

// Filter by selected student (for viewing existing grades)
$selected_student_id = (int)($_GET['student_id'] ?? 0);

if ($pdo) {
    try {
        // All active students
        $students = $pdo->query(
            "SELECT id, full_name, student_id_no, department FROM students WHERE status = 'Active' ORDER BY full_name"
        )->fetchAll();

        // Courses (optionally filtered by department of selected student)
        if ($selected_student_id) {
            $stu = $pdo->prepare("SELECT department FROM students WHERE id = :id");
            $stu->execute([':id' => $selected_student_id]);
            $stu_row = $stu->fetch();
            $dept = $stu_row['department'] ?? '';
            $courses = $pdo->prepare(
                "SELECT * FROM courses WHERE department = :dept OR department = 'All' ORDER BY course_code"
            );
            $courses->execute([':dept' => $dept]);
            $courses = $courses->fetchAll();
        } else {
            $courses = $pdo->query("SELECT * FROM courses ORDER BY department, course_code")->fetchAll();
        }

        // Existing grades for the selected student
        if ($selected_student_id) {
            $grd = $pdo->prepare(
                "SELECT g.*, c.course_code, c.course_title, c.credit_hours
                 FROM grades g
                 JOIN courses c ON g.course_id = c.id
                 WHERE g.student_id = :sid
                 ORDER BY g.semester, c.course_code"
            );
            $grd->execute([':sid' => $selected_student_id]);
            $grades = $grd->fetchAll();
        }

        // Pending change requests by this staff
        $pending_requests = [];
        try {
            $pr = $pdo->prepare(
                "SELECT grade_id, new_grade, status FROM grade_edit_requests
                 WHERE staff_id = :sid AND status = 'Pending'"
            );
            $pr->execute([':sid' => $staff_id]);
            foreach ($pr->fetchAll() as $row) {
                $pending_requests[$row['grade_id']] = $row;
            }
        } catch (\PDOException $e) { /* table may not exist yet */ }

    } catch (\PDOException $e) {
        $status_msg  = 'Error loading data: ' . $e->getMessage();
        $status_type = 'error';
    }
}

// Grade  grade point auto-mapping (used by JS)
$grade_point_map = [
    'A+' => 4.00, 'A' => 4.00, 'A-' => 3.75,
    'B+' => 3.50, 'B' => 3.00, 'B-' => 2.75,
    'C+' => 2.50, 'C' => 2.00, 'C-' => 1.75,
    'D'  => 1.00, 'F' => 0.00,
    'IP' => 0.00, 'W' => 0.00
];

$semester_options = [
    'Year 1, Sem I', 'Year 1, Sem II',
    'Year 2, Sem I', 'Year 2, Sem II',
    'Year 3, Sem I', 'Year 3, Sem II',
];

$page_title = 'Grade Entry';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    /*  Layout  */
    .grades-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    .portal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #1e4d24, #2b6cb0);
        color: #fff;
        padding: 1.5rem 2rem;
        border-radius: 8px;
        margin-bottom: 1.75rem;
        box-shadow: 0 4px 12px rgba(0,0,0,.12);
    }
    .portal-header h2 { font-size: 1.4rem; margin: 0; }
    .portal-header p  { font-size: 0.87rem; color: #ebf8ff; margin: .2rem 0 0; }
    .header-actions { display: flex; gap: .75rem; align-items: center; }
    .btn-back {
        background: rgba(255,255,255,.18);
        color: #fff;
        padding: .45rem 1rem;
        border-radius: 4px;
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid rgba(255,255,255,.35);
        transition: background .2s;
    }
    .btn-back:hover { background: rgba(255,255,255,.30); color: #fff; }
    .logout-btn {
        background-color: #e53e3e;
        color: #fff;
        padding: .45rem 1.1rem;
        border-radius: 4px;
        font-weight: bold;
        text-decoration: none;
        font-size: .85rem;
    }
    .logout-btn:hover { background-color: #c53030; color: #fff; }

    /*  Alert  */
    .alert {
        padding: .85rem 1rem;
        border-radius: 5px;
        margin-bottom: 1.5rem;
        font-size: .95rem;
        font-weight: 500;
    }
    .alert-success { background: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
    .alert-error   { background: #fed7d7; color: #742a2a; border: 1px solid #feb2b2; }
    .alert-warning { background: #fefcbf; color: #744210; border: 1px solid #f6e05e; }

    /*  Cards  */
    .card {
        background: #fff;
        border: 1px solid #cbd5e0;
        border-radius: 8px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }
    .card-title {
        color: #1e3f20;
        font-size: 1.05rem;
        font-weight: 700;
        border-bottom: 2px solid #1e3f20;
        padding-bottom: .5rem;
        margin-bottom: 1.25rem;
    }

    /*  Student Selector  */
    .student-selector { margin-bottom: 1.5rem; }
    .student-selector form { display: flex; gap: .75rem; align-items: flex-end; flex-wrap: wrap; }
    .student-selector label { font-weight: 600; font-size: .9rem; color: #2d3748; display: block; margin-bottom: .3rem; }
    .student-selector select {
        padding: .6rem .75rem;
        border: 1px solid #cbd5e0;
        border-radius: 5px;
        font-size: .95rem;
        min-width: 320px;
        background: #fff;
    }
    .btn-select {
        background: #2b6cb0;
        color: #fff;
        padding: .6rem 1.25rem;
        border: none;
        border-radius: 5px;
        font-size: .95rem;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-select:hover { background: #2c5282; }

    /*  Grade Entry Form  */
    .form-grid {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1fr 1fr 1.2fr;
        gap: 1rem;
        align-items: end;
        margin-bottom: 1rem;
    }
    @media (max-width: 900px) {
        .form-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 600px) {
        .form-grid { grid-template-columns: 1fr; }
    }
    .fg label {
        display: block;
        font-weight: 600;
        font-size: .85rem;
        color: #2d3748;
        margin-bottom: .3rem;
    }
    .fg select, .fg input {
        width: 100%;
        padding: .6rem .75rem;
        border: 1px solid #cbd5e0;
        border-radius: 5px;
        font-size: .92rem;
        background: #fff;
    }
    .fg select:focus, .fg input:focus {
        outline: none;
        border-color: #2b6cb0;
        box-shadow: 0 0 0 3px rgba(43,108,176,.15);
    }
    .btn-save {
        background: #1e4d24;
        color: #fff;
        padding: .65rem 1.5rem;
        border: none;
        border-radius: 5px;
        font-size: .95rem;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
    }
    .btn-save:hover { background: #153618; }

    /*  Grade Table  */
    .table-wrap { overflow-x: auto; }
    table.grade-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .92rem;
    }
    .grade-table th {
        background: #1e4d24;
        color: #fff;
        padding: .65rem 1rem;
        text-align: left;
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .grade-table td {
        padding: .65rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .grade-table tr:nth-child(even) td { background: #f7fafc; }
    .grade-table tr:hover td { background: #ebf8ff; }

    /* grade badges */
    .badge-grade {
        display: inline-block;
        padding: .2rem .55rem;
        border-radius: 4px;
        font-weight: 700;
        font-size: .88rem;
    }
    .bg-a  { background: #c6f6d5; color: #22543d; }
    .bg-b  { background: #bee3f8; color: #2a4365; }
    .bg-c  { background: #fefcbf; color: #744210; }
    .bg-d  { background: #fed7d7; color: #742a2a; }
    .bg-f  { background: #fed7d7; color: #742a2a; }
    .bg-ip { background: #e9d8fd; color: #44337a; }
    .bg-w  { background: #e2e8f0; color: #4a5568; }

    /* status badges */
    .badge-status {
        font-size: .78rem;
        padding: .18rem .5rem;
        border-radius: 3px;
        font-weight: 600;
    }
    .bs-completed { background: #c6f6d5; color: #22543d; }
    .bs-ongoing   { background: #e9d8fd; color: #44337a; }

    .btn-delete {
        background: #e53e3e;
        color: #fff;
        padding: .3rem .75rem;
        border: none;
        border-radius: 4px;
        font-size: .82rem;
        cursor: pointer;
    }
    .btn-delete:hover { background: #c53030; }

    /*  Empty state  */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #718096;
    }
    .empty-state .icon { font-size: 2.5rem; margin-bottom: .75rem; }

    /*  GPA summary  */
    .gpa-bar {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }
    .gpa-item {
        background: #f0fff4;
        border: 1px solid #9ae6b4;
        border-radius: 6px;
        padding: .6rem 1.1rem;
        font-size: .9rem;
    }
    .gpa-item strong { color: #22543d; font-size: 1.1rem; }
</style>

<div class="grades-container">

    <!-- Header -->
    <div class="portal-header">
        <div>
            <h2> Grade Entry</h2>
            <p>Welcome, <strong><?php echo htmlspecialchars($staff_name); ?></strong> &mdash; <?php echo htmlspecialchars($staff_dept); ?> Department</p>
        </div>
        <div class="header-actions">
            <a href="staff_portal.php" class="btn-back"> Staff Portal</a>
            <a href="staff_logout.php" class="logout-btn">Log Out</a>
        </div>
    </div>

    <!-- Alert message -->
    <?php if ($status_msg): ?>
        <div class="alert alert-<?php echo $status_type; ?>">
            <?php echo htmlspecialchars($status_msg); ?>
        </div>
    <?php endif; ?>

    <!-- Step 1: Select a Student -->
    <div class="card">
        <h3 class="card-title"> Step 1 — Select a Student</h3>
        <div class="student-selector">
            <form method="GET" action="teacher_grades.php">
                <div>
                    <label for="studentSelect">Choose student to view / enter grades:</label>
                    <select name="student_id" id="studentSelect">
                        <option value="">— Select a student —</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?php echo $s['id']; ?>"
                                <?php echo ($selected_student_id === (int)$s['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['full_name']); ?>
                                (<?php echo htmlspecialchars($s['student_id_no']); ?>)
                                — <?php echo htmlspecialchars($s['department']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-select">Load Student</button>
            </form>
        </div>

        <?php if (!$pdo): ?>
            <p style="color:#c05621; font-size:.88rem;"> Database offline — demo mode active. Grade saves will not persist.</p>
        <?php endif; ?>
        <?php if (empty($students) && $pdo): ?>
            <p style="color:#718096; font-size:.9rem;">No active students found. Students appear here once their admission is approved.</p>
        <?php endif; ?>
    </div>

    <?php if ($selected_student_id): ?>

        <?php
        // Retrieve the selected student info for display
        $sel_student = null;
        foreach ($students as $s) {
            if ((int)$s['id'] === $selected_student_id) { $sel_student = $s; break; }
        }
        ?>

        <!-- Step 2: Enter / Edit a Grade -->
        <div class="card">
            <h3 class="card-title"> Step 2 — Enter Grade for <?php echo $sel_student ? htmlspecialchars($sel_student['full_name']) : 'Student'; ?></h3>

            <form method="POST" action="teacher_grades.php?student_id=<?php echo $selected_student_id; ?>">
                <input type="hidden" name="student_id" value="<?php echo $selected_student_id; ?>">

                <div class="form-grid">
                    <!-- Course -->
                    <div class="fg">
                        <label for="courseId">Course *</label>
                        <select name="course_id" id="courseId" required>
                            <option value="">— Select course —</option>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>">
                                    <?php echo htmlspecialchars($c['course_code'] . ' — ' . $c['course_title']); ?>
                                    (<?php echo $c['credit_hours']; ?> cr)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Semester -->
                    <div class="fg">
                        <label for="semester">Semester *</label>
                        <select name="semester" id="semester" required>
                            <option value="">— Select —</option>
                            <?php foreach ($semester_options as $sem): ?>
                                <option value="<?php echo $sem; ?>"><?php echo $sem; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Grade -->
                    <div class="fg">
                        <label for="gradeInput">Grade *</label>
                        <select name="grade" id="gradeInput" required onchange="autoFillGradePoint(this.value)">
                            <option value="">—</option>
                            <?php foreach (array_keys($grade_point_map) as $g): ?>
                                <option value="<?php echo $g; ?>"><?php echo $g; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Grade Point -->
                    <div class="fg">
                        <label for="gradePoint">Grade Point</label>
                        <input type="number" name="grade_point" id="gradePoint" min="0" max="4" step="0.01"
                               placeholder="0.00 – 4.00" value="0.00">
                    </div>

                    <!-- Status -->
                    <div class="fg">
                        <label for="gradeStatus">Status</label>
                        <select name="grade_status" id="gradeStatus">
                            <option value="Completed">Completed</option>
                            <option value="Ongoing">Ongoing</option>
                        </select>
                    </div>
                </div>

                <p style="font-size:.83rem; color:#718096; margin-bottom:1rem;">
                     If a grade already exists for the same student + course + semester, it will be <strong>updated</strong>. Otherwise a new record is created.
                </p>
                <button type="submit" name="save_grade" class="btn-save"> Save Grade</button>
            </form>
        </div>

        <!-- Step 3: View / Delete Existing Grades -->
        <div class="card">
            <h3 class="card-title"> Step 3 — Grades for <?php echo $sel_student ? htmlspecialchars($sel_student['full_name']) : 'Student'; ?></h3>

            <?php if (!empty($grades)):
                // Compute overall GPA
                $total_points = 0; $total_credits = 0;
                foreach ($grades as $gr) {
                    if ($gr['status'] === 'Completed' && $gr['grade_point'] > 0) {
                        $total_points  += $gr['grade_point'] * $gr['credit_hours'];
                        $total_credits += $gr['credit_hours'];
                    }
                }
                $gpa = $total_credits > 0 ? round($total_points / $total_credits, 2) : 'N/A';
            ?>
                <div class="gpa-bar">
                    <div class="gpa-item">Courses Recorded: <strong><?php echo count($grades); ?></strong></div>
                    <div class="gpa-item">Cumulative GPA: <strong><?php echo $gpa; ?></strong></div>
                    <div class="gpa-item">Total Credits: <strong><?php echo $total_credits; ?></strong></div>
                </div>

                <div class="table-wrap">
                    <table class="grade-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Credits</th>
                                <th>Semester</th>
                                <th>Grade</th>
                                <th>Grade Point</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grades as $i => $gr):
                                // Choose grade badge class
                                $first = $gr['grade'][0] ?? '';
                                $badge_cls = match($first) {
                                    'A' => 'bg-a',
                                    'B' => 'bg-b',
                                    'C' => 'bg-c',
                                    'D' => 'bg-d',
                                    'F' => 'bg-f',
                                    'I' => 'bg-ip', // IP
                                    'W' => 'bg-w',
                                    default => ''
                                };
                            ?>
                                <tr>
                                    <td><?php echo $i + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($gr['course_code']); ?></strong><br>
                                        <span style="color:#718096; font-size:.83rem;"><?php echo htmlspecialchars($gr['course_title']); ?></span>
                                    </td>
                                    <td style="text-align:center;"><?php echo $gr['credit_hours']; ?></td>
                                    <td><?php echo htmlspecialchars($gr['semester']); ?></td>
                                    <td>
                                        <span class="badge-grade <?php echo $badge_cls; ?>">
                                            <?php echo htmlspecialchars($gr['grade']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align:center;"><?php echo number_format($gr['grade_point'], 2); ?></td>
                                    <td>
                                        <span class="badge-status <?php echo $gr['status'] === 'Completed' ? 'bs-completed' : 'bs-ongoing'; ?>">
                                            <?php echo htmlspecialchars($gr['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST"
                                              action="teacher_grades.php?student_id=<?php echo $selected_student_id; ?>"
                                              onsubmit="return confirm('Delete this grade record?');"
                                              style="display:inline;">
                                            <input type="hidden" name="grade_id" value="<?php echo $gr['id']; ?>">
                                            <button type="submit" name="delete_grade" class="btn-delete"> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <div class="empty-state">
                    <div class="icon"></div>
                    <p>No grades recorded for this student yet.</p>
                    <p style="font-size:.88rem;">Use the form above to add the first grade.</p>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- No student selected yet -->
        <div class="card">
            <div class="empty-state">
                <div class="icon"></div>
                <p>Select a student above to view or enter grades.</p>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
// Grade  grade point auto-fill
const gradePointMap = <?php echo json_encode($grade_point_map); ?>;

function autoFillGradePoint(gradeVal) {
    const gpInput = document.getElementById('gradePoint');
    const statusSel = document.getElementById('gradeStatus');
    if (gradeVal in gradePointMap) {
        gpInput.value = gradePointMap[gradeVal].toFixed(2);
    }
    // Auto-set status to Ongoing for IP
    if (gradeVal === 'IP') {
        statusSel.value = 'Ongoing';
    } else if (gradeVal) {
        statusSel.value = 'Completed';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
