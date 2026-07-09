<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/CalendarController.php';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">

    <?php include '../includes/topbar.php'; ?>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Activity Overview</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Plan, draft, and publish program-wide activities</p>
        </div>
        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: white; color: #1E293B; border: 1px solid #E2E8F0; border-radius: 8px; padding: 8px 16px; font-weight: 500;" data-bs-toggle="collapse" data-bs-target="#addActivityPanel" aria-expanded="false" aria-controls="addActivityPanel">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create Activity
        </button>
    </div>

    <!-- ══════════════════════════════════════════════════
         ADD ACTIVITY INLINE PANEL
    ══════════════════════════════════════════════════ -->
    <div class="collapse mb-4" id="addActivityPanel">
        <div class="dash-panel p-4" style="border-radius: 12px; position: relative;">
            <button type="button" class="btn-close position-absolute" data-bs-toggle="collapse" data-bs-target="#addActivityPanel" style="top: 24px; right: 24px; font-size: 0.75rem;"></button>
            
            <div class="mb-4 border-bottom pb-3" style="border-color: #E2E8F0 !important;">
                <h5 class="fw-bold mb-1" style="color: #111827; font-size: 1.1rem;">New Activity</h5>
                <p class="text-muted small mb-0">Fill in the details and submit for publication</p>
            </div>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Title</label>
                    <input type="text" name="title" class="form-control figma-input p-2" placeholder="e.g. Commencement Rehearsal" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Date</label>
                        <input type="date" name="activity_date" class="form-control figma-input p-2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Time</label>
                        <input type="time" name="activity_time" class="form-control figma-input p-2" required>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Venue</label>
                        <input type="text" name="location" class="form-control figma-input p-2" placeholder="e.g. Main Auditorium" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Audience</label>
                        <select name="component" class="form-select figma-input p-2" required>
                            <option value="All Programs">All Programs</option>
                            <option value="CWTS">CWTS</option>
                            <option value="LTS">LTS</option>
                            <option value="ROTC">ROTC</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Description</label>
                    <textarea name="description" class="form-control figma-input p-2" rows="3" placeholder="Briefly describe the activity, objectives, and requirements."></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm" data-bs-toggle="collapse" data-bs-target="#addActivityPanel" style="background: white; color: #475569; border: 1px solid #E2E8F0; border-radius: 8px; padding: 8px 16px; font-weight: 500;">Cancel</button>
                    <button type="submit" name="add_activity" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Create Activity
                    </button>
                </div>
            </form>
        </div>
    </div>



    <!-- ══════════════════════════════════════════════════
         WEEK STRIP VIEW
    ══════════════════════════════════════════════════ -->
    <div class="dash-panel mb-4" style="padding: 24px; border-radius: 12px;">

        <!-- Week Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="fw-medium" style="font-size: 0.95rem; color: #4B5563;">
                Week of <?= $weekStart->format('M j') ?> – <?= $weekEnd->format('M j, Y') ?>
            </span>
            <div class="d-flex gap-2">
                <button class="btn btn-sm" id="prevWeekBtn" style="background: white; border: 1px solid #E2E8F0; border-radius: 6px; padding: 4px 8px; color: #64748B;">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="btn btn-sm" id="nextWeekBtn" style="background: white; border: 1px solid #E2E8F0; border-radius: 6px; padding: 4px 8px; color: #64748B;">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- 7-Day Grid -->
        <div class="week-strip">
            <?php foreach ($week_days as $wd): ?>
                <div class="week-col <?= $wd['is_today'] ? 'is-today' : '' ?>">
                    <div class="week-col-header">
                        <div class="week-day-abbr"><?= $wd['day_abbr'] ?></div>
                        <div class="week-day-num <?= $wd['is_today'] ? 'today-badge' : '' ?>">
                            <?= $wd['day_num'] ?>
                        </div>
                    </div>
                    <div class="week-col-body">
                        <?php foreach ($wd['activities'] as $act):
                            $c = componentColor($act['component']);
                        ?>
                            <div class="week-event-pill" style="background:<?= $c['bg'] ?>; color:<?= $c['text'] ?>;"
                                 title="<?= htmlspecialchars($act['title']) ?> — <?= date('h:i A', strtotime($act['activity_time'])) ?>">
                                <?= htmlspecialchars($act['title']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         ALL ACTIVITIES LIST
    ══════════════════════════════════════════════════ -->
    <div class="dash-panel" style="padding: 0; overflow: hidden; border-radius: 12px;">

        <!-- List Header -->
        <div class="px-4 py-3 border-bottom" style="border-color: #E2E8F0 !important;">
            <h6 class="fw-bold mb-1" style="font-size: 0.95rem; color: #1E293B;">All Activities</h6>
            <p class="mb-0" style="font-size: 0.8rem; color: #64748B;">
                <?= count($upcoming_activities) ?> scheduled
            </p>
        </div>

        <!-- Activity Rows -->
        <?php if (count($upcoming_activities) > 0): ?>
            <?php foreach ($upcoming_activities as $idx => $act):
                $c = componentColor($act['component']);
                $isLast = ($idx === count($upcoming_activities) - 1);
                $isPast = (strtotime($act['activity_date']) < strtotime(date('Y-m-d')));
            ?>
            <div class="act-row <?= !$isLast ? 'border-bottom' : '' ?>"
                 style="border-color: var(--border-color) !important;">

                <!-- Colour Bar -->
                <div class="act-color-bar" style="background: <?= $c['bar'] ?>;"></div>

                <!-- Calendar Icon -->
                <div class="act-icon-wrap">
                    <i class="bi bi-calendar-event" style="color: var(--text-muted); font-size: 1rem;"></i>
                </div>

                <!-- Content -->
                <div class="act-content">
                    <div class="act-title" style="color: #1E293B; font-weight: 500; font-size: 0.9rem; margin-bottom: 2px;"><?= htmlspecialchars($act['title']) ?></div>
                    <div class="act-meta" style="color: #64748B; font-size: 0.75rem;">
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 12px; height: 12px; margin-right: 4px; margin-bottom: 2px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><span style="margin-right: 4px;"><?= date('M d, Y', strtotime($act['activity_date'])) ?></span></span>
                        <span class="act-meta-dot" style="margin: 0 4px;">·</span>
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 12px; height: 12px; margin-right: 4px; margin-bottom: 2px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span style="margin-right: 4px;">
                            <?php
                                $t = strtotime($act['activity_time']);
                                echo ($t !== false && $act['activity_time'] !== '00:00:00')
                                    ? date('h:i A', $t)
                                    : 'All day';
                            ?>
                        </span></span>
                        <span class="act-meta-dot" style="margin: 0 4px;">·</span>
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 12px; height: 12px; margin-right: 4px; margin-bottom: 2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><span style="margin-right: 4px;"><?= htmlspecialchars($act['location']) ?></span></span>
                    </div>
                </div>

                <!-- Tags -->
                <div class="act-tags">
                    <span style="font-size: 0.75rem; color: #6366F1; font-weight: 500; margin-right: 12px;">
                        <?= htmlspecialchars($act['component']) ?>
                    </span>
                    <?php if ($isPast): ?>
                        <span style="font-size: 0.7rem; padding: 4px 10px; border-radius: 4px; background: #F1F5F9; color: #475569; font-weight: 500;">Draft</span>
                    <?php else: ?>
                        <span style="font-size: 0.7rem; padding: 4px 10px; border-radius: 4px; background: #ECFDF5; color: #10B981; font-weight: 500;">Submitted</span>
                    <?php endif; ?>
                </div>

                <!-- Actions Dropdown -->
                <div class="dropdown ms-3">
                    <button class="btn btn-sm text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: white; border: 1px solid transparent; border-radius: 6px; padding: 4px 8px;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 8px; font-size: 0.85rem; border: 1px solid #E2E8F0;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#editActivityModal<?= $act['id'] ?>" style="color: #475569;">
                                <i class="bi bi-pencil" style="font-size: 0.9rem;"></i> Edit Activity
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="" id="form-delete-<?= $act['id'] ?>" class="m-0 p-0">
                                <input type="hidden" name="activity_id" value="<?= $act['id'] ?>">
                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger" onclick="confirmDeleteActivity('form-delete-<?= $act['id'] ?>')">
                                    <i class="bi bi-trash" style="font-size: 0.9rem;"></i> Delete Activity
                                </button>
                                <input type="submit" name="delete_activity" class="d-none">
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Edit Activity Modal -->
            <div class="modal fade" id="editActivityModal<?= $act['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 12px; border: none;">
                        <div class="modal-header border-bottom" style="border-color: #E2E8F0 !important;">
                            <h5 class="modal-title fw-bold" style="color: #111827; font-size: 1.1rem;">Edit Activity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.75rem;"></button>
                        </div>
                        <form method="POST" action="">
                            <div class="modal-body p-4">
                                <input type="hidden" name="activity_id" value="<?= $act['id'] ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Title</label>
                                    <input type="text" name="title" class="form-control figma-input p-2" value="<?= htmlspecialchars($act['title']) ?>" required>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Date</label>
                                        <input type="date" name="activity_date" class="form-control figma-input p-2" value="<?= $act['activity_date'] ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Time</label>
                                        <input type="time" name="activity_time" class="form-control figma-input p-2" value="<?= $act['activity_time'] ?>" required>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Venue</label>
                                        <input type="text" name="location" class="form-control figma-input p-2" value="<?= htmlspecialchars($act['location']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Audience</label>
                                        <select name="component" class="form-select figma-input p-2" required>
                                            <option value="All Programs" <?= $act['component'] == 'All Programs' ? 'selected' : '' ?>>All Programs</option>
                                            <option value="CWTS" <?= $act['component'] == 'CWTS' ? 'selected' : '' ?>>CWTS</option>
                                            <option value="LTS" <?= $act['component'] == 'LTS' ? 'selected' : '' ?>>LTS</option>
                                            <option value="ROTC" <?= $act['component'] == 'ROTC' ? 'selected' : '' ?>>ROTC</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label fw-medium text-muted" style="font-size: 0.8rem; margin-bottom: 4px;">Description</label>
                                    <textarea name="description" class="form-control figma-input p-2" rows="3"><?= htmlspecialchars($act['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top p-3" style="border-color: #E2E8F0 !important;">
                                <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="background: white; color: #475569; border: 1px solid #E2E8F0; border-radius: 8px; padding: 8px 16px; font-weight: 500;">Cancel</button>
                                <button type="submit" name="edit_activity" class="btn btn-sm" style="background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500;">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5 px-4">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--bg-light);
                            display: flex; align-items: center; justify-content: center;
                            margin: 0 auto 12px; color: var(--text-muted); font-size: 1.3rem;">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <p class="fw-semibold mb-1" style="color: var(--text-dark); font-size: 0.9rem;">No activities yet</p>
                <p class="text-muted small mb-3">Schedule your first program-wide activity.</p>
                <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500;" data-bs-toggle="collapse" data-bs-target="#addActivityPanel" aria-expanded="false" aria-controls="addActivityPanel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Create Activity
                </button>
            </div>
        <?php endif; ?>
    </div>

</div>



</div>

<style>
/* ══════════════════════════════════════════
   CALENDAR PAGE — Figma-match styles
══════════════════════════════════════════ */

/* Nav buttons */
.cal-nav-btn {
    width: 32px; height: 32px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: #fff;
    color: var(--text-muted);
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all 0.15s;
    font-size: 0.85rem;
}
.cal-nav-btn:hover {
    background: var(--bg-light);
    color: var(--text-dark);
    border-color: var(--primary-accent);
}

/* ── Week Strip ── */
.week-strip {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 12px;
}
.week-col {
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    min-height: 120px;
    transition: border-color 0.15s;
    display: flex;
    flex-direction: column;
}
.week-col:hover { border-color: rgba(99,102,241,0.4); }

.week-col-header {
    padding: 12px 12px 4px;
    text-align: left;
    background: white;
}

.week-day-abbr {
    font-size: 0.65rem;
    font-weight: 500;
    color: #64748B;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.week-day-num {
    font-size: 1.15rem;
    font-weight: 500;
    color: #1E293B;
    line-height: 1;
}

.week-col-body {
    padding: 4px 8px 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex-grow: 1;
}

.week-event-pill {
    font-size: 0.7rem;
    font-weight: 400;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
    transition: opacity 0.15s;
    width: 100%;
}
.week-event-pill:hover { opacity: 0.8; }

/* ── Activity List Rows ── */
.act-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 24px;
    transition: background-color 0.15s;
    cursor: pointer;
}
.act-row:hover { background-color: #F8FAFC; }

.act-color-bar {
    width: 4px;
    height: 32px;
    border-radius: 4px;
    flex-shrink: 0;
}

.act-icon-wrap {
    width: 36px; height: 36px;
    border-radius: 8px;
    background: #F8FAFC;
    border: 1px solid #F1F5F9;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.act-content {
    flex: 1;
    min-width: 0;
}
.act-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--primary-accent);
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.act-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    font-size: 0.75rem;
    color: var(--text-muted);
}
.act-meta i { font-size: 0.7rem; }
.act-meta-dot { color: var(--border-color); font-weight: 700; }

.act-tags {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.act-tag {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 6px;
}

/* Modal inputs */
.figma-input {
    border-radius: 8px !important;
    border-color: var(--border-color) !important;
    font-size: 0.875rem;
}
.figma-input:focus {
    border-color: var(--primary-accent) !important;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1) !important;
}

/* Responsive: collapse week strip on small screens */
@media (max-width: 768px) {
    .week-strip { grid-template-columns: repeat(4, 1fr); }
}
@media (max-width: 480px) {
    .week-strip { grid-template-columns: repeat(2, 1fr); }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Simple week navigation via URL params (page reload)
document.getElementById('prevWeekBtn').addEventListener('click', () => {
    const url = new URL(window.location.href);
    const offset = parseInt(url.searchParams.get('weekOffset') || '0') - 1;
    url.searchParams.set('weekOffset', offset);
    window.location.href = url.toString();
});
document.getElementById('nextWeekBtn').addEventListener('click', () => {
    const url = new URL(window.location.href);
    const offset = parseInt(url.searchParams.get('weekOffset') || '0') + 1;
    url.searchParams.set('weekOffset', offset);
    window.location.href = url.toString();
});

function confirmDeleteActivity(formId) {
    Swal.fire({
        title: 'Delete Activity?',
        text: "Are you sure you want to delete this activity? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}
</script>
</body>
</html>
