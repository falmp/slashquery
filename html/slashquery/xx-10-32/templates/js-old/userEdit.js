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

  $('#editUser').validate({
    errorClass: 'help-inline',
    errorElement: 'span',
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
      p1: {
        minlength: 8
      },
      p2: {
        minlength: 8,
        equalTo: '#p1'
      },
      gender: {
        required: true
      },
      status: {
        required: true
      }
    },
    messages: {
      name: {
        minlength: 'At least {0} characters required!'
      },
      email: {
        email: 'Please enter a valid email address',
        remote: 'Email already exists'
      },
      p1: {
        minlength: 'Minimum of {0} characters in length.'
      },
      p2: {
        minlength: 'Minimum of {0} characters in length.',
        equalTo: "Enter the same password as above"
      },
      gender: {
        required: 'Please select a gender'
      },
      status: {
        required: 'Please select a status'
      }
    },
    submitHandler: function() {
      $('#editUser').block();

      var d, p;
      d = {};
      p = $('#p1').val() ? GibberishAES.enc($.sha1($('#p1').val()), $('input[name="token"]').val()) : '';
      d.uid = $('#uid').val();
      d.token = $('input[name="token"]').val();
      d.password = p;
      d.name = $('#name').val();
      d.gender = $("input[name='gender']:checked").val();
      d.status = $("input[name='status']:checked").val();
      d.roles = $("input[name='roles']:checked").map(function() {
        return $(this).val();
      }).get();

      $.ajax({
        type: 'POST',
        url: '/cpanel/users/sqRules/editUser',
        cache: false,
        data: d,
        dataType: 'json',
        success: function(json) {
          $('#editUser').hide();
          $('#editUser').unblock();
          $('#editUser')[0].reset();
          json ? $('#Fsuccess').fadeIn() : $('#Ferror').fadeIn();
          $('#editUser .success').removeClass('success');
        },
        error: function() {
          location.reload(true);
        }
      });
    }
  });


});
