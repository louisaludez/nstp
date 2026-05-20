<?php
session_start(); require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROTC') { header("Location: ../login.php"); exit; }
$extra_css = ['../assets/css/style.css'];
include '../includes/header.php'; include '../includes/rotc_sidebar.php';

$upcoming = [
    ['date' => 'MAY 16', 'color' => '#F59E0B', 'title' => 'Saturday Drill — Formation 0700H', 'location' => 'Parade Grounds'],
    ['date' => 'MAY 18', 'color' => '#EF4444', 'title' => 'Q1 Accomplishment Reports Due', 'location' => 'Officer Console'],
    ['date' => 'MAY 23', 'color' => '#6366F1', 'title' => 'Tactical Inspection', 'location' => 'Field Site B'],
    ['date' => 'MAY 30', 'color' => '#10B981', 'title' => 'Civil-Military Outreach', 'location' => 'Brgy. San Pablo'],
];

$notices = [
    ['type' => 'ORDER', 'typeColor' => '#EF4444', 'pinned' => true, 'time' => 'Today', 'title' => 'General Order 2026-14 — Drill Day moved to 0700H'],
    ['type' => 'SCHEDULE', 'typeColor' => '#8B5CF6', 'pinned' => false, 'time' => '2d ago', 'title' => 'Tactical inspection — May 23 at the parade grounds'],
    ['type' => 'MEMO', 'typeColor' => '#F59E0B', 'pinned' => false, 'time' => 'May 9', 'title' => 'Submit Q1 accomplishment reports by May 18, 2359H'],
    ['type' => 'NOTICE', 'typeColor' => '#10B981', 'pinned' => false, 'time' => 'May 8', 'title' => 'New cadets to be integrated into Alpha & Bravo platoons'],
];
?>
<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background:#F8FAFC;min-height:100vh;">
    <?php include '../includes/topbar.php'; ?>
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h2 class="fw-bold mb-1" style="color:#0F172A;font-size:1.5rem;">Master Calendar</h2>
            <p class="mb-0" style="font-size:0.9rem;color:#64748B;">Drill days, inspections, and field operations across all platoons</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn d-flex align-items-center gap-2" style="background:#fff; border: 1px solid #E2E8F0; border-radius:8px; color:#475569; font-weight: 600; font-size:0.85rem; padding: 8px 16px;">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <button class="btn d-flex align-items-center gap-2" style="background:#0F172A; border: 1px solid #0F172A; border-radius:8px; color:#fff; font-weight: 600; font-size:0.85rem; padding: 8px 16px;">
                <i class="bi bi-plus-lg"></i> Add Event
            </button>
        </div>
    </div>

    <!-- Top Section: Calendar Week View -->
    <div class="card mb-4" style="border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 24px;">
        <h6 style="color:#475569; font-weight:500; font-size:1rem; margin-bottom:20px;">Week of May 14 &ndash; May 20, 2026</h6>
        <div class="d-flex gap-3">
            <!-- Day 1 -->
            <div class="flex-grow-1" style="border:1px solid #E2E8F0; border-radius:10px; min-height:120px; padding:12px;">
                <div style="font-size:0.7rem; font-weight:600; color:#94A3B8; text-transform:uppercase;">Tue</div>
                <div style="font-size:1.1rem; font-weight:600; color:#1E293B; margin-bottom:12px;">14</div>
            </div>
            <!-- Day 2 -->
            <div class="flex-grow-1" style="border:1px solid #E2E8F0; border-radius:10px; min-height:120px; padding:12px;">
                <div style="font-size:0.7rem; font-weight:600; color:#94A3B8; text-transform:uppercase;">Wed</div>
                <div style="font-size:1.1rem; font-weight:600; color:#1E293B; margin-bottom:12px;">15</div>
                <div style="background:#6366F1; color:#fff; font-size:0.7rem; font-weight:500; padding:4px 8px; border-radius:4px; margin-bottom:4px;">Section briefing</div>
            </div>
            <!-- Day 3 -->
            <div class="flex-grow-1" style="border:1px solid #E2E8F0; border-radius:10px; min-height:120px; padding:12px;">
                <div style="font-size:0.7rem; font-weight:600; color:#94A3B8; text-transform:uppercase;">Thu</div>
                <div style="font-size:1.1rem; font-weight:600; color:#1E293B; margin-bottom:12px;">16</div>
                <div style="background:#F59E0B; color:#fff; font-size:0.7rem; font-weight:500; padding:4px 8px; border-radius:4px; margin-bottom:4px;">Drill day 0700H</div>
            </div>
            <!-- Day 4 -->
            <div class="flex-grow-1" style="border:1px solid #E2E8F0; border-radius:10px; min-height:120px; padding:12px;">
                <div style="font-size:0.7rem; font-weight:600; color:#94A3B8; text-transform:uppercase;">Fri</div>
                <div style="font-size:1.1rem; font-weight:600; color:#1E293B; margin-bottom:12px;">17</div>
            </div>
            <!-- Day 5 -->
            <div class="flex-grow-1" style="border:1px solid #E2E8F0; border-radius:10px; min-height:120px; padding:12px;">
                <div style="font-size:0.7rem; font-weight:600; color:#94A3B8; text-transform:uppercase;">Sat</div>
                <div style="font-size:1.1rem; font-weight:600; color:#1E293B; margin-bottom:12px;">18</div>
                <div style="background:#EF4444; color:#fff; font-size:0.7rem; font-weight:500; padding:4px 8px; border-radius:4px; margin-bottom:4px;">Q1 Reports due</div>
            </div>
            <!-- Day 6 -->
            <div class="flex-grow-1" style="border:1px solid #E2E8F0; border-radius:10px; min-height:120px; padding:12px;">
                <div style="font-size:0.7rem; font-weight:600; color:#94A3B8; text-transform:uppercase;">Sun</div>
                <div style="font-size:1.1rem; font-weight:600; color:#1E293B; margin-bottom:12px;">19</div>
            </div>
            <!-- Day 7 -->
            <div class="flex-grow-1" style="border:1px solid #E2E8F0; border-radius:10px; min-height:120px; padding:12px;">
                <div style="font-size:0.7rem; font-weight:600; color:#94A3B8; text-transform:uppercase;">Mon</div>
                <div style="font-size:1.1rem; font-weight:600; color:#1E293B; margin-bottom:12px;">20</div>
                <div style="background:#10B981; color:#fff; font-size:0.7rem; font-weight:500; padding:4px 8px; border-radius:4px; margin-bottom:4px;">Strength report</div>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <!-- Upcoming Events -->
        <div class="col-lg-7">
            <div class="card" style="border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 0;">
                <div class="card-header bg-white" style="border-bottom: 1px solid #E2E8F0; padding: 16px 24px; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h6 class="mb-0" style="color:#374151; font-weight:600;">Upcoming</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach($upcoming as $event): ?>
                        <div class="list-group-item" style="padding: 20px 24px; border-bottom: 1px solid #F1F5F9; background: #fff;">
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-column align-items-center justify-content-center me-3" style="min-width: 40px;">
                                    <div style="font-size:0.65rem; color:#94A3B8; font-weight:700; text-transform:uppercase; line-height:1;">MAY</div>
                                    <div style="font-size:1.1rem; color:#1E293B; font-weight:600; line-height:1.2;"><?= explode(' ', $event['date'])[1] ?></div>
                                </div>
                                <div style="width: 3px; height: 32px; background: <?= $event['color'] ?>; border-radius: 2px; margin-right: 16px;"></div>
                                <div class="flex-grow-1">
                                    <div style="font-size:0.95rem; font-weight:500; color:#1E293B; margin-bottom:2px;"><?= htmlspecialchars($event['title']) ?></div>
                                    <div style="font-size:0.8rem; color:#64748B;"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($event['location']) ?></div>
                                </div>
                                <i class="bi bi-chevron-right text-muted" style="font-size:0.8rem;"></i>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulletin Board -->
        <div class="col-lg-5">
            <div class="card" style="border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 0;">
                <div class="card-header bg-white" style="border-bottom: 1px solid #E2E8F0; padding: 20px 24px; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px; height:40px; border-radius:8px; background:#0F172A; color:#F59E0B; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                            <i class="bi bi-megaphone"></i>
                        </div>
                        <div>
                            <div style="font-size:0.65rem; font-weight:700; letter-spacing:0.05em; color:#64748B;">BULLETIN BOARD</div>
                            <h6 class="mb-0" style="color:#1E293B; font-weight:500;">Official Notices</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach($notices as $notice): 
                            // Using standard rgba color approach for light pill backgrounds
                            $bgColor = $notice['typeColor'] . '15'; // Very basic hex alpha for light bg
                        ?>
                        <div class="list-group-item" style="padding: 16px 24px; border-bottom: 1px solid #F1F5F9; background: #fff;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size:0.65rem; font-weight:600; padding:2px 8px; border-radius:4px; color:<?= $notice['typeColor'] ?>; background:<?= $bgColor ?>;"><?= htmlspecialchars($notice['type']) ?></span>
                                    <?php if($notice['pinned']): ?>
                                    <span style="font-size:0.7rem; color:#94A3B8;"><i class="bi bi-pin-angle-fill me-1"></i>PINNED</span>
                                    <?php endif; ?>
                                </div>
                                <span style="font-size:0.75rem; color:#94A3B8;"><?= htmlspecialchars($notice['time']) ?></span>
                            </div>
                            <div style="font-size:0.85rem; color:#334155; line-height:1.4;"><?= htmlspecialchars($notice['title']) ?></div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Extra padding item matching screenshot -->
                        <div class="list-group-item" style="padding: 16px 24px; border-bottom: none; background: #fff;">
                            <div style="font-size:0.65rem; font-weight:700; letter-spacing:0.05em; color:#94A3B8; margin-top:10px;">NEXT FORMATION</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<style>
/* Basic utility for the alpha colors we appended */
.list-group-item span[style*="#EF444415"] { background: #FEE2E2 !important; }
.list-group-item span[style*="#8B5CF615"] { background: #EDE9FE !important; }
.list-group-item span[style*="#F59E0B15"] { background: #FEF3C7 !important; }
.list-group-item span[style*="#10B98115"] { background: #D1FAE5 !important; }
</style>
</body></html>
