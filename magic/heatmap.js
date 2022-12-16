var isMobile = false; //initiate as false

// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
  || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
  isMobile = true;
}

/* Rest APIs */
window.get_grid_data = ( action, grid_id) => {
  return jQuery.ajax({
    type: "POST",
    data: JSON.stringify({ action: action, parts: jsObject.parts, grid_id: grid_id }),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
    beforeSend: function (xhr) {
      xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )

      if(action === 'by_region') {
        jQuery('#selRegion').prop('disabled', true)
        jQuery('#loader').addClass('show')
        jQuery('#loader #loaderText').html('Loading ' + jQuery('#selRegion option:selected').text() + ' church data...')
      }
    }
  })
    .fail(function(e) {
      jQuery('#error').html(e)
    })
}

window.get_activity_data = (grid_id) => {
  let offset = new Date().getTimezoneOffset();
  return jQuery.ajax({
    type: "POST",
    data: JSON.stringify({ action: 'activity_data', parts: jsObject.parts, grid_id: grid_id, offset: offset }),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
    beforeSend: function (xhr) {
      xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
    }
  })
    .fail(function(e) {
      jQuery('#error').html(e)
    })
}

window.geoJSONs = {
  type: 'FeatureCollection',
  features: []
}

window.selectedGeoJSONs = {
  type: 'FeatureCollection',
  features: []
}

window.regionGridData = []
window.gridData = {}

window.countryRegions = [
  { name: 'Bangladesh', region: 'asia'},
  { name: 'Bhutan', region: 'asia'},
  { name: 'British Indian Ocean Territory', region: 'asia'},
  { name: 'Brunei', region: 'asia'},
  { name: 'Cambodia', region: 'asia'},
  { name: 'China', region: 'asia'},
  { name: 'Hong Kong', region: 'asia'},
  { name: 'India', region: 'asia'},
  { name: 'Indonesia', region: 'asia'},
  { name: 'Japan', region: 'asia'},
  { name: 'Kazakhstan', region: 'asia'},
  { name: 'North Korea', region: 'asia'},
  { name: 'South Korea', region: 'asia'},
  { name: 'Kyrgyzstan', region: 'asia'},
  { name: 'Laos', region: 'asia'},
  { name: 'Macao', region: 'asia'},
  { name: 'Malaysia', region: 'asia'},
  { name: 'Maldives', region: 'asia'},
  { name: 'Mongolia', region: 'asia'},
  { name: 'Myanmar (Burma)', region: 'asia'},
  { name: 'Nepal', region: 'asia'},
  { name: 'Paracel Islands', region: 'asia'},
  { name: 'Philippines', region: 'asia'},
  { name: 'Singapore', region: 'asia'},
  { name: 'Sri Lanka', region: 'asia'},
  { name: 'Taiwan', region: 'asia'},
  { name: 'Tajikistan', region: 'asia'},
  { name: 'Thailand', region: 'asia'},
  { name: 'Turkmenistan', region: 'asia'},
  { name: 'Uzbekistan', region: 'asia'},
  { name: 'Vietnam', region: 'asia'},
  { name: 'Afghanistan', region: 'middle-east'},
  { name: 'Algeria', region: 'middle-east'},
  { name: 'Azerbaijan', region: 'middle-east'},
  { name: 'Bahrain', region: 'middle-east'},
  { name: 'Egypt', region: 'middle-east'},
  { name: 'Iran', region: 'middle-east'},
  { name: 'Iraq', region: 'middle-east'},
  { name: 'Israel', region: 'middle-east'},
  { name: 'Jordan', region: 'middle-east'},
  { name: 'Kuwait', region: 'middle-east'},
  { name: 'Lebanon', region: 'middle-east'},
  { name: 'Libya', region: 'middle-east'},
  { name: 'Morocco', region: 'middle-east'},
  { name: 'Oman', region: 'middle-east'},
  { name: 'Pakistan', region: 'middle-east'},
  { name: 'Palestina', region: 'middle-east'},
  { name: 'Qatar', region: 'middle-east'},
  { name: 'Saudi Arabia', region: 'middle-east'},
  { name: 'Somalia', region: 'middle-east'},
  { name: 'Syria', region: 'middle-east'},
  { name: 'Tunisia', region: 'middle-east'},
  { name: 'Turkey', region: 'middle-east'},
  { name: 'United Arab Emirates', region: 'middle-east'},
  { name: 'Yemen', region: 'middle-east'},
  { name: 'Albania', region: 'europe'},
  { name: 'Gibraltar', region: 'europe'},
  { name: 'Isle of Man', region: 'europe'},
  { name: 'Åland', region: 'europe'},
  { name: 'Andorra', region: 'europe'},
  { name: 'Akrotiri and Dhekelia', region: 'europe'},
  { name: 'Armenia', region: 'europe'},
  { name: 'Austria', region: 'europe'},
  { name: 'Belarus', region: 'europe'},
  { name: 'Belgium', region: 'europe'},
  { name: 'Bosnia and Herzegovina', region: 'europe'},
  { name: 'Bulgaria', region: 'europe'},
  { name: 'Croatia', region: 'europe'},
  { name: 'Cyprus', region: 'europe'},
  { name: 'Northern Cyprus', region: 'europe'},
  { name: 'Czech Republic', region: 'europe'},
  { name: 'Denmark', region: 'europe'},
  { name: 'Estonia', region: 'europe'},
  { name: 'Faroe Islands', region: 'europe'},
  { name: 'Finland', region: 'europe'},
  { name: 'France', region: 'europe'},
  { name: 'Guernsey', region: 'europe'},
  { name: 'Georgia', region: 'europe'},
  { name: 'Germany', region: 'europe'},
  { name: 'Greece', region: 'europe'},
  { name: 'Hungary', region: 'europe'},
  { name: 'Iceland', region: 'europe'},
  { name: 'Ireland', region: 'europe'},
  { name: 'Italy', region: 'europe'},
  { name: 'Jersey', region: 'europe'},
  { name: 'Kosovo', region: 'europe'},
  { name: 'Latvia', region: 'europe'},
  { name: 'Liechtenstein', region: 'europe'},
  { name: 'Lithuania', region: 'europe'},
  { name: 'Luxembourg', region: 'europe'},
  { name: 'Macedonia', region: 'europe'},
  { name: 'Malta', region: 'europe'},
  { name: 'Moldova', region: 'europe'},
  { name: 'Monaco', region: 'europe'},
  { name: 'Montenegro', region: 'europe'},
  { name: 'Netherlands', region: 'europe'},
  { name: 'Norway', region: 'europe'},
  { name: 'Poland', region: 'europe'},
  { name: 'Portugal', region: 'europe'},
  { name: 'Romania', region: 'europe'},
  { name: 'Russia', region: 'europe'},
  { name: 'San Marino', region: 'europe'},
  { name: 'Serbia', region: 'europe'},
  { name: 'Slovakia', region: 'europe'},
  { name: 'Slovenia', region: 'europe'},
  { name: 'Spain', region: 'europe'},
  { name: 'Sweden', region: 'europe'},
  { name: 'Switzerland', region: 'europe'},
  { name: 'Svalbard and Jan Mayen', region: 'europe'},
  { name: 'Ukraine', region: 'europe'},
  { name: 'United Kingdom', region: 'europe'},
  { name: 'Vatican City', region: 'europe'},
  { name: 'Canada', region: 'north-america'},
  { name: 'Saint Pierre and Miquelon', region: 'north-america'},
  { name: 'Greenland', region: 'north-america'},
  { name: 'Mexico', region: 'north-america'},
  { name: 'United States', region: 'north-america'},
  { name: 'Aruba', region: 'central-america'},
  { name: 'Anguilla', region: 'central-america'},
  { name: 'Antigua and Barbuda', region: 'central-america'},
  { name: 'Bahamas', region: 'central-america'},
  { name: 'Bermuda', region: 'central-america'},
  { name: 'Barbados', region: 'central-america'},
  { name: 'British Virgin Islands', region: 'central-america'},
  { name: 'Belize', region: 'central-america'},
  { name: 'Cayman Islands', region: 'central-america'},
  { name: 'Costa Rica', region: 'central-america'},
  { name: 'Cocos Islands', region: 'central-america'},
  { name: 'Cuba', region: 'central-america'},
  { name: 'Curaçao', region: 'central-america'},
  { name: 'Dominica', region: 'central-america'},
  { name: 'Dominican Republic', region: 'central-america'},
  { name: 'El Salvador', region: 'central-america'},
  { name: 'Grenada', region: 'central-america'},
  { name: 'Guatemala', region: 'central-america'},
  { name: 'Guadeloupe', region: 'central-america'},
  { name: 'Haiti', region: 'central-america'},
  { name: 'Honduras', region: 'central-america'},
  { name: 'Jamaica', region: 'central-america'},
  { name: 'Martinique', region: 'central-america'},
  { name: 'Montserrat', region: 'central-america'},
  { name: 'Nicaragua', region: 'central-america'},
  { name: 'Panama', region: 'central-america'},
  { name: 'Puerto Rico', region: 'central-america'},
  { name: 'Saint Kitts and Nevis', region: 'central-america'}, // to cross check
  { name: 'Saint Lucia', region: 'central-america'},
  { name: 'Saint-Martin', region: 'central-america'},
  { name: 'Sint Maarten', region: 'central-america'},
  { name: 'Saint-Barthélemy', region: 'central-america'},
  { name: 'Sint Eustatius and Saba', region: 'central-america'},
  { name: 'Saint Vincent and the Grenadines', region: 'central-america'},
  { name: 'Trinidad and Tobago', region: 'central-america'},
  { name: 'Turks and Caicos Islands', region: 'central-america'},
  { name: 'Argentina', region: 'south-america'},
  { name: 'Bolivia', region: 'south-america'},
  { name: 'Brazil', region: 'south-america'},
  { name: 'Chile', region: 'south-america'},
  { name: 'Colombia', region: 'south-america'},
  { name: 'Ecuador', region: 'south-america'},
  { name: 'French Guiana', region: 'south-america'},
  { name: 'Falkland Islands', region: 'south-america'},
  { name: 'Guyana', region: 'south-america'},
  { name: 'Paraguay', region: 'south-america'},
  { name: 'Peru', region: 'south-america'},
  { name: 'South Georgia and the South Sandwich Islands', region: 'south-america'},
  { name: 'Suriname', region: 'south-america'},
  { name: 'Uruguay', region: 'south-america'},
  { name: 'Venezuela', region: 'south-america'},
  { name: 'Angola', region: 'africa'},
  { name: 'Saint Helena', region: 'africa'},
  { name: 'Benin', region: 'africa'},
  { name: 'Botswana', region: 'africa'},
  { name: 'Burkina Faso', region: 'africa'},
  { name: 'Burundi', region: 'africa'},
  { name: 'Cameroon', region: 'africa'},
  { name: 'Cape Verde', region: 'africa'},
  { name: 'Central African Republic', region: 'africa'}, // to cross check
  { name: 'Chad', region: 'africa'},
  { name: 'Comoros', region: 'africa'},
  { name: 'French Southern Territories', region: 'africa'},
  { name: 'Republic of Congo', region: 'africa'},
  { name: 'Democratic Republic of the Congo', region: 'africa'},
  { name: "Côte d'Ivoire", region: 'africa'},
  { name: 'Djibouti', region: 'africa'},
  { name: 'Equatorial Guinea', region: 'africa'},
  { name: 'Eritrea', region: 'africa'},
  { name: 'Ethiopia', region: 'africa'},
  { name: 'Gabon', region: 'africa'},
  { name: 'Gambia', region: 'africa'},
  { name: 'Ghana', region: 'africa'},
  { name: 'Guinea', region: 'africa'},
  { name: 'Guinea-Bissau', region: 'africa'},
  { name: 'Kenya', region: 'africa'},
  { name: 'Lesotho', region: 'africa'},
  { name: 'Liberia', region: 'africa'},
  { name: 'Madagascar', region: 'africa'},
  { name: 'Mayotte', region: 'africa'},
  { name: 'Malawi', region: 'africa'},
  { name: 'Mali', region: 'africa'},
  { name: 'Mauritania', region: 'africa'},
  { name: 'Mauritius', region: 'africa'},
  { name: 'Mozambique', region: 'africa'},
  { name: 'Namibia', region: 'africa'},
  { name: 'Niger', region: 'africa'},
  { name: 'Nigeria', region: 'africa'},
  { name: 'Rwanda', region: 'africa'},
  { name: 'Reunion', region: 'africa'},
  { name: 'São Tomé and Príncipe', region: 'africa'},
  { name: 'Senegal', region: 'africa'},
  { name: 'Seychelles', region: 'africa'},
  { name: 'Sierra Leone', region: 'africa'},
  { name: 'South Africa', region: 'africa'},
  { name: 'South Sudan', region: 'africa'},
  { name: 'Sudan', region: 'africa'},
  { name: 'Swaziland', region: 'africa'},
  { name: 'Tanzania', region: 'africa'},
  { name: 'Togo', region: 'africa'},
  { name: 'Western Sahara', region: 'africa'},
  { name: 'Uganda', region: 'africa'},
  { name: 'Zambia', region: 'africa'},
  { name: 'Zimbabwe', region: 'africa'},
  { name: 'Australia', region: 'australia'},
  { name: 'Christmas Island', region: 'australia'},
  { name: 'Cook Islands', region: 'australia'},
  { name: 'East Timor', region: 'australia'},
  { name: 'Timor-Leste', region: 'asia'},
  { name: 'Fiji', region: 'australia'},
  { name: 'French Polynesia', region: 'australia'},
  { name: 'Guam', region: 'australia'},
  { name: 'Kiribati', region: 'australia'},
  { name: 'Marshall Islands', region: 'australia'},
  { name: 'Micronesia', region: 'australia'},
  { name: 'Nauru', region: 'australia'},
  { name: 'New Zealand', region: 'australia'},
  { name: 'New Caledonia', region: 'australia'},
  { name: 'Norfolk Island', region: 'australia'},
  { name: 'Northern Mariana Islands', region: 'australia'},
  { name: 'Niue', region: 'australia'},
  { name: 'Palau', region: 'australia'},
  { name: 'Pitcairn Islands', region: 'australia'},
  { name: 'Papua New Guinea', region: 'australia'},
  { name: 'Samoa', region: 'australia'},
  { name: 'American Samoa', region: 'australia'},
  { name: 'Solomon Islands', region: 'australia'},
  { name: 'Tonga', region: 'australia'},
  { name: 'Tokelau', region: 'australia'},
  { name: 'Tuvalu', region: 'australia'},
  { name: 'United States Minor Outlying Islands', region: 'australia'},
  { name: 'Vanuatu', region: 'australia'},
  { name: 'Wallis and Futuna', region: 'australia'}
]

window.regionData = [
  { name: 'asia', lng: 102.74741167307525, lat: 28.44719132311866, zoom: 2.7346873131042644},
  { name: 'middle-east', lng: 32.26661777996014, lat: 34.57477444123633, zoom: 3.068706832225955 },
  { name: 'europe', lng: 11.320486131725147, lat: 55.859496460893325, zoom: 3.0687068322259554},
  { name: 'north-america', lng: -90.19175152320781, lat: 40.94930693641777, zoom: 3.0687068322259554 },
  { name: 'central-america', lng: -75.7419914449971, lat: 16.95118750373335, zoom: 4.070765389591025},
  { name: 'south-america', lng: -66.27175287630882, lat: -28.0398334719848, zoom: 2.7346873131042657},
  { name: 'africa', lng: 14.619184932475264, lat: 0.0014670487746286653, zoom: 3.0687068322259554},
  { name: 'australia', lng: 146.28146007106534, lat: -22.821329682728035, zoom: 2.9073213104280216},
]

window.map = null

window.fns = {
  pop: {
    update: {
      confirm() {
        const payload = {
          grid_id: this.data.current.grid_id,
          population: $('#txtPopEdit').val()
        }

        if(payload.population === '') { return }

        jQuery.ajax({
          type: "POST",
          url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
          contentType: "application/json; charset=utf-8",
          dataType: 'json',
          data: JSON.stringify({
            action: 'update_population',
            parts: jsObject.parts,
            grid_id: payload.grid_id,
            population: payload.population
          }),
          cache: true,
          beforeSend: function (xhr) {
            xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )

            $('#loadingMdlPopEdit').addClass('show')
          }
        })
        .done(function(x){
          window.geoJSONs.features.find(x => x.id === payload.grid_id.toString()).properties.population = payload.population.toLocaleString('en-US')
          $('#loadingMdlPopEdit').removeClass('show')
          $('#modal_population').html(payload.population.toLocaleString('en-US') + ' <i class="fi-page-edit" onclick="fns.pop.update.show(' + payload.population + ', ' + payload.grid_id + ')"></i>');
          $('#population').html(payload.population.toLocaleString('en-US'))
          window.fns.pop.update.hide()
          refreshMapLayer()
        })
        .fail(function(err, sss, et) {
          console.log(err)
          console.log(sss)
          console.log(et)
        })

      },

      show(population, grid_id) {
        $('#wrapMdlPop').hide()
        $('#wrapMdlPopEdit').show()
        $('#txtPopEdit').val(population)

        this.data.current = {
          population,
          grid_id
        }
      },

      hide() {
        $('#wrapMdlPop').show()
        $('#wrapMdlPopEdit').hide()
      },

      data: {
        current: {
          population: 0,
          grid_id: 0
        },
      }
    }
  }
}

/* Document Ready && Precache */
jQuery(document).ready(function($){
  clearInterval(window.fiveMinuteTimer)

  let slider_width = 335
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
    .off-canvas.position-right {
      width:${slider_width}px;
      background-color:white;
    }
    #initialize-screen {
      height: ${window.innerHeight}px !important;
    }
    #welcome-modal {
      height: ${window.innerHeight - 30}px !important;
    }
    #map-sidebar-wrapper {
      height: ${window.innerHeight}px !important;
    }

    .js-off-canvas-overlay.is-overlay-fixed {
      //backdrop-filter: blur(10px);
    }

  `)

  $('#custom-style-portal').empty().append(`
    #wrapper {
      height: ${window.innerHeight-60}px !important;
    }
    #map-wrapper {
      height: ${window.innerHeight-60}px !important;
    }
    #map {
      height: ${window.innerHeight-60}px !important;
    }
    .off-canvas.position-right {
      width:${slider_width}px;
      background-color:white;
    }
    #initialize-screen {
      height: ${window.innerHeight-60}px !important;
    }
    #welcome-modal {
      height: ${window.innerHeight - 30}px !important;
    }
    #map-sidebar-wrapper {
      height: ${window.innerHeight-60}px !important;
    }

  `)

  // Map Region Control Toggle Button
  jQuery('#btnBurger').click(function() {
    const cb = jQuery('.control-board')

    cb.hasClass('show')
      ? cb.removeClass('show')
      : cb.addClass('show')
  });


  jQuery('#btnGetGridData').click(function() {
    getGridDataByRegion()
  });

  jQuery('#selRegion').change(function(value) {
    // configSelectedRegion()
  });

  let initialize_screen = jQuery('.initialize-progress')

  // preload all geojson
  let asset_list = []
  var i = 1;
  while( i <= 45 ){
    asset_list.push({ name: i+'.geojson', loaded: false })
    i++
  }

  let loop = 0
  let list = 0
  window.load_map_triggered = 0

  initMap() // initialize the map

  jQuery.each(asset_list, function(i,v) {

    jQuery.ajax({
      url: jsObject.mirror_url + 'tiles/world/saturation/' + v.name,
      dataType: 'json',
      data: null,
      cache: true,
      beforeSend: function (xhr) {
        if (xhr.overrideMimeType) {
          xhr.overrideMimeType("application/json");
        }
      }
    }).done(function(x){
        window.geoJSONs.features.push(...x.features)

        asset_list[i].loaded = true
        const loaded = checkGeoJSONLoaded(asset_list)

        loop++
        initialize_screen.val(loop)

        if ( 5 === loop ) {
          jQuery('#initialize-people').show()
        }

        if ( 15 === loop ) {
          jQuery('#initialize-activity').show()
        }

        if ( 22 === loop ) {
          jQuery('#initialize-coffee').show()
        }

        if ( 40 === loop ) {
          //jQuery('#initialize-dothis').show()
          jQuery('#initialize-churchdata').show()
        }

        if ( loop > 44 && list > 0 && window.load_map_triggered !== 1 ) {
          window.load_map_triggered = 1
          jQuery('#initialize-screen').fadeOut();
        }

        if(loaded) {
          getGridData()
        }

      })
      .fail(function(){
        loop++
      })
  })
}) /* .ready() */

function getGridData() {
  const grid_id = window.countryRegions.filter(cr => cr.region === jQuery('#selRegion').val()).map(cr => cr.name)
  window.get_grid_data( 'grid_data', grid_id)
    .done(function(x){
      window.gridData = { ...x }
      jQuery('#initialize-dothis').show()
      configGeoJSONRegions(true)

      setTimeout(function() {
        jQuery('#initialize-screen').fadeOut();
      }, 300)
    })
    .fail(function(err){
      console.group('Error getting grid data')
      console.log(err.responseText)
      console.groupEnd()

      jsObject.grid_data = {'data': {}, 'highest_value': 1 }
    })
}

function getGridDataByRegion() {
  jQuery.ajax({
    type: "POST",
    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
    contentType: "application/json; charset=utf-8",
    dataType: 'json',
    data: JSON.stringify({
      action: 'by_region',
      parts: jsObject.parts,
      grid_id: window.countryRegions.filter(cr => cr.region === jQuery('#selRegion').val()).map(cr => cr.name)
    }),
    cache: true,
    beforeSend: function (xhr) {
      xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )

      jQuery('#selRegion').prop('disabled', true)
      jQuery('#loader').addClass('show')
      jQuery('#loader #loaderText').html('Loading ' + jQuery('#selRegion option:selected').text() + ' church data...')
    }
  })
  .done(function(x){
    jQuery('#selRegion').prop('disabled', false)
    jQuery('#loader').removeClass('show')

    window.regionGridData = [...x]
    configHeat()
  })
  .fail(function(err, sss, et) {
    console.log(err)
    console.log(sss)
    console.log(et)
  })
}

function configGeoJSONRegions(init = false) {
  console.log('config geo json regions')
  window.geoJSONs.features.forEach((gj) => {
    let country = gj.properties.full_name.split(',').pop().trim()

    if(country === 'U.S.') {
      country = 'United States'
    }

    if(country === 'Myanmar') {
      country = 'Myanmar (Burma)'
    }

    const r = window.countryRegions.find(cr => cr.name === country)

    if(r !== undefined) {
      gj.properties.region = r.region
      gj.properties.value = 0
    } else {
      console.log(country)
    }
  })

  configHeat()
}

function configSelectedRegion(init = false) {
  window.selectedGeoJSONs.features = [
    ...window.geoJSONs.features.filter((gj) => gj.properties.region === jQuery('#selRegion').val())
  ]

  refreshMapLayer()
  flyMap()
}

function configHeat() {
  console.log('config heat')
  const geojsons = window.geoJSONs

  const rgd = window.regionGridData // region grid data
  const gridData = window.gridData.data

  geojsons.features = geojsons.features.map(gj => {
    const gd = gridData[gj.id]

    const population = parseInt(gd.population.replace(/,/g, ''))
    const needed = Math.ceil(population / 1000)
    const percentage = (gd.reported / needed * 100).toFixed()

    gj.properties.value = percentage > 100 ? 100 : parseInt(percentage)
    gj.properties.reported = parseInt(gd.reported)
    gj.properties.population = population
    gj.properties.needed = needed
    gj.properties.percentage = parseInt(percentage)
    
    return gj
  })

  refreshMapLayer()

  // configSelectedRegion()
}

function checkGeoJSONLoaded(al) {
  return al.every((a) => {
    if(!a.loaded) {
      loaded = false
      return false
    }

    return true
  })
}

// Config : Map

function initMap() {
  let center = [-98, 38.88]
  let maxzoom = jsObject.zoom

  mapboxgl.accessToken = jsObject.map_key;

  window.map  = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/discipletools/cl1qp8vuf002l15ngm5a7up59',
    center: center,
    minZoom: 2,
    maxZoom: maxzoom,
    zoom: 3
  });

  configMap()
}

function refreshMapLayer() {
  const map = window.map

  const refreshLayers = function() {
    if(map.getLayer('default-line')) {
      map.removeLayer('default-line')
    }
  
    if(map.getLayer('hover-fills')) {
      map.removeLayer('hover-fills')
    }

    if (window.map.getLayer('satuation-fills')) {
      window.map.removeLayer('satuation-fills')
    }
  
    if(map.getSource('churches')) {
      map.removeSource('churches')
    }
  
    //const geoJSONs = window.selectedGeoJSONs 
    const geoJSONs = window.geoJSONs 
  
    map.addSource('churches', {
      'type': 'geojson',
      'data': geoJSONs
    })
  
    // Add Default Layer
    map.addLayer({
      'id': 'default-line',
      'type': 'line',
      'source': 'churches',
      'paint': {
        'line-color': 'grey',
        'line-width': .5
      }
    })
  
    // Add Hover Layer 
    map.addLayer({
      'id': 'hover-fills',
      'type': 'fill',
      'source': 'churches',
      'paint': {
        'fill-color': '#ffffff',
        'fill-opacity': [
          'case',
          ['boolean', ['feature-state', 'hover'], false],
          .1,
          0 
        ]
      }
    })

    // Add Satuation Layer
    window.map.addLayer({
      'id': 'satuation-fills',
      'type': 'fill',
      'source': 'churches',
      'paint': {
        'fill-color': {
          property: 'value',
          stops: [
            [0, '#ffffff'],
            [20, '#ba1200'],
            [40, '#cc441c'],
            [60, '#f7c162'],
            [80, '#78945f'],
            [100, '#4a7c59']
          ]
        },
  
        'fill-opacity': 0.75,
        'fill-outline-color': '#888888'
      }
    })
  }

  refreshLayers()

  /*
  if(map.loaded()) {
    refreshLayers()
  } else {
    map.on('load', () => {
      refreshLayers()
    })
  }
  */
}

function flyMap() {
  const crd = window.regionData.find(rd => rd.name === jQuery('#selRegion').val())
  window.map.flyTo({
    center: [crd.lng, crd.lat],
    zoom: crd.zoom,
    speed: 1,
    curve: 1
  })
}

function configMap() {
  let geojsons = window.selectedGeoJSONs
  let map = window.map

  // config title
  $('#panel-type-title').html(jsObject.translation.title)

  // config for desktop
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

  // config drag and touch
  map.dragRotate.disable();
  map.touchZoomRotate.disableRotation();

  // config bounds
  if ( get_map_start( 'heatmap_zoom_memory' ) ) {
    map.fitBounds(get_map_start( 'heatmap_zoom_memory' ))
  }

  // config zoom end
  map.on('zoomend', function(){
    set_map_start( 'heatmap_zoom_memory', map.getBounds() )
  })

  // remove mouse hover state
  window.previous_hover = false

  // config map on load
  map.on('load', function () {

    // config custom marks
    if ( jsObject.custom_marks.length > 0 ) {
      jQuery.each( jsObject.custom_marks, function(i,v) {
        new mapboxgl.Marker()
          .setLngLat([v.lng, v.lat])
          .addTo(map);
      })
    }

    // config mouse move
    map.on('mousemove', 'hover-fills', function (e) {
      const cgd = e.features[0]
      const prop = cgd.properties

      if ( window.previous_hover ) {
        map.setFeatureState(
          window.previous_hover,
          { hover: false }
        )
        hide_details_panel()
      }

      window.previous_hover = { source: 'churches', id: e.features[0].id }

      if (e.features.length > 0) {
        show_details_panel()
        map.setFeatureState(
          window.previous_hover,
          { hover: true }
        );
        $('#title').html(e.features[0].properties.full_name)

        // get stat values from jsObject and format
        const stats = {
          needed: prop.needed === undefined ? 0 : prop.needed,
          reported: prop.reported === undefined ? 0 : prop.reported,
          get percent() {
            return ((this.reported / (this.needed === 0 ? 1 : this.needed) * 100).toFixed(2))
          },

          get progress() {
            return isNaN(this.percent) ? 0 : (this.percent > 100 ? '100' : this.percent)
          }
        }

        // Population
        $('#population').html(prop.population.toLocaleString('en-US'))
        
        // Needed
        $('#needed').html(stats.needed.toLocaleString('en-US'))

        // Reported 
        $('#reported').html(parseInt(stats.reported).toLocaleString('en-US'))
        
        // Goal
        $('#saturation-goal').html(isNaN(stats.percent) ? 0 : stats.percent)

        // Progress Bar
        $('#meter').val(stats.progress)

        //report
        $('#report-modal-title').val(e.features[0].properties.full_name)
        $('#report-grid-id').val(e.features[0].properties.grid_id)

      }
    });

    // config mouse click
    map.on('click', 'hover-fills', function (e) {
      const cgd = e.features[0]
      const prop = cgd.properties

      $('#modal_tile').html(e.features[0].properties.full_name)
      $('#modal_population').html(prop.population.toLocaleString('en-US'))

      loadPopulation(prop.population, prop.grid_id)

      jQuery('.temp-spinner').html(`<span class="loading-spinner active"></span>`)

      window.get_grid_data( 'self', e.features[0].properties.grid_id )
        .done(function(data){
          load_self_content( data )
        })
      window.get_grid_data( 'a0', e.features[0].properties.grid_id )
        .done(function(data){
          load_level_content( data, 'a0' )
        })
      window.get_grid_data( 'a1', e.features[0].properties.grid_id )
        .done(function(data){
          load_level_content( data, 'a1' )
        })
      window.get_grid_data( 'a2', e.features[0].properties.grid_id )
        .done(function(data){
          load_level_content( data, 'a2' )
        })
      window.get_grid_data( 'a3', e.features[0].properties.grid_id )
        .done(function(data){
          load_level_content( data, 'a3' )
        })
      window.get_grid_data( 'world', e.features[0].properties.grid_id )
        .done(function(data){
          load_level_content( data, 'world' )
        })

	    /*
      let ac = $('#activity-content')
      ac.html('<span class="loading-spinner active"></span>')
      window.get_activity_data(e.features[0].properties.grid_id)
        .done(function(data){
          ac.empty()
          if ( data.length < 1 ) {
            ac.append(`<div>No Movement Activity</div>`)
          } else {
            $.each(data, function(i,v){
              if ( typeof v.message !== 'undefined' ){
                ac.append(`<div><div style="float:left;width:180px;"><strong>${v.formatted_time}</strong></div> <span>${v.message}</span></div>`)
              }
            })
          }
        })
      */

      $('#offCanvasNestedPush').foundation('toggle', e);
    });
  })
}

function updatePop() {
}


// e.o Config : Map


/**************************
 * Load map when precache is complete
 **************************/
function load_map() {
  let geojsons = window.geoJSONs
  console.log('load map');

  // set title
  $('#panel-type-title').html(jsObject.translation.title)

  $('.loading-spinner').removeClass('active')

  let map = window.map

  /*
    let center = [-98, 38.88]
    let maxzoom = jsObject.zoom
    // if ( 'contacts' === jsObject.post_type ) {
    //   maxzoom = 13
    // }
  
    mapboxgl.accessToken = jsObject.map_key;
  
    let map = new mapboxgl.Map({
      container: 'map',
      style: 'mapbox://styles/discipletools/cl1qp8vuf002l15ngm5a7up59',
      center: center,
      minZoom: 2,
      maxZoom: maxzoom,
      zoom: 3
    });
  */

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

  if ( get_map_start( 'heatmap_zoom_memory' ) ) {
    map.fitBounds(get_map_start( 'heatmap_zoom_memory' ))
  }

  map.on('zoomend', function(){
    set_map_start( 'heatmap_zoom_memory', map.getBounds() )
  })

  window.previous_hover = false

  return;

  // add features
  jQuery.each(geojsons.features, function (i, v) {
    if (typeof jsObject.grid_data.data[v.id] !== 'undefined') {
      let percentageCalculation = parseInt((((jsObject.grid_data.data[v.id].reported).toString().replace(/,/g, '') / Math.ceil((jsObject.grid_data.data[v.id].needed).toString().replace(/,/g, '') / 2)) * 100).toFixed())
      geojsons.features[i].properties.value = percentageCalculation > 100 ? 100 : percentageCalculation
    } else {
      geojsons.features[i].properties.value = 0
    }
  });

  // Map on Load
  map.on('load', function () {
    //jQuery('#initialize-loadingmap').show()

    // Add Source
    map.addSource('churches', {
      'type': 'geojson',
      'data': geojsons
    });

    // Add Default Layer
    map.addLayer({
      'id': 'default-line',
      'type': 'line',
      'source': 'churches',
      'paint': {
        'line-color': 'grey',
        'line-width': .5
      }
    }); // e.o Add Default Layer

    // Add Hover Layer
    map.addLayer({
      'id': 'hover-fills',
      'type': 'fill',
      'source': 'churches',
      'paint': {
        'fill-color': 'black',
        'fill-opacity': [
          'case',
          ['boolean', ['feature-state', 'hover'], false],
          .8,
          0
        ]
      }
    }) // e.o Add Hover Layer

    // Add Saturation Layer
    map.addLayer({
      'id': 'satuation-fills',
      'type': 'fill',
      'source': 'churches',
      'paint': {
        'fill-color': {
          property: 'value',
          stops: [
            [0, '#ffffff'],
            [20, '#ba1200'],
            [40, '#cc441c'],
            [60, '#f7c162'],
            [80, '#78945f'],
            [100, '#4a7c59']
          ]
        },

        'fill-opacity': 0.75,
        'fill-outline-color': '#777777'
      }
    }) // e.o Add Satuation Layer

    // Set Custom Marks
    if ( jsObject.custom_marks.length > 0 ) {
      jQuery.each( jsObject.custom_marks, function(i,v) {
        new mapboxgl.Marker()
          .setLngLat([v.lng, v.lat])
          .addTo(map);
      })
    } // e.o Set Custom Marks

    // On Mouse Hover / Move
    map.on('mousemove', 'hover-fills', function (e) {
      if ( window.previous_hover ) {
        map.setFeatureState(
          window.previous_hover,
          { hover: false }
        )
        hide_details_panel()
      }

      window.previous_hover = { source: 'churches', id: e.features[0].id }

      if (e.features.length > 0) {
        show_details_panel()
        map.setFeatureState(
          window.previous_hover,
          {hover: true}
        );
        $('#title').html(e.features[0].properties.full_name)

        // get stat values from jsObject and format
        const stats = {
          needed: Math.ceil((jsObject.grid_data.data[e.features[0].properties.grid_id].needed).toString().replace(/,/g, '') / 2),
          reported: (jsObject.grid_data.data[e.features[0].properties.grid_id].reported).toString().replace(/,/g, ''),
          get percent() {
            return ((this.reported / this.needed) * 100).toFixed(2)
          },

          get progress() {
            return this.percent > 100 ? '100' : this.percent
          }
        }

        // Population
        $('#population').html(jsObject.grid_data.data[e.features[0].properties.grid_id].population)
        
        // Needed
        $('#needed').html(stats.needed.toLocaleString('en-US'))

        // Reported 
        $('#reported').html(parseInt(stats.reported).toLocaleString('en-US'))
        
        // Goal
        $('#saturation-goal').html(stats.percent)

        // Progress Bar
        $('#meter').val(stats.progress)

        //report
        $('#report-modal-title').val(e.features[0].properties.full_name)
        $('#report-grid-id').val(e.features[0].properties.grid_id)

      }
    }); // e.o On Mouse Hover / Move

    // On Mouse Click
    map.on('click', 'hover-fills', function (e) {
      $('#modal_tile').html(e.features[0].properties.full_name)
      $('#modal_population').html(jsObject.grid_data.data[e.features[0].properties.grid_id].population)

      jQuery('.temp-spinner').html(`<span class="loading-spinner active"></span>`)

      window.get_grid_data( 'self', e.features[0].properties.grid_id )
        .done(function(data){
          load_self_content( data )
        })
      window.get_grid_data( 'a0', e.features[0].properties.grid_id )
        .done(function(data){
          load_level_content( data, 'a0' )
        })
      window.get_grid_data( 'a1', e.features[0].properties.grid_id )
        .done(function(data){
          load_level_content( data, 'a1' )
        })
      window.get_grid_data( 'a2', e.features[0].properties.grid_id )
        .done(function(data){
          load_level_content( data, 'a2' )
        })
      window.get_grid_data( 'a3', e.features[0].properties.grid_id )
        .done(function(data){
          load_level_content( data, 'a3' )
        })
      window.get_grid_data( 'world', e.features[0].properties.grid_id )
        .done(function(data){
          load_level_content( data, 'world' )
        })

      let ac = $('#activity-content')
      ac.html('<span class="loading-spinner active"></span>')
      window.get_activity_data(e.features[0].properties.grid_id)
        .done(function(data){
          ac.empty()
          if ( data.length < 1 ) {
            ac.append(`<div>No Movement Activity</div>`)
          } else {
            $.each(data, function(i,v){
              if ( typeof v.message !== 'undefined' ){
                ac.append(`<div><div style="float:left;width:180px;"><strong>${v.formatted_time}</strong></div> <span>${v.message}</span></div>`)
              }
            })
          }
        })

      $('#offCanvasNestedPush').foundation('toggle', e);
    }); // e.o On Mouse Click
  }); // e.o Map on Load

  // Show when map loaded
  map.on('idle', function () {
    //jQuery('#initialize-screen').fadeOut();
  }); // e.o Show when map loaded

  return;

  let asset_list = []
  var i = 1;
  while( i <= 45 ){
    asset_list.push(i+'.geojson')
    i++
  }

  jQuery.each(asset_list, function(i,v){

    jQuery.ajax({
      url: jsObject.mirror_url + 'tiles/world/saturation/' + v,
      dataType: 'json',
      data: null,
      cache: true,
      beforeSend: function (xhr) {
        if (xhr.overrideMimeType) {
          xhr.overrideMimeType("application/json");
        }
      }
    })
      .done(function (geojson) {

        map.on('load', function() {

          jQuery.each(geojson.features, function (i, v) {
            if (typeof jsObject.grid_data.data[v.id] !== 'undefined' ) {
              let needed = Math.ceil((jsObject.grid_data.data[v.id].needed).toString().replace(/,/g, '') / 2)
              let reported = (jsObject.grid_data.data[v.id].reported).toString().replace(/,/g, '')
              let calcx = (((jsObject.grid_data.data[v.id].reported).toString().replace(/,/g, '') / Math.ceil((jsObject.grid_data.data[v.id].needed).toString().replace(/,/g, '') / 2)) * 100).toFixed(2)
              let perc = calcx > 100 ? 100 : calcx

              // geojson.features[i].properties.value = parseInt(jsObject.grid_data.data[v.id].percent)
              geojson.features[i].properties.value = perc
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
              /*
              'fill-color': {
                property: 'value',
                stops: [[0, 'rgba(0, 0, 0, 0)'], [0.01, 'rgb(50,205,50)'], [jsObject.grid_data.highest_value, 'rgb(0,128,0)']]
              },
              */
             'fill-color': '#ccc',
              'fill-opacity': 0.75,
              'fill-outline-color': '#006400'
            }
          })
          /**********/
          /* end fill map */
          /**********/

          if ( jsObject.custom_marks.length > 0 ) {
            let lnglat
            jQuery.each( jsObject.custom_marks, function(i,v) {
              // lnglat = new mapboxgl.LngLat( v.lng, v.lat )
              // console.log(lnglat)
              new mapboxgl.Marker()
                .setLngLat([v.lng, v.lat])
                .addTo(map);
            })
          }

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

              // get stat values from jsObject and format
              const stats = {
                needed: Math.ceil((jsObject.grid_data.data[e.features[0].properties.grid_id].needed).toString().replace(/,/g, '') / 2),
                reported: (jsObject.grid_data.data[e.features[0].properties.grid_id].reported).toString().replace(/,/g, ''),
                get percent() {
                  return ((this.reported / this.needed) * 100).toFixed(2)
                },

                get progress() {
                  return this.percent > 100 ? '100' : this.percent
                }
              }

              // Population
              $('#population').html(jsObject.grid_data.data[e.features[0].properties.grid_id].population)
              
              // Needed
              $('#needed').html(stats.needed.toLocaleString('en-US'))

              // Reported 
              $('#reported').html(parseInt(stats.reported).toLocaleString('en-US'))
              
              // Goal
              $('#saturation-goal').html(stats.percent)

              // Progress Bar
              $('#meter').val(stats.progress)

              //report
              $('#report-modal-title').val(e.features[0].properties.full_name)
              $('#report-grid-id').val(e.features[0].properties.grid_id)

            }
          });

          map.on('click', i.toString()+'fills', function (e) {
            $('#modal_tile').html(e.features[0].properties.full_name)
            $('#modal_population').html(jsObject.grid_data.data[e.features[0].properties.grid_id].population)

            jQuery('.temp-spinner').html(`<span class="loading-spinner active"></span>`)

            window.get_grid_data( 'self', e.features[0].properties.grid_id )
              .done(function(data){
                load_self_content( data )
              })
            window.get_grid_data( 'a0', e.features[0].properties.grid_id )
              .done(function(data){
                load_level_content( data, 'a0' )
              })
            window.get_grid_data( 'a1', e.features[0].properties.grid_id )
              .done(function(data){
                load_level_content( data, 'a1' )
              })
            window.get_grid_data( 'a2', e.features[0].properties.grid_id )
              .done(function(data){
                load_level_content( data, 'a2' )
              })
            window.get_grid_data( 'a3', e.features[0].properties.grid_id )
              .done(function(data){
                load_level_content( data, 'a3' )
              })
            window.get_grid_data( 'world', e.features[0].properties.grid_id )
              .done(function(data){
                load_level_content( data, 'world' )
              })

            let ac = $('#activity-content')
            ac.html('<span class="loading-spinner active"></span>')
            window.get_activity_data(e.features[0].properties.grid_id)
              .done(function(data){
                ac.empty()
                if ( data.length < 1 ) {
                  ac.append(`<div>No Movement Activity</div>`)
                } else {
                  $.each(data, function(i,v){
                    if ( typeof v.message !== 'undefined' ){
                      ac.append(`<div><div style="float:left;width:180px;"><strong>${v.formatted_time}</strong></div> <span>${v.message}</span></div>`)
                    }
                  })
                }
              })

            $('#offCanvasNestedPush').foundation('toggle', e);
          });

        })

      }) /* ajax call */
  }) /* for each loop */

} /* .preCache */

/**************************
Support Functions
***************************/

function show_details_panel(){
  $('#details-panel').show()
  $('#training-start-screen').hide()
}

function hide_details_panel(){
  $('#details-panel').hide()
}



