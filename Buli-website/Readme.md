### 1. The `includes/` Directory

These files provide common layout elements and backend utilities shared across all pages.

*   #### **[lang.php](file:///c:/xampp/htdocs/Buli-website/includes/lang.php)**
    *   **Function:** Handles multilingual localization (English and Amharic) and session management.
    *   **How it works:**
        *   Checks if a PHP session is active and starts one if not (lines 2–4).
        *   Defaults the session language to English (`$_SESSION['lang'] = 'en'`).
        *   Intercepts URL language switch requests (e.g., `?lang=am`), updates the session variable, and redirects to a clean URL using `header("Location: ...")` (lines 11–21).
        *   Stores all UI text mappings (navigation links, buttons, headers, etc.) in a multidimensional array `$translations` (lines 26–117).
        *   Defines the global helper function **[__](file:///c:/xampp/htdocs/Buli-website/includes/lang.php#L120)** to translate dictionary keys. It retrieves the requested translation for the current language or falls back to English (lines 120–123).

*   #### **[header.php](file:///c:/xampp/htdocs/Buli-website/includes/header.php)**
    *   **Function:** Renders the HTML `<head>` metadata, links the styling sheet, and renders the top header/navigation menu.
    *   **How it works:**
        *   Requires `lang.php` (line 2) to set language variables and access the translation helper.
        *   Extracts the active page filename using `basename($_SERVER['PHP_SELF'])` (line 3) to apply active state styles to the navigation menu.
        *   Includes language switches (EN / አማ) that append query strings to toggle languages (lines 30–34).
        *   Includes a responsive mobile menu toggle button (`#navToggle`) and lists primary links dynamically using translated phrases (lines 44–94).

*   #### **[footer.php](file:///c:/xampp/htdocs/Buli-website/includes/footer.php)**
    *   **Function:** Renders the page footer and includes JavaScript scripts.
    *   **How it works:**
        *   Closes the `<main>` tag wrapping the page content.
        *   Outputs footer widgets containing brief college info, quick links, and campus contact details.
        *   Generates a dynamic copyright year using `date('Y')` (line 37).
        *   Injects the JavaScript file **[main.js](file:///c:/xampp/htdocs/Buli-website/js/main.js)** (line 41).

---

### 2. Main Page Files (Root Directory)

These PHP pages structure the front-end layout for each section of the college's website. They all require the header and footer modules to display a unified layout.

*   #### **[index.php](file:///c:/xampp/htdocs/Buli-website/index.php)**
    *   **Function:** The homepage of the website.
    *   **Features:**
        *   A hero banner with action buttons targeting admissions and about pages (lines 7–14).
        *   An announcement/notice board sidebar (lines 33–49) and a brief overview of the college.
        *   A statistics section highlighting metrics (students count, departments, industry partners, instructors).
        *   Cards showcasing latest news previews (lines 73–101).

*   #### **[about.php](file:///c:/xampp/htdocs/Buli-website/about.php)**
    *   **Function:** Introduces the college profile, mission, vision, core values, and governance.
    *   **Features:**
        *   Section for history and profile.
        *   Side-by-side display of college **Mission**, **Vision**, and **Core Values** (Excellence, Innovation, Integrity).
        *   A table rendering key leadership personnel (Dean, Vice Deans, Industry Liaison) (lines 68–99).

*   #### **[academics.php](file:///c:/xampp/htdocs/Buli-website/academics.php)**
    *   **Function:** Details the college's curriculum offerings and departments.
    *   **Features:**
        *   Breakdown of the *National TVET Levels (NTQF)* spanning Levels 1 to 5.
        *   Lists popular short-term industry training programs (AutoCAD, solar installation, basic coding).
        *   Displays an academic departments table detailing IT, Automotive, Electrical, Manufacturing, Construction, and Garment sectors (lines 52–93).

*   #### **[admission.php](file:///c:/xampp/htdocs/Buli-website/admission.php)**
    *   **Function:** Lists entry requirements and hosts a simulated application form.
    *   **Features:**
        *   Backend handling of POST inquiries (lines 7–19) to display validation results (Success / Error alerts).
        *   Documentation of admission files and transcripts required from applicants.
        *   An intake schedule calendar table.
        *   An online inquiry form (`#admissionForm`) requesting name, email, phone, and desired program.

*   #### **[contact.php](file:///c:/xampp/htdocs/Buli-website/contact.php)**
    *   **Function:** Provides campus contact information, office hours, a map placeholder, and a contact inquiry form.
    *   **Features:**
        *   Handles message submissions and provides feedback banners (lines 7–17).
        *   Features a contact form (`#contactForm`) with fields for name, email, subject, and message.

*   #### **[news.php](file:///c:/xampp/htdocs/Buli-website/news.php)**
    *   **Function:** Full notice board, event calendars, and detailed news articles.
    *   **Features:**
        *   Archived notice board (Registration dates, innovation seminars, graduation info).
        *   Upcoming event schedules.
        *   Three complete mock news articles regarding partnerships, evening ICT training, and workshop upgrades.

*   #### **[research.php](file:///c:/xampp/htdocs/Buli-website/research.php)**
    *   **Function:** Showcases active research fields, tech transfer models, and student innovations.
    *   **Features:**
        *   Profiles technology transfer focus areas (agriculture, construction, waste management).
        *   Highlights staff research (AR simulations, solar potentials, etc.).
        *   Table featuring student capstone success cases (Smart Egg Incubator, Mobile Campus Map, etc.).

*   #### **[services.php](file:///c:/xampp/htdocs/Buli-website/services.php)**
    *   **Function:** Outlines student affairs, library information, and extracurricular activities.
    *   **Features:**
        *   Cards detailing the digital library, college health clinic, and guidance counseling.
        *   Information regarding student clubs (Technology & Innovation, Sports, Environmental Protection).

*   #### **[staff.php](file:///c:/xampp/htdocs/Buli-website/staff.php)**
    *   **Function:** A searchable directory of academic and administrative personnel.
    *   **Features:**
        *   Mockup filter interface allowing searches by name and department (lines 15–40).
        *   Table showing staff profiles complete with name, department, role, and email contacts.

---

### 3. JavaScript and Documentation

*   #### **[main.js](file:///c:/xampp/htdocs/Buli-website/js/main.js)**
    *   **Function:** Coordinates user interactions and frontend form validation.
    *   **Features:**
        *   **Mobile Menu Toggle:** Adds event listeners to expand/collapse the mobile navigation bar when clicking the hamburger toggle, and closes it if the user clicks outside.
        *   **Contact Form Validation:** Validates the fields in `contact.php` to prevent submission if the name, email, or message is blank or if the email format is invalid.
        *   **Admission Form Validation:** Validates the fields in `admission.php` to ensure the name, email, phone, and department are provided.
        *   **Form Errors:** Creates red-tinted border elements and appends validation messages dynamically.

*   #### **[proposal.txt](file:///c:/xampp/htdocs/Buli-website/proposal.txt)**
    *   **Function:** The project blueprint proposal for the website development.
    *   **Features:**
        *   States project objectives, target users, and key pages.
        *   Details technical specifications (XAMPP server, PHP framework, HTTPS/SSL, responsive design).
        *   Provides design theme rules ("Shine Through Education and Technology") and official color schemes.
        *   Establishes development timeline phases (approx. 5–6 weeks).
        *   Outlines the estimated budget totaling 55,000 ETB.