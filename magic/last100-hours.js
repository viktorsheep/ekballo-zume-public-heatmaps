jQuery(document).ready(function(){
  let chartDiv = jQuery('#chart')
  let spinner = '<span class="loading-spinner active"></span>'

  window.activity_filter = {}
  window.activity_geojson = {
    "type": "FeatureCollection",
    "features": []
  }

  // add highlight to menu
  jQuery('#network_activity_map').prop('style', 'font-weight:900;')

  // write page layout with spinners
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
      </style>
      <div class="grid-x">
        <div class="medium-9 cell">
            <div id="map-wrapper">
                <div id='map'></div>
            </div>
        </div>
        <div class="medium-3 cell">
            <div class="grid-x">
                <div class="cell">
                    Activity List
                </div>
                <div class="cell">
                    <div id="activity-list-wrapper"></div>
                </div>
            </div>
        </div>
      </div>
  `)
  function load_map_geojson( data){
    if ( typeof window.activity_filter === 'undefined' ){
        window.activity_filter = {}
    }
    window.post_request('initial_load_geojson', {} )
      .done( data => {
        console.log(data)

        "use strict";
        window.activity_geojson = data
        update_cluster_map()
      })
  }
  function load_map_activity() {
    window.current_bounds = map.getBounds()
    window.activity_filter.boundary = { 'n_lat': window.current_bounds._ne.lat, 's_lat': window.current_bounds._sw.lat, 'e_lng': window.current_bounds._ne.lng, 'w_lng': window.current_bounds._sw.lng}

    window.post_request('activity_list', {} )
      .done( data => {
        console.log(data)

        "use strict";
        window.activity_geojson = data
        update_cluster_map()
      })
  }
  load_map_geojson()


  function update_cluster_map() {
    var mapSource= map.getSource('layer-source-contacts');
    if(typeof mapSource === 'undefined') {
      write_cluster_map()
    } else {
      map.getSource('layer-source-contacts').setData(window.activity_geojson);
    }
  }

  mapboxgl.accessToken = jsObject.map_key;
  var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/light-v10',
    center: [-98, 38.88],
    minZoom: 0,
    zoom: 0
  });

  map.on('load', function() {
    map.addSource('layer-source-contacts', {
      type: 'geojson',
      data: window.activity_geojson,
      cluster: true,
      clusterMaxZoom: 14,
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

    window.current_bounds = map.getBounds()
    window.activity_filter.boundary = { 'n_lat': window.current_bounds._ne.lat, 's_lat': window.current_bounds._sw.lat, 'e_lng': window.current_bounds._ne.lng, 'w_lng': window.current_bounds._sw.lng}

  });

  map.on('zoomend', function(e){
    window.current_bounds = map.getBounds()
    window.activity_filter.boundary = { 'n_lat': window.current_bounds._ne.lat, 's_lat': window.current_bounds._sw.lat, 'e_lng': window.current_bounds._ne.lng, 'w_lng': window.current_bounds._sw.lng}
    load_map_geojson()
  })
  map.on('dragend', function(e){
    window.current_bounds = map.getBounds()
    window.activity_filter.boundary = { 'n_lat': window.current_bounds._ne.lat, 's_lat': window.current_bounds._sw.lat, 'e_lng': window.current_bounds._ne.lng, 'w_lng': window.current_bounds._sw.lng}
    load_map_geojson()
  })
})
