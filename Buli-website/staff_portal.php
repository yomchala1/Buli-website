<?php
session_start();

if (!isset($_SESSION['staff_logged_in'])) {
    header('Location: staff_login.php');
    exit;
}

$page_title = 'Staff Portal';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$staff_name = $_SESSION['staff_name'];
$staff_dept = $_SESSION['staff_dept'];
$staff_id   = $_SESSION['staff_id'];

$status_msg  = '';
$status_type = '';
$active_tab = 'tabNews';

if (empty($_SESSION['staff_password_csrf'])) {
    $_SESSION['staff_password_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_staff_password'])) {
    $active_tab = 'tabProfile';
    $submitted_token = $_POST['csrf_token'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!is_string($submitted_token) || !hash_equals($_SESSION['staff_password_csrf'], $submitted_token)) {
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
            $password_stmt = $pdo->prepare("SELECT password_hash FROM staff WHERE id = :id");
            $password_stmt->execute([':id' => $staff_id]);
            $staff_account = $password_stmt->fetch();

            if (!$staff_account || !password_verify($current_password, $staff_account['password_hash'])) {
                $status_msg = 'Your current password is incorrect.';
                $status_type = 'error';
            } elseif (password_verify($new_password, $staff_account['password_hash'])) {
                $status_msg = 'Choose a new password that is different from your current password.';
                $status_type = 'error';
            } else {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                if ($new_password_hash === false) {
                    throw new RuntimeException('Password hashing failed.');
                }

                $update_password = $pdo->prepare("UPDATE staff SET password_hash = :hash WHERE id = :id");
                $update_password->execute([':hash' => $new_password_hash, ':id' => $staff_id]);
                $_SESSION['staff_password_csrf'] = bin2hex(random_bytes(32));
                $status_msg = 'Your password has been changed successfully.';
                $status_type = 'success';
            }
        } catch (\PDOException $e) {
            error_log('Staff password change failed: ' . $e->getMessage());
            $status_msg = 'Unable to change your password due to a database error. Please try again.';
            $status_type = 'error';
        }
    }
}

//  Handle profile photo upload 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_photo'])) {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['profile_photo'];
        $allowed  = ['image/jpeg','image/png','image/webp','image/gif'];
        $max_size = 2 * 1024 * 1024; // 2 MB

        if (!in_array($file['type'], $allowed)) {
            $status_msg  = 'Only JPG, PNG, WEBP, or GIF images are allowed.';
            $status_type = 'error';
        } elseif ($file['size'] > $max_size) {
            $status_msg  = 'Image must be under 2 MB.';
            $status_type = 'error';
        } else {
            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'staff_' . $staff_id . '_' . time() . '.' . $ext;
            $dest     = __DIR__ . '/uploads/profiles/' . $filename;

            if (!is_dir(__DIR__ . '/uploads/profiles/')) {
                mkdir(__DIR__ . '/uploads/profiles/', 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                if ($pdo) {
                    try {
                        $upd = $pdo->prepare("UPDATE staff SET profile_photo = :photo WHERE id = :id");
                        $upd->execute([':photo' => 'uploads/profiles/' . $filename, ':id' => $staff_id]);
                        $status_msg  = 'Profile photo updated successfully.';
                        $status_type = 'success';
                    } catch (\PDOException $e) {
                        $status_msg  = 'DB error: ' . $e->getMessage();
                        $status_type = 'error';
                    }
                } else {
                    $status_msg  = '[Demo] Photo saved locally but database is offline.';
                    $status_type = 'warning';
                }
            } else {
                $status_msg  = 'Failed to save uploaded file.';
                $status_type = 'error';
            }
        }
    } else {
        $status_msg  = 'Please choose a photo file to upload.';
        $status_type = 'error';
    }
}

//  Handle news/event publish 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_news'])) {
    $title_en   = trim($_POST['title_en']   ?? '');
    $title_am   = trim($_POST['title_am']   ?? '');
    $content_en = trim($_POST['content_en'] ?? '');
    $content_am = trim($_POST['content_am'] ?? '');
    $type        = $_POST['type']           ?? 'news';
    $date_posted = date('Y-m-d');

    if (!empty($title_en) && !empty($title_am) && !empty($content_en) && !empty($content_am)) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO news_events (title_en, title_am, content_en, content_am, type, date_posted)
                     VALUES (:title_en, :title_am, :content_en, :content_am, :type, :date_posted)"
                );
                $stmt->execute([
                    ':title_en'    => $title_en,
                    ':title_am'    => $title_am,
                    ':content_en'  => $content_en,
                    ':content_am'  => $content_am,
                    ':type'        => $type,
                    ':date_posted' => $date_posted
                ]);
                $status_msg  = 'Post published successfully!';
                $status_type = 'success';
            } catch (\PDOException $e) {
                $status_msg  = 'Database error: ' . $e->getMessage();
                $status_type = 'error';
            }
        } else {
            $status_msg  = '[Demo Mode] Post validated. (Database disconnected)';
            $status_type = 'success';
        }
    } else {
        $status_msg  = 'Please fill in all fields (both English and Amharic).';
        $status_type = 'error';
    }
}

//  Fetch staff profile from DB 
$staff_profile = null;
if ($pdo) {
    try {
        $sp = $pdo->prepare("SELECT * FROM staff WHERE id = :id");
        $sp->execute([':id' => $staff_id]);
        $staff_profile = $sp->fetch();
    } catch (\PDOException $e) { /* silently skip */ }
}

//  Fetch published posts 
$posts = [];
if ($pdo) {
    try {
        $posts = $pdo->query("SELECT * FROM news_events ORDER BY date_posted DESC")->fetchAll();
    } catch (\PDOException $e) { /* silently skip */ }
}
?>

<style>
    .sp-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    /*  Top header bar  */
    .sp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #1e4d24, #2b6cb0);
        color: #fff;
        padding: 1.25rem 2rem;
        border-radius: 8px;
        margin-bottom: 1.75rem;
        box-shadow: 0 4px 12px rgba(0,0,0,.12);
    }
    .sp-header-left { display: flex; align-items: center; gap: 1rem; }
    .sp-avatar {
        width: 56px; height: 56px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,.5);
        background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; color: #fff; font-weight: 700;
        overflow: hidden; flex-shrink: 0;
    }
    .sp-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .sp-header h2 { font-size: 1.3rem; margin: 0; }
    .sp-header p  { font-size: 0.83rem; color: #ebf8ff; margin: .15rem 0 0; }
    .role-badge {
        display: inline-block;
        background: rgba(255,255,255,.22);
        border: 1px solid rgba(255,255,255,.4);
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: .15rem .55rem;
        border-radius: 20px;
        margin-left: .4rem;
        vertical-align: middle;
    }
    .sp-header-actions { display: flex; gap: .6rem; align-items: center; flex-wrap: wrap; }
    .btn-nav {
        background: rgba(255,255,255,.18);
        color: #fff;
        padding: .42rem .95rem;
        border-radius: 5px;
        font-size: .82rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid rgba(255,255,255,.32);
        transition: background .2s;
        white-space: nowrap;
    }
    .btn-nav:hover { background: rgba(255,255,255,.32); color: #fff; }
    .btn-logout {
        background: #e53e3e; color: #fff;
        padding: .42rem .95rem;
        border-radius: 5px; font-weight: 700;
        text-decoration: none; font-size: .82rem;
    }
    .btn-logout:hover { background: #c53030; color: #fff; }

    /*  Tabs  */
    .sp-tabs {
        display: flex; gap: .3rem;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }
    .sp-tab-btn {
        background: none; border: none;
        padding: .8rem 1.35rem;
        font-size: .92rem; font-weight: 600;
        color: #4a5568; cursor: pointer;
        border-radius: 6px 6px 0 0;
        border: 1px solid transparent;
        margin-bottom: -2px;
        transition: all .18s;
    }
    .sp-tab-btn:hover { color: #1e3f20; background: #f7fafc; }
    .sp-tab-btn.active {
        color: #1e3f20; background: #fff;
        border-color: #cbd5e0 #cbd5e0 #fff #cbd5e0;
    }
    .sp-tab-pane { display: none; }
    .sp-tab-pane.active { display: block; }

    /*  Cards  */
    .sp-card {
        background: #fff; border: 1px solid #cbd5e0;
        border-radius: 8px; padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
    }
    .sp-card-title {
        color: #1e3f20; font-size: 1.05rem; font-weight: 700;
        border-bottom: 2px solid #1e3f20;
        padding-bottom: .5rem; margin-bottom: 1.25rem;
    }

    /*  Alerts  */
    .sp-alert {
        padding: .85rem 1rem; border-radius: 5px;
        margin-bottom: 1.5rem; font-size: .93rem; font-weight: 500;
    }
    .sp-alert-success { background: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
    .sp-alert-error   { background: #fed7d7; color: #742a2a; border: 1px solid #feb2b2; }
    .sp-alert-warning { background: #fefcbf; color: #744210; border: 1px solid #f6e05e; }

    /*  Form controls  */
    .fg { margin-bottom: 1.2rem; }
    .fg label {
        display: block; font-weight: 600;
        font-size: .88rem; color: #2d3748; margin-bottom: .35rem;
    }
    .ctrl {
        width: 100%; padding: .62rem .75rem;
        border: 1px solid #cbd5e0; border-radius: 5px;
        font-size: .93rem; font-family: inherit; background: #fff;
    }
    .ctrl:focus { outline: none; border-color: #2b6cb0; box-shadow: 0 0 0 3px rgba(43,108,176,.12); }
    textarea.ctrl { resize: vertical; min-height: 100px; }
    .btn-primary {
        background: #2b6cb0; color: #fff;
        padding: .7rem 1.5rem; border: none;
        border-radius: 5px; font-size: .95rem; font-weight: 700; cursor: pointer;
    }
    .btn-primary:hover { background: #2c5282; }
    .btn-green {
        background: #1e4d24; color: #fff;
        padding: .7rem 1.5rem; border: none;
        border-radius: 5px; font-size: .95rem; font-weight: 700; cursor: pointer;
    }
    .btn-green:hover { background: #153618; }

    /*  Profile tab layout  */
    .profile-layout { display: grid; grid-template-columns: 240px 1fr; gap: 1.5rem; }
    @media (max-width: 700px) { .profile-layout { grid-template-columns: 1fr; } }
    .profile-photo-box {
        text-align: center; padding: 1.5rem 1rem;
        border: 1px solid #e2e8f0; border-radius: 8px; background: #f7fafc;
    }
    .profile-photo-box img {
        width: 120px; height: 120px; border-radius: 50%;
        object-fit: cover; border: 4px solid #1e4d24;
        margin-bottom: .75rem;
    }
    .profile-photo-box .avatar-placeholder {
        width: 120px; height: 120px; border-radius: 50%;
        background: linear-gradient(135deg, #1e4d24, #2b6cb0);
        color: #fff; font-size: 2.8rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto .75rem;
    }
    .profile-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 600px) { .profile-info-grid { grid-template-columns: 1fr; } }
    .pi-item span { display: block; font-size: .75rem; color: #718096; text-transform: uppercase; font-weight: 700; margin-bottom: .2rem; }
    .pi-item strong { font-size: .97rem; color: #2d3748; }

    /*  Two-column news layout  */
    .news-layout { display: grid; grid-template-columns: 3fr 2fr; gap: 1.5rem; }
    @media (max-width: 900px) { .news-layout { grid-template-columns: 1fr; } }

    /*  Post list  */
    .post-list { max-height: 520px; overflow-y: auto; }
    .post-item { padding: .85rem 0; border-bottom: 1px solid #e2e8f0; }
    .post-item:last-child { border-bottom: none; }
    .post-item h4 { color: #2d3748; font-size: .92rem; margin-bottom: .2rem; }
    .post-badge {
        font-size: .72rem; text-transform: uppercase; font-weight: 700;
        padding: .12rem .4rem; border-radius: 3px;
        background: #ebf8ff; color: #2b6cb0;
    }
    .post-date { font-size: .72rem; color: #718096; margin-left: .4rem; }
</style>

<div class="sp-container">

    <!--  Header  -->
    <div class="sp-header">
        <div class="sp-header-left">
            <div class="sp-avatar">
                <?php if (!empty($staff_profile['profile_photo']) && file_exists(__DIR__ . '/' . $staff_profile['profile_photo'])): ?>
                    <img src="<?php echo htmlspecialchars($staff_profile['profile_photo']); ?>" alt="Photo">
                <?php else: ?>
                    <?php echo strtoupper(mb_substr($staff_name, 0, 1)); ?>
                <?php endif; ?>
            </div>
            <div>
                <h2>
                    <?php echo htmlspecialchars($staff_name); ?>
                    <span class="role-badge"><?php echo htmlspecialchars($staff_profile['role'] ?? 'staff'); ?></span>
                </h2>
                <p><?php echo htmlspecialchars($staff_dept); ?> Department &mdash; Staff Portal</p>
            </div>
        </div>
        <div class="sp-header-actions">
            <a href="teacher_grades.php" class="btn-nav"> Grade Entry</a>
            <a href="staff_logout.php"   class="btn-logout">Log Out</a>
        </div>
    </div>

    <!--  Alert  -->
    <?php if ($status_msg): ?>
        <div class="sp-alert sp-alert-<?php echo $status_type; ?>">
            <?php echo htmlspecialchars($status_msg); ?>
        </div>
    <?php endif; ?>

    <!--  Tabs  -->
    <div class="sp-tabs">
        <button class="sp-tab-btn <?php echo $active_tab === 'tabNews' ? 'active' : ''; ?>" onclick="spTab(event,'tabNews')"> Publish News</button>
        <button class="sp-tab-btn <?php echo $active_tab === 'tabProfile' ? 'active' : ''; ?>" onclick="spTab(event,'tabProfile')"> My Profile</button>
    </div>

    <!--  TAB 1: Publish News  -->
    <div class="sp-tab-pane <?php echo $active_tab === 'tabNews' ? 'active' : ''; ?>" id="tabNews">
        <div class="news-layout">
            <!-- Form -->
            <div class="sp-card">
                <h3 class="sp-card-title">Publish News, Event, or Announcement</h3>
                <form method="POST" action="staff_portal.php">
                    <div class="fg">
                        <label>Post Category</label>
                        <select name="type" class="ctrl">
                            <option value="news">News Article</option>
                            <option value="announcement">Announcement / Notice Board</option>
                            <option value="event">Upcoming College Event</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Title (English) *</label>
                        <input type="text" name="title_en" class="ctrl" placeholder="e.g., Enrollment Deadline Extended" required>
                    </div>
                    <div class="fg">
                        <label>Body Content (English) *</label>
                        <textarea name="content_en" class="ctrl" placeholder="Detailed description in English..." required></textarea>
                    </div>
                    <div style="border-top:1px solid #e2e8f0; padding-top:1.25rem; margin-top:.5rem;">
                        <div class="fg">
                            <label>Title (Amharic) *</label>
                            <input type="text" name="title_am" class="ctrl" placeholder="እባክዎን ርዕስ በአማርኛ ያስገቡ" required>
                        </div>
                        <div class="fg">
                            <label>Body Content (Amharic) *</label>
                            <textarea name="content_am" class="ctrl" placeholder="ዝርዝር መግለጫ በአማርኛ..." required></textarea>
                        </div>
                    </div>
                    <button type="submit" name="submit_news" class="btn-primary"> Publish Entry</button>
                </form>
            </div>

            <!-- Published posts list -->
            <div class="sp-card" style="align-self:start;">
                <h3 class="sp-card-title">Published Posts (<?php echo count($posts); ?>)</h3>
                <div class="post-list">
                    <?php if (empty($posts)): ?>
                        <p style="color:#718096; font-style:italic; text-align:center; padding:2rem;">No posts published yet.</p>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="post-item">
                                <h4><?php echo htmlspecialchars($post['title_en']); ?></h4>
                                <span class="post-badge"><?php echo $post['type']; ?></span>
                                <span class="post-date"><?php echo date('d M Y', strtotime($post['date_posted'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!--  TAB 2: My Profile  -->
    <div class="sp-tab-pane <?php echo $active_tab === 'tabProfile' ? 'active' : ''; ?>" id="tabProfile">
        <div class="sp-card">
            <h3 class="sp-card-title"> My Profile</h3>
            <div class="profile-layout">

                <!-- Photo column -->
                <div class="profile-photo-box">
                    <?php if (!empty($staff_profile['profile_photo']) && file_exists(__DIR__ . '/' . $staff_profile['profile_photo'])): ?>
                        <img src="<?php echo htmlspecialchars($staff_profile['profile_photo']); ?>" alt="Profile Photo">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(mb_substr($staff_name, 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <p style="font-weight:700; color:#2d3748; margin:.25rem 0 .1rem;"><?php echo htmlspecialchars($staff_name); ?></p>
                    <p style="font-size:.82rem; color:#718096;"><?php echo htmlspecialchars($staff_dept); ?></p>
                    <p style="margin-top:.5rem;">
                        <span style="background:<?php echo ($staff_profile['role'] ?? 'staff') === 'admin' ? '#e9d8fd' : '#c6f6d5'; ?>;
                                     color:<?php echo ($staff_profile['role'] ?? 'staff') === 'admin' ? '#44337a' : '#22543d'; ?>;
                                     font-size:.78rem; font-weight:700; padding:.2rem .6rem; border-radius:20px;">
                            <?php echo strtoupper($staff_profile['role'] ?? 'STAFF'); ?>
                        </span>
                    </p>

                    <!-- Photo upload form -->
                    <form method="POST" action="staff_portal.php" enctype="multipart/form-data" style="margin-top:1.25rem;">
                        <label style="display:block; font-size:.8rem; font-weight:600; color:#2d3748; margin-bottom:.4rem;">
                            Upload / Change Photo
                        </label>
                        <input type="file" name="profile_photo" accept="image/*"
                               style="font-size:.82rem; width:100%; margin-bottom:.6rem;">
                        <button type="submit" name="upload_photo" class="btn-green" style="width:100%; padding:.55rem; font-size:.88rem;">
                             Save Photo
                        </button>
                    </form>
                    <p style="font-size:.73rem; color:#a0aec0; margin-top:.5rem;">Max 2 MB &bull; JPG / PNG / WEBP</p>
                </div>

                <!-- Info column -->
                <div>
                    <div class="profile-info-grid">
                        <div class="pi-item">
                            <span>Full Name</span>
                            <strong><?php echo htmlspecialchars($staff_profile['full_name'] ?? $staff_name); ?></strong>
                        </div>
                        <div class="pi-item">
                            <span>Email Address</span>
                            <strong><?php echo htmlspecialchars($staff_profile['email'] ?? '—'); ?></strong>
                        </div>
                        <div class="pi-item">
                            <span>Department</span>
                            <strong><?php echo htmlspecialchars($staff_profile['department'] ?? $staff_dept); ?></strong>
                        </div>
                        <div class="pi-item">
                            <span>Role</span>
                            <strong><?php echo ucfirst($staff_profile['role'] ?? 'staff'); ?></strong>
                        </div>
                        <div class="pi-item">
                            <span>Account Created</span>
                            <strong><?php echo isset($staff_profile['created_at']) ? date('d M Y', strtotime($staff_profile['created_at'])) : '—'; ?></strong>
                        </div>
                        <div class="pi-item">
                            <span>Staff ID</span>
                            <strong>#<?php echo htmlspecialchars($staff_id); ?></strong>
                        </div>
                    </div>

                    <div style="margin-top:1.5rem; padding:1rem 1.25rem; background:#f7fafc; border-radius:6px; border:1px solid #e2e8f0;">
                        <p style="font-size:.88rem; color:#4a5568; margin:0;">
                             To update your name, email, or department, contact the <strong>Admin</strong> at
                            <a href="mailto:admin@mgmbptc.edu.et">admin@mgmbptc.edu.et</a>.
                        </p>
                    </div>

                    <div class="sp-card" style="margin-top:1.5rem;">
                        <h3 class="sp-card-title">Change Your Password</h3>
                        <form method="POST" action="staff_portal.php">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['staff_password_csrf']); ?>">
                            <div class="fg">
                                <label for="currentPassword">Current Password</label>
                                <input type="password" id="currentPassword" name="current_password" class="ctrl" autocomplete="current-password" required>
                            </div>
                            <div class="fg">
                                <label for="newPassword">New Password</label>
                                <input type="password" id="newPassword" name="new_password" class="ctrl" minlength="8" autocomplete="new-password" required>
                                <small>Use at least 8 characters.</small>
                            </div>
                            <div class="fg">
                                <label for="confirmPassword">Confirm New Password</label>
                                <input type="password" id="confirmPassword" name="confirm_password" class="ctrl" minlength="8" autocomplete="new-password" required>
                            </div>
                            <button type="submit" name="change_staff_password" class="btn-green">Change Password</button>
                        </form>
                    </div>

                    <div style="margin-top:1.25rem; padding:1rem 1.25rem; background:#ebf8ff; border-radius:6px; border:1px solid #bee3f8;">
                        <h4 style="color:#2b6cb0; margin-bottom:.6rem; font-size:.95rem;"> Quick Actions</h4>
                        <div style="display:flex; gap:.75rem; flex-wrap:wrap;">
                            <a href="teacher_grades.php" class="btn-green" style="font-size:.88rem; padding:.5rem 1rem; text-decoration:none;">
                                 Enter Student Grades
                            </a>
                            <a href="staff_portal.php?tab=news" class="btn-primary" style="font-size:.88rem; padding:.5rem 1rem; text-decoration:none;"
                               onclick="spTab(event,'tabNews'); return false;">
                                 Publish News
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function spTab(e, id) {
    document.querySelectorAll('.sp-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.sp-tab-pane').forEach(p => p.classList.remove('active'));
    e.currentTarget.classList.add('active');
    document.getElementById(id).classList.add('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
