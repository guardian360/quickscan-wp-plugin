<?php
/**
 * Quickscan Connector Uninstall
 *
 * Cleans up all plugin data when uninstalled.
 *
 * @package QuickscanConnector
 * @since 1.0.0
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove plugin options
delete_option('quickscan_enable_logging');
delete_option('quickscan_show_signature');
delete_option('quickscan_signature_text');

// Remove user meta for all users
$users = get_users();
foreach ($users as $user) {
    delete_user_meta($user->ID, 'quickscan_email');
    delete_user_meta($user->ID, 'quickscan_password');
    delete_user_meta($user->ID, 'quickscan_last_scan');
    delete_user_meta($user->ID, 'quickscan_credentials_verified');
}

// Clear any transients that might have been set
delete_transient('quickscan_api_status');
delete_transient('quickscan_last_check');

// Remove any scheduled hooks
wp_clear_scheduled_hook('quickscan_daily_cleanup');
wp_clear_scheduled_hook('quickscan_api_check');

// Clear rewrite rules
flush_rewrite_rules();