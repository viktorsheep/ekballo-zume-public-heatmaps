(function() {
  "use strict";
  jQuery(document).ready(function() {

    // expand the current selected menu
    jQuery('#metrics-sidemenu').foundation('down', jQuery(`#${window.wp_js_object.base_slug}-menu`));


    show_template_overview()

  })

  function show_template_overview(){

    let localizedObject = window.wp_js_object // change this object to the one named in ui-menu-and-enqueue.php
    let translations = localizedObject.translations

    let chartDiv = jQuery('#chart') // retrieves the chart div in the metrics page

    chartDiv.empty().html(`
      <span class="section-header">${localizedObject.translations.title}</span>

      <hr style="max-width:100%;">

      <div id="chartdiv"></div>



    `)
  }
})();
