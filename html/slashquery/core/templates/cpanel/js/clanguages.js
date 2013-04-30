$(document).ready(function() {

  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

  $('#localeForm').submit(function() {
    var t = $(this);
    if ($('select[name="iso"]').val()) {
      t.block();
      $.post('/cpanel/configuration/sqRules/createLocale', {
        iso: $('select[name="iso"]').val(),
        language: $('select[name="iso"] option:selected').text(),
        token: $('#token').text()
      }, function() {
         location.reload(true);
      });
    }
    return false;
  });

  $('a[href^="#d-"]').click(function(e) {
    e.preventDefault();
    var iso = $(this).attr('href').split('-')[1];
    $('#myModalLabel').text('Delete locale: ' + iso);
    $('#myModal').modal('show');
    $('#delete').val(iso);
  });

  $('#delete').on('click', function(e) {
    var iso = $(this).val();
    $('#myModal').block();
    $.post('/cpanel/configuration/sqRules/rmLocale', {
      iso: iso,
      token: $('#token').text()
    }, function(s) {
      if (s) {
        $('a[href="#d-' + iso + '"]').closest('tr').remove();
      }
      $('#myModal').unblock().modal('hide');
    });
  })

});