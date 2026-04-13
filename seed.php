<?php
// Ensure this points to the new location of your database connection
require 'config/db.php';

// Hash a new password securely
$hashedPassword = password_hash('instructor123', PASSWORD_DEFAULT);

// Prepare the insertion query
$stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");

try {
    // FIXED: Changed role from 'Admin' to 'Instructor'
    // Also changed the name and email to avoid confusion with your Admin account
    $stmt->execute(['Prof. Garcia', 'instructor1@dnsc.edu.ph', $hashedPassword, 'Instructor']);
    
    echo "<h3>Success! The Instructor account has been created.</h3>";
    echo "<p><strong>Name:</strong> Prof. Garcia <br>";
    echo "<strong>Email:</strong> instructor1@dnsc.edu.ph <br>";
    echo "<strong>Password:</strong> instructor123 <br>";
    echo "<strong>Role:</strong> Instructor </p>";
    echo "<a href='login.php'>Go to Login Page</a>";
    
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { 
        echo "Error: This instructor account already exists. <a href='login.php'>Go to Login</a>.";
    } else {
        echo "Database Error: " . $e->getMessage();
    }
}
?>