<?php
// admin/controllers/CertificateTemplatesController.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Directory where templates are stored
    $target_dir = "../uploads/templates/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // CREATE TEMPLATE
    if ($_POST['action'] === 'create_template') {
        $name = trim($_POST['template_name']);
        $program_type = $_POST['program_type'];
        $badge_color = $_POST['badge_color'];
        $image_path = '';

        if (isset($_FILES["design_image"]) && $_FILES["design_image"]["error"] == 0) {
            $ext = pathinfo($_FILES["design_image"]["name"], PATHINFO_EXTENSION);
            $filename = uniqid('template_') . '.' . $ext;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["design_image"]["tmp_name"], $target_file)) {
                $image_path = 'uploads/templates/' . $filename;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO certificate_templates (name, program_type, badge_color, image_path, status) VALUES (?, ?, ?, ?, 'Active')");
        $stmt->execute([$name, $program_type, $badge_color, $image_path]);
        
        logAction($pdo, 'Created Certificate Template', "Template '$name' created.");
        
        header("Location: certificate_templates.php?success=1");
        exit;
    }
    
    // UPDATE TEMPLATE
    if ($_POST['action'] === 'edit_template') {
        $id = $_POST['template_id'];
        $name = trim($_POST['template_name']);
        $program_type = $_POST['program_type'];
        $badge_color = $_POST['badge_color'];
        
        // Fetch existing record
        $stmtFetch = $pdo->prepare("SELECT image_path FROM certificate_templates WHERE id = ?");
        $stmtFetch->execute([$id]);
        $existing = $stmtFetch->fetch();
        $image_path = $existing['image_path'];
        
        if (isset($_FILES["design_image"]) && $_FILES["design_image"]["error"] == 0) {
            $ext = pathinfo($_FILES["design_image"]["name"], PATHINFO_EXTENSION);
            $filename = uniqid('template_') . '.' . $ext;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["design_image"]["tmp_name"], $target_file)) {
                // Delete old file if exists
                if (!empty($image_path) && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);
                }
                $image_path = 'uploads/templates/' . $filename;
            }
        }
        
        $stmt = $pdo->prepare("UPDATE certificate_templates SET name = ?, program_type = ?, badge_color = ?, image_path = ? WHERE id = ?");
        $stmt->execute([$name, $program_type, $badge_color, $image_path, $id]);
        
        logAction($pdo, 'Updated Certificate Template', "Template '$name' updated.");
        
        header("Location: certificate_templates.php?success=2");
        exit;
    }

    // DELETE TEMPLATE
    if ($_POST['action'] === 'delete_template') {
        $id = $_POST['template_id'];
        
        // Fetch to get image path for deletion
        $stmtFetch = $pdo->prepare("SELECT name, image_path FROM certificate_templates WHERE id = ?");
        $stmtFetch->execute([$id]);
        $existing = $stmtFetch->fetch();
        
        if ($existing) {
            if (!empty($existing['image_path']) && file_exists('../' . $existing['image_path'])) {
                unlink('../' . $existing['image_path']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM certificate_templates WHERE id = ?");
            $stmt->execute([$id]);
            
            logAction($pdo, 'Deleted Certificate Template', "Template '{$existing['name']}' deleted.");
        }
        
        header("Location: certificate_templates.php?success=3");
        exit;
    }
}

// Fetch all templates for display
$templates = [];
try {
    $stmt = $pdo->query("SELECT * FROM certificate_templates ORDER BY created_at DESC");
    $templates = $stmt->fetchAll();
} catch (Exception $e) {}

// Utility to get badge colors based on selection
function getBadgeStyle($colorName) {
    return match ($colorName) {
        'Indigo' => 'background: #EEF2FF; color: #6366F1;',
        'Emerald' => 'background: #ECFDF5; color: #10B981;',
        'Amber' => 'background: #FFFBEB; color: #F59E0B;',
        'Rose' => 'background: #FFF1F2; color: #F43F5E;',
        default => 'background: #EEF2FF; color: #6366F1;',
    };
}
?>
