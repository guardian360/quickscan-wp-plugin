<?php
/**
 * Quickscan Results Formatter
 *
 * Formats scan results to match quickscan.guardian360.nl display
 *
 * @package QuickscanConnector
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Quickscan_Results_Formatter {

    /**
     * Format scan results for display
     */
    public static function format_results($data, $is_frontend = false) {
        if (!$data || !isset($data['data'])) {
            return '<p>' . __('No scan data available', 'quickscan-connector') . '</p>';
        }

        $scan_data = $data['data'];
        $html = '<div class="quickscan-results-full">';

        // Info section
        if (isset($scan_data['Info'])) {
            $html .= self::format_info_section($scan_data['Info']);
        }

        // SSL Certificates section
        if (isset($scan_data['SSL'])) {
            $html .= self::format_ssl_section($scan_data['SSL']);
        }

        // Content Security Policy section
        if (isset($scan_data['Content-Security-Policy']) || isset($scan_data['csp'])) {
            $csp_data = isset($scan_data['Content-Security-Policy']) ? $scan_data['Content-Security-Policy'] : $scan_data['csp'];
            $html .= self::format_csp_section($csp_data);
        }

        // DNS section
        if (isset($scan_data['DNS'])) {
            $html .= self::format_dns_section($scan_data['DNS']);
        }

        // Security Headers section
        if (isset($scan_data['Security-Headers']) || isset($scan_data['security_headers'])) {
            $headers_data = isset($scan_data['Security-Headers']) ? $scan_data['Security-Headers'] : $scan_data['security_headers'];
            $html .= self::format_security_headers_section($headers_data);
        }

        // Misconfigurations section
        if (isset($scan_data['Misconfigurations'])) {
            $html .= self::format_misconfigurations_section($scan_data['Misconfigurations']);
        }

        // Cookies section
        if (isset($scan_data['Cookies'])) {
            $html .= self::format_cookies_section($scan_data['Cookies']);
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Format info section
     */
    private static function format_info_section($info) {
        $html = '<div class="quickscan-section quickscan-info">';
        $html .= '<h3>' . __('Info', 'quickscan-connector') . '</h3>';
        $html .= '<table class="quickscan-table">';

        if (isset($info['URL'])) {
            $html .= '<tr><td class="label">URL</td><td>' . esc_html($info['URL']) . '</td></tr>';
        }

        if (isset($info['Score'])) {
            $score = intval($info['Score']);
            $class = $score >= 80 ? 'good' : ($score >= 60 ? 'warning' : 'danger');
            $html .= '<tr><td class="label">' . __('Score', 'quickscan-connector') . '</td><td><span class="score ' . $class . '">' . $score . '%</span></td></tr>';
        }

        if (isset($info['IP']) && is_array($info['IP'])) {
            $html .= '<tr><td class="label">' . __('IP addresses', 'quickscan-connector') . '</td><td>' . implode('<br>', array_map('esc_html', $info['IP'])) . '</td></tr>';
        }

        if (isset($info['Raw-Headers']) && is_array($info['Raw-Headers'])) {
            $header_count = count($info['Raw-Headers']);
            $html .= '<tr><td class="label">' . __('Raw headers', 'quickscan-connector') . '</td><td>' . sprintf(__('%d headers found', 'quickscan-connector'), $header_count) . '</td></tr>';

            foreach ($info['Raw-Headers'] as $header => $value) {
                $html .= '<tr><td class="sub-label">' . esc_html($header) . '</td><td class="header-value">' . esc_html($value) . '</td></tr>';
            }
        }

        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Format SSL section
     */
    private static function format_ssl_section($ssl) {
        $html = '<div class="quickscan-section quickscan-ssl">';
        $html .= '<h3>' . __('SSL Certificates', 'quickscan-connector') . '</h3>';
        $html .= '<table class="quickscan-table">';

        foreach ($ssl as $cipher => $data) {
            if ($cipher === 'Algemeen') {
                $html .= '<tr class="section-header"><td colspan="3">' . __('General', 'quickscan-connector') . '</td></tr>';
                if (isset($data['Items']) && is_array($data['Items'])) {
                    foreach ($data['Items'] as $item) {
                        $status = $item['Vulnerable'] ? 'vulnerable' : 'secure';
                        $html .= '<tr class="' . $status . '">';
                        $html .= '<td class="sub-label">' . __('Issue', 'quickscan-connector') . '</td>';
                        $html .= '<td>' . esc_html($item['Issue']) . '</td>';
                        $html .= '<td class="status-' . $status . '">' . ($item['Vulnerable'] ? __('Vulnerable', 'quickscan-connector') : __('Secure', 'quickscan-connector')) . '</td>';
                        $html .= '</tr>';
                        if ($item['Vulnerable'] && !empty($item['Risk'])) {
                            $html .= '<tr class="risk-row"><td class="sub-label">' . __('Risk', 'quickscan-connector') . '</td><td colspan="2" class="risk-' . strtolower($item['Risk']) . '">' . esc_html($item['Risk']) . '</td></tr>';
                        }
                    }
                }
            } else {
                // Individual cipher suites
                $status = isset($data['Vulnerable']) && $data['Vulnerable'] ? 'vulnerable' : 'secure';
                $html .= '<tr class="cipher-row ' . $status . '">';
                $html .= '<td class="cipher-name">' . esc_html($cipher) . '</td>';
                $html .= '<td colspan="2" class="status-' . $status . '">' . ($data['Vulnerable'] ?? false ? __('Vulnerable', 'quickscan-connector') : __('Secure', 'quickscan-connector')) . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Format CSP section
     */
    private static function format_csp_section($csp) {
        $html = '<div class="quickscan-section quickscan-csp">';
        $html .= '<h3>' . __('Content Security Policy', 'quickscan-connector') . '</h3>';
        $html .= '<table class="quickscan-table">';

        if (isset($csp['Policies']) && is_array($csp['Policies'])) {
            foreach ($csp['Policies'] as $directive => $policy) {
                $html .= '<tr class="section-header"><td colspan="3">' . esc_html($directive) . '</td></tr>';

                if (isset($policy['Vulnerabilities']) && is_array($policy['Vulnerabilities'])) {
                    foreach ($policy['Vulnerabilities'] as $url => $vuln) {
                        if (isset($vuln['Vulnerable']) && $vuln['Vulnerable']) {
                            $html .= '<tr class="vulnerable">';
                            $html .= '<td class="sub-label">' . esc_html($url) . '</td>';
                            $html .= '<td>' . esc_html($vuln['Issue'] ?? '') . '</td>';
                            $html .= '<td class="risk-' . strtolower($vuln['Risk'] ?? 'low') . '">' . esc_html($vuln['Risk'] ?? '') . '</td>';
                            $html .= '</tr>';
                        } else {
                            $html .= '<tr class="secure">';
                            $html .= '<td class="sub-label">' . esc_html($url) . '</td>';
                            $html .= '<td colspan="2" class="status-secure">' . __('Secure', 'quickscan-connector') . '</td>';
                            $html .= '</tr>';
                        }
                    }
                }
            }
        }

        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Format DNS section
     */
    private static function format_dns_section($dns) {
        $html = '<div class="quickscan-section quickscan-dns">';
        $html .= '<h3>' . __('DNS', 'quickscan-connector') . '</h3>';
        $html .= '<table class="quickscan-table">';

        foreach ($dns as $record => $data) {
            $status = isset($data['Vulnerable']) && $data['Vulnerable'] ? 'vulnerable' : 'secure';
            $html .= '<tr class="' . $status . '">';
            $html .= '<td class="label">' . esc_html($record) . '</td>';
            $html .= '<td>' . esc_html($data['Value'] ?? '') . '</td>';
            $html .= '<td class="status-' . $status . '">' . ($data['Vulnerable'] ?? false ? __('Vulnerable', 'quickscan-connector') : __('Secure', 'quickscan-connector')) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Format Security Headers section
     */
    private static function format_security_headers_section($headers) {
        $html = '<div class="quickscan-section quickscan-headers">';
        $html .= '<h3>' . __('Security Headers', 'quickscan-connector') . '</h3>';
        $html .= '<table class="quickscan-table">';

        foreach ($headers as $header => $data) {
            $vulnerable = isset($data['Vulnerable']) && $data['Vulnerable'];
            $status = $vulnerable ? 'vulnerable' : 'secure';

            $html .= '<tr class="' . $status . '">';
            $html .= '<td class="label">' . esc_html($header) . '</td>';

            if ($vulnerable) {
                $html .= '<td class="status-vulnerable">' . __('Not configured', 'quickscan-connector') . '</td>';
                $html .= '<td class="issue">' . esc_html($data['Issue'] ?? '') . '</td>';
                $html .= '<td class="risk-' . strtolower($data['Risk'] ?? 'low') . '">' . esc_html($data['Risk'] ?? '') . '</td>';
            } else {
                $html .= '<td class="status-secure">' . __('Configured', 'quickscan-connector') . '</td>';
                $html .= '<td colspan="2">' . esc_html($data['Value'] ?? '✓') . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Format Misconfigurations section
     */
    private static function format_misconfigurations_section($misconfigs) {
        $html = '<div class="quickscan-section quickscan-misconfigurations">';
        $html .= '<h3>' . __('Misconfigurations', 'quickscan-connector') . '</h3>';
        $html .= '<table class="quickscan-table">';

        foreach ($misconfigs as $config => $data) {
            $vulnerable = isset($data['Vulnerable']) && $data['Vulnerable'];
            $status = $vulnerable ? 'vulnerable' : 'secure';

            $html .= '<tr class="' . $status . '">';
            $html .= '<td class="label">' . esc_html($config) . '</td>';

            if ($vulnerable) {
                $html .= '<td class="issue">' . esc_html($data['Issue'] ?? '') . '</td>';
                $html .= '<td class="risk-' . strtolower($data['Risk'] ?? 'low') . '">' . esc_html($data['Risk'] ?? '') . '</td>';
            } else {
                $html .= '<td colspan="2" class="status-secure">' . __('Secure', 'quickscan-connector') . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Format Cookies section
     */
    private static function format_cookies_section($cookies) {
        $html = '<div class="quickscan-section quickscan-cookies">';
        $html .= '<h3>' . __('Cookies', 'quickscan-connector') . '</h3>';
        $html .= '<table class="quickscan-table">';

        foreach ($cookies as $cookie_name => $cookie_data) {
            $html .= '<tr class="section-header"><td colspan="3">' . esc_html($cookie_name) . '</td></tr>';

            foreach ($cookie_data as $flag => $flag_data) {
                $vulnerable = isset($flag_data['Vulnerable']) && $flag_data['Vulnerable'];
                $status = $vulnerable ? 'vulnerable' : 'secure';

                $html .= '<tr class="' . $status . '">';
                $html .= '<td class="sub-label">' . esc_html($cookie_name . '.' . $flag) . '</td>';

                if ($vulnerable) {
                    $html .= '<td class="status-vulnerable">' . __('Not configured', 'quickscan-connector') . '</td>';
                    $html .= '<td class="issue">' . esc_html($flag_data['Issue'] ?? '') . '</td>';
                    $html .= '<td class="risk-' . strtolower($flag_data['Risk'] ?? 'medium') . '">' . esc_html($flag_data['Risk'] ?? '') . '</td>';
                } else {
                    $html .= '<td colspan="3" class="status-secure">' . __('Configured', 'quickscan-connector') . '</td>';
                }

                $html .= '</tr>';
            }
        }

        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Get CSS styles for the results display
     */
    public static function get_styles() {
        return '
        <style>
        .quickscan-results-full {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            line-height: 1.5;
            color: #23282d;
        }

        .quickscan-section {
            margin-bottom: 30px;
            background: #fff;
            border: 1px solid #e2e4e7;
            border-radius: 4px;
            overflow: hidden;
        }

        .quickscan-section h3 {
            margin: 0;
            padding: 12px 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #e2e4e7;
            font-size: 16px;
            font-weight: 600;
            color: #23282d;
        }

        .quickscan-table {
            width: 100%;
            border-collapse: collapse;
        }

        .quickscan-table tr {
            border-bottom: 1px solid #e2e4e7;
        }

        .quickscan-table tr:last-child {
            border-bottom: none;
        }

        .quickscan-table td {
            padding: 10px 15px;
            vertical-align: top;
        }

        .quickscan-table .label {
            font-weight: 600;
            width: 200px;
            color: #50575e;
        }

        .quickscan-table .sub-label {
            padding-left: 30px;
            color: #50575e;
        }

        .quickscan-table .section-header td {
            background: #f8f9fa;
            font-weight: 600;
            color: #23282d;
        }

        .quickscan-table .vulnerable {
            background: #fff5f5;
        }

        .quickscan-table .secure {
            background: #f5fff5;
        }

        .status-vulnerable {
            color: #dc3232;
            font-weight: 600;
        }

        .status-secure {
            color: #46b450;
            font-weight: 600;
        }

        .risk-high {
            color: #dc3232;
            font-weight: 600;
        }

        .risk-medium {
            color: #ffb900;
            font-weight: 600;
        }

        .risk-low {
            color: #00a0d2;
            font-weight: 600;
        }

        .score {
            font-size: 24px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .score.good {
            color: #46b450;
            background: #f5fff5;
        }

        .score.warning {
            color: #ffb900;
            background: #fffbf5;
        }

        .score.danger {
            color: #dc3232;
            background: #fff5f5;
        }

        .cipher-name {
            font-family: monospace;
            font-size: 13px;
        }

        .header-value {
            word-break: break-all;
            font-size: 13px;
            color: #666;
        }

        .risk-row td {
            padding-top: 0;
            font-size: 13px;
        }

        .cipher-row td {
            font-size: 14px;
        }

        .issue {
            font-size: 13px;
            color: #666;
        }
        </style>
        ';
    }
}