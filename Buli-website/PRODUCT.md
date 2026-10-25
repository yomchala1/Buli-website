# PRODUCT.md — Impeccable Design Context

## 1. Product Identity & Overview
* **Product Name**: Major General Mulu Geta Buli Polytechnic College (MGMBPTC) Web Platform & Portal
* **Domain**: Technical and Vocational Education & Training (TVET), Polytechnic Engineering, Academic Management & Student Services
* **Institution Type**: Government Polytechnic College & TVET Technology Transfer Center
* **Primary URL / Workspace**: `c:\xampp\htdocs\Buli-website`
* **Languages**: English (`en`) & Amharic (`am`) — Fully localized bilingual support

---

## 2. Target Audience & Core Personas

### 1. Prospective TVET Students
* **Needs**: Clear program catalogs (NTQF Levels 1-5), admission requirements, intake deadlines, online inquiry form, housing and campus service info.
* **UX Priority**: Frictionless navigation, bold call-to-action buttons, high readability, instant visual feedback.

### 2. Enrolled Students
* **Needs**: Access to Student Portal (`student_portal.php`), academic records, timetable, digital library (`library-services.php`), e-learning resources (`elearning.php`), student clubs, health services.
* **UX Priority**: Fast access to portals, dashboard layout, secure login status, clean mobile-responsive layout.

### 3. Faculty & Academic Staff
* **Needs**: Staff directory listing (`staff.php`), Staff Portal (`staff_portal.php`), grade entry, course materials management, administration tools (`admin.php`).
* **UX Priority**: Data-dense tables, quick search & filter capabilities, clear feedback banners.

### 4. Industry Partners & Enterprise Liaisons
* **Needs**: Information on technology transfer (`research.php`), short-term skill training, apprenticeship programs, graduate employability.
* **UX Priority**: Professional showcase cards, structured metrics, downloadable resources (`downloads.php`).

---

## 3. Brand Archetype & Aesthetic Positioning

* **Design Lane**: *Academic Modernism & Polytechnic Engineering*
* **Brand Tone**: Authoritative, Trustworthy, Inspiring, Technical, Clean, Empowering.
* **Primary Visual Anchor**:
  * **Forest Green (`#1e4d24` / `#1a3d1f`)**: Represents institutional heritage, stability, and growth.
  * **Imperial Gold (`#c9a227` / `#e6b830`)**: Represents achievement, prestige, and vocational excellence.
  * **Deep Blue & Warm Teal (`#1a4480` / `#0d7377`)**: Represents technical engineering, computing, and modern technology transfer.
  * **Slate Neutrals (`#111827` to `#f3f4f6`)**: Provides crisp visual structure and contrast.

---

## 4. Anti-Patterns & "AI Slop" Prevention Rules

To preserve the custom, high-end identity of Major General Mulu Geta Buli Polytechnic College, the following generic AI code patterns are strictly forbidden:

1. 🚫 **No Generic SaaS Indigo/Purple Gradients**: Avoid standard generic purple/violet AI themes. Stick to the institution's Forest Green + Imperial Gold identity.
2. 🚫 **No Monotonous Inter Font Overhead**: Do not use raw default Inter for everything. Use **Plus Jakarta Sans** for display headers and section titles to project architectural precision.
3. 🚫 **No Floating Borderless Card Soup**: Avoid floating white boxes on light gray without clear borders or top accent indicators. All cards must feature structured borders (`--border-color`) or defined depth tokens.
4. 🚫 **No Low-Contrast Badges**: Badges with gold, green, or blue backgrounds MUST maintain WCAG 2.1 AA compliant contrast (minimum 4.5:1 for body text, 3:1 for large headers).
5. 🚫 **No Unstyled HTML Tables**: Every table (staff directory, course catalogs, admission schedules) must use styled headers, hover state rows, zebra striping, and responsive wrapping.
6. 🚫 **No Layout Jumps on Hover**: Interactive elements must hover smoothly using GPU-accelerated CSS transforms (`transform: translateY(-2px)`) rather than modifying margins or padding.

---

## 5. Core Touchpoints & Page Architecture

* **[index.php](file:///c:/xampp/htdocs/Buli-website/index.php)**: Hero section with metric counters, latest announcements, featured departments, news cards.
* **[about.php](file:///c:/xampp/htdocs/Buli-website/about.php)**: History, Mission, Vision, Core Values, Leadership Table.
* **[academics.php](file:///c:/xampp/htdocs/Buli-website/academics.php)**: NTQF Levels 1-5 breakdown, short courses, department catalog.
* **[admission.php](file:///c:/xampp/htdocs/Buli-website/admission.php)**: Requirements, schedule table, admission inquiry form with validation feedback.
* **[staff_portal.php](file:///c:/xampp/htdocs/Buli-website/staff_portal.php)** & **[student_portal.php](file:///c:/xampp/htdocs/Buli-website/student_portal.php)**: Authenticated dashboard panels.
* **[research.php](file:///c:/xampp/htdocs/Buli-website/research.php)**: Technology transfer projects, student capstones, research publications.
