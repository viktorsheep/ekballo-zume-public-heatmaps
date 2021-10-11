<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.


/**
 * Class Zume_App_Portal_Group_Builder
 */
class Zume_App_Portal_Group_Builder extends DT_Magic_Url_Base {

    public $magic = false;
    public $parts = false;
    public $page_title = 'Zume Portal';
    public $root = "zume_app";
    public $type = 'portal';
    public $post_type = 'contacts';
    private $meta_key = '';
    public $type_actions = [
        '' => "Groups",
        'map' => "Map",
        'help' => "Help",
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

        /**
         * post type and module section
         */
        add_action( 'dt_details_additional_section', [ $this, 'dt_details_additional_section' ], 30, 2 );
        add_filter( 'dt_details_additional_tiles', [ $this, 'dt_details_additional_tiles' ], 10, 2 );
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
        if ( !$this->check_parts_match() ){
            return;
        }

        // load if valid url
        if ( 'map' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'map_body' ] );
        }
        else if ( 'help' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'help_body' ] );
        }
        else if ( '' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'groups_body' ] );
        } else {
            add_action( 'dt_blank_body', [ $this, 'groups_body' ] );
        }

        // load if valid url
        add_filter( 'dt_magic_url_base_allowed_css', [ $this, 'dt_magic_url_base_allowed_css' ], 10, 1 );
        add_filter( 'dt_magic_url_base_allowed_js', [ $this, 'dt_magic_url_base_allowed_js' ], 10, 1 );
        add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 99 );
    }

    public function dt_magic_url_base_allowed_js( $allowed_js ) {

        $allowed_js[] = 'jquery-touch-punch';
        $allowed_js[] = 'mapbox-gl';
        $allowed_js[] = 'introjs-js';
        $allowed_js[] = 'jquery-cookie';

        if ( 'map' === $this->parts['action'] ) {
            $allowed_js[] = 'heatmap-js';
        }
        else if ( '' === $this->parts['action'] || 'groups' === $this->parts['action'] ) {
            $allowed_js[] = 'portal-app-group-gen-js';
            $allowed_js[] = 'portal-app-domenu-js';
        }

        return $allowed_js;
    }

    public function dt_magic_url_base_allowed_css( $allowed_css ) {

        $allowed_css[] = 'mapbox-gl-css';
        $allowed_css[] = 'introjs-css';

        if ( 'map' === $this->parts['action'] ) {
            $allowed_css[] = 'heatmap-css';
        }
        else if ( '' === $this->parts['action'] || 'groups' === $this->parts['action'] ) {
            $allowed_css[] = 'portal-app-group-gen-css';
            $allowed_css[] = 'portal-app-domenu-css';
        }

        return $allowed_css;
    }

    public function scripts() {
        wp_register_script( 'jquery-touch-punch', '/wp-includes/js/jquery/jquery.ui.touch-punch.js' ); // @phpcs:ignore

        /* intro js */
        wp_enqueue_script( 'introjs-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'intro.min.js', [ ],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'intro.min.js' ), true );

        wp_enqueue_style( 'introjs-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'introjs.min.css', [],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'introjs.min.css' ) );

        /* jquery cookie */
        wp_enqueue_script( 'jquery-cookie', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js.cookie.min.js', [ 'jquery' ],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'js.cookie.min.js' ), true );

        if ( 'map' === $this->parts['action'] ) {

            /* heatmap */
            wp_enqueue_script( 'heatmap-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.js', [ ],
                filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'heatmap.js' ), true );

            wp_enqueue_style( 'heatmap-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.css', [],
                filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'heatmap.css' ) );
        }
        else if ( '' === $this->parts['action'] || 'groups' === $this->parts['action'] ) {
            /* group-gen */
            wp_enqueue_script( 'portal-app-group-gen-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'portal-app.js', [ 'jquery' ],
                filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'portal-app.js' ), true );

            wp_enqueue_style( 'portal-app-group-gen-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'portal-app.css', [],
                filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'portal-app.css' ) );
            /* domenu */
            wp_enqueue_script( 'portal-app-domenu-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'jquery.domenu-0.100.77.min.js', [ 'jquery' ],
                filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'jquery.domenu-0.100.77.min.js' ), true );

            wp_enqueue_style( 'portal-app-domenu-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'jquery.domenu-0.100.77.css', [],
                filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'jquery.domenu-0.100.77.css' ) );
        }

    }

    /**
     * Writes javascript to the footer
     *
     * @see DT_Magic_Url_Base()->footer_javascript() for default state
     */
    public function footer_javascript(){
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
        $this->customized_welcome_script();
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

    /**
     * Post Type Tile Examples
     */
    public function dt_details_additional_tiles( $tiles, $post_type = "" ) {
        if ( $post_type === $this->post_type ){
            $tiles["dt_contact_portal"] = [
                "label" => __( "Portal", 'disciple-tools-contact-portal' ),
                "description" => "The Portal sets up a page accessible without authentication, only the link is needed. Useful for small applications liked to this record, like quick surveys or updates."
            ];
        }
        return $tiles;
    }

    public function dt_details_additional_section( $section, $post_type ) {
        // test if campaigns post type and campaigns_app_module enabled
        if ( $post_type === $this->post_type ) {
            if ( 'dt_contact_portal' === $section ) {
                $record = DT_Posts::get_post( $post_type, get_the_ID() );
                if ( isset( $record[$this->meta_key] )) {
                    $key = $record[$this->meta_key];
                } else {
                    $key = dt_create_unique_key();
                    update_post_meta( get_the_ID(), $this->meta_key, $key );
                }
                $link = DT_Magic_URL::get_link_url( $this->root, $this->type, $key )
                ?>
                <a class="button" href="<?php echo esc_html( $link ); ?>" target="_blank">Open Link</a>
                <a class="button" id="open-portal-activity" style="cursor:pointer;">Open Activity</a>
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

    public function groups_body(){
        DT_Mapbox_API::geocoder_scripts();
        require_once('portal.html');
    }

    public function map_body(){
        DT_Mapbox_API::geocoder_scripts();
        require_once('portal-map.html');
    }

    public function help_body(){
        DT_Mapbox_API::geocoder_scripts();
        require_once('portal-help-html.php');
    }

    /**
     * Register REST Endpoints
     * @link https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
     */
    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace, '/'.$this->type, [
                [
                    'methods'  => "GET",
                    'callback' => [ $this, 'endpoint_get' ],
                    'permission_callback' => function( WP_REST_Request $request ){
                        $magic = new DT_Magic_URL( $this->root );
                        return $magic->verify_rest_endpoint_permissions_on_post( $request );
                    },
                ],
            ]
        );
        register_rest_route(
            $namespace, '/'.$this->type, [
                [
                    'methods'  => "POST",
                    'callback' => [ $this, 'update_record' ],
                    'permission_callback' => function( WP_REST_Request $request ){
                        $magic = new DT_Magic_URL( $this->root );
                        return $magic->verify_rest_endpoint_permissions_on_post( $request );
                    },
                ],
            ]
        );
    }

    public function endpoint_get( WP_REST_Request $request ) {
        $params = $request->get_params();
        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $tree = [];
        $title_list = [];
        $pre_tree = [];
        $post_id = $params["parts"]["post_id"];
        $list = DT_Posts::list_posts('groups', [
            'fields_to_return' => [  ],
            'coaches' => [ $post_id ]
        ], false );

        if ( ! empty( $list['posts'] ) ) {
            foreach( $list['posts'] as $p ) {
                if ( isset( $p['child_groups'] ) && ! empty( $p['child_groups'] ) ) {
                    foreach( $p['child_groups'] as $children ) {
                        $pre_tree[$children['ID']] = $p['ID'];
                    }
                }
                if (  empty( $p['parent_groups'] ) ) {
                    $pre_tree[$p['ID']] = null;
                }
                $title_list[$p['ID']] = $p['name'];
            }
            $tree = $this->parse_tree($pre_tree, $title_list);
        }

        if ( is_null( $tree) ) {
            $tree = [];
        }

        return [
            'parent_list' => $pre_tree,
            'title_list' => $title_list,
            'tree' => $tree
        ];
    }

    /**
     * @see https://stackoverflow.com/questions/2915748/convert-a-series-of-parent-child-relationships-into-a-hierarchical-tree
     *
     * @param $tree
     * @param null $root
     * @return array|null
     */
    public function parse_tree($tree, $title_list, $root = null) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach($tree as $child => $parent) {
            # A direct child is found
            if($parent == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset($tree[$child]);
                # Append the child into result array and parse its children
                $return[] = array(
                    'id' => $child,
                    'title' => $child,
                    'name' => $title_list[$child] ?? 'No Name',
                    'children' => $this->parse_tree($tree, $title_list, $child),
                    '__domenu_params' => []
                );
            }
        }
        return empty($return) ? null : $return;
    }


    public function update_record( WP_REST_Request $request ) {
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

        switch( $params['action'] ) {
            case 'create_group':
                dt_write_log('create_group');

                $inc = $params['data']['inc'];
                $temp_id = $params['data']['temp_id'];
                $parent_id = $params['data']['parent_id'];



                $fields = [
                    "title" => $post['name'] . ' Group ' . $inc,
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


                $new_post = DT_Posts::create_post('groups', $fields, true, false );
                if ( ! is_wp_error( $new_post ) ) {
                    return [
                        'id' => $new_post['ID'],
                        'title' => $new_post['name'],
                        'prev_parent' => $parent_id,
                        'temp_id' => $temp_id,
                        'post' => $new_post,
                        'post_fields' => DT_Posts::get_post_field_settings('groups', true, false )
                    ];
                }
                else {
                    dt_write_log($new_post);
                    return false;
                }

            case 'onItemRemoved':
                dt_write_log('onItemRemoved');
                $deleted_post = Disciple_Tools_Posts::delete_post( 'groups', $params['data']['id'], false );
                if ( ! is_wp_error( $deleted_post ) ) {
                    return true;
                }
                else {
                    return false;
                }
            case 'onItemDrop':
                dt_write_log('onItemDrop');
                if( ! isset( $params['data']['new_parent'], $params['data']['self'], $params['data']['previous_parent'] ) ) {
                    dt_write_log('Defaults not found');
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

                $group = DT_Posts::get_post('groups', $id, true, false );
                if ( empty( $group ) || is_wp_error( $group ) ) {
                    return new WP_Error(__METHOD__, 'no group found with that id' );
                }

                // custom permission check. Contact must be coaching group to retrieve group
                if ( ! isset( $group['coaches'] ) || empty( $group['coaches'] ) ) {
                    return new WP_Error(__METHOD__, 'no coaching found for group' );
                }
                $found = false;
                foreach( $group['coaches'] as $coach ) {
                    if( (int) $coach['ID'] === (int) $post_id ) {
                        $found = true;
                    }
                }

                if ( $found ) {
                    $group_fields = DT_Posts::get_post_field_settings('groups', true, false );
                    return [
                        'post_fields' => $group_fields,
                        'post' => $group,
                    ];
                } else {
                    return new WP_Error(__METHOD__, 'no coaching connection found' );
                }

            case 'update_group_title':
                $post_id = $params['data']['post_id'];
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post('groups', $post_id, [ 'title' => trim( $new_value ) ], false, false );

            case 'update_group_member_count':
                $post_id = $params['data']['post_id'];
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post('groups', $post_id, [ 'member_count' => trim( $new_value ) ], false, false );

            case 'update_group_type':
                $post_id = $params['data']['post_id'];
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post('groups', $post_id, [ 'group_type' => trim( $new_value ) ], false, false );

            case 'update_group_location':
                $post_id = $params['data']['post_id'];
                $location_data = $params['data']['location_data'];

                return DT_Posts::update_post('groups', $post_id, $location_data, false, false );

            case 'delete_group_location':
                $post_id = $params['data']['post_id'];
                delete_post_meta( $post_id, 'location_grid' );
                delete_post_meta( $post_id, 'location_grid_meta' );
                return Location_Grid_Meta::delete_location_grid_meta( $post_id, 'all', 0 );

            case 'intro_seen':
                $fields = [
                    "intro_seen" => true,
                ];

                DT_Posts::update_post('contacts', $post_id, $fields, true, false);

        }
        return false;
    }

    /* map section */

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
}
Zume_App_Portal_Group_Builder::instance();
