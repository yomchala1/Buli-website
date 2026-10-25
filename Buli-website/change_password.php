<?php
session_start();

// Must be logged in as a student
if (!isset($_SESSION['student_id'])) {
    header('Location: student_login.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$student_id = $_SESSION['student_id'];
$error = '';
$success = '';

// Verify the student actually needs to change their password (prevent URL bypass)
if ($pdo) {
    $chk = $pdo->prepare("SELECT must_change_password FROM students WHERE id = :id");
    $chk->execute([':id' => $student_id]);
    $row = $chk->fetch();
    // If already changed, send them to the portal
    if ($row && !$row['must_change_password']) {
        header('Location: student_portal.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_submit'])) {
    $new_pass    = $_POST['new_password']     ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (strlen($new_pass) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($new_pass !== $confirm_pass) {
        $error = 'Passwords do not match. Please try again.';
    } else {
        if ($pdo) {
            try {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE students SET password_hash = :hash, must_change_password = 0 WHERE id = :id");
                $stmt->execute([':hash' => $hash, ':id' => $student_id]);
                // Redirect to portal — password is now set
                header('Location: student_portal.php');
                exit;
            } catch (\PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } else {
            // Offline demo: just redirect
            header('Location: student_portal.php');
            exit;
        }
    }
}

$page_title = 'Set Your Password';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .change-wrapper {
        max-width: 480px;
        margin: 4rem auto;
        padding: 0 1.5rem;
    }
    .change-card {
        background: #fff;
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        padding: 2.5rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    .otp-banner {
        background: #fefcbf;
        border: 1px solid #f6e05e;
        border-radius: 6px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.75rem;
        font-size: 0.9rem;
        color: #744210;
        line-height: 1.6;
    }
    .otp-banner strong {
        display: block;
        font-size: 1rem;
        color: #744210;
        margin-bottom: 0.25rem;
    }
    .change-card h2 {
        color: #1e3f20;
        font-size: 1.4rem;
        margin-bottom: 0.4rem;
        text-align: center;
    }
    .change-card .subtitle {
        color: #718096;
        font-size: 0.85rem;
        text-align: center;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 1rem;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
        color: #2d3748;
    }
    .form-control-cp {
        width: 100%;
        padding: 0.65rem 0.75rem;
        border: 1px solid #cbd5e0;
        border-radius: 4px;
        font-size: 0.95rem;
        font-family: inherit;
    }
    .form-control-cp:focus {
        outline: none;
        border-color: #1e3f20;
    }
    /* Password strength indicator */
    .strength-bar {
        height: 5px;
        border-radius: 3px;
        background: #e2e8f0;
        margin-top: 6px;
        overflow: hidden;
    }
    .strength-fill {
        height: 100%;
        width: 0;
        transition: width 0.3s, background 0.3s;
    }
    .strength-label {
        font-size: 0.75rem;
        color: #718096;
        margin-top: 4px;
    }
    .btn-change {
        width: 100%;
        padding: 0.75rem;
        background-color: #1e3f20;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        margin-top: 0.5rem;
    }
    .btn-change:hover { background-color: #2b6cb0; }
    .alert-error {
        background: #fed7d7;
        color: #742a2a;
        border: 1px solid #feb2b2;
        border-radius: 4px;
        padding: 0.75rem;
        margin-bottom: 1.25rem;
        font-size: 0.9rem;
    }
    .rules-list {
        font-size: 0.82rem;
        color: #718096;
        margin-top: 0.5rem;
        padding-left: 1.25rem;
    }
    .rules-list li { margin-bottom: 0.2rem; }
</style>

<div class="change-wrapper">
    <div class="change-card">
        <h2> Set Your New Password</h2>
        <p class="subtitle">Welcome to MGMBPTC Student Portal</p>

        <div class="otp-banner">
            <strong> First Login Detected</strong>
            You logged in with a temporary one-time password issued by the Registrar's Office.
            You must set a new personal password before accessing your portal.
        </div>

        <?php if ($error): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="change_password.php">
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="new_password"
                       class="form-control-cp" placeholder="Minimum 6 characters"
                       oninput="checkStrength(this.value)" required>
                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                <div class="strength-label" id="strengthLabel">Enter a password</div>
                <ul class="rules-list">
                    <li>At least 6 characters</li>
                    <li>Mix of letters, numbers, and symbols recommended</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input type="password" id="confirmPassword" name="confirm_password"
                       class="form-control-cp" placeholder="Re-enter your password" required>
            </div>
            <button type="submit" name="change_submit" class="btn-change">
                 Save Password & Enter Portal
            </button>
        </form>
    </div>
</div>

<script>
function checkStrength(password) {
    let fill = document.getElementById('strengthFill');
    let label = document.getElementById('strengthLabel');
    let score = 0;

    if (password.length >= 6)  score++;
    if (password.length >= 10) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[A-Za-z0-9]/.test(password)) score++;

    const levels = [
        { pct: '0%',   color: '#e2e8f0', text: 'Enter a password' },
        { pct: '25%',  color: '#fc8181', text: 'Weak' },
        { pct: '50%',  color: '#f6ad55', text: 'Fair' },
        { pct: '75%',  color: '#68d391', text: 'Good' },
        { pct: '100%', color: '#48bb78', text: 'Strong ' }
    ];

    const level = levels[Math.min(score, 4)];
    fill.style.width  = level.pct;
    fill.style.background = level.color;
    label.textContent = level.text;
    label.style.color = score >= 3 ? '#276749' : '#718096';
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
