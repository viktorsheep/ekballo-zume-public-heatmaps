jQuery(document).ready(function($){
  window.new_report = ( action, form_data ) => {
    return jQuery.ajax({
      type: "POST",
      data: JSON.stringify({ action: action, parts: jsObject.parts, data: form_data }),
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

  let submit_button = $('#submit-new')
  function check_inputs(){
    submit_button.prop('disabled', false)
    jQuery.each($('.required'), function(){
      if ( $(this).val() === '' ) {
        $(this).addClass('redborder')
        submit_button.prop('disabled', true)
      }
      else {
        $(this).removeClass('redborder')
      }
    })
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

    let grid_id = jQuery('#report-grid-id').val()

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
      grid_id: grid_id
    }

    window.new_report( 'new_registration', form_data )
      .done(function(response){
        console.log(response)
        jQuery('.loading-spinner').removeClass('active')

        jQuery('#new-panel').empty().html(`
        Excellent! You've been sent an email with your personal reporting link.<br><br>
        <a class="button" href="${response}">Open Personal Reporting Portal</a>
        `)

      })
  })

  let submit_send_link = $('#submit-send-link')
  submit_send_link.on('click', function(){
    let spinner = jQuery('.loading-spinner')
    spinner.addClass('active')

    let submit_button = jQuery('#submit-send-link')
    submit_button.prop('disabled', true)

    let honey = jQuery('#email').val()
    if ( honey ) {
      submit_button.html('Shame, shame, shame. We know your name ... ROBOT!').prop('disabled', true )
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

    let form_data = {
      email: email,
    }

    window.new_report( 'send_link', form_data )
      .done(function(response){
        console.log(response)
        jQuery('.loading-spinner').removeClass('active')

        jQuery('#send-panel').empty().html(`
        Excellent! Go to you email inbox and find your personal link.<br>
        Use this link!<br>
        <a href="${response}">${response}</a>
        `)

      })
  })
})

