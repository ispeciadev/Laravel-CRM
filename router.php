<?php

/**
 * Laravel router script for PHP's built-in server.
 * This handles routing when .htaccess is not available.
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// Route all other requests through index.php
require_once __DIR__.'/public/index.php';
