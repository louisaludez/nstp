<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/ManageSectionsController.php';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">
    
    <?php include '../includes/topbar.php'; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Sections & Students Overview</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Click a section row to open the student section</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm" style="border: 1px solid #6EE7B7; background: #ECFDF5; color: #047857; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px; padding: 8px 12px;" data-bs-toggle="modal" data-bs-target="#uploadMasterListModal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Import Master List (CSV/XLSX)
            </button>
            <button type="button" class="btn btn-sm" style="background: #4F46E5; color: white; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px; padding: 8px 12px;" data-bs-toggle="modal" data-bs-target="#createSectionModal" onmouseover="this.style.background='#4338CA'" onmouseout="this.style.background='#4F46E5'">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                New Section
            </button>
        </div>
    </div>



    <!-- Program Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- CWTS -->
        <div class="col-md-4">
            <button id="card-CWTS" class="w-100 text-start component-card" data-component="CWTS" onclick="filterByComponent('CWTS', this)" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #E2E8F0; outline: none; cursor: pointer; transition: all 0.2s;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #4F46E5; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem; flex-shrink: 0;">C</div>
                    <div>
                        <div style="font-weight: 600; color: #1E293B; font-size: 0.9rem;">CWTS</div>
                        <div style="font-size: 0.75rem; color: #64748B;">Civic Welfare Training Service</div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.75rem;">
                        <span style="color: #64748B;">Students</span>
                        <span style="font-weight: 600; color: #475569;"><?= $cwts_count ?> / <?= $cwts_cap ?></span>
                    </div>
                    <div style="width: 100%; height: 4px; border-radius: 4px; background: #F1F5F9; margin-bottom: 20px; overflow: hidden;">
                        <div style="height: 100%; width: <?= min(100, round(($cwts_count/$cwts_cap)*100)) ?>%; background: #6366F1; border-radius: 4px;"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span style="color: #64748B;">Sections</span>
                        <span style="font-weight: 600; color: #475569;">-</span>
                    </div>
                </div>
            </button>
        </div>
        <!-- LTS -->
        <div class="col-md-4">
            <button id="card-LTS" class="w-100 text-start component-card" data-component="LTS" onclick="filterByComponent('LTS', this)" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #E2E8F0; outline: none; cursor: pointer; transition: all 0.2s;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #059669; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem; flex-shrink: 0;">L</div>
                    <div>
                        <div style="font-weight: 600; color: #1E293B; font-size: 0.9rem;">LTS</div>
                        <div style="font-size: 0.75rem; color: #64748B;">Literacy Training Service</div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.75rem;">
                        <span style="color: #64748B;">Students</span>
                        <span style="font-weight: 600; color: #475569;"><?= $lts_count ?> / <?= $lts_cap ?></span>
                    </div>
                    <div style="width: 100%; height: 4px; border-radius: 4px; background: #F1F5F9; margin-bottom: 20px; overflow: hidden;">
                        <div style="height: 100%; width: <?= min(100, round(($lts_count/$lts_cap)*100)) ?>%; background: #10B981; border-radius: 4px;"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span style="color: #64748B;">Sections</span>
                        <span style="font-weight: 600; color: #475569;">-</span>
                    </div>
                </div>
            </button>
        </div>
        <!-- ROTC -->
        <div class="col-md-4">
            <button id="card-ROTC" class="w-100 text-start component-card" data-component="ROTC" onclick="filterByComponent('ROTC', this)" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #E2E8F0; outline: none; cursor: pointer; transition: all 0.2s;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #E11D48; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem; flex-shrink: 0;">R</div>
                    <div>
                        <div style="font-weight: 600; color: #1E293B; font-size: 0.9rem;">ROTC</div>
                        <div style="font-size: 0.75rem; color: #64748B;">Reserve Officers' Training Corps</div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.75rem;">
                        <span style="color: #64748B;">Students</span>
                        <span style="font-weight: 600; color: #475569;"><?= $rotc_count ?> / <?= $rotc_cap ?></span>
                    </div>
                    <div style="width: 100%; height: 4px; border-radius: 4px; background: #F1F5F9; margin-bottom: 20px; overflow: hidden;">
                        <div style="height: 100%; width: <?= min(100, round(($rotc_count/$rotc_cap)*100)) ?>%; background: #FB7185; border-radius: 4px;"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span style="color: #64748B;">Sections</span>
                        <span style="font-weight: 600; color: #475569;">-</span>
                    </div>
                </div>
            </button>
        </div>
    </div>

    <!-- Search + Filter Bar -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <div class="position-relative flex-grow-1" style="max-width: 450px;">
            <span class="position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input id="sectionsSearchInput" placeholder="Search section or instructor..." style="width: 100%; padding: 8px 12px 8px 36px; font-size: 0.875rem; border-radius: 8px; background: #F8FAFC; border: 1px solid #E2E8F0; outline: none; transition: all 0.2s;" onfocus="this.style.background='white'; this.style.borderColor='#C7D2FE'" onblur="this.style.background='#F8FAFC'; this.style.borderColor='#E2E8F0'" />
        </div>
        <button class="btn btn-sm d-inline-flex align-items-center gap-2" id="btn-all-sections" onclick="filterByComponent('All', this)" style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; border-radius: 8px; padding: 8px 16px; font-size: 0.875rem; font-weight: 500;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
            All Sections
        </button>
    </div>

    <div style="background: white; border: 1px solid #F1F5F9; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
        <div class="p-4 border-bottom" style="border-color: #F8FAFC !important;">
            <h6 class="mb-0 fw-semibold" style="color: #1E293B;">All Sections</h6>
        </div>

        <div style="overflow-x: auto;">
            <table class="w-100" style="font-size: 0.875rem;" id="sectionsTable">
                <thead style="border-bottom: 1px solid #F1F5F9; background: #FAFAF9;">
                    <tr style="text-align: left; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B;">
                        <th style="padding: 16px 24px; font-weight: 600;">Section</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Program</th>
                        <th style="padding: 16px 24px; font-weight: 600;">School Year</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Students</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Instructor</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Room</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Semester</th>
                        <th style="padding: 16px 24px; font-weight: 600; text-align: center;">Actions</th>
                    </tr>
                </thead>
            <tbody>
                <?php
                try {
                    $sections_stmt = $pdo->query("
                        SELECT s.*, u.full_name as instructor_name,
                        (SELECT COUNT(*) FROM enrollments e WHERE e.section_id = s.id) as student_count
                        FROM sections s
                        LEFT JOIN users u ON s.instructor_id = u.id
                        ORDER BY s.created_at DESC
                    ");
                    $all_sections = $sections_stmt->fetchAll();

                    $instructors_stmt = $pdo->query("SELECT id, full_name FROM users WHERE role = 'Instructor' ORDER BY full_name ASC");
                    $instructors = $instructors_stmt->fetchAll();
                } catch (Exception $e) { 
                    $all_sections = []; 
                    $instructors = [];
                }

                if (count($all_sections) > 0):
                    foreach ($all_sections as $sec):
                        $prog = $sec['component'];
                        $progStyle = ($prog === 'CWTS') ? 'background: #F3E8FF; color: #9333EA;' : (($prog === 'LTS') ? 'background: #DCFCE7; color: #16A34A;' : 'background: #FFE4E6; color: #E11D48;');
                        $syStyle = 'background: #F3E8FF; color: #9333EA;';
                        $semStyle = 'background: #FEF9C3; color: #CA8A04;';
                ?>
                <tr class="section-row" data-component="<?= htmlspecialchars($prog) ?>" style="border-bottom: 1px solid #F8FAFC; transition: background 0.15s; cursor: pointer;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'" onclick="window.location.href='view_section.php?id=<?= $sec['id'] ?>'">
                    <td style="padding: 16px 24px; color: #334155; font-weight: 400;"><?= htmlspecialchars($sec['section_name']) ?></td>
                    <td style="padding: 16px 24px;">
                        <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; font-weight: 500; <?= $progStyle ?>"><?= htmlspecialchars($prog) ?></span>
                    </td>
                    <td style="padding: 16px 24px;">
                        <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; font-weight: 500; <?= $syStyle ?>"><?= htmlspecialchars($sec['school_year']) ?></span>
                    </td>
                    <td style="padding: 16px 24px; color: #475569;">
                        <?= $sec['student_count'] ?>
                    </td>
                    <td style="padding: 16px 24px; color: #475569;"><?= htmlspecialchars($sec['instructor_name'] ?? 'TBA') ?></td>
                    <td style="padding: 16px 24px; color: #475569;">
                        <?= htmlspecialchars($sec['room'] ?? 'TBA') ?>
                    </td>
                    <td style="padding: 16px 24px;">
                        <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; font-weight: 500; <?= $semStyle ?>"><?= htmlspecialchars($sec['semester']) ?></span>
                    </td>
                    <td style="padding: 16px 24px; text-align: center;">
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm p-1 text-muted" onclick="event.stopPropagation(); openEditModal(this)" title="Edit"
                                data-id="<?= $sec['id'] ?>"
                                data-name="<?= htmlspecialchars($sec['section_name']) ?>"
                                data-component="<?= htmlspecialchars($sec['component']) ?>"
                                data-sy="<?= htmlspecialchars($sec['school_year']) ?>"
                                data-sem="<?= htmlspecialchars($sec['semester']) ?>"
                                data-room="<?= htmlspecialchars($sec['room'] ?? '') ?>"
                                data-inst="<?= htmlspecialchars($sec['instructor_id'] ?? '') ?>"
                                style="border: none; background: transparent; cursor: pointer;">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="#94A3B8" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" onmouseover="this.style.stroke='#4F46E5'" onmouseout="this.style.stroke='#94A3B8'"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </button>
                            <button class="btn btn-sm p-1 text-muted" onclick="event.stopPropagation(); openDeleteModal(<?= $sec['id'] ?>)" title="Delete" style="border: none; background: transparent; cursor: pointer;">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="#94A3B8" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" onmouseover="this.style.stroke='#E11D48'" onmouseout="this.style.stroke='#94A3B8'"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php
                    endforeach;
                else:
                ?>
                <tr>
                    <td colspan="8" class="py-5 text-center text-muted" style="font-size: 0.875rem;">
                        No sections found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        
        <!-- Pagination Footer -->
        <div class="d-flex justify-content-between align-items-center p-4 border-top" style="border-color: #F8FAFC !important; background: white;">
            <div style="font-size: 0.875rem; color: #64748B;" id="paginationInfo">
                Showing 0 results
            </div>
            <div class="d-flex gap-2" id="paginationControls">
                <!-- Pagination buttons injected by JS -->
            </div>
        </div>
    </div>

    <!-- Unassigned Students Section -->
    <?php if (count($unassigned_students) > 0): ?>
    <div class="dash-panel mt-4">
        <div class="panel-title">🎓 Unassigned Students</div>
        <p class="text-muted small" style="margin-top:-12px;margin-bottom:16px;">Students waiting for component assignment</p>
        
        <?php foreach ($unassigned_students as $student): ?>
            <div class="d-flex justify-content-between align-items-center py-3" style="border-bottom:1px solid #F3F4F6;">
                <div>
                    <div style="font-weight:600;color:var(--text-dark);font-size:0.9rem;">
                        <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                    </div>
                    <small class="text-muted">
                        <?= htmlspecialchars($student['student_id']) ?> · 
                        <?= htmlspecialchars($student['course'] ?? 'N/A') ?> Year <?= htmlspecialchars($student['year_level'] ?? 'N/A') ?>
                    </small>
                </div>
                <form method="POST" action="" class="d-flex gap-2 mb-0">
                    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']) ?>">
                    <button type="submit" name="component" value="CWTS" class="btn-figma primary" style="font-size:0.75rem;padding:4px 12px;">CWTS</button>
                    <button type="submit" name="component" value="LTS" class="btn-figma teal" style="font-size:0.75rem;padding:4px 12px;">LTS</button>
                    <button type="submit" name="component" value="ROTC" class="btn-figma outline" style="font-size:0.75rem;padding:4px 12px;">ROTC</button>
                    <input type="hidden" name="assign_component" value="1">
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>



</div>

<!-- Create Section Modal -->
<div class="modal fade" id="createSectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Create New Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Section Name</label>
                        <input type="text" name="section_name" class="form-control" style="border-radius:8px;border-color:var(--border-color);" placeholder="e.g., BSCS-2A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Component</label>
                        <select name="component" class="form-select" style="border-radius:8px;border-color:var(--border-color);" required>
                            <option value="CWTS">CWTS</option>
                            <option value="LTS">LTS</option>
                            <option value="ROTC">ROTC</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium small">School Year</label>
                            <input type="text" name="school_year" class="form-control" style="border-radius:8px;border-color:var(--border-color);" value="2026-2027" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium small">Semester</label>
                            <select name="semester" class="form-select" style="border-radius:8px;border-color:var(--border-color);" required>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Max Capacity (Limitation)</label>
                        <input type="number" name="max_capacity" class="form-control" style="border-radius:8px;border-color:var(--border-color);" value="50" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Assign Instructor (Optional)</label>
                        <select name="instructor_id" class="form-select" style="border-radius:8px;border-color:var(--border-color);">
                            <option value="">-- No Instructor --</option>
                            <?php foreach ($instructors as $inst): ?>
                                <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 pe-4 pt-0">
                    <button type="button" class="btn-figma outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_section" class="btn-figma primary">Create Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Master List Modal -->
<div class="modal fade" id="uploadMasterListModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Upload Student Master List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 48px; height: 48px; color: #10B981; margin: 0 auto;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    </div>
                    <p class="text-muted small mb-4">Upload the official student masterlist (CSV/XLSX) to be stored in the system and forwarded to the respective instructors and ROTC officers.</p>
                    <input type="file" name="master_list_file" class="form-control" accept=".csv, .xlsx" required>
                </div>
                <div class="modal-footer border-top-0 pb-4 pe-4 pt-0 justify-content-center">
                    <button type="button" class="btn-figma outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="upload_master_list" class="btn-figma primary d-inline-flex align-items-center gap-2">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Upload to System
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>  

<!-- Assign Instructor Modal -->
<div class="modal fade" id="assignInstructorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Assign Instructor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="section_id" id="assign_section_id">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Assigning an instructor to <span id="assign_section_name" class="fw-bold text-dark"></span></p>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Select Instructor</label>
                        <select name="instructor_id" id="assign_instructor_id" class="form-select" style="border-radius:8px;border-color:var(--border-color);" required>
                            <option value="">-- Select Instructor --</option>
                            <?php foreach ($instructors as $inst): ?>
                                <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 pe-4 pt-0">
                    <button type="button" class="btn-figma outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="assign_instructor" class="btn-figma primary">Save Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function prepareAssign(id, name, currentInstId) {
    document.getElementById('assign_section_id').value = id;
    document.getElementById('assign_section_name').textContent = name;
    document.getElementById('assign_instructor_id').value = currentInstId || '';
}

let currentComponentFilter = 'All';
let currentPage = 1;
const rowsPerPage = 5;

function filterByComponent(component, btnElement) {
    currentComponentFilter = component;

    // Reset card borders
    document.querySelectorAll('.component-card').forEach(card => {
        card.style.border = '1px solid #E2E8F0';
    });

    // Reset All Sections button
    const btnAll = document.getElementById('btn-all-sections');
    if (btnAll) {
        btnAll.style.background = 'white';
        btnAll.style.color = '#0F172A';
        btnAll.style.borderColor = '#E2E8F0';
        btnAll.style.fontWeight = 'normal';
    }

    if (component === 'All') {
        if (btnAll) {
            btnAll.style.background = '#EEF2FF';
            btnAll.style.color = '#4F46E5';
            btnAll.style.borderColor = '#C7D2FE';
            btnAll.style.fontWeight = '500';
        }
    } else {
        const activeCard = document.getElementById('card-' + component);
        if (activeCard) {
            activeCard.style.border = '2px solid #818CF8';
        }
    }

    currentPage = 1;
    updatePagination();
}

function updatePagination() {
    const allRows = Array.from(document.querySelectorAll('table tbody tr.section-row'));
    const q = (document.getElementById('sectionsSearchInput')?.value || '').toLowerCase();
    
    const filteredRows = allRows.filter(row => {
        const text = row.textContent.toLowerCase();
        const rowComponent = row.getAttribute('data-component');
        const matchesSearch = text.includes(q);
        const matchesComponent = currentComponentFilter === 'All' || rowComponent === currentComponentFilter;
        return matchesSearch && matchesComponent;
    });
    
    allRows.forEach(row => row.style.display = 'none');
    
    const totalItems = filteredRows.length;
    const totalPages = Math.ceil(totalItems / rowsPerPage) || 1;
    
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;
    
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = Math.min(startIndex + rowsPerPage, totalItems);
    
    for (let i = startIndex; i < endIndex; i++) {
        filteredRows[i].style.display = '';
    }
    
    const info = document.getElementById('paginationInfo');
    if (info) {
        if (totalItems === 0) {
            info.textContent = 'Showing 0 results';
        } else {
            info.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} results`;
        }
    }
    
    const controls = document.getElementById('paginationControls');
    if (controls) {
        let html = `<button class="btn btn-sm btn-light border" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; background: white;" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg></button>`;
        
        for (let i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                html += `<button class="btn btn-sm" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; background: #F1F5F9; font-weight: 500; border: 1px solid #E2E8F0;" onclick="changePage(${i})">${i}</button>`;
            } else {
                html += `<button class="btn btn-sm btn-light border" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; background: white;" onclick="changePage(${i})">${i}</button>`;
            }
        }
        
        html += `<button class="btn btn-sm btn-light border" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; background: white;" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg></button>`;
        controls.innerHTML = html;
    }
}

function changePage(page) {
    currentPage = page;
    updatePagination();
}

document.getElementById('sectionsSearchInput')?.addEventListener('input', function() {
    currentPage = 1;
    updatePagination();
});

// Initialize pagination on load
document.addEventListener('DOMContentLoaded', updatePagination);

function openEditModal(btn) {
    document.getElementById('edit_section_id').value = btn.getAttribute('data-id');
    document.getElementById('edit_section_name').value = btn.getAttribute('data-name');
    document.getElementById('edit_component').value = btn.getAttribute('data-component');
    document.getElementById('edit_school_year').value = btn.getAttribute('data-sy');
    document.getElementById('edit_semester').value = btn.getAttribute('data-sem');
    document.getElementById('edit_room').value = btn.getAttribute('data-room');
    document.getElementById('edit_instructor_id').value = btn.getAttribute('data-inst');
    
    new bootstrap.Modal(document.getElementById('editSectionModal')).show();
}

function openDeleteModal(id) {
    document.getElementById('delete_section_id').value = id;
    new bootstrap.Modal(document.getElementById('deleteSectionModal')).show();
}
</script>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: #FAFAFA;">
            <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <div>
                    <h5 class="modal-title fw-bold" style="color: #1E293B;">Edit Section</h5>
                    <div style="font-size: 0.85rem; color: #64748B;">Modify section configurations</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 0.75rem;"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="section_id" id="edit_section_id">
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">SECTION CODE <span class="text-danger">*</span></label>
                            <input type="text" name="section_name" id="edit_section_name" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">NSTP COMPONENT <span class="text-danger">*</span></label>
                            <select name="component" id="edit_component" class="form-select" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required>
                                <option value="CWTS">CWTS — Civic Welfare</option>
                                <option value="LTS">LTS — Literacy Training</option>
                                <option value="ROTC">ROTC — Reserve Officers</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">SCHOOL YEAR</label>
                            <input type="text" name="school_year" id="edit_school_year" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">SEMESTER</label>
                            <input type="text" name="semester" id="edit_semester" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">ROOM</label>
                            <input type="text" name="room" id="edit_room" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" placeholder="TBA">
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">ASSIGN INSTRUCTOR</label>
                        <select name="instructor_id" id="edit_instructor_id" class="form-select" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;">
                            <option value="">— Select an instructor (optional) —</option>
                            <?php foreach ($instructors as $inst): ?>
                                <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top p-3" style="background: #FAFAFA; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; border-color: #F1F5F9 !important;">
                    <button type="button" class="btn px-4" data-bs-dismiss="modal" style="background: white; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 500; border-radius: 8px;">Cancel</button>
                    <button type="submit" name="edit_section" class="btn px-4 d-inline-flex align-items-center gap-2" style="background: #4F46E5; color: white; font-weight: 500; border-radius: 8px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Section Modal -->
<div class="modal fade" id="deleteSectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-danger">Delete Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="section_id" id="delete_section_id">
                <div class="modal-body p-4">
                    <p class="mb-0 text-muted">Are you sure you want to delete this section? This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-top-0 pb-4 pe-4 pt-0">
                    <button type="button" class="btn-figma outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_section" class="btn-figma" style="background: #E11D48; color: white; border: none;">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
