<?php
/*
Plugin Name: My Cache
Description: A simple WordPress caching plugin.
Version: 1.0
Author: Your Name
*/

ob_start('process_output');

function process_output($output) {
    if (is_admin()) {
        return $output;
    }

    if (is_user_logged_in()) {
        return $output;
    }
    
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        return $output;
    }

    if (strpos($output, '<html') === false) {
        return $output;
    }
    //defer
    $output = str_replace('<script', '<script defer', $output);

    $cache_path = WP_CONTENT_DIR . '/cache' . $_SERVER['REQUEST_URI'];

    @mkdir($cache_path, 0755, true);
    
    file_put_contents($cache_path."/index.html", $output);

    return $output;


}

// $getdata = gzencode($input,9);
// $file_get_contents($cahe_path."/index.html.gz",$output);

function delete_directory($path) {
    if (is_file($path) && file_exists($path)) {
        return unlink($path);
    } elseif (is_dir($path)) {
        foreach (glob($path . '/*') as $file) {
            delete_directory($file);
        }
        return rmdir($path);
    }
    return false;
}

function clear_cache() {
    $cache_dir = WP_CONTENT_DIR . '/cache/';
    // Delete the cache folder
    delete_directory( $cache_dir);
}

add_action('save_post', 'clear_cache');
add_action('wp_update_comment_count', 'clear_cache');
add_action('switch_theme', 'clear_cache');

function add_advached_cache() {
    copy(__DIR__ . '/advanced-cache.php', WP_CONTENT_DIR . '/advanced-cache.php');
}
register_activation_hook(__FILE__, 'add_advached_cache');

function remove_advached_cache() {
    unlink(WP_CONTENT_DIR . '/advanced-cache.php');
}
register_deactivation_hook(__FILE__, 'remove_advached_cache');


function add_wp_cache_constant() {
    $wp_config = file_get_contents(ABSPATH . 'wp-config.php');
    $wp_config = str_replace("<?php", "<?php\ndefine('WP_CACHE', true);", $wp_config);
    file_put_contents(ABSPATH . 'wp-config.php', $wp_config);
}
register_activation_hook(__FILE__, 'add_wp_cache_constant');

function remove_wp_cache_constant() {
    $wp_config = file_get_contents(ABSPATH . 'wp-config.php');
    $wp_config = str_replace("\ndefine('WP_CACHE', true);", "", $wp_config);
    file_put_contents(ABSPATH . 'wp-config.php', $wp_config);
}
register_deactivation_hook(__FILE__, 'remove_wp_cache_constant');