<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

require 'controllers/ActivityPlansController.php';

// Fetch sections for the dropdown
$stmtAllSections = $pdo->prepare("SELECT id, component, section_name FROM sections WHERE instructor_id = ? ORDER BY component, section_name");
$stmtAllSections->execute([$instructor_id]);
$all_assigned_sections = $stmtAllSections->fetchAll();

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background-color: #F9FAFB; min-height: 100vh;">
    
    <?php include '../includes/topbar.php'; ?>
    
    <!-- Header -->
    <div class="mb-4 mt-2">
        <h5 class="fw-bold mb-1" style="color: #111827; font-size: 1.15rem;">Activity Plans</h5>
        <div class="text-muted" style="font-size: 0.85rem;">Plan, schedule, and submit activities for coordinator approval</div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left Panel: Upcoming & Drafts -->
        <div class="col-lg-8">
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); height: 100%;">
                <div style="padding: 20px 24px; border-bottom: 1px solid #F3F4F6;">
                    <h6 class="fw-bold mb-0" style="color: #374151;">Upcoming & Drafts</h6>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-borderless mb-0" style="font-size: 0.85rem; color: #4B5563;">
                        <thead style="border-bottom: 1px solid #F3F4F6;">
                            <tr>
                                <th class="text-muted fw-semibold" style="padding: 16px 24px; font-size: 0.7rem; letter-spacing: 0.05em; text-transform: uppercase;">Activity</th>
                                <th class="text-muted fw-semibold" style="padding: 16px 24px; font-size: 0.7rem; letter-spacing: 0.05em; text-transform: uppercase;">Section</th>
                                <th class="text-muted fw-semibold" style="padding: 16px 24px; font-size: 0.7rem; letter-spacing: 0.05em; text-transform: uppercase;">Date</th>
                                <th class="text-muted fw-semibold" style="padding: 16px 24px; font-size: 0.7rem; letter-spacing: 0.05em; text-transform: uppercase;">Venue</th>
                                <th class="text-muted fw-semibold" style="padding: 16px 24px; font-size: 0.7rem; letter-spacing: 0.05em; text-transform: uppercase;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($plans) > 0): ?>
                                <?php foreach ($plans as $plan): 
                                    // Fetch section name for the row
                                    $secName = '';
                                    foreach ($all_assigned_sections as $sec) {
                                        if ($sec['id'] == $plan['section_id']) {
                                            $secName = $sec['component'] . ' ' . $sec['section_name'];
                                            break;
                                        }
                                    }
                                    
                                    // Style badges
                                    $badgeStyle = 'background: #F3F4F6; color: #4B5563;'; // Default Draft
                                    $icon = '';
                                    if ($plan['status'] === 'Approved') {
                                        $badgeStyle = 'background: #ECFDF5; color: #10B981;';
                                    } elseif ($plan['status'] === 'Pending') {
                                        $badgeStyle = 'background: #F3E8FF; color: #8B5CF6;';
                                    } elseif ($plan['status'] === 'Rejected' || $plan['status'] === 'Revision') {
                                        $badgeStyle = 'background: #FFFBEB; color: #F59E0B;';
                                        $icon = '<i class="bi bi-exclamation-circle" style="margin-right: 4px;"></i>';
                                    }
                                ?>
                                <tr style="border-bottom: 1px solid #F3F4F6;">
                                    <td style="padding: 16px 24px; font-weight: 500; color: #111827;"><?= htmlspecialchars($plan['title']) ?></td>
                                    <td style="padding: 16px 24px;"><?= htmlspecialchars($secName ?: 'N/A') ?></td>
                                    <td style="padding: 16px 24px;"><?= date('M j, Y', strtotime($plan['scheduled_date'])) ?></td>
                                    <td style="padding: 16px 24px;"><?= htmlspecialchars($plan['location']) ?></td>
                                    <td style="padding: 16px 24px;">
                                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 500; display: inline-flex; align-items: center; <?= $badgeStyle ?>">
                                            <?= $icon ?><?= htmlspecialchars($plan['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No upcoming activity plans found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Panel: Plan Template Form -->
        <div class="col-lg-4">
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
                <div style="padding: 20px 24px; border-bottom: 1px solid #F3F4F6;">
                    <h6 class="fw-bold mb-0" style="color: #374151;">Plan Template</h6>
                </div>
                
                <form method="POST" action="" enctype="multipart/form-data" style="padding: 24px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #6B7280; margin-bottom: 6px;">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Tree-Planting Drive" style="border-radius: 8px; border-color: #E5E7EB; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #6B7280; margin-bottom: 6px;">Date</label>
                        <input type="date" name="scheduled_date" class="form-control" style="border-radius: 8px; border-color: #E5E7EB; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #6B7280; margin-bottom: 6px;">Section</label>
                        <select name="section_id" class="form-select" style="border-radius: 8px; border-color: #E5E7EB; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;" required>
                            <?php foreach ($all_assigned_sections as $sec): ?>
                                <option value="<?= $sec['id'] ?>">
                                    <?= htmlspecialchars($sec['component'] . ' 1 - Section ' . substr($sec['section_name'], -1)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Hidden fields to support existing controller logic -->
                    <input type="hidden" name="description" value="Created via Quick Template">
                    <input type="hidden" name="location" value="TBA">
                    
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #6B7280; margin-bottom: 6px;">Objectives</label>
                        <textarea name="objectives" class="form-control" rows="3" placeholder="State 2-3 measurable objectives..." style="border-radius: 8px; border-color: #E5E7EB; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;" required></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #6B7280; margin-bottom: 6px;">Attachments</label>
                        <input type="file" name="supporting_files[]" id="fileAttachment" multiple class="d-none" onchange="updateFileNames(this)">
                        <div onclick="document.getElementById('fileAttachment').click()" style="border: 1px dashed #A7F3D0; background-color: #F0FDF4; border-radius: 8px; padding: 16px; text-align: center; cursor: pointer; transition: all 0.2s;">
                            <i class="bi bi-upload" style="color: #10B981; margin-right: 6px;"></i>
                            <span style="color: #10B981; font-size: 0.8rem; font-weight: 500;">Add File</span>
                        </div>
                        <div id="fileList" class="mt-2 text-muted" style="font-size: 0.75rem;"></div>
                    </div>
                    

                    <div class="d-flex gap-2">
                        <button type="submit" name="save_draft" class="btn btn-light" style="padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: #374151; border: 1px solid #E5E7EB; background: white;">Save Draft</button>
                        <button type="submit" name="submit_plan" class="btn btn-success" style="padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; background: #059669; border: none;">Submit for Approval</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateFileNames(input) {
    const fileList = document.getElementById('fileList');
    if (input.files.length > 0) {
        let names = [];
        for (let i = 0; i < input.files.length; i++) {
            names.push(input.files[i].name);
        }
        fileList.innerHTML = names.join(', ');
    } else {
        fileList.innerHTML = '';
    }
}
</script>
</body>
</html>
