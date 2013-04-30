$(document).ready(function() {

  /* blockUI */
  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

  $("a[id^='module-']").click(function(e) {
    e.preventDefault();
    var t = $(this);
    mid = t.attr('id').split('-')[1];
    t.closest('table').attr('id') == 'sModules' ? $('#sModules').block() : $('#cpModules').block();
    $.post('/cpanel/ACL/sqRules/status', {
      mid: mid
    }, function(status) {
      if (status) {
        if ($(t).find('i').hasClass('icon\-status0')) {
          $(t).find('i').removeClass('icon\-status0').addClass('icon\-status1');
        } else {
          $(t).find('i').removeClass('icon\-status1').addClass('icon\-status0');
        }
      }
      t.closest('table').attr('id') == 'sModules' ? $('#sModules').unblock() : $('#cpModules').unblock();
    });
  });

});
