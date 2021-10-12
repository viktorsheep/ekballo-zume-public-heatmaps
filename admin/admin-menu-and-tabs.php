<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Class Zume_Public_Heatmaps_Menu
 */
class Zume_Public_Heatmaps_Menu {

    public $token = 'zume_public_heatmaps';

    private static $_instance = null;

    /**
     * Zume_Public_Heatmaps_Menu Instance
     *
     * Ensures only one instance of Zume_Public_Heatmaps_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return Zume_Public_Heatmaps_Menu instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()


    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {

        add_action( "admin_menu", array( $this, "register_menu" ) );

    } // End __construct()


    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu() {
        add_submenu_page( 'dt_extensions', 'Zume Map/Report', 'Zume Map/Report', 'manage_dt', $this->token, [ $this, 'content' ] );
    }

    /**
     * Menu stub. Replaced when Disciple Tools Theme fully loads.
     */
    public function extensions_menu() {}

    /**
     * Builds page contents
     * @since 0.1
     */
    public function content() {

        if ( !current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        if ( isset( $_GET["tab"] ) ) {
            $tab = sanitize_key( wp_unslash( $_GET["tab"] ) );
        } else {
            $tab = 'general';
        }

        $link = 'admin.php?page='.$this->token.'&tab=';

        ?>
        <div class="wrap">
            <h2>Zume Map/Report</h2>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_attr( $link ) . 'general' ?>"
                   class="nav-tab <?php echo esc_html( ( $tab == 'general' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>">General</a>
            </h2>

            <?php
            switch ($tab) {
                case "general":
                    $object = new Zume_Public_Heatmaps_Tab_General();
                    $object->content();
                    break;

                default:
                    break;
            }
            ?>

        </div><!-- End wrap -->

        <?php
    }
}
Zume_Public_Heatmaps_Menu::instance();

/**
 * Class Zume_Public_Heatmaps_Tab_General
 */
class Zume_Public_Heatmaps_Tab_General {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php DT_Ipstack_API::metabox_for_admin(); ?>
                        <?php $this->main_column() ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->

                        <?php $this->right_column() ?>

                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function main_column() {
        if ( isset( $_POST['heatmap_settings'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['heatmap_settings'] ) ), 'heatmap_settings_nonce' ) ) {
            $globe_div = sanitize_text_field( wp_unslash( str_replace( ',', '', trim( $_POST['globe_div'] ?? '' ) ) ) );
            update_option( 'heatmap_global_div', $globe_div, true );

            $us_div = sanitize_text_field( wp_unslash( str_replace( ',', '', trim( $_POST['us_div'] ?? '' ) ) ) );
            update_option( 'heatmap_us_div', $us_div, true );
        }
        $globe_div = get_option( 'heatmap_global_div', 25000 );
        $us_div = get_option( 'heatmap_us_div', 2500 );
        ?>
        <form method="post">
            <?php wp_nonce_field( 'heatmap_settings_nonce', 'heatmap_settings' )?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
            <tr>
                <th>Heatmap Goals</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Global Division <br>
                    <input type="text" name="globe_div" value="<?php echo $globe_div ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    US Division <br>
                    <input type="text" name="us_div" value="<?php echo $us_div ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    The division about calculates the number of units per population division. Example: To get 2 churches for every 50k people. You must count one unit for every 25k, which results in 2 for every 50k.
                </td>
            </tr>
            <tr>
                <td>
                    <button type="submit">Update</button>
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        </form>
        <?php
    }

    public function right_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Information</th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }
}

/**
 * Class Zume_Public_Heatmaps_Tab_General
 */
class Zume_Public_Heatmaps_Tab_ShortCodes {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->

                        <?php $this->right_column() ?>

                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function main_column() {
        DT_Ipstack_API::metabox_for_admin();
    }

    public function right_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
            <tr>
                <th>Information</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }
}
