jQuery(document).ready(function($) {

  // console.log(dt_mapbox_metrics)
  function write_all_points( ) {

    // let blessing_button = jQuery('#blessing-button')
    // let great_blessing_button = jQuery('#great-blessing-button')
    // let greater_blessing_button = jQuery('#greater-blessing-button')
    // let greatest_blessing_button = jQuery('#greatest-blessing-button')
    let country_dropdown = jQuery('#country-dropdown')
    let language_dropdown = jQuery('#language-dropdown')

    // window.blessing = 'visible'
    // window.great_blessing = 'visible'
    // window.greater_blessing = 'visible'
    // window.greatest_blessing = 'visible'

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
          console.log(points)

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


      // map.on('mouseenter', 'blessing', function (e) {
      //   mouse_enter( e )
      // });
      // map.on('mouseleave', 'blessing', function (e) {
      //   mouse_leave( e )
      // });
      // map.on('mouseenter', 'greatBlessing', function (e) {
      //   mouse_enter( e )
      // });
      // map.on('mouseleave', 'greatBlessing', function (e) {
      //   mouse_leave( e )
      // });
      // map.on('mouseenter', 'greaterBlessing', function (e) {
      //   mouse_enter( e )
      // });
      // map.on('mouseleave', 'greaterBlessing', function (e) {
      //   mouse_leave( e )
      // });
      // map.on('mouseenter', 'greatestBlessing', function (e) {
      //   mouse_enter( e )
      // });
      // map.on('mouseleave', 'greatestBlessing', function (e) {
      //   mouse_leave( e )
      // });
      //
      // function mouse_enter( e ) {
      //   map.getCanvas().style.cursor = 'pointer';
      //
      //   var coordinates = e.features[0].geometry.coordinates.slice();
      //   var description = e.features[0].properties.note;
      //
      //   while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
      //     coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
      //   }
      //
      //   popup
      //     .setLngLat(coordinates)
      //     .setHTML(description)
      //     .addTo(map);
      // }
      // function mouse_leave( e ) {
      //   map.getCanvas().style.cursor = '';
      //   popup.remove();
      // }

      jQuery('#map-loader').hide()
    }

    function load_list( points ) {
      let list_container = jQuery('#activity-list')
      list_container.empty()
      // let filter_blessing = blessing_button.hasClass('filtered')
      // let filter_great_blessing = great_blessing_button.hasClass('filtered')
      // let filter_greater_blessing = greater_blessing_button.hasClass('filtered')
      // let filter_greatest_blessing = greatest_blessing_button.hasClass('filtered')
      jQuery.each( points.features, function(i,v){
        let visible = 'block'
        // if ( 'blessing' === v.properties.type && filter_blessing ) {
        //   visible = 'none'
        // }
        // if ( 'great_blessing' === v.properties.type && filter_great_blessing ) {
        //   visible = 'none'
        // }
        // if ( 'greater_blessing' === v.properties.type && filter_greater_blessing ) {
        //   visible = 'none'
        // }
        // if ( 'greatest_blessing' === v.properties.type && filter_greatest_blessing ) {
        //   visible = 'none'
        // }
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

      // jQuery('.blessing-count').empty().append(points.counts.blessing)
      // jQuery('.great-blessing-count').empty().append(points.counts.great_blessing)
      // jQuery('.greater-blessing-count').empty().append(points.counts.greater_blessing)
      // jQuery('.greatest-blessing-count').empty().append(points.counts.greatest_blessing)

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
    // blessing_button.on('click', function(){
    //   if ( blessing_button.hasClass('filtered') ) {
    //     blessing_button.removeClass('filtered')
    //     jQuery('.blessing-activity').show()
    //     window.blessing = 'visible'
    //     map.setLayoutProperty('blessing', 'visibility', 'visible');
    //   } else {
    //     blessing_button.addClass('filtered')
    //     jQuery('.blessing-activity').hide()
    //     window.blessing = 'none'
    //     map.setLayoutProperty('blessing', 'visibility', 'none');
    //   }
    // })
    // great_blessing_button.on('click', function(){
    //   if ( great_blessing_button.hasClass('filtered') ) {
    //     great_blessing_button.removeClass('filtered')
    //     jQuery('.great_blessing-activity').show()
    //     window.great_blessing = 'visible'
    //     map.setLayoutProperty('greatBlessing', 'visibility', 'visible');
    //   } else {
    //     great_blessing_button.addClass('filtered')
    //     jQuery('.great_blessing-activity').hide()
    //     window.great_blessing = 'none'
    //     map.setLayoutProperty('greatBlessing', 'visibility', 'none');
    //   }
    // })
    // greater_blessing_button.on('click', function(){
    //   if ( greater_blessing_button.hasClass('filtered') ) {
    //     greater_blessing_button.removeClass('filtered')
    //     jQuery('.greater_blessing-activity').show()
    //     window.greater_blessing = 'visible'
    //     map.setLayoutProperty('greaterBlessing', 'visibility', 'visible');
    //   } else {
    //     greater_blessing_button.addClass('filtered')
    //     jQuery('.greater_blessing-activity').hide()
    //     window.greater_blessing = 'none'
    //     map.setLayoutProperty('greaterBlessing', 'visibility', 'none');
    //   }
    // })
    // greatest_blessing_button.on('click', function(){
    //   if ( greatest_blessing_button.hasClass('filtered') ) {
    //     greatest_blessing_button.removeClass('filtered')
    //     jQuery('.greatest_blessing-activity').show()
    //     window.greatest_blessing = 'visible'
    //     map.setLayoutProperty('greatestBlessing', 'visibility', 'visible');
    //   } else {
    //     greatest_blessing_button.addClass('filtered')
    //     jQuery('.greatest_blessing-activity').hide()
    //     window.greatest_blessing = 'none'
    //     map.setLayoutProperty('greatestBlessing', 'visibility', 'none');
    //   }
    // })

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
