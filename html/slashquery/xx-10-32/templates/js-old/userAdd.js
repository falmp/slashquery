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

  $('#addUser').validate({
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
      email: {
        required: {
          depends: function() {
            $(this).val($.trim($(this).val()));
            return true;
          }
        },
        email: true,
        remote: {
          url: '/cpanel/users/sqRules/checkEmail',
          type: 'post'
        }
      },
      p1: {
        required: true,
        minlength: 8
      },
      p2: {
        required: true,
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
        required: 'Please type your email',
        email: 'Please enter a valid email address',
        remote: 'Email already exists'
      },
      p1: {
        required: 'Please type your password',
        minlength: 'Minimum of {0} characters in length.'
      },
      p2: {
        required: 'Please confirm your password',
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
      $('#addUser').block();

      var d, e, p;
      d = {};
      e = GibberishAES.enc($('#email').val().toLowerCase(), $('input[name="token"]').val());
      p = GibberishAES.enc($.sha1($('#p1').val()), $('input[name="token"]').val());
      d.t = $('input[name="token"]').val();
      d.e = e;
      d.p = p;
      d.name = $('#name').val();
      d.gender = $("input[name='gender']:checked").val();
      d.status = $("input[name='status']:checked").val();
      d.roles = $("input[name='roles']:checked").map(function() {
        return $(this).val();
      }).get();

      $.ajax({
        type: 'POST',
        url: '/cpanel/users/sqRules/addUser',
        cache: false,
        data: d,
        dataType: 'json',
        success: function(json) {
          $('#addUser').hide();
          $('#addUser').unblock();
          $('#addUser')[0].reset();
          json ? $('#Fsuccess').fadeIn() : $('#Ferror').fadeIn();
          $('#addUser .success').removeClass('success');
        },
        error: function() {
          location.reload(true);
        }
      });
    }
  });

  $('.close').on('click', function(e) {
    e.preventDefault();
    $(this).parent().hide();
    $('#addUser').fadeIn();
  });

});
