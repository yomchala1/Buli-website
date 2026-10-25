<?php
$page_title = 'Media Gallery';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$gallery_items = [];

if ($pdo) {
    try {
        $gallery_items = $pdo->query("SELECT * FROM gallery ORDER BY date_taken DESC")->fetchAll();
    } catch (\PDOException $e) {
        // Silently fall back to offline arrays
    }
}

// Fallback values
if (empty($gallery_items)) {
    $gallery_items = [
        ['title' => 'Annual Graduation Ceremony 2026', 'description' => 'Graduates celebrating at the main pavilion.', 'type' => 'photo', 'date_taken' => '2026-07-01'],
        ['title' => 'IT Workshop Lab Session', 'description' => 'Students during a hands-on ICT practical session.', 'type' => 'photo', 'date_taken' => '2026-06-15'],
        ['title' => 'Innovation Exhibition 2026', 'description' => 'Student projects showcased at the national TVET expo.', 'type' => 'photo', 'date_taken' => '2026-05-20'],
        ['title' => 'Automotive Workshop', 'description' => 'Automotive trainees working on engine diagnostics.', 'type' => 'photo', 'date_taken' => '2026-04-10'],
        ['title' => 'College Campus Tour', 'description' => 'Video overview of the college facilities and campus.', 'type' => 'video', 'date_taken' => '2026-03-05'],
        ['title' => 'Solar Energy Installation Training', 'description' => 'Students installing a solar panel unit on campus.', 'type' => 'photo', 'date_taken' => '2026-02-18']
    ];
}
?>

<style>
    .gallery-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    .gallery-intro {
        text-align: center;
        margin-bottom: 3rem;
    }
    .gallery-intro p {
        color: #718096;
        margin-top: 0.5rem;
        font-size: 1.1rem;
    }
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
    }
    .gallery-card {
        background: #fff;
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .gallery-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    .gallery-media-placeholder {
        height: 200px;
        background: #1e3f20;
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        text-align: center;
        padding: 1rem;
    }
    .gallery-media-placeholder.video-type {
        background: #2b6cb0;
    }
    .gallery-icon {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        opacity: 0.85;
    }
    .gallery-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        font-size: 0.75rem;
        font-weight: bold;
        padding: 0.2rem 0.5rem;
        border-radius: 3px;
        text-transform: uppercase;
    }
    .gallery-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .gallery-body h3 {
        color: #2d3748;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }
    .gallery-body p {
        color: #4a5568;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
        flex-grow: 1;
    }
    .gallery-footer {
        font-size: 0.8rem;
        color: #718096;
        border-top: 1px solid #edf2f7;
        padding-top: 0.75rem;
        display: flex;
        justify-content: space-between;
    }
</style>

<div class="gallery-container">
    <div class="gallery-intro">
        <h2>Photo & Video Gallery</h2>
        <p>Take a virtual tour and explore student life, workshops, and achievements at MGMBPTC.</p>
    </div>

    <div class="gallery-grid">
        <?php foreach ($gallery_items as $item): ?>
            <div class="gallery-card">
                <?php 
                    $is_video = ($item['type'] === 'video');
                    $bg_class = $is_video ? 'video-type' : '';
                    $icon = $is_video ? '' : '';
                ?>
                <div class="gallery-media-placeholder <?php echo $bg_class; ?>">
                    <div class="gallery-icon"><?php echo $icon; ?></div>
                    <div style="font-size: 0.9rem; font-weight: bold;"><?php echo htmlspecialchars($item['title']); ?></div>
                    <span class="gallery-badge"><?php echo htmlspecialchars($item['type']); ?></span>
                </div>
                
                <div class="gallery-body">
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description'] ?: 'No description provided.'); ?></p>
                    
                    <div class="gallery-footer">
                        <span>Event Date:</span>
                        <strong><?php echo $item['date_taken'] ? date('d M Y', strtotime($item['date_taken'])) : 'N/A'; ?></strong>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
