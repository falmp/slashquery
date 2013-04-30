function post(path, parameters) {
  var form = $('<form></form>');

  form.attr("method", "post");
  form.attr("action", path);

  $.each(parameters, function(key, value) {
    var field = $('<input></input>');

    field.attr("type", "hidden");
    field.attr("name", key);
    field.attr("value", value);

    form.append(field);
  });

  // The form needs to be apart of the document in
  // order for us to be able to submit it.
  $(document.body).append(form);
  form.submit();
}

$(document).ready(function() {

  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';


  $('a[href^="#r-"]').click(function() {
    $('form').block();
    post('/cpanel/ACL/permissions/', {
      rid: $(this).attr('href').split('-')[1]
    })
    return false;
  });

  $('a.moduleTitle').on('click', function(e) {
    e.preventDefault();
    var tbodyID = $(this).text().split(':');
    $('#' + tbodyID[0]).toggle();
  });

  $('#sform').click(function() {
    $('form').block();
  })

});