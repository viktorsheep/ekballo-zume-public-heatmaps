<?php
/**
 * Map of Zume activity by movement log data.
 */

if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
    Zume_Public_Heatmap_Activity::instance();
}


add_filter('dt_network_dashboard_supported_public_links', function( $supported_links ){
    $supported_links[] = [
        'name' => 'Public Heatmap ( Zume Activity )',
        'description' => 'Maps church saturation by admin2 (counties)',
        'key' => 'zume_app_heatmap_activity',
        'url' => 'zume_app/heatmap_activity'
    ];
    return $supported_links;
}, 10, 1 );


class Zume_Public_Heatmap_Activity extends Zume_Public_Heatmap_Base
{

    public $magic = false;
    public $parts = false;
    public $root = "zume_app";
    public $type = 'heatmap_activity';
    public $post_type = 'groups';

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
            'name' => 'Zume Activity',
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

    public function _register_url( $template_for_url ){
        $parts = $this->parts;

        // test 1 : correct url root and type
        if ( ! $parts ){ // parts returns false
            return $template_for_url;
        }

        // test 2 : only base url requested
        if ( empty( $parts['public_key'] ) ){ // no public key present
            $template_for_url[ $parts['root'] . '/'. $parts['type'] ] = 'template-blank.php';
            return $template_for_url;
        }

        // test 3 : no specific action requested
        if ( empty( $parts['action'] ) ){ // only root public key requested
            $template_for_url[ $parts['root'] . '/'. $parts['type'] . '/' . $parts['public_key'] ] = 'template-blank.php';
            return $template_for_url;
        }

        // test 4 : valid action requested
        $actions = $this->magic->list_actions( $parts['type'] );
        if ( isset( $actions[ $parts['action'] ] ) ){
            $template_for_url[ $parts['root'] . '/'. $parts['type'] . '/' . $parts['public_key'] . '/' . $parts['action'] ] = 'template-blank.php';
        }

        return $template_for_url;
    }
    public function _has_access() : bool {
        $parts = $this->parts;

        // test 1 : correct url root and type
        if ( $parts ){ // parts returns false
            return true;
        }

        return false;
    }
    public function _header(){
        wp_head();
        $this->header_style();
        $this->header_javascript();
    }
    public function _footer(){
        wp_footer();
    }
    public function _authorize_url( $authorized ){
        if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), $this->root . '/v1/'.$this->type ) !== false ) {
            $authorized = true;
        }
        return $authorized;
    }
    public function _print_scripts(){
        // @link /disciple-tools-theme/dt-assets/functions/enqueue-scripts.php
        $allowed_js = [
            'jquery',
            'lodash',
            'moment',
            'datepicker',
            'site-js',
            'shared-functions',
            'mapbox-gl',
            'mapbox-cookie',
            'mapbox-search-widget',
            'google-search-widget',
            'jquery-cookie',
        ];

        global $wp_scripts;

        if ( isset( $wp_scripts ) ){
            foreach ( $wp_scripts->queue as $key => $item ){
                if ( ! in_array( $item, $allowed_js ) ){
                    unset( $wp_scripts->queue[$key] );
                }
            }
        }
        unset( $wp_scripts->registered['mapbox-search-widget']->extra['group'] );
    }
    public function _print_styles(){
        // @link /disciple-tools-theme/dt-assets/functions/enqueue-scripts.php
        $allowed_css = [
            'foundation-css',
            'jquery-ui-site-css',
            'site-css',
            'datepicker-css',
            'mapbox-gl-css'
        ];

        global $wp_styles;
        if ( isset( $wp_styles ) ) {
            foreach ($wp_styles->queue as $key => $item) {
                if ( !in_array( $item, $allowed_css )) {
                    unset( $wp_styles->queue[$key] );
                }
            }
        }
    }
    public function _browser_tab_title( $title ){
        return __( "Zúme Activity Map", 'disciple_tools' );
    }

    public function header_style(){
        ?>
        <style>
            body {
                background: white;
            }
        </style>
        <?php
    }
    public function header_javascript(){
        ?>
        <script>
            let jsObject = [<?php echo json_encode([
                'map_key' => DT_Mapbox_API::get_key(),
                'mirror_url' => dt_get_location_grid_mirror( true ),
                'theme_uri' => trailingslashit( get_stylesheet_directory_uri() ),
                'root' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'parts' => $this->parts,
                'trans' => [
                    'add' => __( 'Add Magic', 'disciple_tools' ),
                ],
                'grid_data' => $this->grid_list(),
            ]) ?>][0]

            jQuery(document).ready(function(){
                clearInterval(window.fiveMinuteTimer)
            })

            window.get_movement_data = (grid_id) => {
                let offset = new Date().getTimezoneOffset();
                return jQuery.ajax({
                    type: "POST",
                    data: JSON.stringify({ action: 'POST', parts: jsObject.parts, grid_id: grid_id, offset: offset }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type + '/movement_data',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
                    }
                })
                    .fail(function(e) {
                        console.log(e)
                        jQuery('#error').html(e)
                    })
            }

        </script>
        <?php
        return true;
    }
    public function body(){
        DT_Mapbox_API::geocoder_scripts();
        ?>
        <div id="custom-style"></div>
        <div id="wrapper">
            <div id="map-wrapper">
                <div class="hide-for-small-only" style="position:absolute; top: 10px; left:10px; z-index: 10;background-color:white; opacity: .9;padding:5px 10px; margin: 0 10px;">
                    <div class="grid-x">
                        <div class="cell" id="name-id">Hover and zoom for locations</div>
                    </div>
                </div>

                <div id='map'><span class="loading-spinner active"></span></div>
            </div>
        </div>
        <div class="off-canvas position-left is-closed" id="offCanvasNestedPush" data-transition-time=".3s" data-off-canvas>
            <div class="grid-x grid-padding-x " style="margin-top:1rem;">
                <div class="cell">
                    <h1 id="title">Title</h1>
                    <hr>
                </div>
                <div class="cell">
                    <h2>Engagement <i class="fi-info primary-color"></i>: <span id="saturation-goal">0</span>%</h2>
                    <meter id="meter" style="height:3rem;width:100%;" value="30" min="0" low="33" high="66" optimum="100" max="100"></meter>
                </div>
                <div class="cell">
                    <h2>Population: <span id="population">0</span></h2>
                </div>
                <div class="cell">
                    <h2>Events: <span id="reported">0</span></h2>
                </div>
                <div class="cell">
                    <hr>
                </div>
                <div class="cell ">
                    <div class="callout" style="background-color:whitesmoke;">
                        <h2>Activity:</h2>
                        <div id="slider-content"></div>
                    </div>

                </div>
            </div>
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <script>
            jQuery(document).ready(function($){

                /* set vertical size the form column*/
                $('#custom-style').append(`
                    <style>
                        #wrapper {
                            height: ${window.innerHeight}px !important;
                        }
                        #map-wrapper {
                            height: ${window.innerHeight}px !important;
                        }
                        #map {
                            height: ${window.innerHeight}px !important;
                        }
                        .off-canvas {
                        width:${window.innerWidth * .50}px;
                        background-color:white;
                        }
                    </style>`)

                $('#map').empty()
                mapboxgl.accessToken = jsObject.map_key;
                var map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/light-v10',
                    // style: 'mapbox://styles/mapbox/streets-v11',
                    center: [-98, 38.88],
                    minZoom: 2,
                    maxZoom: 8,
                    zoom: 2
                });

                map.addControl(
                    new MapboxGeocoder({
                        accessToken: mapboxgl.accessToken,
                        mapboxgl: mapboxgl,
                        marker: false
                    })
                );

                map.addControl(new mapboxgl.NavigationControl());
                map.dragRotate.disable();
                map.touchZoomRotate.disableRotation();

                window.previous_hover = false

                map.on('load', function() {

                    let asset_list = []
                    var i = 1;
                    while( i <= 46 ){
                        asset_list.push(i+'.geojson')
                        i++
                    }

                    jQuery.each(asset_list, function(i,v){

                        jQuery.ajax({
                            url: jsObject.mirror_url + 'tiles/world/saturation/' + v,
                            dataType: 'json',
                            data: null,
                            beforeSend: function (xhr) {
                                if (xhr.overrideMimeType) {
                                    xhr.overrideMimeType("application/json");
                                }
                            }
                        })
                        .done(function (geojson) {

                            jQuery.each(geojson.features, function (i, v) {
                                if (jsObject.grid_data[v.id]) {
                                    geojson.features[i].properties.value = parseInt(jsObject.grid_data[v.id].percent)
                                } else {
                                    geojson.features[i].properties.value = 0
                                }
                            })

                            map.addSource(i.toString(), {
                                'type': 'geojson',
                                'data': geojson
                            });
                            map.addLayer({
                                'id': i.toString()+'line',
                                'type': 'line',
                                'source': i.toString(),
                                'paint': {
                                    'line-color': '#323A68',
                                    'line-width': .5
                                }
                            });

                            /**************/
                            /* hover map*/
                            /**************/
                            map.addLayer({
                                'id': i.toString() + 'fills',
                                'type': 'fill',
                                'source': i.toString(),
                                'paint': {
                                    'fill-color': 'black',
                                    'fill-opacity': [
                                        'case',
                                        ['boolean', ['feature-state', 'hover'], false],
                                        .8,
                                        0
                                    ]
                                }
                            })
                            /* end hover map*/

                            /**********/
                            /* heat map brown */
                            /**********/
                            map.addLayer({
                                'id': i.toString() + 'fills_heat',
                                'type': 'fill',
                                'source': i.toString(),
                                'paint': {
                                    'fill-color': [
                                        'interpolate',
                                        ['linear'],
                                        ['get', 'value'],
                                        0,
                                        'rgba(0,0,0,0)',
                                        10,
                                        'yellow',
                                        // 30,
                                        // 'red',
                                        // 70,
                                        // 'yellow',
                                        100,
                                        'darkgreen',

                                    ],
                                    'fill-opacity': 0.7
                                }
                            })
                            /**********/
                            /* end fill map */
                            /**********/

                            map.on('mousemove', i.toString()+'fills', function (e) {
                                if ( window.previous_hover ) {
                                    map.setFeatureState(
                                        window.previous_hover,
                                        { hover: false }
                                    )
                                }
                                window.previous_hover = { source: i.toString(), id: e.features[0].id }
                                if (e.features.length > 0) {
                                    jQuery('#name-id').html(e.features[0].properties.full_name)
                                    map.setFeatureState(
                                        window.previous_hover,
                                        {hover: true}
                                    );
                                }
                            });
                            map.on('click', i.toString()+'fills', function (e) {

                                $('#title').html(e.features[0].properties.full_name)
                                $('#meter').val(jsObject.grid_data[e.features[0].properties.grid_id].percent)
                                $('#saturation-goal').html(jsObject.grid_data[e.features[0].properties.grid_id].percent)
                                $('#population').html(jsObject.grid_data[e.features[0].properties.grid_id].population)

                                //report
                                $('#report-modal-title').html(e.features[0].properties.full_name)
                                $('#report-grid-id').html(e.features[0].properties.grid_id)

                                let reported = jsObject.grid_data[e.features[0].properties.grid_id].reported
                                $('#reported').html(reported)

                                let needed = jsObject.grid_data[e.features[0].properties.grid_id].needed
                                $('#needed').html(needed)

                                let sc = $('#slider-content')
                                sc.html('<span class="loading-spinner active"></span>')

                                window.get_movement_data(e.features[0].properties.grid_id)
                                .done(function(data){
                                    console.log(data)
                                    sc.empty()
                                    $.each(data, function(i,v){
                                        if ( typeof v.message !== 'undefined' ){

                                            sc.append(`<div><div style="float:left;width:180px;"><strong>${v.formatted_time}</strong></div> <span>${v.message}</span></div>`)
                                        }
                                    })
                                })

                                $('#offCanvasNestedPush').foundation('toggle', e);

                            });
                        })
                    })
                })
            })
        </script>
        <?php
    }

    public function grid_list(){
        $list = $this->query_world_saturation_grid();
        $grid_list = $this->query_activity_grid();

        $data = [];
        foreach ( $list as $v ){
            $data[$v['grid_id']] = [
                'grid_id' => $v['grid_id'],
                'percent' => 0,
                'reported' => 0,
                'needed' => 1,
                'population' => number_format_i18n( $v['population'] ),
            ];

            $population_division = 1000;

            $needed = round( $v['population'] / $population_division );
            if ( $needed < 1 ){
                $needed = 1;
            }

            if ( isset( $grid_list[$v['grid_id']] ) && ! empty( $grid_list[$v['grid_id']]['count'] ) ){
                $count = $grid_list[$v['grid_id']]['count'];
                if ( ! empty( $count ) && ! empty( $needed ) ){
                    $percent = round( $count / $needed * 100 );

                    $data[$v['grid_id']]['percent'] = $percent;
                    $data[$v['grid_id']]['reported'] = $grid_list[$v['grid_id']]['count'];
                    $data[$v['grid_id']]['needed'] = $needed;
                }
            }
            else {
                $data[$v['grid_id']]['percent'] = 0;
                $data[$v['grid_id']]['reported'] = 0;
                $data[$v['grid_id']]['needed'] = $needed;
            }
        }

        return $data;
    }

    public function query_activity_grid(){
        global $wpdb;
        $list = $wpdb->get_results( "
            SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
            FROM (
             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log as ml
                JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                WHERE ml.grid_id > 0
            ) as t0
            GROUP BY t0.admin0_grid_id
            UNION
            SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
            FROM (
             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log as ml
                JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                WHERE ml.grid_id > 0
            ) as t1
            GROUP BY t1.admin1_grid_id
            UNION
            SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log as ml
                JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                WHERE ml.grid_id > 0
            ) as t2
            GROUP BY t2.admin2_grid_id
            UNION
            SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log as ml
                JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                WHERE ml.grid_id > 0
            ) as t3
            GROUP BY t3.admin3_grid_id
            UNION
            SELECT t4.admin4_grid_id as grid_id, count(t4.admin4_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log as ml
                JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                WHERE ml.grid_id > 0
            ) as t4
            GROUP BY t4.admin4_grid_id
            UNION
            SELECT t5.admin5_grid_id as grid_id, count(t5.admin5_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log as ml
                JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                WHERE ml.grid_id > 0
            ) as t5
            GROUP BY t5.admin5_grid_id;
            ", ARRAY_A );

        $data = [];
        foreach ( $list as $item ){
            $data[$item['grid_id']] = $item;
        }

        return $data;
    }

    public function query_world_saturation_grid(){
        global $wpdb;
        return $wpdb->get_results("
        SELECT
        lg0.grid_id, lg0.population, lg0.country_code
        FROM $wpdb->dt_location_grid lg0
        LEFT JOIN $wpdb->dt_location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
        WHERE lg0.level < 1
        AND lg0.country_code NOT IN (
            SELECT lg23.country_code FROM $wpdb->dt_location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
        )
        AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
        AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')

        UNION ALL
        --
        # Only admin1
        --
        SELECT
        lg1.grid_id, lg1.population, lg1.country_code
        FROM $wpdb->dt_location_grid as lg1
        LEFT JOIN $wpdb->dt_location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
        WHERE lg1.country_code NOT IN (
        SELECT lg22.country_code FROM $wpdb->dt_location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
        ) AND lg1.level_name != 'admin0'
        AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
        AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')


        UNION ALL
        --
        # Has admin2
        --
        SELECT
        lg2.grid_id, lg2.population, lg2.country_code
        FROM $wpdb->dt_location_grid lg2
        LEFT JOIN $wpdb->dt_location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
        WHERE lg2.level_name = 'admin2'
        AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
        AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')

        UNION ALL

        # Exceptions admin3

        SELECT
        lge.grid_id, lge.population, lge.country_code
        FROM $wpdb->dt_location_grid lge
        LEFT JOIN $wpdb->dt_location_grid as a0 ON lge.admin0_grid_id=a0.grid_id
        WHERE a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
            AND lge.level_name = 'admin3'


        UNION ALL

        # Exceptions admin1

        SELECT
        lge1.grid_id, lge1.population, lge1.country_code
        FROM $wpdb->dt_location_grid lge1
        LEFT JOIN $wpdb->dt_location_grid as a0 ON lge1.admin0_grid_id=a0.grid_id
        WHERE lge1.level_name = 'admin1'
        AND a0.name IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')

        ", ARRAY_A );
    }



    /**
     * Register REST Endpoints
     * @link https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
     */
    public function add_endpoints() {
        $namespace = $this->root . '/v1';

        register_rest_route(
            $namespace,
            '/'.$this->type .'/movement_data/',
            array(
                array(
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'movement_data' ),
                ),
            )
        );
    }

    public function movement_data( WP_REST_Request $request ){
        $params = $request->get_json_params() ?? $request->get_body_params();

        if ( ! isset( $params['grid_id'] ) ) {
            return new WP_Error( __METHOD__, 'no grid id' );
        }
        if ( ! isset( $params['offset'] ) ) {
            return new WP_Error( __METHOD__, 'no grid id' );
        }
        $grid_id = sanitize_text_field( wp_unslash( $params['grid_id'] ) );
        $offset = sanitize_text_field( wp_unslash( $params['offset'] ) );

        return $this->query_movement_data( $grid_id, $offset );

    }

    public function query_movement_data( $grid_id, $offset ) {
        global $wpdb;
        $ids = [];
        $ids[] = $grid_id;
        $children = Disciple_Tools_Mapping_Queries::get_children_by_grid_id( $grid_id );
        if ( ! empty( $children ) ) {
            foreach ( $children as $child ){
                $ids[] = $child['grid_id'];
            }
        }
        $prepared_list = dt_array_to_sql( $ids );
        // phpcs:disable
        $list = $wpdb->get_results("
                SELECT
                       id,
                       action,
                       category,
                       lng,
                       lat,
                       label,
                       grid_id,
                       payload,
                       timestamp,
                       'A Zúme partner' as site_name
                FROM $wpdb->dt_movement_log
                WHERE grid_id IN ($prepared_list)
                ORDER BY timestamp DESC", ARRAY_A);
        // phpcs:enable
        if ( empty( $list ) ){
            return [];
        }

        foreach ( $list as $index => $item ){
            $list[$index]['payload'] = maybe_unserialize( $item['payload'] );
            $list[$index]['formatted_time'] = gmdate( 'M, d Y, g:i a', $item['timestamp'] + $offset );
        }

        if ( function_exists( 'zume_log_actions' ) ) {
            $list = zume_log_actions( $list );
        }
        if ( function_exists( 'dt_network_dashboard_translate_log_generations' ) ) {
            $list = dt_network_dashboard_translate_log_generations( $list );
        }
        if ( function_exists( 'dt_network_dashboard_translate_log_new_posts' ) ) {
            $list = dt_network_dashboard_translate_log_new_posts( $list );
        }

        foreach ( $list as $index => $item ){
            if ( ! isset( $item['message'] ) ) {
                $list[$index]['message'] = 'Non-public movement event reported.';
            }
        }

        return $list;
    }

}


