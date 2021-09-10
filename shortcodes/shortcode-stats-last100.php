<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.

class Movement_Shortcode_Stats_Last100hours
{
    public $namespace = 'movement_maps_stats/v1/';
    public $token = 'last100hours_stats';
    public $ip_response;
    public static $languages;

    private static $_instance = null;
    public static function instance() {
        if (is_null( self::$_instance )) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        add_shortcode( $this->token, [ $this, 'short_code' ] );
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
        add_action( 'movement_maps_stats_shortcodes_list', [ $this, 'instructions_for_shortcode'] );
    }

    public function short_code( $atts ){

        // begin echo cache
        ob_start();
        ?>
        <script>

        </script>
        <!-- Title Section-->
        <div class="grid-x grid-padding-x deep-blue-section padding-vertical-1">
            <div class="cell center">
                <h1 class="center title">Status</h1>
            </div>
        </div>
        <div class="grid-x blue-notch-wrapper"><div class="cell center blue-notch"></div></div>
        <div class="grid-x grid-padding-x white-section">
            <div class="cell small-12">
                <div class="grid-x center">
                    <div class="cell small-1"></div>
                    <div class="cell medium-2">
                        <h3>Population</h3>
                        <hr>
                        <p>World population<br><span class="progress-counter" id="population-count-1">?</span><br></p>
                        <p>Births today<br><span class="progress-counter" id="births-today-count-1">?</span><br></p>
                        <p>Deaths today<br><span class="progress-counter" id="deaths-today-count-1">?</span><br></p>
                        <p>Population growth today<br><span class="progress-counter" id="population-growth-today-count-1">?</span><br></p>
                    </div>
                    <div class="cell medium-2">
                        <h3>Crisis</h3>
                        <hr>
                        <p>Born with no access <br>to the gospel today<br><span class="progress-counter" id="births-among-unreached-today-count-1">?</span></p>
                        <p>Christless deaths today<br><span class="progress-counter" id="christless-deaths-today-count-1">?</span></p>
                    </div>
                    <div class="cell medium-2">
                        <img src="<?php echo esc_url( plugin_dir_url( __DIR__ ) )?>images/jesus-globe.png" alt="welcome-graphic" />
                    </div>
                    <div class="cell medium-2">
                        <h3>Training Goal</h3>
                        <hr>
                        <p>Needed<br><span class="progress-counter" id="trainings-needed-count-1">?</span><br></p>
<!--                        <p>Reported<br><span class="progress-counter" id="trainings-reported-count-1">?</span></p>-->
                    </div>
                    <div class="cell medium-2">
                        <h3>Church Planting Goal</h3>
                        <hr>
                        <p>Needed<br>
                        <span class="progress-counter" id="churches-needed-count-1">?</span><br></p>
<!--                        <p>Reported<br><span class="progress-counter" id="churches-reported-count-1">?</span></p>-->
                    </div>
                    <div class="cell small-1"></div>
                </div>
            </div>
        </div>

        <div class="grid-x grid-padding-x deep-blue-section padding-vertical-1">
            <div class="cell center">
                <h1 class="center title">Last 100 Hours</h1>
            </div>
        </div>
        <div class="grid-x blue-notch-wrapper"><div class="cell center blue-notch"></div></div>

        <div class="grid-x grid-padding-x white-section">
            <div class="cell small-12">
                <div class="grid-x center grid-padding-x">
                    <div class="cell small-1"></div>
                    <div class="cell medium-2">
                        <h3>Recent Activity</h3>
                        <hr>
                        <p>Blessings<br><span class="progress-counter" id="blessing"><img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="50px" /></span><br></p>
                        <p>Great Blessings<br><span class="progress-counter" id="great_blessing"><img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="50px" /></span><br></p>
                        <p>Greater Blessings<br><span class="progress-counter" id="greater_blessing"><img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="50px" /></span><br></p>
                        <p>Greatest Blessings<br><span class="progress-counter" id="greatest_blessing"><img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="50px" /></span><br></p>
                    </div>
                    <div class="cell medium-2">
                        <h3>Recent Locations</h3>
                        <hr>
                        <p>Countries<br>
                            <span class="progress-counter" id="active_countries"><img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="50px" /></span>
<!--                            <span class="progress-counter">/</span>-->
<!--                            <span class="progress-counter" id="total_countries"><img src="--><?php //echo plugin_dir_url(__DIR__) ?><!--/spinner.svg" width="50px" /><br></p>-->
                        <p>
                            States<br>
                            <span class="progress-counter" id="active_states"><img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="50px" /></span>
<!--                            <span class="progress-counter">/</span>-->
<!--                            <span class="progress-counter" id="total_states"><img src="--><?php //echo plugin_dir_url(__DIR__) ?><!--/spinner.svg" width="50px" /></span><br>-->
                        </p>
                        <p>
                            Counties/Districts<br>
                            <span class="progress-counter" id="active_counties"><img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="50px" /></span>
<!--                            <span class="progress-counter">/</span>-->
<!--                            <span class="progress-counter" id="total_counties"><img src="--><?php //echo plugin_dir_url(__DIR__) ?><!--/spinner.svg" width="50px" /></span><br>-->
                        </p>
                    </div>
                    <div class="cell medium-2">
                        <img src="<?php echo esc_url( plugin_dir_url( __DIR__ ) )?>images/hero_with_clock.svg" alt="welcome-graphic" />
                    </div>
                    <div class="cell medium-2">
                        <h3>Top 10 Locations</h3>
                        <hr>
                        <p id="top_10_locations">
                            <img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="50px" />
                        </p>
                    </div>
                    <div class="cell medium-2">
                        <h3>Top 10 Languages</h3>
                        <hr>
                        <p id="top_10_languages">
                            <img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="50px" />
                        </p>
                    </div>

                    <div class="cell small-1"></div>
                </div>
            </div>
        </div>
        <script>
            /* <![CDATA[ */
            window.shortcode_metrics = [<?php echo json_encode($this->counter_object()) ?>][0]
            /* ]]> */
            jQuery(document).ready(function($){
                let stats = window.shortcode_metrics.status

                // World Population
                let pop = $('#population-count-1')
                pop.html(stats.counter[1].calculated_population_year.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                setInterval(function() { // births
                    stats.counter[1].calculated_population_year++;
                    pop.html(stats.counter[1].calculated_population_year.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                }, stats.counter[1].births_interval);
                setInterval(function() { // deaths
                    stats.counter[1].calculated_population_year--;
                    pop.html(stats.counter[1].calculated_population_year.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                }, stats.counter[1].deaths_interval);

                // deaths without Christ
                let dwc = $('#christless-deaths-today-count-1')
                dwc.html(stats.counter[1].christless_deaths_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                setInterval(function() {
                    stats.counter[1].christless_deaths_today++;
                    dwc.html(stats.counter[1].christless_deaths_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                }, stats.counter[1].christless_deaths_interval);

                let birth_unreached = $('#births-among-unreached-today-count-1')
                birth_unreached.html(stats.counter[1].births_among_unreached_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                setInterval(function() {
                    stats.counter[1].births_among_unreached_today++;
                    birth_unreached.html(stats.counter[1].births_among_unreached_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                }, stats.counter[1].births_among_unreached_interval);

                // Trainings
                let trainings = $('#trainings-needed-count-1')
                trainings.html(stats.counter[1].trainings_needed.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))

                // Churches
                let churches = $('#churches-needed-count-1')
                churches.html(stats.counter[1].churches_needed.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))

                // births today
                let births_today = $('#births-today-count-1')
                births_today.html(stats.counter[1].births_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                setInterval(function() { // births
                    stats.counter[1].births_today++;
                    births_today.html(stats.counter[1].births_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                }, stats.counter[1].births_interval);


                // deaths today
                let deaths_today = $('#deaths-today-count-1')
                deaths_today.html(stats.counter[1].deaths_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                setInterval(function() { // deaths
                    stats.counter[1].deaths_today++;
                    deaths_today.html(stats.counter[1].deaths_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                }, stats.counter[1].deaths_interval);

                // population growth today
                let pop_today = $('#population-growth-today-count-1')
                pop_today.html(stats.counter[1].calculated_population_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                setInterval(function() { // births
                    stats.counter[1].calculated_population_today++;
                    pop_today.html(stats.counter[1].calculated_population_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                }, stats.counter[1].births_interval);
                setInterval(function() { // deaths
                    stats.counter[1].calculated_population_today--;
                    pop_today.html(stats.counter[1].calculated_population_today.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                }, stats.counter[1].deaths_interval);

                $('#churches-reported-count-1').html(stats.counter[1].churches_reported)
                $('#trainings-reported-count-1').html(stats.counter[1].trainings_reported)

                // Progress
                $('#live-trainings-reported-count-1').html(stats.counter[1].trainings_reported)
                $('#live-churches-reported-count-1').html(stats.counter[1].churches_reported)


                /* LAST 100 HOURS SECTION*/
                let top_10_locations = $('#top_10_locations')
                let top_10_languages = $('#top_10_languages')
                let active_countries = $('#active_countries')
                let total_countries = $('#total_countries')
                let active_states = $('#active_states')
                let total_states = $('#total_states')
                let active_counties = $('#active_counties')
                let total_counties = $('#total_counties')
                let blessing = $('#blessing')
                let great_blessing = $('#great_blessing')
                let greater_blessing = $('#greater_blessing')
                let greatest_blessing = $('#greatest_blessing')

                function write_last_100(){
                    let obj = window.shortcode_metrics
                    makeRequest('POST', obj.settings.rest_url, {  }, obj.settings.rest_base_url )
                        .then(data => {
                            console.log(data)

                            // top_10_locations
                            top_10_locations.empty()
                            $.each( data.top_10_locations, function(i,v){
                                top_10_locations.append(`${v}<br>`)
                            })

                            top_10_languages.empty()
                            $.each( data.top_10_languages, function(i,v){
                                top_10_languages.append(`${v}<br>`)
                            })

                            active_countries.html(`${data.active_countries}`)
                            active_states.html(`${data.active_states}`)
                            active_counties.html(`${data.active_counties}`)

                            total_countries.html(`${data.total_countries}`)
                            total_states.html(`${data.total_states}`)
                            total_counties.html(`${data.total_counties}`)

                            blessing.html(`${data.blessing}`)
                            great_blessing.html(`${data.great_blessing}`)
                            greater_blessing.html(`${data.greater_blessing}`)
                            greatest_blessing.html(`${data.greatest_blessing}`)

                        })
                }
                write_last_100()
            })
        </script>
        <hr>
        <?php

        return ob_get_clean();
    }

    public function counter_object() : array {
        return [
            'translations' => [
                'title' => '',
            ],
            'settings' => [
                'map_key' => DT_Mapbox_API::get_key(),
                'rest_url' => $this->token,
                'rest_base_url' => $this->namespace,
            ],
            'status' => $this->status(),
        ];
    }

    public function status() : array {
        /**
         * Project variables
         * Zúme goals are 1 training for every 5,000 and 2 churches for every 5,000
         */
        $trainings_per_population_ceiling = 5000;
        $churches_per_population_ceiling = 2500;

        // world variables
        $world_population = 7543334085; // world population estimate at Jan 1, 2019 @link https://www.census.gov/newsroom/press-releases/2019/new-years-population.html
        $world_population_timestamp = 1546300800; // unix seconds at Jan 1, 2019
        $current_timestamp = time(); // unix time stamp for right now
        $births_per_second = 4.3;
        $deaths_per_second = 1.8;
        $christless_deaths_per_second = 1.2384; // 68.8 percent of the population is not Christian
        $births_among_unreached_per_second = 1.8146; // 42.2 percent of the population is unreached
        $births_millisecond_interval = 1000 / $births_per_second;
        $deaths_millisecond_interval = 1000 / $deaths_per_second;
        $christless_deaths_millisecond_interval = 1000 / $christless_deaths_per_second;
        $births_among_unreached_interval = 1000 / $births_among_unreached_per_second;

        $seconds_since_world_population_timestamp = $current_timestamp - $world_population_timestamp;

        $calculated_population = ceil( ( $births_per_second * $seconds_since_world_population_timestamp ) + $world_population - ( $deaths_per_second * $seconds_since_world_population_timestamp ) );
        $trainings_per_population = ceil( $calculated_population / $trainings_per_population_ceiling );
        $churches_per_population = ceil( $calculated_population / $churches_per_population_ceiling );

        // today
        $seconds_since_midnight = $current_timestamp - strtotime( 'midnight' );
        $births_today = ceil( $births_per_second * $seconds_since_midnight );
        $deaths_today = ceil( $deaths_per_second * $seconds_since_midnight );
        $christless_deaths_today = ceil( $christless_deaths_per_second * $seconds_since_midnight );
        $births_among_unreached_today = ceil( $births_among_unreached_per_second * $seconds_since_midnight );

        $calculated_population_today = ceil( $births_today - $deaths_today );

        return [
            'time' => time(),
            'counter' => [
                1 => [
                    'calculated_population_year' => $calculated_population,
                    'calculated_population_today' => $calculated_population_today,
                    'births_today' => $births_today,
                    'deaths_today' => $deaths_today,
                    'christless_deaths_today' => $christless_deaths_today,
                    'christless_deaths_interval' => $christless_deaths_millisecond_interval,
                    'births_among_unreached_today' => $births_among_unreached_today,
                    'births_among_unreached_interval' => $births_among_unreached_interval,
                    'births_interval' => $births_millisecond_interval,
                    'deaths_interval' => $deaths_millisecond_interval,
                    'trainings_needed' => $trainings_per_population,
                    'churches_needed' => $churches_per_population,
                ]
            ]
        ];
    }
    /**
     * Sources
     * @link https://data.worldbank.org/indicator/SP.DYN.CDRT.IN (death)
     * @link https://data.worldbank.org/indicator/SP.DYN.CBRT.IN (birth)
     * @link https://www.rapidtables.com/calc/time/seconds-in-year.html ( seconds in a year 31556952 )
     * @link https://www.census.gov/popclock/
     * @link https://www.pioneerseurope.org/en/Stories/Unreached-Peoples unreached peoples 40%
     */



    public function add_api_routes() {
        register_rest_route(
            $this->namespace, '/'.$this->token, [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'last100'],
                ],
            ]
        );
    }

    public function last100() : array {
        global $wpdb;
        $data = [
            'blessing' => 0,
            'great_blessing' => 0,
            'greater_blessing' => 0,
            'greatest_blessing' => 0,
            'total_countries' => '256',
            'active_countries' => 0,
            'total_states' => '3,612',
            'active_states' => 0,
            'total_counties' => '45,960',
            'active_counties' => 0,
            'top_10_locations' => [],
            'top_10_languages' => []
        ];


        $timestamp = strtotime('-100 hours' );
        $move_log = $wpdb->get_results( $wpdb->prepare( "
                SELECT count( DISTINCT(g.admin0_grid_id) ) as countries, count( DISTINCT(g.admin1_grid_id) ) as states, count( DISTINCT(g.admin2_grid_id) ) as counties
                FROM $wpdb->dt_movement_log  as ml
                JOIN $wpdb->dt_location_grid as g ON g.grid_id=ml.grid_id
                WHERE timestamp > %s ORDER BY timestamp DESC
                ", $timestamp ), ARRAY_A );
        if ( ! empty( $move_log ) ) {
            $data['active_countries'] = $move_log[0]['countries'] ?? 0;
            $data['active_states'] = $move_log[0]['states'] ?? 0;
            $data['active_counties'] = $move_log[0]['counties'] ?? 0;
        }



        $results = $wpdb->get_results( $wpdb->prepare( "
                SELECT action, category, lng, lat, label, payload, timestamp FROM $wpdb->dt_movement_log WHERE timestamp > %s ORDER BY timestamp DESC
                ", $timestamp ), ARRAY_A );
        foreach ( $results as $result ){
            $category = $result['category'];
            $action = $result['action'];
            $initials = '';
            $in_language = '';
            $location_label = '';
            $payload = maybe_unserialize($result['payload']);
            $note = Movement_Shortcode_Utilities::create_note_data( $category, $action, $initials, $in_language, $location_label, $payload );

            // count blessing type
            $data[$note['type']]++;
        }

        $language = [];
        foreach( $results as $result ){
            $payload = maybe_unserialize($result['payload']);
            if ( ! isset( $payload['language_code'] ) ) {
                continue;
            }
            if ( ! isset( $payload['language_name'] ) ) {
                continue;
            }
            if ( ! isset( $language[$payload['language_name']] ) ) {
                $language[$payload['language_name']] = 0;
            }

            $language[$payload['language_name']]++;
        }
        arsort($language);
        $data['top_10_languages'] = array_keys( array_slice($language, 0, 10) );

        $top_locations = $wpdb->get_results( $wpdb->prepare( "
                SELECT c.name, count(c.name) as count
                FROM $wpdb->dt_movement_log as ml
                JOIN $wpdb->dt_location_grid as g ON g.grid_id=ml.grid_id
                LEFT JOIN $wpdb->dt_location_grid as c ON g.admin0_grid_id=c.grid_id
                WHERE timestamp > %s
                GROUP BY c.name
                ORDER BY count DESC
                LIMIT 10;
                ", $timestamp ), ARRAY_A );
        foreach( $top_locations as $location ){
            $data['top_10_locations'][] = $location['name'];
        }

        return $data;
    }



    public function instructions_for_shortcode(){
        ?>
        <p>
            Last 100 Stats<br>
            <code>[<?php echo $this->token ?>]</code><br>
            Add this to a page in the website and set template to empty container (full-width, no styling except header and footer.)
        </p>
        <?php
    }

}
Movement_Shortcode_Stats_Last100hours::instance();
