<?php
$page_title = "Health Services";
require_once __DIR__ . '/includes/header.php';
?>

<style>
.container{
    max-width:1200px;
    margin:auto;
    padding:20px;
}

.page-header{
    text-align:center;
    padding:50px 20px;
}

.page-header h2{
    color:#0c2340;
    font-size:40px;
    margin-bottom:15px;
}

.page-header p{
    color:#555;
    font-size:18px;
    line-height:1.8;
}

.dashboard{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:30px;
}

.stat-box{
    background:#0c2340;
    color:white;
    text-align:center;
    padding:25px;
    border-radius:10px;
}

.stat-box h3{
    color:#d4af37;
    font-size:30px;
    margin-bottom:10px;
}

.section{
    background:#fff;
    padding:30px;
    margin-bottom:25px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.section h3{
    color:#0c2340;
    border-left:5px solid #d4af37;
    padding-left:12px;
    margin-bottom:20px;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:20px;
}

.card{
    background:#f8f9fa;
    padding:20px;
    border-radius:10px;
    border-top:4px solid #0c2340;
}

.card h4{
    color:#0c2340;
    margin-bottom:10px;
}

.card p{
    color:#555;
    line-height:1.6;
}

ul{
    line-height:2;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th{
    background:#0c2340;
    color:white;
    padding:12px;
}

table td{
    border:1px solid #ddd;
    padding:12px;
}
</style>

<div class="container">

<div class="page-header">
    <h2>Health Services</h2>

    <p>
        The Health Services Unit at Major General Mulugeta Buli Polytechnic College
        is dedicated to supporting the physical, mental, and emotional well-being
        of students. The college clinic provides essential healthcare services,
        health education, emergency assistance, and wellness programs to ensure
        students remain healthy and successful in their academic journey.
    </p>
</div>

<div class="dashboard">

    <div class="stat-box">
        <h3></h3>
        <p>Campus Clinic</p>
    </div>

    <div class="stat-box">
        <h3></h3>
        <p>First Aid Services</p>
    </div>

    <div class="stat-box">
        <h3></h3>
        <p>Medical Consultation</p>
    </div>

    <div class="stat-box">
        <h3></h3>
        <p>Emergency Assistance</p>
    </div>

</div>

<div class="section">

<h3>Overview</h3>

<p>
The Campus Health Center provides basic healthcare services for students,
staff, and visitors. The center promotes healthy living through preventive
care, health awareness programs, medical consultations, and emergency support.
Students can visit the clinic for health advice, treatment of minor illnesses,
and referrals when specialized care is required.
</p>

</div>

<div class="section">

<h3>Health Services Available</h3>

<div class="grid">

<div class="card">
<h4> Campus Clinic Services</h4>
<p>
General health assessment, treatment of minor illnesses, health monitoring,
and medical advice provided by qualified healthcare professionals.
</p>
</div>

<div class="card">
<h4> First Aid Services</h4>
<p>
Immediate first aid support for injuries, accidents, and sudden health
conditions occurring within the college campus.
</p>
</div>

<div class="card">
<h4> Medical Consultations</h4>
<p>
Professional medical consultation services for common health concerns,
guidance, and referrals to hospitals when necessary.
</p>
</div>

<div class="card">
<h4> Emergency Assistance</h4>
<p>
Emergency response support and coordination with nearby healthcare facilities
for urgent medical situations.
</p>
</div>

</div>

</div>

<div class="section">

<h3>Health Awareness Programs</h3>

<ul>
<li>Personal Hygiene Education</li>
<li>Nutrition and Healthy Living Awareness</li>
<li>Disease Prevention Campaigns</li>
<li>Mental Health Awareness</li>
<li>Substance Abuse Prevention Education</li>
<li>Sexual and Reproductive Health Awareness</li>
<li>Community Health Programs</li>
</ul>

</div>

<div class="section">

<h3>Clinic Facilities</h3>

<ul>
<li>Patient Examination Room</li>
<li>First Aid Equipment</li>
<li>Basic Medical Supplies</li>
<li>Health Information Resources</li>
<li>Emergency Response Equipment</li>
<li>Waiting Area for Students</li>
</ul>

</div>

<div class="section">

<h3>Student Health Benefits</h3>

<ul>
<li>Access to Basic Healthcare Services</li>
<li>Health Education and Awareness</li>
<li>Emergency Medical Support</li>
<li>Preventive Healthcare Programs</li>
<li>Improved Student Well-being</li>
<li>Support for Academic Success</li>
</ul>

</div>

<div class="section">

<h3>Clinic Working Hours</h3>

<table>

<tr>
<th>Day</th>
<th>Working Hours</th>
</tr>

<tr>
<td>Monday - Friday</td>
<td>8:00 AM - 5:00 PM</td>
</tr>

<tr>
<td>Saturday</td>
<td>8:00 AM - 12:00 PM</td>
</tr>

<tr>
<td>Sunday</td>
<td>Closed</td>
</tr>

</table>

</div>

<div class="section">

<h3>Contact Information</h3>

<p><strong>Office:</strong> Campus Health Services Unit</p>

<p><strong>College:</strong> Major General Mulugeta Buli Polytechnic College</p>

<p><strong>Location:</strong> Student Affairs Building</p>

<p><strong>Email:</strong> health@mgmbptc.edu.et</p>

<p><strong>Phone:</strong> +251 XXX XXX XXX</p>

</div>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>