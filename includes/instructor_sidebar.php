<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_slug = str_replace('.php', '', $current_page);

// Count assigned sections for badge
$my_sections_count = 0;
try {
    $sec_stmt = $pdo->prepare("SELECT COUNT(*) FROM sections WHERE instructor_id = ?");
    $sec_stmt->execute([$_SESSION['user_id'] ?? 0]);
    $my_sections_count = $sec_stmt->fetchColumn() ?: 0;
} catch (Exception $e) {}
?>

<!-- Mobile Header -->
<div class="d-lg-none d-flex justify-content-between align-items-center p-3 fixed-top bg-white shadow-sm" style="z-index:1040;">
    <div class="d-flex align-items-center gap-2">
        <img src="../assets/images/DSNC.png" alt="DNSC Logo" style="width:36px;height:36px;object-fit:contain;">
        <div>
            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;">DNSC</h6>
            <small class="text-muted" style="font-size:0.7rem;">Davao del Norte State College</small>
        </div>
    </div>
    <button class="btn btn-sm border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#responsiveSidebar">
        <i class="bi bi-list fs-3 text-dark"></i>
    </button>
</div>

<!-- Sidebar -->
<div class="offcanvas-lg offcanvas-start sidebar p-0 d-flex flex-column" tabindex="-1" id="responsiveSidebar"
     style="--primary-active: #0D9488;">

    <!-- Mobile close -->
    <div class="offcanvas-header d-lg-none border-bottom px-3 py-3">
        <h6 class="offcanvas-title fw-bold">Menu</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#responsiveSidebar"></button>
    </div>

    <!-- Brand -->
    <div class="sidebar-brand-wrap">
        <img src="../assets/images/DSNC.png" alt="DNSC Logo" class="brand-logo" style="width:40px;height:40px;object-fit:contain;flex-shrink:0;">
        <div class="brand-text">
            <h5>DNSC CWTS · LTS</h5>
            <small>Instructor Portal</small>
        </div>
    </div>

    <!-- Navigation -->
    <p class="sidebar-section-label">Workspace</p>

    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?= $page_slug === 'dashboard' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Overview
            </a>
        </li>
        <li class="nav-item">
            <a href="my_section.php" class="nav-link <?= $page_slug === 'my_section' || $page_slug === 'students' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg> My Classes
                <?php if ($my_sections_count > 0): ?>
                    <span class="badge-count"><?= $my_sections_count ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a href="activity_plans.php" class="nav-link <?= $page_slug === 'activity_plans' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><line x1="12" y1="11" x2="16" y2="11"/><line x1="12" y1="16" x2="16" y2="16"/></svg> Activity Plans
            </a>
        </li>
        <li class="nav-item">
            <a href="reports.php" class="nav-link <?= $page_slug === 'reports' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg> Accomplishment Reports
            </a>
        </li>
        <li class="nav-item">
            <a href="announcements.php" class="nav-link <?= $page_slug === 'announcements' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="m3 11 19-9-9 19-2-8-8-2z"/></svg> Announcements
            </a>
        </li>
    </ul>

    <!-- User Info + Sign Out -->
    <?php
    $fullName = $_SESSION['full_name'] ?? 'Instructor Profile';
    $nameParts = explode(' ', str_replace('Prof. ', '', $fullName));
    $initials = count($nameParts) > 1 
        ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
        : strtoupper(substr($fullName, 0, 2));
    ?>
    <div class="mt-auto p-3 border-top d-flex align-items-center justify-content-between" style="border-color: #E5E7EB !important;">
        <div class="d-flex align-items-center gap-2">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #10B981, #14B8A6); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.95rem;">
                <?= htmlspecialchars($initials) ?>
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: 500; color: #111827; line-height: 1.2;"><?= htmlspecialchars($fullName) ?></div>
                <div style="font-size: 0.7rem; color: #6B7280; margin-top: 2px;">CWTS · LTS Instructor</div>
            </div>
        </div>
        <a href="../logout.php" class="text-muted text-decoration-none d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-box-arrow-right" style="font-size: 1.1rem; color: #9CA3AF;"></i>
        </a>
    </div>
</div>