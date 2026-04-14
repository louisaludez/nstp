<?php
// instructor/controllers/ProfileController.php

$user_id = $_SESSION['user_id'];
$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $contact = trim($_POST['contact_number']);
        $upd = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, contact_number = ? WHERE id = ?");
        if ($upd->execute([$full_name, $email, $contact, $user_id])) {
            $_SESSION['full_name'] = $full_name;
            $message = "Profile details updated successfully!";
            $msgType = "success";
        } else {
            $message = "Failed to update profile.";
            $msgType = "danger";
        }
    }
    if (isset($_POST['change_password'])) {
        $stmtMe = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmtMe->execute([$user_id]);
        $me = $stmtMe->fetch();
        $current_pw = $_POST['current_password'];
        $new_pw = $_POST['new_password'];
        $confirm_pw = $_POST['confirm_password'];
        if (password_verify($current_pw, $me['password'])) {
            if ($new_pw === $confirm_pw) {
                if (strlen($new_pw) >= 6) {
                    $hash = password_hash($new_pw, PASSWORD_DEFAULT);
                    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $user_id]);
                    $message = "Password successfully changed!";
                    $msgType = "success";
                } else { $message = "New password must be at least 6 characters long."; $msgType = "warning"; }
            } else { $message = "New passwords do not match."; $msgType = "warning"; }
        } else { $message = "Incorrect current password."; $msgType = "danger"; }
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
