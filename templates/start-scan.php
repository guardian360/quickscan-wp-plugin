<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
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
            <h1 style="margin: 0 0 5px 0; font-size: 28px; color: #2E3285;"><?php _e('Start New Scan', 'quickscan-connector'); ?></h1>
        </div>
    </div>

    <!-- Centered Content Container -->
    <div style="max-width: 800px; margin: 0 auto; padding: 0 20px;">
        <form id="quickscan-form" method="post">
        <?php wp_nonce_field('quickscan_nonce', 'quickscan_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="scan_url"><?php _e('Website URL', 'quickscan-connector'); ?></label>
                </th>
                <td>
                    <input type="url" 
                           id="scan_url" 
                           name="scan_url" 
                           class="regular-text" 
                           placeholder="https://example.com"
                           required />
                    <p class="description">
                        <?php _e('Enter the full URL of the website you want to scan', 'quickscan-connector'); ?>
                    </p>
                </td>
            </tr>
            
        </table>
        
        <p class="submit">
            <input type="submit" 
                   id="start-scan-btn"
                   class="button button-primary" 
                   value="<?php _e('Start Scan', 'quickscan-connector'); ?>" />
        </p>
    </form>
    
    <div id="scan-status" style="display: none; margin-top: 20px;">
        <h3><?php _e('Scan Progress', 'quickscan-connector'); ?></h3>
        <div id="scan-progress">
            <p><?php _e('Starting scan...', 'quickscan-connector'); ?></p>
        </div>
    </div>
    
    <div id="scan-results" style="display: none; margin-top: 20px;">
        <h3><?php _e('Scan Results', 'quickscan-connector'); ?></h3>
        <div id="results-content"></div>
    </div>
    
        <div class="quickscan-signature">
            <?php
            $signature_style = get_option('quickscan_signature_style', 'logo');
            if ($signature_style === 'logo'): ?>
                <div style="font-size: 11px; color: #999; margin-bottom: 8px;">
                    ⚡ <?php echo esc_html(__('Powered by Guardian360', 'quickscan-connector')); ?>
                </div>
                <a href="https://guardian360.eu/quickscan" target="_blank" rel="noopener noreferrer">
                    <img src="<?php echo QUICKSCAN_PLUGIN_URL; ?>assets/images/logo_guardian360_quickscan.png"
                         alt="Guardian360 Quickscan Security Scanner"
                         title="Visit Guardian360 Quickscan Platform"
                         style="width: 200px; height: auto; opacity: 0.7; transition: opacity 0.3s ease;"
                         onmouseover="this.style.opacity='1'"
                         onmouseout="this.style.opacity='0.7'" />
                </a>
            <?php else: ?>
                <a href="https://guardian360.eu/quickscan" target="_blank" rel="noopener noreferrer">
                    ⚡ <?php echo esc_html(__('Powered by Guardian360', 'quickscan-connector')); ?>
                </a>
            <?php endif; ?>
        </div>
    </div> <!-- End Centered Content Container -->
</div>

<script>
jQuery(document).ready(function($) {
    $('#quickscan-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $button = $('#start-scan-btn');
        var $status = $('#scan-status');
        var $results = $('#scan-results');
        
        // Get form data
        var url = $('#scan_url').val();
        var nonce = $('#quickscan_nonce').val();

        console.log('Form data:', { url: url, nonce: nonce });
        
        // Validate URL
        if (!url) {
            alert('<?php _e('Please enter a valid URL', 'quickscan-connector'); ?>');
            return;
        }
        
        // Update UI
        $button.prop('disabled', true).val('<?php _e('Scanning...', 'quickscan-connector'); ?>');
        $status.show();
        $results.hide();
        
        // Start scan
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'quickscan_start_scan',
                url: url,
                is_frontend: 'false',
                nonce: nonce
            },
            success: function(response) {
                console.log('AJAX Success Response:', response);
                
                // Handle mixed HTML/JSON response
                var jsonResponse = response;
                if (typeof response === 'string' && response.indexOf('{"success"') > -1) {
                    try {
                        // Extract JSON part from mixed HTML/JSON response
                        var jsonStart = response.indexOf('{"success"');
                        jsonResponse = JSON.parse(response.substring(jsonStart));
                    } catch (e) {
                        console.log('Failed to parse JSON from response:', e);
                        jsonResponse = null;
                    }
                }
                
                if (jsonResponse && jsonResponse.success) {
                    $('#scan-progress').html('<p class="success"><?php _e('Scan completed successfully!', 'quickscan-connector'); ?></p>');

                    // Display results
                    $results.show();
                    if (jsonResponse.data) {
                        // Use AJAX to format results server-side for consistency
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                action: 'quickscan_format_results',
                                results: JSON.stringify(jsonResponse.data),
                                nonce: nonce
                            },
                            success: function(formatted) {
                                if (formatted.success) {
                                    $('#results-content').html(formatted.data);
                                } else {
                                    // Fallback to JSON display
                                    $('#results-content').html('<details><summary>View Raw JSON Results</summary><pre>' + JSON.stringify(jsonResponse.data, null, 2) + '</pre></details>');
                                }
                            },
                            error: function() {
                                // Fallback to JSON display
                                $('#results-content').html('<details><summary>View Raw JSON Results</summary><pre>' + JSON.stringify(jsonResponse.data, null, 2) + '</pre></details>');
                            }
                        });
                    } else {
                        $('#results-content').html('<p>Scan completed but no data returned.</p>');
                    }
                } else {
                    var errorMsg = jsonResponse && jsonResponse.data ? jsonResponse.data : 'Unknown error occurred';
                    $('#scan-progress').html('<p class="error">Error: ' + errorMsg + '</p>');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', xhr, status, error);
                console.log('Response:', xhr.responseText);
                $('#scan-progress').html('<p class="error">Scan failed: ' + error + ' (Check browser console for details)</p>');
            },
            complete: function() {
                $button.prop('disabled', false).val('<?php _e('Start Scan', 'quickscan-connector'); ?>');
            }
        });
    });
});
</script>

<style>
#scan-results pre {
    background: #f1f1f1;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 400px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.success {
    color: #46b450;
    font-weight: bold;
}

.error {
    color: #dc3232;
    font-weight: bold;
}
</style>