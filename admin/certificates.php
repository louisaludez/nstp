<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/CertificatesController.php';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">

    <?php include '../includes/topbar.php'; ?>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Certificate Overview</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Click a row to view the student list</p>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" class="m-0 p-0">
                <input type="hidden" name="action" value="toggle_ched_approval">
                <?php if ($ched_approval_status): ?>
                    <button type="submit" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #10B981; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500;">
                        <i class="bi bi-check-circle-fill"></i> CHED Approval Received
                    </button>
                <?php else: ?>
                    <button type="submit" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A; border-radius: 8px; padding: 8px 16px; font-weight: 500;">
                        <i class="bi bi-exclamation-triangle"></i> Pending CHED Approval
                    </button>
                <?php endif; ?>
            </form>
            <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #ECFDF5; color: #10B981; border: 1px dashed #10B981; border-radius: 8px; padding: 8px 16px; font-weight: 500;" onclick="alert('Import coming soon.')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Import XLSX Grades
            </button>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         MAIN 2-COLUMN LAYOUT
    ══════════════════════════════════════ -->
    <div class="row g-4">

        <!-- LEFT: Certificate Batches -->
        <div class="col-lg-7 col-xl-8">
            <div class="d-flex flex-column gap-3">

                <!-- Batch list -->
                <?php if (count($cert_batches) > 0):
                    foreach ($cert_batches as $idx => $batch):
                        $ready = ($batch['passed_count'] > 0 && $batch['issued_count'] < $batch['passed_count']);
                        $statusClass = $ready ? 'background: #ECFDF5; color: #10B981;' : 'background: #FFF7ED; color: #F59E0B;';
                        $statusLabel = $ready ? 'Ready' : 'Reviewing';
                        $title = htmlspecialchars($batch['component']) === 'ROTC' 
                            ? "ROTC Basic Course — " . htmlspecialchars($batch['section_name']) 
                            : htmlspecialchars($batch['component']) . " Completion";
                ?>
                <div class="dash-panel d-flex align-items-center justify-content-between p-3 px-4 mb-3" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Award Icon -->
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: #4F46E5; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-hexagon" style="font-size: 1.2rem;"></i>
                        </div>

                        <!-- Info -->
                        <div>
                            <div style="font-size: 1rem; font-weight: 600; color: #0F172A; margin-bottom: 2px;">
                                <?= htmlspecialchars($batch['section_name']) ?>
                            </div>
                            <div style="font-size: 0.85rem; color: #64748B;">
                                <?= htmlspecialchars($batch['component']) ?> · <?= (int)$batch['passed_count'] ?> passed student(s)
                            </div>
                        </div>
                    </div>

                    <!-- Status + Action -->
                    <div class="d-flex align-items-center gap-3">
                        <span style="font-size: 0.75rem; font-weight: 600; padding: 4px 12px; border-radius: 20px; <?= $statusClass ?>">
                            <?= $statusLabel ?>
                        </span>
                        
                        <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 8px; background: #FEF2F2; color: #EF4444; border: 1px solid #FEE2E2;">
                            <i class="bi bi-trash"></i>
                        </button>

                        <?php if ($ready && $ched_approval_status): ?>
                        <button type="button" class="btn btn-sm" style="background: #0F172A; color: white; border-radius: 8px; font-weight: 500; padding: 6px 20px; font-size: 0.85rem;" onclick="openGenerateModal('<?= htmlspecialchars(addslashes($batch['section_name'])) ?>', '<?= htmlspecialchars(addslashes($batch['component'])) ?>', <?= (int)$batch['passed_count'] ?>)">
                            Generate
                        </button>
                        <?php else: ?>
                        <button class="btn btn-sm" style="background: #E2E8F0; color: #64748B; border-radius: 8px; font-weight: 500; padding: 6px 20px; font-size: 0.85rem;" disabled title="<?= !$ched_approval_status ? 'Pending CHED Approval' : 'Reviewing' ?>">
                            Generate
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; else: ?>

                <!-- Empty state -->
                <div class="dash-panel text-center py-5 px-4" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: #EEF2FF;
                                display: flex; align-items: center; justify-content: center;
                                margin: 0 auto 14px; font-size: 1.4rem; color: #6366F1;">
                        <i class="bi bi-award"></i>
                    </div>
                    <p class="fw-semibold mb-1" style="color: var(--text-dark); font-size: 0.9rem;">
                        No certificate batches yet
                    </p>
                    <p class="text-muted small mb-0">
                        Batches appear here once sections have enrolled students.
                    </p>
                </div>

                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Recently Issued -->
        <div class="col-lg-5 col-xl-4">
            <div class="dash-panel p-0" style="overflow: hidden; border-radius: 12px; border: 1px solid #E2E8F0;">

                <div class="px-4 py-3 border-bottom" style="border-color: #E2E8F0 !important;">
                    <h6 class="fw-bold mb-0" style="font-size: 0.95rem; color: #111827;">Recently Issued</h6>
                </div>

                <div class="d-flex flex-column pt-2 pb-2">
                    <?php if (count($recent_certs) > 0):
                        foreach ($recent_certs as $idx => $cert):
                            $c = certComponentColor($cert['component']);
                            $relDate = relativeDate($cert['created_at']);
                    ?>
                    <div class="d-flex align-items-center gap-3 px-4 py-3" style="transition: background-color 0.15s; cursor: pointer;" onmouseover="this.style.backgroundColor='#F8FAFC'" onmouseout="this.style.backgroundColor='transparent'">

                        <!-- Check icon -->
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #F0FDF4; border: 1px solid #BBF7D0; color: #10B981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>

                        <!-- Info -->
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.85rem; font-weight: 500; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;">
                                <?= htmlspecialchars($cert['full_name']) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #64748B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($cert['component']) ?> · <?= htmlspecialchars($cert['serial_number'] ?? '2024-XXXXX') ?>
                            </div>
                        </div>

                        <!-- Date -->
                        <div style="font-size: 0.75rem; color: #64748B; flex-shrink: 0;">
                            <?= $relDate ?>
                        </div>
                    </div>
                    <?php endforeach; else: ?>

                <div class="text-center py-5 px-4 h-100 d-flex flex-column justify-content-center align-items-center" style="min-height: 200px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #E2E8F0; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; color: #CBD5E1; font-size: 1.2rem;">
                        <i class="bi bi-check"></i>
                    </div>
                    <p class="text-muted small mb-0" style="color: #94A3B8 !important;">No certificates recently issued.</p>
                </div>

                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

</div>

<style>
/* ══════════════════════════════════════════
   CERTIFICATES PAGE — Figma-match styles
══════════════════════════════════════════ */

/* Certificate batch rows */
.cert-batch-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 24px;
    transition: background-color 0.15s;
}
.cert-batch-row:hover { background-color: #F9FAFB; }

.cert-batch-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.cert-batch-info { flex: 1; min-width: 0; }

.cert-batch-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--primary-accent);
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cert-batch-meta {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.cert-batch-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

.cert-generate-btn {
    background: #111827;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 7px 16px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.15s;
    font-family: 'Inter', sans-serif;
}
.cert-generate-btn:hover { background: #1F2937; }

/* Recently issued rows */
.cert-issued-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    transition: background-color 0.15s;
}
.cert-issued-row:hover { background-color: #F9FAFB; }

.cert-issued-icon { flex-shrink: 0; }

.cert-issued-info {
    flex: 1;
    min-width: 0;
}
.cert-issued-name {
    font-size: 0.83rem;
    font-weight: 600;
    color: var(--text-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cert-issued-meta {
    font-size: 0.71rem;
    font-weight: 500;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cert-issued-date {
    font-size: 0.72rem;
    color: var(--text-muted);
    flex-shrink: 0;
    white-space: nowrap;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
<!-- Generate Certificates Modal -->
<div class="modal fade" id="generateCertModal" tabindex="-1" aria-labelledby="generateCertModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
      <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
        <div>
            <h5 class="modal-title fw-bold" id="generateCertModalLabel" style="color: #0F172A; font-size: 1.25rem;">Generate Certificates — <span id="modalSectionName"></span></h5>
            <p class="text-muted mb-0" style="font-size: 0.9rem;" id="modalSubtitle">CWTS-1A · CWTS · <span style="color: #10B981; font-weight: 500;">1 eligible</span></p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 pt-4 pb-4">
        
        <form id="generateCertForm" method="POST" action="">
            <input type="hidden" name="action" value="generate_certificates">
            <input type="hidden" name="section_name" id="formSectionName">

            <!-- Select Certificate Design -->
            <h6 class="fw-bold mb-3" style="font-size: 0.75rem; color: #94A3B8; letter-spacing: 0.5px;">SELECT CERTIFICATE DESIGN</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="cert-design-card active" id="design-CWTS" onclick="selectDesign(this)">
                        <div class="cert-preview"><span class="fw-bold" style="font-size: 0.75rem; color: #1E293B;">CWTS</span></div>
                        <div class="fw-bold mt-2" style="font-size: 0.85rem; color: #0F172A;">Default CWTS Completi...</div>
                        <div style="font-size: 0.75rem; color: #94A3B8;">Default Border</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="cert-design-card" id="design-LTS" onclick="selectDesign(this)">
                        <div class="cert-preview"><span class="fw-bold" style="font-size: 0.75rem; color: #1E293B;">LTS</span></div>
                        <div class="fw-bold mt-2" style="font-size: 0.85rem; color: #0F172A;">Default LTS Achievemen...</div>
                        <div style="font-size: 0.75rem; color: #94A3B8;">Default Border</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="cert-design-card" id="design-ROTC" onclick="selectDesign(this)">
                        <div class="cert-preview"><span class="fw-bold" style="font-size: 0.75rem; color: #1E293B;">ROTC</span></div>
                        <div class="fw-bold mt-2" style="font-size: 0.85rem; color: #0F172A;">Default ROTC Military Se...</div>
                        <div style="font-size: 0.75rem; color: #94A3B8;">Default Border</div>
                    </div>
                </div>
            </div>

            <!-- Eligible Students -->
            <h6 class="fw-bold mb-3" style="font-size: 0.75rem; color: #94A3B8; letter-spacing: 0.5px;">ELIGIBLE STUDENTS (<span id="modalEligibleCount">0</span>)</h6>
            
            <div id="eligibleStudentsList" class="d-flex flex-column gap-3">
                <div class="text-center text-muted py-3">Loading...</div>
            </div>
            
        </form>

      </div>
      <div class="modal-footer border-top-0 pt-0 px-4 pb-4 justify-content-between">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500; font-size: 0.9rem; padding: 10px 20px; color: #64748B; background: transparent; border: none;">Cancel</button>
        <button type="submit" form="generateCertForm" class="btn text-white" style="background: #4F46E5; border-radius: 8px; font-weight: 500; font-size: 0.95rem; padding: 10px 24px;">
            <i class="bi bi-download me-2"></i> Download All Certificates
        </button>
      </div>
    </div>
  </div>
</div>

<style>
/* ... existing styles ... */
.cert-design-card {
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}
.cert-design-card:hover {
    border-color: #CBD5E1;
}
.cert-design-card.active {
    border-color: #4F46E5;
    background: #F5F3FF;
    box-shadow: 0 0 0 1px #4F46E5;
}
.cert-preview {
    height: 60px;
    border: 3px solid #312E81;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function selectDesign(element) {
    document.querySelectorAll('.cert-design-card').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
}

function openGenerateModal(sectionName, component, count) {
    document.getElementById('modalSectionName').innerText = sectionName;
    document.getElementById('formSectionName').value = sectionName;
    document.getElementById('modalSubtitle').innerHTML = `${sectionName} · ${component} · <span style="color: #10B981; font-weight: 500;">${count} eligible</span>`;
    document.getElementById('modalEligibleCount').innerText = count;
    
    // Auto-select design based on component
    document.querySelectorAll('.cert-design-card').forEach(el => el.classList.remove('active'));
    let designCard = document.getElementById('design-' + component);
    if(designCard) designCard.classList.add('active');

    const modal = new bootstrap.Modal(document.getElementById('generateCertModal'));
    modal.show();

    // Fetch eligible students
    const listDiv = document.getElementById('eligibleStudentsList');
    listDiv.innerHTML = '<div class="text-center text-muted py-3">Loading students...</div>';

    fetch(`ajax_get_eligible_students.php?section_name=${encodeURIComponent(sectionName)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                listDiv.innerHTML = '';
                if(data.students.length === 0) {
                    listDiv.innerHTML = '<div class="text-center text-muted py-3">No eligible students found.</div>';
                    return;
                }
                data.students.forEach((student, index) => {
                    const row = `
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.8rem;">
                                ${index + 1}
                            </div>
                            <div>
                                <div style="font-weight: 500; color: #0F172A; font-size: 0.95rem; margin-bottom: 2px;">${student.full_name}</div>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size: 0.75rem; color: #94A3B8; font-weight: 600;">SERIAL:</span>
                                    <input type="text" name="serials[${student.enrollment_id}]" class="form-control form-control-sm" value="${student.serial_preview}" style="font-size: 0.8rem; padding: 2px 8px; height: auto; max-width: 140px;">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-1" style="background: #4F46E5; color: white; border-radius: 6px; font-weight: 500; font-size: 0.8rem; padding: 6px 12px;" onclick="alert('Individual PDF download coming soon!')">
                            <i class="bi bi-download"></i> PDF
                        </button>
                    </div>
                    `;
                    listDiv.insertAdjacentHTML('beforeend', row);
                });
            } else {
                listDiv.innerHTML = `<div class="text-center text-danger py-3">Error: ${data.error}</div>`;
            }
        })
        .catch(err => {
            listDiv.innerHTML = `<div class="text-center text-danger py-3">Failed to load students.</div>`;
        });
}

// Check for download trigger on page load
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') && urlParams.has('download_section')) {
        const section = urlParams.get('download_section');
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Certificates generated and saved.',
            confirmButtonColor: '#4F46E5'
        });
        window.open(`generate_pdf.php?section=${encodeURIComponent(section)}`, '_blank');
        
        // Remove params from URL to prevent re-triggering
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}
</script>
</body>
</html>
