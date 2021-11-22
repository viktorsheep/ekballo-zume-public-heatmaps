<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Public_Heatmaps_Metrics_Overview extends DT_Metrics_Chart_Base
{
    public $base_slug = 'zume-public-heatmaps';
    public $base_title = "Public Maps";

    public $title = 'Overview';
    public $slug = 'link-overview';

    public $permissions = [ 'dt_access_contacts', 'view_project_metrics' ];
    public $magic_link;

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        parent::__construct();

        if ( !$this->has_permission() ){
            return;
        }
        $url_path = dt_get_url_path();

        // only load scripts if exact url
        if ( "metrics/$this->base_slug/$this->slug" === $url_path ) {
            add_action( 'wp_head', [ $this, 'head_script' ] );
        }
    }

    public function head_script() {
        $js_object = [
            'rest_endpoints_base' => esc_url_raw( rest_url() ) . "$this->base_slug/$this->slug",
            'base_slug' => $this->base_slug,
            'slug' => $this->slug,
            'root' => esc_url_raw( rest_url() ),
            'plugin_uri' => plugin_dir_url( __DIR__ ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'current_user_login' => wp_get_current_user()->user_login,
            'current_user_id' => get_current_user_id(),
            'translations' => [
                "title" => $this->title,
                "copy" => __( "Copy Public URL", 'zume-public-heatmap' )
            ]
        ];
        ?>
        <script>
            let jsObject = [<?php echo json_encode( $js_object ) ?>][0]
            "use strict";
            jQuery(document).ready(function() {
                jQuery('#metrics-sidemenu').foundation('down', jQuery(`#${jsObject.base_slug}-menu`));

                jQuery('#chart').empty().html(`
                    <style>
                        .cluster-block {
                             width:100%;
                            height: 100px;
                            background-color: #00aeff;
                        }
                        .heat-block {
                             width:100%;
                            height: 100px;
                            background-color: #8bc34a;
                        }
                    </style>
                    <div class="grid-x">
                            <div class="cell medium-6"><span class="section-header">${jsObject.translations.title}</span></div>
                        </div>
                    <hr style="max-width:100%;">
                    <div class="grid-x">
                        <div class="cell">
                            <p>These public maps and public links are available to non-users and can be shown on websites or sent via email. You can copy the public link in the upper right of each map.</p>
                        </div>
                        <div class="cell">
                            <h2>Cluster/Activity Maps</h2>
                            <div class="grid-x" data-equalizer data-equalize-on="medium">
                                <div class="cell medium-3">
                                    <div class="card" style="width: 300px; float:left;margin:10px;" data-equalizer-watch>
                                      <div class="card-divider">
                                        Last 100 Hours
                                      </div>
                                    <div class="cluster-block"></div>
                                      <div class="card-section">
                                        <h4>This is a card.</h4>
                                        <p>It has an easy to override visual style, and is appropriately subdued.</p>
                                      </div>
                                    </div>
                                </div>
                                <div class="cell medium-3">
                                    <div class="card" style="width: 300px; float:left;margin:10px;" data-equalizer-watch>
                                      <div class="card-divider">
                                        Activity
                                      </div>
                                    <div class="cluster-block"></div>
                                      <div class="card-section">
                                        <h4>This is a card.</h4>
                                        <p>It has an easy to override visual style, and is appropriately subdued.</p>
                                      </div>
                                    </div>
                                </div>
                                <div class="cell medium-3">
                                    <div class="card" style="width: 300px; float:left;margin:10px;" data-equalizer-watch>
                                      <div class="card-divider">
                                        Trainings
                                      </div>
                                    <div class="cluster-block"></div>
                                      <div class="card-section">
                                        <h4>This is a card.</h4>
                                        <p>It has an easy to override visual style, and is appropriately subdued.</p>
                                      </div>
                                    </div>
                                </div>
                                <div class="cell medium-3">
                                    <div class="card" style="width: 300px; float:left;margin:10px;" data-equalizer-watch>
                                      <div class="card-divider">
                                        Streams/Hubs
                                      </div>
                                    <div class="cluster-block"></div>
                                      <div class="card-section">
                                        <h4>This is a card.</h4>
                                        <p>It has an easy to override visual style, and is appropriately subdued.</p>
                                      </div>
                                    </div>
                                </div>

                            </div>
                            <br>
                            <h2>Saturation Maps</h2>
                            <div class="grid-x" data-equalizer data-equalize-on="medium">
                                <div class="cell medium-3">
                                    <div class="card" style="width: 300px; float:left;margin:10px;" data-equalizer-watch>
                                      <div class="card-divider">
                                        Activity Saturation
                                      </div>
                                    <div class="heat-block"></div>
                                      <div class="card-section">
                                        <h4>This is a card.</h4>
                                        <p>It has an easy to override visual style, and is appropriately subdued.</p>
                                      </div>
                                    </div>
                                </div>
                                <div class="cell medium-3">
                                    <div class="card" style="width: 300px; float:left;margin:10px;" data-equalizer-watch>
                                      <div class="card-divider">
                                        Practitioner Saturation
                                      </div>
                                    <div class="heat-block"></div>
                                      <div class="card-section">
                                        <h4>This is a card.</h4>
                                        <p>It has an easy to override visual style, and is appropriately subdued.</p>
                                      </div>
                                    </div>
                                </div>
                                <div class="cell medium-3">
                                    <div class="card" style="width: 300px; float:left;margin:10px;" data-equalizer-watch>
                                      <div class="card-divider">
                                        Churches Saturation
                                      </div>
                                    <div class="heat-block"></div>
                                      <div class="card-section">
                                        <h4>This is a card.</h4>
                                        <p>It has an easy to override visual style, and is appropriately subdued.</p>
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cell">

                        </div>
                    </div>
                `)
            })
        </script>
        <?php
    }
}
Zume_Public_Heatmaps_Metrics_Overview::instance();
