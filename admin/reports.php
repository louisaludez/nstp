<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/ReportsController.php';

$extra_css = ['../assets/css/style.css'];
$extra_js_head = ['https://cdn.jsdelivr.net/npm/chart.js'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">
    
    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Archive Overview</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Access completed historic records, enrollment profiles, and parsed grade extracts.</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1 -->
        <div class="col-md-4">
            <div class="dash-panel d-flex align-items-center gap-3 p-3 px-4" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #EEF2FF; color: #6366F1; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/></svg>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1;"><?= $archived_count ?></div>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Archived Student Records</div>
                </div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="col-md-4">
            <div class="dash-panel d-flex align-items-center gap-3 p-3 px-4" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #ECFDF5; color: #10B981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1;"><?= $pass_rate ?>%</div>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Passing Rate (Passed: <?= $total_passed ?>)</div>
                </div>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="col-md-4">
            <div class="dash-panel d-flex align-items-center gap-3 p-3 px-4" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF2F2; color: #EF4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1;"><?= $failure_rate ?>%</div>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Failure / Rem. Rate (Failed: <?= $total_failed ?>)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9CA3AF;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="archiveSearch" class="form-control" placeholder="Search name, student no, section..." style="padding-left: 32px; font-size: 0.8rem; border-radius: 8px; border: 1px solid #E2E8F0; width: 280px; box-shadow: none;">
            </div>
            <div class="d-flex align-items-center gap-2">
                <span style="font-size: 0.8rem; color: #64748B;">Program:</span>
                <select id="programFilter" class="form-select border-0 shadow-none bg-transparent fw-medium" style="font-size: 0.85rem; color: #1E293B; width: auto; padding-left: 0; padding-right: 24px; cursor: pointer;">
                    <option value="All">All Programs</option>
                    <option value="CWTS">CWTS</option>
                    <option value="LTS">LTS</option>
                    <option value="ROTC">ROTC</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span style="font-size: 0.8rem; color: #64748B;">Status:</span>
                <select id="statusFilter" class="form-select border-0 shadow-none bg-transparent fw-medium" style="font-size: 0.85rem; color: #1E293B; width: auto; padding-left: 0; padding-right: 24px; cursor: pointer;">
                    <option value="All">All Remarks</option>
                    <option value="Passed">Passed</option>
                    <option value="Failed">Failed</option>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export Archive
        </button>
    </div>

    <!-- Data Table -->
    <div class="dash-panel p-0" style="overflow: hidden; border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
        <table class="table m-0" style="font-size: 0.8rem; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #E2E8F0;">
                    <th style="padding: 16px 24px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">STUDENT NO</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">STUDENT NAME</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">GENDER</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">SECTION</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">PROGRAM</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">INSTRUCTOR</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">GRADE</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">REMARKS</th>
                    <th style="padding: 16px 24px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">DATE ARCHIVED</th>
                </tr>
            </thead>
            <tbody id="archiveTableBody">
                <?php if (count($archived_records) > 0): ?>
                    <?php foreach ($archived_records as $record): 
                        $program = htmlspecialchars($record['program']);
                        $status = htmlspecialchars($record['remarks']);
                        
                        $programBadge = 'background: #EEF2FF; color: #6366F1;';
                        if ($program === 'LTS') $programBadge = 'background: #ECFDF5; color: #10B981;';
                        if ($program === 'ROTC') $programBadge = 'background: #FFF7ED; color: #F59E0B;';

                        $statusBadge = $status === 'Passed' ? 'background: #ECFDF5; color: #10B981;' : 'background: #FEF2F2; color: #EF4444;';
                    ?>
                    <tr class="archive-row" data-program="<?= $program ?>" data-status="<?= $status ?>" style="border-bottom: 1px solid #F1F5F9; transition: background-color 0.15s; cursor: pointer;" onmouseover="this.style.backgroundColor='#F8FAFC'" onmouseout="this.style.backgroundColor='transparent'">
                        <td style="padding: 16px 24px; color: #64748B; border: none;"><?= htmlspecialchars($record['student_id']) ?></td>
                        <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;" class="searchable-name"><?= htmlspecialchars($record['full_name']) ?></td>
                        <td style="padding: 16px; color: #64748B; border: none;"><?= htmlspecialchars($record['gender']) ?></td>
                        <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;" class="searchable-section"><?= htmlspecialchars($record['section_name']) ?></td>
                        <td style="padding: 16px; border: none;"><span style="<?= $programBadge ?> padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;"><?= $program ?></span></td>
                        <td style="padding: 16px; color: #64748B; border: none;"><?= htmlspecialchars($record['instructor_name']) ?></td>
                        <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;"><?= htmlspecialchars($record['final_grade']) ?></td>
                        <td style="padding: 16px; border: none;"><span style="<?= $statusBadge ?> padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 500;"><?= $status ?></span></td>
                        <td style="padding: 16px 24px; color: #64748B; border: none;"><?= date('M j, Y', strtotime($record['date_archived'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">No archived records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('archiveSearch');
    const programFilter = document.getElementById('programFilter');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.archive-row');

    function filterTable() {
        const query = searchInput.value.toLowerCase();
        const program = programFilter.value;
        const status = statusFilter.value;

        rows.forEach(row => {
            const studentId = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const name = row.querySelector('.searchable-name').textContent.toLowerCase();
            const section = row.querySelector('.searchable-section').textContent.toLowerCase();
            
            const matchSearch = studentId.includes(query) || name.includes(query) || section.includes(query);
            const matchProgram = program === 'All' || row.getAttribute('data-program') === program;
            const matchStatus = status === 'All' || row.getAttribute('data-status') === status;

            if (matchSearch && matchProgram && matchStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTable);
    programFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
});
</script>
</body>
</html>
