<?php
/**
 * Plugin Name: Zume - Public Heatmaps
 * Plugin URI: https://github.com/ZumeProject/zume-public-heatmaps
 * Description: This plugin creates the public facing heatmaps that show trainings and churches and are embedded into public websites.
 * Text Domain: zume-public-heatmaps
 * Domain Path: /languages
 * Version:  0.1
 * Author URI: https://github.com/ZumeProject
 * GitHub Plugin URI: https://github.com/ZumeProject/zume-public-heatmaps
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.6
 *
 * @package Disciple_Tools
 * @link    https://github.com/DiscipleTools
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! defined( 'GLOBAL_POPULATION_BLOCKS' ) ) {
    define( 'GLOBAL_POPULATION_BLOCKS', 50000 );
}

if ( ! defined( 'US_POPULATION_BLOCKS' ) ) {
    define( 'US_POPULATION_BLOCKS', 5000 );
}

/**
 * Gets the instance of the `Zume_Public_Heatmaps` class.
 *
 * @since  0.1
 * @access public
 * @return object|bool
 */
function zume_public_heatmaps() {
    $zume_public_heatmaps_required_dt_theme_version = '1.0';
    $wp_theme = wp_get_theme();
    $version = $wp_theme->version;

    /*
     * Check if the Disciple.Tools theme is loaded and is the latest required version
     */
    $is_theme_dt = strpos( $wp_theme->get_template(), "disciple-tools-theme" ) !== false || $wp_theme->name === "Disciple Tools";
    if ( $is_theme_dt && version_compare( $version, $zume_public_heatmaps_required_dt_theme_version, "<" ) ) {
        add_action( 'admin_notices', 'zume_public_heatmaps_hook_admin_notice' );
        add_action( 'wp_ajax_dismissed_notice_handler', 'dt_hook_ajax_notice_handler' );
        return false;
    }
    if ( !$is_theme_dt ){
        return false;
    }
    /**
     * Load useful function from the theme
     */
    if ( !defined( 'DT_FUNCTIONS_READY' ) ){
        require_once get_template_directory() . '/dt-core/global-functions.php';
    }

    return Zume_Public_Heatmaps::instance();

}
add_action( 'dt_network_dashboard_loaded', 'zume_public_heatmaps', 20 ); // hooks the network dashboard to load first

/**
 * Singleton class for setting up the plugin.
 *
 * @since  0.1
 * @access public
 */
class Zume_Public_Heatmaps {

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {

        require_once( 'magic/heatmap.php' );
        // polygon heat
        require_once( 'magic/map-2-network-activities.php' );
        require_once( 'magic/map-3-practitioners.php' );
        require_once( 'magic/map-5-churches.php' );
        // cluster heat
        require_once( 'magic/cluster-1-last100.php' );
        require_once( 'magic/cluster-2-all-time-activity.php' );
        require_once( 'magic/cluster-3-trainings.php' );
        require_once( 'magic/cluster-4-streams.php' );

        require_once( 'magic/reporter.php' );
        require_once( 'magic/portal.php' );

        require_once( 'magic/custom-tile.php' );

        require_once( 'charts/charts-loader.php' );

        if ( is_admin() ) {
            require_once( 'admin/admin-menu-and-tabs.php' ); // adds starter admin page and section for plugin
        }

        $this->i18n();

        if ( is_admin() ) { // adds links to the plugin description area in the plugin admin list.
            add_filter( 'plugin_row_meta', [ $this, 'plugin_description_links' ], 10, 4 );
        }
    }

    /**
     * Filters the array of row meta for each/specific plugin in the Plugins list table.
     * Appends additional links below each/specific plugin on the plugins page.
     */
    public function plugin_description_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
        if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
            // You can still use `array_unshift()` to add links at the beginning.

            $links_array[] = '<a href="https://disciple.tools">Disciple.Tools Community</a>';
            $links_array[] = '<a href="https://github.com/ZumeProject/zume-public-heatmaps">Github Project</a>';
        }

        return $links_array;
    }

    /**
     * Method that runs only when the plugin is activated.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public static function activation() {
        // add elements here that need to fire on activation
    }

    /**
     * Method that runs only when the plugin is deactivated.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public static function deactivation() {
        // add functions here that need to happen on deactivation
        delete_option( 'dismissed-zume-public-heatmaps' );
    }

    /**
     * Loads the translation files.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function i18n() {
        $domain = 'zume-public-heatmaps';
        load_plugin_textdomain( $domain, false, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ). 'languages' );
    }

    /**
     * Magic method to output a string if trying to use the object as a string.
     *
     * @since  0.1
     * @access public
     * @return string
     */
    public function __toString() {
        return 'zume-public-heatmaps';
    }

    /**
     * Magic method to keep the object from being cloned.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, 'Whoah, partner!', '0.1' );
    }

    /**
     * Magic method to keep the object from being unserialized.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, 'Whoah, partner!', '0.1' );
    }

    /**
     * Magic method to prevent a fatal error when calling a method that doesn't exist.
     *
     * @param string $method
     * @param array $args
     * @return null
     * @since  0.1
     * @access public
     */
    public function __call( $method = '', $args = array() ) {
        _doing_it_wrong( "zume_public_heatmaps::" . esc_html( $method ), 'Method does not exist.', '0.1' );
        unset( $method, $args );
        return null;
    }
}


// Register activation hook.
register_activation_hook( __FILE__, [ 'Zume_Public_Heatmaps', 'activation' ] );
register_deactivation_hook( __FILE__, [ 'Zume_Public_Heatmaps', 'deactivation' ] );


if ( ! function_exists( 'zume_public_heatmaps_hook_admin_notice' ) ) {
    function zume_public_heatmaps_hook_admin_notice() {
        global $zume_public_heatmaps_required_dt_theme_version;
        $wp_theme = wp_get_theme();
        $current_version = $wp_theme->version;
        $message = "'Zume - Public Heatmaps' plugin requires 'Disciple Tools' theme to work. Please activate 'Disciple Tools' theme or make sure it is latest version.";
        if ( $wp_theme->get_template() === "disciple-tools-theme" ){
            $message .= ' ' . sprintf( esc_html( 'Current Disciple Tools version: %1$s, required version: %2$s' ), esc_html( $current_version ), esc_html( $zume_public_heatmaps_required_dt_theme_version ) );
        }
        // Check if it's been dismissed...
        if ( ! get_option( 'dismissed-zume-public-heatmaps', false ) ) { ?>
            <div class="notice notice-error notice-zume-public-heatmaps is-dismissible" data-notice="zume-public-heatmaps">
                <p><?php echo esc_html( $message );?></p>
            </div>
            <script>
                jQuery(function($) {
                    $( document ).on( 'click', '.notice-zume-public-heatmaps .notice-dismiss', function () {
                        $.ajax( ajaxurl, {
                            type: 'POST',
                            data: {
                                action: 'dismissed_notice_handler',
                                type: 'zume-public-heatmaps',
                                security: '<?php echo esc_html( wp_create_nonce( 'wp_rest_dismiss' ) ) ?>'
                            }
                        })
                    });
                });
            </script>
        <?php }
    }
}

/**
 * AJAX handler to store the state of dismissible notices.
 */
if ( ! function_exists( "dt_hook_ajax_notice_handler" )){
    function dt_hook_ajax_notice_handler(){
        check_ajax_referer( 'wp_rest_dismiss', 'security' );
        if ( isset( $_POST["type"] ) ){
            $type = sanitize_text_field( wp_unslash( $_POST["type"] ) );
            update_option( 'dismissed-' . $type, true );
        }
    }
}

add_action( 'plugins_loaded', function (){
    if ( is_admin() && !( is_multisite() && class_exists( "DT_Multisite" ) ) || wp_doing_cron() ){
        // Check for plugin updates
        if ( ! class_exists( 'Puc_v4_Factory' ) ) {
            if ( file_exists( get_template_directory() . '/dt-core/libraries/plugin-update-checker/plugin-update-checker.php' )){
                require( get_template_directory() . '/dt-core/libraries/plugin-update-checker/plugin-update-checker.php' );
            }
        }
        if ( class_exists( 'Puc_v4_Factory' ) ){
            Puc_v4_Factory::buildUpdateChecker(
                'https://raw.githubusercontent.com/DiscipleTools/zume-public-heatmaps/master/version-control.json',
                __FILE__,
                'zume-public-heatmaps'
            );
        }
    }
} );


if ( ! function_exists( 'persecuted_countries' ) ){
    function persecuted_countries() : array {
        return [
            'North Korea',
            'Afghanistan',
            'Somolia',
            'Libya',
            'Pakistan',
            'Eritrea',
            'Sudan',
            'Yemen',
            'Iran',
            'India',
            'Syria',
            'Nigeria',
            'Saudi Arabia',
            'Maldives',
            'Iraq',
            'Egypt',
            'Algeria',
            'Uzbekistan',
            'Myanmar',
            'Laos',
            'Vietnam',
            'Turkmenistan',
            'China',
            'Mauritania',
            'Central African Republic',
            'Morocco',
            'Qatar',
            'Burkina Faso',
            'Mali',
            'Sri Lanka',
            'Tajikistan',
            'Nepal',
            'Jordan',
            'Tunisia',
            'Kazakhstan',
            'Turkey',
            'Brunei',
            'Bangladesh',
            'Ethiopia',
            'Malaysia',
            'Colombia',
            'Oman',
            'Kuwait',
            'Kenya',
            'Bhutan',
            'Russian Federation',
            'United Arab Emirates',
            'Cameroon',
            'Indonesia',
            'Niger'
        ];
    }
}

function movement_maps_stats_plugin_must_be_multisite() {
    $message = __( "'Movement Maps & Stats' plugin must be run on a multisite server with a network dashboard enabled disciple tools system. Please disable plugin.", "dt_dashboard_plugin" );
    ?>
    <div class="notice notice-error notice-dt-dashboard is-dismissible" data-notice="dt-dashboard">
        <p><?php echo esc_html( $message );?></p>
    </div>
    <?php
}
function movement_maps_stats_plugin_must_not_be_disciple_tools() {
    $message = __( "'Movement Maps & Stats' plugin is to be installed on a non-disciple tools website on a multisite server with a network dashboard enabled disciple tools system.", "dt_dashboard_plugin" );
    ?>
    <div class="notice notice-error notice-dt-dashboard is-dismissible" data-notice="dt-dashboard">
        <p><?php echo esc_html( $message );?></p>
    </div>
    <?php
}

