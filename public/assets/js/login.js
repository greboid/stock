(function($) {
  "use strict";
  var options = {
    'btn-loading': '<i class="fa fa-spinner fa-pulse"></i>',
    'btn-success': '<i class="fa fa-check"></i>',
    'btn-error': '<i class="fa fa-remove"></i>',
    'msg-success': 'All Good! Redirecting...',
    'msg-error': 'Wrong login credentials!',
    'useAJAX': false,
  };
  $("#login-form").validate({
    rules: {
      lg_username: "required",
      lg_password: "required",
    },
    errorClass: "form-invalid"
  });
  $("#login-form").submit(function() {
    remove_loading($(this));
    if(options['useAJAX'] == true) {
      dummy_submit_form($(this));
      return false;
    }
  });
  $("#register-form").validate({
    rules: {
      reg_username: "required",
      reg_password: {
        required: true,
        minlength: 5
        },
        reg_password_confirm: {
          required: true,
          minlength: 5,
          equalTo: "#register-form [name=reg_password]"
        },
        reg_email: {
        required: true,
          email: true
        },
        reg_agree: "required",
    },
    errorClass: "form-invalid",
    errorPlacement: function( label, element ) {
      if( element.attr( "type" ) === "checkbox" || element.attr( "type" ) === "radio" ) {
          element.parent().append( label ); // this would append the label after all your checkboxes/labels (so the error-label will be the last element in <div class="controls"> )
      } else {
        label.insertAfter( element ); // standard behaviour
      }
    }
  });
  $("#register-form").submit(function() {
    remove_loading($(this));
    if(options['useAJAX'] == true) {
      dummy_submit_form($(this));
      return false;
    }
  });
  $("#forgot-password-form").validate({
    rules: {
      fp_email: "required",
    },
    errorClass: "form-invalid"
  });
  $("#forgot-password-form").submit(function() {
    remove_loading($(this));
    if(options['useAJAX'] == true)
      dummy_submit_form($(this));
      return false;
    }
  });
  function remove_loading($form) {
    $form.find('[type=submit]').removeClass('error success');
    $form.find('.login-form-main-message').removeClass('show error success').html('');
  }
  function form_loading($form) {
    $form.find('[type=submit]').addClass('clicked').html(options['btn-loading']);
  }
  function form_success($form) {
    $form.find('[type=submit]').addClass('success').html(options['btn-success']);
    $form.find('.login-form-main-message').addClass('show success').html(options['msg-success']);
    setTimeout(function() {
      window.location.replace("/");
    }, 500);
  }
  function form_failed($form) {
    $form.find('[type=submit]').addClass('error').html(options['btn-error']);
    $form.find('.login-form-main-message').addClass('show error').html(options['msg-error']);
  }
  function dummy_submit_form($form) {
    if($form.valid()) {
      form_loading($form);
      setTimeout(function() {
          if (Math.floor((Math.random() * 10) + 1) > 5) {
          form_success($form);
          } else {
            form_failed($form);
          }
      }, 500);
    }
  }
})(jQuery);
