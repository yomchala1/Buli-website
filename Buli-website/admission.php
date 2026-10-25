<?php
$page_title = 'Admission & Registration';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$form_status = '';
$db_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect fields
    $name  = trim($_POST['student_name'] ?? '');
    $email = trim($_POST['student_email'] ?? '');
    $phone = trim($_POST['student_phone'] ?? '');
    $dept  = trim($_POST['student_dept'] ?? '');
    
    // Step 2 & 3 fields
    $prog_type = $_POST['program_type'] ?? '';
    $prog_mode = $_POST['program_mode'] ?? '';

    $transcript_path = null;
    $id_card_path = null;
    $photo_path = null;

    if (!empty($name) && !empty($email) && !empty($phone) && !empty($dept)) {
        if ($pdo) {
            try {
                // Handle file uploads
                $upload_dir = 'uploads/admissions/';
                if (!file_exists(__DIR__ . '/' . $upload_dir)) {
                    mkdir(__DIR__ . '/' . $upload_dir, 0777, true);
                }

                // File 1: Transcript
                if (isset($_FILES['file_transcript']) && $_FILES['file_transcript']['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['file_transcript']['name'], PATHINFO_EXTENSION);
                    $new_name = 'transcript_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['file_transcript']['tmp_name'], __DIR__ . '/' . $upload_dir . $new_name)) {
                        $transcript_path = $upload_dir . $new_name;
                    }
                }

                // File 2: ID Card
                if (isset($_FILES['file_id_card']) && $_FILES['file_id_card']['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['file_id_card']['name'], PATHINFO_EXTENSION);
                    $new_name = 'id_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['file_id_card']['tmp_name'], __DIR__ . '/' . $upload_dir . $new_name)) {
                        $id_card_path = $upload_dir . $new_name;
                    }
                }

                // File 3: Photo
                if (isset($_FILES['file_photo']) && $_FILES['file_photo']['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['file_photo']['name'], PATHINFO_EXTENSION);
                    $new_name = 'photo_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['file_photo']['tmp_name'], __DIR__ . '/' . $upload_dir . $new_name)) {
                        $photo_path = $upload_dir . $new_name;
                    }
                }

                $stmt = $pdo->prepare("INSERT INTO admissions (full_name, email, phone, department, program_type, program_mode, file_transcript, file_id_card, file_photo) 
                                       VALUES (:name, :email, :phone, :dept, :prog_type, :prog_mode, :transcript, :id_card, :photo)");
                $stmt->execute([
                    ':name'       => $name,
                    ':email'      => $email,
                    ':phone'      => $phone,
                    ':dept'       => $dept,
                    ':prog_type'  => $prog_type,
                    ':prog_mode'  => $prog_mode,
                    ':transcript' => $transcript_path,
                    ':id_card'    => $id_card_path,
                    ':photo'      => $photo_path
                ]);
                $form_status = 'success';
            } catch (\PDOException $e) {
                $form_status = 'error_db';
                $db_error = $e->getMessage();
            }
        } else {
            $form_status = 'error_db';
            $db_error = 'The database is unavailable, so your application could not be saved. Please try again later.';
        }
    } else {
        $form_status = 'error';
    }
}
?>

<style>
    .admission-header {
        background: #1e3f20;
        color: #fff;
        padding: 3rem 2rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        text-align: center;
    }
    .admission-header h2 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    .admission-header p {
        color: #cbd5e0;
        font-size: 1rem;
    }
    
    /* Wizard Steps styling */
    .wizard-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        background: #edf2f7;
        border-radius: 6px;
        padding: 0.5rem;
    }
    .wizard-step {
        flex: 1;
        text-align: center;
        padding: 0.75rem;
        font-weight: bold;
        font-size: 0.9rem;
        color: #718096;
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.2s;
    }
    .wizard-step.active {
        background: #1e3f20;
        color: #fff;
    }
    .wizard-step.completed {
        color: #2b6cb0;
    }
    
    .step-content {
        display: none;
    }
    .step-content.active {
        display: block;
    }
    
    .wizard-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }
    .btn-nav {
        padding: 0.6rem 1.5rem;
        font-weight: bold;
        font-size: 0.95rem;
        border-radius: 4px;
        cursor: pointer;
        border: none;
    }
    .btn-prev {
        background-color: #cbd5e0;
        color: #4a5568;
    }
    .btn-prev:hover {
        background-color: #a0aec0;
    }
    .btn-next {
        background-color: #2b6cb0;
        color: white;
    }
    .btn-next:hover {
        background-color: #2b6cb0;
    }
    
    .requirements-box {
        background: #f7fafc;
        border-left: 4px solid #ecc94b;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border-radius: 0 4px 4px 0;
    }
    .requirements-box h4 {
        color: #2d3748;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .requirements-box ul {
        padding-left: 1.25rem;
        font-size: 0.88rem;
        color: #4a5568;
        line-height: 1.6;
    }
    
    .file-input-wrapper {
        border: 2px dashed #cbd5e0;
        padding: 1.5rem;
        text-align: center;
        border-radius: 6px;
        background: #fafafb;
        margin-bottom: 1rem;
        cursor: pointer;
    }
    .file-input-wrapper:hover {
        border-color: #2b6cb0;
    }
    .file-input-wrapper input[type="file"] {
        display: none;
    }
    .file-label {
        font-size: 0.9rem;
        color: #4a5568;
        cursor: pointer;
        display: block;
    }
    .file-icon {
        font-size: 1.8rem;
        color: #a0aec0;
        margin-bottom: 0.5rem;
        display: block;
    }
    .file-selected-name {
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #2f855a;
        font-weight: bold;
    }
</style>

<div class="admission-header">
    <h2>Apply Online for Admission</h2>
    <p>Complete our multi-step registration inquiry to apply for current academic sessions.</p>
</div>

<div class="grid-2">
    <!-- Left Column: Guide -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3>Application Guide</h3>
            <p style="font-size: 0.95rem; line-height: 1.6; color: #4a5568; margin-bottom: 1rem;">
                Please follow the step-by-step portal wizard to complete your application. You must prepare scanning copies of the required documents prior to starting.
            </p>
            
            <div class="requirements-box">
                <h4>Required Digital Scans:</h4>
                <ul>
                    <li><strong>Academic Transcript / COC Copy:</strong> PDF or high-quality image of your latest grades or technical credentials.</li>
                    <li><strong>Official ID Copy:</strong> Scanned Kebele ID, Resident ID, or Passport bio data page.</li>
                    <li><strong>Passport Photo:</strong> Color portrait photo with white background (JPEG/PNG).</li>
                </ul>
            </div>
            
            <h4 style="color: #1e3f20; font-size: 1rem; margin-bottom: 0.5rem;">Available Categories &amp; Programs</h4>
            <p style="font-size: 0.85rem; color: #4a5568; line-height: 1.5; margin-bottom: 1rem;">
                <strong>TVET Levels:</strong> Level 1 up to Level 5 (Advanced Diploma) in engineering &amp; technology fields.<br>
                <strong>Undergraduate (BSc):</strong> Regular degree programs for academic matriculation qualifiers.<br>
                <strong>Postgraduate:</strong> Higher diploma specializations and advanced professional cohorts.<br>
                <strong>Modes of Study:</strong> Regular (Daytime study), Extension (Evening/Weekend slots), and Summer (In-service courses).
            </p>
        </div>
    </div>

    <!-- Right Column: Wizard Form -->
    <div>
        <section class="card" style="position: relative;">
            <h3>Application Wizard</h3>
            
            <!-- Wizard Navigation Indicators -->
            <div class="wizard-steps">
                <div class="wizard-step active" id="stepIndicator1" onclick="goToStep(1)">1. Program</div>
                <div class="wizard-step" id="stepIndicator2" onclick="goToStep(2)">2. Documents</div>
                <div class="wizard-step" id="stepIndicator3" onclick="goToStep(3)">3. Review</div>
            </div>

            <?php if ($form_status === 'success'): ?>
                <div class="alert alert-success">
                     <strong>Application Submitted!</strong> Your files and registration inquiry have been successfully received. The admissions office will evaluate your uploaded transcript and contact you soon.
                </div>
            <?php elseif ($form_status === 'success_simulated'): ?>
                <div class="alert alert-success" style="background-color: #e2e8f0; border-color: #cbd5e0; color: #4a5568;">
                    <strong>[Demo Mode]</strong> Application validated. Setup database connection to save files and details.
                </div>
            <?php elseif ($form_status === 'error_db'): ?>
                <div class="alert alert-error">
                    Database Error: <?php echo htmlspecialchars($db_error); ?>
                </div>
            <?php elseif ($form_status === 'error'): ?>
                <div class="alert alert-error">
                    Please ensure all required fields are filled.
                </div>
            <?php endif; ?>

            <form action="admission.php" method="POST" enctype="multipart/form-data" id="wizardForm">
                
                <!-- STEP 1: Program & Personal Details -->
                <div class="step-content active" id="stepContent1">
                    <h4 style="margin-bottom: 1rem; color: #2b6cb0;">Personal &amp; Program Selection</h4>
                    
                    <div class="form-group">
                        <label for="studentName">Full Name *</label>
                        <input type="text" name="student_name" id="studentName" class="form-control" placeholder="e.g. Abebe Kebede" required>
                    </div>

                    <div class="grid-2" style="margin-bottom: 0;">
                        <div class="form-group">
                            <label for="studentEmail">Email Address *</label>
                            <input type="email" name="student_email" id="studentEmail" class="form-control" placeholder="e.g. abebe@example.com" required>
                        </div>
                        <div class="form-group">
                            <label for="studentPhone">Phone Number *</label>
                            <input type="text" name="student_phone" id="studentPhone" class="form-control" placeholder="e.g. +251 911 000000" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="programType">Program Category *</label>
                        <select name="program_type" id="programType" class="form-control" onchange="updateDepartments()" required>
                            <option value="">-- Choose Category --</option>
                            <option value="TVET (Level I - V)">TVET Diploma (Level I - V)</option>
                            <option value="Undergraduate (BSc Degree)">Undergraduate (BSc Degree)</option>
                            <option value="Postgraduate Program">Postgraduate / Specialization</option>
                            <option value="Summer / Short-Term Courses">Summer / Short-Term Certification</option>
                        </select>
                    </div>

                    <div class="grid-2" style="margin-bottom: 0;">
                        <div class="form-group">
                            <label for="studentDept">Course / Department *</label>
                            <select name="student_dept" id="studentDept" class="form-control" required>
                                <option value="">-- Select Department --</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Automotive Technology">Automotive Technology</option>
                                <option value="Electrical &amp; Electronics">Electrical &amp; Electronics</option>
                                <option value="Manufacturing &amp; Mechanical">Manufacturing &amp; Mechanical</option>
                                <option value="Construction &amp; Civil Technology">Construction &amp; Civil Technology</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="programMode">Mode of Study *</label>
                            <select name="program_mode" id="programMode" class="form-control" required>
                                <option value="Regular">Regular (Daytime)</option>
                                <option value="Extension">Extension (Evening/Weekend)</option>
                                <option value="Summer">Summer (In-Service)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Document Uploads -->
                <div class="step-content" id="stepContent2">
                    <h4 style="margin-bottom: 1rem; color: #2b6cb0;">Required Document Uploads</h4>
                    <p style="font-size: 0.85rem; color: #718096; margin-bottom: 1.5rem;">
                        Supported files: PDF, PNG, JPG, JPEG. Max file size: 5MB per document.
                    </p>

                    <!-- Document 1: Transcript -->
                    <div class="form-group">
                        <label>Academic Transcript / COC Certificate *</label>
                        <div class="file-input-wrapper" onclick="document.getElementById('file_transcript').click()">
                            <span class="file-icon"></span>
                            <span class="file-label" id="lbl_transcript">Click to select files or upload scan</span>
                            <input type="file" name="file_transcript" id="file_transcript" accept=".pdf,.png,.jpg,.jpeg" onchange="fileSelected('file_transcript', 'lbl_transcript')" required>
                        </div>
                    </div>

                    <!-- Document 2: ID -->
                    <div class="form-group">
                        <label>Official Identification Card (Kebele ID / Passport) *</label>
                        <div class="file-input-wrapper" onclick="document.getElementById('file_id_card').click()">
                            <span class="file-icon"></span>
                            <span class="file-label" id="lbl_id_card">Click to select files or upload ID scan</span>
                            <input type="file" name="file_id_card" id="file_id_card" accept=".pdf,.png,.jpg,.jpeg" onchange="fileSelected('file_id_card', 'lbl_id_card')" required>
                        </div>
                    </div>

                    <!-- Document 3: Photo -->
                    <div class="form-group">
                        <label>Recent Passport Size Photo *</label>
                        <div class="file-input-wrapper" onclick="document.getElementById('file_photo').click()">
                            <span class="file-icon"></span>
                            <span class="file-label" id="lbl_photo">Click to select portrait image</span>
                            <input type="file" name="file_photo" id="file_photo" accept="image/*" onchange="fileSelected('file_photo', 'lbl_photo')" required>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Review and Declaration -->
                <div class="step-content" id="stepContent3">
                    <h4 style="margin-bottom: 1rem; color: #2b6cb0;">Review Your Details &amp; Declare</h4>
                    
                    <div style="background: #ebf8ff; border: 1px solid #bee3f8; padding: 1.25rem; border-radius: 6px; font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.6;">
                        <strong>Name:</strong> <span id="rev_name">—</span><br>
                        <strong>Email:</strong> <span id="rev_email">—</span><br>
                        <strong>Phone:</strong> <span id="rev_phone">—</span><br>
                        <strong>Category:</strong> <span id="rev_type">—</span><br>
                        <strong>Department:</strong> <span id="rev_dept">—</span><br>
                        <strong>Mode:</strong> <span id="rev_mode">—</span>
                    </div>

                    <div style="background: #fffaf0; border: 1px solid #feebc8; padding: 1rem; border-radius: 6px; font-size: 0.85rem; color: #7b341e;">
                        <label style="display: flex; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="declaration_accept" required style="margin-top: 3px;">
                            <span>I hereby declare that all the information and uploaded scans provided in this registration form are true and accurate to the best of my knowledge.</span>
                        </label>
                    </div>
                </div>

                <!-- Wizard control buttons -->
                <div class="wizard-buttons">
                    <button type="button" class="btn-nav btn-prev" id="btnPrev" onclick="moveStep(-1)">Back</button>
                    <button type="button" class="btn-nav btn-next" id="btnNext" onclick="moveStep(1)">Next Step</button>
                </div>
            </form>
        </section>
    </div>
</div>

<script>
    let currentStep = 1;
    const totalSteps = 3;

    function goToStep(step) {
        if (step > currentStep) {
            // Validate fields on the current step first
            if (!validateStep(currentStep)) return;
        }
        
        currentStep = step;
        updateWizardUI();
    }

    function moveStep(direction) {
        if (direction === 1) {
            if (!validateStep(currentStep)) return;
        }
        
        currentStep += direction;
        if (currentStep < 1) currentStep = 1;
        if (currentStep > totalSteps) currentStep = totalSteps;
        
        updateWizardUI();
    }

    function validateStep(step) {
        const activeContainer = document.getElementById('stepContent' + step);
        const inputs = activeContainer.querySelectorAll('input[required], select[required]');
        
        for (let i = 0; i < inputs.length; i++) {
            if (!inputs[i].value) {
                alert('Please fill out all required fields on this page before proceeding.');
                inputs[i].focus();
                return false;
            }
        }
        return true;
    }

    function fileSelected(inputId, labelId) {
        const input = document.getElementById(inputId);
        const label = document.getElementById(labelId);
        if (input.files && input.files.length > 0) {
            label.textContent = 'Selected: ' + input.files[0].name + ' (' +
                (input.files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
        }
    }

    function updateWizardUI() {
        // Update content panes
        for (let i = 1; i <= totalSteps; i++) {
            const pane = document.getElementById('stepContent' + i);
            const indicator = document.getElementById('stepIndicator' + i);
            
            if (i === currentStep) {
                pane.classList.add('active');
                indicator.classList.add('active');
            } else {
                pane.classList.remove('active');
                indicator.classList.remove('active');
            }
        }

        // Populate step 3 review information
        if (currentStep === 3) {
            document.getElementById('rev_name').innerText = document.getElementById('studentName').value || '—';
            document.getElementById('rev_email').innerText = document.getElementById('studentEmail').value || '—';
            document.getElementById('rev_phone').innerText = document.getElementById('studentPhone').value || '—';
            
            const selType = document.getElementById('programType');
            document.getElementById('rev_type').innerText = selType.options[selType.selectedIndex].text || '—';
            
            const selDept = document.getElementById('studentDept');
            document.getElementById('rev_dept').innerText = selDept.options[selDept.selectedIndex].text || '—';
            
            const selMode = document.getElementById('programMode');
            document.getElementById('rev_mode').innerText = selMode.options[selMode.selectedIndex].text || '—';
        }

        // Adjust buttons
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');

        if (currentStep === 1) {
            btnPrev.style.visibility = 'hidden';
        } else {
            btnPrev.style.visibility = 'visible';
        }

        if (currentStep === totalSteps) {
            btnNext.innerText = 'Submit Application';
            btnNext.type = 'submit';
            // Click handler is bypassed when type = submit
            btnNext.onclick = null;
        } else {
            btnNext.innerText = 'Next Step';
            btnNext.type = 'button';
            btnNext.onclick = function() { moveStep(1); };
        }
    }

    // Dynamic departments setup depending on program type
    function updateDepartments() {
        const typeSelect = document.getElementById('programType').value;
        const deptSelect = document.getElementById('studentDept');
        
        // Save current selection
        const prevVal = deptSelect.value;
        deptSelect.innerHTML = '';
        
        let options = [];
        if (typeSelect.includes('TVET')) {
            options = [
                { val: 'Information Technology', text: 'Information Technology (Level 1-5)' },
                { val: 'Automotive Technology', text: 'Automotive Technology (Level 1-5)' },
                { val: 'Electrical & Electronics', text: 'Electrical & Electronics' },
                { val: 'Manufacturing & Mechanical', text: 'Manufacturing & Mechanical' },
                { val: 'Construction & Civil Technology', text: 'Construction & Civil Technology' }
            ];
        } else if (typeSelect.includes('Undergraduate')) {
            options = [
                { val: 'BSc Computer Science', text: 'BSc in Computer Science' },
                { val: 'BSc Automotive Engineering', text: 'BSc in Automotive Engineering' },
                { val: 'BSc Electrical Engineering', text: 'BSc in Electrical & Communication Engineering' },
                { val: 'BSc Mechanical Engineering', text: 'BSc in Mechanical & Manufacturing Engineering' }
            ];
        } else if (typeSelect.includes('Postgraduate')) {
            options = [
                { val: 'MSc Network Security', text: 'MSc in Network Security & IT Systems' },
                { val: 'MSc Advanced Manufacturing', text: 'MSc in Advanced Manufacturing Systems' },
                { val: 'PG Diploma Technical Leadership', text: 'PG Diploma in Technical Leadership' }
            ];
        } else {
            options = [
                { val: 'Short-Course Python & Web', text: 'Short-Course: Python & Web Apps' },
                { val: 'Short-Course Machine Diagnostics', text: 'Short-Course: Car Engine Diagnostics' },
                { val: 'Short-Course Solar Installation', text: 'Short-Course: Solar Grid Setup' }
            ];
        }
        
        // Add default option
        const defOpt = document.createElement('option');
        defOpt.value = '';
        defOpt.text = '-- Select Specific Program --';
        deptSelect.appendChild(defOpt);
        
        options.forEach(opt => {
            const el = document.createElement('option');
            el.value = opt.val;
            el.text = opt.text;
            deptSelect.appendChild(el);
        });
        
        // Try restoring previous value if matches
        deptSelect.value = prevVal;
    }

    // Set initial buttons
    updateWizardUI();
</script>


<!-- ===================================================
     TRAINING FEES TABLE (AATPTC Benchmark)
     =================================================== -->
<section class="card" style="margin-bottom:2.5rem;margin-top:2rem;">
    <h3> Training Fees Overview</h3>
    <p style="margin-bottom:1.25rem;font-size:.9rem;color:#4a5568;">Fees are set per academic semester and vary by program level and mode of study. All fees are indicative and subject to approval by the Oromia TVET Bureau.</p>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Program Category</th>
                    <th>NTQF Level</th>
                    <th>Mode</th>
                    <th>Semester Fee (ETB)</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $fees = [
                    ['TVET (Technical Dept.)','Level I – II','Regular','Free (Gov. Sponsored)','Zero tuition for qualified trainees'],
                    ['TVET (Technical Dept.)','Level III – V','Regular','500 – 800','Materials &amp; assessment fee only'],
                    ['TVET (Technical Dept.)','Level III – V','Extension','1,200 – 1,500','Evening/Weekend program'],
                    ['TVET (Business Dept.)','Level II – V','Regular','Free – 600','Varies by specialization'],
                    ['Short-Term Courses','N/A','Flexible','800 – 2,500','Per course; materials included'],
                    ['COC Assessment','All Levels','Exam Only','150 – 300','Competency verification fee'],
                    ['Summer Program','All Levels','Summer','600 – 1,000','Per session, in-service only'],
                ];
                foreach ($fees as $f): ?>
                <tr>
                    <td><strong><?php echo $f[0]; ?></strong></td>
                    <td><?php echo $f[1]; ?></td>
                    <td><?php echo $f[2]; ?></td>
                    <td style="font-weight:700;color:var(--primary-color);"><?php echo $f[3]; ?></td>
                    <td style="font-size:.82rem;color:#718096;"><?php echo $f[4]; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p style="font-size:.8rem;color:#718096;margin-top:.75rem;"><em>* Fee waivers are available for marginalized communities. Contact the Registrar's Office for details.</em></p>
</section>

<!-- ===================================================
     ACADEMIC CALENDAR (AATPTC Benchmark)
     =================================================== -->
<section class="card" style="margin-bottom:2.5rem;">
    <h3> Academic Calendar 2026/2027 (2018/2019 E.C.)</h3>
    <p style="margin-bottom:1.25rem;font-size:.9rem;color:#4a5568;">Key dates and milestones for the upcoming academic year across all programs.</p>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Applies To</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $calendar = [
                    ['New Student Registration (Regular)', 'Sep 01, 2026', 'Sep 15, 2026', 'All Regular Programs'],
                    ['Semester I Classes Begin', 'Sep 22, 2026', '—', 'All Programs'],
                    ['Extension Program Registration', 'Sep 15, 2026', 'Sep 25, 2026', 'Extension Mode Trainees'],
                    ['Mid-Semester Assessment (Sem. I)', 'Nov 10, 2026', 'Nov 20, 2026', 'All Programs'],
                    ['Semester I Final Exams', 'Jan 05, 2027', 'Jan 22, 2027', 'All Programs'],
                    ['COC Practical Assessments', 'Jan 25, 2027', 'Feb 10, 2027', 'Level III, IV, V'],
                    ['Semester I Break', 'Jan 23, 2027', 'Feb 14, 2027', 'Regular Students'],
                    ['Semester II Registration', 'Feb 15, 2027', 'Feb 25, 2027', 'Continuing Students'],
                    ['Semester II Classes Begin', 'Mar 01, 2027', '—', 'All Programs'],
                    ['Summer / Short-Course Intake', 'Jun 15, 2027', 'Jun 25, 2027', 'Short-Term &amp; In-Service'],
                    ['Annual Graduation Ceremony', 'Jul 2027', '—', 'Level IV &amp; V Graduates'],
                ];
                foreach ($calendar as $c): ?>
                <tr>
                    <td><strong><?php echo $c[0]; ?></strong></td>
                    <td style="color:var(--primary-color);font-weight:600;"><?php echo $c[1]; ?></td>
                    <td><?php echo $c[2]; ?></td>
                    <td style="font-size:.85rem;color:#4a5568;"><?php echo $c[3]; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p style="font-size:.8rem;color:#718096;margin-top:.75rem;"><em>* Calendar dates are subject to official gazette updates by the Oromia Education Bureau.</em></p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
