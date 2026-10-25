<?php
$page_title = "Student Housing & Accommodation";
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
    <h2>Student Housing & Accommodation</h2>

    <p>
        The Student Housing & Accommodation Service at Major General Mulugeta
        Buli Polytechnic College provides safe, comfortable, and supportive
        living facilities for students. The service promotes a secure learning
        environment, student welfare, and successful academic life through
        quality accommodation services and residential support.
    </p>
</div>

<div class="dashboard">

    <div class="stat-box">
        <h3></h3>
        <p>Dormitory Services</p>
    </div>

    <div class="stat-box">
        <h3></h3>
        <p>Student Accommodation</p>
    </div>

    <div class="stat-box">
        <h3></h3>
        <p>Safe Residence</p>
    </div>

    <div class="stat-box">
        <h3></h3>
        <p>Housing Support</p>
    </div>

</div>

<div class="section">

<h3>Overview</h3>

<p>
The Student Housing Office manages student residences and accommodation
services. The office works to ensure students have access to safe, clean,
and comfortable living facilities that support learning, personal growth,
and campus engagement.
</p>

</div>

<div class="section">

<h3>Services Offered</h3>

<div class="grid">

<div class="card">
<h4> Dormitory Services</h4>
<p>
Well-organized dormitory facilities providing students with secure and
comfortable living spaces during their studies.
</p>
</div>

<div class="card">
<h4> Housing Allocation</h4>
<p>
Fair and transparent room allocation services for eligible students
based on institutional policies.
</p>
</div>

<div class="card">
<h4> Residence Hall Information</h4>
<p>
Information about residence halls, facilities, regulations, and
student responsibilities within campus housing.
</p>
</div>

<div class="card">
<h4> Accommodation Support</h4>
<p>
Support services that address student accommodation concerns,
maintenance requests, and residential welfare issues.
</p>
</div>

</div>

</div>

<div class="section">

<h3>Residence Facilities</h3>

<ul>
<li>Student Dormitory Rooms</li>
<li>Reading and Study Areas</li>
<li>Water Supply Services</li>
<li>Electricity Services</li>
<li>Sanitation Facilities</li>
<li>Campus Security Services</li>
<li>Common Recreation Areas</li>
<li>Residential Supervisors</li>
</ul>

</div>

<div class="section">

<h3>Housing Guidelines</h3>

<ul>
<li>Maintain cleanliness in rooms and surroundings.</li>
<li>Respect fellow students and residence staff.</li>
<li>Follow all dormitory regulations.</li>
<li>Protect college property and facilities.</li>
<li>Observe safety and security procedures.</li>
<li>Promote peaceful and responsible living.</li>
</ul>

</div>

<div class="section">

<h3>Benefits of Campus Housing</h3>

<ul>
<li>Safe and Secure Living Environment</li>
<li>Easy Access to Classrooms and Workshops</li>
<li>Better Study Opportunities</li>
<li>Student Community Engagement</li>
<li>Participation in Campus Activities</li>
<li>Supportive Academic Environment</li>
</ul>

</div>

<div class="section">

<h3>Accommodation Support Services</h3>

<ul>
<li>Housing Information and Guidance</li>
<li>Residence Management Services</li>
<li>Maintenance and Repair Requests</li>
<li>Conflict Resolution Assistance</li>
<li>Student Welfare Support</li>
<li>Residential Life Programs</li>
</ul>

</div>

<div class="section">

<h3>Office Working Hours</h3>

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

<p><strong>Office:</strong> Student Housing & Accommodation Office</p>

<p><strong>College:</strong> Major General Mulugeta Buli Polytechnic College</p>

<p><strong>Location:</strong> Student Affairs Building</p>

<p><strong>Email:</strong> housing@mgmbptc.edu.et</p>

<p><strong>Phone:</strong> +251 XXX XXX XXX</p>

<p><strong>Working Days:</strong> Monday – Saturday</p>

</div>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>