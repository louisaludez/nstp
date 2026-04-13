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
        
        /* Mobile View Adjustments (Screens smaller than 992px) */
        @media (max-width: 991.98px) {
            .flex-grow-1 { 
                padding-top: 90px !important; 
                padding-left: 20px !important; 
                padding-right: 20px !important; 
            }
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
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
        }
    </style>
</head>
<body style="background-color: var(--bg-light, #F4F6F9);">
    <div class="d-flex flex-column flex-lg-row wrapper">