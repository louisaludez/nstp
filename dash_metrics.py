import os
import re

p = r"c:\Users\USER\Desktop\nstp\instructor\dashboard.php"
with open(p, 'r', encoding='utf-8') as file:
    content = file.read()

added_logic = """
$student_count = 0;
$active_student_count = 0;
$attendance_rate = 100;

if ($section) {
    $stmtStu = $pdo->prepare("SELECT COUNT(*) FROM students WHERE section_id = ?");
    $stmtStu->execute([$section['id']]);
    $student_count = $stmtStu->fetchColumn();

    $stmtAct = $pdo->prepare("SELECT COUNT(*) FROM students WHERE section_id = ? AND enrollment_status = 'Active'");
    $stmtAct->execute([$section['id']]);
    $active_student_count = $stmtAct->fetchColumn();
    
    $stmtAtt = $pdo->prepare("SELECT COUNT(*) as tot, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as pres FROM attendance WHERE section_id = ?");
    $stmtAtt->execute([$section['id']]);
    $attData = $stmtAtt->fetch();
    if ($attData['tot'] > 0) {
        $attendance_rate = round(($attData['pres'] / $attData['tot']) * 100);
    }
}

$stmtPlans = $pdo->prepare("SELECT * FROM activity_plans WHERE instructor_id = ? ORDER BY submitted_date DESC LIMIT 5");
$stmtPlans->execute([$instructor_id]);
$activity_plans = $stmtPlans->fetchAll();

$stmtReps = $pdo->prepare("SELECT * FROM accomplishment_reports WHERE instructor_id = ? ORDER BY submitted_date DESC LIMIT 5");
$stmtReps->execute([$instructor_id]);
$accomplishment_reports = $stmtReps->fetchAll();

$stmtSessions = $pdo->prepare("SELECT * FROM activities WHERE component = ? AND activity_date >= CURDATE() ORDER BY activity_date ASC LIMIT 5");
$stmtSessions->execute([$section['component'] ?? '']);
$upcoming_sessions = $stmtSessions->fetchAll();
"""

if "$student_count = 0;" not in content:
    content = content.replace("$section = $stmtSection->fetch();", "$section = $stmtSection->fetch();\n" + added_logic)

content = re.sub(
    r'<div><small class="text-muted d-block">My Students</small>\s*<h2 class="fw-bold mb-0">[^<]*</h2>',
    r'<div><small class="text-muted d-block">My Students</small>\n                    <h2 class="fw-bold mb-0"><?= $student_count ?></h2>',
    content
)

content = re.sub(
    r'<div><small class="text-muted d-block">Attendance Rate</small>\s*<h2 class="fw-bold mb-0">[^<]*</h2>',
    r'<div><small class="text-muted d-block">Attendance Rate</small>\n                    <h2 class="fw-bold mb-0"><?= $attendance_rate ?>%</h2>',
    content
)

content = re.sub(
    r'<div><small class="text-muted d-block">Activity Plans</small>\s*<h2 class="fw-bold mb-0">[^<]*</h2>',
    r'<div><small class="text-muted d-block">Activity Plans</small>\n                    <h2 class="fw-bold mb-0"><?= count($activity_plans) ?></h2>',
    content
)

content = re.sub(
    r'<div><small class="text-muted d-block">Reports Submitted</small>\s*<h2 class="fw-bold mb-0">[^<]*</h2>',
    r'<div><small class="text-muted d-block">Reports Submitted</small>\n                    <h2 class="fw-bold mb-0"><?= count($accomplishment_reports) ?></h2>',
    content
)

content = re.sub(
    r'<div><small class="text-muted d-block">Students</small><strong>[^<]*</strong>',
    r'<div><small class="text-muted d-block">Students</small><strong><?= $student_count ?> (<?= $active_student_count ?> active)</strong>',
    content
)

with open(p, 'w', encoding='utf-8') as file:
    file.write(content)
print("Updated instructor dashboard metric cards.")
