<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
    Zume_Public_Heatmap_100hours_V2::instance();
}

/**
 * Class Disciple_Tools_Plugin_Starter_Template_Magic_Link
 */
class Zume_Public_Heatmap_100hours_V2 extends DT_Magic_Url_Base {

    public $magic = false;
    public $parts = false;
    public $page_title = 'Last 100 Hours';
    public $root = "zume_app";
    public $type = 'last100_hours';
    public $post_type = 'contacts';
    private $meta_key = '';

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        $this->meta_key = $this->root . '_' . $this->type . '_magic_key';
        parent::__construct();

        /**
         * post type and module section
         */
        add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );


        /**
         * tests if other URL
         */
        $url = dt_get_url_path();
        if ( strpos( $url, $this->root . '/' . $this->type ) === false ) {
            return;
        }
        /**
         * tests magic link parts are registered and have valid elements
         */
        if ( !$this->check_parts_match( false ) ){
            return;
        }

        // require classes
        if ( ! class_exists( 'DT_Ipstack_API' ) ) {
            require_once( trailingslashit( get_theme_file_path() ) . 'dt-mapping/geocode-api/ipstack-api.php' );
        }
        if ( ! class_exists( 'DT_Mapbox_API' ) ) {
            require_once( trailingslashit( get_theme_file_path() ) . 'dt-mapping/geocode-api/mapbox-api.php' );
        }

        // remove header notification
        remove_action( 'wp_head', 'dt_release_modal' );

        // load if valid url
        add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 99 );
        add_action( 'dt_blank_body', [ $this, 'body' ] ); // body for no post key
        add_filter( 'dt_magic_url_base_allowed_css', [ $this, 'dt_magic_url_base_allowed_css' ], 10, 1 );
        add_filter( 'dt_magic_url_base_allowed_js', [ $this, 'dt_magic_url_base_allowed_js' ], 10, 1 );

    }

    public function dt_magic_url_base_allowed_js( $allowed_js ) {
        $allowed_js[] = 'jquery-cookie';
        $allowed_js[] = 'mapbox-cookie';
        $allowed_js[] = 'mapbox-gl';
        $allowed_js[] = 'last100-hours-js';
        $allowed_js[] = 'lodash';
        return $allowed_js;
    }

    public function dt_magic_url_base_allowed_css( $allowed_css ) {
        $allowed_css[] = 'mapbox-gl-css';
        return $allowed_css;
    }

    public function scripts() {
        wp_enqueue_script( 'lodash' );
        wp_enqueue_script( 'last100-hours-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'last100-hours.js', [ 'jquery' ],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'last100-hours.js' ), true );
    }

    /**
     * Writes custom styles to header
     *
     * @see DT_Magic_Url_Base()->header_style() for default state
     */
    public function header_style(){
        ?>
        <style>
            body {
                background-color: white;
                padding: 0;
            }
            /**
            Custom Styles
             */
            .blessing {
                background-color: #21336A;
                border: 1px solid white;
                color: white;
                font-weight: bold;
                margin:0;
            }
            .great-blessing {
                background-color: #2CACE2;
                border: 1px solid white;
                color: white;
                font-weight: bold;
                margin:0;
            }
            .greater-blessing {
                background-color: #FAEA38;
                border: 1px solid white;
                color: #21336A;
                font-weight: bold;
                margin:0;
            }
            .greatest-blessing {
                background-color: #90C741;
                border: 1px solid white;
                color: white;
                font-weight: bold;
                margin:0;
            }
            .blessing:hover {
                border: 1px solid #21336A;
            }
            .great-blessing:hover {
                border: 1px solid #21336A;
                background-color: #2CACE2;
            }
            .greater-blessing:hover {
                border: 1px solid #21336A;
                background-color: #FAEA38;
                color: #21336A;
            }
            .greatest-blessing:hover {
                border: 1px solid #21336A;
                background-color: #90C741;
            }
            .filtered {
                background-color: lightgrey;
                color: white;
            }
            .filtered:hover {
                background-color: lightgrey;
                border: 1px solid #21336A;
                color: white;
            }
            #activity-list {
                font-size:.7em;
                list-style-type:none;
            }
            #map-loader {
                position: absolute;
                top:40%;
                left:50%;
                z-index: 20;
            }
            #map-header {
                position: absolute;
                top:10px;
                left:10px;
                z-index: 20;
                background-color: white;
                padding:1em;
                opacity: 0.8;
                border-radius: 5px;
            }
            .center-caption {
                font-size:.8em;
                text-align:center;
                color:darkgray;
            }
            .caption {
                font-size:.8em;
                color:darkgray;
                padding-bottom:1em;
            }
        </style>
        <?php
    }

    /**
     * Writes javascript to the footer
     * @see DT_Magic_Url_Base()->footer_javascript() for default state
     */
    public function footer_javascript(){
        ?>
        <script>
            let jsObject = [<?php echo json_encode([
                'map_key' => DT_Mapbox_API::get_key(),
                'root' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'parts' => $this->parts
            ]) ?>][0]
            /* <![CDATA[ */

            window.dt_mapbox_metrics = [<?php echo json_encode([
                'translations' => [
                    'title' => __( "Last 100 Hours", "disciple_tools" ),
                ],
                'settings' => [
                    'map_key' => DT_Mapbox_API::get_key(),
                    'points_rest_url' => '/'.$this->type,
                    'points_rest_base_url' => $this->root . '/v1',
                ]
            ]) ?>][0]
            /* ]]> */

            window.post_request = ( action, data ) => {
                return jQuery.ajax({
                    type: "POST",
                    data: JSON.stringify({ action: action, parts: jsObject.parts, data: data }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
                    }
                })
                    .fail(function(e) {
                        console.log(e)
                        jQuery('#error').html(e)
                        jQuery('.loading-spinner').removeClass('active')
                    })
            }
        </script>
        <?php
    }

    public function body(){
        DT_Mapbox_API::geocoder_scripts();
        ?>
        <div id="chart"></div>
        <?php
    }

    /**
     * Register REST Endpoints
     */
    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace, '/'.$this->type, [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [ $this, 'endpoint' ],
                    'permission_callback' => function( WP_REST_Request $request ){
                        return true;
                    },
                ],
            ]
        );
    }

    public function endpoint( WP_REST_Request $request ) {
        $params = $request->get_params();

        if ( ! isset( $params['parts'], $params['action'], $params['data'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $params = dt_recursive_sanitize_array( $params );
        $action = sanitize_text_field( wp_unslash( $params['action'] ) );

        switch ( $action ) {
            case 'load_geojson':
                return Zume_Public_Heatmap_100hours_Utilities::get_activity_geojson();
            case 'activity_list':
                return Zume_Public_Heatmap_100hours_Utilities::get_activity_list( $params['data'], true );
            default:
                return new WP_Error( __METHOD__, "Missing valid action", [ 'status' => 400 ] );
        }
    }

//    public function geojson( $data ) {
//        if ( isset( $data['timezone'] ) && ! empty( $data['timezone'] ) ) {
//            $tz_name = sanitize_text_field( wp_unslash( $data['timezone'] ) );
//        } else {
//            $tz_name = 'America/Denver';
//        }
//        $country = 'none';
//        if ( isset( $data['country'] ) && ! empty( $data['country'] )) {
//            $country = sanitize_text_field( wp_unslash( $data['country'] ) );
//        }
//        $language = 'none';
//        if ( isset( $data['language'] ) && ! empty( $data['language'] )) {
//            $language = sanitize_text_field( wp_unslash( $data['language'] ) );
//        }
//        return Zume_Public_Heatmap_100hours_Utilities::get_activity_geojson( $tz_name, $country, $language );
//    }
//
//    public function _empty_geojson() {
//        return array(
//            'type' => 'FeatureCollection',
//            'features' => []
//        );
//    }
}
