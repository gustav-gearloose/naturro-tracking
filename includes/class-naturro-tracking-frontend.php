<?php
/**
 * Frontend Class for NaturRo Tracking System
 */

if (!defined('ABSPATH')) {
    exit;
}

class NaturRo_Tracking_Frontend {

    private $options;

    public function __construct() {
        $this->options = get_option('naturro_tracking_settings', array());

        add_action('wp_enqueue_scripts', array($this, 'enqueue_tracking_scripts'));
        
        // Output specific provider scripts
        add_action('wp_footer', array($this, 'output_rybbit_analytics'), 99);
    }

    public function enqueue_tracking_scripts() {
        // Sequentially load the JS module files to build the architecture
        $js_dir = NATURRO_TRACKING_PLUGIN_URL . 'assets/js/';
        
        wp_enqueue_script('naturro-tracking-strategy', $js_dir . 'strategies/TrackingStrategy.js', array(), NATURRO_TRACKING_VERSION, true);
        wp_enqueue_script('naturro-tracking-rybbit', $js_dir . 'strategies/RybbitStrategy.js', array('naturro-tracking-strategy'), NATURRO_TRACKING_VERSION, true);
        wp_enqueue_script('naturro-tracking-manager', $js_dir . 'core/TrackingManager.js', array('naturro-tracking-strategy'), NATURRO_TRACKING_VERSION, true);
        wp_enqueue_script('naturro-tracking-observer', $js_dir . 'core/TrackingDOMObserver.js', array(), NATURRO_TRACKING_VERSION, true);
        
        // Main initialization file depends on all pieces being loaded
        wp_enqueue_script(
            'naturro-tracking-main',
            $js_dir . 'main.js',
            array('naturro-tracking-rybbit', 'naturro-tracking-manager', 'naturro-tracking-observer'),
            NATURRO_TRACKING_VERSION,
            true
        );

        $enable_rybbit = isset($this->options['enable_rybbit']) ? (bool) $this->options['enable_rybbit'] : true;

        wp_localize_script('naturro-tracking-main', 'naturroTrackingConfig', array(
            'services' => array(
                'rybbit' => $enable_rybbit
            )
        ));
    }

    public function output_rybbit_analytics() {
        $enable_rybbit = isset($this->options['enable_rybbit']) ? (bool) $this->options['enable_rybbit'] : true;
        $site_id = isset($this->options['rybbit_site_id']) ? $this->options['rybbit_site_id'] : '';
        $base_url = isset($this->options['rybbit_base_url']) && !empty($this->options['rybbit_base_url']) 
            ? $this->options['rybbit_base_url'] 
            : 'rybbit.gearloose.dk';
        
        // Strip trailing/leading slashes just in case humans add them
        $base_url = rtrim(trim($base_url), '/');

        // Allow fallback / override via theme filter as requested
        $site_id = apply_filters('naturro_rybbit_site_id', $site_id);

        if (!$enable_rybbit || empty($site_id)) {
            return;
        }

        // Construct complete URL safely
        // Assuming HTTPS protocol. If needed, this could also be a setting.
        $script_url = 'https://' . $base_url . '/api/script.js';

        printf(
            '<script src="%s" data-site-id="%s" defer></script>',
            esc_url($script_url),
            esc_attr($site_id)
        );
    }
}
