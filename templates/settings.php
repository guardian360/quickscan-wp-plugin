<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current user's credentials
$current_user_id = get_current_user_id();
$stored_email = get_user_meta($current_user_id, 'quickscan_email', true);
$has_credentials = !empty($stored_email);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <!-- Credential Management Section -->
    <div class="card" style="margin: 20px 0;">
        <h2><?php _e('Quickscan Account Credentials', 'quickscan-connector'); ?></h2>
        
        <?php if ($has_credentials): ?>
            <div class="notice notice-success inline">
                <p><strong><?php _e('Connected:', 'quickscan-connector'); ?></strong> <?php echo esc_html($stored_email); ?></p>
            </div>
        <?php else: ?>
            <div class="notice notice-warning inline">
                <p><?php _e('No Quickscan credentials configured. Please set up your credentials below.', 'quickscan-connector'); ?></p>
            </div>
        <?php endif; ?>
        
        <form id="quickscan-credentials-form" style="margin-top: 20px;">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="quickscan_account_type"><?php _e('Account Setup', 'quickscan-connector'); ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="radio" name="account_type" value="existing" checked>
                                <?php _e('I have a Quickscan account', 'quickscan-connector'); ?>
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="account_type" value="new">
                                <?php _e('Request new Quickscan account', 'quickscan-connector'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </table>
            
            <!-- Existing Account Login -->
            <div id="existing-account-form" class="credential-form">
                <h3><?php _e('Login to Quickscan', 'quickscan-connector'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="quickscan_email"><?php _e('Email Address', 'quickscan-connector'); ?></label>
                        </th>
                        <td>
                            <input type="email" 
                                   id="quickscan_email" 
                                   name="email" 
                                   value="<?php echo esc_attr($stored_email); ?>" 
                                   class="regular-text" 
                                   required />
                            <p class="description">
                                <?php _e('The email address you use to login to Quickscan Portal', 'quickscan-connector'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="quickscan_password"><?php _e('Password', 'quickscan-connector'); ?></label>
                        </th>
                        <td>
                            <input type="password" 
                                   id="quickscan_password" 
                                   name="password" 
                                   class="regular-text" 
                                   required />
                            <p class="description">
                                <?php _e('Your Quickscan Portal password. This will be encrypted and stored securely.', 'quickscan-connector'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- New Account Registration -->
            <div id="new-account-form" class="credential-form" style="display: none;">
                <h3><?php _e('Request Quickscan Account Access', 'quickscan-connector'); ?></h3>
                <div class="notice notice-info inline">
                    <p><strong><?php _e('Professional Security Platform Access', 'quickscan-connector'); ?></strong></p>
                    <p><?php _e('To ensure the highest quality of service and maintain the integrity of our security scanning platform, we personally review each account request. This process typically takes up to 24 hours.', 'quickscan-connector'); ?></p>
                </div>

                <div style="margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #00a0d2;">
                    <h4 style="margin-top: 0;"><?php _e('Why We Vet Accounts', 'quickscan-connector'); ?></h4>
                    <ul style="margin: 10px 0 0 20px;">
                        <li><?php _e('Provide personalized onboarding and optimal configuration', 'quickscan-connector'); ?></li>
                        <li><?php _e('Maintain security and reliability of our scanning infrastructure', 'quickscan-connector'); ?></li>
                        <li><?php _e('Offer tailored recommendations based on your website profile', 'quickscan-connector'); ?></li>
                        <li><?php _e('Ensure priority support from our security experts', 'quickscan-connector'); ?></li>
                    </ul>
                </div>

                <p>
                    <a href="<?php echo admin_url('admin.php?page=quickscan-account-request'); ?>" class="button button-primary">
                        <?php _e('Request Account Access', 'quickscan-connector'); ?> →
                    </a>
                </p>

                <p class="description">
                    <?php _e('Already submitted a request? You\'ll receive your credentials via email once approved. Then return here to login with your new account.', 'quickscan-connector'); ?>
                </p>
            </div>
            
            <p class="submit">
                <button type="button" id="test-credentials" class="button button-secondary">
                    <?php _e('Test Credentials', 'quickscan-connector'); ?>
                </button>
                <button type="submit" class="button button-primary">
                    <?php _e('Save Credentials', 'quickscan-connector'); ?>
                </button>
                <?php if ($has_credentials): ?>
                    <button type="button" id="clear-credentials" class="button button-secondary" style="margin-left: 10px;">
                        <?php _e('Clear Credentials', 'quickscan-connector'); ?>
                    </button>
                <?php endif; ?>
            </p>
        </form>
        
        <div id="credential-result" style="margin-top: 15px;"></div>
    </div>
    
    <hr />
    
    <!-- Plugin Settings -->
    <form method="post" action="options.php">
        <?php settings_fields('quickscan_settings'); ?>
        
        <!-- API Configuration Notice -->
        <div class="notice notice-info inline" style="margin: 20px 0; padding: 15px;">
            <h3 style="margin-top: 0;"><?php _e('API Configuration', 'quickscan-connector'); ?></h3>
            <p><?php _e('This plugin connects to the Guardian360 Quickscan API v1:', 'quickscan-connector'); ?></p>
            <p><strong><?php _e('API Endpoint:', 'quickscan-connector'); ?></strong> <code>https://quickscan.guardian360.nl/api/v1</code></p>
            <p><?php _e('Results are displayed in English and stored in your Quickscan account.', 'quickscan-connector'); ?></p>
        </div>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="quickscan_enable_logging"><?php _e('Enable Logging', 'quickscan-connector'); ?></label>
                </th>
                <td>
                    <input type="checkbox" 
                           id="quickscan_enable_logging" 
                           name="quickscan_enable_logging" 
                           value="1" 
                           <?php checked(get_option('quickscan_enable_logging', false)); ?> />
                    <p class="description">
                        <?php _e('Log API errors and debug information', 'quickscan-connector'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="quickscan_show_signature"><?php _e('Show Guardian360 Signature', 'quickscan-connector'); ?></label>
                </th>
                <td>
                    <input type="checkbox" 
                           id="quickscan_show_signature" 
                           name="quickscan_show_signature" 
                           value="1" 
                           <?php checked(get_option('quickscan_show_signature', true)); ?> />
                    <p class="description">
                        <?php _e('Display a small Guardian360 link below frontend scanners. Help support the project!', 'quickscan-connector'); ?>
                    </p>
                </td>
            </tr>
            
        </table>
        
        <?php submit_button(); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle account type switching
    $('input[name="account_type"]').on('change', function() {
        $('.credential-form').hide();
        const type = $(this).val();
        
        switch(type) {
            case 'existing':
                $('#existing-account-form').show();
                break;
            case 'new':
                $('#new-account-form').show();
                break;
        }
    });
    
    // Test credentials
    $('#test-credentials').on('click', function() {
        const $button = $(this);
        const $result = $('#credential-result');
        const email = $('#quickscan_email').val();
        const password = $('#quickscan_password').val();
        
        if (!email || !password) {
            $result.html('<div class="notice notice-error"><p>Please enter both email and password.</p></div>');
            return;
        }
        
        $button.prop('disabled', true).text('Testing...');
        $result.html('<div class="notice notice-info"><p>Testing credentials...</p></div>');
        
        $.post(ajaxurl, {
            action: 'quickscan_test_credentials',
            email: email,
            password: password,
            nonce: quickscan_ajax.nonce
        }, function(response) {
            if (response.success) {
                $result.html('<div class="notice notice-success"><p>' + response.data + '</p></div>');
            } else {
                $result.html('<div class="notice notice-error"><p>' + response.data + '</p></div>');
            }
        }).always(function() {
            $button.prop('disabled', false).text('Test Credentials');
        });
    });
    
    // Save credentials
    $('#quickscan-credentials-form').on('submit', function(e) {
        e.preventDefault();
        
        const $result = $('#credential-result');
        const email = $('#quickscan_email').val();
        const password = $('#quickscan_password').val();
        const accountType = $('input[name="account_type"]:checked').val();
        
        if (accountType === 'new') {
            $result.html('<div class="notice notice-warning"><p>Account registration is not yet available.</p></div>');
            return;
        }
        
        if (accountType === 'wordpress') {
            $result.html('<div class="notice notice-warning"><p>WordPress credential integration is not yet available.</p></div>');
            return;
        }
        
        if (!email || !password) {
            $result.html('<div class="notice notice-error"><p>Please enter both email and password.</p></div>');
            return;
        }
        
        $result.html('<div class="notice notice-info"><p>Saving credentials...</p></div>');
        
        $.post(ajaxurl, {
            action: 'quickscan_save_credentials',
            email: email,
            password: password,
            use_wp_credentials: accountType === 'wordpress',
            nonce: quickscan_ajax.nonce
        }, function(response) {
            if (response.success) {
                $result.html('<div class="notice notice-success"><p>' + response.data + '</p></div>');
                setTimeout(function() {
                    location.reload(); // Refresh to show updated credential status
                }, 1500);
            } else {
                $result.html('<div class="notice notice-error"><p>' + response.data + '</p></div>');
            }
        });
    });
    
    // Clear credentials
    $('#clear-credentials').on('click', function() {
        if (confirm('Are you sure you want to clear your Quickscan credentials?')) {
            const $result = $('#credential-result');
            
            $.post(ajaxurl, {
                action: 'quickscan_save_credentials',
                email: '',
                password: '',
                nonce: quickscan_ajax.nonce
            }, function(response) {
                location.reload(); // Refresh page
            });
        }
    });
});
</script>