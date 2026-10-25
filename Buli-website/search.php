<?php
$page_title = "Search Results";
require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/includes/db.php";

function normalize_search_text($value)
{
    return strtolower(trim(preg_replace('/[^a-z0-9\s]/i', ' ', (string) $value)));
}

function matches_search_query($haystack, $query)
{
    $haystack = normalize_search_text($haystack);
    $query = normalize_search_text($query);

    if ($query === '') {
        return false;
    }

    return strpos($haystack, $query) !== false;
}

$query = trim($_GET['q'] ?? '');
$results = [];

if (!empty($query)) {
    $q_lower = strtolower($query);

    $site_pages = [
        ["title" => "Home", "desc" => "College homepage with TVET programs, flagship centers, announcements and student opportunities.", "keywords" => ["home", "main page", "college", "campus", "excellence", "students", "announcements", "news"], "link" => "index.php", "category" => "Home"],
        ["title" => "About MGMBPTC", "desc" => "Overview of the college, background, mission, and institutional identity.", "keywords" => ["about", "who we are", "college profile", "background", "mission", "vision", "values", "history", "gallery", "institution"], "link" => "about.php", "category" => "About"],
        ["title" => "Mission", "desc" => "Institutional mission, community engagement, and service values.", "keywords" => ["mission", "goal", "purpose", "community", "service", "values", "vision", "objective"], "link" => "mission.php", "category" => "About"],
        ["title" => "History", "desc" => "College history, milestones, and development over time.", "keywords" => ["history", "milestone", "development", "background", "timeline", "institutional growth"], "link" => "history.php", "category" => "About"],
        ["title" => "Gallery", "desc" => "Photos and visuals of campus life, ceremonies, training, and events.", "keywords" => ["gallery", "photos", "campus life", "event", "image", "visuals", "museum", "activities"], "link" => "gallery.php", "category" => "About"],
        ["title" => "Admission & Entry", "desc" => "Applications, registration process, fees, entry requirements, and calendars.", "keywords" => ["admission", "apply", "registration", "entry", "students", "fees", "calendar", "documents", "online form", "tuition"], "link" => "admission.php", "category" => "Admissions"],
        ["title" => "Academics & Programs", "desc" => "TVET departments, training tracks, curricula, guidance, and student support.", "keywords" => ["academics", "program", "department", "curriculum", "student support", "training", "courses", "study", "education", "ntqf", "diploma", "certificate"], "link" => "academics.php", "category" => "Academics"],
        ["title" => "Vocational Guidance", "desc" => "Career guidance, counseling, and counseling support for choosing the right program.", "keywords" => ["guidance", "counseling", "career", "career advice", "student counseling", "job placement", "aptitude", "skills"], "link" => "academics.php#guidance", "category" => "Student Service"],
        ["title" => "Innovation & Incubation", "desc" => "Technology transfer, startup support, prototyping, and production services.", "keywords" => ["innovation", "incubation", "technology hub", "prototype", "startup", "production", "transfer", "manufacturing", "ideas", "research"], "link" => "innovation.php#incubation", "category" => "Innovation"],
        ["title" => "Production Unit", "desc" => "Commercial production, fabrication, and industry-linked manufacturing support.", "keywords" => ["production", "trade unit", "commercial production", "fabrication", "industry", "manufacturing", "workshop", "product"], "link" => "innovation.php#production", "category" => "Innovation"],
        ["title" => "Offices & Administration", "desc" => "Dean, vice deans, directorates, administration, leadership, and offices.", "keywords" => ["offices", "administration", "dean", "vice dean", "director", "directorate", "academic office", "finance", "human resource", "procurement", "audit", "ethics"], "link" => "offices.php", "category" => "Administration"],
        ["title" => "News & Updates", "desc" => "Latest announcements, events, achievements, and college news.", "keywords" => ["news", "updates", "announcement", "events", "stories", "achievements", "notice", "latest"], "link" => "news.php", "category" => "News"],
        ["title" => "Research & Development", "desc" => "Research activities, knowledge generation, and innovation support.", "keywords" => ["research", "development", "innovation", "study", "project", "technology", "knowledge", "science"], "link" => "research.php", "category" => "Research"],
        ["title" => "Downloads", "desc" => "Forms, handbooks, calendars, guidelines, and academic documents.", "keywords" => ["download", "documents", "forms", "calendar", "handbook", "curriculum", "guideline", "pdf", "academic file", "application"], "link" => "downloads.php", "category" => "Downloads"],
        ["title" => "Health Services", "desc" => "Medical, health support, and student welfare services on campus.", "keywords" => ["health", "medical", "clinic", "wellbeing", "student wellness", "care", "first aid", "hospital"], "link" => "health-services.php", "category" => "Student Service"],
        ["title" => "Housing & Hostel", "desc" => "Hostel information, accommodation support, and residential services.", "keywords" => ["housing", "hostel", "accommodation", "residence", "room", "dormitory", "living", "student housing"], "link" => "housing.php", "category" => "Student Service"],
        ["title" => "Career Development", "desc" => "Career coaching, entrepreneurship, employment support, and job readiness.", "keywords" => ["career", "development", "employment", "job", "placement", "entrepreneurship", "skills", "work readiness"], "link" => "career-development.php", "category" => "Student Service"],
        ["title" => "Clubs & Organizations", "desc" => "Student associations, clubs, leadership, and extracurricular activities.", "keywords" => ["clubs", "organizations", "student society", "association", "extra curric", "leadership", "activities"], "link" => "clubs.php", "category" => "Student Life"],
        ["title" => "Library Services", "desc" => "Reading rooms, resource support, digital references, and study spaces.", "keywords" => ["library", "books", "reading", "study room", "resources", "reference", "archives", "search"], "link" => "library-services.php", "category" => "Student Service"],
        ["title" => "Contact Us", "desc" => "College phone numbers, email, office details, and communication channels.", "keywords" => ["contact", "phone", "email", "address", "office", "communication", "telephone", "location"], "link" => "contact.php", "category" => "Contact"],
        ["title" => "Student Portal", "desc" => "Student sign in, access to academic records and learning tools.", "keywords" => ["student portal", "student login", "student account", "portal", "student record", "student service"], "link" => "student_login.php", "category" => "Portal"],
        ["title" => "Staff Portal", "desc" => "Staff login access for administrative and academic functions.", "keywords" => ["staff portal", "staff login", "employee portal", "academic staff", "teacher", "faculty", "admin"], "link" => "staff_login.php", "category" => "Portal"],
        ["title" => "Admin Panel", "desc" => "Administrative dashboard for secure college system management.", "keywords" => ["admin", "administrator", "dashboard", "management", "secure panel", "control", "super user"], "link" => "admin.php", "category" => "Portal"],
        ["title" => "e-Learning Hub", "desc" => "Online learning and digital instructional resources for students and staff.", "keywords" => ["elearning", "e-learning", "online learning", "digital learning", "course", "virtual", "platform", "learning hub"], "link" => "elearning.php", "category" => "Portal"]
    ];

    foreach ($site_pages as $page) {
        $search_text = $page['title'] . ' ' . $page['desc'] . ' ' . implode(' ', $page['keywords']);
        if (matches_search_query($search_text, $query)) {
            $results[] = $page;
        }
    }

    // 1. Search Departments & Academics
    $departments = [
        ["title" => "Information Technology (IT)", "desc" => "Hardware, Networking, Software Development, Database Administration, web development, programming, cyber security.", "link" => "academics.php#ntqf", "category" => "Academic Dept", "keywords" => ["it", "information technology", "computer", "network", "database", "software", "web", "programming", "system"]],
        ["title" => "Automotive Technology", "desc" => "Vehicle engine diagnostics, auto electricity, transmission systems, maintenance and repair.", "link" => "academics.php#ntqf", "category" => "Academic Dept", "keywords" => ["automotive", "vehicle", "engine", "motor", "repair", "mechanic", "auto" ]],
        ["title" => "Electrical & Electronics", "desc" => "Industrial control, power systems, electronic servicing, solar installation, electrical work.", "link" => "academics.php#ntqf", "category" => "Academic Dept", "keywords" => ["electrical", "electronics", "solar", "power", "installation", "control", "circuit"]],
        ["title" => "Manufacturing & Mechanical", "desc" => "CNC machining, lathe, milling, welding, metal fabrication, production engineering.", "link" => "academics.php#ntqf", "category" => "Academic Dept", "keywords" => ["mechanical", "manufacturing", "welding", "cnc", "lathe", "milling", "fabrication", "metal"]],
        ["title" => "Construction & Civil Technology", "desc" => "Building construction, road maintenance, plumbing, surveying, masonry, civil works.", "link" => "academics.php#ntqf", "category" => "Academic Dept", "keywords" => ["construction", "civil", "masonry", "surveying", "plumbing", "road", "building"]],
        ["title" => "Garment & Textile", "desc" => "Fashion design, tailoring, industrial sewing, textile production, garment making.", "link" => "academics.php#ntqf", "category" => "Academic Dept", "keywords" => ["garment", "textile", "fashion", "tailoring", "sewing", "dressmaking"]],
        ["title" => "Woodwork Technology", "desc" => "Furniture design, cabinet making, interior wood finishing, woodworking.", "link" => "academics.php#ntqf", "category" => "Academic Dept", "keywords" => ["woodwork", "wood", "furniture", "cabinet", "carpentry", "joinery"]],
        ["title" => "Business & Commerce", "desc" => "Accounting, marketing, HR management, secretarial science, business support.", "link" => "academics.php#ntqf", "category" => "Academic Dept", "keywords" => ["business", "commerce", "accounting", "marketing", "hr", "secretarial", "finance"]]
    ];

    foreach ($departments as $dept) {
        $search_text = $dept['title'] . ' ' . $dept['desc'] . ' ' . implode(' ', $dept['keywords']);
        if (matches_search_query($search_text, $query)) {
            $results[] = [
                "title" => $dept['title'],
                "desc" => $dept['desc'],
                "link" => $dept['link'],
                "category" => $dept['category']
            ];
        }
    }

    // 2. Search Innovation & Offices
    $institutional = [
        ["title" => "Incubation & Technology Hub", "desc" => "Prototyping laboratory, hardware fabrication, and startup support.", "link" => "innovation.php#incubation", "category" => "Innovation Hub", "keywords" => ["incubation", "technology hub", "prototype", "startup", "lab", "hardware", "innovation"]],
        ["title" => "Production & Trade Unit", "desc" => "Real-world manufacturing and commercial production services.", "link" => "innovation.php#production", "category" => "Production Unit", "keywords" => ["production", "trade", "manufacturing", "commercial", "services", "factory", "industry"]],
        ["title" => "Dean's Office", "desc" => "Strategic planning, leadership, and college governance.", "link" => "offices.php#dean", "category" => "Administration", "keywords" => ["dean", "leadership", "governance", "strategic", "office", "principal"]],
        ["title" => "Vice Deans Directory", "desc" => "Academic, technology development, and administrative vice deans.", "link" => "offices.php#vice-deans", "category" => "Administration", "keywords" => ["vice dean", "vice", "academic", "technology development", "administrative", "office"]],
        ["title" => "Institutional Directorates", "desc" => "HR, Finance, Procurement, Plan & Budget, Internal Audit, Ethics, Property.", "link" => "offices.php#directorates", "category" => "Directorates", "keywords" => ["directorates", "hr", "finance", "procurement", "audit", "ethics", "property", "budget"]],
        ["title" => "Vocational Guidance & Counseling", "desc" => "Career aptitude testing, job placement assistance, and student counseling.", "link" => "academics.php#guidance", "category" => "Student Service", "keywords" => ["guidance", "counseling", "career", "aptitude", "job placement", "support"]],
        ["title" => "Admission & Online Application", "desc" => "TVET diploma registration, required document uploads, fee structure.", "link" => "admission.php", "category" => "Admissions", "keywords" => ["admission", "online application", "registration", "fees", "requirements", "apply", "form"]],
        ["title" => "Download Center", "desc" => "Academic calendars, application forms, TVET handbooks, curriculum guides.", "link" => "downloads.php", "category" => "Downloads", "keywords" => ["download", "forms", "calendar", "handbook", "curriculum", "guide", "pdf", "document"]]
    ];

    foreach ($institutional as $item) {
        $search_text = $item['title'] . ' ' . $item['desc'] . ' ' . implode(' ', $item['keywords']);
        if (matches_search_query($search_text, $query)) {
            $results[] = [
                "title" => $item['title'],
                "desc" => $item['desc'],
                "link" => $item['link'],
                "category" => $item['category']
            ];
        }
    }

    // 3. Search Database News & Announcements if available
    if (isset($pdo) && $pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT id, title_en, content_en, type FROM news_events WHERE title_en LIKE :q OR content_en LIKE :q LIMIT 8");
            $stmt->execute([':q' => "%$query%"]);
            $db_news = $stmt->fetchAll();
            foreach ($db_news as $n) {
                $results[] = [
                    "title" => $n['title_en'],
                    "desc" => strlen($n['content_en']) > 140 ? substr($n['content_en'], 0, 137) . "..." : $n['content_en'],
                    "link" => "news.php#news" . $n['id'],
                    "category" => ucfirst($n['type'])
                ];
            }
        } catch (\PDOException $e) {
            // Database query fallback
        }
    }

    $seen = [];
    $final_results = [];
    foreach ($results as $result) {
        $signature = $result['title'] . '|' . $result['link'];
        if (!isset($seen[$signature])) {
            $seen[$signature] = true;
            $final_results[] = $result;
        }
    }
    $results = $final_results;
}
?>

<div class="page-header">
    <h2> Search Results</h2>
    <p><?php echo !empty($query) ? "Showing results for \"" . htmlspecialchars($query) . "\"" : "Please enter a search term above."; ?></p>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <!-- Search Form Input inside Page -->
    <form action="search.php" method="GET" style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem;">
        <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search programs, departments, news..." class="form-control" style="flex: 1;" required>
        <button type="submit" class="btn btn-secondary">Search</button>
    </form>

    <?php if (empty($query)): ?>
        <div class="alert alert-info">Enter a keyword above to search through MGMBPTC departments, admissions, news, and services.</div>
    <?php elseif (empty($results)): ?>
        <div class="alert alert-error">No results found for "<strong><?php echo htmlspecialchars($query); ?></strong>". Try searching for "IT", "Automotive", "Admission", "Dean", or "Fees".</div>
    <?php else: ?>
        <p style="font-weight: 700; color: var(--green-dark); margin-bottom: 1rem;">Found <?php echo count($results); ?> matching item(s):</p>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($results as $res): ?>
                <div style="background: var(--gray-100); border: 1.5px solid var(--border-color); border-left: 4px solid var(--green-mid); border-radius: var(--radius-sm); padding: 1rem 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                        <span class="dept-badge"><?php echo htmlspecialchars($res['category']); ?></span>
                        <a href="<?php echo htmlspecialchars($res['link']); ?>" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.78rem;">View Details &rarr;</a>
                    </div>
                    <h4 style="margin-bottom: 0.35rem; color: var(--green-dark);"><?php echo htmlspecialchars($res['title']); ?></h4>
                    <p style="font-size: 0.88rem; color: var(--gray-600); margin: 0;"><?php echo htmlspecialchars($res['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
