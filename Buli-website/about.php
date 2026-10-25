<?php
$page_title = 'About Us';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h2><?php echo __('about_title'); ?></h2>
</div>

<!-- Core Values Section -->
<section class="card" style="margin-bottom: 2rem;">
    <h3><?php echo __('core_values'); ?></h3>
    <div class="grid-3" style="margin-top: 1rem; margin-bottom: 0;">
        <div class="card" style="background-color: var(--bg-light); text-align: center;">
            <h4 style="color: var(--primary-color);">Excellence</h4>
            <p style="font-size: 0.9rem;">We strive for the highest quality standards in teaching, learning, and technology transfer.</p>
        </div>
        <div class="card" style="background-color: var(--bg-light); text-align: center;">
            <h4 style="color: var(--primary-color);">Innovation</h4>
            <p style="font-size: 0.9rem;">We encourage creativity and modern technical problem-solving to address local and national needs.</p>
        </div>
        <div class="card" style="background-color: var(--bg-light); text-align: center;">
            <h4 style="color: var(--primary-color);">Integrity</h4>
            <p style="font-size: 0.9rem;">We uphold high ethical standards, transparency, and accountability in all our academic operations.</p>
        </div>
    </div>
</section>

<!-- Organizational Leadership -->
<section class="card" style="margin-bottom: 2rem;">
    <h3>College Governance & Leadership</h3>
    <p style="margin-bottom: 1rem;">
        The administration of MGMBPTC is structured to ensure efficient decision-making, quality delivery of academic programs, and responsive student support.
    </p>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Designation</th>
                    <th>Officer Name</th>
                    <th>Email Contact</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Dean</strong></td>
                    <td>Abebe Kebede (PhD)</td>
                    <td>dean@mgmbptc.edu.et</td>
                </tr>
                <tr>
                    <td><strong>Vice Dean for Academics</strong></td>
                    <td>Tsegaye Mengistu</td>
                    <td>academic.vicedean@mgmbptc.edu.et</td>
                </tr>
                <tr>
                    <td><strong>Vice Dean for Administration</strong></td>
                    <td>Aster Almaz</td>
                    <td>admin.vicedean@mgmbptc.edu.et</td>
                </tr>
                <tr>
                    <td><strong>Industry Liaison Director</strong></td>
                    <td>Yohannes Bekele</td>
                    <td>industry.liaison@mgmbptc.edu.et</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
