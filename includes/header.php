<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current file name
$current_file = basename($_SERVER['PHP_SELF']);

// List of pages that DO NOT need a login (add more if needed)
$public_pages = ['login.php', 'register.php'];

// If the user is NOT logged in and is trying to access a private page
if (!isset($_SESSION['user_id']) && !in_array($current_file, $public_pages)) {
    header("Location: /nstp/login"); // Redirect to clean login URL
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        // Check local storage immediately to prevent flashing
        if(localStorage.getItem("sidebarCollapsed") === "true") {
            document.documentElement.classList.add("sidebar-collapsed");
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSTP System Console</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        /* Force the wrapper to take the full height of the screen */
        .wrapper { 
            min-height: 100vh; 
        }
        
        @media (max-width: 991.98px) {
            .flex-grow-1 { 
                padding-top: 160px !important; /* Clear both stacked mobile headers */
                padding-left: 15px !important; 
                padding-right: 15px !important; 
            }
            .topbar-fixed {
                left: 0 !important;
                top: 74px !important; /* Slide under the mobile brand roof */
                padding: 0 1.5rem !important;
                z-index: 1020 !important;
            }
        }

        /* Topbar Fixed Styles */
        .topbar-fixed {
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            height: 75px;
            z-index: 1030;
            background-color: #fff;
            padding: 0 3rem;
            border-bottom: 1px solid #E5E7EB;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html.sidebar-collapsed .topbar-fixed {
            left: 80px;
        }
        
        /* Desktop View Adjustments (Screens 992px and larger) */
        @media (min-width: 992px) {
            .sidebar { 
                width: 260px !important; 
                min-width: 260px !important;
                max-width: 260px !important;
                flex-shrink: 0 !important; 
                position: sticky; 
                top: 0; 
                height: 100vh; 
                overflow-y: hidden;
                overflow-x: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .sidebar:hover { overflow-y: auto; }

            /* Collapsed State */
            html.sidebar-collapsed .sidebar {
                width: 80px !important;
                min-width: 80px !important;
                max-width: 80px !important;
            }

            /* Hide Text when collapsed */
            html.sidebar-collapsed .sidebar h4,
            html.sidebar-collapsed .sidebar .text-white-50,
            html.sidebar-collapsed .sidebar .btn-light {
                display: none !important;
            }
            
            /* Text-Hiding Hack for Nav Links */
            html.sidebar-collapsed .sidebar .nav-link {
                font-size: 0;
                justify-content: center;
                padding: 0.8rem 0;
                text-align: center;
                display: flex;
            }
            html.sidebar-collapsed .sidebar .nav-link i {
                font-size: 1.4rem;
                margin-right: 0 !important;
            }
            html.sidebar-collapsed .sidebar-brand-icon {
                font-size: 2rem !important;
                margin-bottom: 0 !important;
                display: block !important;
            }

            /* Ensure the main content area has its own scrolling space */
            .flex-grow-1 {
                min-height: 100vh;
                overflow-x: hidden;
                flex-grow: 1;
                min-width: 0; 
                padding-top: 100px !important; /* Push beneath 75px topbar */
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
        }
    </style>
    
    <?php if (isset($extra_css)): ?>
        <?php foreach((array)$extra_css as $css_file): ?>
            <link href="<?= htmlspecialchars($css_file) ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($extra_js_head)): ?>
        <?php foreach((array)$extra_js_head as $js_file): ?>
            <script src="<?= htmlspecialchars($js_file) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body style="background-color: var(--bg-light, #F4F6F9);">
    <div class="d-flex flex-column flex-lg-row wrapper">
