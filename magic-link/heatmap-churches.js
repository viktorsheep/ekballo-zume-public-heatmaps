var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
  || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
  isMobile = true;
}

window.get_grid_data = (grid_id) => {
  return jQuery.ajax({
    type: "POST",
    data: JSON.stringify({ action: 'grid_id', parts: jsObject.parts, grid_id: grid_id }),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
    beforeSend: function (xhr) {
      xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
    }
  })
    .fail(function(e) {
      console.log(e)
      jQuery('#error').html(e)
    })
}

jQuery(document).ready(function($){
  clearInterval(window.fiveMinuteTimer)

  let slider_width = window.innerWidth * .70
  if ( isMobile ) {
    slider_width = window.innerWidth * .95
  }

  /* set vertical size the form column*/
  $('#custom-style').empty().append(`
      #wrapper {
          height: ${window.innerHeight}px !important;
      }
      #map-wrapper {
          height: ${window.innerHeight}px !important;
      }
      #map {
          height: ${window.innerHeight}px !important;
      }
      .off-canvas {
          width:${slider_width}px;
          background-color:white;
      }
  `)

  $('.loading-spinner').removeClass('active')

  mapboxgl.accessToken = jsObject.map_key;
  var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/light-v10',
    center: [-98, 38.88],
    minZoom: 2,
    maxZoom: 8,
    zoom: 3
  });
  if ( ! isMobile ) {
    map.addControl(
      new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        mapboxgl: mapboxgl,
        marker: false,
        position: 'top-left'
      })
    );
    map.addControl(
      new mapboxgl.GeolocateControl({
        positionOptions: {
          enableHighAccuracy: true
        },
        trackUserLocation: true,
        position: 'top-left'
      })
    );
    map.addControl(
      new mapboxgl.NavigationControl({
        position: 'bottom-right',
        showCompass: false
      })
    );
  }
  map.dragRotate.disable();
  map.touchZoomRotate.disableRotation();

  window.previous_hover = false

  let asset_list = []
  var i = 1;
  while( i <= 46 ){
    asset_list.push(i+'.geojson')
    i++
  }

  jQuery.each(asset_list, function(i,v){

    jQuery.ajax({
      url: jsObject.mirror_url + 'tiles/world/saturation/' + v,
      dataType: 'json',
      data: null,
      beforeSend: function (xhr) {
        if (xhr.overrideMimeType) {
          xhr.overrideMimeType("application/json");
        }
      }
    })
      .done(function (geojson) {

        map.on('load', function() {

          jQuery.each(geojson.features, function (i, v) {
            if (jsObject.grid_data.data[v.id]) {
              geojson.features[i].properties.value = parseInt(jsObject.grid_data.data[v.id].percent)
            } else {
              geojson.features[i].properties.value = 0
            }
          })

          map.addSource(i.toString(), {
            'type': 'geojson',
            'data': geojson
          });
          map.addLayer({
            'id': i.toString()+'line',
            'type': 'line',
            'source': i.toString(),
            'paint': {
              // 'line-color': '#323A68',
              'line-color': 'grey',
              'line-width': .5
            }
          });

          /**************/
          /* hover map*/
          /**************/
          map.addLayer({
            'id': i.toString() + 'fills',
            'type': 'fill',
            'source': i.toString(),
            'paint': {
              'fill-color': 'black',
              'fill-opacity': [
                'case',
                ['boolean', ['feature-state', 'hover'], false],
                .8,
                0
              ]
            }
          })
          /* end hover map*/

          /**********/
          /* heat map brown */
          /**********/
          map.addLayer({
            'id': i.toString() + 'fills_heat',
            'type': 'fill',
            'source': i.toString(),
            'paint': {
              'fill-color': {
                property: 'value',
                stops: [[0, 'rgba(0, 0, 0, 0)'], [1, 'rgb(155, 200, 254)'], [jsObject.grid_data.highest_value, 'rgb(37, 82, 154)']]
              },
              'fill-opacity': 0.75,
              'fill-outline-color': '#707070'
            }
          })
          /**********/
          /* end fill map */
          /**********/

          map.on('mousemove', i.toString()+'fills', function (e) {
            if ( window.previous_hover ) {
              map.setFeatureState(
                window.previous_hover,
                { hover: false }
              )
              hide_details_panel()
            }
            window.previous_hover = { source: i.toString(), id: e.features[0].id }
            if (e.features.length > 0) {
              show_details_panel()
              map.setFeatureState(
                window.previous_hover,
                {hover: true}
              );
              $('#title').html(e.features[0].properties.full_name)
              $('#meter').val(jsObject.grid_data.data[e.features[0].properties.grid_id].percent)
              $('#saturation-goal').html(jsObject.grid_data.data[e.features[0].properties.grid_id].percent)
              $('#population').html(jsObject.grid_data.data[e.features[0].properties.grid_id].population)

              //report
              $('#report-modal-title').html(e.features[0].properties.full_name)
              $('#report-grid-id').val(e.features[0].properties.grid_id)

              let reported = jsObject.grid_data.data[e.features[0].properties.grid_id].reported
              $('#reported').html(reported)

              let needed = jsObject.grid_data.data[e.features[0].properties.grid_id].needed
              $('#needed').html(needed)
            }
          });
          map.on('click', i.toString()+'fills', function (e) {
            $('#title_report').html(e.features[0].properties.full_name)
            $('#population_report').html(jsObject.grid_data.data[e.features[0].properties.grid_id].population)

            let sc = $('#slider-content')
            sc.html('<span class="loading-spinner active"></span>')

            window.get_grid_data(e.features[0].properties.grid_id)
              .done(function(data){
                console.log(data)
                load_slider_content( data )
              })

            $('#offCanvasNestedPush').foundation('toggle', e);
          });

        })

      }) /* ajax call */
  }) /* for each loop */


  $('#add-report').on('click', function(e){
    $('#church-list').empty()
    append_report_row()

    jQuery('#report-modal').foundation('open')
  })
  $('#add-another').on('click', function(e){
    append_report_row()
  })
  let submit_button = $('#submit-report')
  function check_inputs(){
    submit_button.prop('disabled', false)
    $.each($('.required'), function(){
      if ( $(this).val() === '' ) {
        $(this).addClass('redborder')
        submit_button.prop('disabled', true)
      }
      else {
        $(this).removeClass('redborder')
      }
    })

  }
  function append_report_row(){
    let id = Date.now()
    $('#church-list').append(`
      <div class="grid-x row-${id} list-row" data-id="${id}">
          <div class="cell small-5">
              <input type="text" name="${id}[name]" class="${id} name-${id} required" placeholder="Name of Simple Church" data-name="name" data-group-id="${id}" />
          </div>
          <div class="cell small-2">
              <input type="number" name="${id}[members]" class="${id} members-${id} required" placeholder="#" data-name="members" data-group-id="${id}" />
          </div>
          <div class="cell small-4">
              <input type="date" name="${id}[start]" class="${id} start-${id} required" placeholder="Started" data-name="start" data-group-id="${id}" />
          </div>
          <div class="cell small-1">
              <button class="button expanded alert" style="border-radius: 0;" onclick="remove_row(${id})">X</button>
          </div>
      </div>
    `)

    $('.required').focusout(function(){
      check_inputs()
    })
    check_inputs()
  }
  submit_button.on('click', function(){
    let spinner = jQuery('.loading-spinner')
    spinner.addClass('active')

    let submit_button = jQuery('#submit-report')
    submit_button.prop('disabled', true)

    let honey = jQuery('#email').val()
    if ( honey ) {
      submit_button.html('Shame, shame, shame. We know your name ... ROBOT!').prop('disabled', true )
      spinner.removeClass('active')
      return;
    }

    let name_input = jQuery('#name')
    let name = name_input.val()
    if ( ! name ) {
      jQuery('#name-error').show()
      submit_button.removeClass('loading')
      name_input.focus(function(){
        jQuery('#name-error').hide()
      })
      submit_button.prop('disabled', false)
      spinner.removeClass('active')
      return;
    }

    let email_input = jQuery('#e2')
    let email = email_input.val()
    if ( ! email ) {
      jQuery('#email-error').show()
      submit_button.removeClass('loading')
      email_input.focus(function(){
        jQuery('#email-error').hide()
      })
      submit_button.prop('disabled', false)
      spinner.removeClass('active')
      return;
    }

    let phone_input = jQuery('#phone')
    let phone = phone_input.val()
    if ( ! phone ) {
      jQuery('#phone-error').show()
      submit_button.removeClass('loading')
      email_input.focus(function(){
        jQuery('#phone-error').hide()
      })
      submit_button.prop('disabled', false)
      spinner.removeClass('active')
      return;
    }

    let list = []
    jQuery.each( jQuery('.list-row'), function(i,v){
      let row_id = jQuery(this).data('id')
      list.push({
        name: jQuery('.name-'+row_id).val(),
        members: jQuery('.members-'+row_id).val(),
        start: jQuery('.start-'+row_id).val()
      })
    })

    let grid_id = jQuery('#report-grid-id').val()
    let return_reporter = jQuery('#return-reporter').is(':checked');

    // if cookie contact_id
    // if window contact_id
    let contact_id = ''
    if ( typeof window.contact_id !== 'undefined' && typeof window.contact_email !== 'undefined' ) {
      if ( email === window.contact_email ) {
        contact_id = window.contact_id
      }
    }

    let form_data = {
      name: name,
      email: email,
      phone: phone,
      grid_id: grid_id,
      contact_id: contact_id,
      return_reporter: return_reporter,
      list: list
    }

    jQuery.ajax({
      type: "POST",
      data: JSON.stringify({ action: 'new_report', parts: jsObject.parts, data: form_data }),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
      }
    })
      .done(function(response){
        jQuery('.loading-spinner').removeClass('active')
        console.log(response)

        window.contact_id = response.contact.ID
        window.contact_email = email

      })
      .fail(function(e) {
        console.log(e)
        jQuery('#error').html(e)
      })
  })
})

function show_details_panel(){
  $('#details-panel').show()

}
function hide_details_panel(){
  $('#details-panel').hide()

}

function load_slider_content( data ) {
  let content = $('#slider-content')
  content.empty()

  content.append(`
    <div class="grid-x">
        <div class="cell small-4">
            <div class="grid-x">
                <div class="cell">
                    THIS LOCATION
                </div>
                <div class="cell">
                    Churches Needed
                </div>
                <div class="cell">
                    Churches Reported
                </div>
            </div>
        </div>
        <div class="cell small-4">
            <div class="grid-x">
                <div class="cell">
                    PROGRESS
                </div>
                <div class="cell">
                    Country Progress
                </div>
                <div class="cell">
                    Location Progress
                </div>
            </div>
        </div>
        <div class="cell small-4">
            <div class="grid-x">
                <div class="cell">
                    ACTIVITY
                </div>
                <div class="cell">

                </div>
                <div class="cell">

                </div>
            </div>
        </div>
    </div>
  `)

}

function remove_row( id ) {
  let submit_button = $('#submit-report')
  jQuery('.row-'+id).remove();
  submit_button.prop('disabled', true)
}
if (document.readyState === 'complete') {
  window.contact_id = Cookie.get('contact_id')
  window.contact_email = Cookie.get('contact_email')
}
