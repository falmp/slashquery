$(document).ready(function() {

  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

  $.validator.addMethod('nameRegex', function(value, element) {
    var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":<>?";
    valid = true;
    for (var i = 0; i < value.length; i++) {
      if (iChars.indexOf(value.charAt(i)) != -1) {
        valid = false;
      }
    }
    return valid;
  }, 'invalid characters');

  $('#name, input[name="gender"]').on('change keyup', function() {
    $('#changeDetails .form-actions').show();
  });

  var udForm = $('#changeDetails').validate({
    errorClass: "help-block",
    errorElement: "p",
    errorPlacement: function(error, element) {
      $(element).after(error);
    },
    highlight: function(element, errorClass, validClass) {
      $(element).parents('.control-group').removeClass('success');
      $(element).parents('.control-group').addClass('error');
    },
    unhighlight: function(element, errorClass, validClass) {
      $(element).parents('.control-group').removeClass('error');
      $(element).parents('.control-group').addClass('success');
    },
    rules: {
      name: {
        minlength: 3,
        nameRegex: true
      },
      gender: {
        required: true
      }
    },
    messages: {
      name: {
        minlength: 'At least {0} characters required!'
      },
      gender: {
        required: 'Please select a gender'
      }
    },
    submitHandler: function() {
      $('#changeDetails').block();

      var d;
      d = {};
      d.t = $('input[name="token"]').val();
      d.name = $.trim($('#name').val());
      d.gender = $("input[name='gender']:checked").val();

      $.ajax({
        type: 'POST',
        url: '/cpanel/sqRules/changeDetails',
        cache: false,
        data: d,
        dataType: 'json',
        success: function(json) {
          $('#changeDetails').unblock();
          $('#changeDetails .control-group').removeClass('error success');
        },
        error: function() {
          window.location.reload(true);
        }
      });
    }
  });

  $('#changeDetails .cancel').on('click', function() {
    udForm.resetForm();
    $('#changeDetails .control-group').removeClass('error success');
    $('#changeDetails .form-actions').hide();
  });

  $('.close').on('click', function(e) {
    e.preventDefault();
    $(this).parent().hide();
    $('#addUser').fadeIn();
  });

  $('#cpBtn').click(function() {
    $(this).hide();
    $('#changePass').show();
  })

  $('#changePass .cancel').on('click', function() {
    $('#cpBtn').show();
    $('#changePass').hide();
  });

  $('#changePass').validate({
    errorClass: "help-block",
    errorElement: "p",
    errorPlacement: function(error, element) {
      $(element).after(error);
    },
    highlight: function(element, errorClass, validClass) {
      $(element).parents('.control-group').removeClass('success');
      $(element).parents('.control-group').addClass('error');
    },
    unhighlight: function(element, errorClass, validClass) {
      $(element).parents('.control-group').removeClass('error');
      $(element).parents('.control-group').addClass('success');
    },
    rules: {
      p1: {
        required: true,
        minlength: 8
      },
      p2: {
        required: true,
        minlength: 8,
        equalTo: '#p1'
      },
    },
    messages: {
      p1: {
        required: 'Please type your password',
        minlength: 'Minimum of {0} characters in length.'
      },
      p2: {
        required: 'Please confirm your password',
        minlength: 'Minimum of {0} characters in length.',
        equalTo: "Enter the same password as above"
      },
    },
    submitHandler: function() {
      $('#changePass').block();

      var d, p;
      d = {};
      p = GibberishAES.enc($.sha1($('#p1').val()), $('input[name="token"]').val());
      d.t = $('input[name="token"]').val();
      d.p = p;

      $.ajax({
        type: 'POST',
        url: '/cpanel/sqRules/changePassword',
        cache: false,
        data: d,
        dataType: 'json',
        success: function(json) {
          $('#changePass').unblock().hide();
          $('#cpFsuccess').show();
        },
        error: function() {
          window.location.reload(true);
        }
      });
    }
  });

});
