<?php
session_start();

// Admin credentials (change these to your preferred values)
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'buliadmin123');

// Connect to database
require_once __DIR__ . '/includes/db.php';

$login_error = '';

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $input_user = $_POST['username'] ?? '';
    $input_pass = $_POST['password'] ?? '';
    if ($input_user === ADMIN_USER && $input_pass === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = 'Invalid username or password.';
    }
}

// Handle application status update (Approve / Reject) — auto-creates student account on Approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status']) && isset($_SESSION['admin_logged_in'])) {
    $id     = (int)($_POST['record_id'] ?? 0);
    $status = $_POST['new_status'] ?? '';
    if ($id > 0 && in_array($status, ['Approved', 'Rejected'], true) && $pdo) {
        try {
            $pdo->beginTransaction();

            $adm = $pdo->prepare("SELECT * FROM admissions WHERE id = :id FOR UPDATE");
            $adm->execute([':id' => $id]);
            $admission = $adm->fetch();

            if (!$admission) {
                throw new RuntimeException('Admission record was not found.');
            }

            $credential_flash = null;
            $existing_account = false;
            if ($status === 'Approved') {
                $chk = $pdo->prepare("SELECT id FROM students WHERE email = :email");
                $chk->execute([':email' => $admission['email']]);
                $existing_account = (bool)$chk->fetch();

                if (!$existing_account) {
                    $count = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
                    $student_id_no = 'MGMB/' . date('Y') . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

                    $charset = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                    $otp = '';
                    for ($i = 0; $i < 8; $i++) {
                        $otp .= $charset[random_int(0, strlen($charset) - 1)];
                    }

                    $hash = password_hash($otp, PASSWORD_DEFAULT);
                    if ($hash === false) {
                        throw new RuntimeException('Unable to generate a secure student password.');
                    }

                    $ins = $pdo->prepare(
                        "INSERT INTO students (full_name, email, password_hash, department, student_id_no, status, must_change_password, admission_ref_id)
                         VALUES (:name, :email, :hash, :dept, :sid, 'Active', 1, :ref_id)"
                    );
                    $ins->execute([
                        ':name'   => $admission['full_name'],
                        ':email'  => $admission['email'],
                        ':hash'   => $hash,
                        ':dept'   => $admission['department'],
                        ':sid'    => $student_id_no,
                        ':ref_id' => $admission['id']
                    ]);

                    $credential_flash = [
                        'name'       => $admission['full_name'],
                        'email'      => $admission['email'],
                        'department' => $admission['department'],
                        'student_id' => $student_id_no,
                        'otp'        => $otp
                    ];
                }
            }

            $stmt = $pdo->prepare("UPDATE admissions SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);
            $pdo->commit();

            if ($credential_flash !== null) {
                $_SESSION['new_student_credentials'] = $credential_flash;
            } elseif ($status === 'Approved' && $existing_account) {
                $_SESSION['admin_flash'] = [
                    'type' => 'warning',
                    'msg' => 'Admission approved. A student account already exists for this email, so no duplicate account was created.'
                ];
            } else {
                $_SESSION['admin_flash'] = [
                    'type' => 'success',
                    'msg' => 'Admission status updated to ' . $status . '.'
                ];
            }
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Admission status update failed: ' . $e->getMessage());
            $_SESSION['admin_flash'] = [
                'type' => 'error',
                'msg' => 'The admission could not be processed. Check the database setup and try again.'
            ];
        }
    } else {
        $_SESSION['admin_flash'] = [
            'type' => 'error',
            'msg' => $pdo ? 'Invalid admission request.' : 'The database is unavailable. Admission status cannot be changed.'
        ];
    }
    header('Location: admin.php?tab=students');
    exit;
}

// Handle Reset Password (regenerate OTP for a student)
$reset_msg  = '';
$reset_type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_student_password']) && isset($_SESSION['admin_logged_in'])) {
    $id = (int)($_POST['account_id'] ?? 0);
    if ($id > 0 && $pdo) {
        try {
            // Fetch student so we can return their name
            $s = $pdo->prepare("SELECT full_name, email FROM students WHERE id = :id");
            $s->execute([':id' => $id]);
            $stu = $s->fetch();

            if ($stu) {
                $charset = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                $otp = '';
                for ($i = 0; $i < 8; $i++) {
                    $otp .= $charset[random_int(0, strlen($charset) - 1)];
                }
                $hash = password_hash($otp, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE students SET password_hash = :hash, must_change_password = 1 WHERE id = :id");
                $upd->execute([':hash' => $hash, ':id' => $id]);

                // Flash new credentials via session
                $_SESSION['new_student_credentials'] = [
                    'name'       => $stu['full_name'],
                    'email'      => $stu['email'],
                    'department' => '',
                    'student_id' => '',
                    'otp'        => $otp,
                    'is_reset'   => true
                ];
            }
        } catch (\PDOException $e) {
            // silently ignore
        }
    }
    header('Location: admin.php?tab=accounts');
    exit;
}

// Handle delete student portal account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student_account']) && isset($_SESSION['admin_logged_in'])) {
    $id = (int)($_POST['account_id'] ?? 0);
    if ($id > 0 && $pdo) {
        $pdo->prepare("DELETE FROM students WHERE id = :id")->execute([':id' => $id]);
    }
    header('Location: admin.php?tab=accounts');
    exit;
}

// Handle delete (admissions)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_admission']) && isset($_SESSION['admin_logged_in'])) {
    $id = (int)($_POST['record_id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM admissions WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
    header('Location: admin.php?tab=students');
    exit;
}

//  Handle: Add new staff account 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff']) && isset($_SESSION['admin_logged_in'])) {
    $sf_name  = trim($_POST['sf_name']  ?? '');
    $sf_email = trim($_POST['sf_email'] ?? '');
    $sf_dept  = trim($_POST['sf_dept']  ?? '');
    $sf_role  = $_POST['sf_role']       ?? 'staff';
    $sf_pass  = trim($_POST['sf_pass']  ?? '');

    if ($sf_name && $sf_email && $sf_dept && $sf_pass && $pdo) {
        try {
            $chk = $pdo->prepare("SELECT id FROM staff WHERE email = :e");
            $chk->execute([':e' => $sf_email]);
            if ($chk->fetch()) {
                $_SESSION['admin_flash'] = ['type'=>'error','msg'=>'Email already exists in staff records.'];
            } else {
                $hash = password_hash($sf_pass, PASSWORD_DEFAULT);
                $ins  = $pdo->prepare("INSERT INTO staff (full_name,email,password_hash,role,department) VALUES (:n,:e,:h,:r,:d)");
                $ins->execute([':n'=>$sf_name,':e'=>$sf_email,':h'=>$hash,':r'=>$sf_role,':d'=>$sf_dept]);
                $_SESSION['admin_flash'] = ['type'=>'success','msg'=>"Staff account created for $sf_name."];
            }
        } catch (\PDOException $ex) {
            $_SESSION['admin_flash'] = ['type'=>'error','msg'=>'DB error: '.$ex->getMessage()];
        }
    } else {
        $_SESSION['admin_flash'] = ['type'=>'error','msg'=>'Please fill in all staff fields.'];
    }
    header('Location: admin.php?tab=staff'); exit;
}

//  Handle: Delete staff account 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_staff']) && isset($_SESSION['admin_logged_in'])) {
    $id = (int)($_POST['staff_id'] ?? 0);
    if ($id > 0 && $pdo) {
        $pdo->prepare("DELETE FROM staff WHERE id = :id")->execute([':id'=>$id]);
        $_SESSION['admin_flash'] = ['type'=>'success','msg'=>'Staff account removed.'];
    }
    header('Location: admin.php?tab=staff'); exit;
}

//  Handle: Reset staff password 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_staff_pass']) && isset($_SESSION['admin_logged_in'])) {
    $id      = (int)($_POST['staff_id'] ?? 0);
    $newpass = trim($_POST['new_staff_pass'] ?? '');
    if ($id > 0 && $newpass && $pdo) {
        $hash = password_hash($newpass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE staff SET password_hash = :h WHERE id = :id")->execute([':h'=>$hash,':id'=>$id]);
        $_SESSION['admin_flash'] = ['type'=>'success','msg'=>'Staff password updated.'];
    }
    header('Location: admin.php?tab=staff'); exit;
}

//  Handle: Approve / Reject grade edit request 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_grade_request']) && isset($_SESSION['admin_logged_in'])) {
    $req_id    = (int)($_POST['req_id']     ?? 0);
    $decision  = $_POST['decision']         ?? '';
    $note      = trim($_POST['admin_note']  ?? '');

    if ($req_id > 0 && in_array($decision, ['Approved', 'Rejected']) && $pdo) {
        try {
            // Fetch the request
            $req = $pdo->prepare("SELECT * FROM grade_edit_requests WHERE id = :id AND status = 'Pending'");
            $req->execute([':id' => $req_id]);
            $request = $req->fetch();

            if ($request) {
                // Update request status
                $pdo->prepare(
                    "UPDATE grade_edit_requests SET status = :st, admin_note = :note, reviewed_at = NOW()
                     WHERE id = :id"
                )->execute([':st' => $decision, ':note' => $note, ':id' => $req_id]);

                // If approved  apply the new grade to the grades table
                if ($decision === 'Approved') {
                    $pdo->prepare(
                        "UPDATE grades SET grade = :grade, grade_point = :gp WHERE id = :gid"
                    )->execute([
                        ':grade' => $request['new_grade'],
                        ':gp'    => $request['new_gp'],
                        ':gid'   => $request['grade_id'],
                    ]);
                    $_SESSION['admin_flash'] = ['type' => 'success', 'msg' => 'Grade change approved and applied.'];
                } else {
                    $_SESSION['admin_flash'] = ['type' => 'success', 'msg' => 'Grade change request rejected.'];
                }
            } else {
                $_SESSION['admin_flash'] = ['type' => 'error', 'msg' => 'Request not found or already reviewed.'];
            }
        } catch (\PDOException $e) {
            $_SESSION['admin_flash'] = ['type' => 'error', 'msg' => 'DB error: ' . $e->getMessage()];
        }
    }
    header('Location: admin.php?tab=grades'); exit;
}

//  Handle: Delete contact message 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message']) && isset($_SESSION['admin_logged_in'])) {
    $id = (int)($_POST['record_id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
    header('Location: admin.php?tab=messages');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course']) && isset($_SESSION['admin_logged_in'])) {
    $c_code  = strtoupper(trim($_POST['c_code']  ?? ''));
    $c_title = trim($_POST['c_title'] ?? '');
    $c_cr    = (int)($_POST['c_cr']   ?? 0);
    $c_dept  = trim($_POST['c_dept']  ?? '');
    if ($c_code && $c_title && $c_cr && $c_dept && $pdo) {
        try {
            $pdo->prepare("INSERT INTO courses (course_code,course_title,credit_hours,department) VALUES (:code,:title,:cr,:dept)")
                ->execute([':code'=>$c_code,':title'=>$c_title,':cr'=>$c_cr,':dept'=>$c_dept]);
            $_SESSION['admin_flash'] = ['type'=>'success','msg'=>"Course $c_code added."];
        } catch (\PDOException $ex) {
            $_SESSION['admin_flash'] = ['type'=>'error','msg'=>'Course code already exists or DB error.'];
        }
    } else {
        $_SESSION['admin_flash'] = ['type'=>'error','msg'=>'Fill in all course fields.'];
    }
    header('Location: admin.php?tab=courses'); exit;
}

//  Handle: Delete course 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course']) && isset($_SESSION['admin_logged_in'])) {
    $id = (int)($_POST['course_id'] ?? 0);
    if ($id && $pdo) {
        $pdo->prepare("DELETE FROM courses WHERE id = :id")->execute([':id'=>$id]);
        $_SESSION['admin_flash'] = ['type'=>'success','msg'=>'Course deleted.'];
    }
    header('Location: admin.php?tab=courses'); exit;
}

// If not logged in, show login page
if (!isset($_SESSION['admin_logged_in'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | MGMBPTC</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #0d1f10 0%, #1a3a4a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 860px;
            width: 100%;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(0,0,0,.5);
        }
        @media(max-width:650px){ .login-box { grid-template-columns:1fr; } }

        /*  Left brand panel  */
        .lb-brand {
            background: linear-gradient(160deg, #1e3f20 0%, #1a3a4a 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .lb-brand::before {
            content: '';
            position: absolute;
            width: 280px; height: 280px; border-radius: 50%;
            background: rgba(212,175,55,.08);
            top: -60px; right: -60px;
        }
        .lb-brand::after {
            content: '';
            position: absolute;
            width: 160px; height: 160px; border-radius: 50%;
            background: rgba(255,255,255,.04);
            bottom: -30px; left: -30px;
        }
        .lb-brand .shield { font-size: 3.2rem; margin-bottom: 1.25rem; display:block; position:relative; z-index:1; }
        .lb-brand h2 { font-size: 1.5rem; font-weight: 800; color: #fff; line-height: 1.3; margin-bottom: .6rem; position:relative; z-index:1; }
        .lb-brand p  { font-size: .85rem; color: rgba(255,255,255,.7); line-height: 1.65; position:relative; z-index:1; }
        .lb-gold-bar {
            display: inline-block; margin-top: 1.5rem;
            background: rgba(212,175,55,.18); border: 1px solid rgba(212,175,55,.45);
            color: #d4af37; font-size: .75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .1em;
            padding: .3rem .9rem; border-radius: 20px;
            position: relative; z-index: 1;
        }
        .lb-feature { list-style:none; margin-top:1.5rem; position:relative; z-index:1; }
        .lb-feature li { font-size:.81rem; color:rgba(255,255,255,.72); padding:.28rem 0; display:flex; align-items:center; gap:.5rem; }
        .lb-feature li::before { content:'›'; color:#d4af37; font-weight:900; font-size:1rem; }

        /*  Right form panel  */
        .lb-form {
            background: #fff;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        @media(max-width:650px){ .lb-form { padding:2rem; } .lb-brand { padding:2rem; } }
        .lb-form h3 { font-size: 1.25rem; font-weight: 800; color: #1a202c; margin-bottom: .25rem; }
        .lb-form .sub { font-size: .84rem; color: #718096; margin-bottom: 1.6rem; padding-bottom: 1.2rem; border-bottom: 1px solid #e2e8f0; }

        .fg { margin-bottom: 1.15rem; }
        .fg label { display:block; font-size:.8rem; font-weight:700; color:#4a5568; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.38rem; }
        .fi-wrap { position:relative; }
        .fi-wrap .fi-icon { position:absolute; left:.85rem; top:50%; transform:translateY(-50%); font-size:.95rem; color:#a0aec0; pointer-events:none; }
        .fi {
            width:100%; padding:.68rem .75rem .68rem 2.35rem;
            border:1.5px solid #e2e8f0; border-radius:8px;
            font-size:.92rem; font-family:inherit; background:#f8fafc;
            transition:border-color .2s, box-shadow .2s;
        }
        .fi:focus { outline:none; border-color:#1e3f20; background:#fff; box-shadow:0 0 0 3px rgba(30,63,32,.12); }

        .lb-btn {
            width:100%; padding:.76rem;
            background: linear-gradient(135deg, #1e3f20 0%, #1a3a4a 100%);
            color:#fff; border:none; border-radius:8px;
            font-size:.96rem; font-weight:700; cursor:pointer;
            margin-top:.5rem; letter-spacing:.03em;
            transition:opacity .2s, transform .15s;
            position: relative;
        }
        .lb-btn:hover { opacity:.9; transform:translateY(-1px); }
        .lb-btn::after {
            content: '';
            position: absolute; inset: 0;
            border-radius: 8px;
            border: 1px solid rgba(212,175,55,.4);
            pointer-events: none;
        }

        .err-msg {
            display:flex; align-items:flex-start; gap:.55rem;
            padding:.72rem 1rem;
            background:#fff5f5; border:1px solid #fed7d7;
            border-left:4px solid #e53e3e;
            border-radius:8px; color:#742a2a;
            font-size:.86rem; margin-bottom:1.2rem; line-height:1.5;
        }

        .lb-back { margin-top:1.4rem; text-align:center; font-size:.81rem; color:#a0aec0; }
        .lb-back a { color:#1e3f20; font-weight:700; }
    </style>
</head>
<body>
    <div class="login-box">

        <!-- Left branding -->
        <div class="lb-brand">
            <span class="shield"></span>
            <h2>Admin Control Panel</h2>
            <p>Secure administrative access for managing admissions, accounts, and college operations.</p>
            <ul class="lb-feature">
                <li>Manage student applications</li>
                <li>Create &amp; reset student accounts</li>
                <li>View contact messages</li>
                <li>Oversee all portal activity</li>
            </ul>
            <span class="lb-gold-bar">MGMBPTC Admin Access</span>
        </div>

        <!-- Right form -->
        <div class="lb-form">
            <h3>Administrator Sign In</h3>
            <p class="sub">Enter your admin credentials to access the control panel.</p>

            <?php if ($login_error): ?>
                <div class="err-msg">
                    <span></span>
                    <span><?php echo htmlspecialchars($login_error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="admin.php">
                <div class="fg">
                    <label for="username">Username</label>
                    <div class="fi-wrap">
                        <span class="fi-icon"></span>
                        <input type="text" id="username" name="username" class="fi"
                               placeholder="admin" required autocomplete="username">
                    </div>
                </div>
                <div class="fg">
                    <label for="password">Password</label>
                    <div class="fi-wrap">
                        <span class="fi-icon"></span>
                        <input type="password" id="password" name="password" class="fi"
                               placeholder="••••••••" required autocomplete="current-password">
                    </div>
                </div>
                <button type="submit" name="login_submit" class="lb-btn">Sign In to Admin Panel &rarr;</button>
            </form>

            <p class="lb-back"><a href="index.php"> Back to College Website</a></p>
        </div>

    </div>
</body>
</html>
<?php
    exit;
}

// Logged in — Fetch data
$active_tab = $_GET['tab'] ?? 'students';

$students = [];
$messages = [];
$accounts = [];
$summary  = [];
$staff_list = [];
$courses_list = [];
$grades_overview = [];
$student_account_emails = [];

if ($pdo) {
    $students = $pdo->query("SELECT * FROM admissions ORDER BY created_at DESC")->fetchAll();
    $messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
    $accounts = $pdo->query("SELECT id, full_name, email, department, student_id_no, status, created_at, must_change_password FROM students ORDER BY created_at DESC")->fetchAll();
    $staff_list   = $pdo->query("SELECT id, full_name, email, role, department, created_at FROM staff ORDER BY role, full_name")->fetchAll();
    $courses_list = $pdo->query("SELECT * FROM courses ORDER BY department, course_code")->fetchAll();
    $student_account_emails = $pdo->query("SELECT email FROM students")->fetchAll(PDO::FETCH_COLUMN);
    $student_account_emails = array_fill_keys(array_map('strtolower', $student_account_emails), true);

    // Grades overview: join grades + students + courses
    try {
        $grades_overview = $pdo->query("
            SELECT g.id, g.semester, g.grade, g.grade_point, g.status,
                   s.full_name AS student_name, s.student_id_no, s.department AS student_dept,
                   c.course_code, c.course_title, c.department,
                   sf.full_name AS recorded_by
            FROM grades g
            JOIN students s ON g.student_id = s.id
            JOIN courses  c ON g.course_id  = c.id
            LEFT JOIN grade_edit_requests ger ON ger.grade_id = g.id AND ger.status = 'Pending'
            LEFT JOIN staff sf ON sf.id = (
                SELECT staff_id FROM grade_edit_requests WHERE grade_id = g.id ORDER BY requested_at DESC LIMIT 1
            )
            ORDER BY s.full_name, g.semester, c.course_code
        ")->fetchAll();
    } catch (\PDOException $e) { /* silently skip */ }

    // Pending grade edit requests
    $grade_requests = [];
    try {
        $grade_requests = $pdo->query("
            SELECT ger.*, 
                   s.full_name AS student_name, s.student_id_no,
                   c.course_code, c.course_title,
                   sf.full_name AS staff_name
            FROM grade_edit_requests ger
            JOIN students s  ON s.id  = ger.student_id
            JOIN courses  c  ON c.id  = ger.course_id
            JOIN staff    sf ON sf.id = ger.staff_id
            ORDER BY ger.requested_at DESC
        ")->fetchAll();
    } catch (\PDOException $e) { /* table may not exist yet */ }

    $summary['pending_grade_requests'] = count(array_filter($grade_requests, fn($r) => $r['status'] === 'Pending'));

    $summary['total_students']  = $pdo->query("SELECT COUNT(*) FROM admissions")->fetchColumn();
    $summary['pending']         = $pdo->query("SELECT COUNT(*) FROM admissions WHERE status='Pending'")->fetchColumn();
    $summary['approved']        = $pdo->query("SELECT COUNT(*) FROM admissions WHERE status='Approved'")->fetchColumn();
    $summary['rejected']        = $pdo->query("SELECT COUNT(*) FROM admissions WHERE status='Rejected'")->fetchColumn();
    $summary['total_messages']  = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    $summary['total_accounts']  = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $summary['total_staff']     = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
    $summary['total_courses']   = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $summary['total_grades']    = $pdo->query("SELECT COUNT(*) FROM grades")->fetchColumn();
}

// Flash message from redirects
$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | MGMBPTC</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background-color: #edf2f7;
            color: #2d3748;
        }

        /* -- Top Admin Bar -- */
        .admin-bar {
            background-color: #1e3f20;
            color: #fff;
            padding: 0.85rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #d4af37;
        }
        .admin-bar h1 { font-size: 1.2rem; }
        .admin-bar .bar-right { display: flex; align-items: center; gap: 1.5rem; font-size: 0.9rem; }
        .admin-bar a { color: #cbd5e0; text-decoration: none; }
        .admin-bar a:hover { color: #d4af37; }
        .logout-btn {
            background-color: #e53e3e;
            color: #fff;
            padding: 0.4rem 1rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: bold;
            text-decoration: none;
        }
        .logout-btn:hover { background-color: #c53030; color: #fff; }

        /* -- Main Container -- */
        .admin-container { max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem; }

        /* -- Summary Cards -- */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        /* Account form */
        .acct-form { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
        .acct-form .full-width { grid-column: 1 / -1; }
        .acct-form label { display: block; font-size: 0.85rem; font-weight:600; color:#2d3748; margin-bottom:0.3rem; }
        .acct-form input, .acct-form select { width:100%; padding:0.6rem 0.75rem; border:1px solid #cbd5e0; border-radius:4px; font-size:0.9rem; font-family:inherit; }
        .acct-form input:focus, .acct-form select:focus { outline:none; border-color:#1e3f20; }
        .btn-create { background:#1e3f20; color:#fff; padding:0.65rem 1.5rem; border:none; border-radius:4px; font-weight:bold; cursor:pointer; font-size:0.9rem; }
        .btn-create:hover { background:#2b6cb0; }
        .acct-alert { padding:0.75rem 1rem; border-radius:4px; margin-bottom:1.25rem; font-size:0.9rem; }
        .acct-alert-success { background:#c6f6d5; color:#22543d; border:1px solid #9ae6b4; }
        .acct-alert-error { background:#fed7d7; color:#742a2a; border:1px solid #feb2b2; }
        .summary-card {
            background: #fff;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            padding: 1.25rem;
            text-align: center;
        }
        .summary-card h2 { font-size: 2rem; color: #1e3f20; }
        .summary-card p { font-size: 0.85rem; color: #718096; margin-top: 0.25rem; }
        .summary-card.pending h2 { color: #d4af37; }
        .summary-card.approved h2 { color: #2f855a; }
        .summary-card.rejected h2 { color: #c53030; }

        /* -- Tabs -- */
        .tabs { display: flex; gap: 0; border-bottom: 2px solid #cbd5e0; margin-bottom: 1.5rem; }
        .tab-btn {
            padding: 0.75rem 1.75rem;
            border: 1px solid #cbd5e0;
            border-bottom: none;
            background: #edf2f7;
            color: #4a5568;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 4px 4px 0 0;
            margin-right: 4px;
        }
        .tab-btn.active {
            background: #fff;
            color: #1e3f20;
            border-bottom: 2px solid #fff;
            margin-bottom: -2px;
        }
        .tab-btn:hover { background: #fff; color: #1e3f20; }

        /* -- Table Panel -- */
        .panel {
            background: #fff;
            border: 1px solid #cbd5e0;
            border-radius: 0 6px 6px 6px;
            padding: 1.5rem;
        }
        .panel h3 {
            font-size: 1.1rem;
            color: #1e3f20;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #1e3f20;
        }
        .empty-msg {
            text-align: center;
            padding: 3rem;
            color: #718096;
            font-style: italic;
        }

        /* -- Data Table -- */
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        th {
            background-color: #edf2f7;
            color: #1e3f20;
            padding: 0.65rem 0.85rem;
            border: 1px solid #cbd5e0;
            text-align: left;
            font-weight: 700;
        }
        td {
            padding: 0.65rem 0.85rem;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        tr:nth-child(even) td { background-color: #f7fafc; }
        tr:hover td { background-color: #ebf8ff; }

        /* -- Status Badges -- */
        .badge {
            display: inline-block;
            padding: 0.2rem 0.65rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .badge-pending  { background: #fefcbf; color: #744210; }
        .badge-approved { background: #c6f6d5; color: #22543d; }
        .badge-rejected { background: #fed7d7; color: #742a2a; }

        /* -- Action Buttons -- */
        .action-btns { display: flex; gap: 0.4rem; flex-wrap: wrap; }
        .btn-sm {
            padding: 0.3rem 0.7rem;
            font-size: 0.8rem;
            font-weight: bold;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn-approve { background: #c6f6d5; color: #22543d; }
        .btn-approve:hover { background: #9ae6b4; }
        .btn-reject  { background: #fefcbf; color: #744210; }
        .btn-reject:hover  { background: #f6e05e; }
        .btn-delete  { background: #fed7d7; color: #742a2a; }
        .btn-delete:hover  { background: #feb2b2; }

        @media (max-width: 900px) {
            .summary-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 600px) {
            .summary-grid { grid-template-columns: 1fr 1fr; }
            .admin-bar { flex-direction: column; gap: 0.75rem; }
        }
        /* === Credential Card === */
        .cred-card {
            background: #f0fff4;
            border: 2px solid #48bb78;
            border-radius: 8px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
        }
        .cred-card h3 { color: #276749; font-size: 1.15rem; margin-bottom: 0.5rem; }
        .cred-card p  { color: #2f855a; font-size: 0.9rem; margin-bottom: 1.25rem; }
        .cred-table { width: 100%; border-collapse: collapse; margin-bottom: 1.25rem; }
        .cred-table td { padding: 0.6rem 0.85rem; border: 1px solid #9ae6b4; font-size: 0.95rem; }
        .cred-table td:first-child { font-weight: bold; color: #276749; width: 180px; background: #c6f6d5; }
        .cred-otp { font-size: 1.4rem; font-weight: bold; letter-spacing: 0.2rem; color: #22543d; font-family: monospace; }
        .cred-actions { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn-print { background:#276749; color:#fff; border:none; padding:0.5rem 1.25rem; border-radius:4px; cursor:pointer; font-weight:bold; font-size:0.9rem; }
        .btn-print:hover { background:#2f855a; }
        .cred-warning { background:#fefcbf; border:1px solid #f6e05e; border-radius:4px; padding:0.6rem 1rem; font-size:0.85rem; color:#744210; margin-top:1rem; }
    </style>
</head>
<body>

<!-- Admin Top Bar -->
<div class="admin-bar">
    <h1> MGMBPTC Admin Panel</h1>
    <div class="bar-right">
        <span>Logged in as: <strong>admin</strong></span>
        <a href="index.php"> View Website</a>
        <a href="admin.php?action=logout" class="logout-btn">Log Out</a>
    </div>
</div>

<div class="admin-container">

    <?php
    // === SHOW CREDENTIAL CARD (one-time session flash) ===
    if (isset($_SESSION['new_student_credentials'])):
        $cred = $_SESSION['new_student_credentials'];
        unset($_SESSION['new_student_credentials']); // Show only once
    ?>
    <div class="cred-card" id="credCard">
        <?php if (!empty($cred['is_reset'])): ?>
            <h3> Student Password Reset Successfully</h3>
            <p>A new One-Time Password (OTP) has been generated. <strong>Share this with the student immediately</strong> — the OTP is shown only once.</p>
        <?php else: ?>
            <h3> Student Account Auto-Created Successfully</h3>
            <p>The following login credentials were generated. <strong>Share these with the student immediately</strong> — the OTP is shown only once.</p>
        <?php endif; ?>
        <table class="cred-table">
            <tr><td>Full Name</td><td><?php echo htmlspecialchars($cred['name']); ?></td></tr>
            <?php if (!empty($cred['department'])): ?>
                <tr><td>Department</td><td><?php echo htmlspecialchars($cred['department']); ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($cred['student_id'])): ?>
                <tr><td>Student ID</td><td><strong><?php echo htmlspecialchars($cred['student_id']); ?></strong></td></tr>
            <?php endif; ?>
            <tr><td>Login Email</td><td><?php echo htmlspecialchars($cred['email']); ?></td></tr>
            <tr><td>One-Time Password</td><td><span class="cred-otp"><?php echo htmlspecialchars($cred['otp']); ?></span></td></tr>
        </table>
        <div class="cred-warning">
             The student will be forced to change this password on their next login.
        </div>
        <div class="cred-actions" style="margin-top:1rem;">
            <button class="btn-print" onclick="window.print()"> Print / Save Credentials</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Flash message -->
    <?php if ($flash): ?>
        <?php
            $flash_colors = [
                'success' => ['background' => '#c6f6d5', 'color' => '#22543d', 'border' => '#9ae6b4'],
                'warning' => ['background' => '#fefcbf', 'color' => '#744210', 'border' => '#f6e05e'],
                'error'   => ['background' => '#fed7d7', 'color' => '#742a2a', 'border' => '#feb2b2']
            ];
            $flash_style = $flash_colors[$flash['type']] ?? $flash_colors['error'];
        ?>
        <div style="padding:.8rem 1.1rem; border-radius:6px; margin-bottom:1.5rem; font-weight:600; font-size:.92rem;
            background:<?php echo $flash_style['background']; ?>;
            color:<?php echo $flash_style['color']; ?>;
            border:1px solid <?php echo $flash_style['border']; ?>;">
            <?php echo htmlspecialchars($flash['msg']); ?>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <h2><?php echo $summary['total_students']; ?></h2>
            <p>Applicants</p>
        </div>
        <div class="summary-card pending">
            <h2><?php echo $summary['pending']; ?></h2>
            <p>Pending</p>
        </div>
        <div class="summary-card approved">
            <h2><?php echo $summary['approved']; ?></h2>
            <p>Approved</p>
        </div>
        <div class="summary-card rejected">
            <h2><?php echo $summary['rejected']; ?></h2>
            <p>Rejected</p>
        </div>
        <div class="summary-card">
            <h2><?php echo $summary['total_accounts'] ?? 0; ?></h2>
            <p>Students</p>
        </div>
        <div class="summary-card">
            <h2><?php echo $summary['total_messages']; ?></h2>
            <p>Messages</p>
        </div>
        <div class="summary-card" style="border-top:3px solid #2b6cb0;">
            <h2 style="color:#2b6cb0;"><?php echo $summary['total_staff'] ?? 0; ?></h2>
            <p>Staff</p>
        </div>
        <div class="summary-card" style="border-top:3px solid #0d7377;">
            <h2 style="color:#0d7377;"><?php echo $summary['total_courses'] ?? 0; ?></h2>
            <p>Courses</p>
        </div>
        <div class="summary-card" style="border-top:3px solid #d4af37;">
            <h2 style="color:#d4af37;"><?php echo $summary['total_grades'] ?? 0; ?></h2>
            <p>Grades Recorded</p>
        </div>
    </div>

    <!-- Tab Switcher -->
    <div class="tabs" style="flex-wrap:wrap;">
        <a href="admin.php?tab=students" class="tab-btn <?php echo $active_tab==='students'?'active':''; ?>"> Applications (<?php echo $summary['total_students']; ?>)</a>
        <a href="admin.php?tab=accounts" class="tab-btn <?php echo $active_tab==='accounts'?'active':''; ?>"> Student Accounts (<?php echo $summary['total_accounts']??0; ?>)</a>
        <a href="admin.php?tab=staff"    class="tab-btn <?php echo $active_tab==='staff'?'active':''; ?>"> Staff (<?php echo $summary['total_staff']??0; ?>)</a>
        <a href="admin.php?tab=courses"  class="tab-btn <?php echo $active_tab==='courses'?'active':''; ?>"> Courses (<?php echo $summary['total_courses']??0; ?>)</a>
        <a href="admin.php?tab=grades"   class="tab-btn <?php echo $active_tab==='grades'?'active':''; ?>"> Grades (<?php echo $summary['total_grades']??0; ?>)<?php if (!empty($summary['pending_grade_requests'])): ?> <span style="background:#e53e3e;color:#fff;border-radius:10px;padding:.05rem .42rem;font-size:.73rem;margin-left:.3rem;"><?php echo $summary['pending_grade_requests']; ?></span><?php endif; ?></a>
        <a href="admin.php?tab=messages" class="tab-btn <?php echo $active_tab==='messages'?'active':''; ?>"> Messages (<?php echo $summary['total_messages']; ?>)</a>
    </div>

    <!-- Panel Content -->
    <div class="panel">

        <?php if ($active_tab === 'students'): ?>

            <h3>Student Application Records</h3>

            <?php if (empty($students)): ?>
                <div class="empty-msg">
                    No student applications have been received yet.<br>
                    Applications submitted through the <a href="admission.php">Admissions page</a> will appear here.
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Contact Info</th>
                            <th>Program / Department</th>
                            <th>Study Mode</th>
                            <th>Uploaded Scans</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td><?php echo $s['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($s['full_name']); ?></strong></td>
                                <td style="font-size: 0.85rem; line-height: 1.4;">
                                     <?php echo htmlspecialchars($s['email']); ?><br>
                                     <?php echo htmlspecialchars($s['phone']); ?>
                                </td>
                                <td style="font-size: 0.85rem; line-height: 1.4;">
                                    <strong><?php echo htmlspecialchars($s['department']); ?></strong><br>
                                    <span style="color: #718096; font-size: 0.8rem;"><?php echo htmlspecialchars($s['program_type'] ?? 'TVET'); ?></span>
                                </td>
                                <td>
                                    <span class="badge" style="background:#edf2f7; color:#2d3748;">
                                        <?php echo htmlspecialchars($s['program_mode'] ?? 'Regular'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <?php if (!empty($s['file_transcript'])): ?>
                                            <a href="<?php echo htmlspecialchars($s['file_transcript']); ?>" target="_blank" class="btn-sm btn-approve" style="text-decoration:none; font-size:0.75rem; text-align:center; display:block;"> Transcript</a>
                                        <?php else: ?>
                                            <span style="color:#a0aec0; font-size:0.75rem;">No Transcript</span>
                                        <?php endif; ?>

                                        <?php if (!empty($s['file_id_card'])): ?>
                                            <a href="<?php echo htmlspecialchars($s['file_id_card']); ?>" target="_blank" class="btn-sm btn-reject" style="text-decoration:none; font-size:0.75rem; text-align:center; display:block; background:#e2e8f0; color:#4a5568;"> ID Card</a>
                                        <?php else: ?>
                                            <span style="color:#a0aec0; font-size:0.75rem;">No ID Card</span>
                                        <?php endif; ?>

                                        <?php if (!empty($s['file_photo'])): ?>
                                            <a href="<?php echo htmlspecialchars($s['file_photo']); ?>" target="_blank" class="btn-sm" style="text-decoration:none; font-size:0.75rem; text-align:center; display:block; background:#ebf8ff; color:#2b6cb0;"> Photo</a>
                                        <?php else: ?>
                                            <span style="color:#a0aec0; font-size:0.75rem;">No Photo</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo date('d M Y, H:i', strtotime($s['created_at'])); ?></td>
                                <td>
                                    <?php
                                        $badge_class = match($s['status']) {
                                            'Approved' => 'badge-approved',
                                            'Rejected' => 'badge-rejected',
                                            default    => 'badge-pending'
                                        };
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars($s['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <?php $has_student_account = isset($student_account_emails[strtolower($s['email'])]); ?>
                                        <?php if (!$has_student_account): ?>
                                            <form method="POST">
                                                <input type="hidden" name="record_id" value="<?php echo $s['id']; ?>">
                                                <input type="hidden" name="new_status" value="Approved">
                                                <button type="submit" name="update_status" class="btn-sm btn-approve">
                                                    <?php echo $s['status'] === 'Approved' ? 'Create Account' : 'Approve'; ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge badge-approved">Account Created</span>
                                        <?php endif; ?>
                                        <?php if ($s['status'] !== 'Rejected'): ?>
                                            <form method="POST">
                                                <input type="hidden" name="record_id" value="<?php echo $s['id']; ?>">
                                                <input type="hidden" name="new_status" value="Rejected">
                                                <button type="submit" name="update_status" class="btn-sm btn-reject">Reject</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this record?');">
                                            <input type="hidden" name="record_id" value="<?php echo $s['id']; ?>">
                                            <button type="submit" name="delete_admission" class="btn-sm btn-delete">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($active_tab === 'messages'): ?>

            <h3>Contact Message Inbox</h3>

            <?php if (empty($messages)): ?>
                <div class="empty-msg">
                    No contact messages have been received yet.<br>
                    Messages submitted through the <a href="contact.php">Contact page</a> will appear here.
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Received On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $m): ?>
                            <tr>
                                <td><?php echo $m['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($m['name']); ?></strong></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($m['email']); ?>"><?php echo htmlspecialchars($m['email']); ?></a></td>
                                <td><?php echo htmlspecialchars($m['subject'] ?: '—'); ?></td>
                                <td style="max-width: 300px;">
                                    <?php 
                                        $msg = $m['message'];
                                        echo htmlspecialchars(strlen($msg) > 100 ? substr($msg, 0, 97) . '...' : $msg);
                                    ?>
                                </td>
                                <td><?php echo date('d M Y, H:i', strtotime($m['created_at'])); ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Delete this message permanently?');">
                                        <input type="hidden" name="record_id" value="<?php echo $m['id']; ?>">
                                        <button type="submit" name="delete_message" class="btn-sm btn-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($active_tab === 'accounts'): ?>

            <h3> Student Portal Accounts</h3>
            <p style="color:#4a5568; font-size:0.9rem; margin-bottom:1.5rem;">
                Accounts are <strong>automatically created</strong> when you approve an application in the Applications tab.
                From here you can view all accounts, reset a student's password, or delete an account.
            </p>

            <!-- Credential card re-uses the same session flash used by auto-creation -->

            <!-- Existing Accounts Table -->
            <?php if (empty($accounts)): ?>
                <div class="empty-msg">
                    No student portal accounts yet. Approve an application in the
                    <a href="admin.php?tab=students">Applications tab</a> to create one automatically.
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email (Login)</th>
                            <th>Department</th>
                            <th>OTP?</th>
                            <th>Enrolled</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($accounts as $a): ?>
                            <tr>
                                <td><?php echo $a['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($a['student_id_no'] ?: '—'); ?></strong></td>
                                <td><?php echo htmlspecialchars($a['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($a['email']); ?></td>
                                <td><?php echo htmlspecialchars($a['department']); ?></td>
                                <td>
                                    <?php if (!empty($a['must_change_password'])): ?>
                                        <span class="badge badge-pending" title="Student has not changed their OTP yet">Pending</span>
                                    <?php else: ?>
                                        <span class="badge badge-approved">Changed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d M Y', strtotime($a['created_at'])); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <form method="POST" onsubmit="return confirm('Reset password for <?php echo addslashes($a['full_name']); ?>? A new OTP will be generated.')">
                                            <input type="hidden" name="account_id" value="<?php echo $a['id']; ?>">
                                            <button type="submit" name="reset_student_password" class="btn-sm btn-approve">Reset OTP</button>
                                        </form>
                                        <form method="POST" onsubmit="return confirm('Permanently delete this student account?');">
                                            <input type="hidden" name="account_id" value="<?php echo $a['id']; ?>">
                                            <button type="submit" name="delete_student_account" class="btn-sm btn-delete">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($active_tab === 'staff'): ?>

            <h3> Staff Account Management</h3>
            <p style="color:#4a5568; font-size:.9rem; margin-bottom:1.5rem;">
                Add instructors, teachers and staff members here. Each staff gets their own login credentials for the Staff Portal.
            </p>

            <!-- Add Staff Form -->
            <div style="background:#f0fff4; border:1px solid #9ae6b4; border-radius:8px; padding:1.5rem; margin-bottom:2rem;">
                <h4 style="color:#22543d; margin-bottom:1rem; font-size:1rem;"> Add New Staff / Instructor</h4>
                <form method="POST" action="admin.php?tab=staff">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div>
                            <label style="display:block; font-size:.83rem; font-weight:700; color:#2d3748; margin-bottom:.3rem;">Full Name *</label>
                            <input type="text" name="sf_name" class="ctrl-admin" placeholder="e.g. Abebe Kebede" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:.83rem; font-weight:700; color:#2d3748; margin-bottom:.3rem;">Email Address *</label>
                            <input type="email" name="sf_email" class="ctrl-admin" placeholder="abebe@mgmbptc.edu.et" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:.83rem; font-weight:700; color:#2d3748; margin-bottom:.3rem;">Department *</label>
                            <select name="sf_dept" class="ctrl-admin" required>
                                <option value="">— Select department —</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Automotive Technology">Automotive Technology</option>
                                <option value="Electrical Technology">Electrical Technology</option>
                                <option value="Construction Technology">Construction Technology</option>
                                <option value="Accounting & Finance">Accounting &amp; Finance</option>
                                <option value="Administration">Administration</option>
                                <option value="General">General</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:.83rem; font-weight:700; color:#2d3748; margin-bottom:.3rem;">Role *</label>
                            <select name="sf_role" class="ctrl-admin">
                                <option value="staff">Staff / Instructor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:.83rem; font-weight:700; color:#2d3748; margin-bottom:.3rem;">Password *</label>
                            <input type="text" name="sf_pass" class="ctrl-admin" placeholder="Set initial password" required>
                        </div>
                        <div style="display:flex; align-items:flex-end;">
                            <button type="submit" name="add_staff"
                                    style="background:#1e4d24; color:#fff; padding:.65rem 1.5rem; border:none; border-radius:5px; font-weight:700; cursor:pointer; font-size:.93rem; width:100%;">
                                 Create Staff Account
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Staff List Table -->
            <?php
            $staff_list = [];
            if ($pdo) {
                $staff_list = $pdo->query("SELECT id, full_name, email, role, department, created_at FROM staff ORDER BY role, full_name")->fetchAll();
            }
            ?>
            <?php if (empty($staff_list)): ?>
                <div class="empty-msg">No staff accounts found.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Email (Login)</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Added On</th>
                            <th>Reset Password</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staff_list as $sf): ?>
                            <tr>
                                <td><?php echo $sf['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($sf['full_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($sf['email']); ?></td>
                                <td><?php echo htmlspecialchars($sf['department']); ?></td>
                                <td>
                                    <span class="badge <?php echo $sf['role']==='admin' ? 'badge-approved' : 'badge-pending'; ?>">
                                        <?php echo strtoupper($sf['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($sf['created_at'])); ?></td>
                                <td>
                                    <form method="POST" action="admin.php?tab=staff"
                                          style="display:flex; gap:.4rem; align-items:center;"
                                          onsubmit="return confirm('Reset password for <?php echo addslashes($sf['full_name']); ?>?');">
                                        <input type="hidden" name="staff_id" value="<?php echo $sf['id']; ?>">
                                        <input type="text" name="new_staff_pass" placeholder="New password"
                                               style="padding:.28rem .5rem; border:1px solid #cbd5e0; border-radius:4px; font-size:.8rem; width:110px;" required>
                                        <button type="submit" name="reset_staff_pass" class="btn-sm btn-approve">Reset</button>
                                    </form>
                                </td>
                                <td>
                                    <?php if ($sf['role'] !== 'admin'): ?>
                                    <form method="POST" action="admin.php?tab=staff"
                                          onsubmit="return confirm('Delete <?php echo addslashes($sf['full_name']); ?>\'s account?');">
                                        <input type="hidden" name="staff_id" value="<?php echo $sf['id']; ?>">
                                        <button type="submit" name="delete_staff" class="btn-sm btn-delete">Delete</button>
                                    </form>
                                    <?php else: ?>
                                        <span style="color:#a0aec0; font-size:.8rem;">Protected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p style="font-size:.8rem; color:#718096; margin-top:.75rem;">
                    Total staff accounts: <strong><?php echo count($staff_list); ?></strong>
                    &nbsp;|&nbsp; Login page: <a href="staff_login.php" target="_blank">staff_login.php</a>
                </p>
            <?php endif; ?>

        <?php elseif ($active_tab === 'courses'): ?>

            <h3> Course Management</h3>

            <!-- Add Course Form -->
            <div style="background:#ebf8ff; border:1px solid #90cdf4; border-radius:8px; padding:1.5rem; margin-bottom:2rem;">
                <h4 style="color:#2b6cb0; margin-bottom:1rem; font-size:1rem;"> Add New Course</h4>
                <form method="POST" action="admin.php?tab=courses">
                    <div style="display:grid; grid-template-columns:1fr 2fr 1fr 1fr auto; gap:1rem; align-items:flex-end;">
                        <div>
                            <label style="display:block; font-size:.83rem; font-weight:700; color:#2d3748; margin-bottom:.3rem;">Code *</label>
                            <input type="text" name="c_code" class="ctrl-admin" placeholder="IT-101" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:.83rem; font-weight:700; color:#2d3748; margin-bottom:.3rem;">Course Title *</label>
                            <input type="text" name="c_title" class="ctrl-admin" placeholder="Introduction to Computing" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:.83rem; font-weight:700; color:#2d3748; margin-bottom:.3rem;">Credits *</label>
                            <input type="number" name="c_cr" class="ctrl-admin" placeholder="3" min="1" max="10" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:.83rem; font-weight:700; color:#2d3748; margin-bottom:.3rem;">Department *</label>
                            <select name="c_dept" class="ctrl-admin" required>
                                <option value="">— Select —</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Automotive Technology">Automotive Technology</option>
                                <option value="Electrical Technology">Electrical Technology</option>
                                <option value="Construction Technology">Construction Technology</option>
                                <option value="Accounting & Finance">Accounting &amp; Finance</option>
                                <option value="All">All Departments</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" name="add_course"
                                    style="background:#2b6cb0; color:#fff; padding:.65rem 1.25rem; border:none; border-radius:5px; font-weight:700; cursor:pointer; font-size:.9rem; white-space:nowrap;">
                                 Add Course
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <?php if (empty($courses_list)): ?>
                <div class="empty-msg">No courses added yet.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Course Title</th>
                            <th>Credits</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses_list as $i => $cr): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><strong><?php echo htmlspecialchars($cr['course_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($cr['course_title']); ?></td>
                                <td style="text-align:center;"><?php echo $cr['credit_hours']; ?></td>
                                <td><?php echo htmlspecialchars($cr['department']); ?></td>
                                <td>
                                    <form method="POST" action="admin.php?tab=courses"
                                          onsubmit="return confirm('Delete course <?php echo addslashes($cr['course_code']); ?>? This will also delete all associated grades.');">
                                        <input type="hidden" name="course_id" value="<?php echo $cr['id']; ?>">
                                        <button type="submit" name="delete_course" class="btn-sm btn-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($active_tab === 'grades'): ?>

            <h3> Grades Overview &amp; Grade Change Requests</h3>

            <?php
            // Pending requests
            $pending_reqs = array_filter($grade_requests ?? [], fn($r) => $r['status'] === 'Pending');
            ?>

            <?php if (!empty($pending_reqs)): ?>
            <!--  PENDING GRADE CHANGE REQUESTS  -->
            <div style="background:#fffbeb; border:2px solid #f6ad55; border-radius:8px; padding:1.5rem; margin-bottom:2rem;">
                <h4 style="color:#c05621; margin-bottom:1rem; font-size:1rem;">
                     Pending Grade Change Requests (<?php echo count($pending_reqs); ?>)
                </h4>
                <table style="font-size:.88rem;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Requested By</th>
                            <th>Old Grade</th>
                            <th>New Grade</th>
                            <th>Reason</th>
                            <th>Requested</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $ri = 1; foreach ($pending_reqs as $pr): ?>
                            <tr>
                                <td><?php echo $ri++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($pr['student_name']); ?></strong><br>
                                    <span style="color:#718096; font-size:.8rem;"><?php echo htmlspecialchars($pr['student_id_no']); ?></span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($pr['course_code']); ?></strong><br>
                                    <span style="color:#718096; font-size:.8rem;"><?php echo htmlspecialchars($pr['course_title']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($pr['semester']); ?></td>
                                <td><?php echo htmlspecialchars($pr['staff_name']); ?></td>
                                <td>
                                    <span style="background:#fed7d7; color:#742a2a; padding:.2rem .5rem; border-radius:4px; font-weight:700;">
                                        <?php echo htmlspecialchars($pr['old_grade']); ?>
                                    </span>
                                    <span style="color:#718096; font-size:.8rem;">(<?php echo number_format($pr['old_gp'], 2); ?>)</span>
                                </td>
                                <td>
                                    <span style="background:#c6f6d5; color:#22543d; padding:.2rem .5rem; border-radius:4px; font-weight:700;">
                                        <?php echo htmlspecialchars($pr['new_grade']); ?>
                                    </span>
                                    <span style="color:#718096; font-size:.8rem;">(<?php echo number_format($pr['new_gp'], 2); ?>)</span>
                                </td>
                                <td style="max-width:200px; font-size:.83rem; color:#4a5568;">
                                    <?php echo htmlspecialchars($pr['reason']); ?>
                                </td>
                                <td style="white-space:nowrap; font-size:.82rem;"><?php echo date('d M Y', strtotime($pr['requested_at'])); ?></td>
                                <td>
                                    <form method="POST" action="admin.php?tab=grades" style="display:flex; flex-direction:column; gap:.4rem; min-width:160px;">
                                        <input type="hidden" name="req_id" value="<?php echo $pr['id']; ?>">
                                        <input type="text" name="admin_note" placeholder="Optional note (optional)"
                                               style="padding:.28rem .5rem; border:1px solid #cbd5e0; border-radius:4px; font-size:.78rem;">
                                        <input type="hidden" name="decision" value="">
                                        <div style="display:flex; gap:.4rem;">
                                            <button type="submit" name="review_grade_request" value="1"
                                                    onclick="this.form.decision.value='Approved'; return confirm('Approve this grade change?')"
                                                    class="btn-sm btn-approve" style="flex:1;"> Approve</button>
                                            <button type="submit" name="review_grade_request" value="1"
                                                    onclick="this.form.decision.value='Rejected'; return confirm('Reject this request?')"
                                                    class="btn-sm btn-reject" style="flex:1;"> Reject</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!--  GRADE HISTORY / ALL REQUESTS  -->
            <?php
            $reviewed_reqs = array_filter($grade_requests ?? [], fn($r) => $r['status'] !== 'Pending');
            if (!empty($reviewed_reqs)):
            ?>
            <details style="margin-bottom:2rem;">
                <summary style="cursor:pointer; font-weight:700; color:#2d3748; padding:.5rem; background:#f7fafc; border:1px solid #e2e8f0; border-radius:5px;">
                     Reviewed Requests (<?php echo count($reviewed_reqs); ?>) — click to expand
                </summary>
                <table style="font-size:.85rem; margin-top:.75rem;">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Staff</th>
                            <th>Old</th>
                            <th>New</th>
                            <th>Status</th>
                            <th>Admin Note</th>
                            <th>Reviewed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviewed_reqs as $rr): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($rr['student_name']); ?><br><span style="color:#718096;font-size:.78rem;"><?php echo htmlspecialchars($rr['student_id_no']); ?></span></td>
                                <td><?php echo htmlspecialchars($rr['course_code']); ?></td>
                                <td><?php echo htmlspecialchars($rr['semester']); ?></td>
                                <td><?php echo htmlspecialchars($rr['staff_name']); ?></td>
                                <td><strong><?php echo htmlspecialchars($rr['old_grade']); ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($rr['new_grade']); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo $rr['status']==='Approved'?'badge-approved':'badge-rejected'; ?>">
                                        <?php echo $rr['status']; ?>
                                    </span>
                                </td>
                                <td style="font-size:.8rem;"><?php echo htmlspecialchars($rr['admin_note'] ?: '—'); ?></td>
                                <td style="font-size:.8rem; white-space:nowrap;"><?php echo $rr['reviewed_at'] ? date('d M Y', strtotime($rr['reviewed_at'])) : '—'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </details>
            <?php endif; ?>

            <!--  ALL GRADES TABLE  -->
            <h4 style="color:#1e3f20; margin-bottom:1rem; font-size:1rem; padding-bottom:.5rem; border-bottom:2px solid #1e3f20;">
                 All Student Grades
            </h4>

            <?php
            // Filter by dept or student
            $filter_dept    = trim($_GET['dept']    ?? '');
            $filter_student = trim($_GET['student'] ?? '');
            $departments    = array_unique(array_column($grades_overview ?? [], 'student_dept'));
            sort($departments);

            $filtered = $grades_overview ?? [];
            if ($filter_dept) {
                $filtered = array_filter($filtered, fn($g) => $g['student_dept'] === $filter_dept);
            }
            if ($filter_student) {
                $filtered = array_filter($filtered, fn($g) => stripos($g['student_name'], $filter_student) !== false || stripos($g['student_id_no'], $filter_student) !== false);
            }
            ?>

            <!-- Filter bar -->
            <form method="GET" action="admin.php" style="display:flex; gap:.75rem; align-items:flex-end; flex-wrap:wrap; margin-bottom:1.25rem;">
                <input type="hidden" name="tab" value="grades">
                <div>
                    <label style="display:block; font-size:.8rem; font-weight:700; color:#4a5568; margin-bottom:.25rem;">Filter by Department</label>
                    <select name="dept" style="padding:.5rem .75rem; border:1px solid #cbd5e0; border-radius:5px; font-size:.9rem;">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?php echo htmlspecialchars($d); ?>" <?php echo $filter_dept===$d?'selected':''; ?>>
                                <?php echo htmlspecialchars($d); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:.8rem; font-weight:700; color:#4a5568; margin-bottom:.25rem;">Search Student</label>
                    <input type="text" name="student" value="<?php echo htmlspecialchars($filter_student); ?>"
                           placeholder="Name or Student ID"
                           style="padding:.5rem .75rem; border:1px solid #cbd5e0; border-radius:5px; font-size:.9rem; min-width:220px;">
                </div>
                <div style="display:flex; gap:.5rem;">
                    <button type="submit" style="background:#1e3f20; color:#fff; padding:.5rem 1.1rem; border:none; border-radius:5px; font-weight:700; cursor:pointer;">Filter</button>
                    <a href="admin.php?tab=grades" style="background:#edf2f7; color:#4a5568; padding:.5rem 1rem; border-radius:5px; font-weight:600; text-decoration:none; font-size:.9rem;">Clear</a>
                </div>
            </form>

            <?php if (empty($filtered)): ?>
                <div class="empty-msg">
                    <?php echo empty($grades_overview) ? 'No grades have been recorded yet. Staff enter grades via the <a href="teacher_grades.php">Grade Entry</a> page.' : 'No grades match the current filter.'; ?>
                </div>
            <?php else: ?>
                <div style="overflow-x:auto;">
                    <table style="font-size:.875rem;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Dept</th>
                                <th>Course</th>
                                <th>Semester</th>
                                <th>Grade</th>
                                <th>GP</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $gi = 1; foreach ($filtered as $gr): ?>
                                <tr>
                                    <td><?php echo $gi++; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($gr['student_name']); ?></strong><br>
                                        <span style="color:#718096; font-size:.78rem;"><?php echo htmlspecialchars($gr['student_id_no']); ?></span>
                                    </td>
                                    <td style="font-size:.82rem;"><?php echo htmlspecialchars($gr['student_dept']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($gr['course_code']); ?></strong><br>
                                        <span style="color:#718096; font-size:.78rem;"><?php echo htmlspecialchars($gr['course_title']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($gr['semester']); ?></td>
                                    <td>
                                        <?php
                                        $first = $gr['grade'][0] ?? '';
                                        $gc = match($first) {
                                            'A' => 'background:#c6f6d5;color:#22543d',
                                            'B' => 'background:#bee3f8;color:#2a4365',
                                            'C' => 'background:#fefcbf;color:#744210',
                                            'D','F' => 'background:#fed7d7;color:#742a2a',
                                            default => 'background:#e2e8f0;color:#4a5568'
                                        };
                                        ?>
                                        <span style="<?php echo $gc; ?>; padding:.2rem .55rem; border-radius:4px; font-weight:700; font-size:.88rem;">
                                            <?php echo htmlspecialchars($gr['grade']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align:center;"><?php echo number_format($gr['grade_point'], 2); ?></td>
                                    <td>
                                        <span style="font-size:.78rem; padding:.18rem .5rem; border-radius:3px; font-weight:600;
                                            <?php echo $gr['status']==='Completed' ? 'background:#c6f6d5;color:#22543d' : 'background:#e9d8fd;color:#44337a'; ?>">
                                            <?php echo htmlspecialchars($gr['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size:.8rem; color:#718096; margin-top:.75rem;">
                    Showing <strong><?php echo count($filtered); ?></strong> of <strong><?php echo count($grades_overview); ?></strong> grade records.
                </p>
            <?php endif; ?>

        <?php endif; ?>

    </div><!-- end panel -->
</div><!-- end admin-container -->

<style>
.ctrl-admin {
    width: 100%;
    padding: .6rem .75rem;
    border: 1px solid #cbd5e0;
    border-radius: 5px;
    font-size: .9rem;
    font-family: inherit;
    background: #fff;
}
.ctrl-admin:focus { outline: none; border-color: #1e3f20; }
</style>

</body>
</html>
