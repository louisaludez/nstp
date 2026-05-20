# Figma Design Specification — Aurora University NSTP & Cadet Portal

> [!IMPORTANT]
> This document captures the **complete Figma design** from the shared link. The design includes **3 role-based dashboards** (Coordinator, Instructor, ROTC Officer), a **login/role-selection screen**, and **19 total pages**.

---

## 1. Login / Role Selection Screen

![Login Page](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_login.png)

### Layout
- **Split screen**: Left panel (dark navy/indigo gradient) + Right panel (white sign-in form)
- Left panel shows branding: logo icon, "AURORA UNIVERSITY", "NSTP & Cadet Portal"
- Tagline: "One platform for program coordination, classroom delivery, and ROTC command."
- Feature bullets: Single sign-on, Role-based access controls, Audit-logged report submissions

### Role Selection Cards (Right Panel)
| Role | Icon Color | Description |
|------|-----------|-------------|
| **Program Coordinator** | Teal/green | "Oversee sections, approve reports, issue certificates" |
| **Instructor** | Green square | "Manage your CWTS / LTS sections and submit reports" |
| **ROTC First Class Officer** | Dark circle | "Manage platoons, design activities, file accomplishments" |

- Selected role gets a highlighted border + "SELECTED" badge
- Sign-in button text updates dynamically: "Sign in as [Role] →"
- Fields: University Email + Password
- Footer: "Trouble signing in? Contact the NSTP/ROTC office."

### Design Tokens
- Left panel background: Deep navy/indigo gradient (`~#1e1b4b` to `~#312e81`)
- Accent dots on feature list: Blue (`#6366F1`)
- Sign-in button: Dark/black (`#1F2937`)
- Card border on selected: Matches role accent color

---

## 2. Program Coordinator Dashboard (8 Pages)

### 2A. Dashboard (Overview)

![Coordinator Dashboard](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_coordinator_dashboard.png)

**Header**: "Spring Semester · 2026" / "Welcome back, Maya"

**Sidebar** (White background, items have rounded pill-shape active state):
- Aurora U. / Program Office (with teal circle logo)
- **WORKSPACE** label
- Dashboard ✅ (active — filled blue/purple background)
- Sections & Students
- Instructors
- Report Approvals (badge: 4)
- OCR Grade Upload
- Activity Calendar
- Certificates
- Audit Logs

**Sidebar Active Style**: Rounded pill with `#6366F1` (indigo/blue-purple) background, white text

**Top Bar**: Search input + Help icon + Notification bell + "New Section" button (indigo)

**4 Stat Cards** (row):
| Stat | Value | Change | Icon BG |
|------|-------|--------|---------|
| Total Students | 2,847 | +4.2% (green) | Coral/red circle with people icon |
| Active Sections | 126 | +12 | Green circle with grid icon |
| Pass Rate | 91.4% | +1.8% | Red/coral circle with trend icon |
| Reports Pending | 23 | -6 (red) | Indigo circle with document icon |

**Charts Row**:
- **Enrollment Trend** (left, ~65% width): Line chart, Aug–Mar, showing upward trend from ~2300 to ~2900. Subtitle: "2,847 — ▲ 4.2% vs last semester"
- **Pass/Fail by Program** (right, ~35%): Bar chart with program codes (BSCS, BSIT, BSBA, BSEd, BSN, BSA). Green = Passed, Red = Failed

---

### 2B. Sections & Students

![Sections & Students](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_sections_students.png)

- Page title: "Sections & Students" / "Manage class rosters and section assignments"
- **Action buttons**: Filter + New Section (indigo)
- **Search bar** + "All programs" dropdown
- **Table columns**: SECTION | PROGRAM | STUDENTS | INSTRUCTOR | ROOM | STATUS
- Status badges: "Active" (green text), "Closed" (gray text)

| Section | Program | Students | Instructor | Room | Status |
|---------|---------|----------|------------|------|--------|
| BSCS-2A | BSCS | 42 | Prof. L. Tan | B-204 | Active |
| BSCS-2B | BSCS | 38 | Prof. R. Cruz | B-206 | Active |
| BSIT-3A | BSIT | 45 | Prof. A. Yusuf | C-101 | Active |
| BSIT-3B | BSIT | 41 | Prof. M. Lim | C-103 | Active |
| BSBA-1A | BSBA | 48 | Prof. K. Domingo | A-205 | Active |
| BSEd-4A | BSEd | 36 | Prof. P. Garcia | D-110 | Closed |

---

### 2C. Instructors

![Instructors](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_instructors.png)

- Page title: "Instructors" / "Faculty directory and section load"
- **Action button**: "✉ Invite Instructor" (indigo)
- **Card grid layout** (3 columns, 2 rows)

Each instructor card shows:
- Circular avatar with initials (colored background)
- Name + Department
- Status badge (Active / On Leave)
- Sections count + Students count
- Email + Extension number

| Name | Dept | Status | Sections | Students |
|------|------|--------|----------|----------|
| Prof. Lester Tan | Computer Science | Active | 4 | 158 |
| Prof. Rita Cruz | Chemistry | Active | 3 | 124 |
| Prof. Adam Yusuf | Mathematics | Active | 5 | 203 |
| Prof. Priya Garcia | Education | On Leave | 2 | 78 |
| Prof. Marco Lim | Information Tech | Active | 4 | 165 |
| Prof. Karen Domingo | Business Admin | Active | 3 | 142 |

---

### 2D. Report Approvals

![Report Approvals](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_report_approvals.png)

- **Left panel**: Pending Queue (4 awaiting review) — list of reports
- **Right panel**: Report detail view with action buttons

Pending Queue items:
- Tree-Planting Drive Report (Prof. Tan · CWTS 1-A) — 2h ago
- Adult Literacy Session #4 (Prof. Santos · LTS 2-A) — Yesterday
- Barangay Clean-Up Plan (Prof. Cruz · CWTS 1-C) — May 9
- Reading Buddies Kick-off (Prof. Garcia · LTS 2-B) — May 8

Report Detail:
- Title: "Tree-Planting Drive Report"
- Metadata: Submitted 2h ago | Beneficiaries: 42 community members | Attachments: 6 photos · 1 PDF
- Description text about the activity
- **3 Action Buttons**: ✅ Approve (green) | 🔄 Request Revisions (outline) | ❌ Reject (red text)

---

### 2E. OCR Grade Upload

![OCR Grade Upload](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_ocr_upload.png)

- **Drag & drop zone** (dashed border, purple upload icon)
- Text: "Drop grade sheets here or click to upload"
- Supports: PDF, PNG, JPG up to 25 MB, multi-page scans
- OCR engine v3.2

**Stats row**: Confidence 98.4% | Queue 2 files | Avg time ~12s/page

**Recent Uploads sidebar**:
| File | Section | Status |
|------|---------|--------|
| BSCS-2A_Midterm.pdf | BSCS-2A · 42 students | Processed |
| BSIT-3B_Finals.jpg | BSIT-3B · 41 students | Reviewing |
| BSBA-1A_Midterm.pdf | BSBA-1A · 48 students | Processed |
| BSEd-4A_Finals.png | BSEd-4A · 36 students | Failed |

---

### 2F. Activity Calendar

![Activity Calendar](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_activity_calendar.png)

- **Action buttons**: "+ Create Activity" + "📤 Submit"
- **Week view** (May 14 – May 20, 2026) with day columns
- Color-coded event pills (green, red, blue)
- **All Activities list** below (4 scheduled):

| Activity | Date/Time | Venue | Audience | Status |
|----------|-----------|-------|----------|--------|
| Faculty Council Meeting | May 14, 10:00 AM | Dean's Conf. Room | Faculty | Submitted |
| Midterm Reports Deadline | May 16, 11:59 PM | Coordinator Portal | All Instructors | Submitted |
| OCR Grade Upload Window | May 20, All day | Online | All Programs | Draft |
| Spring Commencement Rehearsal | May 24, 2:00 PM | Main Auditorium | Graduating Students | Submitted |

---

### 2G. Certificates

![Certificates](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_certificates.png)

- **Action buttons**: "Generate Certificates" (orange) + "🎓 Generate Batch" (orange)
- **Completion Certificates** list:

| Certificate | Students | Eligible | Status |
|-------------|----------|----------|--------|
| Spring 2026 — CWTS 1 Completion | 198 students | May 30 | Ready → Generate |
| Spring 2026 — LTS 2 Completion | 116 students | May 30 | Ready → Generate |
| ROTC Basic Course — Batch 14 | 84 students | Jun 6 | Reviewing → Generate |

- **Recently Issued sidebar**: Maria Aquino, Jose Reyes, Anna Bautista, Karl Domingo with timestamps

---

### 2H. Audit Logs
*(Same sidebar, table with timestamped system events)*

---

## 3. Instructor Dashboard (5 Pages)

### 3A. Overview

![Instructor Dashboard](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_instructor_dashboard.png)

**Header**: "Good morning, Julian"
**Sidebar** (same teal/green as coordinator, but labeled "Instructor Portal"):
- Overview ✅ (active)
- My Classes (badge: 4)
- Activity Plans
- Accomplishment Reports (badge: 3)
- Announcements

**4 Stat Cards**:
| Stat | Value | Detail | Icon |
|------|-------|--------|------|
| Assigned Sections | 4 | — | Green grid |
| Total Students | 146 | Across all sections | Teal people |
| Reports Pending | 3 | 2 due this week | Red document |
| Approved YTD | 27 | +5 this month | Purple check |

**Your Sections** (card grid, 2x2):
- CWTS 1 · Section A (Active, 42 students, Bldg B Rm 204, Mon 1:00-4:00 PM, 68% progress)
- CWTS 1 · Section C (Active, 38 students, Bldg B Rm 206, Tue 9:00-12:00 PM, 54% progress)
- LTS 2 · Section A (Field Day badge, 35 students, Bldg D Rm 110, Wed 1:00-4:00 PM, 72% progress)
- LTS 2 · Section B (Active, 31 students, Bldg D Rm 112, Thu 9:00-12:00 PM, 41% progress)

**Submission Tracker** (right sidebar):
- Tree-Planting Drive Report — ✏️ Draft
- Adult Literacy Session #4 — ✅ Submitted
- Barangay Clean-Up Plan — ✅ Approved
- Reading Buddies Kick-off — ⚠️ Revisions
- Mid-semester Accomplishment — ✏️ Draft

---

### 3B. My Classes

![Instructor Classes](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_instructor_classes.png)

- **Action buttons**: Filter + "📋 Take Attendance"
- **Section cards** with colored headers:
  - CWTS sections: Purple/indigo header
  - LTS sections: Orange/amber header
- Each card: section name, program type, student count, room, schedule, progress bar
- Quick actions: Roster | Attendance | Grades | New report

---

### 3C. Activity Plans

![Activity Plans](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_instructor_activity_plans.png)

- **Left**: Table of activities (Activity, Section, Date, Venue, Status)
- **Right**: Plan Template form (Title, Date, Duration, Section dropdown, Objectives textarea)
- Buttons: Save Draft | Submit for Approval (teal/green)

---

## 4. ROTC First Class Officer Dashboard (5 Pages)

### 4A. Overview

![ROTC Dashboard](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_rotc_dashboard.png)

**Completely different theme**: Dark navy sidebar with **gold/yellow** accent
- Logo: Gold star ★ with "AURORA ROTC / OFFICER CONSOLE"
- Header: "SATURDAY DRILL · MAY 16, 2026" / "Stand-to, Lt. Castillo"

**Sidebar Navigation** (under "COMMAND"):
- Overview ✅ (gold active background)
- Platoon Management (badge: 4)
- Rosters
- Activity Designs (badge: 2)
- Master Calendar

**4 Stat Cards** (military style, uppercase labels):
| Stat | Value | Detail |
|------|-------|--------|
| TOTAL CADETS | 148 | +12 this intake |
| ACTIVE PLATOONS | 3 | Alpha · Bravo · Charlie |
| UNASSIGNED | 6 | Awaiting assignment |
| REPORTS OPEN | 4 | 2 due this week |

**Documentation — Accomplishment Reports** (with progress bars):
- Q1 Tactical Drill Accomplishment — Draft (60%)
- Civil-Military Operations Report — Under Review (100%)
- Community Outreach — Brgy. San Roque — Approved (100%)
- Monthly Strength Report — May — Revisions (75%)

**Bulletin Board — Official Notices** (right panel):
- ORDER (red label): General Order 2026-14
- SCHEDULE (green label): Tactical inspection
- MEMO (blue label): Submit Q1 reports
- NOTICE (yellow label): New cadets integration
- NEXT FORMATION: 0700H · Parade Grounds

---

### 4B. Rosters

![ROTC Rosters](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_rotc_rosters.png)

- **Action buttons**: Filter | Export | 🎖 Add Cadet
- **Table columns**: CADET ID | NAME | RANK | PLATOON | SPECIALTY | YEAR | STATUS
- Platoon badges: Alpha (blue), Bravo (green), Charlie (purple), Unassigned (gray)
- Rank values: Pvt, Cpl, Sgt
- Specialties: Rifle, Medical, Signal, Support

---

### 4C. Activity Designs

![ROTC Activity Designs](C:\Users\PC IC\.gemini\antigravity\brain\fca386aa-809f-4a6d-9fc5-da25e72d985f\figma_rotc_activity_designs.png)

- **Left**: Designs table (Activity, Phase, Date, Duration, Status)
- **Right**: Design Brief form (Activity Title, Phase dropdown, Date, Duration, Objectives)
- Buttons: Save Draft | 📤 Submit

---

## 5. Design System Summary

### Color Palette

| Token | Coordinator | Instructor | ROTC |
|-------|------------|------------|------|
| Sidebar BG | White | White | Dark Navy (`#1F2937`) |
| Sidebar Active | Indigo pill (`#6366F1`) | Teal pill (`#0D9488`) | Gold pill (`#F59E0B`) |
| Sidebar Text | Gray-600 | Gray-600 | Light gray |
| Primary Accent | Indigo (`#6366F1`) | Teal (`#0D9488`) | Gold (`#F59E0B`) |
| CTA Buttons | Indigo | Teal | Gold |
| Top Bar | White w/ subtle border | White w/ subtle border | White w/ subtle border |

### Typography
- **Font**: Inter (Google Fonts)
- **Heading**: 600-700 weight, dark gray
- **Body**: 400-500 weight
- **Labels**: Uppercase, letter-spacing, small caps (e.g., "WORKSPACE", "COMMAND")

### Component Patterns
- **Cards**: `border-radius: 12px`, subtle shadow, white background
- **Stat cards**: Icon circle (48px) + large number + label + delta badge
- **Tables**: Clean, no borders between rows, header in uppercase small text
- **Buttons**: Rounded (8px), filled for primary, outline for secondary
- **Sidebar**: Fixed left, ~240px width, collapsible
- **Active nav**: Fully rounded pill shape with role-colored background
- **Badges**: Small rounded pills for counts
- **Progress bars**: Thin (6-8px), rounded, colored by status

### Key Differences from Current Codebase

> [!WARNING]
> The current PHP codebase uses a **dark green sidebar** (`#2b5f56`) with **mint green** active state (`#c8f5d0`). The Figma design uses a **white sidebar** with **indigo pill** active state for the Coordinator, and a completely different **dark navy + gold** theme for ROTC.

| Aspect | Current Code | Figma Design |
|--------|-------------|--------------|
| Sidebar BG | Dark green `#2b5f56` | White (Coordinator/Instructor) or Dark navy (ROTC) |
| Active link | Mint green `#c8f5d0` bg | Indigo/Teal/Gold pill |
| Login page | Simple centered card | Split-screen with role selector |
| Role system | Admin + Instructor | Coordinator + Instructor + ROTC Officer |
| Navigation items | 9 items | 8 items (Coordinator), 4 items (Instructor), 5 items (ROTC) |
| Dashboard charts | No charts | Enrollment trend line + Pass/Fail bar chart |
| Certificates page | Missing | Full certificate generation UI |
| ROTC dashboard | Missing | Completely new military-themed interface |
