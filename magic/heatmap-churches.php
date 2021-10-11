<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
    Zume_Public_Heatmap_Churches::instance();
}

add_filter('dt_network_dashboard_supported_public_links', function( $supported_links ){
    $supported_links[] = [
        'name' => 'Public Heatmap ( Churches )',
        'description' => 'Maps church saturation by admin2 (counties)',
        'key' => 'zume_app_heatmap_churches',
        'url' => 'zume_app/heatmap_churches'
    ];
    return $supported_links;
}, 10, 1 );


class Zume_Public_Heatmap_Churches extends Zume_Public_Heatmap_Base
{

    public $magic = false;
    public $parts = false;
    public $root = "zume_app";
    public $type = 'heatmap_churches';
    public $key = 'zume_app_heatmap_churches';
    public $post_type = 'groups';
    public $us_div = 2500; // this is 2 for every 5000
    public $global_div = 25000; // this equals 2 for every 50000

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        parent::__construct();

        // register type
        $this->magic = new DT_Magic_URL( $this->root );
        add_filter( 'dt_magic_url_register_types', [ $this, '_register_type' ], 10, 1 );

        // register REST and REST access
        add_filter( 'dt_allow_rest_access', [ $this, '_authorize_url' ], 100, 1 );
        add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );
        add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ], 100 );


        // fail if not valid url
        $url = dt_get_url_path();
        if ( strpos( $url, $this->root . '/' . $this->type ) === false ) {
            return;
        }

        // fail to blank if not valid url
        $this->parts = $this->magic->parse_url_parts();
        if ( ! $this->parts ){
            // @note this returns a blank page for bad url, instead of redirecting to login
            add_filter( 'dt_templates_for_urls', function ( $template_for_url ) {
                $url = dt_get_url_path();
                $template_for_url[ $url ] = 'template-blank.php';
                return $template_for_url;
            }, 199, 1 );
            add_filter( 'dt_blank_access', function(){ return true;
            } );
            add_filter( 'dt_allow_non_login_access', function(){ return true;
            }, 100, 1 );
            return;
        }

        // fail if does not match type
        if ( $this->type !== $this->parts['type'] ){
            return;
        }

        // load if valid url
        add_filter( "dt_blank_title", [ $this, "_browser_tab_title" ] );
        add_action( 'dt_blank_head', [ $this, '_header' ] );
        add_action( 'dt_blank_footer', [ $this, '_footer' ] );
        add_action( 'dt_blank_body', [ $this, 'body' ] ); // body for no post key

        // load page elements
        add_action( 'wp_print_scripts', [ $this, '_print_scripts' ], 1500 );
        add_action( 'wp_print_styles', [ $this, '_print_styles' ], 1500 );

        // register url and access
        add_filter( 'dt_templates_for_urls', [ $this, '_register_url' ], 199, 1 );
        add_filter( 'dt_blank_access', [ $this, '_has_access' ] );
        add_filter( 'dt_allow_non_login_access', function(){ return true;
        }, 100, 1 );

    }


    /****************************************************************************************
     *  CLASS EXTENSION CUSTOMIZATION FUNCTIONS
     ****************************************************************************************/

    public function get_grid_totals(){
        return Zume_Public_Heatmap_Queries::query_church_grid_totals();
    }

    public function get_grid_totals_by_level( $administrative_level ) {
        return Zume_Public_Heatmap_Queries::query_church_grid_totals( $administrative_level );
    }

    /**
     * Can be customized with class extension
     * @param $country_code
     * @return float|int
     */
    public function get_population_division( $country_code ){
        $population_division = $this->global_div * 2;
        if ( $country_code === 'US' ){
            $population_division = $this->us_div * 2;
        }
        return $population_division;
    }

    public function _browser_tab_title( $title ){
        return __( "Zúme Churches Map", 'disciple_tools' );
    }

    /**
     * Can be customized with class extension
     */
    public function customized_welcome_script(){
        ?>
        <script>
            jQuery(document).ready(function($){
                let asset_url = '<?php echo esc_url( trailingslashit( plugin_dir_url( __DIR__ ) ) . 'images/' ) ?>'
                $('.training-content').append(`
                <div class="grid-x grid-padding-x" >
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'search.svg'}" alt="search icon" />
                        <h2>Search</h2>
                        <p>Search for any city or place with the search input.</p>
                    </div>
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'zoom.svg'}" alt="zoom icon"  />
                        <h2>Zoom</h2>
                        <p>Scroll zoom with your mouse or pinch zoom with track pads and phones to focus on sections of the map.</p>
                    </div>
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'drag.svg'}" alt="drag icon"  />
                        <h2>Drag</h2>
                        <p>Click and drag the map any direction to look at a different part of the map.</p>
                    </div>
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'click.svg'}" alt="click icon" />
                        <h2>Click</h2>
                        <p>Click a single section and reveal a details panel with more information about the location.</p>
                    </div>
                </div>
                `)

            })
        </script>
        <?php
    }

    public function endpoint_new_report( $form_data )
    {
        global $wpdb;
        if (!isset( $form_data['name'], $form_data['email'], $form_data['phone'] )) {
            return new WP_Error(__METHOD__, 'Missing params.', ['status' => 400]);
        }


        $contact_id = false;

        // find the reporter
        // try to get contact_id and contact
        if (isset($form_data['contact_id']) && !empty($form_data['contact_id'])) {
            $contact_id = (int)$form_data['contact_id'];
            $contact = DT_Posts::get_post('contacts', $contact_id, false, false);
            if (is_wp_error($contact)) {
                return $contact;
            }
        } else if (isset($form_data['return_reporter']) && $form_data['return_reporter']) {
            $email = sanitize_email(wp_unslash($form_data['email']));
            // phpcs:disable
            $contact_ids = $wpdb->get_results($wpdb->prepare("
                SELECT DISTINCT pm.post_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->postmeta as pm1 ON pm.post_id=pm1.post_id AND pm1.meta_key LIKE 'contact_email%' AND pm1.meta_key NOT LIKE '%details'
                WHERE pm.meta_key = 'overall_status' AND pm.meta_value = 'active' AND pm1.meta_value = %s
            ", $email), ARRAY_A);
            // phpcs:enable
            if (!empty($contact_ids)) {
                $contact_id = $contact_ids[0]['post_id'];
                $contact = DT_Posts::get_post('contacts', $contact_id, false, false);
                if (is_wp_error($contact)) {
                    return $contact;
                }
            }
        }

        // create contact and send link.

        return true;
    }
}
