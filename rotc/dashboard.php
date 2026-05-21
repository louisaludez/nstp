<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROTC') {
    header("Location: ../login.php");
    exit;
}

$officer_id   = $_SESSION['user_id'];
$officer_name = $_SESSION['full_name'] ?? 'ROTC Officer';
$nameParts    = explode(' ', $officer_name);
$lastName     = end($nameParts);

// ── Stats ─────────────────────────────────────────────────────────────────────

// Total ROTC cadets enrolled
try {
    $s = $pdo->prepare("SELECT COUNT(DISTINCT e.student_id) FROM enrollments e JOIN sections s ON e.section_id=s.id WHERE s.component='ROTC'");
    $s->execute(); $total_cadets = $s->fetchColumn() ?: 0;
} catch (Exception $e) { $total_cadets = 0; }

// Active platoons
try {
    $s = $pdo->prepare("SELECT COUNT(*) FROM sections WHERE component='ROTC'");
    $s->execute(); $active_platoons = $s->fetchColumn() ?: 0;
} catch (Exception $e) { $active_platoons = 0; }

// Platoon names
try {
    $s = $pdo->prepare("SELECT section_name FROM sections WHERE component='ROTC' ORDER BY id LIMIT 3");
    $s->execute(); $pnames = array_column($s->fetchAll(), 'section_name');
    $platoon_names = count($pnames) ? implode(' · ', $pnames) : 'None assigned';
} catch (Exception $e) { $platoon_names = 'None assigned'; }

// Unassigned ROTC students (enrolled in ROTC section with no section assignment actually; simplified: students with component=ROTC not in any enrollment)
try {
    $s = $pdo->prepare("SELECT COUNT(*) FROM students st WHERE st.component='ROTC' AND st.student_id NOT IN (SELECT e.student_id FROM enrollments e JOIN sections sec ON e.section_id=sec.id WHERE sec.component='ROTC')");
    $s->execute(); $unassigned = $s->fetchColumn() ?: 0;
} catch (Exception $e) { $unassigned = 0; }

// Reports open (pending activity plans)
try {
    $s = $pdo->prepare("SELECT COUNT(*) FROM activity_plans WHERE status='Pending'");
    $s->execute(); $reports_open = $s->fetchColumn() ?: 0;
} catch (Exception $e) { $reports_open = 0; }

// ── Recent Accomplishment Reports ─────────────────────────────────────────────
try {
    $s = $pdo->prepare("SELECT ar.*, u.full_name AS instructor_name FROM accomplishment_reports ar LEFT JOIN users u ON ar.instructor_id=u.id WHERE ar.instructor_id=? ORDER BY ar.submitted_date DESC LIMIT 5");
    $s->execute([$officer_id]); $reports = $s->fetchAll();
} catch (Exception $e) { $reports = []; }

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/rotc_sidebar.php';
?>

<div class="flex-grow-1 w-100" style="background:#F8FAFC;min-height:100vh;">

    <!-- ── Topbar ── -->
    <?php include '../includes/topbar.php'; ?>

    <div class="p-4 p-lg-5" style="padding-top:88px !important;">

        <!-- ── Page Header ── -->


        <!-- ── Stat Cards ── -->
        <div class="row g-3 mb-4">

            <!-- Total Cadets -->
            <div class="col-sm-6 col-xl-3">
                <div style="background:#fff;border:1px solid #E2E8F0;border-radius:12px;padding:20px 22px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                    <div style="font-size:0.68rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#94A3B8;margin-bottom:8px;">Total Cadets</div>
                    <div style="font-size:2rem;font-weight:700;color:#0F172A;line-height:1;"><?= number_format($total_cadets) ?></div>
                    <div style="font-size:0.75rem;color:#64748B;margin-top:4px;">+12 this intake</div>
                </div>
            </div>

            <!-- Active Platoons -->
            <div class="col-sm-6 col-xl-3">
                <div style="background:#fff;border:1px solid #E2E8F0;border-radius:12px;padding:20px 22px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                    <div style="font-size:0.68rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#94A3B8;margin-bottom:8px;">Active Platoons</div>
                    <div style="font-size:2rem;font-weight:700;color:#0F172A;line-height:1;"><?= $active_platoons ?></div>
                    <div style="font-size:0.75rem;color:#64748B;margin-top:4px;">Alpha · Bravo · Charlie</div>
                </div>
            </div>

            <!-- Total Officer in Charge -->
            <div class="col-sm-6 col-xl-3">
                <div style="background:#fff;border:1px solid #E2E8F0;border-radius:12px;padding:20px 22px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                    <div style="font-size:0.68rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#94A3B8;margin-bottom:8px;">Total Officer in Charge</div>
                    <div style="font-size:2rem;font-weight:700;color:#0F172A;line-height:1;">6</div>
                    <div style="font-size:0.75rem;color:#64748B;margin-top:4px;">Alpha · Bravo · Charlie</div>
                </div>
            </div>

            <!-- Reports Open -->
            <div class="col-sm-6 col-xl-3">
                <div style="background:#fff;border:1px solid #E2E8F0;border-radius:12px;padding:20px 22px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                    <div style="font-size:0.68rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#94A3B8;margin-bottom:8px;">Reports Open</div>
                    <div style="font-size:2rem;font-weight:700;color:#0F172A;line-height:1;"><?= $reports_open ?></div>
                    <div style="font-size:0.75rem;color:#64748B;margin-top:4px;">2 due this week</div>
                </div>
            </div>
        </div>

        <!-- ── Main Content Row ── -->
        <div class="row g-4">

            <!-- Left: Accomplishment Reports list -->
            <div class="col-lg-8">
                <div style="background:#fff;border:1px solid #E2E8F0;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.04);overflow:hidden;">
                    <!-- Panel Header -->
                    <div style="padding:20px 24px 14px;border-bottom:1px solid #F8FAFC;">
                        <div style="font-size:0.65rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#94A3B8;margin-bottom:2px;">Documentation</div>
                        <div style="font-size:1rem;font-weight:700;color:#0F172A;">Accomplishment Reports</div>
                    </div>

                    <!-- Report Items -->
                    <div style="padding:4px 0;">
                        <?php
                        if (count($reports) === 0): ?>
                            <div style="padding:20px 24px; text-align:center; color:#94A3B8; font-size:0.85rem;">
                                No recent accomplishment reports found.
                            </div>
                        <?php else:
                            $statusConfig = [
                                'Draft'        => ['label'=>'Draft',        'color'=>'#4B5563','bg'=>'transparent','barColor'=>'#94A3B8'],
                                'Pending'      => ['label'=>'Under Review', 'color'=>'#6366F1','bg'=>'#EEF2FF',   'barColor'=>'#6366F1'],
                                'Reviewed'     => ['label'=>'Approved',     'color'=>'#10B981','bg'=>'#ECFDF5',   'barColor'=>'#10B981'],
                                'Revision'     => ['label'=>'Revisions',    'color'=>'#EF4444','bg'=>'#FEF2F2',   'barColor'=>'#EF4444'],
                            ];

                            foreach ($reports as $i => $rep):
                                $status = $rep['status'];
                                $cfg = $statusConfig[$status] ?? $statusConfig['Draft'];
                                $dateLabel = 'Submitted ' . date('M j', strtotime($rep['submitted_date'] ?? $rep['completed_date']));
                                
                                if ($status === 'Reviewed') $prog = 100;
                                elseif ($status === 'Pending') $prog = 75;
                                elseif ($status === 'Revision') $prog = 50;
                                else $prog = 25;
                        ?>
                        <div style="padding:16px 24px;border-bottom:1px solid #F8FAFC;">
                            <!-- Top row: Icon, text, badge -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex gap-3">
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #9CA3AF; flex-shrink: 0;">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div>
                                        <a href="accomplishment_reports.php" style="font-size:0.9rem;font-weight:500;color:#1E293B;text-decoration:none;">
                                            <?= htmlspecialchars($rep['title']) ?>
                                        </a>
                                        <div style="font-size:0.75rem;color:#94A3B8;margin-top:2px;"><?= $dateLabel ?></div>
                                    </div>
                                </div>
                                <span style="font-size:0.7rem;font-weight:500;padding:4px 12px;border-radius:20px;background:<?= $cfg['bg'] ?>;color:<?= $cfg['color'] ?>;white-space:nowrap;display:flex;align-items:center;gap:4px; border: <?= $cfg['bg'] === 'transparent' ? '1px solid #E5E7EB' : 'none' ?>;">
                                    <?php if ($cfg['label'] === 'Draft'): ?><i class="bi bi-pencil" style="font-size:0.65rem;"></i><?php endif; ?>
                                    <?php if ($cfg['label'] === 'Under Review'): ?><i class="bi bi-send" style="font-size:0.65rem;"></i><?php endif; ?>
                                    <?php if ($cfg['label'] === 'Approved'): ?><i class="bi bi-check-circle" style="font-size:0.65rem;"></i><?php endif; ?>
                                    <?php if ($cfg['label'] === 'Revisions'): ?><i class="bi bi-exclamation-circle" style="font-size:0.65rem;"></i><?php endif; ?>
                                    <?= $cfg['label'] ?>
                                </span>
                            </div>
                            <!-- Progress bar spanning width -->
                            <div class="d-flex align-items-center gap-3">
                                <div style="flex:1;height:4px;background:#F1F5F9;border-radius:10px;overflow:hidden;">
                                    <div style="height:100%;width:<?= $prog ?>%;background:<?= $cfg['barColor'] ?>;border-radius:10px;"></div>
                                </div>
                                <span style="font-size:0.7rem;font-weight:500;color:#64748B;width:24px;text-align:right;"><?= $prog ?>%</span>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Calendar of Activities -->
            <div class="col-lg-4">
                <div style="background:#fff;border:1px solid #E2E8F0;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.04);padding:20px 24px;">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h6 style="font-weight:700;color:#0F172A;margin-bottom:2px;">Calendar of Activities</h6>
                            <div style="font-size:0.8rem;color:#64748B;"><?= date('F Y') ?></div>
                        </div>
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #EEF2FF; color: #6366F1; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                            <i class="bi bi-calendar"></i>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; text-align: center; margin-bottom: 8px;">
                        <div style="font-size:0.65rem;color:#94A3B8;font-weight:600;text-transform:uppercase;">Su</div>
                        <div style="font-size:0.65rem;color:#94A3B8;font-weight:600;text-transform:uppercase;">Mo</div>
                        <div style="font-size:0.65rem;color:#94A3B8;font-weight:600;text-transform:uppercase;">Tu</div>
                        <div style="font-size:0.65rem;color:#94A3B8;font-weight:600;text-transform:uppercase;">We</div>
                        <div style="font-size:0.65rem;color:#94A3B8;font-weight:600;text-transform:uppercase;">Th</div>
                        <div style="font-size:0.65rem;color:#94A3B8;font-weight:600;text-transform:uppercase;">Fr</div>
                        <div style="font-size:0.65rem;color:#94A3B8;font-weight:600;text-transform:uppercase;">Sa</div>
                    </div>
                    
                    <?php
                    // Dynamic Calendar Logic
                    $year = date('Y');
                    $month = date('n');
                    $daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
                    $firstDayOfWeek = date('w', mktime(0, 0, 0, $month, 1, $year));
                    
                    // Fetch activities for ROTC this month
                    $activeDays = [];
                    try {
                        $st = $pdo->prepare("SELECT DAY(activity_date) as d FROM activities WHERE component='ROTC' AND MONTH(activity_date)=? AND YEAR(activity_date)=?");
                        $st->execute([$month, $year]);
                        $activeDays = $st->fetchAll(PDO::FETCH_COLUMN);
                        
                        $st = $pdo->prepare("SELECT DAY(scheduled_date) as d FROM activity_plans WHERE instructor_id=? AND MONTH(scheduled_date)=? AND YEAR(scheduled_date)=?");
                        $st->execute([$officer_id, $month, $year]);
                        $activeDays = array_merge($activeDays, $st->fetchAll(PDO::FETCH_COLUMN));
                        $activeDays = array_unique($activeDays);
                    } catch (Exception $e) {}

                    $currentDay = date('j');
                    $weeks = [];
                    $dayCounter = 1;
                    
                    for ($row = 0; $row < 6; $row++) {
                        $week = [];
                        for ($col = 0; $col < 7; $col++) {
                            if ($row === 0 && $col < $firstDayOfWeek) {
                                $week[] = '';
                            } elseif ($dayCounter <= $daysInMonth) {
                                $week[] = (string)$dayCounter;
                                $dayCounter++;
                            } else {
                                $week[] = '';
                            }
                        }
                        $weeks[] = $week;
                        if ($dayCounter > $daysInMonth) break;
                    }
                    ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; row-gap: 16px; text-align: center; margin-bottom: 24px;">
                        <?php foreach($weeks as $week): ?>
                            <?php foreach($week as $day): 
                                $isCurrent = ($day === $currentDay);
                                $isActive = in_array($day, $activeDays);
                                
                                $style = "width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 500; color: #1E293B; margin: 0 auto; border-radius: 50%;";
                                if ($isCurrent) {
                                    $style .= " background: #6366F1; color: white;";
                                } elseif ($isActive) {
                                    $style .= " border: 1px solid #6366F1; color: #6366F1;";
                                }
                            ?>
                                <div>
                                    <?php if($day): ?>
                                        <div style="<?= $style ?>"><?= $day ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="d-flex align-items-center gap-4" style="font-size:0.75rem;color:#64748B;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #6366F1;"></div> Today
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 8px; height: 8px; border-radius: 50%; border: 1px solid #6366F1;"></div> Has activity
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /row -->
    </div><!-- /padding -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
