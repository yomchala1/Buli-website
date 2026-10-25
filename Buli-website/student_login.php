<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$login_error = '';

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM students WHERE email = :email");
                $stmt->execute([':email' => $email]);
                $student = $stmt->fetch();

                if ($student && password_verify($password, $student['password_hash'])) {
                    $_SESSION['student_id']   = $student['id'];
                    $_SESSION['student_name'] = $student['full_name'];

                    // Force password change on first login (OTP detected)
                    if (!empty($student['must_change_password'])) {
                        header('Location: change_password.php');
                    } else {
                        header('Location: student_portal.php');
                    }
                    exit;
                } else {
                    $login_error = 'Invalid email or password. Contact the Registrar if you have not received your credentials.';
                }
            } catch (\PDOException $e) {
                $login_error = 'Database error: ' . $e->getMessage();
            }
        } else {
            // Offline demo fallback
            if ($email === 'student@mgmbptc.edu.et' && $password === 'student123') {
                $_SESSION['student_id'] = 999;
                $_SESSION['student_name'] = 'Demo Student';
                header('Location: student_portal.php');
                exit;
            } else {
                $login_error = 'Invalid credentials. (Demo: student@mgmbptc.edu.et / student123)';
            }
        }
    } else {
        $login_error = 'Please fill in all fields.';
    }
}

$page_title = 'Student Portal Login';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .login-page {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }
    .login-box {
        display: grid;
        grid-template-columns: 1fr 1fr;
        max-width: 900px;
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,.18);
    }
    @media(max-width:700px){ .login-box { grid-template-columns:1fr; } }

    .login-brand {
        background: linear-gradient(160deg, #1a4480 0%, #0d7377 100%);
        color: #fff;
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .login-brand::before {
        content: '';
        position: absolute;
        width: 300px; height: 300px; border-radius: 50%;
        background: rgba(255,255,255,.07);
        top: -70px; right: -70px;
    }
    .login-brand::after {
        content: '';
        position: absolute;
        width: 180px; height: 180px; border-radius: 50%;
        background: rgba(255,255,255,.05);
        bottom: -30px; left: -30px;
    }
    .login-brand .brand-icon { font-size: 3rem; margin-bottom: 1.25rem; display: block; }
    .login-brand h2 { font-size: 1.55rem; font-weight: 800; color: #fff; line-height: 1.3; margin-bottom: .75rem; position: relative; z-index: 1; }
    .login-brand p  { font-size: .88rem; color: rgba(255,255,255,.78); line-height: 1.65; position: relative; z-index: 1; margin-bottom: 0; }
    .login-brand .brand-badge {
        display: inline-block; margin-top: 1.5rem;
        background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3);
        color: #fff; font-size: .78rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; padding: .3rem .85rem; border-radius: 20px;
        position: relative; z-index: 1;
    }
    .login-brand .feature-list { list-style: none; margin-top: 1.5rem; position: relative; z-index: 1; }
    .login-brand .feature-list li {
        font-size: .83rem; color: rgba(255,255,255,.8);
        padding: .3rem 0; display: flex; align-items: center; gap: .5rem;
    }
    .login-brand .feature-list li::before { content: ''; color: #68d391; font-weight: 700; }
    @media(max-width:700px){ .login-brand { padding:2rem; } }

    .login-form-panel {
        background: #fff;
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    @media(max-width:700px){ .login-form-panel { padding:2rem; } }
    .login-form-panel h3 { font-size: 1.3rem; font-weight: 800; color: #1a202c; margin-bottom: .3rem; }
    .login-form-panel .form-sub { font-size: .85rem; color: #718096; margin-bottom: 1.75rem; padding-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; }

    .lf-group { margin-bottom: 1.2rem; }
    .lf-group label { display: block; font-size: .82rem; font-weight: 700; color: #4a5568; text-transform: uppercase; letter-spacing: .04em; margin-bottom: .4rem; }
    .lf-input-wrap { position: relative; }
    .lf-input-wrap .lf-icon { position: absolute; left: .85rem; top: 50%; transform: translateY(-50%); font-size: 1rem; color: #a0aec0; pointer-events: none; }
    .lf-input {
        width: 100%; padding: .7rem .75rem .7rem 2.4rem;
        border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: .93rem; font-family: inherit; background: #f8fafc;
        transition: border-color .2s, box-shadow .2s;
    }
    .lf-input:focus { outline: none; border-color: #0d7377; background: #fff; box-shadow: 0 0 0 3px rgba(13,115,119,.12); }

    .lf-btn {
        width: 100%; padding: .78rem;
        background: linear-gradient(135deg, #1a4480, #0d7377);
        color: #fff; border: none; border-radius: 8px;
        font-size: .97rem; font-weight: 700; cursor: pointer;
        margin-top: .5rem; letter-spacing: .02em;
        transition: opacity .2s, transform .15s;
    }
    .lf-btn:hover { opacity: .92; transform: translateY(-1px); }

    .lf-alert {
        display: flex; align-items: flex-start; gap: .6rem;
        padding: .75rem 1rem;
        background: #fff5f5; border: 1px solid #fed7d7;
        border-left: 4px solid #e53e3e;
        border-radius: 8px; color: #742a2a;
        font-size: .87rem; margin-bottom: 1.25rem; line-height: 1.5;
    }
    .lf-alert .alert-icon { font-size: 1.1rem; flex-shrink: 0; }

    .lf-notice {
        margin-top: 1.5rem; padding: 1rem;
        background: #ebf8ff; border: 1px solid #bee3f8; border-radius: 8px;
        font-size: .82rem; color: #2a69ac; line-height: 1.6; text-align: center;
    }
    .lf-notice strong { display: block; margin-bottom: .25rem; color: #1a4480; }
    .lf-footer { margin-top: 1.25rem; text-align: center; font-size: .82rem; color: #a0aec0; }
    .lf-footer a { color: #1a4480; font-weight: 600; }
</style>

<div class="login-page">
    <div class="login-box">

        <!-- Left branding -->
        <div class="login-brand">
            <span class="brand-icon"></span>
            <h2>Student Portal</h2>
            <p>Access your academic records, grades, course registrations, and college resources.</p>
            <ul class="feature-list">
                <li>View teacher-reported grades</li>
                <li>Register for courses by semester</li>
                <li>Download academic resources</li>
                <li>Track your CGPA progress</li>
            </ul>
            <span class="brand-badge">MGMBPTC Student Access</span>
        </div>

        <!-- Right form -->
        <div class="login-form-panel">
            <h3>Student Sign In</h3>
            <p class="form-sub">Enter your college-issued email and password to continue.</p>

            <?php if ($login_error): ?>
                <div class="lf-alert">
                    <span class="alert-icon"></span>
                    <span><?php echo htmlspecialchars($login_error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="student_login.php">
                <div class="lf-group">
                    <label for="loginEmail">Student Email</label>
                    <div class="lf-input-wrap">
                        <span class="lf-icon"></span>
                        <input type="email" id="loginEmail" name="email" class="lf-input"
                               placeholder="yourname@mgmbptc.edu.et" required
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>
                <div class="lf-group">
                    <label for="loginPassword">Password</label>
                    <div class="lf-input-wrap">
                        <span class="lf-icon"></span>
                        <input type="password" id="loginPassword" name="password" class="lf-input"
                               placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" name="login_submit" class="lf-btn">Sign In to Portal &rarr;</button>
            </form>

            <div class="lf-notice">
                <strong> No account yet?</strong>
                Accounts are issued by the Registrar after enrollment.
                <a href="contact.php">Contact the Registrar</a> for help.
            </div>

            <div class="lf-footer">
                <a href="index.php"> Back to College Website</a>
            </div>
        </div>

    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
