<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

//if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
//    Zume_Public_Heatmap_Base::instance();
//}


class Zume_Public_Heatmap_Base
{
    public $root = 'zume_app';
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
    }







    /****************************************************************************************
     *  CLASS EXTENSION CUSTOMIZATION FUNCTIONS
     ****************************************************************************************/

    public function get_grid_totals(){
        return Zume_App_Heatmap::query_church_grid_totals();
    }

    public function get_grid_totals_by_level( $administrative_level ) {
        return Zume_App_Heatmap::query_church_grid_totals( $administrative_level );
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

