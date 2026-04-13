<?php
/**
 * PHP Built-in Server Router File
 * Allows hiding .php extensions natively while preserving relative path resolution.
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = __DIR__ . $uri;

// If a raw path directly requests the root (e.g., localhost:8000/)
if ($uri === '/' || $uri === '') {
    chdir(__DIR__);
    include __DIR__ . '/index.php';
    return;
}

// If it's directly seeking an existing file like .png or .css, let it serve the file
if (file_exists($path) && is_file($path)) {
    return false;
}

// If appending .php results in a valid file, redirect internally
if (file_exists($path . '.php')) {
    // CRITICAL FIX: Change the Current Working Directory to the target script's actual directory
    // This physically prevents file-tree mismatch issues so that commands like `require '../config/db.php'` work flawlessly.
    chdir(dirname($path . '.php'));
    
    // Explicitly update server globals just in case internal logic relies on it
    $_SERVER['SCRIPT_FILENAME'] = $path . '.php';
    $_SERVER['PHP_SELF'] = $uri . '.php';
    $_SERVER['SCRIPT_NAME'] = $uri . '.php';

    include $path . '.php';
    return;
}

// Fallback (Not Found)
http_response_code(404);
echo "404 Not Found";
