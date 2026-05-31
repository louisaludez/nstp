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
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Manage and customize certificate layouts for different programs</p>
        </div>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" style="background: #4F46E5; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500;" data-bs-toggle="modal" data-bs-target="#newTemplateModal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Template
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
                    <div class="card h-100 template-card" style="border-radius: 16px; border: 1px solid #E5E7EB; overflow: hidden; background: white;">
                        <div class="p-3">
                            <div class="template-image-container" style="height: 180px; border-radius: 12px; border: 2px dashed #E5E7EB; background-color: #F9FAFB; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <?php if (!empty($template['image_path']) && file_exists('../' . $template['image_path'])): ?>
                                    <img src="../<?= htmlspecialchars($template['image_path']) ?>" alt="Template Image" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 48px; height: 48px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body pt-0 pb-3 px-4 d-flex flex-column">
                            <h5 class="card-title fw-bold" style="color: #111827; font-size: 1.05rem; margin-bottom: 2px;"><?= htmlspecialchars($template['name']) ?></h5>
                            <p class="card-text text-muted" style="font-size: 0.8rem; margin-bottom: 12px;"><?= htmlspecialchars($template['program_type']) ?></p>
                            
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span style="font-size: 0.7rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; <?= getBadgeStyle($template['badge_color']) ?>">
                                    <?= htmlspecialchars($template['status']) ?>
                                </span>
                                <button type="button" class="btn btn-link p-0 text-decoration-none edit-btn" style="color: #4F46E5; font-size: 0.85rem; font-weight: 600;" 
                                    data-bs-toggle="modal" data-bs-target="#editTemplateModal"
                                    data-id="<?= $template['id'] ?>"
                                    data-name="<?= htmlspecialchars($template['name']) ?>"
                                    data-program="<?= htmlspecialchars($template['program_type']) ?>"
                                    data-color="<?= htmlspecialchars($template['badge_color']) ?>">
                                    Edit
                                </button>
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

<!-- New Template Modal -->
<div class="modal fade" id="newTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered border-0">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);">
            <div class="modal-header border-0 pb-0 pt-4 px-4 position-relative">
                <h5 class="modal-title fw-bold" style="color: #111827;">New Template</h5>
                <button type="button" class="btn-close rounded-circle" style="background-color: #F3F4F6; padding: 0.5rem; position: absolute; right: 20px; top: 20px;" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="px-4 pb-3">
                <p class="text-muted" style="font-size: 0.85rem; margin-bottom: 0;">Add a new certificate layout</p>
            </div>
            <div class="modal-body px-4 pt-2 pb-4">
                <form action="certificate_templates.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create_template">
                    
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">TEMPLATE NAME</label>
                        <input type="text" name="template_name" class="form-control" placeholder="e.g. Standard Completion" required style="border-radius: 8px; border: 1px solid #D1D5DB; padding: 10px 14px; font-size: 0.95rem;">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">PROGRAM TYPE</label>
                            <select name="program_type" class="form-select" required style="border-radius: 8px; border: 1px solid #D1D5DB; padding: 10px 14px; font-size: 0.95rem;">
                                <option value="All Programs">All Programs</option>
                                <option value="CWTS/LTS">CWTS / LTS</option>
                                <option value="ROTC">ROTC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">BADGE COLOR</label>
                            <select name="badge_color" class="form-select" required style="border-radius: 8px; border: 1px solid #D1D5DB; padding: 10px 14px; font-size: 0.95rem;">
                                <option value="Indigo">Indigo</option>
                                <option value="Emerald">Emerald</option>
                                <option value="Amber">Amber</option>
                                <option value="Rose">Rose</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">DESIGN IMAGE</label>
                        <input type="file" name="design_image" class="form-control" accept="image/*" style="border-radius: 8px; border: 1px solid #D1D5DB; padding: 8px 14px; font-size: 0.95rem;">
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-2">
                        <button type="button" class="btn" style="background: white; border: 1px solid #D1D5DB; border-radius: 8px; padding: 8px 16px; font-weight: 500; color: #374151;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500;">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Template Modal -->
<div class="modal fade" id="editTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered border-0">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);">
            <div class="modal-header border-0 pb-0 pt-4 px-4 position-relative">
                <h5 class="modal-title fw-bold" style="color: #111827;">Edit Template</h5>
                <button type="button" class="btn-close rounded-circle" style="background-color: #F3F4F6; padding: 0.5rem; position: absolute; right: 20px; top: 20px;" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="px-4 pb-3">
                <p class="text-muted" style="font-size: 0.85rem; margin-bottom: 0;">Update existing template details</p>
            </div>
            <div class="modal-body px-4 pt-2 pb-4">
                <form action="certificate_templates.php" method="POST" enctype="multipart/form-data" id="editForm">
                    <input type="hidden" name="action" value="edit_template">
                    <input type="hidden" name="template_id" id="edit_template_id">
                    
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">TEMPLATE NAME</label>
                        <input type="text" name="template_name" id="edit_template_name" class="form-control" required style="border-radius: 8px; border: 1px solid #D1D5DB; padding: 10px 14px; font-size: 0.95rem;">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">PROGRAM TYPE</label>
                            <select name="program_type" id="edit_program_type" class="form-select" required style="border-radius: 8px; border: 1px solid #D1D5DB; padding: 10px 14px; font-size: 0.95rem;">
                                <option value="All Programs">All Programs</option>
                                <option value="CWTS/LTS">CWTS / LTS</option>
                                <option value="ROTC">ROTC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">BADGE COLOR</label>
                            <select name="badge_color" id="edit_badge_color" class="form-select" required style="border-radius: 8px; border: 1px solid #D1D5DB; padding: 10px 14px; font-size: 0.95rem;">
                                <option value="Indigo">Indigo</option>
                                <option value="Emerald">Emerald</option>
                                <option value="Amber">Amber</option>
                                <option value="Rose">Rose</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">DESIGN IMAGE</label>
                        <input type="file" name="design_image" class="form-control" accept="image/*" style="border-radius: 8px; border: 1px solid #D1D5DB; padding: 8px 14px; font-size: 0.95rem;">
                        <small class="text-muted d-block mt-1">Leave empty to keep existing image</small>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                        <button type="button" class="btn p-0" style="color: #EF4444; font-weight: 500; font-size: 0.95rem;" onclick="document.getElementById('deleteForm').submit();">Delete</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn" style="background: white; border: 1px solid #D1D5DB; border-radius: 8px; padding: 8px 16px; font-weight: 500; color: #374151;" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn" style="background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500;">Save Template</button>
                        </div>
                    </div>
                </form>
                
                <!-- Hidden delete form -->
                <form id="deleteForm" action="certificate_templates.php" method="POST" style="display: none;">
                    <input type="hidden" name="action" value="delete_template">
                    <input type="hidden" name="template_id" id="delete_template_id">
                </form>
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
.form-control:focus, .form-select:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
}
.btn-close:focus {
    box-shadow: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.edit-btn');
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_template_id').value = this.dataset.id;
            document.getElementById('delete_template_id').value = this.dataset.id;
            document.getElementById('edit_template_name').value = this.dataset.name;
            document.getElementById('edit_program_type').value = this.dataset.program;
            document.getElementById('edit_badge_color').value = this.dataset.color;
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
