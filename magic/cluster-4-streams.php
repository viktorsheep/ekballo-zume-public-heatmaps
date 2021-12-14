<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
    Zume_Public_Heatmaps_Cluster_Streams::instance();
}

/**
 * Class Disciple_Tools_Plugin_Starter_Template_Magic_Link
 */
class Zume_Public_Heatmaps_Cluster_Streams extends DT_Magic_Url_Base {

    public $magic = false;
    public $parts = false;
    public $page_title = 'Streams';
    public $root = "zume_app";
    public $type = 'cluster_streams';
    public $post_type = 'streams';
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
        $allowed_js[] = 'cluster-js';
        $allowed_js[] = 'lodash';
        return $allowed_js;
    }

    public function dt_magic_url_base_allowed_css( $allowed_css ) {
        $allowed_css[] = 'mapbox-gl-css';
        return $allowed_css;
    }

    public function scripts() {
        wp_enqueue_script( 'lodash' );
        wp_enqueue_script( 'cluster-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'cluster.js', [ 'jquery' ],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'cluster.js' ), true );
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
                'parts' => $this->parts,
                'translation' => [
                    'title' => 'Streams'
                ]
            ]) ?>][0]
            /* <![CDATA[ */

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
                return self::geojson();
            case 'load_empty_geojson':
                return self::geojson( true );
            case 'activity_list':
                return self::activity( $params['data'] );
            default:
                return new WP_Error( __METHOD__, "Missing valid action", [ 'status' => 400 ] );
        }
    }

    public function geojson( $empty = false ){
        $flat_grid = Zume_App_Heatmap::query_saturation_list_full();
        $grid_totals = Zume_App_Heatmap::query_streams_grid_totals();

        foreach ( $flat_grid as $i => $v ){
            $properties = [
                'grid_id' => $i,
                'reported' => 0,
            ];

            if ( isset( $grid_totals[$v['grid_id']] ) && ! empty( $grid_totals[$v['grid_id']] ) ){
                $properties['reported'] = $grid_totals[$v['grid_id']];
            }

            $lng = round( $v['longitude'], 2 );
            $lat = round( $v['latitude'], 2 );

            if ( $empty && 0 === $properties['reported'] ) { // true && report is 0, then
                $features[] = array(
                    'type' => 'Feature',
                    'id' => $i,
                    'geometry' => array(
                        'type' => 'Point',
                        'coordinates' => array(
                            $lng,
                            $lat,
                            1
                        ),
                    ),
                );
            }

            else if ( ! $empty && $properties['reported'] ) { // neg false && report positive
                $features[] = array(
                    'type' => 'Feature',
                    'id' => $i,
                    'geometry' => array(
                        'type' => 'Point',
                        'coordinates' => array(
                            $lng,
                            $lat,
                            1
                        ),
                    ),
                );
            }
        }

        $new_data = array(
            'type' => 'FeatureCollection',
            'features' => $features,
        );

        return $new_data;
    }

    public function activity( $filters ){
        $flat_grid = Zume_App_Heatmap::query_saturation_list_with_filters( $filters );
        $grid_totals = Zume_App_Heatmap::query_streams_grid_totals();

        $list = [];
        $empty = 0;
        foreach ( $flat_grid as $i => $v ){
            if ( isset( $grid_totals[$v['grid_id']] ) && ! empty( $grid_totals[$v['grid_id']] ) ){
                $list[] = $v['full_name'] . ' (' . $grid_totals[$v['grid_id']] . ')';
            }
            else {
                $empty++;
            }
        }

        if ( empty( $list ) ) {
            return [
                'list' => [],
                'count' => 0,
                'empty_count' => ( ! empty( $flat_grid ) ) ? count( $flat_grid ) : 0
            ];
        }

        $c = array_chunk( $list, 250 );
        return [
            'list' => $c[0] ?? $list,
            'count' => count( $list ),
            'empty_count' => $empty
        ];
    }

}
