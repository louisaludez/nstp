<?php
// includes/layout.php
// Expected variables from the view:
// $content (string) - the HTML content
// $extra_css (array, optional) - array of CSS file paths
// $extra_js (array, optional) - array of JS file paths

// The header handles starting the HTML, <head>, and <body> tag, 
// as well as the <div class="wrapper">
require_once __DIR__ . '/header.php'; 

// Include the correct sidebar based on role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Admin') {
        require_once __DIR__ . '/admin_sidebar.php';
    } elseif ($_SESSION['role'] === 'Instructor') {
        require_once __DIR__ . '/instructor_sidebar.php';
    }
}
?>

<div class="flex-grow-1 p-5 w-100">
    <?php require_once __DIR__ . '/topbar.php'; ?>
    
    <!-- Render the main page content -->
    <?= $content ?? '' ?>
</div>

</div> <!-- closes the .wrapper div left open in header.php -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts defined dynamically by the view -->
<?php if (isset($extra_js)): ?>
    <?php foreach((array)$extra_js as $js_file): ?>
        <script src="<?= htmlspecialchars($js_file) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
