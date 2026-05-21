<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROTC') {
    header("Location: ../login.php");
    exit;
}

$officer_id = $_SESSION['user_id'];

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create_platoon') {
        $platoon_name = trim($_POST['platoon_name']);
        $semester = trim($_POST['semester']);
        $school_year = date('Y') . '-' . (date('Y') + 1);

        try {
            // Insert Platoon
            $stmt = $pdo->prepare("INSERT INTO sections (component, section_name, school_year, semester, instructor_id) VALUES ('ROTC', ?, ?, ?, ?)");
            $stmt->execute([$platoon_name, $school_year, $semester, $officer_id]);
            $section_id = $pdo->lastInsertId();

            // Handle CSV Upload
            if (isset($_FILES['cadets_csv']) && $_FILES['cadets_csv']['error'] == 0) {
                $fileTemp = $_FILES['cadets_csv']['tmp_name'];
                if (($handle = fopen($fileTemp, "r")) !== FALSE) {
                    $header = fgetcsv($handle, 1000, ",");
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        // Expected CSV format: student_id, first_name, last_name, course, year_level, sex
                        if (count($data) >= 3) {
                            $student_id = trim($data[0]);
                            $first_name = trim($data[1]);
                            $last_name = trim($data[2]);
                            $course = trim($data[3] ?? '');
                            $year_level = intval($data[4] ?? 1);
                            $sex = trim($data[5] ?? 'Male');

                            // Insert or Ignore Student
                            $check = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
                            $check->execute([$student_id]);
                            if ($check->rowCount() == 0) {
                                $insStud = $pdo->prepare("INSERT INTO students (student_id, first_name, last_name, course, year_level, component, sex) VALUES (?, ?, ?, ?, ?, 'ROTC', ?)");
                                $insStud->execute([$student_id, $first_name, $last_name, $course, $year_level, $sex]);
                            }

                            // Insert Enrollment
                            $insEnr = $pdo->prepare("INSERT INTO enrollments (student_id, section_id) VALUES (?, ?)");
                            $insEnr->execute([$student_id, $section_id]);
                        }
                    }
                    fclose($handle);
                }
            }

            $_SESSION['success'] = "Platoon created successfully.";
            header("Location: platoon_management.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_platoon') {
        $section_id = $_POST['section_id'];
        $stmt = $pdo->prepare("DELETE FROM sections WHERE id = ?");
        $stmt->execute([$section_id]);
        $_SESSION['success'] = "Platoon deleted.";
        header("Location: platoon_management.php");
        exit;
    }
}

// Fetch Data
try {
    // Total assigned officers (unique instructors in ROTC sections)
    $s1 = $pdo->prepare("SELECT COUNT(DISTINCT instructor_id) FROM sections WHERE component='ROTC'");
    $s1->execute(); $total_officers = $s1->fetchColumn() ?: 0;

    // Active Platoons count
    $s2 = $pdo->prepare("SELECT COUNT(*) FROM sections WHERE component='ROTC'");
    $s2->execute(); $active_platoons = $s2->fetchColumn() ?: 0;

    // Fetch Platoons list
    $s3 = $pdo->prepare("SELECT s.*, (SELECT COUNT(*) FROM enrollments WHERE section_id=s.id) as cadet_count FROM sections s WHERE s.component='ROTC' ORDER BY s.id DESC");
    $s3->execute(); $platoons = $s3->fetchAll();

} catch (Exception $e) {
    $total_officers = 0;
    $active_platoons = 0;
    $platoons = [];
}

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/rotc_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../includes/topbar.php'; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h5 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.15rem;">Platoon Management Overview</h5>
            <div class="text-muted" style="font-size: 0.85rem;">Click a platoon to view its assigned officers or upload a master list.</div>
        </div>
        <div class="d-flex gap-3">
            <button class="btn" style="background: white; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: #475569; padding: 8px 16px; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                <i class="bi bi-funnel"></i> All Platoons <i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i>
            </button>
            <button class="btn" data-bs-toggle="modal" data-bs-target="#newPlatoonModal" style="background: #0F172A; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: white; padding: 8px 16px; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-shield"></i> New Platoon
            </button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <!-- Officers -->
        <div class="col-md-6">
            <div style="background: white; border: 1px solid #E2E8F0; border-radius: 12px; padding: 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #EEF2FF; color: #6366F1; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #0F172A; line-height: 1.2;"><?= $total_officers ?></div>
                    <div style="font-size: 0.8rem; color: #64748B;">Total Assigned Officers</div>
                </div>
            </div>
        </div>
        
        <!-- Active Platoons -->
        <div class="col-md-6">
            <div style="background: white; border: 1px solid #E2E8F0; border-radius: 12px; padding: 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #FFFBEB; color: #F59E0B; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="bi bi-shield"></i>
                </div>
                <div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #0F172A; line-height: 1.2;"><?= $active_platoons ?></div>
                    <div style="font-size: 0.8rem; color: #64748B;">Active Platoons</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Platoon List Panel -->
    <div style="background: white; border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
        <!-- Search -->
        <div style="padding: 16px 24px; border-bottom: 1px solid #F1F5F9;">
            <div class="position-relative" style="width: 320px;">
                <i class="bi bi-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                <input type="text" class="form-control" placeholder="Search platoons..." style="padding-left: 36px; border-radius: 8px; font-size: 0.85rem; border: 1px solid #E2E8F0; box-shadow: none; background: #F8FAFC;">
            </div>
        </div>
        
        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-borderless mb-0" style="font-size: 0.85rem;">
                <thead style="border-bottom: 1px solid #F1F5F9;">
                    <tr>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Platoon Name</th>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Total Cadets</th>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Status</th>
                    </tr>
                </thead>
                    <?php if(count($platoons) == 0): ?>
                    <tr><td colspan="3" class="text-center py-4 text-muted">No platoons found.</td></tr>
                    <?php else: foreach($platoons as $p): ?>
                    <tr style="border-bottom: 1px solid #F8FAFC; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'" data-bs-toggle="modal" data-bs-target="#platoonModal<?= $p['id'] ?>">
                        <td style="padding: 20px 24px; font-weight: 600; color: #0F172A;"><?= htmlspecialchars($p['section_name']) ?></td>
                        <td style="padding: 20px 24px; color: #475569;"><?= $p['cadet_count'] ?> Cadets</td>
                        <td style="padding: 20px 24px;">
                            <?php if($p['semester'] === '1st'): ?>
                                <span style="background: #ECFDF5; color: #10B981; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">1st Semester</span>
                            <?php else: ?>
                                <span style="background: #FFFBEB; color: #F59E0B; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">2nd Semester</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
            </table>
        </div>
    </div>

</div>

<!-- New Platoon Modal -->
<div class="modal fade" id="newPlatoonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <form method="POST" action="platoon_management.php" enctype="multipart/form-data" class="modal-content" style="border: none; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <input type="hidden" name="action" value="create_platoon">
            <div class="modal-header border-0" style="padding: 24px 24px 16px;">
                <div>
                    <h5 class="modal-title fw-bold mb-1" style="color: #0F172A; font-size: 1.15rem;">New Platoon</h5>
                    <div class="text-muted" style="font-size: 0.85rem;">Fill in the details to add a new ROTC platoon</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
            <div class="modal-body" style="padding: 16px 24px;">
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Platoon Name</label>
                        <input type="text" name="platoon_name" class="form-control" placeholder="e.g. Delta" required style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Status</label>
                        <select name="semester" class="form-select" required style="border-radius: 8px; border-color: #6366F1; font-size: 0.85rem; padding: 10px 12px; box-shadow: none; color: #0F172A; background-color: #F8FAFC;">
                            <option value="1st">1st Semester</option>
                            <option value="2nd">2nd Semester</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-2">
                    <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Import Cadets List CSV File (Optional)</label>
                    <input type="file" name="cadets_csv" class="form-control" accept=".csv" style="border-radius: 8px; border: 1px solid #E2E8F0; font-size: 0.85rem; padding: 10px 12px;">
                    <small class="text-muted d-block mt-2" style="font-size: 0.7rem;">Format: student_id, first_name, last_name, course, year_level, sex</small>
                </div>
            </div>
            <div class="modal-footer border-0" style="padding: 16px 24px 24px;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: #475569; border: 1px solid #E2E8F0; background: white;">Cancel</button>
                <button type="submit" class="btn btn-dark" style="padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; background: #0F172A; border: none; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-shield"></i> Create Platoon
                </button>
            </div>
        </form>
    </div>
</div>

<?php foreach($platoons as $p): ?>
<!-- Platoon Modal <?= $p['id'] ?> -->
<div class="modal fade" id="platoonModal<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            
            <!-- Dark Header -->
            <div class="modal-header border-0 d-flex justify-content-between align-items-start" style="background: #0F172A; padding: 24px; border-radius: 12px 12px 0 0;">
                <div>
                    <h4 class="modal-title fw-bold mb-1" style="color: white; font-size: 1.25rem;"><?= htmlspecialchars($p['section_name']) ?> — Assign Cadets Section</h4>
                    <div style="color: #94A3B8; font-size: 0.85rem;"><?= $p['cadet_count'] ?> assigned</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <form method="POST" action="platoon_management.php" onsubmit="return confirm('Are you sure you want to delete this platoon?');">
                        <input type="hidden" name="action" value="delete_platoon">
                        <input type="hidden" name="section_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-outline-danger" style="border-radius: 8px; font-size: 0.8rem; font-weight: 500; padding: 6px 16px; border-color: rgba(239,68,68,0.3); color: #FCA5A5; display: flex; align-items: center; gap: 6px;">
                            <i class="bi bi-trash3"></i> Delete Platoon
                        </button>
                    </form>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            
            <!-- Table -->
            <div class="modal-body p-0" style="background: white; max-height: 400px; overflow-y: auto;">
                <table class="table table-borderless mb-0" style="font-size: 0.85rem;">
                    <thead style="position: sticky; top: 0; background: white; border-bottom: 1px solid #F1F5F9; z-index: 1;">
                        <tr>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">ID</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Name</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Course</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Year Level</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Gender</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Cell #</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $st = $pdo->prepare("SELECT s.* FROM students s JOIN enrollments e ON s.student_id=e.student_id WHERE e.section_id=?");
                        $st->execute([$p['id']]);
                        $cadets = $st->fetchAll();
                        if (count($cadets) == 0):
                        ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No cadets assigned to this platoon yet.</td></tr>
                        <?php else: foreach($cadets as $c): ?>
                        <tr style="border-bottom: 1px solid #F8FAFC; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 16px; color: #64748B;"><?= htmlspecialchars($c['student_id']) ?></td>
                            <td style="padding: 16px; font-weight: 600; color: #0F172A;"><?= htmlspecialchars($c['last_name'].', '.$c['first_name']) ?></td>
                            <td style="padding: 16px; color: #64748B;"><?= htmlspecialchars($c['course'] ?: '—') ?></td>
                            <td style="padding: 16px; color: #64748B;"><?= htmlspecialchars($c['year_level'] ?: '—') ?></td>
                            <td style="padding: 16px; color: #64748B;"><?= htmlspecialchars($c['sex'] ?: '—') ?></td>
                            <td style="padding: 16px; color: #64748B;"><?= htmlspecialchars($c['contact_number'] ?: '—') ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
