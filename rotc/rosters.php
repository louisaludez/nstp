<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROTC') {
    header("Location: ../login.php");
    exit;
}

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/rotc_sidebar.php';

// Filter and Search logic
$search = $_GET['search'] ?? '';
$platoon_filter = $_GET['platoon'] ?? 'All Platoons';

$query = "
    SELECT s.*, sec.section_name AS platoon, sec.semester AS status
    FROM students s
    JOIN enrollments e ON s.student_id = e.student_id
    JOIN sections sec ON e.section_id = sec.id
    WHERE s.component = 'ROTC'
";
$params = [];

if ($search !== '') {
    $query .= " AND (s.student_id LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if ($platoon_filter !== 'All Platoons' && $platoon_filter !== '') {
    $query .= " AND sec.section_name = ?";
    $params[] = $platoon_filter;
}

$query .= " ORDER BY s.last_name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$officers = $stmt->fetchAll();

// Fetch all active platoons for the filter dropdown
$pStmt = $pdo->prepare("SELECT section_name FROM sections WHERE component='ROTC' ORDER BY section_name");
$pStmt->execute();
$platoonList = $pStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../includes/topbar.php'; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h5 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.15rem;">Assign Officer Section Overview</h5>
            <div class="text-muted" style="font-size: 0.85rem;">Master list of officers with rank, platoon, and specialty</div>
        </div>
        <div class="d-flex gap-3">
            <button class="btn" style="background: white; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: #475569; padding: 8px 16px; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                <i class="bi bi-download"></i> Export
            </button>
            <button class="btn" data-bs-toggle="modal" data-bs-target="#addOfficerModal" style="background: #0F172A; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: white; padding: 8px 16px; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-person-plus"></i> Add Officer
            </button>
        </div>
    </div>

    <!-- Main Panel -->
    <div style="background: white; border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
        <!-- Filters -->
        <form method="GET" action="rosters.php" style="padding: 16px 24px; border-bottom: 1px solid #F1F5F9; display: flex; gap: 12px; align-items: center;">
            <div class="position-relative" style="width: 300px;">
                <i class="bi bi-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control shadow-none" placeholder="Search officer ID or name..." style="padding-left: 36px; border-radius: 8px; border: 1px solid #E2E8F0; font-size: 0.85rem; height: 38px;">
            </div>
            
            <button type="submit" class="btn" style="background: #0F172A; color: white; border-radius: 8px; padding: 0 20px; font-size: 0.85rem; font-weight: 600; border: none; height: 38px;">Search</button>
            
            <select name="platoon" onchange="this.form.submit()" class="form-select shadow-none" style="width: 180px; border-radius: 8px; border: 1px solid #E2E8F0; font-size: 0.85rem; color: #475569; font-weight: 500; height: 38px; padding-top: 0; padding-bottom: 0;">
                <option>All Platoons</option>
                <?php foreach($platoonList as $pName): ?>
                <option value="<?= htmlspecialchars($pName) ?>" <?= $pName === $platoon_filter ? 'selected' : '' ?>><?= htmlspecialchars($pName) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        
        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-borderless mb-0" style="font-size: 0.85rem;">
                <thead style="border-bottom: 1px solid #F1F5F9;">
                    <tr>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Officer ID</th>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Name</th>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Rank</th>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Platoon</th>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Specialty</th>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($officers) == 0): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No cadets found.</td></tr>
                    <?php else: foreach ($officers as $officer): ?>
                    <tr style="border-bottom: 1px solid #F8FAFC; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 20px 24px; color: #475569;"><?= htmlspecialchars($officer['student_id']) ?></td>
                        <td style="padding: 20px 24px; font-weight: 500; color: #0F172A;"><?= htmlspecialchars($officer['last_name'].', '.$officer['first_name']) ?></td>
                        <td style="padding: 20px 24px; color: #475569;">Pvt</td>
                        <td style="padding: 20px 24px;">
                            <span style="background: #EEF2FF; color: #6366F1; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">
                                <?= htmlspecialchars($officer['platoon']) ?>
                            </span>
                        </td>
                        <td style="padding: 20px 24px; color: #475569;">Rifle</td>
                        <td style="padding: 20px 24px;">
                            <?php if ($officer['status'] === '1st'): ?>
                                <span style="background: #ECFDF5; color: #10B981; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">1st Semester</span>
                            <?php else: ?>
                                <span style="background: #FFFBEB; color: #F59E0B; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">2nd Semester</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Add New Officer Modal -->
<div class="modal fade" id="addOfficerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content" style="border: none; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div class="modal-header border-0" style="padding: 24px 24px 16px;">
                <div>
                    <h5 class="modal-title fw-bold mb-1" style="color: #0F172A; font-size: 1.15rem;">Add New Officer</h5>
                    <div class="text-muted" style="font-size: 0.85rem;">Enter officer details to add to the section</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
            <div class="modal-body" style="padding: 16px 24px;">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Officer ID</label>
                        <input type="text" class="form-control" placeholder="e.g. 2024-XXXXX" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Rank</label>
                        <input type="text" class="form-control" placeholder="Rank" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Name</label>
                    <input type="text" class="form-control" placeholder="Last, First M." style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Platoon</label>
                        <select class="form-select" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none; color: #475569;">
                            <option>Platoon</option>
                            <option>Alpha</option>
                            <option>Bravo</option>
                            <option>Charlie</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Specialty</label>
                        <input type="text" class="form-control" placeholder="Specialty" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0" style="padding: 16px 24px 24px;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: #475569; border: 1px solid #E2E8F0; background: white;">Cancel</button>
                <button type="button" class="btn btn-dark" style="padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; background: #0F172A; border: none; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-check2"></i> Save Officer
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
