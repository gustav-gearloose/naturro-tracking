<?php
/**
 * Plugin Name: NaturRo Tracking System
 * Description: A flexible, SOLID-based tracking system using the Strategy Pattern with an Admin UI to manage tracking providers and custom scripts.
 * Version: 1.1.0
 * Author: NaturRo
 * Text Domain: naturro
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('NATURRO_TRACKING_VERSION', '1.1.0');
define('NATURRO_TRACKING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NATURRO_TRACKING_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Bootstrap Class
 */
final class NaturRo_Tracking {

    private static $instance = null;

    /**
     * Singleton instance
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->includes();
        $this->init();
    }

    private function includes() {
        require_once NATURRO_TRACKING_PLUGIN_DIR . 'includes/class-naturro-tracking-admin.php';
        require_once NATURRO_TRACKING_PLUGIN_DIR . 'includes/class-naturro-tracking-frontend.php';
    }

    private function init() {
        // Initialize Admin UI if in dashboard
        if (is_admin()) {
            new NaturRo_Tracking_Admin();
        }

        // Initialize Frontend Hooks
        new NaturRo_Tracking_Frontend();
    }
}

// Boot up the plugin
function naturro_tracking_init() {
    NaturRo_Tracking::instance();
}
add_action('plugins_loaded', 'naturro_tracking_init');
