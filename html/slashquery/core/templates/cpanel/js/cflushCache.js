$(document).ready(function() {

  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

  $('#submitButton').click(function() {
    $('#caches').block();
    $.post('/cpanel/configuration/sqRules/flushCache', function(status) {
      if (status) {
        location.reload(true);
      }
    })
  });

});