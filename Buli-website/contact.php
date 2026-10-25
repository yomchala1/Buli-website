<?php
$page_title = 'Contact Us';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

// Simple simulated feedback logic
$feedback_status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_name = isset($_POST['contact_name']) ? htmlspecialchars($_POST['contact_name']) : '';
    $sender_email = isset($_POST['contact_email']) ? htmlspecialchars($_POST['contact_email']) : '';
    $sender_subject = isset($_POST['contact_subject']) ? htmlspecialchars($_POST['contact_subject']) : '';
    $sender_msg = isset($_POST['contact_message']) ? htmlspecialchars($_POST['contact_message']) : '';
    
    if (!empty($sender_name) && !empty($sender_email) && !empty($sender_msg)) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
                $stmt->execute([
                    ':name' => $sender_name,
                    ':email' => $sender_email,
                    ':subject' => $sender_subject,
                    ':message' => $sender_msg
                ]);
                $feedback_status = 'success';
            } catch (\PDOException $e) {
                $feedback_status = 'error_db';
                $db_error = $e->getMessage();
            }
        } else {
            // Fallback simulated success
            $feedback_status = 'success_simulated';
        }
    } else {
        $feedback_status = 'error';
    }
}
?>

<div class="page-header">
    <h2><?php echo __('contact_title'); ?></h2>
</div>

<div class="grid-2">
    <!-- Contact Info & Map Mock -->
    <div class="card">
        <h3>Campus Contact Details</h3>
        <p style="margin-bottom: 1rem;">
            Feel free to visit our campus or reach out via phone/email during our regular office hours (Monday to Friday, 8:00 AM - 5:00 PM).
        </p>
        
        <p style="margin-bottom: 0.5rem;"><strong><?php echo __('address'); ?>:</strong> Holteta,Oromia Region, Ethiopia</p>
        <p style="margin-bottom: 0.5rem;"><strong><?php echo __('phone'); ?>:</strong> +251 11 123456</p>
        <p style="margin-bottom: 1rem;"><strong><?php echo __('email'); ?>:</strong> info@mgmbptc.edu.et</p>

        <h4 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">Location Map Placeholder</h4>
        <div style="background-color: #edf2f7; border: 1px solid var(--border-color); height: 220px; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #718096; font-style: italic;">
            [ Google Map Embed / Location Map Placeholder ]
        </div>
    </div>

    <!-- Contact Form -->
    <div class="card">
        <h3><?php echo __('contact_title'); ?> Form</h3>
        <p style="margin-bottom: 1.25rem; font-size: 0.95rem;">Send us your comments, suggestions, or academic inquiries.</p>

        <?php if ($feedback_status === 'success'): ?>
            <div class="alert alert-success">
                Thank you, <strong><?php echo $sender_name; ?></strong>! Your message has been saved to the database. We will get back to you soon.
            </div>
        <?php elseif ($feedback_status === 'success_simulated'): ?>
            <div class="alert alert-success" style="background-color: #e2e8f0; border-color: #cbd5e0; color: #4a5568;">
                <strong>[Offline Demo Mode]</strong> Thank you, <strong><?php echo $sender_name; ?></strong>! Your feedback was received. (Connect to database to persist this).
            </div>
        <?php elseif ($feedback_status === 'error_db'): ?>
            <div class="alert alert-error">
                Database Error: Failed to save message. Details: <em><?php echo $db_error; ?></em>
            </div>
        <?php elseif ($feedback_status === 'error'): ?>
            <div class="alert alert-error">
                Failed to send message. Please fill out all required fields.
            </div>
        <?php endif; ?>

        <form action="contact.php" method="POST" id="contactForm">
            <div class="form-group">
                <label for="contactName"><?php echo __('name_label'); ?> *</label>
                <input type="text" name="contact_name" id="contactName" class="form-control" placeholder="Abebe Kebede">
            </div>

            <div class="form-group">
                <label for="contactEmail"><?php echo __('email_label'); ?> *</label>
                <input type="email" name="contact_email" id="contactEmail" class="form-control" placeholder="abebe@example.com">
            </div>

            <div class="form-group">
                <label for="contactSubject"><?php echo __('subject_label'); ?></label>
                <input type="text" name="contact_subject" id="contactSubject" class="form-control" placeholder="Inquiry about Registration">
            </div>

            <div class="form-group">
                <label for="contactMessage"><?php echo __('message_label'); ?> *</label>
                <textarea name="contact_message" id="contactMessage" class="form-control" placeholder="Write your message here..."></textarea>
            </div>

            <button type="submit" class="btn"><?php echo __('btn_send'); ?></button>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
