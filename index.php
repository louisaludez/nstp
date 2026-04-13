<?php
/**
 * Root Index Router
 * Automatically redirects base URL to the login page
 */

// Since .htaccess drops the extension, we can safely redirect to '/login'
header("Location: login");
exit;
