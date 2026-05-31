<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_slug = str_replace('.php', '', $current_page);

// Count pending report approvals for badge
$pending_reports = 0;
try {
    $pr_stmt = $pdo->query("SELECT COUNT(*) FROM activities WHERE activity_date >= CURDATE()");
    $pending_reports = $pr_stmt->fetchColumn() ?: 0;
} catch (Exception $e) {
}
?>

<!-- Mobile Header -->
<div class="d-lg-none d-flex justify-content-between align-items-center p-3 fixed-top bg-white shadow-sm"
    style="z-index:1040;">
    <div class="d-flex align-items-center gap-2">
        <img src="../assets/images/DSNC.png" alt="DNSC Logo" style="width:36px;height:36px;object-fit:contain;">
        <div>
            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;">DNSC NSTP</h6>
            <small class="text-muted" style="font-size:0.7rem;">Coordinator Portal</small>
        </div>
    </div>
    <button class="btn btn-sm border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#responsiveSidebar">
        <i class="bi bi-list fs-3 text-dark"></i>
    </button>
</div>

<!-- Sidebar -->
<div class="offcanvas-lg offcanvas-start sidebar p-0 d-flex flex-column" tabindex="-1" id="responsiveSidebar">

    <!-- Mobile close -->
    <div class="offcanvas-header d-lg-none border-bottom px-3 py-3">
        <h6 class="offcanvas-title fw-bold">Menu</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
            data-bs-target="#responsiveSidebar"></button>
    </div>

    <!-- Brand (matching reference: logo + "DNSC NSTP" / "Coordinator Portal") -->
    <div class="sidebar-brand-wrap">
        <img src="../assets/images/DSNC.png" alt="DNSC Logo" class="brand-logo" style="width:40px;height:40px;object-fit:contain;flex-shrink:0;">
        <div class="brand-text">
            <h5>DNSC NSTP</h5>
            <small>Coordinator Portal</small>
        </div>
    </div>

    <!-- Navigation (matching reference nav items exactly) -->
    <div class="flex-grow-1 mb-auto">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?= $page_slug === 'dashboard' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="manage_sections.php"
                    class="nav-link <?= $page_slug === 'manage_sections' || $page_slug === 'manage_students' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Sections & Students
                </a>
            </li>
            <li class="nav-item">
                <a href="manage_instructors.php" class="nav-link <?= $page_slug === 'manage_instructors' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg> Instructors & ROTC Officers
                </a>
            </li>
        </ul>

        <p class="sidebar-section-label mt-4">REPORTS & PLAN</p>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="submissions.php" class="nav-link <?= $page_slug === 'submissions' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><polyline points="9 15 11 17 15 13"/></svg> Report & Activity Approvals
                    <?php if ($pending_reports > 0): ?>
                        <span class="badge-count"><?= $pending_reports ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="calendar.php" class="nav-link <?= $page_slug === 'calendar' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Activity Calendar
                </a>
            </li>
        </ul>

        <p class="sidebar-section-label mt-4">GRADES</p>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="scan_grades.php" class="nav-link <?= $page_slug === 'scan_grades' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><line x1="3" y1="12" x2="21" y2="12"/></svg> OCR Grade
                </a>
            </li>
            <li class="nav-item">
                <a href="certificates.php" class="nav-link <?= $page_slug === 'certificates' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg> Certificates
                </a>
            </li>
            <li class="nav-item">
                <a href="certificate_templates.php" class="nav-link <?= $page_slug === 'certificate_templates' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg> Certificate Templates
                </a>
            </li>
        </ul>

        <p class="sidebar-section-label mt-4">HISTORY</p>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="reports.php" class="nav-link <?= $page_slug === 'reports' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M21 8v13H3V8z"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg> Student Archive
                </a>
            </li>
            <li class="nav-item">
                <a href="audit_logs.php" class="nav-link <?= $page_slug === 'audit_logs' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M8 21h12a2 2 0 0 0 2-2v-2H10v2a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12v4"/><line x1="16" y1="13" x2="18" y2="13"/><line x1="10" y1="13" x2="14" y2="13"/><line x1="10" y1="17" x2="14" y2="17"/></svg> Audit Logs
                </a>
            </li>
        </ul>
    </div>

    <!-- User Info + Sign Out (matching reference: avatar + name + Coordinator + logout icon) -->
    <?php
    $fullName = $_SESSION['full_name'] ?? 'Coordinator Profile';
    $nameParts = explode(' ', str_replace(['Dr. ', 'Prof. '], '', $fullName));
    $initials = count($nameParts) > 1 
        ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
        : strtoupper(substr($fullName, 0, 2));
    ?>
    <div class="mt-auto p-3 border-top d-flex align-items-center justify-content-between" style="border-color: #E5E7EB !important;">
        <div class="d-flex align-items-center gap-2">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #F59E0B, #FB7185); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.95rem;">
                <?= htmlspecialchars($initials) ?>
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: 500; color: #111827; line-height: 1.2;"><?= htmlspecialchars($fullName) ?></div>
                <div style="font-size: 0.7rem; color: #6B7280; margin-top: 2px;">Coordinator</div>
            </div>
        </div>
        <a href="../logout.php" class="text-muted text-decoration-none d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-box-arrow-right" style="font-size: 1.1rem; color: #9CA3AF;"></i>
        </a>
    </div>
</div>