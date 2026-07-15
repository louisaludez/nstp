<?php
if (!isset($_GET['id'])) {
    header("Location: manage_sections.php");
    exit;
}
$section_id = (int)$_GET['id'];

// Get section details
$stmt = $pdo->prepare("SELECT * FROM sections WHERE id = ?");
$stmt->execute([$section_id]);
$section = $stmt->fetch();

if (!$section) {
    header("Location: manage_sections.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_student'])) {
    $student_id = trim($_POST['student_id']);
    $course = trim($_POST['course']);
    $full_name = trim($_POST['full_name']); // e.g. "Reyes, Maria Clara S."
    $gender = $_POST['gender'];
    $dob = trim($_POST['date_of_birth']);
    $pob = trim($_POST['place_of_birth']);
    $cell_number = trim($_POST['cell_number']);
    $res_address = trim($_POST['residential_address']);
    $email = trim($_POST['email']);
    $status = $_POST['status'];
    $final_grade = trim($_POST['final_grade']);

    // Parse full name
    $parts = explode(',', $full_name, 2);
    $last_name = trim($parts[0] ?? '');
    $first_name = trim($parts[1] ?? '');

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE students SET first_name=?, last_name=?, course=?, sex=?, date_of_birth=?, place_of_birth=?, contact_number=?, complete_address=?, email=? WHERE student_id=?");
        $stmt->execute([$first_name, $last_name, $course, $gender, ($dob ?: null), ($pob ?: null), ($cell_number ?: null), ($res_address ?: null), ($email ?: null), $student_id]);

        $stmt2 = $pdo->prepare("UPDATE enrollments SET status=?, final_grade=? WHERE student_id=? AND section_id=?");
        $stmt2->execute([$status, ($final_grade === '' ? null : $final_grade), $student_id, $section_id]);

        $pdo->commit();
        $message = "Student record successfully updated!";
        $msgType = "success";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Get enrolled students
$stmt = $pdo->prepare("
    SELECT s.*, e.status as grade_status, e.final_grade, e.id as enrollment_id
    FROM students s
    JOIN enrollments e ON s.student_id = e.student_id
    WHERE e.section_id = ?
    ORDER BY s.last_name ASC, s.first_name ASC
");
$stmt->execute([$section_id]);
$enrolled_students = $stmt->fetchAll();
?>
