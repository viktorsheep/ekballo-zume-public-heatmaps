<?php DT_Mapbox_API::geocoder_scripts(); ?>

<!-- title -->
<div class="grid-x">
    <div class="cell padding-1" >
        <button type="button" style="margin:1em .5em 1em;" id="menu-icon" data-open="offCanvasLeft"><i class="fi-list" style="font-size:2em;"></i></button>
        <a style="margin:1em 1em 1em 0; color:black;" href="<?php echo esc_url( site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/' ) ?>"><i class="fi-home" style="font-size:2em;"></i></a>
        <span style="font-size:1.5rem;font-weight: bold;">Map</span>
        <span class="loading-spinner active" style="float:right;margin:10px;"></span><!-- javascript container -->
    </div>
</div>

<!-- nav -->
<?php $this->nav(); ?>

<style id="custom-style-portal">
    #wrapper {
        height: 2000px !important;
    }
    #map-wrapper {
        height: 2000px !important;
    }
    #map {
        height: 2000px !important;
    }
</style>

<div id="initialize-screen">
    <div id="initialize-spinner-wrapper" class="center">
        <progress class="success initialize-progress" max="46" value="0"></progress><br>
        Loading the planet ...<br>
        <span id="initialize-people" style="display:none;">Locating world population...</span><br>
        <span id="initialize-activity" style="display:none;">Calculating movement activity...</span><br>
        <span id="initialize-coffee" style="display:none;">Shamelessly brewing coffee...</span><br>
        <span id="initialize-dothis" style="display:none;">Let's do this...</span><br>
    </div>
</div>

<div class="large reveal" id="welcome-modal" data-v-offset="10px" data-reveal>
    <div id="welcome-content" data-close></div>
    <div class="center"><button class="button" id="welcome-close-button" data-close>Get Started!</button></div>
</div>


<div class="grid-x">
    <div class="cell medium-9" >
        <div id="map-wrapper">
            <span class="loading-spinner active"></span>
            <div id='map'></div>
        </div>
    </div>
    <div class="cell medium-3" id="map-sidebar-wrapper">
        <!-- details panel -->
        <div id="details-panel">
            <div class="grid-x grid-padding-x" >
                <div class="cell">
                    <h1 id="title"></h1>
                    <h3>Population: <span id="population">0</span></h3>
                    <hr>
                </div>
                <div class="cell">
                    <h2 id="panel-type-title">Churches</h2>
                </div>
                <div class="cell" id="needed-row">
                    <h3>Needed: <span id="needed">0</span></h3>
                </div>
                <div class="cell">
                    <h3>Reported: <span id="reported">0</span></h3>
                </div>
                <div class="cell">
                    <hr>
                </div>
                <div class="cell" id="goal-row">
                    <h2>Goal: <span id="saturation-goal">0</span>%</h2>
                    <meter id="meter" class="meter" value="30" min="0" low="33" high="66" optimum="100" max="100"></meter>
                </div>
            </div>
        </div>

        <!-- start screen training-->
        <div id="training-start-screen" class="training-content"></div>
        <div id="training-help-screen" class="training-content" style="display:none;"><hr></div>
        <div class="center"><i class="fi-info" id="help-toggle-icon" onclick="jQuery('#training-help-screen').toggle()"></i></div>
    </div>
</div>


<!-- modal -->
<div class="off-canvas position-right is-closed" id="offCanvasNestedPush" data-transition-time=".3s" data-off-canvas>
    <input type="hidden" id="report-modal-title" />
    <input type="hidden" id="report-grid-id" />
    <div class="grid-x" id="canvas_panel">
        <div class="cell">
            <div class="grid-x">
                <div class="cell">
                    <h1 id="modal_tile"></h1>
                    <h3>Population: <span id="modal_population">0</span></h3>
                    <button class="button" onclick="window.create_group_by_map()">Report New Church</button>
                </div>
            </div>
            <hr>
        </div>
        <div class="cell" id="slider-content">
            <div class="grid-x grid-padding-x">
                <div class="cell medium-6">
                    <div class="grid-x">
                        <div class="cell">
                            <h3>PROGRESS</h3>
                        </div>
                        <div class="cell" id="progress-content">
                            <div class="grid-x">
                                <div class="cell">
                                    <p id="custom-paragraph" class="temp-spinner"></p>
                                </div>
                                <div class="cell"><hr></div>
                                <div class="cell temp-spinner" id="a3-list-item"></div>
                                <div class="cell temp-spinner" id="a2-list-item"></div>
                                <div class="cell temp-spinner" id="a1-list-item"></div>
                                <div class="cell temp-spinner" id="a0-list-item"></div>
                                <div class="cell temp-spinner" id="world-list-item"></div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="cell medium-6">
                    <div class="grid-x">
                        <div class="cell">
                            <h3>ACTIVITY</h3>
                        </div>
                        <div class="cell"><hr></div>
                        <div class="cell temp-spinner" id="activity-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="reveal large" id="edit-modal" data-v-offset="0" data-close-on-click="false" data-reveal>
    <div id="modal-title"></div>
    <div id="modal-content"></div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">Save</span>
    </button>
</div>

<div class="float">
    <i class="fi fi-plus floating small"></i>
</div>

