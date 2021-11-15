<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.


/**
 * Class Zume_App_Portal
 */
class Zume_App_Portal extends DT_Magic_Url_Base {

    public $magic = false;
    public $parts = false;
    public $page_title = 'Practitioner Portal';
    public $page_description = 'This is a portal for reporting church multiplication and practitioner profile.';
    public $root = "zume_app";
    public $type = 'portal';
    public $post_type = 'contacts';
    private $meta_key = '';
    public $type_actions = [
        '' => "Home",
        'profile' => "Profile",
        'list' => "List View",
        'map' => "Map View",
    ];
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
        $this->meta_key = $this->root . '_' . $this->type . '_magic_key';
        parent::__construct();

        add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );
        add_action( 'dt_details_additional_section', [ $this, 'dt_details_additional_section' ], 30, 2 );
        add_filter( 'dt_details_additional_tiles', [ $this, 'dt_details_additional_tiles' ], 10, 2 );
        add_filter( 'dt_custom_fields_settings', [ $this, 'add_active_reporter_status' ], 50, 2 );
        add_filter( 'dt_settings_apps_list', [ $this, 'dt_settings_apps_list' ], 10, 1 );

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
        if ( !$this->check_parts_match() ){
            return;
        }

        // load if valid url

        if ( 'list' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'list_body' ] );
        }
        else if ( 'map' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'map_body' ] );
        }
        else if ( 'profile' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'profile_body' ] );
        }
        else if ( '' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'home_body' ] );
        } else {
            return;
        }

        // load if valid url
        add_filter( 'dt_magic_url_base_allowed_css', [ $this, 'dt_magic_url_base_allowed_css' ], 10, 1 );
        add_filter( 'dt_magic_url_base_allowed_js', [ $this, 'dt_magic_url_base_allowed_js' ], 10, 1 );
        add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ], 99 );
    }

    public function dt_magic_url_base_allowed_js( $allowed_js ) {

        $allowed_js[] = 'jquery-touch-punch';
        $allowed_js[] = 'mapbox-gl';
        $allowed_js[] = 'lodash';
        $allowed_js[] = 'introjs-js';
        $allowed_js[] = 'jquery-cookie';
        $allowed_js[] = 'portal';

        if ( 'map' === $this->parts['action'] ) {
            $allowed_js[] = 'heatmap-js';
            $allowed_js[] = 'mapbox-cookie';
        }
        else if ( 'list' === $this->parts['action'] ) {
            $allowed_js[] = 'portal-app-domenu-js';
        }

        return $allowed_js;
    }

    public function dt_magic_url_base_allowed_css( $allowed_css ) {

        $allowed_css[] = 'mapbox-gl-css';
        $allowed_css[] = 'introjs-css';
        $allowed_css[] = 'portal';

        if ( 'map' === $this->parts['action'] ) {
            $allowed_css[] = 'heatmap-css';
        }
        else if ( 'list' === $this->parts['action'] ) {
            $allowed_css[] = 'portal-app-domenu-css';
        }

        return $allowed_css;
    }

    public function _wp_enqueue_scripts() {
        wp_enqueue_script( 'lodash' );
        wp_register_script( 'jquery-touch-punch', '/wp-includes/js/jquery/jquery.ui.touch-punch.js' ); // @phpcs:ignore

        /* intro js */
        wp_enqueue_script( 'introjs-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'intro.min.js', ['jquery'],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'intro.min.js' ), true );

        wp_enqueue_style( 'introjs-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'introjs.min.css', [],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'introjs.min.css' ) );

        /* jquery cookie */
        wp_enqueue_script( 'jquery-cookie', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js.cookie.min.js', [ 'jquery' ],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'js.cookie.min.js' ), true );

        /* group-gen */
        wp_enqueue_script( 'portal', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'portal.js', [ 'jquery' ],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'portal.js' ), true );

        if ( 'map' === $this->parts['action'] ) {

            /* heatmap */
            wp_enqueue_script( 'heatmap-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.js', [],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'heatmap.js' ), true );

            wp_enqueue_style( 'heatmap-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.css', [],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'heatmap.css' ) );

            wp_enqueue_script( 'mapbox-cookie', trailingslashit( get_stylesheet_directory_uri() ) . 'dt-mapping/geocode-api/mapbox-cookie.js', [ 'jquery', 'jquery-cookie' ], '3.0.0' );
        }
        else if ( 'list' === $this->parts['action'] ) {

            /* domenu */
            wp_enqueue_script( 'portal-app-domenu-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'jquery.domenu-0.100.77.min.js', [ 'jquery' ],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'jquery.domenu-0.100.77.min.js' ), true );

            wp_enqueue_style( 'portal-app-domenu-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'jquery.domenu-0.100.77.css', [],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'jquery.domenu-0.100.77.css' ) );
        }

    }

    public function add_active_reporter_status( array $fields, string $post_type = "" ) {
        //check if we are dealing with a contact
        if ( $post_type === "contacts" ) {
            if ( isset( $fields["overall_status"] ) && !isset( $fields["overall_status"]["default"]["reporting_only"] ) ) {
                $fields["overall_status"]["default"]["reporting_only"] = __( "Reporting Only", 'zume-public-heatmaps' );
            }
            $fields["practitioner"] = [
                'name' => __( 'Practitioner', 'zume-public-heatmaps' ),
                'description' => __( "Key stages of engagement to the effort as a practitioner", 'zume-public-heatmaps' ),
                'type' => 'multi_select',
                'default' => [
                    'trained' => [
                        'label' => __( 'Trained', 'zume-public-heatmaps' ),
                        'description' => _x( 'Item 1.', 'field description', 'zume-public-heatmaps' ),
                    ],
                    'practicing' => [
                        'label' => __( 'Practicing', 'zume-public-heatmaps' ),
                        'description' => _x( 'Item 1.', 'field description', 'zume-public-heatmaps' ),
                    ],
                    'reporting' => [
                        'label' => __( 'Reporting', 'zume-public-heatmaps' ),
                        'description' => _x( 'Item 1.', 'field description', 'zume-public-heatmaps' ),
                    ],
                ],
                "tile" => "details",
                "in_create_form" => true,
                'icon' => get_template_directory_uri() . "/dt-assets/images/coach.svg?v=2",
            ];
            $fields["practitioner_community_restrictions"] = [
                'name' => __( 'Community Restrictions', 'zume-public-heatmaps' ),
                'description' => __( "Restrictions for communication and publicity of information in the community/coalition/network.", 'zume-public-heatmaps' ),
                'type' => 'multi_select',
                'default' => [
                    'no_inquiries' => [
                        'label' => __( 'No Inquiries', 'zume-public-heatmaps' ),
                        'description' => _x( 'Do not connect me with other community members or forward inquiries to me.', 'field description', 'zume-public-heatmaps' ),
                    ],
                    'no_public_map' => [
                        'label' => __( 'No Public Map', 'zume-public-heatmaps' ),
                        'description' => _x( 'Do not add my location to the public map. Internal maps can have my location.', 'field description', 'zume-public-heatmaps' ),
                    ]
                ],
                "tile" => "details",
                "in_create_form" => true,
                'icon' => get_template_directory_uri() . "/dt-assets/images/sign-post.svg?v=2",
            ];
        }
        return $fields;
    }

    /**
     * @param $filters
     * @param $post_type
     * @return mixed
     *
     *
     * @todo not currently working
     */
    public static function dt_user_list_filters( $filters, $post_type )
    {
        if ($post_type === 'contacts') {
            $filters["filters"][] = [
                'ID' => 'active_reporter',
                'visible' => "1",
                'type' => 'default',
                'tab' => 'default',
                'name' => __( 'Active Reporter', 'disciple_tools' ),
                'count' => 0,
                'query' => [
                    "overall_status" => [ "active_reporter" ]
                ],
                'labels' => [
                    [
                        'id' => 'active_reporter',
                        'name' => __( 'Active Reporter', 'disciple_tools' ),
                        'field' => 'overall_status',
                    ],
                ],
            ];
        }
        return $filters;
    }

    public function header_style(){
        ?>
        <style>
            body {
                background-color: white !important;
                padding: 0 .2rem;
            }
            #wrapper {
                padding:0 .5rem;
                margin: 0 auto;
            }
            #offCanvasLeft ul {
                list-style-type: none;
            }
            .link {
                cursor: pointer;
                color: #3f729b;
            }
            #location-status {
                height:1.5rem;
                width:1.5rem;
                border-radius: 50%;
                position: absolute;
                bottom: 20px;
                right: 20px;
                z-index: 100;
            }
            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
            .loading-field-spinner.active {
                border-radius: 50%;
                width: 24px;
                height: 24px;
                border: 0.25rem solid #919191;
                border-top-color: black;
                animation: spin 1s infinite linear;
                display: inline-block;
            }
            #initial-loading-spinner {
                padding-top: 10px;
            }
            .mapboxgl-ctrl-top-right.mapboxgl-ctrl{
                width:100% !important;
                margin:10px !important;
            }
            .input-group-button {
                padding:8px 0 0 5px !important;
            }
            #map-edit, #map-wrapper-edit  {
                height: 300px !important;
            }
            .float{
                position:fixed;
                width:60px;
                height:60px;
                bottom:30px;
                right:30px;
                background-color:#3f729b;
                color:#FFF;
                border-radius:50px;
                text-align:center;
                box-shadow: 2px 2px 3px #999;
                z-index:100;
            }
            .floating.fi-plus:before {
                margin-top:22px;
            }
            .dd .dd-new-item {
                background: #3f729b !important;
                color:white !important;
                border: 1px solid #3f729b !important;
                box-shadow: 2px 2px 3px #999;
                border-radius: 20px !important;
            }

        </style>
        <?php
    }

    /**
     * Writes javascript to the footer
     *
     * @see DT_Magic_Url_Base()->footer_javascript() for default state
     */
    public function footer_javascript(){
        if ( 'map' === $this->parts['action'] ) {
            ?>
            <script>
                let jsObject = [<?php echo json_encode([
                    'map_key' => DT_Mapbox_API::get_key(),
                    'mirror_url' => dt_get_location_grid_mirror( true ),
                    'theme_uri' => trailingslashit( get_stylesheet_directory_uri() ),
                    'root' => esc_url_raw( rest_url() ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'intro_images' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/',
                    'parts' => $this->parts,
                    'post_type' => 'groups',
                    'trans' => [
                        'add' => __( 'Add Magic', 'disciple_tools' ),
                    ],
                    'grid_data' => ['data' => [], 'highest_value' => 1 ],
                    'custom_marks' => $this->get_custom_map_markers( $this->parts['post_id'] )
                ]) ?>][0]

                /* custom content */
                function load_self_content( data ) {
                    let pop_div = data.population_division_int * 2
                    jQuery('#custom-paragraph').html(`
                          <span class="self_name ucwords temp-spinner bold">${data.name}</span> is one of <span class="self_peers  bold">${data.peers}</span>
                          administrative divisions in <span class="parent_name ucwords bold">${data.parent_name}</span> and it has a population of
                          <span class="self_population  bold">${data.population}</span>.
                          In order to reach the community goal of 2 churches for every <span class="population_division  bold">${pop_div.toLocaleString("en-US")}</span> people,
                          <span class="self_name ucwords  bold">${data.name}</span> needs
                          <span class="self_needed bold">${data.needed}</span> new churches.
                    `)
                }
                /* custom level content */
                function load_level_content( data, level ) {
                    let gl = jQuery('#'+level+'-list-item')
                    gl.empty()
                    if ( false !== data ) {
                        gl.append(`
                        <div class="cell">
                          <strong>${data.name}</strong><br>
                          Population: <span>${data.population}</span><br>
                          Churches Needed: <span>${data.needed}</span><br>
                          Churches Reported: <span class="reported_number">${data.reported}</span><br>
                          Goal Reached: <span>${data.percent}</span>%
                          <meter class="meter" value="${data.percent}" min="0" low="33" high="66" optimum="100" max="100"></meter>
                        </div>
                    `)
                    }
                }
            </script>
            <?php

            $this->customized_welcome_script();
        }
        else if ( 'list' === $this->parts['action'] ) {
            $post_id = $this->parts["post_id"];
            $post = DT_Posts::get_post( $this->post_type, $post_id, true, false );
            if ( is_wp_error( $post ) ){
                return;
            }
            ?>
            <script>
                let jsObject = [<?php echo json_encode([
                    'map_key' => DT_Mapbox_API::get_key(),
                    'mirror_url' => dt_get_location_grid_mirror( true ),
                    'root' => esc_url_raw( rest_url() ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'intro_images' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/',
                    'parts' => $this->parts,
                    'post' => $post,
                    'translations' => [
                        'add' => __( 'Add Magic', 'disciple-tools-contact-portal' ),
                    ],
                    'grid_data' => ['data' => [], 'highest_value' => 1 ],
                ]) ?>][0]

            </script>
            <?php
        }
        else if ( 'profile' === $this->parts['action'] ) {
            $post_id = $this->parts["post_id"];
            $post = DT_Posts::get_post( $this->post_type, $post_id, true, false );
            if ( is_wp_error( $post ) ){
                return;
            }
            ?>
            <script>
                let jsObject = [<?php echo json_encode([
                    'map_key' => DT_Mapbox_API::get_key(),
                    'mirror_url' => dt_get_location_grid_mirror( true ),
                    'root' => esc_url_raw( rest_url() ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'intro_images' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/',
                    'parts' => $this->parts,
                    'post' => $post,
                    'translations' => [
                        'add' => __( 'Add Magic', 'disciple-tools-contact-portal' ),
                    ],
                    'grid_data' => ['data' => [], 'highest_value' => 1 ],
                ]) ?>][0]

                jQuery('.loading-spinner').removeClass('active')

            </script>
            <?php
        }
        else if ( '' === $this->parts['action'] ) {
            $post_id = $this->parts["post_id"];
            $post = DT_Posts::get_post( $this->post_type, $post_id, true, false );
            if ( is_wp_error( $post ) ){
                return;
            }
            ?>
            <script>
                let jsObject = [<?php echo json_encode([
                    'map_key' => DT_Mapbox_API::get_key(),
                    'mirror_url' => dt_get_location_grid_mirror( true ),
                    'root' => esc_url_raw( rest_url() ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'intro_images' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/',
                    'parts' => $this->parts,
                    'post' => $post,
                    'translations' => [
                        'add' => __( 'Add Magic', 'disciple-tools-contact-portal' ),
                    ],
                    'grid_data' => ['data' => [], 'highest_value' => 1 ],
                ]) ?>][0]

                jQuery('.loading-spinner').removeClass('active')
            </script>
            <?php
        }
    }

    public function list_body(){
        require_once( 'portal-html-list.php' );
    }

    public function map_body(){
        require_once( 'portal-html-map.php' );
    }

    public function profile_body(){
        require_once( 'portal-html-profile.php' );
    }

    public function home_body(){
        require_once( 'portal-html-home.php' );
    }

    public function nav() {
        ?>
        <!-- off canvas menus -->
        <div class="off-canvas-wrapper">
            <!-- Left Canvas -->
            <div class="off-canvas position-left" id="offCanvasLeft" data-off-canvas data-transition="push">
                <button class="close-button" aria-label="Close alert" type="button" data-close>
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="grid-x grid-padding-x" style="padding:1em">
                    <div class="cell"><br><br></div>
                    <div class="cell"><a href="<?php echo esc_url( site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/' ) ?>"><h3><i class="fi-home"></i> Home</h3></a></div>
                    <div class="cell"><a href="<?php echo esc_url( site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/profile' ) ?>"><h3><i class="fi-torso"></i> Community Profile</h3></a></div>
                    <div class="cell"><a href="<?php echo esc_url( site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/list' ) ?>"><h3><i class="fi-list-thumbnails"></i> Edit Church List</h3></a></div>
                    <div class="cell"><a href="<?php echo esc_url( site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/map' ) ?>"><h3><i class="fi-map"></i> Map</h3></a></div>
                    <br><br>
                </div>
            </div>
        </div>
    <?php
    }

    /**
     * Register REST Endpoints
     * @link https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
     */
    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace, '/'.$this->type . '_list', [
                [
                    'methods'  => "POST",
                    'callback' => [ $this, 'endpoint_list' ],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
        register_rest_route(
            $namespace, '/'.$this->type . '_update', [
                [
                    'methods'  => "POST",
                    'callback' => [ $this, 'endpoint_update' ],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
        register_rest_route(
            $namespace,
            '/'.$this->type,
            [
                [
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => [ $this, 'endpoint_map' ],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    public function endpoint_map( WP_REST_Request $request ) {
        $params = $request->get_params();

        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $params = dt_recursive_sanitize_array( $params );
        $action = sanitize_text_field( wp_unslash( $params['action'] ) );

        switch ( $action ) {
            case 'self':
                return Zume_App_Heatmap::get_self( $params['grid_id'], $this->global_div, $this->us_div );
            case 'a3':
            case 'a2':
            case 'a1':
            case 'a0':
            case 'world':
                $list = Zume_App_Heatmap::query_church_grid_totals( $action );
                return Zume_App_Heatmap::endpoint_get_level( $params['grid_id'], $action, $list, $this->global_div, $this->us_div );
            case 'activity_data':
                $grid_id = sanitize_text_field( wp_unslash( $params['grid_id'] ) );
                $offset = sanitize_text_field( wp_unslash( $params['offset'] ) );
                return Zume_App_Heatmap::query_activity_data( $grid_id, $offset );
            case 'grid_data':
                $grid_totals = Zume_App_Heatmap::query_church_grid_totals();
                return Zume_App_Heatmap::_initial_polygon_value_list( $grid_totals, $this->global_div, $this->us_div );
            default:
                return new WP_Error( __METHOD__, "Missing valid action", [ 'status' => 400 ] );
        }
    }

    public function endpoint_list( WP_REST_Request $request ) {
        $params = $request->get_params();
        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $tree = [];
        $title_list = [];
        $pre_tree = [];
        $post_id = $params["parts"]["post_id"];
        $list = DT_Posts::list_posts('groups', [
            'fields_to_return' => [],
            'coaches' => [ $post_id ]
        ], false );

        if ( ! empty( $list['posts'] ) ) {
            foreach ( $list['posts'] as $p ) {
                if ( isset( $p['child_groups'] ) && ! empty( $p['child_groups'] ) ) {
                    foreach ( $p['child_groups'] as $children ) {
                        $pre_tree[$children['ID']] = $p['ID'];
                    }
                }
                if ( empty( $p['parent_groups'] ) ) {
                    $pre_tree[$p['ID']] = null;
                }
                $title_list[$p['ID']] = $p['name'];
            }
            $tree = $this->parse_tree( $pre_tree, $title_list );
        }

        if ( is_null( $tree ) ) {
            $tree = [];
        }

        return [
            'parent_list' => $pre_tree,
            'title_list' => $title_list,
            'tree' => $tree
        ];
    }

    public function endpoint_update( WP_REST_Request $request ) {
        $params = $request->get_params();
        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }
        $params = dt_recursive_sanitize_array( $params );

        $post_id = $params["parts"]["post_id"]; //has been verified in verify_rest_endpoint_permissions_on_post()
        $post = DT_Posts::get_post( $this->post_type, $post_id, true, false );

        $args = [];
        if ( !is_user_logged_in() ){
            $args["comment_author"] = $post['name'];
            wp_set_current_user( 0 );
            $current_user = wp_get_current_user();
            $current_user->add_cap( "create_contact" );
            $current_user->display_name = $post['name'];
        }

        switch ( $params['action'] ) {
            case 'create_group':
                dt_write_log( 'create_group' );

                $inc = $params['data']['inc'];
                $temp_id = $params['data']['temp_id'];
                $parent_id = $params['data']['parent_id'];

                $fields = [
                    "title" => $post['name'] . ' Church ' . $inc,
                    "group_status" => "active",
                    "group_type" => "church",
                    "coaches" => [
                        "values" => [
                            [ "value" => $post_id ]
                        ]
                    ],
                ];

                if ( 'domenu-0' !== $parent_id && is_numeric( $parent_id ) ) {
                    $fields["parent_groups"] = [
                        "values" => [
                            [ "value" => $parent_id ]
                        ]
                    ];
                }

                $new_post = DT_Posts::create_post( 'groups', $fields, true, false );
                if ( ! is_wp_error( $new_post ) ) {
                    // clear cash on church grid totals
                    Zume_App_Heatmap::clear_church_grid_totals();

                    return [
                        'id' => $new_post['ID'],
                        'title' => $new_post['name'],
                        'prev_parent' => $parent_id,
                        'temp_id' => $temp_id,
                        'post' => $new_post,
                        'post_fields' => DT_Posts::get_post_field_settings( 'groups', true, false ),
                        'custom_marks' => self::get_custom_map_markers( $post_id ),
                    ];
                }
                else {
                    dt_write_log( $new_post );
                    return false;
                }

            case 'create_group_by_map':
                dt_write_log( 'create_group_by_map' );

                $inc = $params['data']['inc'];
                $grid_id = $params['data']['grid_id'];
                $title = $params['data']['title'];

                $fields = [
                    "title" => $title . ' Church ' .$inc,
                    "group_status" => "active",
                    "group_type" => "church",
                    "coaches" => [
                        "values" => [
                            [ "value" => $post_id ]
                        ]
                    ],
                    "location_grid_meta" => [
                        "values" => [
                            [
                                "grid_id" => $grid_id
                            ]
                        ]
                    ]
                ];

                $new_post = DT_Posts::create_post( 'groups', $fields, true, false );
                if ( ! is_wp_error( $new_post ) ) {
                    // clear cash on church grid totals
                    Zume_App_Heatmap::clear_church_grid_totals();

                    return [
                        'id' => $new_post['ID'],
                        'title' => $new_post['name'],
                        'post' => $new_post,
                        'post_fields' => DT_Posts::get_post_field_settings( 'groups', true, false ),
                        'custom_marks' => self::get_custom_map_markers( $post_id ),
                    ];
                }
                else {
                    dt_write_log( $new_post );
                    return false;
                }

            case 'onItemRemoved':
                dt_write_log( 'onItemRemoved' );
                $deleted_post = Disciple_Tools_Posts::delete_post( 'groups', $params['data']['id'], false );

                Zume_App_Heatmap::clear_church_grid_totals();

                if ( ! is_wp_error( $deleted_post ) ) {
                    return true;
                }
                else {
                    return false;
                }
            case 'onItemDrop':
                dt_write_log( 'onItemDrop' );
                if ( ! isset( $params['data']['new_parent'], $params['data']['self'], $params['data']['previous_parent'] ) ) {
                    dt_write_log( 'Defaults not found' );
                    return false;
                }

                global $wpdb;
                if ( 'domenu-0' !== $params['data']['previous_parent'] ) {
                    $wpdb->query( $wpdb->prepare(
                        "DELETE
                                FROM $wpdb->p2p
                                WHERE p2p_from = %s
                                  AND p2p_to = %s
                                  AND p2p_type = 'groups_to_groups'", $params['data']['self'], $params['data']['previous_parent'] ) );
                }
                // add parent child
                $wpdb->query( $wpdb->prepare(
                    "INSERT INTO $wpdb->p2p (p2p_from, p2p_to, p2p_type)
                            VALUES (%s, %s, 'groups_to_groups');
                    ", $params['data']['self'], $params['data']['new_parent'] ) );
                return true;

            case 'get_group':
                $id = $params['data']['id'];

                $group = DT_Posts::get_post( 'groups', $id, true, false );
                if ( empty( $group ) || is_wp_error( $group ) ) {
                    return new WP_Error( __METHOD__, 'no group found with that id' );
                }

                // custom permission check. Contact must be coaching group to retrieve group
                if ( ! isset( $group['coaches'] ) || empty( $group['coaches'] ) ) {
                    return new WP_Error( __METHOD__, 'no coaching found for group' );
                }
                $found = false;
                foreach ( $group['coaches'] as $coach ) {
                    if ( (int) $coach['ID'] === (int) $post_id ) {
                        $found = true;
                    }
                }

                if ( $found ) {
                    $group_fields = DT_Posts::get_post_field_settings( 'groups', true, false );
                    return [
                        'post_fields' => $group_fields,
                        'post' => $group,
                    ];
                } else {
                    return new WP_Error( __METHOD__, 'no coaching connection found' );
                }

            case 'update_group_title':
                $post_id = $params['data']['post_id'];
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post( 'groups', $post_id, [ 'title' => trim( $new_value ) ], false, false );

            case 'update_group_member_count':
                $post_id = $params['data']['post_id'];
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post( 'groups', $post_id, [ 'member_count' => trim( $new_value ) ], false, false );

            case 'update_group_start_date':
                $post_id = $params['data']['post_id'];
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post( 'groups', $post_id, [ 'church_start_date' => trim( $new_value ) ], false, false );

            case 'update_group_status':
                $post_id = $params['data']['post_id'];
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post( 'groups', $post_id, [ 'group_status' => trim( $new_value ) ], false, false );

            case 'update_group_location':

                $post_id = $params['data']['post_id'];
                $location_data = $params['data']['location_data'];
                $delete = $params['data']['delete'];

                if ( $delete ) {
                    delete_post_meta( $post_id, 'location_grid' );
                    delete_post_meta( $post_id, 'location_grid_meta' );
                    Location_Grid_Meta::delete_location_grid_meta( $post_id, 'all', 0 );
                }

                $result = DT_Posts::update_post( 'groups', $post_id, $location_data, false, false );

                Zume_App_Heatmap::clear_church_grid_totals();

                return $result;

            case 'delete_group_location':
                $post_id = $params['data']['post_id'];
                delete_post_meta( $post_id, 'location_grid' );
                delete_post_meta( $post_id, 'location_grid_meta' );

                Zume_App_Heatmap::clear_church_grid_totals();

                return Location_Grid_Meta::delete_location_grid_meta( $post_id, 'all', 0 );
        }
        return false;
    }

    public function get_custom_map_markers( $post_id ) {
        global $wpdb;
        $list = $wpdb->get_results($wpdb->prepare( "
            SELECT lgm.lng, lgm.lat, p.post_title
            FROM $wpdb->p2p as p2p
            LEFT JOIN $wpdb->dt_location_grid_meta as lgm ON lgm.post_id = p2p.p2p_from
            LEFT JOIN $wpdb->posts as p ON p.ID = p2p.p2p_from
            WHERE p2p.p2p_to = %s
        ", $post_id), ARRAY_A );

        if ( ! empty( $list ) ) {
            foreach ( $list as $index => $item ) {
                $list[$index]['lng'] = (float) $item['lng'];
                $list[$index]['lat'] = (float) $item['lat'];
            }
        }
        return $list;
    }

    /**
     * @see https://stackoverflow.com/questions/2915748/convert-a-series-of-parent-child-relationships-into-a-hierarchical-tree
     *
     * @param $tree
     * @param $title_list
     * @param null $root
     * @return array|null
     */
    public function parse_tree( $tree, $title_list, $root = null) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach ($tree as $child => $parent) {
            # A direct child is found
            if ($parent == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset( $tree[$child] );
                # Append the child into result array and parse its children
                $return[] = array(
                    'id' => $child,
                    'title' => $child,
                    'name' => $title_list[$child] ?? 'No Name',
                    'children' => $this->parse_tree( $tree, $title_list, $child ),
                    '__domenu_params' => []
                );
            }
        }
        return empty( $return ) ? null : $return;
    }

    /* map section */
    public function get_grid_totals(){
        return Zume_App_Heatmap::query_church_grid_totals();
    }

    public function get_grid_totals_by_level( $administrative_level ) {
        return Zume_App_Heatmap::query_church_grid_totals( $administrative_level );
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
                let asset_url = '<?php echo esc_url( trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/' ) ?>'
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

    /**
     * Post Type Tile Examples
     */
    public function dt_settings_apps_list( $apps_list ) {
        $apps_list[$this->meta_key] = [
            'key' => $this->meta_key,
            'url_base' => $this->root. '/'. $this->type,
            'label' => $this->page_title,
            'description' => $this->page_description,
        ];
        return $apps_list;
    }

    public function dt_details_additional_tiles( $tiles, $post_type = "" ) {
        if ( $post_type === $this->post_type && user_can( get_current_user_id(), 'manage_dt') ){

            $tiles["apps"] = [
                "label" => __( "Apps", 'disciple-tools-contact-portal' ),
                "description" => "This tile contains magic link apps and survey tools."
            ];
        }
        return $tiles;
    }

    public function dt_details_additional_section( $section, $post_type ) {
        // test if campaigns post type and campaigns_app_module enabled
        if ( $post_type === $this->post_type ) {
            if ( 'apps' === $section && user_can( get_current_user_id(), 'manage_dt') ) {
                $record = DT_Posts::get_post( $post_type, get_the_ID() );
                if ( isset( $record[$this->meta_key] )) {
                    $key = $record[$this->meta_key];
                } else {
                    $key = dt_create_unique_key();
                    update_post_meta( get_the_ID(), $this->meta_key, $key );
                }
                $link = DT_Magic_URL::get_link_url( $this->root, $this->type, $key )
                ?>
                <div class="section-subheader">Practitioner Portal</div>
                <div id="practitioner_portal">
                    <a class="button small hollow" href="<?php echo esc_html( $link ); ?>" target="_blank">Open Portal</a>
                    <a class="button small hollow" href="<?php echo esc_html( $link ); ?>" target="_blank">Copy Link</a>
                    <!--                <a class="button" id="open-portal-activity" style="cursor:pointer;">Open Activity</a>-->
                </div>

                <script>
                    jQuery(document).ready(function(){
                        jQuery('#open-portal-activity').on('click', function(e){
                            jQuery('#modal-full-title').empty().html(`Portal Activity`)
                            jQuery('#modal-full-content').empty().html(`content`) // @todo add content logic
                            jQuery('#modal-full').foundation('open')
                        })
                    })
                </script>
                <?php
            }
        }
    }
}
Zume_App_Portal::instance();
