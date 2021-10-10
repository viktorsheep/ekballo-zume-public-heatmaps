<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

if ( strpos( dt_get_url_path(), 'zume_app' ) !== false || dt_is_rest() ){
    Zume_Public_Heatmap_100hours::instance();
}

add_filter('dt_network_dashboard_supported_public_links', function( $supported_links ){
    $supported_links[] = [
        'name' => 'Public Heatmap ( 100 Hours )',
        'description' => 'Last 100 hours of Zúme activity.',
        'key' => 'zume_app_last_100_hours',
        'url' => 'zume_app/last_100_hours'
    ];
    return $supported_links;
}, 10, 1 );


/**
 * Class Disciple_Tools_Plugin_Starter_Template_Magic_Link
 */
class Zume_Public_Heatmap_100hours extends DT_Magic_Url_Base {

    public $magic = false;
    public $parts = false;
    public $page_title = 'Last 100 Hours';
    public $root = "zume_app";
    public $type = 'last_100_hours';
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

        /**
         * post type and module section
         */
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
        if ( !$this->check_parts_match( false ) ){
            return;
        }

        // require classes
        if ( ! class_exists( 'DT_Ipstack_API' ) ) {
            require_once( trailingslashit( get_theme_file_path() ) . 'dt-mapping/geocode-api/ipstack-api.php' );
        }
        if ( ! class_exists( 'DT_Mapbox_API' ) ) {
            require_once( trailingslashit( get_theme_file_path() ) . 'dt-mapping/geocode-api/mapbox-api.php' );
        }

        // remove header notification
        remove_action( 'wp_head', 'dt_release_modal' );

        // load if valid url
        add_action( 'dt_blank_body', [ $this, 'body' ] ); // body for no post key
        add_filter( 'dt_magic_url_base_allowed_css', [ $this, 'dt_magic_url_base_allowed_css' ], 10, 1 );
        add_filter( 'dt_magic_url_base_allowed_js', [ $this, 'dt_magic_url_base_allowed_js' ], 10, 1 );

    }

    public function dt_magic_url_base_allowed_js( $allowed_js ) {
        $allowed_js[] = 'jquery-cookie';
        $allowed_js[] = 'mapbox-cookie';
        $allowed_js[] = 'mapbox-gl';
        return $allowed_js;
    }

    public function dt_magic_url_base_allowed_css( $allowed_css ) {
        $allowed_css[] = 'mapbox-gl-css';
        return $allowed_css;
    }

    /**
     * Writes custom styles to header
     *
     * @see DT_Magic_Url_Base()->header_style() for default state
     */
    public function header_style(){
        ?>
        <style>
            body {
                background-color: white;
                padding: 0;
            }
        </style>
        <?php
    }

    /**
     * Writes javascript to the header
     *
     * @see DT_Magic_Url_Base()->header_javascript() for default state
     * @todo remove if not needed
     */
    public function header_javascript(){
    }

    /**
     * Writes javascript to the footer
     *
     * @see DT_Magic_Url_Base()->footer_javascript() for default state
     * @todo remove if not needed
     */
    public function footer_javascript(){
        ?>
        <script>
            let jsObject = [<?php echo json_encode([
                'map_key' => DT_Mapbox_API::get_key(),
                'root' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'parts' => $this->parts,
                'translations' => [
                    'add' => __( 'Add Magic', 'disciple-tools-plugin-starter-template' ),
                ],
            ]) ?>][0]
        </script>
        <?php
        return true;
    }

    public function body(){
        DT_Mapbox_API::geocoder_scripts();

        // set timezone info
        // Expects to be installed in a theme like Zume.Vision that has a full copy of the dt-mapping folder from Disciple Tools.
        $ipstack = new DT_Ipstack_API();
        $ip_address = $ipstack::get_real_ip_address();
        $this->ip_response = $ipstack::geocode_ip_address($ip_address);

        // begin echo cache
        ?>
        <script>
            /* <![CDATA[ */
            window.dt_mapbox_metrics = [<?php echo json_encode([
                'translations' => [
                    'title' => __( "Last 100 Hours of Zúme", "disciple_tools" ),
                ],
                'settings' => [
                    'map_key' => DT_Mapbox_API::get_key(),
                    'points_rest_url' => '/'.$this->type,
                    'points_rest_base_url' => $this->root . '/v1',
                ]
            ]) ?>][0]
            /* ]]> */
        </script>
        <style>
            /**
            Custom Styles
             */
            .blessing {
                background-color: #21336A;
                border: 1px solid white;
                color: white;
                font-weight: bold;
                margin:0;
            }
            .great-blessing {
                background-color: #2CACE2;
                border: 1px solid white;
                color: white;
                font-weight: bold;
                margin:0;
            }
            .greater-blessing {
                background-color: #FAEA38;
                border: 1px solid white;
                color: #21336A;
                font-weight: bold;
                margin:0;
            }
            .greatest-blessing {
                background-color: #90C741;
                border: 1px solid white;
                color: white;
                font-weight: bold;
                margin:0;
            }
            .blessing:hover {
                border: 1px solid #21336A;
            }
            .great-blessing:hover {
                border: 1px solid #21336A;
                background-color: #2CACE2;
            }
            .greater-blessing:hover {
                border: 1px solid #21336A;
                background-color: #FAEA38;
                color: #21336A;
            }
            .greatest-blessing:hover {
                border: 1px solid #21336A;
                background-color: #90C741;
            }
            .filtered {
                background-color: lightgrey;
                color: white;
            }
            .filtered:hover {
                background-color: lightgrey;
                border: 1px solid #21336A;
                color: white;
            }
            #activity-list {
                font-size:.7em;
                list-style-type:none;
            }
            #map-loader {
                position: absolute;
                top:40%;
                left:50%;
                z-index: 20;
            }
            #map-header {
                position: absolute;
                top:10px;
                left:10px;
                z-index: 20;
                background-color: white;
                padding:1em;
                opacity: 0.8;
                border-radius: 5px;
            }
            .center-caption {
                font-size:.8em;
                text-align:center;
                color:darkgray;
            }
            .caption {
                font-size:.8em;
                color:darkgray;
                padding-bottom:1em;
            }
        </style>

        <div class="grid-x">
            <div class="cell medium-8">
                <div id="dynamic-styles"></div>
                <div id="map-wrapper">
                    <div id='map'></div>
                    <div id="map-loader" class="spinner-loader"><img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="100px" /></div>
                    <div id="map-header"><h3>Last 100 Hours of Zúme</h3></div>
                </div>
            </div>
            <div class="cell medium-4" style="padding: .5rem;">
                <div class="grid-x grid-padding-x">
                    <div class="cell medium-6">
                        <!-- Blessing Buttons-->
                        <button class="button expanded greatest-blessing" id="greatest-blessing-button">Greatest Blessing (<span class="greatest-blessing-count">-</span>)</button>
                        <button class="button expanded greater-blessing" id="greater-blessing-button">Greater Blessing (<span class="greater-blessing-count">-</span>)</button>
                        <button class="button expanded great-blessing" id="great-blessing-button">Great Blessing (<span class="great-blessing-count">-</span>)</button>
                        <button class="button expanded blessing" id="blessing-button">Blessing (<span class="blessing-count">-</span>)</button>

                        <!-- Learn More Modal-->
                        <div class="center-caption"><a href="javascript:void(0)" onclick="open_great_blessing()">what's this?</a></div>
                        <div class="large reveal" id="blessing-modal" data-reveal data-v-offset="10px">
                            <h2>Great, Greater, Greatest Blessings</h2>
                            <hr>
                            <div class="grid-x grid-padding-x">
                                <div class="cell medium-6">
                                    <p>Our map is filtered by a concept we call <a href="https://zume.training/vision-casting-the-greatest-blessing/">the great, greater, and greatest blessings.</a></p>
                                    <p>It goes like this: <b>"It is a blessing to follow Jesus. It is a great blessing to lead others to follow Jesus. It is a greater blessing to start a new spiritual family. It is the greatest blessing to equip others to start new spiritual families."</b></p>
                                </div>
                                <div class="cell medium-6">
                                    <p id="video-holder"></p>
                                </div>
                            </div>
                            <table class="unstriped">
                                <thead>
                                <tr>
                                    <th style="width:250px;"></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <img src="<?php echo esc_url( plugin_dir_url(__DIR__) ) . '/images/blessing.jpg' ?>" style="height:200px;" alt="blessing" />
                                    </td>
                                    <td><h4>Blessing</h4></td>
                                    <td>
                                        <strong>(i.e. Knowing Jesus Better)</strong><br>
                                        being baptized, studying Jesus' Great Commission, committing to obey a word from the Spirit
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="<?php echo esc_url( plugin_dir_url(__DIR__) ) . '/images/great-blessing.jpg' ?>" style="height:200px;" alt="great-blessing" />
                                    </td>
                                    <td><h4>Great Blessing</h4></td>
                                    <td>
                                        <strong>(i.e. Helping Others Know Jesus)</strong><br>
                                        sharing Jesus, prayer walking, discipling someone to follow Jesus
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="<?php echo esc_url( plugin_dir_url(__DIR__) ) . '/images/greater-blessing.jpg' ?>" style="height:200px;" alt="greater-blessing" />
                                    </td>
                                    <td><h4>Greater Blessing</h4></td>
                                    <td>
                                        <strong>(i.e. Starting Spiritual Families)</strong><br>
                                        launching, building or leading: discovery bible studies, home churches, a DMM training groups
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="<?php echo esc_url( plugin_dir_url(__DIR__) ) . '/images/greatest-blessing.jpg' ?>" style="width:250px;" alt="greatest-blessing" />
                                    </td>
                                    <td style="white-space: nowrap;"><h4>Greatest Blessing</h4></td>
                                    <td>
                                        <strong>(i.e. Helping Others Start Spiritual Families)</strong><br>
                                        coaching someone to: lead DBS, home church, or training group; coaching someone in disciple multiplication; joining peers to labor for a multiplying movement
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <br><br><br>
                            <button class="close-button" data-close aria-label="Close modal" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <script>
                            function open_great_blessing(){
                                jQuery('#video-holder').html(`<iframe src="https://player.vimeo.com/video/247064323" width="350" height="200" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`)
                                jQuery('#blessing-modal').foundation('open')
                            }
                        </script>
                        <!-- End Learn More -->

                    </div>
                    <div class="cell medium-6">
                        Timezone (<a href="javascript:void(0)" data-open="timezone-changer" id="timezone-current"><?php echo esc_html( $this->ip_response['time_zone']['id'] ?? 'America/Denver' ) ?></a>)
                        <!-- Reveal Modal Timezone Changer-->
                        <div id="timezone-changer" class="reveal tiny" data-reveal>
                            <h2>Change your timezone:</h2>
                            <select id="timezone-select">
                                <?php
                                $selected_tz = $this->ip_response['time_zone']['id'];
                                if ( ! empty( $selected_tz ) ) {
                                    echo '<option value="'.esc_html( $selected_tz ).'" selected>'.esc_html( $selected_tz ).'</option><option disabled>----</option>';
                                }
                                $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                foreach( $tzlist as $tz ) {
                                    echo '<option value="'.esc_html( $tz ).'">'.esc_html( $tz ).'</option>';
                                }
                                ?>
                            </select>
                            <button class="close-button" data-close aria-label="Close modal" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <hr>
                        <p>
                            <select name="country" id="country-dropdown">
                                <option value="none">Filter by Country</option>
                            </select>
                        </p>
                        <div>
                            <select name="language" id="language-dropdown">
                                <option value="none">Filter by Language</option>
                            </select>
                        </div>
                        <div class="center-caption" style="text-align:center;"><a href="javascript:void(0);" onclick="location.reload();">reset filters</a></div>

                    </div>
                </div>
                <hr>

                <!-- Security disclaimer -->
                <div class="caption">For identity protection, names and locations are obfuscated. <a href="javascript:void(0);" data-open="security">what's this</a></div>
                <div id="security" class="large reveal" data-reveal >
                    <h2>Obfuscating Names and Locations</h2>
                    <hr>
                    <p>
                        Our map is made public for two purposes: (1) <b>encouragement</b> of the movement community, and (2) feeding <b>prayer</b> efforts with real-time prayer points.
                        We realize both encouragement and prayer do not need exact names and exact addresses. Beyond this security and protection of identity are essential.
                    </p>
                    <p>
                        For this reason we obfuscate names and locations, so security is protected, but prayer efforts can feel confident and connected to the kingdom steps listed.
                    </p>
                    <hr>
                    <div class="grid-x grid-padding-x">
                        <div class="cell medium-6">
                            <h3>Alias Facts:</h3>
                            <ul>
                                <li>These initials do not correspond to the actual first and last name of the person doing the action. No initials used are personally identifiable.</li>
                                <li>An algorithm is used to consistently generate the same alias for the same person, but with letters that do not correspond to their actual name.</li>
                            </ul>
                        </div>
                        <div class="cell medium-6">
                            <h3>Location Facts:</h3>
                            <ul>
                                <li>These are not personally identifiable locations.</li>
                                <li>Accuracy of locations have be reduced to between 11 kilometers to 111 kilometers, depending on the security level of the country.</li>
                                <li>Countries that are known to be hostile towards Christians are obfuscated most. (<a href="https://www.opendoorsusa.org/christian-persecution/world-watch-list/">Top Countries</a>)</li>
                            </ul>
                        </div>
                    </div>
                    <button class="close-button" data-close aria-label="Close modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="list-loader" class="spinner-loader"><img src="<?php echo plugin_dir_url(__DIR__) ?>/spinner.svg" width="50px" /> </div>
                <!-- Activity List -->
                <div id="activity-wrapper">
                    <ul id="activity-list"></ul>
                </div>

            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {

                // console.log(dt_mapbox_metrics)
                function write_all_points( ) {

                    let blessing_button = jQuery('#blessing-button')
                    let great_blessing_button = jQuery('#great-blessing-button')
                    let greater_blessing_button = jQuery('#greater-blessing-button')
                    let greatest_blessing_button = jQuery('#greatest-blessing-button')
                    let country_dropdown = jQuery('#country-dropdown')
                    let language_dropdown = jQuery('#language-dropdown')

                    window.blessing = 'visible'
                    window.great_blessing = 'visible'
                    window.greater_blessing = 'visible'
                    window.greatest_blessing = 'visible'

                    window.refresh_timer = ''
                    window.timer_limit = 0
                    function set_timer() {
                        clear_timer()
                        if ( window.timer_limit > 30 ){
                            if ( jQuery('#live-data-warning').length < 1 ){
                                jQuery('#activity-wrapper').prepend(`<span id="live-data-warning" class="caption">Refresh limit reached. Refresh the page to restart live data.</span>`)
                            }
                            return
                        }
                        window.refresh_timer = setTimeout(function(){
                            get_points( )
                            window.timer_limit++
                        }, 10000);
                    }
                    function clear_timer() {
                        clearTimeout(window.refresh_timer)
                    }

                    let obj = window.dt_mapbox_metrics
                    let tz_select = jQuery('#timezone-select')

                    let dynamic_styles = jQuery('#dynamic-styles')
                    dynamic_styles.empty().html(`
                            <style>
                                #map-wrapper {
                                    height: ${window.innerHeight}px !important;
                                    position:relative;
                                }
                                #map {
                                    height: ${window.innerHeight}px !important;
                                }
                                #activity-wrapper {
                                    height: ${window.innerHeight - 300}px !important;
                                    overflow: scroll;
                                }
                            </style>
                         `)

                    mapboxgl.accessToken = obj.settings.map_key;
                    var map = new mapboxgl.Map({
                        container: 'map',
                        style: 'mapbox://styles/mapbox/light-v10',
                        center: [10, 30],
                        minZoom: 1,
                        maxZoom: 8,
                        zoom: 3
                    });

                    // disable map rotation using right click + drag
                    map.dragRotate.disable();
                    map.touchZoomRotate.disableRotation();

                    // load sources
                    map.on('load', function () {
                        window.selected_language = 'none'
                        window.selected_country = 'none'
                        get_points()
                    })
                    map.on('zoomstart', function(){
                        clear_timer()
                    })
                    map.on('zoomend', function(){
                        set_timer()
                    })
                    map.on('dragstart', function(){
                        clear_timer()
                    })
                    map.on('dragend', function(){
                        set_timer()
                    })

                    tz_select.on('change', function() {
                        let tz = tz_select.val()
                        get_points( tz )

                        jQuery('#timezone-changer').foundation('close');
                        jQuery('#timezone-current').html(tz);
                    })

                    function get_points( tz ) {
                        if ( ! tz ) {
                            tz = tz_select.val()
                        }
                        makeRequest('POST', obj.settings.points_rest_url, { timezone_offset: tz, country: window.selected_country, language: window.selected_language }, obj.settings.points_rest_base_url )
                            .then(points => {

                                // load drop downs and list
                                load_countries_dropdown( points )
                                load_languages_dropdown( points )
                                load_list( points )

                                // check if map needs updating.
                                if ( window.geojson_hash === points.hash ){
                                    return;
                                }
                                window.geojson_hash = points.hash

                                // load map data
                                var mapSource= map.getSource('pointsSource');
                                if(typeof mapSource === 'undefined') {
                                    load_layer( points )
                                } else {
                                    map.getSource('pointsSource').setData(points);
                                }
                                jQuery('#map-loader').hide()

                                // fly to boundaries
                                var bounds = new mapboxgl.LngLatBounds();
                                points.features.forEach(function(feature) {
                                    bounds.extend(feature.geometry.coordinates);
                                });
                                if ( window.geojson_bounds !== bounds ){
                                    map.fitBounds(bounds);

                                }
                            })
                        set_timer()
                    }

                    function load_layer( points ) {
                        var blessing = map.getLayer('blessing');
                        if(typeof blessing !== 'undefined') {
                            map.removeLayer( 'blessing' )
                        }
                        var greatBlessing = map.getLayer('greatBlessing');
                        if(typeof greatBlessing !== 'undefined') {
                            map.removeLayer( 'greatBlessing' )
                        }
                        var greaterBlessing = map.getLayer('greaterBlessing');
                        if(typeof greaterBlessing !== 'undefined') {
                            map.removeLayer( 'greaterBlessing' )
                        }
                        var greatestBlessing = map.getLayer('greatestBlessing');
                        if(typeof greatestBlessing !== 'undefined') {
                            map.removeLayer( 'greatestBlessing' )
                        }
                        var mapSource= map.getSource('pointsSource');
                        if(typeof mapSource !== 'undefined') {
                            map.removeSource( 'pointsSource' )
                        }
                        map.addSource('pointsSource', {
                            'type': 'geojson',
                            'data': points
                        });
                        map.addLayer({
                            id: 'blessing',
                            type: 'circle',
                            source: 'pointsSource',
                            paint: {
                                'circle-radius': {
                                    'base': 4,
                                    'stops': [
                                        [3, 4],
                                        [4, 6],
                                        [5, 8],
                                        [6, 10],
                                        [7, 12],
                                        [8, 14],
                                    ]
                                },
                                'circle-color': '#21336A'
                            },
                            filter: ["==", "type", "blessing" ]
                        });
                        map.setLayoutProperty('blessing', 'visibility', window.blessing);

                        map.addLayer({
                            id: 'greatBlessing',
                            type: 'circle',
                            source: 'pointsSource',
                            paint: {
                                'circle-radius': {
                                    'base': 6,
                                    'stops': [
                                        [3, 6],
                                        [4, 8],
                                        [5, 10],
                                        [6, 12],
                                        [7, 14],
                                        [8, 16],
                                    ]
                                },
                                'circle-color': '#2CACE2'
                            },
                            filter: ["==", "type", "great_blessing" ]
                        });
                        map.setLayoutProperty('greatBlessing', 'visibility', window.great_blessing);

                        map.addLayer({
                            id: 'greaterBlessing',
                            type: 'circle',
                            source: 'pointsSource',
                            paint: {
                                'circle-radius': {
                                    'base': 8,
                                    'stops': [
                                        [3, 8],
                                        [4, 12],
                                        [5, 16],
                                        [6, 20],
                                        [7, 22],
                                        [8, 22],
                                    ]
                                },
                                'circle-color': '#FAEA38'
                            },
                            filter: ["==", "type", "greater_blessing" ]
                        });
                        map.setLayoutProperty('greaterBlessing', 'visibility', window.greater_blessing);

                        map.addLayer({
                            id: 'greatestBlessing',
                            type: 'circle',
                            source: 'pointsSource',
                            paint: {
                                'circle-radius': {
                                    'base': 10,
                                    'stops': [
                                        [3, 10],
                                        [4, 14],
                                        [5, 18],
                                        [6, 22],
                                        [7, 22],
                                        [8, 22],
                                    ]
                                },
                                'circle-color': '#90C741'
                            },
                            filter: ["==", "type", "greatest_blessing" ]
                        });
                        map.setLayoutProperty('greatestBlessing', 'visibility', window.greatest_blessing);

                        // @link https://docs.mapbox.com/mapbox-gl-js/example/popup-on-hover/
                        var popup = new mapboxgl.Popup({
                            closeButton: false,
                            closeOnClick: false
                        });

                        map.on('mouseenter', 'blessing', function (e) {
                            mouse_enter( e )
                        });
                        map.on('mouseleave', 'blessing', function (e) {
                            mouse_leave( e )
                        });
                        map.on('mouseenter', 'greatBlessing', function (e) {
                            mouse_enter( e )
                        });
                        map.on('mouseleave', 'greatBlessing', function (e) {
                            mouse_leave( e )
                        });
                        map.on('mouseenter', 'greaterBlessing', function (e) {
                            mouse_enter( e )
                        });
                        map.on('mouseleave', 'greaterBlessing', function (e) {
                            mouse_leave( e )
                        });
                        map.on('mouseenter', 'greatestBlessing', function (e) {
                            mouse_enter( e )
                        });
                        map.on('mouseleave', 'greatestBlessing', function (e) {
                            mouse_leave( e )
                        });

                        function mouse_enter( e ) {
                            map.getCanvas().style.cursor = 'pointer';

                            var coordinates = e.features[0].geometry.coordinates.slice();
                            var description = e.features[0].properties.note;

                            while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                                coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                            }

                            popup
                                .setLngLat(coordinates)
                                .setHTML(description)
                                .addTo(map);
                        }
                        function mouse_leave( e ) {
                            map.getCanvas().style.cursor = '';
                            popup.remove();
                        }

                        jQuery('#map-loader').hide()
                    }

                    function load_list( points ) {
                        let list_container = jQuery('#activity-list')
                        list_container.empty()
                        let filter_blessing = blessing_button.hasClass('filtered')
                        let filter_great_blessing = great_blessing_button.hasClass('filtered')
                        let filter_greater_blessing = greater_blessing_button.hasClass('filtered')
                        let filter_greatest_blessing = greatest_blessing_button.hasClass('filtered')
                        jQuery.each( points.features, function(i,v){
                            let visible = 'block'
                            if ( 'blessing' === v.properties.type && filter_blessing ) {
                                visible = 'none'
                            }
                            if ( 'great_blessing' === v.properties.type && filter_great_blessing ) {
                                visible = 'none'
                            }
                            if ( 'greater_blessing' === v.properties.type && filter_greater_blessing ) {
                                visible = 'none'
                            }
                            if ( 'greatest_blessing' === v.properties.type && filter_greatest_blessing ) {
                                visible = 'none'
                            }
                            if ( window.selected_country !== 'none' && window.selected_country !== v.properties.country ) {
                                visible = 'none'
                            }
                            if ( window.selected_language !== 'none' && window.selected_language !== v.properties.language ) {
                                visible = 'none'
                            }

                            if ( v.properties.note ) {
                                list_container.append(`<li class="${v.properties.type}-activity ${v.properties.country}-item ${v.properties.language}-item" style="display:${visible}"><strong>${v.properties.time}</strong> - ${v.properties.note}</li>`)
                            }
                        })
                        jQuery('#list-loader').hide()

                        jQuery('.blessing-count').empty().append(points.counts.blessing)
                        jQuery('.great-blessing-count').empty().append(points.counts.great_blessing)
                        jQuery('.greater-blessing-count').empty().append(points.counts.greater_blessing)
                        jQuery('.greatest-blessing-count').empty().append(points.counts.greatest_blessing)

                    }

                    function load_countries_dropdown( points ) {
                        window.selected_country = country_dropdown.val()
                        country_dropdown.empty()

                        let add_selected = ''
                        country_dropdown.append(`<option value="none">Filter by Country</option>`)
                        country_dropdown.append(`<option value="none">Clear</option>`)
                        country_dropdown.append(`<option disabled>---</option>`)
                        jQuery.each(points.countries, function(i,v){
                            add_selected = ''
                            if ( v === window.selected_country ) {
                                add_selected = ' selected'
                            }
                            country_dropdown.append(`<option value="${i}" ${add_selected}>${v}</option>`)
                        })
                    }
                    function load_languages_dropdown( points ) {
                        window.selected_language = language_dropdown.val()
                        language_dropdown.empty()

                        let add_selected = ''
                        language_dropdown.append(`<option value="none">Filter by Language</option>`)
                        language_dropdown.append(`<option value="none">Clear</option>`)
                        language_dropdown.append(`<option disabled>---</option>`)
                        jQuery.each(points.languages, function(i,v){
                            add_selected = ''
                            if ( i === window.selected_language ) {
                                add_selected = ' selected'
                            }
                            language_dropdown.append(`<option value="${i}" ${add_selected}>${v}</option>`)
                        })
                    }

                    // Filter button controls
                    blessing_button.on('click', function(){
                        if ( blessing_button.hasClass('filtered') ) {
                            blessing_button.removeClass('filtered')
                            jQuery('.blessing-activity').show()
                            window.blessing = 'visible'
                            map.setLayoutProperty('blessing', 'visibility', 'visible');
                        } else {
                            blessing_button.addClass('filtered')
                            jQuery('.blessing-activity').hide()
                            window.blessing = 'none'
                            map.setLayoutProperty('blessing', 'visibility', 'none');
                        }
                    })
                    great_blessing_button.on('click', function(){
                        if ( great_blessing_button.hasClass('filtered') ) {
                            great_blessing_button.removeClass('filtered')
                            jQuery('.great_blessing-activity').show()
                            window.great_blessing = 'visible'
                            map.setLayoutProperty('greatBlessing', 'visibility', 'visible');
                        } else {
                            great_blessing_button.addClass('filtered')
                            jQuery('.great_blessing-activity').hide()
                            window.great_blessing = 'none'
                            map.setLayoutProperty('greatBlessing', 'visibility', 'none');
                        }
                    })
                    greater_blessing_button.on('click', function(){
                        if ( greater_blessing_button.hasClass('filtered') ) {
                            greater_blessing_button.removeClass('filtered')
                            jQuery('.greater_blessing-activity').show()
                            window.greater_blessing = 'visible'
                            map.setLayoutProperty('greaterBlessing', 'visibility', 'visible');
                        } else {
                            greater_blessing_button.addClass('filtered')
                            jQuery('.greater_blessing-activity').hide()
                            window.greater_blessing = 'none'
                            map.setLayoutProperty('greaterBlessing', 'visibility', 'none');
                        }
                    })
                    greatest_blessing_button.on('click', function(){
                        if ( greatest_blessing_button.hasClass('filtered') ) {
                            greatest_blessing_button.removeClass('filtered')
                            jQuery('.greatest_blessing-activity').show()
                            window.greatest_blessing = 'visible'
                            map.setLayoutProperty('greatestBlessing', 'visibility', 'visible');
                        } else {
                            greatest_blessing_button.addClass('filtered')
                            jQuery('.greatest_blessing-activity').hide()
                            window.greatest_blessing = 'none'
                            map.setLayoutProperty('greatestBlessing', 'visibility', 'none');
                        }
                    })
                    country_dropdown.on('change', function(){
                        clear_timer()
                        window.selected_country = country_dropdown.val()
                        window.selected_language = language_dropdown.val()
                        jQuery('#map-loader').show()
                        jQuery('#list-loader').show()
                        let tz = tz_select.val()
                        get_points( tz )
                    })
                    language_dropdown.on('change', function(){
                        clear_timer()
                        window.selected_country = country_dropdown.val()
                        window.selected_language = language_dropdown.val()
                        jQuery('#map-loader').show()
                        jQuery('#list-loader').show()
                        let tz = tz_select.val()
                        get_points( tz )
                    })

                }
                write_all_points()
            })
        </script>
        <?php
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
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'points_geojson'],
                    'permission_callback' => function( WP_REST_Request $request ){
                        return true;
                    },
                ],
            ]
        );
    }

    public function points_geojson( WP_REST_Request $request ) {
        $params = $request->get_json_params() ?? $request->get_body_params();
        if ( isset( $params['timezone_offset'] ) && ! empty( $params['timezone_offset']  ) ) {
            $tz_name = sanitize_text_field( wp_unslash($params['timezone_offset'] ));
        } else {
            $tz_name = 'America/Denver';
        }
        $country = 'none';
        if ( isset( $params['country'] ) && ! empty( $params['country'] )) {
            $country = sanitize_text_field( wp_unslash( $params['country'] ) );
        }
        $language = 'none';
        if ( isset( $params['language'] ) && ! empty( $params['language'] )) {
            $language = sanitize_text_field( wp_unslash( $params['language'] ) );
        }

        return Zume_Public_Heatmap_100hours_Utilities::query_contacts_points_geojson( $tz_name, $country, $language );
    }

}



class Zume_Public_Heatmap_100hours_Utilities {

    public static function create_initials( $longitude, $latitude, $payload ) : string {
        $letters = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'N', 'S',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'D', 'E',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'E', 'A', 'R', 'I', 'T', 'N', 'S', 'L', 'E', 'A', 'R', 'I', 'N', 'S',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'C', 'D',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'A', 'B',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'A', 'B',
            'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'N', 'S',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'D', 'E',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'E', 'A', 'R', 'I', 'T', 'N', 'S', 'L', 'E', 'A', 'R', 'I', 'N', 'S',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'C', 'D',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'A', 'B',
            'E', 'A', 'R', 'I', 'T', 'N', 'S', 'L', 'E', 'A', 'R', 'I', 'N', 'S',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'N', 'S',
        ];
        $fnum = abs( round( $longitude ) );
        $lnum = abs( round( $latitude ) );
        $list = str_split( hash( 'sha256', maybe_serialize( $payload ) ) );
        foreach( $list as $character ){
            if ( is_numeric( $character ) ) {
                $fnum = $fnum + $character;
                $lnum = $lnum + $character;
                break;
            }
        }
        return strtoupper( $letters[$fnum] . $letters[$lnum] );
    }

    public static function create_time_string( $timestamp, $timezone_offset ) : string {
        $adjusted_time = $timestamp + $timezone_offset;
        if ( $timestamp > strtotime('-1 hour') ) {
            $time_string = self::_time_elapsed_string('@'.$timestamp);
        }
        else if ( $timestamp > strtotime('today+00:00') + $timezone_offset ) {
            $time_string = date( 'g:i a', $adjusted_time );
        }
        else {
            $time_string = date( 'D g:i a', $adjusted_time );
        }
        return $time_string;
    }

    public static function _time_elapsed_string( $datetime, $full = false ) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function create_in_language_string( $payload ) : string {
        $in_language = '';
        if ( isset( $payload['language_name'] ) && ! empty( $payload['language_name'] ) && 'English' !== $payload['language_name'] ) {
            $in_language = ' in ' . $payload['language_name'];
        }
        return $in_language;
    }

    public static function create_location_precision( $lng, $lat, $label, $payload ) : array {
        $location = [
            'lng' => $lng,
            'lat' => $lat,
            'label' => $label
        ];

        $restricted = self::_persecuted_countries();

        if ( ! isset( $payload['country'] ) ) { // if country is not set, reduce precision to 111km
            $location['lng'] = round($location['lng'], 0 );
            $location['lat'] = round($location['lat'], 0 );
            $location['label'] = '';
        }
        else if ( in_array( $payload['country'], $restricted ) ) { // if persecuted country, reduce precision to 111km
            $location['label'] = ' (' . $payload['country'] . ')';
            $location['lng'] = round( $location['lng'], 0 );
            $location['lat'] = round( $location['lat'], 0 );
        } else { // if non-persecuted country, reduce precision to 11km
            $location['label'] = ' (' . $location['label'] . ')';
            $location['lng'] = round( $location['lng'], 1 );
            $location['lat'] = round( $location['lat'], 1 );
        }

        return $location;
    }

    public static function _persecuted_countries() : array {

        // Top 50 most persecuted countries
        // @link https://www.opendoorsusa.org/christian-persecution/world-watch-list/

        return [
            'North Korea',
            'Afghanistan',
            'Somolia',
            'Libya',
            'Pakistan',
            'Eritrea',
            'Sudan',
            'Yemen',
            'Iran',
            'India',
            'Syria',
            'Nigeria',
            'Saudi Arabia',
            'Maldives',
            'Iraq',
            'Egypt',
            'Algeria',
            'Uzbekistan',
            'Myanmar',
            'Laos',
            'Vietnam',
            'Turkmenistan',
            'China',
            'Mauritania',
            'Central African Republic',
            'Morocco',
            'Qatar',
            'Burkina Faso',
            'Mali',
            'Sri Lanka',
            'Tajikistan',
            'Nepal',
            'Jordan',
            'Tunisia',
            'Kazakhstan',
            'Turkey',
            'Brunei',
            'Bangladesh',
            'Ethiopia',
            'Malaysia',
            'Colombia',
            'Oman',
            'Kuwait',
            'Kenya',
            'Bhutan',
            'Russian Federation',
            'United Arab Emirates',
            'Cameroon',
            'Indonesia',
            'Niger'
        ];
    }

    public static function create_note_data( $category, $action, $initials, $in_language, $location_label, $payload ) : array {
        $data = [
            'note' => '',
            'type' => 'blessing',
        ];

        switch( $action ) {
            case 'starting_group':
                $data['note'] =  $initials . ' is starting a training group' . $in_language . '! ' . $location_label;
                $data['type'] = 'greater_blessing';
                break;
            case 'building_group':
                $data['note'] =  $initials . ' is growing a training group' . $in_language . '! ' . $location_label;
                $data['type'] = 'greater_blessing';
                break;
            case 'leading_1':
            case 'leading_2':
            case 'leading_3':
            case 'leading_4':
            case 'leading_5':
            case 'leading_6':
            case 'leading_7':
            case 'leading_8':
            case 'leading_9':
            case 'leading_10':
                if ( isset($payload['group_size']) && $payload['group_size'] > 1 ) {
                    $data['note'] =  $initials . ' is leading a group of '. $payload['group_size'] .' through session ' . str_replace( '_', '', substr( $action, -2, 2 ) ) . $in_language . '! ' . $location_label;
                } else {
                    $data['note'] =  $initials . ' is leading a group through session ' . str_replace( '_', '', substr( $action, -2, 2 ) ) . $in_language . '! ' . $location_label;
                }
                $data['type'] = 'greatest_blessing';
                break;
            case 'zume_training':
                $data['note'] =  $initials . ' is registering for Zúme training' . $in_language . '! ' . $location_label;
                $data['type'] = 'great_blessing';
                break;
            case 'zume_vision':
                $data['note'] =  $initials . ' is joining the Zúme community to engage in Disciple Making Movements' . $in_language . '! ' . $location_label;
                $data['type'] = 'greatest_blessing';
                break;
            case 'coaching':
                $data['note'] =  $initials . ' is requesting coaching from Zúme coaches' . $in_language . '! ' . $location_label;
                $data['type'] = 'great_blessing';
                break;
            case 'studying_1':
            case 'studying_2':
            case 'studying_3':
            case 'studying_4':
            case 'studying_5':
            case 'studying_6':
            case 'studying_7':
            case 'studying_8':
            case 'studying_9':
            case 'studying_10':
            case 'studying_11':
            case 'studying_12':
            case 'studying_13':
            case 'studying_14':
            case 'studying_15':
            case 'studying_16':
            case 'studying_17':
            case 'studying_18':
            case 'studying_19':
            case 'studying_20':
            case 'studying_21':
            case 'studying_22':
            case 'studying_23':
            case 'studying_24':
            case 'studying_25':
            case 'studying_26':
            case 'studying_27':
            case 'studying_28':
            case 'studying_29':
            case 'studying_30':
            case 'studying_31':
            case 'studying_32':
            case 'studying_offline_1':
            case 'studying_offline_2':
            case 'studying_offline_3':
            case 'studying_offline_4':
            case 'studying_offline_5':
            case 'studying_offline_6':
            case 'studying_offline_7':
            case 'studying_offline_8':
            case 'studying_offline_9':
            case 'studying_offline_10':
            case 'studying_offline_11':
            case 'studying_offline_12':
            case 'studying_offline_13':
            case 'studying_offline_14':
            case 'studying_offline_15':
            case 'studying_offline_16':
            case 'studying_offline_17':
            case 'studying_offline_18':
            case 'studying_offline_19':
            case 'studying_offline_20':
            case 'studying_offline_21':
            case 'studying_offline_22':
            case 'studying_offline_23':
            case 'studying_offline_24':
            case 'studying_offline_25':
            case 'studying_offline_26':
            case 'studying_offline_27':
            case 'studying_offline_28':
            case 'studying_offline_29':
            case 'studying_offline_30':
            case 'studying_offline_31':
            case 'studying_offline_32':
                $title = ' disciple making movement principles';
                if ( isset( $payload['title'] ) && ! empty( $payload['title'] ) ) {
                    $title = ' "' . $payload['title'] . '"';
                }
                $data['note'] =  $initials . ' is studying' . $title . $in_language . '! ' . $location_label;
                $data['type'] = 'blessing';
                break;
            case 'updated_3_month':
                $data['note'] =  $initials . '  made a three month plan to multiply disciples' . $in_language . '! ' . $location_label;
                $data['type'] = 'great_blessing';
                break;
            default:
                break;
        }

        return $data;
    }

    public static function query_contacts_points_geojson( $tz_name, $country = 'none', $language = 'none' ) {
        global $wpdb;

        $utc_time = new DateTime('now', new DateTimeZone($tz_name));
        $timezoneOffset = $utc_time->format('Z');

        $timestamp = strtotime('-100 hours' );
        $results = $wpdb->get_results( $wpdb->prepare( "
                SELECT action, category, lng, lat, label, payload, timestamp FROM $wpdb->dt_movement_log WHERE timestamp > %s ORDER BY timestamp DESC
                ", $timestamp ), ARRAY_A );

        /**
         * (none) - #0E172F
         * Blessing - blessing- #21336A
         * Great Blessing - great_blessing - #2CACE2
         * Greater Blessing - greater_blessing - #90C741
         * Greatest Blessing - greatest_blessing - #FAEA38
         */
        $counts = [
            'blessing' => 0,
            'great_blessing' => 0,
            'greater_blessing' => 0,
            'greatest_blessing' => 0,
        ];
        $countries = [];
        $languages = [];
        $hash = [];

        $features = [];
        foreach ( $results as $result ) {

            $payload = maybe_unserialize( $result['payload'] );

            // make country list from results
            if ( isset( $payload['country'] ) && ! empty( $payload['country'] ) ) {
                $countries[$payload['country']] = $payload['country'];
            }

            // make language list
            if ( isset( $payload['language_name'] )
                && ! empty( $payload['language_name'] )
                && isset( $payload['language_code'] )
                && ! empty( $payload['language_code'] )
            ) {
                $languages[$payload['language_code']] = $payload['language_name'];
            }

            // BUILD NOTE

            // time string
            $time_string = Zume_Public_Heatmap_100hours_Utilities::create_time_string( $result['timestamp'], $timezoneOffset );

            // language
            $in_language = Zume_Public_Heatmap_100hours_Utilities::create_in_language_string( $payload );

            // initials string
            $initials = Zume_Public_Heatmap_100hours_Utilities::create_initials( $result['lng'], $result['lat'], $payload );

            // location string
            $location = Zume_Public_Heatmap_100hours_Utilities::create_location_precision( $result['lng'], $result['lat'], $result['label'], $payload );

            // note and type data
            $data = Zume_Public_Heatmap_100hours_Utilities::create_note_data( $result['category'], $result['action'], $initials, $in_language, $location['label'], $payload );

            // filter out non selected country
            if ( 'none' !== $country && $country !== $payload['country'] ?? '' ) {
                continue;
            }

            // filter out non selected language
            if ( 'none' !== $language && $language !== $payload['language_code'] ?? '' ) {
                continue;
            }

            $hash[] = $data;

            $counts[$data['type']]++;

            $features[] = array(
                'type' => 'Feature',
                'properties' => array(
                    "note" => esc_html( $data['note'] ),
                    "type" => esc_attr( $data['type'] ),
                    "time" => esc_attr( $time_string ),
                    "language" => esc_attr( $payload['language_code'] ?? '' ),
                    "country" => esc_attr( $payload['country'] ?? '' )
                ),
                'geometry' => array(
                    'type' => 'Point',
                    'coordinates' => array(
                        $location['lng'],
                        $location['lat'],
                        1
                    ),
                ),
            );

        } // end foreach loop

        ksort( $countries );
        ksort($languages);

        $new_data = array(
            'type' => 'FeatureCollection',
            'counts' => $counts,
            'countries' => $countries,
            'languages' => $languages,
            'hash' => hash('sha256', serialize( $hash ) ),
            'features' => $features,
        );

        return $new_data;
    }
}
