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
    <!-- Official Guardian360 Quickscan Branding Header -->
    <div style="text-align: center; margin-bottom: 30px; padding: 20px 0; border-bottom: 2px solid #e5e5e5;">
        <div style="margin-bottom: 15px;">
            <img src="<?php echo QUICKSCAN_PLUGIN_URL; ?>assets/images/logo_guardian360_quickscan.png"
                 alt="Guardian360 Quickscan"
                 style="height: 60px; width: auto;" />
        </div>
    </div>

    <!-- Centered Content Container -->
    <div style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">

    <?php if ($has_credentials): ?>
        <div class="notice notice-success">
            <p><strong><?php _e('Connected to Quickscan', 'quickscan-connector'); ?></strong> - <?php echo esc_html($stored_email); ?></p>
        </div>
    <?php endif; ?>

    <div class="dashboard-widgets-wrap">
        <div class="metabox-holder columns-2">
            
            <!-- Welcome Section -->
            <div class="postbox-container" style="width: 100%;">
                <div class="meta-box-sortables">
                            <h2><?php _e('Welcome to Guardian360 Quickscan', 'quickscan-connector'); ?></h2>

                            <?php if (!$has_credentials): ?>
                            <!-- Free Plugin Notification -->
                            <div class="notice notice-info is-dismissible" id="quickscan-pro-notification" style="margin: 20px 0;">
                                <p>
                                    <?php _e('Your Guardian360 Quickscan plugin is ready to use! For additional administrative features like user tracking, white-label reports, and secure result management, upgrade to our Pro version at no cost. All that\'s required is a complimentary Quickscan Account subscription.', 'quickscan-connector'); ?>
                                    <a href="#quickscan-pro-upgrade" class="quickscan-scroll-to-pro" style="text-decoration: none; font-weight: bold;">
                                        <?php _e('Learn more about Pro features →', 'quickscan-connector'); ?>
                                    </a>
                                </p>
                            </div>
                            <?php endif; ?>

                      <div class="postbox">
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

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$has_credentials): ?>
    <!-- Account Upgrade Section for Unregistered Users -->
    <div id="quickscan-pro-upgrade" style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #e5e5e5; clear: both; width: 100%;">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">

            <!-- Free vs Pro Section (Left - 2/3) -->
            <div class="card" style="margin: 0; background: linear-gradient(135deg, #2E3285 0%, #9089c1 100%); color: white; max-width:100%;">
                <div style="padding: 30px;">
                    <h2 style="color: white; margin-top: 0;"><?php _e('Upgrade to Quickscan Pro', 'quickscan-connector'); ?></h2>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin: 20px 0;">
                        <div>
                            <h3 style="color: #e8f4fd;">🔧 <?php _e('Basic Version', 'quickscan-connector'); ?></h3>
                            <ul style="list-style: none; padding: 0; margin: 10px 0;">
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> Complete security scanning</li>
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> Full vulnerability reports</li>
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> Gutenberg blocks & widgets</li>
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> Shortcode support</li>
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> PDF email reports</li>
                                <li style="padding: 3px 0;"><span style="color: #f44336;">✗</span> <span style="opacity: 0.7;">User activity tracking</span></li>
                                <li style="padding: 3px 0;"><span style="color: #f44336;">✗</span> <span style="opacity: 0.7;">White-label reports</span></li>
                                <li style="padding: 3px 0;"><span style="color: #f44336;">✗</span> <span style="opacity: 0.7;">Secure admin dashboard</span></li>
                            </ul>
                        </div>
                        <div>
                            <h3 style="color: #e8f4fd;">⭐ <?php _e('Pro Version', 'quickscan-connector'); ?></h3>
                            <ul style="list-style: none; padding: 0; margin: 10px 0;">
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> Everything in Basic</li>
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> <strong>User activity tracking</strong></li>
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> <strong>Secure results dashboard</strong></li>
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> <strong>White-label PDF reports</strong></li>
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> <strong>Administrative controls</strong></li>
                                <li style="padding: 3px 0;"><span style="color: #4CAF50;">✓</span> <strong>Priority support</strong></li>
                            </ul>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 25px;">
                        <p style="margin-bottom: 15px; font-size: 16px; color: #e8f4fd;">
                            <?php _e('Currently using: Basic Version', 'quickscan-connector'); ?>
                        </p>
                        <a href="<?php echo admin_url('admin.php?page=quickscan-account-request'); ?>" class="button button-hero" style="background: white; color: #2E3285; border: none; font-weight: bold; padding: 12px 24px;">
                            <?php _e('Request Pro Account →', 'quickscan-connector'); ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Existing Users Section (Right - 1/3) -->
            <div class="card" style="margin: 0; background: #ffffff; border-left: 4px solid #28a745;">
                <h3 style="margin-top: 0; color: #2E3285;">🔑 <?php _e('Have a Quickscan Account?', 'quickscan-connector'); ?></h3>
                <p><?php _e('If you already have Quickscan credentials, connect your account to unlock advanced features.', 'quickscan-connector'); ?></p>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=quickscan-settings'); ?>" class="button button-primary">
                        <?php _e('Connect Account', 'quickscan-connector'); ?> →
                    </a>
                </p>
                <p style="font-size: 12px; color: #666; margin-top: 15px;">
                    <?php _e('Enter your existing Quickscan email and password in Settings.', 'quickscan-connector'); ?>
                </p>
            </div>

        </div>
    </div>
    <?php endif; ?>

    </div> <!-- End Centered Content Container -->

    <?php if (get_option('quickscan_show_signature', true)): ?>
    <div class="quickscan-signature" style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; clear: both; width: 100%;">
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

.quickscan-scroll-to-pro {
    transition: color 0.3s ease;
}

.quickscan-scroll-to-pro:hover {
    color: #0073aa !important;
}

#quickscan-pro-upgrade {
    scroll-margin-top: 20px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Handle smooth scrolling to Pro upgrade section
    $('.quickscan-scroll-to-pro').on('click', function(e) {
        e.preventDefault();

        const target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 20
            }, 800);
        }
    });

    // Handle dismissible notification
    $(document).on('click', '#quickscan-pro-notification .notice-dismiss', function() {
        // Store dismissal state in localStorage
        localStorage.setItem('quickscan_pro_notification_dismissed', 'true');
    });

    // Check if notification was previously dismissed
    if (localStorage.getItem('quickscan_pro_notification_dismissed') === 'true') {
        $('#quickscan-pro-notification').hide();
    }
});
</script>