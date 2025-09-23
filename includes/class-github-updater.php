<?php
/**
 * GitHub Auto-Updater for Quickscan Connector
 *
 * @package QuickscanConnector
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Quickscan_GitHub_Updater {

    private $file;
    private $plugin;
    private $basename;
    private $active;
    private $username;
    private $repository;
    private $authorize_token;
    private $github_response;

    public function __construct($file) {
        $this->file = $file;
        $this->plugin = get_plugin_data($this->file);
        $this->basename = plugin_basename($this->file);
        $this->active = is_plugin_active($this->basename);
        $this->username = 'guardian360';
        $this->repository = 'quickscan-wp-plugin';
        $this->authorize_token = '';

        add_filter('pre_set_site_transient_update_plugins', array($this, 'modify_transient'), 10, 1);
        add_filter('plugins_api', array($this, 'plugin_popup'), 10, 3);
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);

        // Add custom update message
        add_action('in_plugin_update_message-' . $this->basename, array($this, 'in_plugin_update_message'), 10, 2);
    }

    private function request() {
        $remote_get = wp_remote_get(sprintf(
            'https://api.github.com/repos/%s/%s/releases/latest',
            $this->username,
            $this->repository
        ), array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url')
            )
        ));

        if (!is_wp_error($remote_get) && wp_remote_retrieve_response_code($remote_get) === 200) {
            return json_decode(wp_remote_retrieve_body($remote_get), true);
        }

        return false;
    }

    public function modify_transient($transient) {
        if ($checked = $transient->checked) {
            $this->get_repository_info();

            $out_of_date = version_compare($this->github_response['tag_name'], $checked[$this->basename], 'gt');

            if ($out_of_date) {
                $new_files = $this->get_zip_url();

                $transient->response[$this->basename] = (object) array(
                    'slug' => current(explode('/', $this->basename)),
                    'plugin' => $this->basename,
                    'new_version' => $this->github_response['tag_name'],
                    'url' => $this->plugin['PluginURI'],
                    'package' => $new_files,
                    'tested' => '6.4',
                    'requires_php' => '7.4',
                    'compatibility' => new stdClass(),
                );
            }
        }

        return $transient;
    }

    public function plugin_popup($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return false;
        }

        if (!empty($args->slug)) {
            if ($args->slug == current(explode('/', $this->basename))) {
                $this->get_repository_info();

                $plugin = array(
                    'name' => $this->plugin['Name'],
                    'slug' => $this->basename,
                    'requires' => '5.0',
                    'tested' => '6.4',
                    'requires_php' => '7.4',
                    'rating' => 0,
                    'ratings' => array(5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0),
                    'num_ratings' => 0,
                    'support_threads' => 0,
                    'support_threads_resolved' => 0,
                    'downloaded' => 0,
                    'last_updated' => $this->github_response['published_at'],
                    'added' => $this->github_response['published_at'],
                    'homepage' => $this->plugin['PluginURI'],
                    'sections' => array(
                        'description' => $this->plugin['Description'],
                        'installation' => 'Upload the Quickscan Connector plugin to your site, activate it, and configure your Quickscan credentials.',
                        'changelog' => class_exists('Parsedown') ? Parsedown::instance()->parse($this->github_response['body']) : $this->github_response['body'],
                    ),
                    'download_link' => $this->get_zip_url(),
                    'version' => $this->github_response['tag_name'],
                    'author' => $this->plugin['AuthorName'],
                );

                return (object) $plugin;
            }
        }

        return $result;
    }

    public function after_install($response, $hook_extra, $result) {
        global $wp_filesystem;

        $install_directory = plugin_dir_path($this->file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;

        if ($this->active) {
            activate_plugin($this->basename);
        }

        return $result;
    }

    private function get_repository_info() {
        if (is_null($this->github_response)) {
            $this->github_response = $this->request();
        }
    }

    private function get_zip_url() {
        if (!empty($this->github_response['zipball_url'])) {
            return $this->github_response['zipball_url'];
        }

        return sprintf(
            'https://github.com/%s/%s/archive/refs/tags/%s.zip',
            $this->username,
            $this->repository,
            $this->github_response['tag_name']
        );
    }

    public function in_plugin_update_message($data, $response) {
        if (isset($data['upgrade_notice'])) {
            printf(
                '<div class="update-message">%s</div>',
                wp_kses_post($data['upgrade_notice'])
            );
        }
    }
}