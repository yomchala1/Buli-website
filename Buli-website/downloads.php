<?php
$page_title = 'Download Center';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$download_items = [];

if ($pdo) {
    try {
        $download_items = $pdo->query("SELECT * FROM downloads ORDER BY category, title")->fetchAll();
    } catch (\PDOException $e) {
        // Fall back to offline array
    }
}

// Fallback values if DB table is empty or disconnected
if (empty($download_items)) {
    $download_items = [
        ['title' => 'Student Application Form', 'description' => 'Official form for new student admissions.', 'category' => 'Application Forms', 'file_name' => 'application_form.pdf'],
        ['title' => 'TVET Level Registration Guide', 'description' => 'Step-by-step guide for level registration.', 'category' => 'Academic Guides', 'file_name' => 'registration_guide.pdf'],
        ['title' => 'College Academic Calendar 2026/27', 'description' => 'Official academic calendar for the new year.', 'category' => 'Academic Guides', 'file_name' => 'academic_calendar_2026.pdf'],
        ['title' => 'Student Code of Conduct', 'description' => 'Rules, regulations and student discipline manual.', 'category' => 'Regulations', 'file_name' => 'student_conduct.pdf'],
        ['title' => 'Research Publication Vol. 3', 'description' => 'MGMBPTC annual research and innovation publication.', 'category' => 'Publications', 'file_name' => 'research_vol3.pdf'],
        ['title' => 'Short-Course Registration Form', 'description' => 'Form for enrolling in evening short-term programs.', 'category' => 'Application Forms', 'file_name' => 'short_course_form.pdf']
    ];
}

// Group items by category
$categories = [];
foreach ($download_items as $item) {
    $categories[$item['category']][] = $item;
}
?>

<style>
    .downloads-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    .downloads-intro {
        text-align: center;
        margin-bottom: 3rem;
    }
    .downloads-intro p {
        color: #718096;
        margin-top: 0.5rem;
        font-size: 1.1rem;
    }
    .category-section {
        margin-bottom: 2.5rem;
    }
    .category-title {
        color: #1e3f20;
        font-size: 1.3rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #d4af37;
        padding-bottom: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .download-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }
    .download-card {
        background: #fff;
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        padding: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .download-info h4 {
        color: #2d3748;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    .download-info p {
        color: #718096;
        font-size: 0.85rem;
        line-height: 1.4;
    }
    .btn-dl {
        background-color: #1e3f20;
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-weight: bold;
        text-decoration: none;
        font-size: 0.85rem;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-dl:hover {
        background-color: #2b6cb0;
    }
    @media (max-width: 768px) {
        .download-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="downloads-container">
    <div class="downloads-intro">
        <h2>Download Center</h2>
        <p>Access official forms, scholastic guides, guidelines, and publications published by the registrar & departments.</p>
    </div>

    <?php foreach ($categories as $catName => $items): ?>
        <div class="category-section">
            <h3 class="category-title">
                <span></span> <?php echo htmlspecialchars($catName); ?>
            </h3>
            
            <div class="download-grid">
                <?php foreach ($items as $item): ?>
                    <div class="download-card">
                        <div class="download-info">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <span style="font-size: 0.75rem; color: #a0aec0;">Filename: <?php echo htmlspecialchars($item['file_name']); ?></span>
                        </div>
                        <div>
                            <!-- Simple Javascript alert since files are simulated -->
                            <a href="#" onclick="alert('This is a demo. In a live production environment, this link will trigger a download for: <?php echo htmlspecialchars($item['file_name']); ?>'); return false;" class="btn-dl">
                                 Download
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
