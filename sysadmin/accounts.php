<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'System Admin') {
    header("Location: ../login.php");
    exit;
}

$error = '';
$success = '';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';

    if ($action === 'create') {
        $full_name = trim($_POST['full_name'] ?? '');
        $contact_number = trim($_POST['contact_number'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $degree_type = $_POST['degree_type'] ?? '';
        $degree_title = trim($_POST['degree_title'] ?? '');
        $role = $_POST['role'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($full_name && $email && $role && $password) {
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (full_name, contact_number, email, degree_type, degree_title, role, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$full_name, $contact_number, $email, $degree_type, $degree_title, $role, $hashed_password]);
                $success = "Account created successfully.";
            } catch (PDOException $e) {
                $error = "Error creating account. Email might already exist.";
            }
        } else {
            $error = "Please fill in all required fields.";
        }
    } elseif ($action === 'edit') {
        $id = $_POST['user_id'] ?? 0;
        $full_name = trim($_POST['full_name'] ?? '');
        $contact_number = trim($_POST['contact_number'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $degree_type = $_POST['degree_type'] ?? '';
        $degree_title = trim($_POST['degree_title'] ?? '');
        $role = $_POST['role'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($id && $full_name && $email && $role) {
            try {
                if ($password) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, contact_number = ?, email = ?, degree_type = ?, degree_title = ?, role = ?, password = ? WHERE id = ?");
                    $stmt->execute([$full_name, $contact_number, $email, $degree_type, $degree_title, $role, $hashed_password, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, contact_number = ?, email = ?, degree_type = ?, degree_title = ?, role = ? WHERE id = ?");
                    $stmt->execute([$full_name, $contact_number, $email, $degree_type, $degree_title, $role, $id]);
                }
                $success = "Account updated successfully.";
            } catch (PDOException $e) {
                $error = "Error updating account. Email might already exist.";
            }
        } else {
            $error = "Please fill in all required fields.";
        }
    } elseif ($action === 'delete') {
        $id = $_POST['user_id'] ?? 0;
        if ($id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $success = "Account deleted successfully.";
            } catch (PDOException $e) {
                $error = "Error deleting account.";
            }
        }
    }
}

// Fetch all registered accounts
$stmt = $pdo->query("SELECT * FROM users WHERE role != 'System Admin' ORDER BY id DESC");
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/sysadmin_sidebar.php';
?>
<style>
/* Reset and Base Styles */
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    background-color: #F8FAFC; /* Light gray background */
}

/* Main Content Area */
.main-content {
    padding: 2rem;
    display: flex;
    gap: 2rem;
    align-items: flex-start;
}

/* Create Account Card */
.create-card {
    background: #FFFFFF;
    border-radius: 12px;
    padding: 2rem;
    width: 450px;
    flex-shrink: 0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    border: 1px solid #F1F5F9;
}
.create-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}
.create-icon {
    width: 48px;
    height: 48px;
    background-color: #F5F3FF;
    color: #8B5CF6;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.create-title h2 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #0F172A;
    margin: 0 0 0.25rem 0;
}
.create-title p {
    font-size: 0.75rem;
    color: #64748B;
    margin: 0;
}

.form-group {
    margin-bottom: 1.25rem;
}
.form-group label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}
.input-with-icon {
    position: relative;
}
.input-with-icon i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94A3B8;
}
.input-with-icon input, .form-group select, .form-group input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    font-size: 0.875rem;
    color: #1E293B;
    transition: all 0.2s;
    outline: none;
}
.input-with-icon input {
    padding-left: 36px;
}
.input-with-icon input:focus, .form-group select:focus, .form-group input:focus {
    border-color: #8B5CF6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}
.form-row {
    display: flex;
    gap: 1rem;
}
.form-row .form-group {
    flex: 1;
}

.btn-submit {
    width: 100%;
    background-color: #8B5CF6;
    color: #FFFFFF;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: background-color 0.2s;
    margin-top: 1rem;
}
.btn-submit:hover {
    background-color: #7C3AED;
}

/* Registry Card */
.registry-card {
    background: #FFFFFF;
    border-radius: 12px;
    padding: 2rem;
    flex-grow: 1;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    border: 1px solid #F1F5F9;
}
.registry-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}
.registry-title h2 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #0F172A;
    margin: 0 0 0.25rem 0;
}
.registry-title p {
    font-size: 0.875rem;
    color: #64748B;
    margin: 0;
}
.total-users {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 0.75rem;
    color: #475569;
    font-weight: 500;
}

table {
    width: 100%;
    border-collapse: collapse;
}
th {
    text-align: left;
    padding: 1rem 0.5rem;
    font-size: 0.65rem;
    font-weight: 600;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #E2E8F0;
}
td {
    padding: 1rem 0.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #F1F5F9;
}
tr:last-child td {
    border-bottom: none;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
    color: #FFFFFF;
}
.user-info {
    display: flex;
    flex-direction: column;
}
.user-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1E293B;
}
.user-email {
    font-size: 0.75rem;
    color: #64748B;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 500;
}
.role-admin { background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; }
.role-instructor { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
.role-rotc { background: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; }

.degree-info {
    display: flex;
    flex-direction: column;
}
.degree-type {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1E293B;
}
.degree-title {
    font-size: 0.75rem;
    color: #64748B;
}

.action-btns {
    display: flex;
    gap: 0.5rem;
}
.btn-action {
    background: none;
    border: none;
    color: #94A3B8;
    cursor: pointer;
    font-size: 1rem;
    transition: color 0.2s;
}
.btn-action:hover {
    color: #475569;
}

@media (max-width: 1200px) {
    .main-content {
        flex-direction: column;
    }
    .create-card {
        width: 100%;
    }
}
</style>

<div class="flex-grow-1 w-100">
    <?php include '../includes/topbar.php'; ?>

    <div class="main-content">
        <!-- Create Account Section -->
        <div class="create-card">
            <div class="create-header">
                <div class="create-icon">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div class="create-title">
                    <h2>Create Account</h2>
                    <p>Register new system coordinator or instructor</p>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger p-2" style="font-size: 0.85rem; border-radius: 8px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success p-2" style="font-size: 0.85rem; border-radius: 8px;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-with-icon">
                        <i class="bi bi-person"></i>
                        <input type="text" name="full_name" placeholder="e.g. Dr. Juan Dela Cruz" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <div class="input-with-icon">
                        <i class="bi bi-telephone"></i>
                        <input type="text" name="contact_number" placeholder="e.g. +63 917 123 4567">
                    </div>
                </div>

                <div class="form-group">
                    <label>Gmail Address (Login Email)</label>
                    <div class="input-with-icon">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" placeholder="e.g. j.delacruz@dnsc.edu.ph" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Degree Type</label>
                        <select name="degree_type">
                            <option value="">Select Degree Type</option>
                            <option value="Bachelor">Bachelor</option>
                            <option value="Masteral">Masteral</option>
                            <option value="Doctoral">Doctoral</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Account Role</label>
                        <select name="role" required>
                            <option value="">Select Role</option>
                            <option value="Admin">NSTP Coordinator</option>
                            <option value="Instructor">CWTS/LTS Instructor</option>
                            <option value="ROTC">ROTC 1st Class Officer</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Degree Description / Title</label>
                    <div class="input-with-icon">
                        <i class="bi bi-mortarboard"></i>
                        <input type="text" name="degree_title" placeholder="e.g. Master of Science in Information Technology">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-with-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" placeholder="Create a secure password" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="bi bi-person-plus-fill"></i> Create Account
                </button>
            </form>
        </div>

        <!-- Registry Section -->
        <div class="registry-card">
            <div class="registry-header">
                <div class="registry-title">
                    <h2>Registered Accounts Registry</h2>
                    <p>Manage administrative credentials & profile details</p>
                </div>
                <div class="total-users">
                    Total: <?= count($accounts) ?> User(s)
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>User Details</th>
                        <th>Assigned Role</th>
                        <th>Contact No</th>
                        <th>Degree Info</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $colors = ['#3B82F6', '#10B981', '#1E293B', '#F59E0B', '#8B5CF6'];
                    foreach ($accounts as $index => $acc): 
                        $nameParts = explode(' ', str_replace(['Dr. ', 'Prof. '], '', $acc['full_name']));
                        $initials = count($nameParts) > 1 
                            ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
                            : strtoupper(substr($acc['full_name'], 0, 2));
                        $bg = $colors[$index % count($colors)];
                        
                        $roleClass = '';
                        $roleIcon = '';
                        $roleName = '';
                        if ($acc['role'] === 'Admin') {
                            $roleClass = 'role-admin';
                            $roleIcon = 'bi-shield-check';
                            $roleName = 'NSTP Coordinator';
                        } elseif ($acc['role'] === 'Instructor') {
                            $roleClass = 'role-instructor';
                            $roleIcon = 'bi-book-half';
                            $roleName = 'CWTS/LTS Instructor';
                        } else {
                            $roleClass = 'role-rotc';
                            $roleIcon = 'bi-person-badge';
                            $roleName = 'ROTC 1st Class Officer';
                        }
                    ?>
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar" style="background-color: <?= $bg ?>;">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                                <div class="user-info">
                                    <span class="user-name"><?= htmlspecialchars($acc['full_name']) ?></span>
                                    <span class="user-email"><?= htmlspecialchars($acc['email']) ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="role-badge <?= $roleClass ?>">
                                <i class="bi <?= $roleIcon ?>"></i> <?= $roleName ?>
                            </span>
                        </td>
                        <td style="font-size: 0.875rem; color: #475569;">
                            <?= htmlspecialchars($acc['contact_number'] ?: '—') ?>
                        </td>
                        <td>
                            <div class="degree-info">
                                <span class="degree-type"><?= htmlspecialchars($acc['degree_type'] ?: '—') ?></span>
                                <span class="degree-title"><?= htmlspecialchars($acc['degree_title'] ?: '—') ?></span>
                            </div>
                        </td>
                        <td style="font-size: 0.875rem; color: #475569;">
                            &bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn-action" data-bs-toggle="modal" data-bs-target="#editModal<?= $acc['id'] ?>"><i class="bi bi-pencil"></i></button>
                                <button class="btn-action" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $acc['id'] ?>"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $acc['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Account</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="user_id" value="<?= $acc['id'] ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Full Name</label>
                                            <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($acc['full_name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Email</label>
                                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($acc['email']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Contact Number</label>
                                            <input type="text" class="form-control" name="contact_number" value="<?= htmlspecialchars($acc['contact_number']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Role</label>
                                            <select name="role" class="form-select" required>
                                                <option value="Admin" <?= $acc['role'] === 'Admin' ? 'selected' : '' ?>>NSTP Coordinator</option>
                                                <option value="Instructor" <?= $acc['role'] === 'Instructor' ? 'selected' : '' ?>>CWTS/LTS Instructor</option>
                                                <option value="ROTC" <?= $acc['role'] === 'ROTC' ? 'selected' : '' ?>>ROTC 1st Class Officer</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Degree Type</label>
                                            <select name="degree_type" class="form-select">
                                                <option value="">Select Degree Type</option>
                                                <option value="Bachelor" <?= $acc['degree_type'] === 'Bachelor' ? 'selected' : '' ?>>Bachelor</option>
                                                <option value="Masteral" <?= $acc['degree_type'] === 'Masteral' ? 'selected' : '' ?>>Masteral</option>
                                                <option value="Doctoral" <?= $acc['degree_type'] === 'Doctoral' ? 'selected' : '' ?>>Doctoral</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Degree Title</label>
                                            <input type="text" class="form-control" name="degree_title" value="<?= htmlspecialchars($acc['degree_title']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Password (Leave blank to keep current)</label>
                                            <input type="password" class="form-control" name="password" placeholder="New Password">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary" style="background-color: #8B5CF6; border: none;">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal<?= $acc['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger">Delete Account</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= $acc['id'] ?>">
                                        <p>Are you sure you want to delete the account for <strong><?= htmlspecialchars($acc['full_name']) ?></strong>?</p>
                                        <p class="text-muted small">This action cannot be undone.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
