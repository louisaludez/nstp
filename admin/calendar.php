<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';
$msgType = '';

// Handle form submission for a new activity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_activity'])) {
    $title = trim($_POST['title']);
    $component = $_POST['component'];
    $date = $_POST['activity_date'];
    $time = $_POST['activity_time'];
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO activities (title, component, activity_date, activity_time, location, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $component, $date, $time, $location, $description]);
        $message = "Activity successfully scheduled.";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Fetch upcoming activities for the right panel (Ordering by date, only showing future/today)
$stmtActivities = $pdo->query("SELECT * FROM activities WHERE activity_date >= CURDATE() ORDER BY activity_date ASC, activity_time ASC");
$upcoming_activities = $stmtActivities->fetchAll();

// --- PHP Calendar Logic ---
// Get current month and year (or specific if passed via URL)
$month = date('m');
$year = date('Y');

// Calculate days in month and what day of the week the 1st falls on
$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
$first_day_of_month = date('w', mktime(0, 0, 0, $month, 1, $year));

// Fetch all activity dates for this specific month to highlight the calendar squares
$stmtDates = $pdo->prepare("SELECT DISTINCT DAY(activity_date) as act_day FROM activities WHERE MONTH(activity_date) = ? AND YEAR(activity_date) = ?");
$stmtDates->execute([$month, $year]);
$active_days = $stmtDates->fetchAll(PDO::FETCH_COLUMN);

include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<style>
    /* UI Matching Styles */
    .btn-brand { background-color: var(--primary-active, #4A46D6); color: white; font-weight: 500; border-radius: 8px; padding: 8px 16px; }
    .btn-brand:hover { background-color: var(--primary-bg, #2B2866); color: white; }
    .btn-outline-cancel { border: 1px solid #D1D5DB; color: #111827; font-weight: 500; background-color: white; }
    .btn-outline-cancel:hover { background-color: #F3F4F6; }
    
    .panel-container { background-color: #fff; border: 1px solid #E5E7EB; border-radius: 16px; padding: 24px; height: 100%; }
    
    .card-activity { border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-bottom: 16px; transition: 0.2s; }
    .card-activity:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.03); border-color: #D1D5DB; }
    
    /* Component Badges */
    .badge-cwts { background-color: #E0E7FF; color: #4338CA; padding: 4px 10px; font-size: 0.75rem; border-radius: 12px; }
    .badge-lts { background-color: #D1FAE5; color: #059669; padding: 4px 10px; font-size: 0.75rem; border-radius: 12px; }
    .badge-rotc { background-color: #FEE2E2; color: #DC2626; padding: 4px 10px; font-size: 0.75rem; border-radius: 12px; }
    
    /* Calendar Grid Styles */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 12px;
        text-align: center;
        margin-top: 20px;
    }
    .calendar-header-day { font-weight: 500; color: #6B7280; font-size: 0.9rem; padding-bottom: 10px; }
    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-weight: 500;
        color: #111827;
        font-size: 0.95rem;
    }
    .calendar-day.empty { background-color: transparent; }
    .calendar-day.active-day {
        background-color: #E0E7FF; /* The light purple from the Figma mockup */
        color: #4A46D6;
        font-weight: 600;
    }
    
    /* Form Inputs */
    .modal-form-control { border-radius: 8px; border: 1px solid #D1D5DB; padding: 10px 14px; font-size: 0.95rem; }
    .modal-form-control:focus { border-color: var(--primary-active, #4A46D6); box-shadow: 0 0 0 3px rgba(74, 70, 214, 0.1); outline: none; }
</style>

<div class="flex-grow-1 p-5 w-100">
    
    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-1">
        <div>
            <h3 class="fw-bold mb-0" style="color: #111827;">Calendar & Activities</h3>
            <p class="text-muted mb-4">Schedule and manage NSTP activities</p>
        </div>
        <button type="button" class="btn btn-brand border-0" data-bs-toggle="modal" data-bs-target="#addActivityModal">
            <i class="bi bi-plus-lg me-1"></i> Add Activity
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <div class="col-xl-7 col-lg-7">
            <div class="panel-container">
                <h5 class="fw-bold mb-4" style="color: #111827;">
                    <i class="bi bi-calendar4 text-primary me-2"></i> Calendar View
                </h5>
                
                <div class="calendar-grid">
                    <div class="calendar-header-day">Sun</div>
                    <div class="calendar-header-day">Mon</div>
                    <div class="calendar-header-day">Tue</div>
                    <div class="calendar-header-day">Wed</div>
                    <div class="calendar-header-day">Thu</div>
                    <div class="calendar-header-day">Fri</div>
                    <div class="calendar-header-day">Sat</div>
                    
                    <?php
                    // Print empty slots for days before the 1st of the month
                    for ($i = 0; $i < $first_day_of_month; $i++) {
                        echo '<div class="calendar-day empty"></div>';
                    }
                    
                    // Print the actual days
                    for ($day = 1; $day <= $days_in_month; $day++) {
                        $is_active = in_array($day, $active_days) ? 'active-day' : '';
                        echo "<div class='calendar-day $is_active'>$day</div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-5">
            <div class="panel-container" style="background-color: #F9FAFB;">
                <h5 class="fw-bold mb-4" style="color: #111827;">Upcoming Activities</h5>
                
                <?php if (count($upcoming_activities) > 0): ?>
                    <?php foreach ($upcoming_activities as $act): ?>
                        <div class="card-activity bg-white">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0" style="color: #111827;"><?= htmlspecialchars($act['title']) ?></h6>
                                
                                <?php if ($act['component'] === 'CWTS'): ?>
                                    <span class="badge badge-cwts fw-medium">CWTS</span>
                                <?php elseif ($act['component'] === 'LTS'): ?>
                                    <span class="badge badge-lts fw-medium">LTS</span>
                                <?php elseif ($act['component'] === 'ROTC'): ?>
                                    <span class="badge badge-rotc fw-medium">ROTC</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="text-muted small mt-3">
                                <div class="mb-1"><i class="bi bi-calendar3 me-2"></i> <?= date('m/d/Y', strtotime($act['activity_date'])) ?></div>
                                <div class="mb-1"><i class="bi bi-clock me-2"></i> <?= date('h:i A', strtotime($act['activity_time'])) ?></div>
                                <div class="mb-2"><i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($act['location']) ?></div>
                                <div><i class="bi bi-people me-2"></i> <?= rand(40, 120) ?> participants</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5 bg-white rounded-3 border" style="border-color: #E5E7EB;">
                        <i class="bi bi-calendar-x text-muted fs-1 mb-2"></i>
                        <p class="text-muted small mb-0">No upcoming activities scheduled.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="addActivityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px; max-width: 450px; margin: auto;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 mt-1">
                <h4 class="modal-title fw-bold" style="color: #111827;">Add Activity</h4>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">Activity Title</label>
                        <input type="text" name="title" class="form-control modal-form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">Component</label>
                        <select name="component" class="form-select modal-form-control" required>
                            <option value="CWTS">CWTS</option>
                            <option value="LTS">LTS</option>
                            <option value="ROTC">ROTC</option>
                        </select>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-dark fw-medium small mb-1">Date</label>
                            <input type="date" name="activity_date" class="form-control modal-form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-dark fw-medium small mb-1">Time</label>
                            <input type="time" name="activity_time" class="form-control modal-form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">Location</label>
                        <input type="text" name="location" class="form-control modal-form-control" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-dark fw-medium small mb-1">Description</label>
                        <textarea name="description" class="form-control modal-form-control" rows="3"></textarea>
                    </div>

                </div>
                <div class="modal-footer border-top-0 pt-0 pe-4 pb-4">
                    <button type="button" class="btn btn-outline-cancel rounded-3 px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_activity" class="btn btn-brand rounded-3 px-4 py-2">Add Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>