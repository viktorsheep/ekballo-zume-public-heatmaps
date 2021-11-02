<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
    Zume_Public_Reporter_Manager::instance();
}

class Zume_Public_Reporter_Manager extends DT_Magic_Url_Base
{
    public $page_title = 'Reporter Manager';
    public $root = "zume_app";
    public $type = 'reporter_manager';
    public $type_name = 'Manager';
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

        add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );

        // fail if not valid url
        $url = dt_get_url_path();
        if ( strpos( $url, $this->root . '/' . $this->type ) === false ) {
            return;
        }

        if ( !$this->check_parts_match( false ) ){
            return;
        }

        add_action( 'dt_blank_body', [ $this, 'body' ] );
        add_filter( 'dt_magic_url_base_allowed_css', [ $this, 'dt_magic_url_base_allowed_css' ], 10, 1 );
        add_filter( 'dt_magic_url_base_allowed_js', [ $this, 'dt_magic_url_base_allowed_js' ], 10, 1 );
        add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 99 );

    }

    public function dt_magic_url_base_allowed_js( $allowed_js ) {
        $allowed_js[] = 'reporter';
        return $allowed_js;
    }

    public function dt_magic_url_base_allowed_css( $allowed_css ) {
        return $allowed_css;
    }

    public function wp_enqueue_scripts(){
        wp_enqueue_script( 'reporter', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'reporter.js', [
        ], filemtime( plugin_dir_path( __FILE__ ) .'reporter.js' ), true );
    }

    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace,
            '/'.$this->type,
            [
                [
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => [ $this, 'endpoint' ],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    public function endpoint( WP_REST_Request $request ) {
        $params = $request->get_params();

        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $params = dt_recursive_sanitize_array( $params );
        $action = sanitize_text_field( wp_unslash( $params['action'] ) );

        switch ( $action ) {
            case 'new_registration':
                return Zume_App_Heatmap::create_new_reporter( 'zume_app', 'report_new_churches', $params['data'] );
            case 'send_link':
                return Zume_App_Heatmap::send_reporter_link( 'zume_app', 'report_new_churches', $params['data'] );

            default:
                return new WP_Error( __METHOD__, "Missing valid action", [ 'status' => 400 ] );
        }
    }

    public function body(){
        ?>
        <div id="wrapper">
            <div class="grid-x">
                <div class="cell" id="report-content"></div>
            </div>
        </div>
        <?php
    }

    public function footer_javascript(){
        ?>
        <style>
            body {
                background-color:white;
            }
            #wrapper {
                max-width: 600px;
                margin: 1em auto;
            }
            #email {
                display:none !important;
            }
        </style>
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
                    'add' => __( 'Zume', 'disciple_tools' ),
                ],
            ]) ?>][0]
        </script>
        <?php
    }
}
