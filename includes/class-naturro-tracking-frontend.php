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
        
        // Output custom scripts
        add_action('wp_head', array($this, 'output_head_scripts'), 999);
        add_action('wp_body_open', array($this, 'output_body_scripts'), 1);
        
        // Fallback for body scripts if theme lacks wp_body_open
        add_action('wp_footer', array($this, 'output_body_scripts_fallback'), 999);
    }

    public function enqueue_tracking_scripts() {
        wp_enqueue_script(
            'naturro-tracking-core',
            NATURRO_TRACKING_PLUGIN_URL . 'assets/js/tracking-core.js',
            array(),
            NATURRO_TRACKING_VERSION,
            true
        );

        $enable_rybbit = isset($this->options['enable_rybbit']) ? (bool) $this->options['enable_rybbit'] : true; // Default true

        wp_localize_script('naturro-tracking-core', 'naturroTrackingConfig', array(
            'services' => array(
                'rybbit' => $enable_rybbit
            )
        ));
    }

    public function output_head_scripts() {
        if (!empty($this->options['custom_head_scripts'])) {
            echo "\n<!-- NaturRo Tracking Head Scripts -->\n";
            // Do NOT escape - output raw script intentionally
            echo $this->options['custom_head_scripts'];
            echo "\n<!-- End NaturRo Tracking Head Scripts -->\n";
        }
    }

    public function output_body_scripts() {
        if (!empty($this->options['custom_body_scripts'])) {
            echo "\n<!-- NaturRo Tracking Body Scripts -->\n";
            echo $this->options['custom_body_scripts'];
            echo "\n<!-- End NaturRo Tracking Body Scripts -->\n";
            // Mark as output to avoid printing again in fallback
            $this->options['_body_scripts_printed'] = true;
        }
    }

    public function output_body_scripts_fallback() {
        if (!empty($this->options['custom_body_scripts']) && empty($this->options['_body_scripts_printed'])) {
            echo "\n<!-- NaturRo Tracking Body Scripts (Footer Fallback) -->\n";
            echo $this->options['custom_body_scripts'];
            echo "\n<!-- End NaturRo Tracking Body Scripts -->\n";
        }
    }
}
