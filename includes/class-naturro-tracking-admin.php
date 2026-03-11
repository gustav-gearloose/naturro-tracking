<?php
/**
 * Admin Class for NaturRo Tracking System
 */

if (!defined('ABSPATH')) {
    exit;
}

class NaturRo_Tracking_Admin {

    private $option_name = 'naturro_tracking_settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    public function add_plugin_page() {
        add_options_page(
            'NaturRo Tracking', 
            'Tracking Options', 
            'manage_options', 
            'naturro-tracking', 
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $this->options = get_option($this->option_name);
        ?>
        <div class="wrap">
            <h1>NaturRo Tracking Settings</h1>
            <p>Manage active tracking providers and custom script injections here.</p>
            <form method="post" action="options.php">
            <?php
                settings_fields('naturro_tracking_option_group');
                do_settings_sections('naturro-tracking-admin');
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'naturro_tracking_option_group',
            $this->option_name,
            array($this, 'sanitize')
        );

        add_settings_section(
            'naturro_tracking_setting_section',
            'Active Providers',
            array($this, 'section_info'),
            'naturro-tracking-admin'
        );

        add_settings_field(
            'enable_rybbit',
            'Enable Rybbit',
            array($this, 'rybbit_callback'),
            'naturro-tracking-admin',
            'naturro_tracking_setting_section'
        );

        add_settings_section(
            'naturro_tracking_scripts_section',
            'Custom Scripts',
            array($this, 'scripts_section_info'),
            'naturro-tracking-admin'
        );

        add_settings_field(
            'custom_head_scripts',
            'Head Scripts (&lt;head&gt;)',
            array($this, 'head_scripts_callback'),
            'naturro-tracking-admin',
            'naturro_tracking_scripts_section'
        );

        add_settings_field(
            'custom_body_scripts',
            'Body Scripts (&lt;body&gt;)',
            array($this, 'body_scripts_callback'),
            'naturro-tracking-admin',
            'naturro_tracking_scripts_section'
        );
    }

    public function sanitize($input) {
        $sanitary_values = array();
        
        // Rybbit checkbox
        $sanitary_values['enable_rybbit'] = isset($input['enable_rybbit']) ? 1 : 0;

        // Script textareas - WARNING: bypassing sanitization intentionally per spec to allow script tags.
        // Requires manage_options capability which we enforce at menu registration.
        $sanitary_values['custom_head_scripts'] = isset($input['custom_head_scripts']) ? $input['custom_head_scripts'] : '';
        $sanitary_values['custom_body_scripts'] = isset($input['custom_body_scripts']) ? $input['custom_body_scripts'] : '';

        return $sanitary_values;
    }

    public function section_info() {
        echo 'Choose which built-in tracking strategies to enable on the frontend.';
    }

    public function scripts_section_info() {
        echo 'Paste raw scripts exactly as they should appear on the frontend (e.g. including &lt;script&gt; tags). <strong>Careful: These scripts run directly on the site.</strong>';
    }

    public function rybbit_callback() {
        $options = get_option($this->option_name);
        $val = isset($options['enable_rybbit']) ? $options['enable_rybbit'] : 1; // default to 1 (enabled)
        printf(
            '<input type="checkbox" name="%1$s[enable_rybbit]" value="1" %2$s />',
            $this->option_name,
            checked(1, $val, false)
        );
    }

    public function head_scripts_callback() {
        $options = get_option($this->option_name);
        $val = isset($options['custom_head_scripts']) ? $options['custom_head_scripts'] : '';
        printf(
            '<textarea class="large-text code" rows="6" name="%1$s[custom_head_scripts]">%2$s</textarea>',
            $this->option_name,
            esc_textarea($val)
        );
    }

    public function body_scripts_callback() {
        $options = get_option($this->option_name);
        $val = isset($options['custom_body_scripts']) ? $options['custom_body_scripts'] : '';
        printf(
            '<textarea class="large-text code" rows="6" name="%1$s[custom_body_scripts]">%2$s</textarea>',
            $this->option_name,
            esc_textarea($val)
        );
    }
}
