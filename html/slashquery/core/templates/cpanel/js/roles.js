$(document).ready(function() {

  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

  $('#roles tbody').on('click', 'a[href^="#e-"]', function(e) {
    e.preventDefault();
    $('#rid').val($(this).attr('href').split('-')[1]);
    $('#role').val($(this).closest('tr').find('td:first').text());
    $('#submitButton').text('Modify role');
    $('#cancelButton').show();
    $('#createRoleSuccess, #modifyRoleSuccess').fadeOut();
    $('#createRole').fadeIn();
  });

  $('#cancelButton').click(function() {
    location.reload(true);
  });

  $('#roles tbody').on('click', 'a[href^="#d-"]', function(e) {
    e.preventDefault();
    $('#myModalLabel').text('Delete: ' + $(this).parent().siblings().eq(1).text());
    $('#myModal').modal('show');
    $('#delete').val($(this).attr('href').split('-')[1]);
  });

  $('#delete').on('click', function(e) {
    $('#myModal').block();
    $.post('/cpanel/ACL/sqRules/delRole', {
      rid: $(this).val(),
      token: $('#token').text()
    }, function() {
      location.reload(true);
    });
  })

  $.validator.addMethod('rnameRegex', function(value, element) {
    return this.optional(element) || /[^\.,"'\?\!;\:#\$%&\(\)\*\+\-\/\<\>\=@\[\]\\\^_\{\}\|~]{2,32}$/.test(value);
  }, 'invalid characters');

  $('#createRole').validate({
    errorClass: 'help-block',
    errorElement: 'p',
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
      role: {
        required: true,
        minlength: 2,
        rnameRegex: true,
        remote: {
          url: '/cpanel/ACL/sqRules/checkRole',
          type: 'post',
          data: {
            rid: function() {
              return $("#rid").val();
            }
          }
        }
      }
    },
    messages: {
      role: {
        required: 'Please type a role name',
        minlength: 'At least {0} characters required!',
        remote: 'Role name already exists'
      }
    },
    submitHandler: function() {
      var d = {};
      d.role = $.trim($('#role').val());
      d.rid = $.trim($('#rid').val());
      $.ajax({
        type: 'POST',
        url: '/cpanel/ACL/sqRules/addRole',
        cache: false,
        data: d,
        dataType: 'json',
        beforeSend: function() {
          $('#createRole').block();
        },
        success: function(status) {
          $("#createRole").hide();
          $('#createRole').unblock();
          if (status) {
            if ($('#rid').val() == 0) {
              $('#createRoleSuccess').fadeIn()
              $('#roles tr:last').after('<tr><td>' + d.role + '</td><td><a href="#e-' + status + '"><i class="icon-pencil"></i></a>&nbsp;&nbsp;&nbsp;<a href="#d-' + status + '"><i class="icon-trash"></i></a></td></tr>');
            } else {
              $('#modifyRoleSuccess').fadeIn();
              $('a[href="#e-' + d.rid + '"]').closest('tr').find('td:first').text(d.role);
              $('#submitButton').text('Create role');
              $('#cancelButton').hide();
            }
          } else {
            $('#createRoleError').fadeIn();
          }
          $('#createRole')[0].reset()
          $("#role").removeData('previousValue');
          $("#rid").val(0);
          $('#createRole .success').removeClass('success');
        },
        error: function(data) {
          location.reload(true);
        }
      });
    }
  });

  $('.close').on('click', function(e) {
    e.preventDefault();
    $(this).parent().hide();
    $('#createRole').fadeIn();
  });

});
