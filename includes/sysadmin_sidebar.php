<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_slug = str_replace('.php', '', $current_page);
?>
<!-- Mobile Header -->
<div class="d-lg-none d-flex justify-content-between align-items-center p-3 fixed-top bg-white shadow-sm"
    style="z-index:1040;">
    <div class="d-flex align-items-center gap-2">
        <img src="../assets/images/DSNC.png" alt="DNSC Logo" style="width:36px;height:36px;object-fit:contain;">
        <div>
            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;">DNSC NSTP</h6>
            <small class="text-muted" style="font-size:0.7rem;">Admin Console</small>
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

    <!-- Brand -->
    <div class="sidebar-brand-wrap">
        <img src="../assets/images/DSNC.png" alt="DNSC Logo" class="brand-logo" style="width:40px;height:40px;object-fit:contain;flex-shrink:0;">
        <div class="brand-text">
            <h5>DNSC NSTP</h5>
            <small>Admin Console</small>
        </div>
    </div>

    <div class="flex-grow-1 mb-auto mt-3">
        <p class="sidebar-section-label">ADMINISTRATION</p>
        <ul class="nav flex-column px-2">
            <li class="nav-item">
                <a href="accounts.php" class="nav-link <?= $page_slug === 'accounts' ? 'active' : '' ?>" style="<?= $page_slug === 'accounts' ? 'background: #8B5CF6; color: white; border-radius: 8px;' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> Accounts
                </a>
            </li>
        </ul>
    </div>

    <!-- User Info + Sign Out -->
    <?php
    $fullName = $_SESSION['full_name'] ?? 'System Administrator';
    $nameParts = explode(' ', str_replace(['Dr. ', 'Prof. '], '', $fullName));
    $initials = count($nameParts) > 1 
        ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
        : strtoupper(substr($fullName, 0, 2));
    ?>
    <div class="mt-auto p-3 border-top d-flex align-items-center justify-content-between" style="border-color: #E5E7EB !important;">
        <div class="d-flex align-items-center gap-2" style="min-width: 0;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #9333EA; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.95rem; flex-shrink: 0;">
                SA
            </div>
            <div class="user-info-text" style="min-width: 0;">
                <div class="text-truncate" style="font-size: 0.85rem; font-weight: 500; color: #111827; line-height: 1.2; max-width: 140px;"><?= htmlspecialchars($fullName) ?></div>
                <div class="text-truncate" style="font-size: 0.7rem; color: #6B7280; margin-top: 2px; max-width: 140px;">System Administrator</div>
            </div>
        </div>
        <a href="../logout.php" class="logout-btn text-muted text-decoration-none d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-box-arrow-right" style="font-size: 1.1rem; color: #9CA3AF;"></i>
        </a>
    </div>
</div>
