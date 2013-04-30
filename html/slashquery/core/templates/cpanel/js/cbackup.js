$(document).ready(function() {

  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

  $('#submitButton').click(function() {
    $(this).parent().html('This could take awhile, depends on your database size and link to it.');
    $('#command').block();
    $.post('/cpanel/configuration/sqRules/backup', function(status) {
      if (status) {
        $('#command > div .box').html('Backup made. <strong>check that you can read the backup and restore it</stong>');
      } else {
        $('#command > div .box').html('Something went wrong, database size could affect, try to run the backup command directly on your server and check that you can read the backup and restore it');
      }
      $('#command').unblock();
    })
  });

});