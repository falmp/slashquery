$(document).ready(function() {

  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';


  $('#oiForm').submit(function() {
    var t = $(this);
    t.block();
    $.post('/cpanel/sqRules/checkOpenID', $(this).serializeArray(), function(status) {
      if (status['openid']) {
        window.location.replace(status['openid']);
      } else {
        $('#Ferror').show()
        t.unblock();
      }
    }, 'json');
    return false;
  });


  $('tbody').on('click', 'a[href^="#d-"]', function(e) {
    e.preventDefault();
    $('#myModalLabel').text('Delete: ' + $(this).parent().siblings().eq(0).text());
    $('#myModal').modal('show');
    $('#delete').val($(this).attr('href').split('-')[1]);
  });

  $('#delete').on('click', function(e) {
    $('#myModal').block();
    $.post('/cpanel/sqRules/delOpenID', {
      id: $(this).val(),
      token: $('#token').text()
    }, function() {
      location.reload(true);
    });
  })

});
