jQuery(document).ready(function(){
  let chartDiv = jQuery('#chart')

  window.activity_filter = {}
  window.activity_list = {}
  window.activity_geojson = {
    "type": "FeatureCollection",
    "features": []
  }

  // Add html and map
  chartDiv.empty().html(`
      <style>
          #activity-list-wrapper {
              height: ${window.innerHeight - 270}px !important;
              overflow: scroll;
          }
          #activity-list-wrapper li {
              font-size:.8em;
              list-style-type: none;
          }
          #activity-list-wrapper h2 {
              font-size:1.2em;
              font-weight:bold;
          }
          #map-wrapper {
              height: ${window.innerHeight}px !important;
          }
          #map {
              height: ${window.innerHeight}px !important;
          }
          #activity-wrapper {
              height: ${window.innerHeight}px !important;
              overflow: scroll;
          }
      </style>
      <div class="grid-x">
        <div class="medium-9 cell">
            <div id="map-wrapper">
                <div id='map'></div>
            </div>
        </div>
        <div class="medium-3 cell">
            <div class="grid-x grid-padding-x">
                <div class="cell">
                    <h2>Activity List</h2>
                </div>
                <div class="cell">
                    <div>
                        <select name="type" id="type-dropdown">
                            <option value="none">Filter by Type</option>
                            <option value="learning">Learning</option>
                            <option value="joining">Joining</option>
                            <option value="producing">Producing</option>

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
                </div>
                <div class="cell"><div class="loading-spinner active"></div></div>
                <div class="cell">

                </div>
            </div>
            <div id="activity-wrapper">
                <ul id="activity-list"></ul>
            </div>
        </div>
      </div>
  `)
  let container = jQuery('#activity-list');

  mapboxgl.accessToken = jsObject.map_key;
  var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/light-v10',
    center: [-98, 38.88],
    minZoom: 1,
    maxZoom: 15,
    zoom: 0
  });

  // disable map rotation using right click + drag
  map.dragRotate.disable();
  map.touchZoomRotate.disableRotation();

  map.on('load', function() {
    initialize_cluster_map()
  });
  function initialize_cluster_map() {
    map.addSource('layer-source-contacts', {
      type: 'geojson',
      data: window.activity_geojson,
      cluster: true,
      clusterMaxZoom: 20,
      clusterRadius: 50
    });
    map.addLayer({
      id: 'clusters',
      type: 'circle',
      source: 'layer-source-contacts',
      filter: ['has', 'point_count'],
      paint: {
        'circle-color': [
          'step',
          ['get', 'point_count'],
          '#51bbd6',
          100,
          '#f1f075',
          750,
          '#f28cb1'
        ],
        'circle-radius': [
          'step',
          ['get', 'point_count'],
          20,
          100,
          30,
          750,
          40
        ]
      }
    });
    map.addLayer({
      id: 'cluster-count-contacts',
      type: 'symbol',
      source: 'layer-source-contacts',
      filter: ['has', 'point_count'],
      layout: {
        'text-field': '{point_count_abbreviated}',
        'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
        'text-size': 12
      }
    });
    map.addLayer({
      id: 'unclustered-point-contacts',
      type: 'circle',
      source: 'layer-source-contacts',
      filter: ['!', ['has', 'point_count']],
      paint: {
        'circle-color': '#11b4da',
        'circle-radius':12,
        'circle-stroke-width': 1,
        'circle-stroke-color': '#fff'
      }
    });
  }

  function initial_load_geojson(){
    window.post_request('initial_load_geojson', {} )
      .done( data => {
        console.log(data)
        "use strict";
        window.activity_geojson = data
        update_cluster_map()
      })
  }
  function load_map_activity() {
    container.empty()
    let spinner = jQuery('.loading-spinner')
    spinner.addClass('active')
    window.current_bounds = map.getBounds()

    let country = jQuery('#country-dropdown').val()
    let language = jQuery('#language-dropdown').val()

    let data = {
      bounds: { 'n_lat': window.current_bounds._ne.lat, 's_lat': window.current_bounds._sw.lat, 'e_lng': window.current_bounds._ne.lng, 'w_lng': window.current_bounds._sw.lng},
      timezone: 'America/Denver',
      country: country,
      language: language
    }

    window.post_request('activity_list', data )
      .done( data => {
        let spinner = jQuery('.loading-spinner')
        console.log(data)
        "use strict";
        window.activity_list = data
        update_activity_list()
        spinner.removeClass('active')
      })
  }
  initial_load_geojson()
  load_map_activity()


  function update_cluster_map() {
    var mapSource= map.getSource('layer-source-contacts');
    if(typeof mapSource === 'undefined') {
      initial_load_geojson()
    } else {
      map.getSource('layer-source-contacts').setData(window.activity_geojson);
    }
    load_countries_dropdown()
    load_languages_dropdown()
  }

  function update_activity_list(){
    container.empty()
    let spinner = jQuery('.loading-spinner')
    jQuery.each( window.activity_list, function(i,v){
      if ( '' === v.note ) {
        return
      }
      container.append(`<li class="${v.type} ${v.country} ${v.language}"><strong>(${v.time})</strong> ${v.note} </li>`)
    })

    if ( window.activity_list.length < 1 ) {
      container.append(`<li><strong>Results</strong> 0</li>`)
    }

    spinner.removeClass('active')
  }

  map.on('zoomend', function(e){
    load_map_activity()
  })
  map.on('dragend', function(e){
    load_map_activity()
  })

  function load_countries_dropdown() {
    let country_dropdown = jQuery('#country-dropdown')
    let points = window.activity_geojson
    window.selected_country = country_dropdown.val()
    country_dropdown.empty()

    let add_selected = ''
    country_dropdown.append(`<option value="none">Filter by Country</option>`)
    country_dropdown.append(`<option value="none">Clear</option>`)
    country_dropdown.append(`<option disabled>---</option>`)
    jQuery.each(points.countries, function(i,v){
      add_selected = ''
      if ( i === window.selected_country ) {
        add_selected = ' selected'
      }
      country_dropdown.append(`<option value="${i}" ${add_selected}>${v}</option>`)
    })
  }
  function load_languages_dropdown() {
    let language_dropdown = jQuery('#language-dropdown')
    let points = window.activity_geojson
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
})
