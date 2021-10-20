<?php
// set timezone info
// Expects to be installed in a theme like Zume.Vision that has a full copy of the dt-mapping folder from Disciple Tools.
$ipstack = new DT_Ipstack_API();
$ip_address = $ipstack::get_real_ip_address();
$this->ip_response = $ipstack::geocode_ip_address( $ip_address );

// begin echo cache
?>
<div class="grid-x">
    <div class="cell medium-8">
        <div id="dynamic-styles"></div>
        <div id="map-wrapper">
            <div id='map'></div>
            <div id="map-loader" class="spinner-loader"><img src="<?php echo plugin_dir_url( __DIR__ ) ?>/spinner.svg" width="100px" /></div>
            <div id="map-header"><h3>Last 100 Hours</h3></div>
        </div>
    </div>
    <div class="cell medium-4" style="padding: .5rem;">
        <div class="grid-x grid-padding-x">
            <div class="cell">
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
                        $tzlist = DateTimeZone::listIdentifiers( DateTimeZone::ALL );
                        foreach ( $tzlist as $tz ) {
                            echo '<option value="'.esc_html( $tz ).'">'.esc_html( $tz ).'</option>';
                        }
                        ?>
                    </select>
                    <button class="close-button" data-close aria-label="Close modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <hr>
                <div>
                    <select name="type" id="type-dropdown">
                        <option value="none">Filter by Type</option>
                        <option value="none">Learning</option>
                        <option value="none">Joining</option>
                        <option value="none">Producing</option>
                    </select>
                </div>
                <div>
                    <select name="country" id="country-dropdown">
                        <option value="none">Filter by Country</option>
                    </select>
                </div>
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
        <div id="list-loader" class="spinner-loader"><img src="<?php echo plugin_dir_url( __DIR__ ) ?>/spinner.svg" width="50px" /> </div>
        <!-- Activity List -->
        <div id="activity-wrapper">
            <ul id="activity-list"></ul>
        </div>

    </div>
</div>
