<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_slug = str_replace('.php', '', $current_page);

// Count ROTC platoons
$platoon_count = 0;
try {
    $sec_stmt = $pdo->prepare("SELECT COUNT(*) FROM sections WHERE component = 'ROTC'");
    $sec_stmt->execute();
    $platoon_count = $sec_stmt->fetchColumn() ?: 0;
} catch (Exception $e) {}

// Count pending activity plans
$activity_count = 0;
try {
    $act_stmt = $pdo->prepare("SELECT COUNT(*) FROM activity_plans WHERE status = 'Pending'");
    $act_stmt->execute();
    $activity_count = $act_stmt->fetchColumn() ?: 0;
} catch (Exception $e) {}

$fullName   = $_SESSION['full_name'] ?? 'ROTC Officer';
$nameParts  = explode(' ', $fullName);
$initials   = count($nameParts) > 1
    ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
    : strtoupper(substr($fullName, 0, 2));
// Last name for greeting
$lastName = end($nameParts);
?>

<!-- Mobile Header -->
<div class="d-lg-none d-flex justify-content-between align-items-center p-3 fixed-top shadow-sm"
     style="z-index:1040;background:#0F172A;">
    <div class="d-flex align-items-center gap-2">
        <img src="../assets/images/DSNC.png" alt="DNSC Logo" style="width:36px;height:36px;object-fit:contain;">
        <div>
            <div style="font-size:0.85rem;font-weight:700;color:#fff;line-height:1.1;">DNSC ROTC</div>
            <div style="font-size:0.62rem;font-weight:500;letter-spacing:0.08em;color:#6B7280;">OFFICER CONSOLE</div>
        </div>
    </div>
    <button class="btn btn-sm border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#rotcSidebar">
        <i class="bi bi-list fs-3" style="color:#fff;"></i>
    </button>
</div>

<!-- ── ROTC Sidebar ──────────────────────────────────────────────────── -->
<div class="offcanvas-lg offcanvas-start sidebar sidebar-rotc p-0 d-flex flex-column"
     tabindex="-1" id="rotcSidebar" style="background:#0F172A !important;">

    <!-- Mobile close -->
    <div class="offcanvas-header d-lg-none px-3 py-3"
         style="border-bottom:1px solid rgba(255,255,255,0.06);">
        <span style="font-weight:700;color:#fff;font-size:0.9rem;">Menu</span>
        <button type="button" class="btn-close btn-close-white"
                data-bs-dismiss="offcanvas" data-bs-target="#rotcSidebar"></button>
    </div>

    <!-- Brand -->
    <div class="rotc-brand" style="padding:22px 20px 18px;display:flex;align-items:center;gap:12px;">
        <img src="../assets/images/DSNC.png" alt="DNSC Logo" class="brand-logo" style="width:40px;height:40px;object-fit:contain;flex-shrink:0;">
        <div class="rotc-brand-text">
            <div style="font-size:0.9rem;font-weight:700;color:#F8FAFC;line-height:1.1;text-transform:uppercase;">DNSC ROTC</div>
            <div style="font-size:0.62rem;font-weight:600;letter-spacing:0.1em;color:#6B7280;text-transform:uppercase;">Officer Portal</div>
        </div>
    </div>

    <!-- Section label -->
    <div class="rotc-section-label" style="font-size:0.62rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#4B5563;padding:12px 20px 8px;">Command</div>

    <!-- Nav -->
    <ul class="nav flex-column mb-auto" style="padding:0 10px;gap:2px;">
        <li>
            <a href="dashboard.php"
               class="nav-link <?= $page_slug === 'dashboard' ? 'rotc-active' : 'rotc-link' ?>"
               style="border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:11px;font-size:0.875rem;font-weight:500;<?= $page_slug === 'dashboard' ? 'background:#F59E0B;color:#1C1917;font-weight:600;' : 'color:#94A3B8;' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Overview
            </a>
        </li>
        <li>
            <a href="platoon_management.php"
               class="nav-link <?= in_array($page_slug, ['platoon_management','platoons']) ? 'rotc-active' : 'rotc-link' ?>"
               style="border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:11px;font-size:0.875rem;font-weight:500;<?= in_array($page_slug, ['platoon_management','platoons']) ? 'background:#F59E0B;color:#1C1917;font-weight:600;' : 'color:#94A3B8;' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Platoon Management
                <?php if ($platoon_count > 0): ?>
                <span style="margin-left:auto;background:rgba(245,158,11,0.15);color:#F59E0B;font-size:0.65rem;font-weight:700;padding:2px 7px;border-radius:10px;"><?= $platoon_count ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="rosters.php"
               class="nav-link <?= $page_slug === 'rosters' ? 'rotc-active' : 'rotc-link' ?>"
               style="border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:11px;font-size:0.875rem;font-weight:500;<?= $page_slug === 'rosters' ? 'background:#F59E0B;color:#1C1917;font-weight:600;' : 'color:#94A3B8;' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Assign Officer Section
            </a>
        </li>
        <li>
            <a href="activity_designs.php"
               class="nav-link <?= in_array($page_slug, ['activity_designs','activity_plans']) ? 'rotc-active' : 'rotc-link' ?>"
               style="border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:11px;font-size:0.875rem;font-weight:500;<?= in_array($page_slug, ['activity_designs','activity_plans']) ? 'background:#F59E0B;color:#1C1917;font-weight:600;' : 'color:#94A3B8;' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><line x1="12" y1="11" x2="16" y2="11"/><line x1="12" y1="16" x2="16" y2="16"/></svg>
                Activity Designs
                <?php if ($activity_count > 0): ?>
                <span style="margin-left:auto;background:rgba(245,158,11,0.15);color:#F59E0B;font-size:0.65rem;font-weight:700;padding:2px 7px;border-radius:10px;"><?= $activity_count ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="accomplishment_reports.php"
               class="nav-link <?= $page_slug === 'accomplishment_reports' ? 'rotc-active' : 'rotc-link' ?>"
               style="border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:11px;font-size:0.875rem;font-weight:500;<?= $page_slug === 'accomplishment_reports' ? 'background:#F59E0B;color:#1C1917;font-weight:600;' : 'color:#94A3B8;' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg> Report Submission
            </a>
        </li>
    </ul>

    <!-- Hover + collapsed styles -->
    <style>
        #rotcSidebar .nav-link:not([style*="background:#F59E0B"]):hover {
            background: rgba(255,255,255,0.04) !important;
            color: #F8FAFC !important;
        }

        /* ── Collapsed state ── */
        html.sidebar-collapsed #rotcSidebar .rotc-brand {
            justify-content: center;
            padding: 22px 8px 18px;
        }
        html.sidebar-collapsed #rotcSidebar .rotc-brand-text,
        html.sidebar-collapsed #rotcSidebar .rotc-section-label,
        html.sidebar-collapsed #rotcSidebar .rotc-user-text,
        html.sidebar-collapsed #rotcSidebar .rotc-signout-label {
            display: none !important;
        }
        html.sidebar-collapsed #rotcSidebar .nav-link {
            justify-content: center !important;
            padding: 10px 0 !important;
            font-size: 0 !important;
            gap: 0 !important;
        }
        html.sidebar-collapsed #rotcSidebar .nav-link i {
            font-size: 1.2rem !important;
            width: auto !important;
            margin: 0 !important;
        }
        html.sidebar-collapsed #rotcSidebar .nav-link span {
            display: none !important;
        }
        html.sidebar-collapsed #rotcSidebar .rotc-user-footer {
            padding: 12px 8px;
            justify-content: center;
        }
        html.sidebar-collapsed #rotcSidebar .rotc-avatar {
            margin: 0 auto;
        }
        html.sidebar-collapsed #rotcSidebar .rotc-signout-btn {
            justify-content: center;
            padding: 9px 0 !important;
        }
    </style>

    <!-- User footer -->
    <div style="margin-top:auto;border-top:1px solid rgba(255,255,255,0.06);">
        <!-- User info -->
        <div class="rotc-user-footer" style="padding:14px 16px 10px;display:flex;align-items:center;gap:10px;">
            <div class="rotc-avatar" style="width:36px;height:36px;border-radius:50%;background:#374151;border:1px solid rgba(245,158,11,0.3);color:#F59E0B;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                <?= htmlspecialchars($initials) ?>
            </div>
            <div class="rotc-user-text" style="flex:1;min-width:0;">
                <div style="font-size:0.82rem;font-weight:600;color:#F8FAFC;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Lt. <?= htmlspecialchars($lastName) ?></div>
                <div style="font-size:0.68rem;color:#6B7280;margin-top:1px;">First Class Officer</div>
            </div>
        </div>
        <!-- Sign Out button -->
        <div style="padding:0 10px 14px;">
            <a href="../logout.php" class="rotc-signout-btn"
               style="display:flex;align-items:center;gap:9px;padding:9px 14px;border-radius:8px;text-decoration:none;color:#94A3B8;font-size:0.82rem;font-weight:500;transition:all 0.15s;border:1px solid rgba(255,255,255,0.06);"
               onmouseover="this.style.background='rgba(239,68,68,0.08)';this.style.color='#F87171';this.style.borderColor='rgba(239,68,68,0.2)';"
               onmouseout="this.style.background='transparent';this.style.color='#94A3B8';this.style.borderColor='rgba(255,255,255,0.06)';">
                <i class="bi bi-box-arrow-right" style="font-size:1rem;"></i>
                <span class="rotc-signout-label">Sign Out</span>
            </a>
        </div>
    </div>
</div>
