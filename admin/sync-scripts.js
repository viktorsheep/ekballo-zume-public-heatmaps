let recievedGeoJSONs = 0;

window.d = {
	geoJSONs: {
	  type: 'FeatureCollection',
	  features: []
	},
	churchData: [],
	churchCountData: [],
	counts: {
		batch: {
			total: 0,
			current: 0
		},
		locations: 0,
		log: 0,
		updated: 0
	},
	urls: {}
}

jQuery(document).ready(function($){
	// set api url
	d.urls.api = `${jsObject.baseURL}${jsObject.api}/${jsObject.version}/${jsObject.part}`
	updateBtnSyncText()
	setLastSyncedDate()
});

function checkGeoJSONLoaded(al) {
  return al.every((a) => {
    if(!a.loaded) {
      loaded = false
      return false
    }

    return true
  })
}

function getGeoJSONs() {
  let asset_list = []
  var i = 1;
  while( i <= 45 ){
    asset_list.push({ name: i+'.geojson', loaded: false })
    i++
  }

  let loop = 0
  let list = 0
  window.load_map_triggered = 0

	updateProgressText('Getting geojsons.')
	jQuery('#progGetGeoJson').prepend('<img src="/wp-admin/images/loading.gif" />')

  jQuery.each(asset_list, function(i,v) {
    jQuery.ajax({
      url: jsObject.mirror + 'tiles/world/saturation/' + v.name,
      dataType: 'json',
      data: null,
      cache: true,
      beforeSend: function (xhr) {
        if (xhr.overrideMimeType) {
          xhr.overrideMimeType("application/json");
        }
      }
    }).done(function(x){
			window.d.geoJSONs.features.push(...x.features)
			asset_list[i].loaded = true
			const loaded = checkGeoJSONLoaded(asset_list)
			updateProgressText(v.name + ' downloaded. (' + asset_list.filter(al => al.loaded === true).length + '/' + asset_list.length + ')')

			if(loaded) {
				d.counts.locations = d.geoJSONs.features.length
				updateProgressText('Get grid data (geojsons) completed.')
				updateProgressText('Total geojson files: ' + asset_list.length + ' files.')
				updateProgressText('Total grid data: ' + d.geoJSONs.features.length + ' locations.')
				jQuery('#progGetGeoJson').find('img').remove()
				jQuery('#progGetGeoJson').prepend('<span class="dashicons dashicons-yes"></span>')

				d.geoJSONs.features = paginate(d.geoJSONs.features, 5000)
				d.counts.batch.total = d.geoJSONs.features.length

				getChurchData()
			}
		})
		.fail(function(){
			loop++
		})
  })
}

function handleBtnSyncClicked() {
	displaySyncPanel()
	getGeoJSONs()
	// steps
	// get geojsons
	// get church data
	// sync
}

function getChurchData() {
	jQuery('#progGetChurchData').prepend('<img src="/wp-admin/images/loading.gif" />')
  jQuery.ajax({
    type: "POST",
    data: JSON.stringify({
			action: 'grid_data',
			parts: {
				root: "zume_app",
				type: "heatmap_1000",
		    meta_key: "",
		    public_key: "",
		    action: "",
		    post_id: "",
		    post_type: "groups",
		    instance_id: "" 
			}
		}),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
		url: d.urls.api,
    beforeSend: function (xhr) {
      // xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
			updateProgressText('Getting church data. This might take some minutes.')
    }
  })
  .done(function(x){
		updateProgressText('Get church data completed.')
		jQuery('#progGetChurchData').find('img').remove()
		jQuery('#progGetChurchData').prepend('<span class="dashicons dashicons-yes"></span>')

		const r = Object.values(x.data)

		d.churchData = [...r]
		sync()
  })
  .fail(function(err){
    console.group('Error getting grid data')
    console.log(err.responseText)
    console.groupEnd()

    jsObject.grid_data = {'data': {}, 'highest_value': 1 }
  })
}

function paginate(arr, size) {
	return arr.reduce((acc,val,i) => {
		let idx = Math.floor(i / size)
		let page = acc[idx] || (acc[idx] = [])
		page.push(val)

		return acc
	}, [])
}

function sync() {
	if(jQuery('#progSync').find('img').length < 1) {
		jQuery('#progSync').prepend('<img src="/wp-admin/images/loading.gif" />')
	}

	//if(d.counts.batch.current === (d.counts.batch.total - 1)) { return }

	const js = d.geoJSONs.features[d.counts.batch.current]

	const batchData = JSON.parse(JSON.stringify(d.geoJSONs.features[d.counts.batch.current]))

	const patchData = []

	updateProgressText('Matching batch ' + (d.counts.batch.current + 1) + ' data.')

	batchData.forEach((bd, idx) => {
		const churchData = window.d.churchData.find(cd => cd.grid_id === parseInt(bd.id))

		const population = churchData.population === '' ? '1' : churchData.population
		const reported = churchData.reported === 'undefined' ? 0 : churchData.reported

		patchData.push({
			name: bd.properties.full_name,
			grid_id: bd.id,
			population: population,
			reported: reported
		})
	})

	updateProgressText('Matching batch ' + (d.counts.batch.current + 1) + ' data complete.')
	updateProgressText('Updating patched data to database.')

  jQuery.ajax({
    type: "POST",
    data: JSON.stringify({
			action: 'sync',
			parts: {
				root: "zume_app",
				type: "heatmap_1000",
		    meta_key: "",
		    public_key: "",
		    action: "",
		    post_id: "",
		    post_type: "groups",
		    instance_id: "" 
			},
			batch: patchData
		}),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
		url: d.urls.api,
    beforeSend: function (xhr) {
      // xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
			updateProgressText('===')
			updateProgressText(`Updating batch ${(d.counts.batch.current + 1)} / ${d.counts.batch.total}` )
    }
  })
  .done(function(res){
		d.counts.updated = parseInt(res[0].count)
		if(res) {
			updateProgressText('===')
			updateProgressText('Updating batch ' + (d.counts.batch.current + 1) + ' successful.')
			updateProgressText(d.counts.updated + '/' + d.counts.locations)
			d.counts.batch.current++

			if(d.counts.batch.current < d.counts.batch.total) {
				sync()
			} else {
				updateProgressText('Update completed.')
				updateSyncCompletionSetting()
			} 
		} else {
			// todo: display error
		}
  })
  .fail(function(err){
    console.group('Error getting grid data')
    console.log(JSON.parse(err.responseText))
    console.groupEnd()

    jsObject.grid_data = {'data': {}, 'highest_value': 1 }
  })
}

function displaySyncPanel() {
	jQuery('#wrapSyncProgress').show()
	jQuery('#btnSync').text('Syncing').prop('disabled', true)
}

function updateProgressText(txt) {
	d.counts.log++
	const el = {
		log: jQuery('#txtProgressLog'),
		current: jQuery('#txtCurrentProgress'),
	}

	el.current.html(txt)
	el.log.prepend('<div>#' + d.counts.log + ': ' +  txt + '</div>')
}

function updateSyncCompletionSetting() {
  jQuery.ajax({
    type: "POST",
    data: JSON.stringify({
			action: 'update_sync_completion_setting',
			parts: {
				root: "zume_app",
				type: "heatmap_1000",
		    meta_key: "",
		    public_key: "",
		    action: "",
		    post_id: "",
		    post_type: "groups",
		    instance_id: "" 
			}
		}),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
		url: d.urls.api,
    beforeSend: function (xhr) {
      // xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
			updateProgressText('Updating sync setting.')
    }
  })
  .done(function(res){
		if(res) {
			updateProgressText('Sync completed.')
			jQuery('#progSync').find('img').remove()
			jQuery('#progSync').prepend('<span class="dashicons dashicons-yes"></span>')
			jQuery('#btnSync').text('Sync').prop('disabled', false)
			jQuery('#btnCloseSyncProgress').show()
			getZumeSettings()
		}
  })
  .fail(function(err){
    console.group('Error getting grid data')
    console.log(err.responseText)
    console.groupEnd()

    jsObject.grid_data = {'data': {}, 'highest_value': 1 }
  })
}

function resetSyncData() {
  jQuery.ajax({
    type: "POST",
    data: JSON.stringify({
			action: 'reset',
			parts: {
				root: "zume_app",
				type: "heatmap_1000",
		    meta_key: "",
		    public_key: "",
		    action: "",
		    post_id: "",
		    post_type: "groups",
		    instance_id: "" 
			},
		}),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
		url: d.urls.api,
    beforeSend: function (xhr) {
      // xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
			jQuery('#btnReset').prop('disabled', true)
    }
  })
  .done(function(res){
		if(res) {
			jQuery('#btnReset').prop('disabled', false)
			getZumeSettings()
		} else {
			// todo: display error
		}
  })
  .fail(function(err){
    console.group('Error getting grid data')
    console.log(err.responseText)
    console.groupEnd()

    jsObject.grid_data = {'data': {}, 'highest_value': 1 }
  })
}

function getZumeSettings() {
  jQuery.ajax({
    type: "POST",
    data: JSON.stringify({
			action: 'get_zume_settings',
			parts: {
				root: "zume_app",
				type: "heatmap_1000",
		    meta_key: "",
		    public_key: "",
		    action: "",
		    post_id: "",
		    post_type: "groups",
		    instance_id: "" 
			},
		}),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
		url: d.urls.api,
    beforeSend: function (xhr) {
      // xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
			jQuery('#btnGetZumeSettings').prop('disabled', true)
			jQuery('.wrap-detail .loader').show()
    }
  })
  .done(function(res){
		jQuery('#btnGetZumeSettings').prop('disabled', false)
		jQuery('.wrap-detail .loader').hide()
		settings = [...JSON.parse(res)]
		updateBtnSyncText()
		setLastSyncedDate()
  })
  .fail(function(err){
    console.group('Error getting grid data')
    console.log(err.responseText)
    console.groupEnd()

    jsObject.grid_data = {'data': {}, 'highest_value': 1 }
  })
}

function setLastSyncedDate() {
	const lsd = settings.find(s => s.name === 'last_synced_date')
	jQuery('#txtLastSynced').html(lsd.value === '' ? '<i style="color:#777;">Never</i>' : lsd.value)
	const is = settings.find(s => s.name === 'is_synced')
	jQuery('#txtChurchCountSyncStatus').html(is.value === 'false' ? '<span class="dashicons dashicons-dismiss" style="color: #cf4944;"></span> Church data count is not synced! The system will count every time the map loads.' : '<span class="dashicons dashicons-yes" style="color: #00a32a;"></span> Church data count is synced. The system will load from church count table when loading the map.')
}

function closeSyncProgress() {
	jQuery('#wrapSyncProgress').hide()
	jQuery('#txtProgressLog').html('')
	jQuery('#txtCurrentProgress').html('')
	d.counts.log = 0
	jQuery('#progSync span.dashicons').remove()
	jQuery('#progGetChurchData span.dashicons').remove()
	jQuery('#progGetGeoJson span.dashicons').remove()

	d.geoJSONs.features = []
	d.churchData = []

}

function updateBtnSyncText() {
	const is = settings.find(s => s.name === 'is_synced')
	if(is.value === 'true') {
		jQuery('#btnSync').text('Resync')
		jQuery('#btnGetChurchCountData').prop('disabled', false)
	} else {
		jQuery('#btnSync').text('Sync Now')
		jQuery('#btnGetChurchCountData').prop('disabled', true)
	}
}

function getChurchCountData() {
  jQuery.ajax({
    type: "POST",
    data: JSON.stringify({
			action: 'get_zume_church_counts',
			parts: {
				root: "zume_app",
				type: "heatmap_1000",
		    meta_key: "",
		    public_key: "",
		    action: "",
		    post_id: "",
		    post_type: "groups",
		    instance_id: "" 
			},
		}),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
		url: d.urls.api,
    beforeSend: function (xhr) {
      // xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
			jQuery('#btnGetChurchCountData').prop('disabled', true)
			jQuery('.wrap-detail .loader').show()
    }
  })
  .done(function(res){
		jQuery('#btnGetChurchCountData').prop('disabled', false)
		jQuery('.wrap-detail .loader').hide()
		displayChurchCountData(JSON.parse(res))
  })
  .fail(function(err){
    console.group('Error getting grid data')
    console.log(err.responseText)
    console.groupEnd()

    jsObject.grid_data = {'data': {}, 'highest_value': 1 }
  })
}

function displayChurchCountData(ccd) {
	const el = jQuery('#wrapChurchCountData')

	el.html('')
	const table = jQuery('<table />')
	table.append('<tr><th>Name</th><th>Grid ID</th><th>Population</th><th>Reported</th></tr>')
	ccd.forEach(c => {
		table.append(`<tr><td>${c.name}</td><td>${c.grid_id}</td><td>${c.population}</td><td>${c.reported}</td></tr>`)
	})
	el.html(table)
}

