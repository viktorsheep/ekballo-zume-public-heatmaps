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
                <a href="<?php echo esc_attr( $link ) . 'general' ?>" class="nav-tab <?php echo esc_html( ( $tab == 'general' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>">General</a>
                <a href="<?php echo esc_attr( $link ) . 'sync' ?>" class="nav-tab <?php echo esc_html( ( $tab == 'sync' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>">Sync</a>
            </h2>

            <?php
            switch ($tab) {
                case "general":
                    $object = new Zume_Public_Heatmaps_Tab_General();
                    $object->content();
                    break;

                case "sync":
                  $object = new Zume_Public_Heatmaps_Tab_Sync();
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

                        <?php $this->main_column() ?>
                        <?php DT_Ipstack_API::metabox_for_admin(); ?>

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
        ?>
        <form method="post">
            <?php wp_nonce_field( 'heatmap_settings_nonce', 'heatmap_settings' )?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
            <tr>
                <th>Heatmaps</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                   Reporter Manager<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/reporter_manager"><?php echo esc_url( site_url() ) ?>/zume_app/reporter_manager</a>
                </td>
            </tr>
            <tr>
                <td>
                    Last 100 Hours<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/last100_hours"><?php echo esc_url( site_url() ) ?>/zume_app/last100_hours</a>
                </td>
            </tr>
            <tr>
                <td>
                    Practitioners Heatmap<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/heatmap_practitioners"><?php echo esc_url( site_url() ) ?>/zume_app/heatmap_practitioners</a>
                </td>
            </tr>
            <tr>
                <td>
                    Church Heatmap<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/heatmap_churches"><?php echo esc_url( site_url() ) ?>/zume_app/heatmap_churches</a>
                </td>
            </tr>
            <tr>
                <td>
                    Trainings Cluster Map<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/cluster_trainings"><?php echo esc_url( site_url() ) ?>/zume_app/cluster_trainings</a>
                </td>
            </tr>
            <tr>
                <td>
                    Streams Cluster Map<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/cluster_streams"><?php echo esc_url( site_url() ) ?>/zume_app/cluster_streams</a>
                </td>
            </tr>

            <!-- Zume -->
            <tr>
                <td>
                    Network Activities Map<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/heatmap_activity"><?php echo esc_url( site_url() ) ?>/zume_app/heatmap_activity</a>
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

class Zume_Public_Heatmaps_Tab_Sync {

	public function getSetting() {
		$result = '';
		try {
			global $wpdb;

			$settingTableName = $wpdb->prefix . '_euzume_settings';
			$result = $wpdb->get_results("SELECT count(*) FROM $settingTableName WHERE name = 'is_synced'", ARRAY_A);
		} catch(Exception $e) {
			$result = $e->getMessage();
		}

		return $result;
	}

  public function content() {
  ?>
		<script>
			<?php require('sync-scripts.js'); ?>
		</script>

		<script>
		let settings = <?php
			$settings = Zume_App_Heatmap::get_zume_settings();
			echo json_encode($settings);
		?>

		const syncCount = <?php
			$count = Zume_App_Heatmap::get_zume_church_count();
			echo json_encode($count);
		?>

		const jsObject = <?php
			echo json_encode([
				'mirror'    => dt_get_location_grid_mirror( true ),
                'baseURL'   => esc_url_raw(rest_url()),
                'api'       => 'zume_app',
                'version'   => 'v1',
                'part'      => 'heatmap_1000',
                'nonce'     => wp_create_nonce( 'wp_rest' ),
                'parts'     => $this->parts,
                'post_type' => 'groups'
			]);
		?>
		</script>

  <div class="wrap-sync">
    <h2>Sync Church Count</h2>

		<div class="synopsis"> Syncronizing churches' count tally to each geoJsons to load the map faster.</div>

		<div class="wrap-detail">
			<table class="tbl-data">
				<tr>
					<td>
						<div><span id="txtChurchCountSyncStatus"></span></div>
					</td>
					<td>
						<div><b>Last synced</b> : <span id="txtLastSynced"></span></div>
					</td>
				</tr>
			</table>

			<div class="loader"><span></span><div class="content"><img src="<?php echo esc_url( get_admin_url() . 'images/loading.gif' ); ?>" /> Loading...</div></div>
		</div>

		<div class="wrap-controls">
	    <button id="btnSync" type="button" class="button button-success" onclick="handleBtnSyncClicked()">
	      Sync Now
	    </button>
	
			<!--
			<button type="button" class="button" onclick="getChurchData()">
				Get Church Data
			</button>
			-->

			<button id="btnReset" type="button" class="button" onclick="resetSyncData()">
				Reset Sync
			</button>

			<!--
			<button id="btnGetChurchCountData" type="button" class="button" onclick="getChurchCountData()">		
				Get Church Count Data
			</button>

			<button id="btnGetZumeSettings" type="button" class="button" onclick="getZumeSettings()">
				Get Zume Settings
			</button>
			-->

		</div>

		<div id="wrapSyncProgress">
			<div class="overview">
				<div id="progressOverview">
					<div id="progGetGeoJson"><span>Get grid data (geojsons)</span></div>
					<div id="progGetChurchData"><span>Get church data</span></div>
					<div id="progSync"><span>Sync church data with grid data</span></div>
				</div>
			</div>
			<div class="progress">
				<div id="txtCurrentProgress">Commencing Synchronization</div>
				<div id="txtProgressLog">Starting...</div>
			</div>
			<div style="clear: both;"></div>

			<div style="text-align: center; margin-top: 10px;">
				<button type="button" class="button button-primary" id="btnCloseSyncProgress" style="display:none;" onclick="closeSyncProgress()">
					Close
				</button>
			</div>
		</div>

		<div id="wrapChurchCountData"></div>

  </div> 

		<style>
			<?php require('sync-styles.css'); ?>
		</style>


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
