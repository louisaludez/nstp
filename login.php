<?php
session_start();

require 'config/db.php';

// 1. IMPROVED CHECK: If already logged in, redirect based on current session
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $redirect = ($_SESSION['role'] === 'Admin') ? 'admin/dashboard.php' : 'instructor/dashboard.php';
    header("Location: $redirect");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // 2. CLEAR OLD DATA: Ensure no remnants of previous logins exist
        session_unset();
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        // 3. ROLE-BASED REDIRECT: (Using Clean URLs without .php)
        if ($_SESSION['role'] === 'Admin') {
            header("Location: admin/dashboard");
        } else if ($_SESSION['role'] === 'Instructor') {
            header("Location: instructor/dashboard");
        } else {
            $error = "Unauthorized role. Please contact the administrator.";
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NSTP System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        /* Specific overrides for the standalone login page to match Figma */
        body {
            background-color: var(--bg-light); 
        }
        .login-card {
            border-radius: 16px; /* Smoother, more modern corners */
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
            border: none;
        }
        .btn-brand {
            background-color: var(--primary-active);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-brand:hover {
            background-color: var(--primary-bg);
            color: white;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 16px;
            border: 1px solid #E5E7EB;
        }
        .form-control:focus {
            border-color: var(--primary-active);
            box-shadow: 0 0 0 0.25rem rgba(74, 70, 214, 0.1);
        }
    </style>
</head>
<body class="d-flex align-items-center" style="height: 100vh; background-image: url('assets/images/ab_bg.png'); background-size: cover; background-position: center;">
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="card login-card p-4 p-sm-5 bg-white">
                    
                    <div class="text-center mb-5">
                        <h3 class="fw-bold mb-1" style="color: var(--primary-bg);">NSTP System</h3>
                        <p class="text-muted small">Please sign in to your account</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-3" style="font-size: 0.9rem;">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="form-label text-muted fw-medium" style="font-size: 0.85rem;">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="name@dnsc.edu.ph" required autofocus>
                        </div>
                        <div class="mb-5">
                            <label class="form-label text-muted fw-medium" style="font-size: 0.85rem;">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-brand w-100">Sign In</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>

</body>
</html>