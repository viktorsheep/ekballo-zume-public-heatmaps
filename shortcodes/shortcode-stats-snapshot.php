<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.

class Movement_Shortcode_Stats_Snapshot
{
    public $namespace = 'movement_maps_stats/v1/';
    public $shortcode_token = 'stats_snapshot';

    private static $_instance = null;
    public static function instance() {
        if (is_null( self::$_instance )) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        add_shortcode( $this->shortcode_token, [ $this, 'short_code' ] );
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
        add_action( 'movement_maps_stats_shortcodes_list', [ $this, 'instructions_for_shortcode'] );
    }

    public function short_code( $atts ){

        $data = $this->get_data();

        // begin echo cache
        ob_start();
        ?>
        <style>
            #mms-wrapper {
                width: 100%;
                max-width:600px;
                margin: auto;
            }
            .mms-row {
                width:100%;
                float:left;
                padding-bottom:10px;
            }
            .mms-left-box {
                width: 50%;
                float:left;
            }
            .mms-right-box {
                width: 50%;
                float:right;
            }
            .mms-header {
                padding-top: 15px;
                padding-bottom: 10px;
            }
            .mms-header h2 {
                font-size:2rem;
            }
            .mms-header h2 span {
                font-size:1.2rem;
                color: grey;
                font-style: italic;
            }
            span.mms-up {
                /* &#8593; */
                font-weight: bold;
                color: white;
                background-color:green;
                padding: 0 10px;
                margin-top:1px;
                border-radius:15px;
                border: 1px solid white;
            }
            span.mms-down {
                /* &#8595; */
                font-weight: bold;
                color: white;
                content: "\2198";
                background-color:indianred;
                padding: 0 10px;
                margin-top:1px;
                border-radius:15px;
                border: 1px solid white;

            }
            .mms-center {
                text-align: center;
            }
            .display-config-1 {
                display:none;
            }
        </style>
        <div id="mms-wrapper">
            <div class="mms-row">
                <img src="https://zume.vision/wp-content/uploads/sites/12/2020/09/zume-community-report-1.png" style="padding-top:10px" />
            </div>
            <div class="mms-row mms-header mms-center">
                Progress Report from Jan 7, 2020
            </div>

            <hr>
            <div class="mms-row mms-header">
                <h2>Blessings <span>(Knowing Jesus Better)</span></h2>
            </div>
            <div class="mms-row">
                <strong>Saturation</strong><br>
                <?php echo esc_html( $data['blessing']['countries_last_week'] )  ?> countries, <?php echo esc_html( $data['blessing']['states_last_week'] )  ?> states, <?php echo esc_html( $data['blessing']['counties_last_week'] )  ?> counties with blessings in the last week.<br>
                <?php echo esc_html( $data['blessing']['countries_this_year'] )  ?> countries, <?php echo esc_html( $data['blessing']['states_this_year'] )  ?> states, <?php echo esc_html( $data['blessing']['counties_this_year'] )  ?> counties with blessings from Zúme start.<br>
                <?php echo esc_html( $data['blessing']['countries_in_the_world'] )  ?> countries, <?php echo esc_html( $data['blessing']['states_in_the_world'] )  ?> states, <?php echo esc_html( $data['blessing']['counties_in_the_world'] )  ?> counties in the world.<br>
                <?php echo esc_html( $data['blessing']['countries_remaining'] )  ?> countries, <?php echo esc_html( $data['blessing']['states_remaining'] )  ?> states, <?php echo esc_html( $data['blessing']['counties_remaining'] )  ?> counties yet to reach.<br>
            </div>
            <div class="mms-row">
                <div class="mms-left-box">
                    <strong>Studying</strong><br>
                    <span class="mms-down" style="display:<?php echo ( $data['blessing']['studying_percent_change_week'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up"  style="display:<?php echo ( $data['blessing']['studying_percent_change_week'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['blessing']['studying_percent_change_week'] )  ?>% from last week<br>

                    <span class="mms-down" style="display:<?php echo ( $data['blessing']['studying_percent_change_year'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up"  style="display:<?php echo ( $data['blessing']['studying_percent_change_year'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['blessing']['studying_percent_change_year'] )  ?>% for the year<br>
                </div>
                <div class="mms-right-box">
                    <strong>Joining</strong><br>
                    <span class="mms-down" style="display:<?php echo ( $data['blessing']['joining_percent_change_week'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up" style="display:<?php echo ( $data['blessing']['joining_percent_change_week'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['blessing']['joining_percent_change_week'] )  ?>% from last week<br>

                    <span class="mms-down" style="display:<?php echo ( $data['blessing']['joining_percent_change_year'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up" style="display:<?php echo ( $data['blessing']['joining_percent_change_year'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['blessing']['joining_percent_change_year'] )  ?>% for the year<br>
                </div>
            </div>

            <hr>
            <div class="mms-row mms-header">
                <h2>Great Blessings <span>(Helping Others Know Jesus)</span></h2>
            </div>

            <div class="mms-row">
                <div class="mms-left-box">
                    <strong>Top Locations</strong><br>
                    <?php
                    foreach( $data['great_blessing']['top_locations'] as $index => $value ){
                        $index++;
                        echo $index . '. ' . $value . '<br>';
                    }
                    ?>
                </div>
                <div class="mms-left-box">
                    <strong>Top Languages</strong><br>
                    <?php
                    foreach( $data['great_blessing']['top_languages'] as $index => $value ){
                        $index++;
                        echo $index . '. ' . $value . '<br>';
                    }
                    ?>
                </div>
            </div>
            <div class="mms-row">
                <div class="mms-left-box">
                    <strong>Leading</strong><br>
                    <span class="mms-down" style="display:<?php echo ( $data['great_blessing']['leading_percent_change_week'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up" style="display:<?php echo ( $data['great_blessing']['leading_percent_change_week'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['great_blessing']['leading_percent_change_week'] )  ?>% from last week<br>

                    <span class="mms-down" style="display:<?php echo ( $data['blessing']['leading_percent_change_year'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up" style="display:<?php echo ( $data['blessing']['leading_percent_change_year'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['blessing']['leading_percent_change_year'] )  ?>% for the year<br>
                </div>
                <div class="mms-left-box">
                    <strong>Praying</strong><br>
                    <span class="mms-down" style="display:<?php echo ( $data['great_blessing']['praying_percent_change_week'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up" style="display:<?php echo ( $data['great_blessing']['praying_percent_change_week'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['great_blessing']['praying_percent_change_week'] )  ?>% from last week<br>

                    <span class="mms-down" style="display:<?php echo ( $data['great_blessing']['praying_percent_change_year'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up" style="display:<?php echo ( $data['great_blessing']['praying_percent_change_year'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['great_blessing']['praying_percent_change_year'] )  ?>% for the year<br>
                </div>
            </div>

            <hr>
            <div class="mms-row mms-header">
                <h2>Greater Blessings <span>(Starting Spiritual Families)</span></h2>
            </div>
            <div class="mms-row">
                <p>
                    <?php echo esc_html( $data['greater_blessing']['summary'] ) ?>
                </p>
                <p>
                    Pray with us that these trainings bear fruit for disciples
                </p>
            </div>
            <div class="mms-row">
                <div class="mms-left-box">
                    <strong>Training</strong><br>
                    <span class="mms-up">&#10138;</span> .0056% from last week<br>
                    <span class="mms-up">&#10138;</span> .0056% for the year<br>
                </div>
                <div class="mms-right-box">
                    <strong>Forming Spiritual Families</strong><br>
                    <span class="mms-up">&#10138;</span> .00012% from last week<br>
                    <span class="mms-up">&#10138;</span> .00012% for the year<br>
                </div>
            </div>



            <hr>
            <div class="mms-row mms-header">
                <h2>Greatest Blessings <span>(Helping Others Start Spiritual Families)</span></h2>
            </div>
            <div class="mms-row">
                <p>
                    We've identified <?php echo esc_html( $data['greatest_blessing']['coaching_events'] )  ?> coaching events between coaches and learners or peer mentoring.
                    We also have <?php echo esc_html( $data['greatest_blessing']['movement_reports'] ) ?> reports from disciple multipliers about generational growth withing their network.
                </p>
                <p>
                    <?php echo esc_html( $data['greatest_blessing']['top_countries_string'] ) ?> have been the most active in reporting in the last week.
                </p>
            </div>
            <div class="mms-row">
                <div class="mms-left-box">
                    <strong>Coaching</strong><br>
                    <span class="mms-down" style="display:<?php echo ( $data['greatest_blessing']['coaching_percent_change_week'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up" style="display:<?php echo ( $data['greatest_blessing']['coaching_percent_change_week'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['greatest_blessing']['coaching_percent_change_week'] )  ?>% from last week<br>

                    <span class="mms-down" style="display:<?php echo ( $data['greatest_blessing']['coaching_percent_change_year'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up" style="display:<?php echo ( $data['greatest_blessing']['coaching_percent_change_year'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['greatest_blessing']['coaching_percent_change_year'] )  ?>% for the year<br>
                </div>
                <div class="mms-right-box">
                    <strong>Reporting</strong><br>
                    <span class="mms-down" style="display:<?php echo ( $data['greatest_blessing']['reporting_percent_change_week'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up" style="display:<?php echo ( $data['greatest_blessing']['reporting_percent_change_week'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['greatest_blessing']['reporting_percent_change_week'] )  ?>% from last week<br>

                    <span class="mms-down" style="display:<?php echo ( $data['greatest_blessing']['reporting_percent_change_year'] > 0 ) ? 'none' : 'inline'; ?>;">&#10136;</span>
                    <span class="mms-up" style="display:<?php echo ( $data['greatest_blessing']['reporting_percent_change_year'] > 0 ) ? 'inline' : 'none'; ?>;">&#10138;</span>
                    <?php echo esc_html( $data['greatest_blessing']['reporting_percent_change_year'] )  ?>% for the year<br>
                </div>
            </div>
        </div>
        <br><br>
        <?php

        return ob_get_clean();
    }

    public function get_data(){

        $data = [
            'total' => [],
            'blessing' => [],
            'great_blessing' => [],
            'greater_blessing' => [],
            'greatest_blessing' => []
        ];

        /**********************************************************/
        /* BLESSING */
        /**********************************************************/
        // location
        $data['blessing']['countries_last_week'] = '100';
        $data['blessing']['states_last_week'] = '200';
        $data['blessing']['counties_last_week'] = '500';

        $data['blessing']['countries_this_year'] = '100';
        $data['blessing']['states_this_year'] = '200';
        $data['blessing']['counties_this_year'] = '500';

        $data['blessing']['countries_in_the_world'] = '100';
        $data['blessing']['states_in_the_world'] = '200';
        $data['blessing']['counties_in_the_world'] = '500';

        $data['blessing']['countries_remaining'] = '120';
        $data['blessing']['states_remaining'] = '120';
        $data['blessing']['counties_remaining'] = '120';

        // lists
        $data['blessing']['top_locations'] = [
            'United States',
            'Philippines',
            'Russia',
            'Spain',
            'Brazil'
        ];
        $data['blessing']['top_languages'] = [
            'English',
            'Arabic',
            'Russian',
            'Portuguese',
            'Slovenian'
        ];

        // statements

        // percent change
        $data['blessing']['studying_percent_change_week'] = .0056; // percent this week vs last week
        $data['blessing']['studying_percent_change_year'] = -.0056;
        $data['blessing']['joining_percent_change_week'] = .0056;
        $data['blessing']['joining_percent_change_year'] = -.0056;

        /**********************************************************/
        /* GREAT BLESSING */
        /**********************************************************/
        $data['great_blessing']['countries_last_week'] = '100';
        $data['great_blessing']['states_last_week'] = '200';
        $data['great_blessing']['counties_last_week'] = '500';

        $data['great_blessing']['countries_this_year'] = '100';
        $data['great_blessing']['states_this_year'] = '200';
        $data['great_blessing']['counties_this_year'] = '500';

        $data['great_blessing']['countries_in_the_world'] = '100';
        $data['great_blessing']['states_in_the_world'] = '200';
        $data['great_blessing']['counties_in_the_world'] = '500';

        $data['great_blessing']['countries_remaining'] = '120';
        $data['great_blessing']['states_remaining'] = '120';
        $data['great_blessing']['counties_remaining'] = '120';

        $data['great_blessing']['top_locations'] = [
            'United States',
            'Philippines',
            'Russia',
            'Spain',
            'Brazil'
        ];
        $data['great_blessing']['top_languages'] = [
            'English',
            'Arabic',
            'Russian',
            'Portuguese',
            'Slovenian'
        ];
        $data['great_blessing']['praying_percent_change_week'] = .0056;
        $data['great_blessing']['praying_percent_change_year'] = -.0056;
        $data['great_blessing']['leading_percent_change_week'] = .0056;
        $data['great_blessing']['leading_percent_change_year'] = -.0056;

        /**********************************************************/
        /* GREATER BLESSING */
        /**********************************************************/

        $data['greater_blessing']['countries_last_week'] = '100';
        $data['greater_blessing']['states_last_week'] = '200';
        $data['greater_blessing']['counties_last_week'] = '500';

        $data['greater_blessing']['countries_this_year'] = '100';
        $data['greater_blessing']['states_this_year'] = '200';
        $data['greater_blessing']['counties_this_year'] = '500';

        $data['greater_blessing']['countries_in_the_world'] = '100';
        $data['greater_blessing']['states_in_the_world'] = '200';
        $data['greater_blessing']['counties_in_the_world'] = '500';

        $data['greater_blessing']['countries_remaining'] = '120';
        $data['greater_blessing']['states_remaining'] = '120';
        $data['greater_blessing']['counties_remaining'] = '120';
        $data['greater_blessing']['top_locations'] = [
            'United States',
            'Philippines',
            'Russia',
            'Spain',
            'Brazil'
        ];
        $data['greater_blessing']['top_languages'] = [
            'English',
            'Arabic',
            'Russian',
            'Portuguese',
            'Slovenian'
        ];

        // statements

        $data['greater_blessing']['summary'] = sprintf( "We saw %s multiplication trainings reported in the last week,
        and have counted %s movement trainings in the last year. Though reporting is often inconsistent,
        we know about %s new spiritual families forming in the last week and %s in the last year.", '324', '2,432', '15', '345' );

        // percent change
        $data['greater_blessing']['training_percent_change_week'] = .0056;
        $data['greater_blessing']['training_percent_change_year'] = -.0056;
        $data['greater_blessing']['grouping_percent_change_week'] = .0056;
        $data['greater_blessing']['grouping_percent_change_year'] = -.0056;

        /**********************************************************/
        /* GREATEST BLESSING */
        /**********************************************************/
        $data['greatest_blessing']['countries_last_week'] = '100';
        $data['greatest_blessing']['states_last_week'] = '200';
        $data['greatest_blessing']['counties_last_week'] = '500';

        $data['greatest_blessing']['countries_this_year'] = '100';
        $data['greatest_blessing']['states_this_year'] = '200';
        $data['greatest_blessing']['counties_this_year'] = '500';

        $data['greatest_blessing']['countries_in_the_world'] = '100';
        $data['greatest_blessing']['states_in_the_world'] = '200';
        $data['greatest_blessing']['counties_in_the_world'] = '500';

        $data['greatest_blessing']['countries_remaining'] = '120';
        $data['greatest_blessing']['states_remaining'] = '120';
        $data['greatest_blessing']['counties_remaining'] = '120';

        $data['greatest_blessing']['top_locations'] = [
            'United States',
            'Philippines',
            'Russia',
            'Spain',
            'Brazil'
        ];
        $data['greatest_blessing']['top_languages'] = [
            'English',
            'Arabic',
            'Russian',
            'Portuguese',
            'Slovenian'
        ];

        $data['greatest_blessing']['movement_reports'] = '39'; // movement reports submitted
        $data['greatest_blessing']['coaching_events'] = '245'; // coaching events
        $data['greatest_blessing']['top_countries_string'] = 'United States, Brazil, Mexico, and Slovenia';

        $data['greatest_blessing']['coaching_percent_change_week'] = .0056;
        $data['greatest_blessing']['coaching_percent_change_year'] = -.0056;
        $data['greatest_blessing']['reporting_percent_change_week'] = .0056;
        $data['greatest_blessing']['reporting_percent_change_year'] = -.0056;

        return $data;
    }

}
Movement_Shortcode_Stats_Snapshot::instance();
