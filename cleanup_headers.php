<?php
$dirs = ['admin', 'instructor', 'rotc'];
foreach ($dirs as $dir) {
    foreach (glob(__DIR__ . "/$dir/*.php") as $file) {
        $content = file_get_contents($file);
        
        // Remove standard page-header-figma blocks completely
        $content = preg_replace('/<!-- Page Header.*?<div class="[^"]*page-header-figma[^"]*"[^>]*>.*?<\/div>/is', '', $content);
        
        // For manage_sections style headers with buttons, remove just the title div
        $content = preg_replace('/<!-- Page Header -->\s*<div class="d-flex justify-content-between align-items-center mb-4">\s*<div>\s*<h3[^>]*>.*?<\/h3>\s*<p[^>]*>.*?<\/p>\s*<\/div>/is', '<div class="d-flex justify-content-end align-items-center mb-4">', $content);
       
        // For manage_students style headers
        $content = preg_replace('/<h3 class="fw-bold mb-0"[^>]*>Student Management<\/h3>/is', '', $content);

        // For platoon_management
        $content = preg_replace('/<div class="d-flex justify-content-between align-items-center mb-4 mt-2">\s*<div>\s*<h2[^>]*>.*?<\/h2>\s*<p[^>]*>.*?<\/p>\s*<\/div>/is', '<div class="d-flex justify-content-end align-items-center mb-4 mt-2">', $content);

        file_put_contents($file, $content);
    }
}
echo "Headers cleaned.\n";
