<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
    Zume_Public_Heatmap_Base::instance();
}


class Zume_Public_Heatmap_Base
{

    public $magic = false;
    public $parts = false;
    public $root = "zume_app";
    public $type = '';
    public $key = '';
    public $post_type = '';
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
//        parent::__construct();
        // register type
        $this->magic = new DT_Magic_URL( $this->root );
//        add_filter( 'dt_magic_url_register_types', [ $this, '_register_type' ], 10, 1 );

        // register REST and REST access
        add_filter( 'dt_allow_rest_access', [ $this, '_authorize_url' ], 100, 1 );
        add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );
        add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ], 100 );

    }

    public function _register_type( array $types ) : array {
        if ( ! isset( $types[$this->root] ) ) {
            $types[$this->root] = [];
        }
        $types[$this->root][$this->type] = [
            'name' => 'Magic',
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

    public function _wp_enqueue_scripts(){
        $url = dt_get_url_path();
        if ( strpos( $url, $this->root . '/' . $this->type ) !== false ) {
            wp_enqueue_script( 'lodash' );
            wp_enqueue_script( 'jquery-ui' );
            wp_enqueue_script( 'jquery-touch-punch' );

            wp_enqueue_script( $this->key, trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.js', [
                'jquery',
                'jquery-touch-punch'
            ], filemtime( plugin_dir_path( __FILE__ ) .'heatmap.js' ), true );

            wp_enqueue_style( $this->key, trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.css', [ 'site-css' ], filemtime( plugin_dir_path( __FILE__ ) .'heatmap.css' ) );
        }
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
        if ( $parts ){ // parts returns false
            return true;
        }
        return false;
    }
    public function _header(){
        ?>
        <link rel="dns-prefetch" href="https://storage.googleapis.com/" >
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/1.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/2.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/3.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/4.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/5.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/6.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/7.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/8.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/9.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/10.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/11.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/12.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/13.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/14.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/15.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/16.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/17.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/18.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/19.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/20.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/21.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/22.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/23.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/24.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/25.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/26.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/27.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/28.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/29.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/30.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/31.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/32.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/33.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/34.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/35.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/36.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/37.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/38.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/39.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/40.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/41.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/42.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/43.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/44.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/45.geojson">
        <?php
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
            'site-js',
            'shared-functions',
            'mapbox-gl',
            'mapbox-cookie',
//            'jquery-cookie',
            $this->key
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
            'mapbox-gl-css',
            $this->key
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
    public function header_style(){
        ?>
        <style>
            #initialize-screen {
                background-image: url("<?php echo esc_url( plugin_dir_url( __FILE__ ) ) ?>initialize-background.jpg");
                background-size:cover;
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
                'post_type' => $this->post_type,
                'trans' => [
                    'add' => __( 'Add Magic', 'disciple_tools' ),
                ],
                'grid_data' => ['data' => [], 'highest_value' => 1 ],
            ]) ?>][0]
        </script>
        <?php
        $this->customized_welcome_script();
        return true;
    }

    public function body(){
        DT_Mapbox_API::geocoder_scripts();
        include( 'heatmap.html' );
    }

    /**
     * Grid list build initial map list of elements and drives sidebar
     * @return array
     */
    public function _initial_polygon_value_list(){
        $flat_grid = Zume_Public_Heatmap_Queries::query_saturation_list();
        $grid_totals = $this->get_grid_totals();

        $data = [];
        $highest_value = 1;
        foreach ( $flat_grid as $i => $v ){
            $data[$i] = [
                'grid_id' => $i,
                'population' => number_format_i18n( $v['population'] ),
                'needed' => 1,
                'reported' => 0,
                'percent' => 0,
            ];

            $population_division = $this->get_population_division( $v['country_code'] );

            $needed = round( $v['population'] / $population_division );
            if ( $needed < 1 ){
                $needed = 1;
            }

            if ( isset( $grid_totals[$v['grid_id']] ) && ! empty( $grid_totals[$v['grid_id']] ) ){
                $reported = $grid_totals[$v['grid_id']];
                if ( ! empty( $reported ) && ! empty( $needed ) ){
                    $data[$v['grid_id']]['needed'] = $needed;

                    if ( $reported > $needed ) {
                        $reported = $needed;
                    }
                    $data[$v['grid_id']]['reported'] = $reported;

                    $percent = round( $reported / $needed * 100 );
                    if ( 100 < $percent ) {
                        $percent = 100;
                    } else {
                        $percent = number_format_i18n( $percent, 2 );
                    }
                    $data[$v['grid_id']]['percent'] = $percent;
                }
            }
            else {
                $data[$v['grid_id']]['percent'] = 0;
                $data[$v['grid_id']]['reported'] = 0;
                $data[$v['grid_id']]['needed'] = $needed;
            }

            if ( $highest_value < $data[$v['grid_id']]['reported'] ){
                $highest_value = $data[$v['grid_id']]['reported'];
            }
        }

        return [
            'highest_value' => (int) $highest_value,
            'data' => $data
        ];
    }

    /**
     * Register REST Endpoints
     * @link https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
     */
    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace,
            '/'.$this->type,
            [
                [
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => [ $this, 'endpoint' ],
                ],
            ]
        );
    }

    /**
     * @param WP_REST_Request $request
     * @return array|false|int|WP_Error|null
     */
    public function endpoint( WP_REST_Request $request ) {
        $params = $request->get_params();

        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $params = dt_recursive_sanitize_array( $params );
        $action = sanitize_text_field( wp_unslash( $params['action'] ) );

        switch ( $action ) {
            case 'self':
                return $this->get_self( $params['grid_id'] );
            case 'a3':
            case 'a2':
            case 'a1':
            case 'a0':
            case 'world':
                return $this->endpoint_get_level( $params['grid_id'], $action );
            case 'activity_data':
                $grid_id = sanitize_text_field( wp_unslash( $params['grid_id'] ) );
                $offset = sanitize_text_field( wp_unslash( $params['offset'] ) );
                return $this->query_activity_data( $grid_id, $offset );
            case 'new_report':
                return $this->endpoint_new_report( $params['data'] );
            case 'grid_data':
                return $this->_initial_polygon_value_list();
            default:
                return new WP_Error( __METHOD__, "Missing valid action", [ 'status' => 400 ] );
        }
    }

    public function get_self( $grid_id ) {
        global $wpdb;

        // get grid elements for design
        $grid = $wpdb->get_row( $wpdb->prepare( "
            SELECT
              g.grid_id,
              g.level,
              g.alt_name as name,
              gn.alt_name as parent_name,
              g.country_code,
              g.population,
              IF(ROUND(g.population / IF(g.country_code = 'US', %d, %d)) < 1, 1,
                 ROUND(g.population / IF(g.country_code = 'US', %d, %d))) as needed,
              (SELECT COUNT(prs.grid_id) FROM $wpdb->dt_location_grid as prs WHERE prs.parent_id = g.parent_id ) as peers
            FROM $wpdb->dt_location_grid as g
            LEFT JOIN $wpdb->dt_location_grid as gn ON g.parent_id=gn.grid_id
            WHERE g.grid_id = %s
        ", $this->us_div, $this->global_div, $this->us_div, $this->global_div, $grid_id ), ARRAY_A );

        // set array
        $population_division = $this->get_population_division( $grid['country_code'] );
        $data = [
            'level' => $grid['level'],
            'parent_level' => $grid['level'] - 1, // one level higher than current
            'population_division' => number_format_i18n( $population_division ), // label for content not calculation
            'name' => $grid['name'],
            'parent_name' => $grid['parent_name'],
            'peers' => number_format_i18n( $grid['peers'] ),
            'population' => number_format_i18n( $grid['population'] ),
            'needed' => number_format_i18n( $grid['needed'] ),
        ];

        return $data;
    }

    public function endpoint_get_level( $grid_id, $administrative_level ) {
        // add levels
        $list = $this->get_grid_totals_by_level( $administrative_level ); // get list of training counts
        $flat_grid = Zume_Public_Heatmap_Queries::query_flat_grid_by_level( $administrative_level, $this->us_div, $this->global_div );
        $flat_grid_limited = $this->_limit_counts( $flat_grid, $list ); // limit counts to no larger than needed per location.

        $grid = Zume_Public_Heatmap_Queries::query_grid_elements( $grid_id ); // get level ids for grid_id

        if ( isset( $flat_grid_limited[$grid[$administrative_level]] ) && ! empty( $flat_grid_limited[$grid[$administrative_level]] ) ) {
            $level = $flat_grid_limited[$grid[$administrative_level]];
        }
        else {
            return false;
        }

        $percent = ceil( $level['reported'] / $level['needed'] * 100 );
        if ( 100 < $percent ) {
            $percent = 100;
        } else {
            $percent = number_format_i18n( $percent, 2 );
        }

        /**
         * @todo temp cover for populations
         */
        if ( isset( $grid[$administrative_level . '_population'] )
            && ! empty( $grid[$administrative_level . '_population'] )
            && in_array( $administrative_level, [ 'a0', 'world' ] ) ) {
            $level['population'] = $grid[$administrative_level . '_population'];

            $population_division = $this->get_population_division( $grid['country_code'] );
            $needed = round( $level['population'] / ( $population_division / 2 ) );
            if ( $needed < 1 ){
                $needed = 1;
            }
            $level['needed'] = $needed;
            if ( $administrative_level === 'world' ) {
                $world_population = 7860000000;
                $us_population = 331000000;
                $global_pop_block = $this->global_div;
                $us_pop_block = $this->us_div;
                $world_population_without_us = $world_population - $us_population;
                $needed_without_us = $world_population_without_us / $global_pop_block;
                $needed_in_the_us = $us_population / $us_pop_block;
                $level['needed'] = $needed_without_us + $needed_in_the_us;
            }
        }
        // @todo end temp cover for populations

        if ( empty( $level['name'] ) ) {
            return false;
        }

        $data = [
            'name' => $level['name'],
            'grid_id' => (int) $level['grid_id'],
            'population' => number_format_i18n( $level['population'] ),
            'needed' => number_format_i18n( $level['needed'] ),
            'reported' => number_format_i18n( $level['reported'] ),
            'percent' => $percent,
        ];

        return $data;
    }

    /**
     * Function limits counts to no higher than the location need. This keeps from inflating the counts up the levels.
     * @param $flat_grid
     * @param $list
     * @return array
     */
    public function _limit_counts( $flat_grid, $list ) {
        $flat_grid_limited = [];
        foreach ( $flat_grid as $value ) {
            $flat_grid_limited[$value['grid_id']] = $value;

            if ( isset( $list[$value['grid_id']] ) && ! empty( $list[$value['grid_id']] ) ) {
                if ( $list[$value['grid_id']] <= $value['needed'] ) {
                    $flat_grid_limited[$value['grid_id']]['reported'] = $list[$value['grid_id']];
                } else {
                    $flat_grid_limited[$value['grid_id']]['reported'] = $value['needed'];
                }
            }
        }
        return $flat_grid_limited;
    }

    /****************************************************************************************
     * ACTIVITY SECTION
     ****************************************************************************************/

    public function activity_data( WP_REST_Request $request ){
        $params = $request->get_json_params() ?? $request->get_body_params();

        if ( ! isset( $params['grid_id'] ) ) {
            return new WP_Error( __METHOD__, 'no grid id' );
        }
        if ( ! isset( $params['offset'] ) ) {
            return new WP_Error( __METHOD__, 'no grid id' );
        }
        $grid_id = sanitize_text_field( wp_unslash( $params['grid_id'] ) );
        $offset = sanitize_text_field( wp_unslash( $params['offset'] ) );

        return $this->query_activity_data( $grid_id, $offset );
    }

    public function query_activity_data( $grid_id, $offset ) {
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

    /****************************************************************************************
     * NEW REPORTS SECTION
     ****************************************************************************************/

    public function endpoint_new_report( $form_data ) {
        global $wpdb;
        if ( ! isset( $form_data['grid_id'], $form_data['name'], $form_data['email'], $form_data['phone'], $form_data['list'] ) ) {
            return new WP_Error( __METHOD__, 'Missing params.', [ 'status' => 400 ] );
        }

        $contact_id = false;

        // try to get contact_id and contact
        if ( isset( $form_data['contact_id'] ) && ! empty( $form_data['contact_id'] ) ) {
            $contact_id = (int) $form_data['contact_id'];
            $contact = DT_Posts::get_post( 'contacts', $contact_id, false, false );
            if ( is_wp_error( $contact ) ){
                return $contact;
            }
        }
        else if ( isset( $form_data['return_reporter'] ) && $form_data['return_reporter'] ) {
            $email = sanitize_email( wp_unslash( $form_data['email'] ) );
            // phpcs:disable
            $contact_ids = $wpdb->get_results($wpdb->prepare( "
                SELECT DISTINCT pm.post_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->postmeta as pm1 ON pm.post_id=pm1.post_id AND pm1.meta_key LIKE 'contact_email%' AND pm1.meta_key NOT LIKE '%details'
                WHERE pm.meta_key = 'overall_status' AND pm.meta_value = 'active' AND pm1.meta_value = %s
            ", $email ), ARRAY_A );
            // phpcs:enable
            if ( ! empty( $contact_ids ) ){
                $contact_id = $contact_ids[0]['post_id'];
                $contact = DT_Posts::get_post( 'contacts', $contact_id, false, false );
                if ( is_wp_error( $contact ) ){
                    return $contact;
                }
            }
        }

        // create contact if not able to be found
        if ( ! $contact_id ) {
            // create contact
            $fields = [
                'title' => $form_data['name'],
                "overall_status" => "new",
                "type" => "access",
                "contact_email" => [
                    [ "value" => $form_data['email'] ],
                ],
                "contact_phone" => [
                    [ "value" => $form_data['phone'] ],
                ],
                'notes' => [
                    'source_note' => 'Submitted from public heatmap.'
                ]

            ];
            if ( DT_Mapbox_API::get_key() ) {
                $fields["location_grid_meta"] = [
                    "values" => [
                        [ "grid_id" => $form_data['grid_id'] ]
                    ]
                ];
            } else {
                $fields["location_grid"] = [
                    "values" => [
                        [ "value" => $form_data['grid_id'] ]
                    ]
                ];
            }

            $contact = DT_Posts::create_post( 'contacts', $fields, true, false );
            if ( is_wp_error( $contact ) ){
                return $contact;
            }
            $contact_id = $contact['ID'];
        }

        // create groups
        $group_ids = [];
        $groups = [];
        foreach ( $form_data['list'] as $group ) {
            $fields = [
                'title' => $group['name'],
                'member_count' => $group['members'],
                'start_date' => $group['start'],
                'church_start_date' => $group['start'],
                'group_status' => 'active',
                'leader_count' => 1,
                'group_type' => 'church',
                'members' => [
                    "values" => [
                        [ "value" => $contact_id ],
                    ],
                ],
                'leaders' => [
                    "values" => [
                        [ "value" => $contact_id ],
                    ],
                ],
                'notes' => [
                    'source_note' => 'Submitted from public heatmap.'
                ]
            ];
            if ( DT_Mapbox_API::get_key() ) {
                $fields["location_grid_meta"] = [
                    "values" => [
                        [ "grid_id" => $form_data['grid_id'] ]
                    ]
                ];
            } else {
                $fields["location_grid"] = [
                    "values" => [
                        [ "value" => $form_data['grid_id'] ]
                    ]
                ];
            }

            $g = DT_Posts::create_post( 'groups', $fields, true, false );
            if ( is_wp_error( $g ) ){
                $groups[] = $g;
                continue;
            }
            $group_id = $g['ID'];
            $group_ids[] = $group_id;
            $groups[$group_id] = $g;
        }

        // create connections
        $connection_ids = [];
        if ( ! empty( $group_ids ) ) {
            foreach ( $group_ids as $gid ) {
                $fields = [
                    "peer_groups" => [
                        "values" => [],
                    ]
                ];
                foreach ( $group_ids as $subid ) {
                    if ( $gid === $subid ) {
                        continue;
                    }
                    $fields['peer_groups']['values'][] = [ "value" => $subid ];
                }

                $c = DT_Posts::update_post( 'groups', $gid, $fields, true, false );
                $connection_ids[] = $c;
            }
        }

        $data = [
            'contact' => $contact,
            'groups' => $groups,
            'connections' => $connection_ids
        ];

        return $data;
    }

    public function get_reported( $grid_id, $grid_totals = [] ) : int {
//        if ( is_null($grid_totals)) {
//            $grid_totals = Zume_Public_Heatmap_Queries::query_church_location_grid_totals();
//        }
        if ( isset( $grid_totals[$grid_id] )){
            return (int) $grid_totals[$grid_id]['count'];
        }
        else {
            return 0;
        }
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

}

