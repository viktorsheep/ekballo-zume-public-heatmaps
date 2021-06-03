<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
    DT_Network_Dashboard_Public_Heatmap_Trainings::instance();
}


add_filter('dt_network_dashboard_supported_public_links', function( $supported_links ){
    $supported_links[] = [
        'name' => 'Public Heatmap ( Trainings )',
        'description' => 'Trainings map for world saturation',
        'key' => 'zume_app_heatmap_trainings',
        'url' => 'zume_app/heatmap_trainings'
    ];
    return $supported_links;
}, 10, 1 );


class DT_Network_Dashboard_Public_Heatmap_Trainings extends DT_Network_Dashboard_Public_Heatmap_Churches
{

    public $magic = false;
    public $parts = false;
    public $root = "zume_app";
    public $type = 'heatmap_trainings';
    public $post_type = 'trainings';

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

    public function _register_type( array $types ) : array {
        if ( ! isset( $types[$this->root] ) ) {
            $types[$this->root] = [];
        }
        $types[$this->root][$this->type] = [
            'name' => 'Trainings Saturation',
            'root' => $this->root,
            'type' => $this->type,
            'meta_key' => 'public_key',
            'actions' => [
                '' => 'Manage',
            ],
            'post_type' => $this->post_type,
        ];
        return $types;
    }

    public function get_grid_totals(){
        return Zume_Public_Heatmap_Queries::query_training_location_grid_totals();
    }

    public function get_world_total() {
        global $wpdb;
        return $wpdb->get_var("
            SELECT COUNT(lg.admin0_grid_id) as count
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
        ");
    }

    public function get_population_division( $country_code ){
        $population_division = 50000;
        if ( $country_code === 'US' ){
            $population_division = 5000;
        }
        return $population_division;
    }
}

