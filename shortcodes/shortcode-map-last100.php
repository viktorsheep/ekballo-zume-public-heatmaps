<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.

class Movement_Shortcode_Map_Last100hours
{
    public $namespace = 'movement_maps_stats/v1/';
    public $token = 'last100hours_map';
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
        add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ], 99 );
        add_shortcode( $this->token, [ $this, 'short_code' ] );
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
        add_action( 'movement_maps_stats_shortcodes_list', [ $this, 'instructions_for_shortcode'] );

    }

    public function register_scripts(){
        /**
         * Register the scripts early, then call them late through the short code.
         */
        wp_register_script( 'jquery-cookie', 'https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js', [ 'jquery' ], '3.0.0' );
        wp_register_script( 'mapbox-cookie', trailingslashit( get_stylesheet_directory_uri() ) . 'dt-mapping/geocode-api/mapbox-cookie.js', [ 'jquery', 'jquery-cookie' ], '3.0.0' );
        wp_register_script( 'mapbox-gl', DT_Mapbox_API::$mapbox_gl_js, [ 'jquery' ], DT_Mapbox_API::$mapbox_gl_version, false );
        wp_register_style( 'mapbox-gl-css', DT_Mapbox_API::$mapbox_gl_css, [], DT_Mapbox_API::$mapbox_gl_version );
    }

    public function short_code( $atts ){

        // require classes
        if ( ! class_exists( 'DT_Ipstack_API' ) ) {
            require_once( plugin_dir_path(__DIR__) . '/dt-mapping/geocode-api/ipstack-api.php' );
        }
        if ( ! class_exists( 'DT_Mapbox_API' ) ) {
            require_once( plugin_dir_path(__DIR__) . '/dt-mapping/geocode-api/mapbox-api.php' );
        }

        // call registered scripts
        wp_enqueue_script( 'jquery-cookie' );
        wp_enqueue_script( 'mapbox-cookie');
        wp_enqueue_script( 'mapbox-gl');
        wp_enqueue_style( 'mapbox-gl-css');

        // set timezone info
         // Expects to be installed in a theme like Zume.Vision that has a full copy of the dt-mapping folder from Disciple Tools.
        $ipstack = new DT_Ipstack_API();
        $ip_address = $ipstack::get_real_ip_address();
        $this->ip_response = $ipstack::geocode_ip_address($ip_address);

        // begin echo cache
        ob_start();
        ?>
        <script>
            /* <![CDATA[ */
            window.dt_mapbox_metrics = [<?php echo json_encode([
                'translations' => [
                    'title' => __( "Last 100 Hours of Zúme", "disciple_tools" ),
                ],
                'settings' => [
                    'map_key' => DT_Mapbox_API::get_key(),
                    'points_rest_url' => $this->token,
                    'points_rest_base_url' => $this->namespace,
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
            <div class="cell medium-4 padding-1">
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
                                    height: ${window.innerHeight - 100}px !important;
                                    position:relative;
                                }
                                #map {
                                    height: ${window.innerHeight - 100}px !important;
                                }
                                #activity-wrapper {
                                    height: ${window.innerHeight - 350}px !important;
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

        return ob_get_clean();
    }

    public function add_api_routes() {
        register_rest_route(
            $this->namespace, '/'.$this->token, [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'points_geojson'],
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

        return Movement_Shortcode_Utilities::query_contacts_points_geojson( $tz_name, $country, $language );
    }

    public function instructions_for_shortcode(){
        ?>
        <p>
            Last 100 Map<br>
            <code>[<?php echo $this->token ?>]</code><br>
            Add this to a page in the website and set template to empty container (full-width, no styling except header and footer.)
        </p>
        <?php
    }

}
Movement_Shortcode_Map_Last100hours::instance();
