<?php

// readfile(WP_CONTENT_DIR."/cache/"."index.html");

// exit;

header("my-cache:MISS");

$cache_file = WP_CONTENT_DIR . '/cache' . $_SERVER['REQUEST_URI'] . 'index.html';

if (file_exists($cache_file)) {
    header("my-cache:HIT");
    readfile($cache_file);
    exit;
}