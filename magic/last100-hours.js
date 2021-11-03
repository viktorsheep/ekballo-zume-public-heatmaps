jQuery(document).ready(function(){
  let chartDiv = jQuery('#chart')

  window.activity_list = {}
  window.activity_geojson = {
    "type": "FeatureCollection",
    "features": []
  }

  // Add html and map
  chartDiv.empty().html(`
      <style>
          #activity-wrapper {
              height: ${window.innerHeight - 200}px !important;
              overflow: scroll;
          }
          #activity-list{
              height: ${window.innerHeight - 200}px !important;
              overflow: scroll;
          }
          #activity-list li {
              font-size:.8em;
              list-style-type: none;
          }
          #activity-list h2 {
              font-size:1.2em;
              font-weight:bold;
          }
          #map-wrapper {
              height: ${window.innerHeight}px !important;
          }
          #map {
              height: ${window.innerHeight}px !important;
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
      </style>
      <div class="grid-x">
        <div class="medium-9 cell">
            <div id="map-wrapper">
                <div id='map'></div>
                <div id="map-header"><h3>Last 100 Hours of Activity</h3></div>
            </div>
        </div>
        <div class="medium-3 cell">
            <div class="grid-x grid-padding-x">
                <div class="cell">
                    <h2 style="padding-top:.7rem;">Activity List</h2>
                </div>
                <div class="cell">
                    <div>
                        <select name="type" id="type-dropdown" class="input-filter">
                            <option value="none">All Types</option>
                        </select>
                    </div>
                    <div>
                        <select name="country" id="country-dropdown" class="input-filter">
                            <option value="none">All Countries</option>
                        </select>
                    </div>
                    <div>
                        <select name="language" id="language-dropdown" class="input-filter">
                            <option value="none">All Languages</option>
                        </select>
                    </div>
                </div>
                <div class="cell"><div class="loading-spinner active"></div></div>
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
    zoom: 1
  });

  // disable map rotation using right click + drag
  map.dragRotate.disable();
  map.touchZoomRotate.disableRotation();

  map.on('load', function() {
    initialize_cluster_map()
  });

  map.on('zoomend', function(e){
    load_map_activity()
  })
  map.on('dragend', function(e){
    load_map_activity()
  })

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
          '#00d9ff',
          20,
          '#00aeff',
          150,
          '#90C741'
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
        'circle-color': '#00d9ff',
        'circle-radius':12,
        'circle-stroke-width': 1,
        'circle-stroke-color': '#fff'
      }
    });
  }

  function load_geojson(){
    let data = get_filters()
    window.post_request('load_geojson', data )
      .done( data => {
        console.log(data)
        "use strict";
        window.activity_geojson = data

        var mapSource= map.getSource('layer-source-contacts');
        if( typeof mapSource !== 'undefined') {
          map.getSource('layer-source-contacts').setData(window.activity_geojson);
        }

        load_countries_dropdown()
        load_languages_dropdown()
        load_type_dropdown()
      })
  }

  function load_map_activity() {
    container.empty()
    let spinner = jQuery('.loading-spinner')
    spinner.addClass('active')
    let data = get_filters()

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
  load_geojson()
  load_map_activity()

  jQuery('.input-filter').on('change', function(e){
    load_map_activity()
    limit_cluster_to_filter()
  })

  function get_filters() {
    window.current_bounds = map.getBounds()
    let country = jQuery('#country-dropdown').val()
    let language = jQuery('#language-dropdown').val()
    let type = jQuery('#type-dropdown').val()
    return {
      bounds: { 'n_lat': window.current_bounds._ne.lat, 's_lat': window.current_bounds._sw.lat, 'e_lng': window.current_bounds._ne.lng, 'w_lng': window.current_bounds._sw.lng},
      timezone: 'America/Denver',
      country: country,
      language: language,
      type: type,
      zoom: map.getZoom()
    }
  }
  function limit_cluster_to_filter() {
    let data = get_filters()
    console.log(data)

    let geojson = {
      "type": "FeatureCollection",
      "features": []
    }

    jQuery.each( window.activity_geojson.features, function(i,v){
      if ( data.type === v.properties.type ) {
        geojson.features.push(v)
      } else if ( data.language === v.properties.language ) {
        geojson.features.push(v)
      } else if ( data.country === v.properties.country ) {
        geojson.features.push(v)
      }

      if ( 'none' === data.type && 'none' === data.language && 'none' === data.country ) {
        geojson.features.push(v)
      }
    })

    var mapSource= map.getSource('layer-source-contacts');
    if(typeof mapSource === 'undefined') {
      load_geojson()
    } else {
      map.getSource('layer-source-contacts').setData(geojson);
    }
  }

  function update_activity_list(){
    container.empty()
    let spinner = jQuery('.loading-spinner')
    jQuery.each( window.activity_list.list, function(i,v){
      if ( '' === v.note ) {
        return
      }
      container.append(`<li class="${v.type} ${v.country} ${v.language}"><strong>(${v.time})</strong> ${v.note} </li>`)
    })

    if ( window.activity_list.list.length < 1 ) {
      container.append(`<li><strong>Results</strong> 0</li>`)
    }

    if ( window.activity_list.count > 250 ) {
      container.append(`<hr><li><strong>${window.activity_list.count - 250} Additional Records. (Zoom or Filter map to focus results)</strong></li><br><br>`)
    }

    spinner.removeClass('active')
  }

  function load_countries_dropdown() {
    let country_dropdown = jQuery('#country-dropdown')
    let points = window.activity_geojson
    window.selected_country = country_dropdown.val()
    country_dropdown.empty()

    let add_selected = ''
    country_dropdown.append(`<option value="none">All Countries</option>`)
    country_dropdown.append(`<option disabled>---</option>`)
    jQuery.each(points.countries, function(i,v){
      add_selected = ''
      if ( v.code === window.selected_country ) {
        add_selected = ' selected'
      }
      country_dropdown.append(`<option value="${v.code}" ${add_selected}>${v.name} (${v.count})</option>`)
    })
  }
  function load_languages_dropdown() {
    let language_dropdown = jQuery('#language-dropdown')
    let points = window.activity_geojson
    window.selected_language = language_dropdown.val()
    language_dropdown.empty()

    let add_selected = ''
    language_dropdown.append(`<option value="none">All Languages</option>`)
    language_dropdown.append(`<option disabled>---</option>`)
    jQuery.each(points.languages, function(i,v){
      add_selected = ''
      if ( v.code === window.selected_language ) {
        add_selected = ' selected'
      }
      language_dropdown.append(`<option value="${v.code}" ${add_selected}>${v.name} (${v.count})</option>`)
    })
  }
  function load_type_dropdown() {
    let type_dropdown = jQuery('#type-dropdown')
    let points = window.activity_geojson
    window.selected_type = type_dropdown.val()
    type_dropdown.empty()
    let learning = ''
    let joining = ''
    let producing = ''

    let add_selected = ''
    type_dropdown.append(`<option value="none">All Types</option>`)
    type_dropdown.append(`<option disabled>---</option>`)
    type_dropdown.append(`<option value="" id="learning"></option>`)
    type_dropdown.append(`<option value="" id="joining"></option>`)
    type_dropdown.append(`<option value="" id="producing"></option>`)
    jQuery.each(points.types, function(i,v){
      add_selected = ''
      if ( v.code === window.selected_type ) {
        add_selected = ' selected'
      }
      jQuery('#'+v.code).val(v.code).html(`${v.name} (${v.count})`)
      // type_dropdown.append(`<option value="${v.code}" ${add_selected}>${v.name} (${v.count})</option>`)
    })
  }
})
