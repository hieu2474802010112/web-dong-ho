<?php
require_once __DIR__ . '/../wordpress/wp-load.php';

$plugin = 'ai-content-guard/ai-content-guard.php';
$active_plugins = get_option('active_plugins', []);

if (!in_array($plugin, $active_plugins)) {
    $active_plugins[] = $plugin;
    update_option('active_plugins', $active_plugins);
    echo "Plugin $plugin activated successfully!\n";
} else {
    echo "Plugin $plugin is already active.\n";
}

// Ensure table is created
if (function_exists('lw_ai_guard_create_table')) {
    lw_ai_guard_create_table();
    echo "Table wp_ai_moderation_logs verified.\n";
}

