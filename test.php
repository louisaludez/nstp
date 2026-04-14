<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['PHP_SELF'] = '/admin/manage_students';
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'Admin';
chdir('admin');
ob_start();
include 'manage_students.php';
$output = ob_get_clean();
if (empty($output)) {
    echo "NO OUTPUT";
} else {
    echo "OUTPUT LENGTH: " . strlen($output);
}
