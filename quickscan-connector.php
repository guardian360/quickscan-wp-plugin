<?php
/**
 * Plugin Name: Quickscan Connector
 * Plugin URI: https://github.com/guardian360/quickscan-wp-plugin
 * Description: WordPress plugin to connect and interact with the Quickscan API for security scanning
 * Version: 1.0.0
 * Author: Guardian360
 * Author URI: https://guardian360.eu/quickscan
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: quickscan-connector
 * Domain Path: /languages
 *
 * IMPORTANT LEGAL NOTICE:
 * This plugin is licensed under GPLv2 as required by WordPress.org guidelines.
 * However, the Quickscan API service remains proprietary and requires proper authorization.
 * Unauthorized use of the Quickscan API service is prohibited and may result in:
 * - Termination of API access
 * - Legal action for breach of terms of service
 * - Liability for damages
 *
 * The GPL license applies ONLY to this WordPress plugin code, NOT to:
 * - The Quickscan API service
 * - Guardian360 trademarks and branding
 * - Proprietary security scanning algorithms
 *
 * For API access and terms of service, visit: https://quickscan.guardian360.nl
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('QUICKSCAN_VERSION', '1.0.1');
define('QUICKSCAN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('QUICKSCAN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('QUICKSCAN_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Quickscan Connector Class
 */
class QuickscanConnector {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * API Base URL - can be configured in settings
     */
    private $api_base_url = '';
    
    /**
     * API Key for authentication
     */
    private $api_key = '';
    
    /**
     * User email for Quickscan authentication
     */
    private $user_email = '';
    
    /**
     * User password for Quickscan authentication
     */
    private $user_password = '';
    
    /**
     * API token for authenticated requests
     */
    private $api_token = '';
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_settings();
        $this->init_updater();
        $this->load_formatter();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        
        // Load text domain for translations
        add_action('init', [$this, 'load_textdomain']);

        // Admin hooks
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        
        // API hooks
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        
        // Shortcode for frontend display
        add_shortcode('quickscan', [$this, 'render_quickscan_shortcode']);
        
        // AJAX handlers
        add_action('wp_ajax_quickscan_start_scan', [$this, 'ajax_start_scan']);
        add_action('wp_ajax_nopriv_quickscan_start_scan', [$this, 'ajax_start_scan']);
        add_action('wp_ajax_quickscan_send_pdf', [$this, 'ajax_send_pdf']);
        add_action('wp_ajax_nopriv_quickscan_send_pdf', [$this, 'ajax_send_pdf']);
        add_action('wp_ajax_quickscan_send_email_report', [$this, 'ajax_send_email_report']);
        add_action('wp_ajax_nopriv_quickscan_send_email_report', [$this, 'ajax_send_email_report']);
        add_action('wp_ajax_quickscan_test_connection', [$this, 'ajax_test_connection']);
        add_action('wp_ajax_quickscan_save_credentials', [$this, 'ajax_save_credentials']);
        add_action('wp_ajax_quickscan_test_credentials', [$this, 'ajax_test_credentials']);
        add_action('wp_ajax_quickscan_format_results', [$this, 'ajax_format_results']);
        add_action('wp_ajax_nopriv_quickscan_format_results', [$this, 'ajax_format_results']);

        // Gutenberg blocks
        add_action('init', [$this, 'register_blocks']);
        
        // Frontend assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);

        // Frontend localization - use wp_footer to ensure textdomain is loaded
        add_action('wp_footer', [$this, 'localize_frontend_scripts']);
        
        // Widget registration
        add_action('widgets_init', [$this, 'register_widgets']);
        
    }
    
    /**
     * Load settings from WordPress options
     */
    private function load_settings() {
        // Determine API version based on user credentials
        $current_user_id = get_current_user_id();
        if ($current_user_id) {
            $this->user_email = get_user_meta($current_user_id, 'quickscan_email', true);
            $this->user_password = get_user_meta($current_user_id, 'quickscan_password', true);

            // Decrypt password if stored
            if ($this->user_password) {
                $this->user_password = $this->decrypt_password($this->user_password);
            }

            // User-specific token storage
            $this->api_token = get_transient('quickscan_api_token_' . $current_user_id);
        }

        // Use v1 API for authenticated Pro users, v2 for free users
        if (!empty($this->user_email) && !empty($this->user_password)) {
            $this->api_base_url = 'https://quickscan.guardian360.nl/api/v1';
        } else {
            // Free version uses v2 API (no authentication required)
            $this->api_base_url = 'https://quickscan.guardian360.nl/api/v2';
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        // Tables not needed - using Quickscan API storage
        
        // Set default options
        add_option('quickscan_enable_logging', true);
        add_option('quickscan_show_signature', true);
        add_option('quickscan_signature_style', 'logo');
        add_option('quickscan_signature_text', 'Powered by Guardian360'); // Use literal string during activation
        add_option('quickscan_api_version', 'v2'); // Default to free version
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up scheduled tasks
        wp_clear_scheduled_hook('quickscan_daily_scan');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Initialize GitHub updater
     */
    private function init_updater() {
        if (is_admin()) {
            require_once plugin_dir_path(__FILE__) . 'includes/class-github-updater.php';
            new Quickscan_GitHub_Updater(__FILE__);
        }
    }

    /**
     * Load the results formatter
     */
    private function load_formatter() {
        require_once plugin_dir_path(__FILE__) . 'includes/class-results-formatter.php';
    }

    /**
     * Add admin menu items
     */
    public function add_admin_menu() {
        // Official Guardian360 shield icon with brand colors
        $icon_svg = 'data:image/svg+xml;base64,' . base64_encode(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">' .
            '<defs>' .
                '<linearGradient id="guardian-gradient" x1="0%" y1="0%" x2="100%" y2="100%">' .
                    '<stop offset="0%" stop-color="#2E3285"/>' .
                    '<stop offset="100%" stop-color="#9089c1"/>' .
                '</linearGradient>' .
            '</defs>' .
            '<path d="M12 2L4 6v5c0 5.55 3.84 10.74 8 12 4.16-1.26 8-6.45 8-12V6l-8-4z" fill="currentColor"/>' .
            '<path d="M12 6L9 8v3c0 2.76 1.92 5.37 3 6 1.08-0.63 3-3.24 3-6V8l-3-2z" fill="white" opacity="0.9"/>' .
            '<circle cx="12" cy="9.5" r="1.5" fill="white"/>' .
            '<path d="M12 14c-0.5 0-1-0.2-1-0.5s0.5-0.5 1-0.5 1 0.2 1 0.5-0.5 0.5-1 0.5z" fill="#E67E22"/>' .
            '</svg>'
        );

        // Main menu - using literal strings to avoid early translation loading
        add_menu_page(
            'Quickscan',
            'Quickscan',
            'manage_options',
            'quickscan',
            [$this, 'render_dashboard_page'],
            $icon_svg,
            30
        );

        // Submenu pages - using literal strings to avoid early translation loading
        add_submenu_page(
            'quickscan',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'quickscan',
            [$this, 'render_dashboard_page']
        );

        add_submenu_page(
            'quickscan',
            'Start Scan',
            'Start Scan',
            'manage_options',
            'quickscan-start-scan',
            [$this, 'render_start_scan_page']
        );

        add_submenu_page(
            'quickscan',
            'Request Account',
            'Request Account',
            'manage_options',
            'quickscan-account-request',
            [$this, 'render_account_request_page']
        );

        add_submenu_page(
            'quickscan',
            'Settings',
            'Settings',
            'manage_options',
            'quickscan-settings',
            [$this, 'render_settings_page']
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('quickscan_settings', 'quickscan_enable_logging');
        register_setting('quickscan_settings', 'quickscan_show_signature');
        register_setting('quickscan_settings', 'quickscan_signature_style');
    }
    
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'quickscan') === false) {
            return;
        }
        
        wp_enqueue_style(
            'quickscan-admin',
            QUICKSCAN_PLUGIN_URL . 'assets/css/admin.css',
            [],
            QUICKSCAN_VERSION
        );
        
        wp_enqueue_script(
            'quickscan-admin',
            QUICKSCAN_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            QUICKSCAN_VERSION,
            true
        );
        
        wp_localize_script('quickscan-admin', 'quickscan_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('quickscan_nonce')
        ]);
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('quickscan/v1', '/scan', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_start_scan'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ]);
        
        register_rest_route('quickscan/v1', '/scan/(?P<id>[a-zA-Z0-9-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_scan_status'],
            'permission_callback' => '__return_true'
        ]);
    }
    
    /**
     * Render dashboard page
     */
    public function render_dashboard_page() {
        include QUICKSCAN_PLUGIN_DIR . 'templates/dashboard.php';
    }
    
    /**
     * Render start scan page
     */
    public function render_start_scan_page() {
        include QUICKSCAN_PLUGIN_DIR . 'templates/start-scan.php';
    }
    
    
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        include QUICKSCAN_PLUGIN_DIR . 'templates/settings.php';
    }

    /**
     * Render account request page
     */
    public function render_account_request_page() {
        include QUICKSCAN_PLUGIN_DIR . 'templates/account-request.php';
    }

    /**
     * Render shortcode
     */
    public function render_quickscan_shortcode($atts) {
        $atts = shortcode_atts([
            'show_results' => 'true',
            'title' => '',
            'placeholder' => 'Enter website URL to scan...',
            'button_text' => 'Start Security Scan'
        ], $atts);
        
        ob_start();
        include QUICKSCAN_PLUGIN_DIR . 'templates/shortcode.php';
        return ob_get_clean();
    }
    
    /**
     * AJAX handler for starting scan
     */
    public function ajax_start_scan() {
        // Log received data for debugging (only in debug mode)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Quickscan AJAX received: ' . print_r($_POST, true));
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'quickscan_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        $url = sanitize_text_field($_POST['url'] ?? '');
        $is_frontend = sanitize_text_field($_POST['is_frontend'] ?? 'false') === 'true';
        
        // Validate input
        if (empty($url)) {
            wp_send_json_error('URL is required');
            return;
        }
        
        // Determine if using v2 (free) or v1 (pro) API
        $is_v2 = strpos($this->api_base_url, '/v2') !== false;

        // Call Quickscan API with appropriate parameters
        if ($is_v2) {
            // v2 API only needs URL
            $result = $this->call_api('POST', 'scan', [
                'url' => $url
            ]);
        } else {
            // v1 API supports language parameter
            $result = $this->call_api('POST', 'scan', [
                'url' => $url,
                'language' => 'en'
            ]);
        }
        
        if (is_wp_error($result)) {
            wp_send_json_error('API Error: ' . $result->get_error_message());
            return;
        }
        
        if ($result) {
            // Results are stored in user's Quickscan account - no local storage needed
            wp_send_json_success($result);
        } else {
            wp_send_json_error('Failed to start scan - no response from API');
        }
    }
    
    /**
     * AJAX handler for sending PDF via email
     * This proxies the request to Quickscan which generates and sends the PDF
     */
    public function ajax_send_pdf() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'quickscan_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        // Collect and sanitize form data
        $email_data = [
            'url' => sanitize_url($_POST['url'] ?? ''),
            'company' => sanitize_text_field($_POST['company'] ?? ''),
            'firstname' => sanitize_text_field($_POST['firstname'] ?? ''),
            'surname' => sanitize_text_field($_POST['surname'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        ];
        
        // Validate required fields
        $required_fields = ['url', 'company', 'firstname', 'surname', 'email'];
        foreach ($required_fields as $field) {
            if (empty($email_data[$field])) {
                wp_send_json_error('Missing required field: ' . $field);
                return;
            }
        }
        
        // Validate email format
        if (!is_email($email_data['email'])) {
            wp_send_json_error('Invalid email address');
            return;
        }
        
        // Use correct Quickscan API endpoint for PDF reports
        $result = $this->call_api('POST', 'scan/report', $email_data);

        if (is_wp_error($result)) {
            wp_send_json_error('Failed to send email request: ' . $result->get_error_message());
            return;
        }

        // Handle Quickscan API response format
        if (isset($result['success']) && $result['success']) {
            wp_send_json_success($result['message'] ?? 'PDF report will be sent to your email by Quickscan');
        } else {
            $error_message = isset($result['message']) ? $result['message'] : 'Unknown error occurred';
            wp_send_json_error('Email request failed: ' . $error_message);
        }
    }

    /**
     * AJAX handler for sending email reports directly to Quickscan API
     * Includes proper validation and legal compliance notices
     */
    public function ajax_send_email_report() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'quickscan_nonce')) {
            wp_send_json_error('Security verification failed');
            return;
        }

        // Rate limiting check (simple implementation)
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $rate_limit_key = 'quickscan_email_rate_' . md5($client_ip);
        $rate_limit_count = get_transient($rate_limit_key);

        if ($rate_limit_count && $rate_limit_count >= 5) {
            wp_send_json_error('Rate limit exceeded. Please wait before sending another email.');
            return;
        }

        // Collect and sanitize form data
        $email_data = [
            'url' => sanitize_url($_POST['url'] ?? ''),
            'company' => sanitize_text_field($_POST['company'] ?? ''),
            'firstname' => sanitize_text_field($_POST['firstname'] ?? ''),
            'surname' => sanitize_text_field($_POST['surname'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        ];

        // Validate required fields
        $required_fields = ['url', 'company', 'firstname', 'surname', 'email'];
        foreach ($required_fields as $field) {
            if (empty($email_data[$field])) {
                wp_send_json_error("Required field '$field' is missing");
                return;
            }
        }

        // Validate email format
        if (!is_email($email_data['email'])) {
            wp_send_json_error('Please enter a valid email address');
            return;
        }

        // Validate URL format
        if (!filter_var($email_data['url'], FILTER_VALIDATE_URL)) {
            wp_send_json_error('Please enter a valid URL');
            return;
        }

        // Log the email request for compliance tracking
        $this->log_message('Email report requested', [
            'url' => $email_data['url'],
            'email' => $email_data['email'],
            'company' => $email_data['company'],
            'ip' => $client_ip,
            'timestamp' => current_time('mysql')
        ]);

        // Forward request to Quickscan API
        // Add WordPress plugin metadata to the request
        $request_data = array_merge($email_data, [
            'source' => 'wordpress_plugin',
            'plugin_version' => QUICKSCAN_VERSION,
            'site_url' => home_url()
        ]);

        // Use correct Quickscan API endpoint for PDF reports
        $result = $this->call_api('POST', 'scan/report', $request_data);

        // Update rate limiting counter
        set_transient($rate_limit_key, ($rate_limit_count ? $rate_limit_count + 1 : 1), HOUR_IN_SECONDS);

        if (is_wp_error($result)) {
            $this->log_error('Email API error: ' . $result->get_error_message());
            wp_send_json_error('Unable to connect to email service. Please try again later.');
            return;
        }

        if ($http_code >= 200 && $http_code < 300) {
            $this->log_message('Email report sent successfully', [
                'url' => $email_data['url'],
                'email' => $email_data['email']
            ]);
            wp_send_json_success('Your security report has been sent! Please check your email inbox (and spam folder).');
        } else {
            $this->log_error('Email API HTTP error: ' . $http_code . ' - ' . $response_body);
            wp_send_json_error('Service temporarily unavailable. Please try again in a few minutes.');
        }
    }


    /**
     * Encrypt password for storage using secure AES-256-GCM
     *
     * Uses authenticated encryption with randomly generated IV and tag
     * for maximum security. Cannot be reverse engineered.
     */
    private function encrypt_password($password) {
        // Derive key from WordPress AUTH_KEY using PBKDF2
        $salt = defined('SECURE_AUTH_KEY') ? SECURE_AUTH_KEY : 'quickscan_secure_salt';
        $key = hash_pbkdf2('sha256', AUTH_KEY ?: 'fallback_key', $salt, 10000, 32, true);

        // Generate cryptographically secure random IV
        $iv = openssl_random_pseudo_bytes(16);

        // Encrypt with AES-256-GCM for authenticated encryption
        $encrypted = openssl_encrypt($password, 'AES-256-GCM', $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($encrypted === false) {
            return false;
        }

        // Return base64 encoded: IV + tag + encrypted data
        return base64_encode($iv . $tag . $encrypted);
    }

    /**
     * Decrypt password from storage using secure AES-256-GCM
     *
     * Verifies authenticity and integrity before decryption
     */
    private function decrypt_password($encrypted_data) {
        if (empty($encrypted_data)) {
            return false;
        }

        $data = base64_decode($encrypted_data);
        if ($data === false || strlen($data) < 32) { // IV(16) + tag(16) minimum
            return false;
        }

        // Extract IV (first 16 bytes), tag (next 16 bytes), encrypted data (rest)
        $iv = substr($data, 0, 16);
        $tag = substr($data, 16, 16);
        $encrypted = substr($data, 32);

        // Derive same key used for encryption
        $salt = defined('SECURE_AUTH_KEY') ? SECURE_AUTH_KEY : 'quickscan_secure_salt';
        $key = hash_pbkdf2('sha256', AUTH_KEY ?: 'fallback_key', $salt, 10000, 32, true);

        // Decrypt and verify authenticity
        $decrypted = openssl_decrypt($encrypted, 'AES-256-GCM', $key, OPENSSL_RAW_DATA, $iv, $tag);

        return $decrypted;
    }
    
    /**
     * Save user credentials
     */
    public function save_user_credentials($email, $password, $use_wp_credentials = false) {
        $current_user_id = get_current_user_id();
        if (!$current_user_id) {
            return false;
        }
        
        if ($use_wp_credentials) {
            $wp_user = get_userdata($current_user_id);
            $email = $wp_user->user_email;
            // For WordPress credentials, we'd need to handle password hashing differently
            // This is a placeholder - password management would need more careful consideration
            $password = wp_generate_password(12); // Generate a new password for Quickscan
        }
        
        // Store encrypted credentials
        update_user_meta($current_user_id, 'quickscan_email', sanitize_email($email));
        update_user_meta($current_user_id, 'quickscan_password', $this->encrypt_password($password));
        
        // Clear any existing token to force re-authentication
        delete_transient('quickscan_api_token_' . $current_user_id);
        
        return true;
    }

    /**
     * Clear stored user credentials
     */
    public function clear_user_credentials() {
        $current_user_id = get_current_user_id();
        if (!$current_user_id) {
            return false;
        }

        // Remove stored credentials
        delete_user_meta($current_user_id, 'quickscan_email');
        delete_user_meta($current_user_id, 'quickscan_password');

        // Clear any existing token
        delete_transient('quickscan_api_token_' . $current_user_id);

        return true;
    }

    /**
     * Authenticate with Guardian360 API and get token
     */
    private function authenticate() {
        if (empty($this->user_email) || empty($this->user_password)) {
            return new WP_Error('no_credentials', 'No Quickscan credentials found. Please configure your credentials in the settings.');
        }
        
        $login_url = $this->api_base_url . '/login';
        
        $args = [
            'method' => 'POST',
            'body' => [
                'email' => $this->user_email,
                'password' => base64_encode($this->user_password)
            ],
            'timeout' => 30
        ];
        
        $response = wp_remote_request($login_url, $args);
        
        if (is_wp_error($response)) {
            $this->log_error('Authentication failed: ' . $response->get_error_message());
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $auth_data = json_decode($body, true);
        
        if (isset($auth_data['data']['token'])) {
            $token = $auth_data['data']['token'];
            $current_user_id = get_current_user_id();
            // Store token for 1 hour (3600 seconds) per user
            set_transient('quickscan_api_token_' . $current_user_id, $token, 3600);
            $this->api_token = $token;
            return true;
        }
        
        $this->log_error('Authentication failed: Invalid credentials or response format');
        return false;
    }
    
    /**
     * Ensure we have a valid API token (only for v1 API)
     */
    private function ensure_authenticated() {
        // v2 API doesn't require authentication
        if (strpos($this->api_base_url, '/v2') !== false) {
            return true;
        }

        // v1 API requires authentication
        if (empty($this->api_token)) {
            return $this->authenticate();
        }
        return true;
    }
    
    /**
     * Call Quickscan API (v1 or v2)
     */
    private function call_api($method, $endpoint, $data = null) {
        // Check if using v2 (free) or v1 (pro) API
        $is_v2 = strpos($this->api_base_url, '/v2') !== false;

        // Only authenticate for v1 API
        if (!$is_v2 && !$this->ensure_authenticated()) {
            return new WP_Error('auth_failed', 'Failed to authenticate with Quickscan API');
        }

        $url = trailingslashit($this->api_base_url) . ltrim($endpoint, '/');

        $args = [
            'method' => $method,
            'headers' => [
                'Accept' => 'application/json'
            ],
            'timeout' => 120
        ];

        // Add authorization header only for v1 API
        if (!$is_v2 && $this->api_token) {
            $args['headers']['Authorization'] = 'Bearer ' . $this->api_token;
        }
        
        // Handle data based on method
        if ($data && !empty($data)) {
            if ($method === 'GET') {
                // For GET requests, add data as query parameters
                $query_string = http_build_query($data);
                $url .= (strpos($url, '?') !== false ? '&' : '?') . $query_string;
            } elseif ($method === 'POST') {
                // For scan endpoint, URL is passed as query parameter
                if ($endpoint === 'scan' && isset($data['url'])) {
                    $url .= '?url=' . urlencode($data['url']);
                    unset($data['url']); // Remove from data since it's in query string
                }
                
                // Add remaining data as form fields
                if (!empty($data)) {
                    $args['body'] = $data;
                }
            }
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            $this->log_error('API call failed: ' . $response->get_error_message());
            return $response;
        }
        
        $http_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        // If we get 401 (unauthorized), try to re-authenticate once
        if ($http_code === 401) {
            $current_user_id = get_current_user_id();
            delete_transient('quickscan_api_token_' . $current_user_id);
            $this->api_token = null;
            
            $auth_result = $this->authenticate();
            if ($auth_result === true) {
                // Retry the request with new token
                $args['headers']['Authorization'] = 'Bearer ' . $this->api_token;
                $response = wp_remote_request($url, $args);
                
                if (is_wp_error($response)) {
                    return $response;
                }
                
                $body = wp_remote_retrieve_body($response);
            } else {
                return is_wp_error($auth_result) ? $auth_result : new WP_Error('auth_failed', 'Failed to re-authenticate with Quickscan API');
            }
        }
        
        return json_decode($body, true);
    }
    
    
    
    
    /**
     * AJAX handler for testing API connection
     */
    public function ajax_test_connection() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'quickscan_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        // Test API connection by making a simple request
        $test_result = $this->test_api_connection();
        
        if ($test_result['success']) {
            wp_send_json_success($test_result['message']);
        } else {
            wp_send_json_error($test_result['message']);
        }
    }
    
    /**
     * Test API connection
     */
    private function test_api_connection() {
        // Try to make a test scan request to verify API is accessible
        $test_url = 'https://example.com';
        $result = $this->call_api('POST', '/scan', [
            'url' => $test_url
        ]);
        
        if (is_wp_error($result)) {
            return [
                'success' => false,
                'message' => 'API connection failed: ' . $result->get_error_message()
            ];
        }
        
        if (isset($result['error'])) {
            return [
                'success' => false,
                'message' => 'API error: ' . $result['error']
            ];
        }
        
        return [
            'success' => true,
            'message' => 'API connection successful!'
        ];
    }
    
    /**
     * AJAX handler for saving user credentials
     */
    public function ajax_save_credentials() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'quickscan_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        // Check user permissions
        if (!current_user_can('manage_options') && !current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        $email = sanitize_email($_POST['email'] ?? '');
        $password = sanitize_text_field($_POST['password'] ?? '');
        $use_wp_credentials = ($_POST['use_wp_credentials'] ?? '') === 'true';

        // Check if this is a clear credentials request (both email and password empty)
        if (empty($email) && empty($password)) {
            // Clear credentials
            if ($this->clear_user_credentials()) {
                wp_send_json_success('Credentials cleared successfully');
            } else {
                wp_send_json_error('Failed to clear credentials');
            }
            return;
        }

        // For saving credentials, both email and password are required
        if (empty($email) || empty($password)) {
            wp_send_json_error('Email and password are required');
            return;
        }

        if ($this->save_user_credentials($email, $password, $use_wp_credentials)) {
            wp_send_json_success('Credentials saved successfully');
        } else {
            wp_send_json_error('Failed to save credentials');
        }
    }
    
    /**
     * AJAX handler for testing user credentials
     */
    public function ajax_test_credentials() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'quickscan_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        // Check user permissions
        if (!current_user_can('manage_options') && !current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        $email = sanitize_email($_POST['email'] ?? '');
        $password = sanitize_text_field($_POST['password'] ?? '');
        
        if (empty($email) || empty($password)) {
            wp_send_json_error('Email and password are required');
            return;
        }
        
        // Test credentials by attempting to authenticate
        $login_url = $this->api_base_url . '/login';
        
        $args = [
            'method' => 'POST',
            'body' => [
                'email' => $email,
                'password' => base64_encode($password)
            ],
            'timeout' => 30
        ];
        
        $response = wp_remote_request($login_url, $args);
        
        if (is_wp_error($response)) {
            wp_send_json_error('Connection failed: ' . $response->get_error_message());
            return;
        }
        
        $body = wp_remote_retrieve_body($response);
        $auth_data = json_decode($body, true);
        
        if (isset($auth_data['data']['token'])) {
            wp_send_json_success('Credentials are valid!');
        } else {
            wp_send_json_error('Invalid credentials. Please check your email and password.');
        }
    }

    /**
     * AJAX handler to format scan results
     */
    public function ajax_format_results() {
        check_ajax_referer('quickscan_nonce', 'nonce');

        $results = isset($_POST['results']) ? json_decode(stripslashes($_POST['results']), true) : null;

        if (!$results) {
            wp_send_json_error('No results provided');
        }

        // Include CSS styles
        $html = Quickscan_Results_Formatter::get_styles();

        // Format the results
        $html .= Quickscan_Results_Formatter::format_results($results, !is_admin());

        wp_send_json_success($html);
    }
    
    /**
     * Register Gutenberg blocks
     */
    public function register_blocks() {
        // Check if function exists (Gutenberg is active)
        if (!function_exists('register_block_type')) {
            return;
        }
        
        // Register the security scanner block - using literal strings to avoid early translation loading
        $block_args = array(
            'api_version' => 2,
            'title' => 'Security Scanner',
            'description' => 'Add a security scanner form to scan websites for vulnerabilities',
            'category' => 'common',
            'icon' => 'shield',
            'keywords' => array('security', 'scan', 'quickscan', 'vulnerability'),
            'supports' => array(
                'html' => false,
                'align' => array('left', 'center', 'right', 'wide', 'full')
            ),
            'attributes' => array(
                'showResults' => array(
                    'type' => 'boolean', 
                    'default' => true
                ),
                'placeholder' => array(
                    'type' => 'string',
                    'default' => 'Enter website URL to scan...'
                ),
                'buttonText' => array(
                    'type' => 'string',
                    'default' => 'Start Security Scan'
                ),
                'title' => array(
                    'type' => 'string',
                    'default' => 'Website Security Scanner'
                ),
                'showTitle' => array(
                    'type' => 'boolean',
                    'default' => true
                )
            ),
            'editor_script' => 'quickscan-block-editor',
            'editor_style' => 'quickscan-block-editor-style',
            'style' => 'quickscan-block-style',
            'render_callback' => array($this, 'render_security_scanner_block')
        );
        
        register_block_type('quickscan/security-scanner', $block_args);
        
        // Enqueue block editor assets
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Block styles (for both editor and frontend)
        wp_enqueue_style(
            'quickscan-block-style',
            QUICKSCAN_PLUGIN_URL . 'blocks/security-scanner/style.css',
            [],
            QUICKSCAN_VERSION
        );
        
        // Frontend CSS for Guardian360-style results
        wp_enqueue_style(
            'quickscan-frontend',
            QUICKSCAN_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            QUICKSCAN_VERSION
        );
        
        // Frontend JavaScript
        wp_enqueue_script(
            'quickscan-frontend',
            QUICKSCAN_PLUGIN_URL . 'assets/js/frontend.js',
            [],
            QUICKSCAN_VERSION,
            true
        );
        
        // Localization moved to separate function to avoid early translation loading
    }

    /**
     * Localize frontend scripts - runs after textdomain is loaded
     */
    public function localize_frontend_scripts() {
        // Only run if the script is enqueued
        if (!wp_script_is('quickscan-frontend', 'enqueued')) {
            return;
        }

        // Determine if current user has Pro credentials
        $current_user_id = get_current_user_id();
        $has_pro_credentials = false;
        if ($current_user_id) {
            $user_email = get_user_meta($current_user_id, 'quickscan_email', true);
            $user_password = get_user_meta($current_user_id, 'quickscan_password', true);
            $has_pro_credentials = !empty($user_email) && !empty($user_password);
        }

        wp_localize_script('quickscan-frontend', 'quickscan_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('quickscan_nonce'),
            'show_signature' => true, // Always show attribution now
            'signature_style' => get_option('quickscan_signature_style', 'logo'),
            'signature_text' => __('Powered by Guardian360', 'quickscan-connector'),
            'signature_url' => 'https://guardian360.eu/quickscan',
            'logo_url' => QUICKSCAN_PLUGIN_URL . 'assets/images/logo_guardian360_quickscan.png',
            'is_pro' => $has_pro_credentials ? 'true' : 'false',
            'admin_url' => admin_url(),
            'strings' => [
                'enter_valid_url' => __('Please enter a valid URL', 'quickscan-connector'),
                'scanning' => __('Scanning...', 'quickscan-connector'),
                'scan_completed' => __('✓ Scan completed successfully!', 'quickscan-connector'),
                'scan_failed' => __('✗ Scan failed:', 'quickscan-connector'),
                'security_score' => __('Security Score', 'quickscan-connector'),
                'scanned_url' => __('Scanned URL:', 'quickscan-connector'),
                'security_headers' => __('Security Headers', 'quickscan-connector'),
                'configured' => __('Configured', 'quickscan-connector'),
                'missing' => __('Missing', 'quickscan-connector'),
                'security_issues' => __('Security Issues & Recommendations', 'quickscan-connector'),
                'how_to_fix' => __('💡 How to Fix This', 'quickscan-connector'),
                'dns_security' => __('DNS Security', 'quickscan-connector'),
                'issue' => __('Issue', 'quickscan-connector'),
                'no_misconfigurations' => __('✓ No major security misconfigurations detected', 'quickscan-connector'),
                'technical_details' => __('View Technical Details', 'quickscan-connector'),
                'gdpr_notice' => __('This scan is performed in real-time and results are not stored on our servers. Your data is processed according to GDPR regulations.', 'quickscan-connector'),
                'email_pdf' => __('Email PDF Report', 'quickscan-connector'),
                'company' => __('Company', 'quickscan-connector'),
                'first_name' => __('First Name', 'quickscan-connector'),
                'last_name' => __('Last Name', 'quickscan-connector'),
                'email' => __('Email Address', 'quickscan-connector'),
                'phone' => __('Phone', 'quickscan-connector'),
                'optional' => __('optional', 'quickscan-connector'),
                'privacy_notice' => __('Your information will be used solely to email you the PDF report. We respect your privacy and will not share your data with third parties.', 'quickscan-connector'),
                'cancel' => __('Cancel', 'quickscan-connector'),
                'send_pdf' => __('Send PDF Report', 'quickscan-connector'),
                'sending' => __('Sending...', 'quickscan-connector'),
                'email_sent' => __('Email Sent!', 'quickscan-connector'),
                'check_email' => __('Please check your email for the PDF security report.', 'quickscan-connector'),
                'close' => __('Close', 'quickscan-connector'),
                'try_again' => __('Try Again', 'quickscan-connector')
            ]
        ]);
    }

    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'quickscan-block-editor',
            QUICKSCAN_PLUGIN_URL . 'blocks/security-scanner/index.compiled.js',
            array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components'),
            QUICKSCAN_VERSION,
            true
        );
        
        wp_enqueue_style(
            'quickscan-block-editor-style',
            QUICKSCAN_PLUGIN_URL . 'blocks/security-scanner/editor.css',
            array(),
            QUICKSCAN_VERSION
        );
    }
    
    /**
     * Render security scanner block on frontend
     */
    public function render_security_scanner_block($attributes, $content) {
        $defaults = array(
            'showResults' => true,
            'placeholder' => 'Enter website URL to scan...',
            'buttonText' => 'Start Security Scan',
            'title' => 'Website Security Scanner',
            'showTitle' => true
        );
        
        $attributes = wp_parse_args($attributes, $defaults);
        
        ob_start();
        ?>
        <div class="wp-block-quickscan-security-scanner">
            <div class="quickscan-frontend-block"
                 data-show-results="<?php echo $attributes['showResults'] ? 'true' : 'false'; ?>"
                 data-placeholder="<?php echo esc_attr($attributes['placeholder']); ?>"
                 data-button-text="<?php echo esc_attr($attributes['buttonText']); ?>"
                 data-title="<?php echo esc_attr($attributes['title']); ?>"
                 data-show-title="<?php echo $attributes['showTitle'] ? 'true' : 'false'; ?>">
                <!-- Content will be rendered by frontend JavaScript -->
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Register widgets
     */
    public function register_widgets() {
        register_widget('Quickscan_Security_Widget');
    }
    
    /**
     * Load text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'quickscan-connector',
            false,
            dirname(QUICKSCAN_PLUGIN_BASENAME) . '/languages/'
        );
    }
    
    /**
     * Log error messages
     */
    private function log_error($message) {
        if (get_option('quickscan_enable_logging')) {
            error_log('[Quickscan Connector] ' . $message);
        }
    }

    /**
     * Log general messages with optional data
     */
    private function log_message($message, $data = []) {
        if (get_option('quickscan_enable_logging')) {
            $log_entry = '[Quickscan Connector] ' . $message;
            if (!empty($data)) {
                $log_entry .= ' | Data: ' . json_encode($data);
            }
            error_log($log_entry);
        }
    }
}

/**
 * Security Scanner Widget
 */
class Quickscan_Security_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'quickscan_security_widget',
            'Security Scanner', // Use literal string to avoid early translation loading
            [
                'description' => 'Add a security scanner form to scan websites for vulnerabilities', // Use literal string
                'classname' => 'quickscan-security-widget'
            ]
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);
        
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $show_results = isset($instance['show_results']) ? (bool) $instance['show_results'] : false;
        $placeholder = !empty($instance['placeholder']) ? $instance['placeholder'] : 'Enter website URL...';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : 'Scan';

        echo '<div class="quickscan-widget"
                data-show-results="' . ($show_results ? 'true' : 'false') . '"
                data-placeholder="' . esc_attr($placeholder) . '"
                data-button-text="' . esc_attr($button_text) . '"
                data-show-title="false">
              </div>';
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : 'Security Scanner'; // Use literal string
        $show_results = isset($instance['show_results']) ? (bool) $instance['show_results'] : false;
        $placeholder = isset($instance['placeholder']) ? $instance['placeholder'] : 'Enter website URL...';
        $button_text = isset($instance['button_text']) ? $instance['button_text'] : 'Scan';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'quickscan-connector'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('placeholder')); ?>"><?php _e('Placeholder Text:', 'quickscan-connector'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('placeholder')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('placeholder')); ?>" 
                   type="text" value="<?php echo esc_attr($placeholder); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>"><?php _e('Button Text:', 'quickscan-connector'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" 
                   type="text" value="<?php echo esc_attr($button_text); ?>">
        </p>
        
        
        <p>
            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('show_results')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_results')); ?>" 
                   value="1" <?php checked($show_results, true); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_results')); ?>"><?php _e('Show results on page', 'quickscan-connector'); ?></label>
            <br><small><?php _e('If unchecked, results will be shown in a popup or redirect to results page', 'quickscan-connector'); ?></small>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_results'] = !empty($new_instance['show_results']);
        $instance['placeholder'] = !empty($new_instance['placeholder']) ? sanitize_text_field($new_instance['placeholder']) : 'Enter website URL...';
        $instance['button_text'] = !empty($new_instance['button_text']) ? sanitize_text_field($new_instance['button_text']) : 'Scan';
        
        return $instance;
    }
}

// Initialize the plugin
add_action('plugins_loaded', function() {
    QuickscanConnector::get_instance();
});