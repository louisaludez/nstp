<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/CertificateTemplatesController.php';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">

    <?php include '../includes/topbar.php'; ?>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Certificate Design Templates</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Create, edit, and manage high-fidelity certificate templates for different NSTP components</p>
        </div>
        <button type="button" class="btn text-white d-inline-flex align-items-center gap-2" style="background: #4F46E5; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500;" data-bs-toggle="modal" data-bs-target="#templateModal" onclick="openModal('new')">
            <i class="bi bi-plus-lg"></i> New Design Template
        </button>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; font-size: 0.9rem;">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php 
                if ($_GET['success'] == 1) echo "Template created successfully.";
                elseif ($_GET['success'] == 2) echo "Template updated successfully.";
                elseif ($_GET['success'] == 3) echo "Template deleted successfully.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Grid of Templates -->
    <div class="row g-4">
        <?php if (count($templates) > 0): ?>
            <?php foreach ($templates as $template): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 template-card p-3 pb-2" style="border-radius: 16px; border: 1px solid #E5E7EB; background: white; border-bottom: 2px solid #E5E7EB;">
                        
                        <!-- Image Preview Top -->
                        <div style="height: 140px; border-radius: 12px; background-color: #F9FAFB; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 16px; border: 1px solid #E2E8F0;">
                            <?php if (!empty($template['image_path']) && file_exists('../' . $template['image_path'])): ?>
                                <img src="../<?= htmlspecialchars($template['image_path']) ?>" alt="Template Image" style="width: 100%; height: 100%; object-fit: contain;">
                            <?php else: ?>
                                <div class="text-center text-muted" style="font-size: 0.8rem;">
                                    <div style="width: 100%; height: 100%; border: 3px solid #1E3A8A; padding: 20px;">
                                        <div style="font-size: 14px; font-weight: bold; color: #1E293B; margin-top: 20px;"><?= htmlspecialchars($template['header_title'] ?: 'CERTIFICATE') ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body Details -->
                        <div class="d-flex flex-column h-100">
                            <!-- Badges -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; <?= getBadgeStyle($template['badge_color']) ?>">
                                    <?= htmlspecialchars($template['program_type']) ?>
                                </span>
                                <?php if($template['is_active']): ?>
                                    <span style="font-size: 0.75rem; font-weight: 600; color: #10B981;">
                                        <i class="bi bi-circle-fill" style="font-size: 0.5rem; vertical-align: middle; margin-right: 2px;"></i> Active
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h5 class="fw-bold mb-3" style="color: #111827; font-size: 1rem;"><?= htmlspecialchars($template['name']) ?></h5>
                            
                            <div class="p-3 mb-3" style="background: #F8FAFC; border-radius: 12px;">
                                <div style="font-size: 0.8rem; color: #475569; margin-bottom: 4px;">
                                    <strong>Design:</strong> Default Border Template
                                </div>
                                <div style="font-size: 0.8rem; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <strong>Signatory:</strong> <?= htmlspecialchars($template['signatory_name'] ?: 'None') ?> (<?= htmlspecialchars($template['signatory_title'] ?: 'None') ?>)
                                </div>
                            </div>
                            
                            <!-- Actions Footer -->
                            <div class="mt-auto d-flex gap-2">
                                <button type="button" class="btn flex-grow-1" style="border: 1px solid #E2E8F0; border-radius: 8px; font-weight: 600; font-size: 0.85rem; color: #475569; background: white;" 
                                    onclick='openModal("edit", <?= json_encode($template) ?>)'>
                                    <i class="bi bi-pencil-square me-1"></i> Edit Template
                                </button>
                                <form method="POST" action="certificate_templates.php" class="m-0" onsubmit="return confirm('Are you sure you want to delete this template?');">
                                    <input type="hidden" name="action" value="delete_template">
                                    <input type="hidden" name="template_id" value="<?= $template['id'] ?>">
                                    <button type="submit" class="btn" style="border: 1px solid #FEE2E2; border-radius: 8px; background: #FEF2F2; color: #EF4444; width: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5 px-4" style="border-radius: 12px; border: 1px dashed #E2E8F0; background: white;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: #EEF2FF; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 1.4rem; color: #4F46E5;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </div>
                    <p class="fw-semibold mb-1" style="color: #111827; font-size: 0.95rem;">No templates yet</p>
                    <p class="text-muted small mb-0">Create your first certificate template by clicking the button above.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Wide Template Modal with Live Preview -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered border-0" style="max-width: 95vw;">
        <div class="modal-content overflow-hidden" style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); height: 90vh;">
            
            <div class="row g-0 h-100">
                <!-- LEFT COLUMN: Form -->
                <div class="col-lg-5 col-xl-4 h-100 d-flex flex-column" style="background: white;">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-bold" id="modalTitleText" style="color: #0F172A;">New Design Template</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="p-4 overflow-auto flex-grow-1 form-container">
                        <form action="certificate_templates.php" method="POST" enctype="multipart/form-data" id="templateForm">
                            <input type="hidden" name="action" id="formAction" value="create_template">
                            <input type="hidden" name="template_id" id="formTemplateId" value="">
                            
                            <div class="mb-4">
                                <label class="form-label text-xs fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">TEMPLATE NAME</label>
                                <input type="text" name="template_name" id="inp_name" class="form-control form-control-lg" placeholder="e.g. CWTS Official Roster Template" required style="font-size: 0.95rem; border-radius: 8px;">
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label text-xs fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">PROGRAM / COMPONENT</label>
                                    <select name="program_type" id="inp_program" class="form-select form-select-lg" required style="font-size: 0.95rem; border-radius: 8px;" onchange="updatePreview()">
                                        <option value="CWTS">CWTS</option>
                                        <option value="LTS">LTS</option>
                                        <option value="ROTC">ROTC</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-xs fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">BACKGROUND IMAGE DESIGN <span class="text-lowercase fw-normal">(.png, .jpg)</span> <span style="color:#9CA3AF;font-size:0.65rem;text-transform:lowercase">(optional)</span></label>
                                    <input type="file" name="design_image" class="form-control form-control-lg" accept="image/*" style="font-size: 0.95rem; border-radius: 8px; padding: 6px 12px;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-xs fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">CERTIFICATE HEADER TITLE</label>
                                <input type="text" name="header_title" id="inp_header" class="form-control form-control-lg" placeholder="Certificate of Completion" value="Certificate of Completion" style="font-size: 0.95rem; border-radius: 8px;" oninput="updatePreview()">
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <label class="form-label text-xs fw-bold text-muted text-uppercase mb-0" style="letter-spacing: 0.5px; font-size: 0.75rem;">CERTIFICATE BODY STATEMENT</label>
                                    <span style="font-size: 0.7rem; color: #94A3B8;">Use tags: [STUDENT_NAME], [SECTION], [SCHOOL_YEAR], [SERIAL_NO]</span>
                                </div>
                                <textarea name="body_statement" id="inp_body" class="form-control" rows="5" style="font-size: 0.95rem; border-radius: 8px; resize: vertical;" oninput="updatePreview()">This is to certify that [STUDENT_NAME] has successfully completed the Civic Welfare Training Service (CWTS) component of the National Service Training Program (NSTP) during the school year [SCHOOL_YEAR] in section [SECTION], in compliance with Republic Act No. 9163.</textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label text-xs fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">SIGNATORY FULL NAME</label>
                                    <input type="text" name="signatory_name" id="inp_sig_name" class="form-control form-control-lg" placeholder="Dr. Emil F. Briones" style="font-size: 0.95rem; border-radius: 8px;" oninput="updatePreview()">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-xs fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">SIGNATORY SUBTITLE / TITLE</label>
                                    <input type="text" name="signatory_title" id="inp_sig_title" class="form-control form-control-lg" placeholder="NSTP Coordinator" style="font-size: 0.95rem; border-radius: 8px;" oninput="updatePreview()">
                                </div>
                            </div>
                            
                            <div class="form-check mb-4 mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="inp_active" value="1" style="width: 1.2rem; height: 1.2rem; margin-top: 0.15rem;">
                                <label class="form-check-label fw-semibold" for="inp_active" style="color: #334155; margin-left: 0.5rem;">
                                    Keep this certificate template active
                                </label>
                            </div>
                            
                        </form>
                    </div>

                    <div class="p-4 border-top d-flex justify-content-end gap-3" style="background: white;">
                        <button type="button" class="btn" style="background: transparent; color: #475569; font-weight: 600;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" form="templateForm" class="btn text-white px-4" style="background: #4F46E5; border-radius: 8px; font-weight: 600;">Save Template</button>
                    </div>
                </div>
                
                <!-- RIGHT COLUMN: Live Preview -->
                <div class="col-lg-7 col-xl-8 h-100 position-relative d-flex flex-column align-items-center justify-content-center" style="background: #0B1120;">
                    
                    <div style="position: absolute; top: 40px; left: 0; right: 0; text-align: center;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: #64748B; letter-spacing: 1px;">LIVE PREVIEW CANVAS</span>
                    </div>

                    <!-- The Certificate Preview Box -->
                    <div id="previewCanvas" class="shadow-lg" style="width: 90%; max-width: 900px; aspect-ratio: 1.414 / 1; background: white; border-radius: 12px; padding: 5%; position: relative; border: 3px solid #312E81; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                        
                        <div style="font-size: 0.9vw; color: #475569; margin-bottom: 2px;">REPUBLIC OF THE PHILIPPINES</div>
                        <div style="font-size: 1.1vw; font-weight: bold; color: #1E3A8A; margin-bottom: 5%;">DAVAO DEL NORTE STATE COLLEGE</div>
                        
                        <div id="prev_header" style="font-size: 3vw; font-weight: bold; color: #1E293B; margin-bottom: 6%; text-transform: uppercase;">CERTIFICATE OF COMPLETION</div>
                        
                        <div style="font-size: 1.2vw; color: #475569; margin-bottom: 2%;">This is to certify that</div>
                        
                        <div style="font-size: 2.8vw; font-weight: bold; color: #0F172A; text-transform: uppercase; margin-bottom: 3%;">JUAN DELA CRUZ</div>
                        
                        <div id="prev_body" style="font-size: 1vw; color: #334155; line-height: 1.8; max-width: 80%;">
                            has successfully completed the Civic Welfare Training Service (CWTS) component of the National Service Training Program (NSTP) during the school year 2025-2026 in section CWTS-1A, in compliance with Republic Act No. 9163.
                        </div>

                        <div style="position: absolute; bottom: 8%; width: 80%; display: flex; justify-content: space-between; align-items: flex-end;">
                            <div style="text-align: left;">
                                <div style="font-size: 0.8vw; color: #94A3B8;">S.N. 2024-00001</div>
                            </div>
                            <div style="text-align: center; min-width: 200px;">
                                <div id="prev_sig_name" style="font-size: 1.3vw; font-weight: bold; color: #1E293B; border-bottom: 1px solid #334155; padding-bottom: 2px; text-transform: uppercase;">DR. EMIL F. BRIONES</div>
                                <div id="prev_sig_title" style="font-size: 0.9vw; color: #64748B; margin-top: 4px;">NSTP Coordinator</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
.template-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
.form-control:focus, .form-select:focus, .form-check-input:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
}
.btn-close:focus {
    box-shadow: none;
}
/* Scrollbar styling for the form column */
.form-container::-webkit-scrollbar {
    width: 6px;
}
.form-container::-webkit-scrollbar-track {
    background: transparent;
}
.form-container::-webkit-scrollbar-thumb {
    background-color: #CBD5E1;
    border-radius: 10px;
}
</style>

<script>
function openModal(mode, data = null) {
    const modal = new bootstrap.Modal(document.getElementById('templateModal'));
    const title = document.getElementById('modalTitleText');
    const formAction = document.getElementById('formAction');
    const formTemplateId = document.getElementById('formTemplateId');
    
    // Reset form
    document.getElementById('templateForm').reset();
    
    if (mode === 'edit' && data) {
        title.innerText = 'Edit Design Template';
        formAction.value = 'edit_template';
        formTemplateId.value = data.id;
        
        document.getElementById('inp_name').value = data.name;
        document.getElementById('inp_program').value = data.program_type;
        document.getElementById('inp_header').value = data.header_title || '';
        document.getElementById('inp_body').value = data.body_statement || '';
        document.getElementById('inp_sig_name').value = data.signatory_name || '';
        document.getElementById('inp_sig_title').value = data.signatory_title || '';
        document.getElementById('inp_active').checked = (data.is_active == 1);
        
        // Also set badge color to match program (CWTS->Indigo, LTS->Emerald, ROTC->Rose) implicitly in backend or just default it
    } else {
        title.innerText = 'New Design Template';
        formAction.value = 'create_template';
        formTemplateId.value = '';
    }
    
    updatePreview();
    modal.show();
}

function updatePreview() {
    const header = document.getElementById('inp_header').value || 'CERTIFICATE TITLE';
    let body = document.getElementById('inp_body').value || 'Certificate body statement goes here...';
    const sigName = document.getElementById('inp_sig_name').value || 'SIGNATORY NAME';
    const sigTitle = document.getElementById('inp_sig_title').value || 'Signatory Title';
    
    // Replace tags in body for preview
    body = body.replace(/\[STUDENT_NAME\]/g, '<strong>JUAN DELA CRUZ</strong>');
    body = body.replace(/\[SECTION\]/g, '<strong>CWTS-1A</strong>');
    body = body.replace(/\[SCHOOL_YEAR\]/g, '<strong>2025-2026</strong>');
    body = body.replace(/\[SERIAL_NO\]/g, '<strong>2024-00001</strong>');

    document.getElementById('prev_header').innerText = header;
    document.getElementById('prev_body').innerHTML = body;
    document.getElementById('prev_sig_name').innerText = sigName;
    document.getElementById('prev_sig_title').innerText = sigTitle;
}

// Initial preview update
document.addEventListener('DOMContentLoaded', updatePreview);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
