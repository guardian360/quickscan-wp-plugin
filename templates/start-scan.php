<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Start New Scan', 'quickscan-connector'); ?></h1>
    
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
            
            <tr>
                <th scope="row">
                    <label for="scan_type"><?php _e('Scan Type', 'quickscan-connector'); ?></label>
                </th>
                <td>
                    <select id="scan_type" name="scan_type">
                        <option value="quick"><?php _e('Quick Scan', 'quickscan-connector'); ?></option>
                        <option value="full"><?php _e('Full Scan', 'quickscan-connector'); ?></option>
                    </select>
                    <p class="description">
                        <?php _e('Choose the type of security scan to perform', 'quickscan-connector'); ?>
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
    
    <?php if (get_option('quickscan_show_signature', true)): ?>
    <div class="quickscan-signature">
        <a href="https://guardian360.eu" target="_blank" rel="noopener noreferrer">
            ⚡ Powered by Guardian360
        </a>
    </div>
    <?php endif; ?>
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
        var scanType = $('#scan_type').val();
        var nonce = $('#quickscan_nonce').val();
        
        console.log('Form data:', { url: url, scanType: scanType, nonce: nonce });
        
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
                scan_type: scanType,
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
                        // Format the results nicely
                        var data = jsonResponse.data.data;
                        var html = '<div class="scan-results-formatted">';
                        
                        if (data && data.Info) {
                            html += '<div class="security-score"><h4>Security Score: <span class="score-value">' + data.Info.Score + '/100</span></h4></div>';
                            html += '<div class="scanned-url"><strong>URL:</strong> ' + data.Info.URL + '</div>';
                        }
                        
                        // Show security headers status
                        if (data && data.security_headers) {
                            html += '<div class="security-headers"><h4>Security Headers:</h4>';
                            for (var header in data.security_headers) {
                                var headerData = data.security_headers[header];
                                if (headerData && headerData.length > 0) {
                                    html += '<div class="header-item secure">✓ ' + header + ': <span class="success">Configured</span></div>';
                                } else {
                                    html += '<div class="header-item vulnerable">✗ ' + header + ': <span class="error">Missing</span></div>';
                                }
                            }
                            html += '</div>';
                        }
                        
                        // Show misconfigurations
                        if (data && data.Misconfigurations) {
                            html += '<div class="misconfigurations"><h4>Misconfigurations:</h4>';
                            for (var config in data.Misconfigurations) {
                                var configData = data.Misconfigurations[config];
                                if (configData && configData.Vulnerable) {
                                    html += '<div class="config-item vulnerable">⚠️ ' + config + ': <span class="error">' + configData.Issue + '</span></div>';
                                }
                            }
                            html += '</div>';
                        }
                        
                        html += '<details><summary>View Raw JSON Results</summary><pre>' + JSON.stringify(jsonResponse.data, null, 2) + '</pre></details>';
                        html += '</div>';
                        
                        $('#results-content').html(html);
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