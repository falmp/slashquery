$(document).ready(function() {

  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

  $('#deleteBtn').click(function() {
    var pdiv = $(this).parent();
    pdiv.block();
    $.post('/cpanel/configuration/sqRules/deleteAbuse', function(status) {
      window.location.reload(true);
    })
  });

  $('tbody').on('click', 'a[href^="#d-"]', function(e) {
    e.preventDefault();
    var t = $(this);
    t.closest('div').block();
    $('#myModal').block();
    $.post('/cpanel/configuration/sqRules/deleteAbuseID', {
      id: t.attr('href').split('-')[1]
    }, function() {
      t.closest('div').unblock();
      t.closest('tr').remove();
    });
  });

});
