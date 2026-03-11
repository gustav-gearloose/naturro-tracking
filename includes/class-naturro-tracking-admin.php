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
            <p>Manage active tracking providers and their respective Site IDs.</p>
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
            'naturro_tracking_rybbit_section',
            'Rybbit Analytics',
            array($this, 'rybbit_section_info'),
            'naturro-tracking-admin'
        );

        add_settings_field(
            'enable_rybbit',
            'Enable Rybbit',
            array($this, 'rybbit_toggle_callback'),
            'naturro-tracking-admin',
            'naturro_tracking_rybbit_section'
        );

        add_settings_field(
            'rybbit_site_id',
            'Rybbit Site ID',
            array($this, 'rybbit_site_id_callback'),
            'naturro-tracking-admin',
            'naturro_tracking_rybbit_section'
        );
    }

    public function sanitize($input) {
        $sanitary_values = array();
        
        $sanitary_values['enable_rybbit'] = isset($input['enable_rybbit']) ? 1 : 0;
        $sanitary_values['rybbit_site_id'] = isset($input['rybbit_site_id']) ? sanitize_text_field($input['rybbit_site_id']) : '';

        return $sanitary_values;
    }

    public function rybbit_section_info() {
        echo 'Configure the centralized Rybbit Analytics tracking. If a Site ID is provided, the tracking script will automatically be injected safely into the site footer.';
    }

    public function rybbit_toggle_callback() {
        $options = get_option($this->option_name);
        $val = isset($options['enable_rybbit']) ? $options['enable_rybbit'] : 1;
        printf(
            '<input type="checkbox" name="%1$s[enable_rybbit]" value="1" %2$s />',
            $this->option_name,
            checked(1, $val, false)
        );
    }

    public function rybbit_site_id_callback() {
        $options = get_option($this->option_name);
        $val = isset($options['rybbit_site_id']) ? $options['rybbit_site_id'] : '';
        printf(
            '<input type="text" id="rybbit_site_id" name="%1$s[rybbit_site_id]" value="%2$s" class="regular-text" placeholder="e.g. 4" />',
            $this->option_name,
            esc_attr($val)
        );
    }
}
