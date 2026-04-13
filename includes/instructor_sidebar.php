<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="d-lg-none d-flex justify-content-between align-items-center p-3 fixed-top shadow-sm" style="background-color: #004D40 !important;">
    <div>
        <h5 class="text-white mb-0 fw-bold">NSTP System</h5>
        <small class="text-white-50" style="font-size: 0.75rem;">Instructor Portal</small>
    </div>
    <button class="btn btn-outline-light border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#responsiveSidebar">
        <i class="bi bi-list fs-2"></i>
    </button>
</div>

<div class="offcanvas-lg offcanvas-start sidebar p-3 d-flex flex-column" tabindex="-1" id="responsiveSidebar" style="background-color: #004D40 !important;">
    
    <div class="offcanvas-header d-lg-none border-bottom border-light border-opacity-10 mb-3 pb-3">
        <h5 class="offcanvas-title text-white fw-bold">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#responsiveSidebar"></button>
    </div>

    <div class="mb-4 mt-2 px-3 d-none d-lg-block">
        <h4 class="text-white fw-bold mb-0">NSTP System</h4>
        <small class="text-white-50" style="font-size: 0.8rem;">Instructor Portal</small>
    </div>
    
    <ul class="nav flex-column mb-auto gap-1 mt-2">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <i class="bi bi-grid me-3"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="my_section.php" class="nav-link <?= ($current_page == 'my_section.php') ? 'active' : '' ?>">
                <i class="bi bi-person-video3 me-3"></i> My Section
            </a>
        </li>
        <li class="nav-item">
            <a href="students.php" class="nav-link <?= ($current_page == 'students.php') ? 'active' : '' ?>">
                <i class="bi bi-people me-3"></i> Students
            </a>
        </li>

        <li class="nav-item">
            <a href="activity_plans.php" class="nav-link <?= ($current_page == 'activity_plans.php') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text me-3"></i> Activity Plans
            </a>
        </li>
        <li class="nav-item">
            <a href="reports.php" class="nav-link <?= ($current_page == 'reports.php') ? 'active' : '' ?>">
                <i class="bi bi-clipboard-check me-3"></i> Reports
            </a>
        </li>
        <li class="nav-item">
            <a href="activities.php" class="nav-link <?= ($current_page == 'activities.php') ? 'active' : '' ?>">
                <i class="bi bi-book me-3"></i> Activities
            </a>
        </li>
    </ul>
    
    <div class="mt-auto mb-2 text-center pt-4">
        <a href="../logout.php" class="btn btn-sm btn-light w-100 text-muted fw-medium">Sign Out</a>
    </div>
</div>