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
    <!-- Official Guardian360 Quickscan Branding Header -->
    <div style="text-align: center; margin-bottom: 30px; padding: 20px 0; border-bottom: 2px solid #e5e5e5;">
        <div style="margin-bottom: 15px;">
            <img src="<?php echo QUICKSCAN_PLUGIN_URL; ?>assets/images/logo_guardian360_quickscan.png"
                 alt="Guardian360 Quickscan"
                 style="height: 60px; width: auto;" />
        </div>
        <div>
            <h1 style="margin: 0 0 5px 0; font-size: 28px; color: #2E3285;"><?php echo esc_html(get_admin_page_title()); ?></h1>
        </div>
    </div>


    <!-- Credential Management Section -->
    <div class="card" style="margin: 0 auto; max-width: 1000px;">
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
        </form>
        
        <div id="credential-result" style="margin-top: 15px;"></div>
    </div>
    
    <hr />

    <!-- Plugin Settings -->
    <div class="card" style="margin: 0 auto; max-width: 1000px;">
        <h2><?php _e('Plugin Settings', 'quickscan-connector'); ?></h2>

        <form method="post" action="options.php">
            <?php settings_fields('quickscan_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <?php _e('Attribution Display', 'quickscan-connector'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <p style="margin-top: 0; margin-bottom: 15px; font-weight: 500;">
                                <?php _e('Choose how to display the required Guardian360 attribution below your security scanners:', 'quickscan-connector'); ?>
                            </p>

                            <label style="display: block; margin-bottom: 10px;">
                                <input type="radio"
                                       name="quickscan_signature_style"
                                       value="logo"
                                       <?php checked(get_option('quickscan_signature_style', 'logo'), 'logo'); ?> />
                                <?php _e('Show clickable Quickscan logo', 'quickscan-connector'); ?>
                            </label>

                            <label style="display: block; margin-bottom: 10px;">
                                <input type="radio"
                                       name="quickscan_signature_style"
                                       value="text"
                                       <?php checked(get_option('quickscan_signature_style', 'logo'), 'text'); ?> />
                                <?php _e('Show text link only: "Powered by Guardian360"', 'quickscan-connector'); ?>
                            </label>

                            <p class="description" style="margin-top: 15px;">
                                <?php _e('Attribution is required and helps support this free plugin. Links direct to our Quickscan platform.', 'quickscan-connector'); ?>
                            </p>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
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
            nonce: '<?php echo wp_create_nonce('quickscan_nonce'); ?>'
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
            nonce: '<?php echo wp_create_nonce('quickscan_nonce'); ?>'
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
                nonce: '<?php echo wp_create_nonce('quickscan_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    location.reload(); // Refresh page
                } else {
                    $result.html('<div class="notice notice-error"><p>' + (response.data || 'Failed to clear credentials') + '</p></div>');
                }
            });
        }
    });
});
</script>