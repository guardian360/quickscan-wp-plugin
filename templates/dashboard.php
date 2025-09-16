<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current user's credentials status
$current_user_id = get_current_user_id();
$stored_email = get_user_meta($current_user_id, 'quickscan_email', true);
$has_credentials = !empty($stored_email);
?>

<div class="wrap">
    <h1><?php _e('Quickscan Dashboard', 'quickscan-connector'); ?></h1>
    
    <?php if ($has_credentials): ?>
        <div class="notice notice-success">
            <p><strong><?php _e('Connected to Quickscan', 'quickscan-connector'); ?></strong> - <?php echo esc_html($stored_email); ?></p>
        </div>
    <?php else: ?>
        <div class="notice notice-warning">
            <p><?php _e('Please configure your Quickscan credentials to get started.', 'quickscan-connector'); ?></p>
            <p>
                <a href="<?php echo admin_url('admin.php?page=quickscan-settings'); ?>" class="button button-primary">
                    <?php _e('Setup Credentials', 'quickscan-connector'); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>

    <div class="dashboard-widgets-wrap">
        <div class="metabox-holder columns-2">
            
            <!-- Welcome Section -->
            <div class="postbox-container" style="width: 100%;">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><?php _e('Welcome to Quickscan Security Scanner', 'quickscan-connector'); ?></h2>
                        </div>
                        <div class="inside">
                            <p><?php _e('Quickscan is a comprehensive security scanning tool that helps you identify vulnerabilities and security issues on your websites. Use the tools below to get started:', 'quickscan-connector'); ?></p>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
                                
                                <div class="card">
                                    <h3><?php _e('Start New Scan', 'quickscan-connector'); ?></h3>
                                    <p><?php _e('Run a comprehensive security scan on any website to identify vulnerabilities and get actionable recommendations.', 'quickscan-connector'); ?></p>
                                    <a href="<?php echo admin_url('admin.php?page=quickscan-start-scan'); ?>" class="button button-primary">
                                        <?php _e('Start Scan', 'quickscan-connector'); ?>
                                    </a>
                                </div>
                                
                                <div class="card">
                                    <h3><?php _e('View Scan Results', 'quickscan-connector'); ?></h3>
                                    <p><?php _e('Access your complete scan history and detailed security reports stored in your Quickscan account.', 'quickscan-connector'); ?></p>
                                    <a href="https://quickscan.guardian360.nl/login" target="_blank" class="button button-secondary">
                                        <?php _e('Login to Quickscan Portal', 'quickscan-connector'); ?>
                                    </a>
                                </div>
                                
                                <div class="card">
                                    <h3><?php _e('Frontend Integration', 'quickscan-connector'); ?></h3>
                                    <p><?php _e('Add security scanners to your website using Gutenberg blocks, widgets, or shortcodes for your visitors.', 'quickscan-connector'); ?></p>
                                    <details style="margin-top: 10px;">
                                        <summary style="cursor: pointer; font-weight: bold;"><?php _e('Integration Methods', 'quickscan-connector'); ?></summary>
                                        <ul style="margin-top: 10px;">
                                            <li><strong><?php _e('Gutenberg Block:', 'quickscan-connector'); ?></strong> <?php _e('Search for "Security Scanner" in the block inserter', 'quickscan-connector'); ?></li>
                                            <li><strong><?php _e('Widget:', 'quickscan-connector'); ?></strong> <?php _e('Add "Quickscan Security Scanner" widget in Appearance > Widgets', 'quickscan-connector'); ?></li>
                                            <li><strong><?php _e('Shortcode:', 'quickscan-connector'); ?></strong> <code>[quickscan]</code></li>
                                        </ul>
                                    </details>
                                </div>
                                
                                <?php if (!$has_credentials): ?>
                                <div class="card" style="border-left: 4px solid #dc3545;">
                                    <h3><?php _e('Setup Required', 'quickscan-connector'); ?></h3>
                                    <p><?php _e('Configure your Quickscan account credentials to enable scanning functionality.', 'quickscan-connector'); ?></p>
                                    <a href="<?php echo admin_url('admin.php?page=quickscan-settings'); ?>" class="button button-primary">
                                        <?php _e('Configure Credentials', 'quickscan-connector'); ?>
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <div style="margin-top: 30px;">
        <h2><?php _e('How It Works', 'quickscan-connector'); ?></h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div class="feature-box">
                <div class="feature-icon">🔐</div>
                <h3><?php _e('Personal Account', 'quickscan-connector'); ?></h3>
                <p><?php _e('Each WordPress user connects with their own Quickscan account for personalized security scanning.', 'quickscan-connector'); ?></p>
            </div>
            
            <div class="feature-box">
                <div class="feature-icon">☁️</div>
                <h3><?php _e('Cloud Storage', 'quickscan-connector'); ?></h3>
                <p><?php _e('All scan results are stored securely in your Quickscan account, accessible from anywhere.', 'quickscan-connector'); ?></p>
            </div>
            
            <div class="feature-box">
                <div class="feature-icon">⚡</div>
                <h3><?php _e('Real-time Results', 'quickscan-connector'); ?></h3>
                <p><?php _e('Get instant security analysis with detailed findings and actionable recommendations.', 'quickscan-connector'); ?></p>
            </div>
            
            <div class="feature-box">
                <div class="feature-icon">🛡️</div>
                <h3><?php _e('GDPR Compliant', 'quickscan-connector'); ?></h3>
                <p><?php _e('Frontend scans are processed without local storage, ensuring complete privacy compliance.', 'quickscan-connector'); ?></p>
            </div>
        </div>
    </div>

    <?php if (get_option('quickscan_show_signature', true)): ?>
    <div class="quickscan-signature" style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
        <a href="https://guardian360.eu" target="_blank" rel="noopener noreferrer">
            <?php echo esc_html(__('Powered by Guardian360', 'quickscan-connector')); ?>
        </a>
    </div>
    <?php endif; ?>
</div>

<style>
.card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-left: 4px solid #0073aa;
    border-radius: 4px;
    padding: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.card h3 {
    margin-top: 0;
    color: #23282d;
}

.card p {
    color: #666;
    line-height: 1.5;
}

.feature-box {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,.05);
}

.feature-icon {
    font-size: 2.5em;
    margin-bottom: 15px;
    display: block;
}

.feature-box h3 {
    margin: 10px 0;
    color: #23282d;
}

.feature-box p {
    color: #666;
    font-size: 14px;
    line-height: 1.4;
    margin-bottom: 0;
}

.quickscan-signature a {
    color: #666;
    text-decoration: none;
    font-size: 12px;
}

.quickscan-signature a:hover {
    color: #0073aa;
    text-decoration: underline;
}

details summary {
    padding: 5px 0;
    outline: none;
}

details[open] summary {
    margin-bottom: 10px;
}

code {
    background: #f1f1f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: Monaco, Consolas, monospace;
}
</style>