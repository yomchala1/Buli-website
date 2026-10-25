<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM staff WHERE email = :email");
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    if ($user['role'] === 'admin') {
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_name'] = $user['full_name'];
                        header('Location: admin.php');
                        exit;
                    } else {
                        $_SESSION['staff_logged_in'] = true;
                        $_SESSION['staff_id'] = $user['id'];
                        $_SESSION['staff_name'] = $user['full_name'];
                        $_SESSION['staff_dept'] = $user['department'];
                        header('Location: staff_portal.php');
                        exit;
                    }
                } else {
                    $login_error = 'Invalid email or password.';
                }
            } catch (\PDOException $e) {
                $login_error = 'Database error: ' . $e->getMessage();
            }
        } else {
            // Simulated login fallback (Offline demo)
            if ($email === 'admin@mgmbptc.edu.et' && $password === 'buliadmin123') {
                $_SESSION['admin_logged_in'] = true;
                header('Location: admin.php');
                exit;
            } elseif ($email === 'tsegaye@mgmbptc.edu.et' && $password === 'staff123') {
                $_SESSION['staff_logged_in'] = true;
                $_SESSION['staff_id'] = 2;
                $_SESSION['staff_name'] = 'Tsegaye Mengistu';
                $_SESSION['staff_dept'] = 'Information Technology';
                header('Location: staff_portal.php');
                exit;
            } else {
                $login_error = '[Demo Mode] Admin: admin@mgmbptc.edu.et / buliadmin123, Staff: tsegaye@mgmbptc.edu.et / staff123';
            }
        }
    } else {
        $login_error = 'Please fill in all fields.';
    }
}

$page_title = 'Staff & Admin Login';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    /*  Full-page split layout  */
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

    /*  Left panel (branding)  */
    .login-brand {
        background: linear-gradient(160deg, #1e3f20 0%, #2b6cb0 100%);
        color: #fff;
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        position: relative;
        overflow: hidden;
    }
    .login-brand::before {
        content: '';
        position: absolute;
        width: 320px; height: 320px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
        top: -80px; right: -80px;
    }
    .login-brand::after {
        content: '';
        position: absolute;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,.04);
        bottom: -40px; left: -40px;
    }
    .login-brand .brand-icon {
        font-size: 3rem;
        margin-bottom: 1.25rem;
        display: block;
    }
    .login-brand h2 {
        font-size: 1.55rem;
        font-weight: 800;
        color: #fff;
        line-height: 1.3;
        margin-bottom: .75rem;
        position: relative;
        z-index: 1;
    }
    .login-brand p {
        font-size: .88rem;
        color: rgba(255,255,255,.78);
        line-height: 1.65;
        position: relative;
        z-index: 1;
        margin-bottom: 0;
    }
    .login-brand .brand-badge {
        display: inline-block;
        margin-top: 1.5rem;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.3);
        color: #fff;
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: .3rem .85rem;
        border-radius: 20px;
        position: relative;
        z-index: 1;
    }
    @media(max-width:700px){ .login-brand { padding:2rem; } }

    /*  Right panel (form)  */
    .login-form-panel {
        background: #fff;
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    @media(max-width:700px){ .login-form-panel { padding:2rem; } }
    .login-form-panel h3 {
        font-size: 1.3rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: .3rem;
    }
    .login-form-panel .form-sub {
        font-size: .85rem;
        color: #718096;
        margin-bottom: 1.75rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    /*  Form fields  */
    .lf-group { margin-bottom: 1.2rem; }
    .lf-group label {
        display: block;
        font-size: .82rem;
        font-weight: 700;
        color: #4a5568;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .4rem;
    }
    .lf-input-wrap { position: relative; }
    .lf-input-wrap .lf-icon {
        position: absolute;
        left: .85rem; top: 50%;
        transform: translateY(-50%);
        font-size: 1rem;
        color: #a0aec0;
        pointer-events: none;
    }
    .lf-input {
        width: 100%;
        padding: .7rem .75rem .7rem 2.4rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: .93rem;
        font-family: inherit;
        background: #f8fafc;
        transition: border-color .2s, box-shadow .2s;
    }
    .lf-input:focus {
        outline: none;
        border-color: #2b6cb0;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(43,108,176,.12);
    }

    /*  Submit button  */
    .lf-btn {
        width: 100%;
        padding: .78rem;
        background: linear-gradient(135deg, #1e3f20, #2b6cb0);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .97rem;
        font-weight: 700;
        cursor: pointer;
        margin-top: .5rem;
        letter-spacing: .02em;
        transition: opacity .2s, transform .15s;
    }
    .lf-btn:hover { opacity: .92; transform: translateY(-1px); }
    .lf-btn:active { transform: translateY(0); }

    /*  Error alert  */
    .lf-alert {
        display: flex; align-items: flex-start; gap: .6rem;
        padding: .75rem 1rem;
        background: #fff5f5;
        border: 1px solid #fed7d7;
        border-left: 4px solid #e53e3e;
        border-radius: 8px;
        color: #742a2a;
        font-size: .87rem;
        margin-bottom: 1.25rem;
        line-height: 1.5;
    }
    .lf-alert .alert-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: .05rem; }

    /*  Footer link  */
    .lf-footer {
        margin-top: 1.5rem;
        text-align: center;
        font-size: .82rem;
        color: #a0aec0;
    }
    .lf-footer a { color: #2b6cb0; font-weight: 600; }
</style>

<div class="login-page">
    <div class="login-box">

        <!-- Left branding panel -->
        <div class="login-brand">
            <span class="brand-icon"></span>
            <h2>Staff &amp; Admin Portal</h2>
            <p>Manage admissions, publish news, enter student grades, and oversee college operations — all in one place.</p>
            <span class="brand-badge">MGMBPTC Staff Access</span>
        </div>

        <!-- Right form panel -->
        <div class="login-form-panel">
            <h3>Welcome Back</h3>
            <p class="form-sub">Sign in with your college staff email and password.</p>

            <?php if ($login_error): ?>
                <div class="lf-alert">
                    <span class="alert-icon"></span>
                    <span><?php echo htmlspecialchars($login_error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="staff_login.php">
                <div class="lf-group">
                    <label for="staffEmail">Email Address</label>
                    <div class="lf-input-wrap">
                        <span class="lf-icon"></span>
                        <input type="email" id="staffEmail" name="email" class="lf-input"
                               placeholder="name@mgmbptc.edu.et" required
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>
                <div class="lf-group">
                    <label for="staffPassword">Password</label>
                    <div class="lf-input-wrap">
                        <span class="lf-icon"></span>
                        <input type="password" id="staffPassword" name="password" class="lf-input"
                               placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" name="login_submit" class="lf-btn">Sign In &rarr;</button>
            </form>

            <div class="lf-footer">
                <a href="index.php"> Back to College Website</a>
            </div>
        </div>

    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
