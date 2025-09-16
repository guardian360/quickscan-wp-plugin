<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get shortcode attributes
$scan_type = $atts['type'];
$show_results = $atts['show_results'] === 'true';
$title = isset($atts['title']) ? $atts['title'] : '';
$placeholder = isset($atts['placeholder']) ? $atts['placeholder'] : 'Enter website URL to scan...';
$button_text = isset($atts['button_text']) ? $atts['button_text'] : 'Start Security Scan';
?>

<div class="quickscan-frontend-block"
     data-scan-type="<?php echo esc_attr($scan_type); ?>"
     data-show-results="<?php echo $show_results ? 'true' : 'false'; ?>"
     data-placeholder="<?php echo esc_attr($placeholder); ?>"
     data-button-text="<?php echo esc_attr($button_text); ?>"
     data-title="<?php echo esc_attr($title); ?>"
     data-show-title="<?php echo !empty($title) ? 'true' : 'false'; ?>">
    <!-- Content will be rendered by frontend JavaScript -->
</div>