jQuery(document).ready(function($) {
    
    // Connection test functionality
    $('#test-connection').on('click', function() {
        var $button = $(this);
        var $result = $('#connection-result');
        
        $button.prop('disabled', true);
        $result.html('<p><span class="quickscan-loading"></span>Testing connection...</p>');
        
        $.ajax({
            url: quickscan_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'quickscan_test_connection',
                nonce: quickscan_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $result.html('<div class="notice notice-success"><p><strong>Success!</strong> ' + response.data + '</p></div>');
                } else {
                    $result.html('<div class="notice notice-error"><p><strong>Error:</strong> ' + response.data + '</p></div>');
                }
            },
            error: function(xhr, status, error) {
                $result.html('<div class="notice notice-error"><p><strong>Connection test failed:</strong> ' + error + '</p></div>');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
    
    // Auto-refresh scan status (for future use)
    function refreshScanStatus() {
        var $statusElements = $('.scan-status[data-scan-id]');
        
        if ($statusElements.length > 0) {
            $statusElements.each(function() {
                var $element = $(this);
                var scanId = $element.data('scan-id');
                var currentStatus = $element.data('current-status');
                
                // Only check pending/running scans
                if (currentStatus === 'pending' || currentStatus === 'running') {
                    // This would make an API call to check scan status
                    // Implementation depends on how the API handles status checks
                }
            });
        }
    }
    
    // Auto-refresh every 30 seconds for active scans
    setInterval(refreshScanStatus, 30000);
    
    // Form validation
    $('form[id*="quickscan"]').on('submit', function(e) {
        var $form = $(this);
        var $urlField = $form.find('input[type="url"]');
        
        if ($urlField.length > 0 && $urlField.val()) {
            var url = $urlField.val();
            
            // Basic URL validation
            if (!url.match(/^https?:\/\/.+/)) {
                e.preventDefault();
                alert('Please enter a valid URL starting with http:// or https://');
                $urlField.focus();
                return false;
            }
        }
    });
    
    // Results table enhancements
    $('.wp-list-table').on('click', '.view-scan-details', function(e) {
        e.preventDefault();
        var scanId = $(this).data('scan-id');
        // Could implement modal or inline expansion here
    });
    
    // Copy scan results to clipboard
    $(document).on('click', '.copy-results', function() {
        var $button = $(this);
        var $resultsContainer = $button.closest('.scan-results').find('pre');
        
        if ($resultsContainer.length > 0) {
            var text = $resultsContainer.text();
            
            // Create temporary textarea to copy text
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            
            try {
                document.execCommand('copy');
                $button.text('Copied!').addClass('button-disabled');
                
                setTimeout(function() {
                    $button.text('Copy Results').removeClass('button-disabled');
                }, 2000);
            } catch (err) {
                alert('Failed to copy results');
            }
            
            $temp.remove();
        }
    });
    
    // Expandable results sections
    $(document).on('click', '.toggle-section', function() {
        var $button = $(this);
        var $section = $button.next('.expandable-section');
        
        if ($section.is(':visible')) {
            $section.slideUp();
            $button.text($button.data('expand-text') || 'Show Details');
        } else {
            $section.slideDown();
            $button.text($button.data('collapse-text') || 'Hide Details');
        }
    });
    
    // Settings page enhancements
    $('input[name="quickscan_api_url"]').on('blur', function() {
        var url = $(this).val();
        if (url && !url.match(/\/api\/?$/)) {
            $(this).after('<p class="description warning">⚠️ URL should typically end with /api</p>');
        }
    });
    
});