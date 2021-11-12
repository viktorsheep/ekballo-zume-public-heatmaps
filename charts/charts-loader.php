<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Public_Heatmaps_Metrics
{
    private static $_instance = null;
    public static function instance(){
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct(){

        require_once( 'metrics-map-practitioners.php' );
        new Zume_Public_Heatmaps_Metrics_Map_Practitioners();

        /**
         * @todo add other charts like the pattern above here
         */

    } // End __construct
}
Zume_Public_Heatmaps_Metrics::instance();
