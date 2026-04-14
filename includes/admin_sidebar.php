<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_slug = str_replace('.php', '', $current_page);
?>

<div class="d-lg-none d-flex justify-content-between align-items-center p-3 fixed-top shadow-sm" style="background-color: #2B2866 !important;">
    <div>
        <h5 class="text-white mb-0 fw-bold">NSTP System</h5>
        <small class="text-white-50" style="font-size: 0.75rem;">Coordinator Portal</small>
    </div>
    <button class="btn btn-outline-light border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#responsiveSidebar">
        <i class="bi bi-list fs-2"></i>
    </button>
</div>

<div class="offcanvas-lg offcanvas-start sidebar p-3 d-flex flex-column" tabindex="-1" id="responsiveSidebar" style="background-color: #2B2866 !important;">

    <div class="offcanvas-header d-lg-none border-bottom border-light border-opacity-10 mb-3 pb-3">
        <h5 class="offcanvas-title text-white fw-bold">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#responsiveSidebar"></button>
    </div>

    <div class="mb-4 mt-2 px-3 d-none d-lg-block">
        <h4 class="text-white fw-bold mb-0">NSTP System</h4>
        <small class="text-white-50" style="font-size: 0.8rem;">Coordinator Portal</small>
    </div>

    <ul class="nav flex-column mb-auto gap-1 mt-2">
        <li class="nav-item">
            <a href="dashboard" class="nav-link <?= $page_slug === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-book me-3"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="manage_students" class="nav-link <?= $page_slug === 'manage_students' ? 'active' : '' ?>">
                <i class="bi bi-people me-3"></i> Students
            </a>
        </li>
        <li class="nav-item">
            <a href="manage_sections" class="nav-link <?= $page_slug === 'manage_sections' ? 'active' : '' ?>">
                <i class="bi bi-gear me-3"></i> Components
            </a>
        </li>
        <li class="nav-item">
            <a href="manage_instructors" class="nav-link <?= $page_slug === 'manage_instructors' ? 'active' : '' ?>">
                <i class="bi bi-person-badge me-3"></i> Instructors
            </a>
        </li>
        <li class="nav-item">
            <a href="submissions" class="nav-link <?= $page_slug === 'submissions' ? 'active' : '' ?>">
                <i class="bi bi-inbox me-3"></i> Submissions
            </a>
        </li>
        <li class="nav-item">
            <a href="calendar" class="nav-link <?= $page_slug === 'calendar' ? 'active' : '' ?>">
                <i class="bi bi-calendar3 me-3"></i> Calendar
            </a>
        </li>
        <li class="nav-item">
            <a href="scan_grades" class="nav-link <?= $page_slug === 'scan_grades' ? 'active' : '' ?>">
                <i class="bi bi-upc-scan me-3"></i> OCR Upload
            </a>
        </li>
        <li class="nav-item">
            <a href="reports" class="nav-link <?= $page_slug === 'reports' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-line me-3"></i> Reports
            </a>
        </li>
        <li class="nav-item">
            <a href="audit_logs" class="nav-link <?= $page_slug === 'audit_logs' ? 'active' : '' ?>">
                <i class="bi bi-shield-check me-3"></i> Audit Logs
            </a>
        </li>
    </ul>

    <div class="mt-auto mb-2 text-center pt-4">
        <a href="../logout" class="btn btn-sm btn-light w-100 text-muted fw-medium">Sign Out</a>
    </div>
</div>