$(document).ready(function() {

  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

  $('tbody').on('click', 'a[href^="#d-"]', function(e) {
    e.preventDefault();
    $('#myModalLabel').text('Delete: ' + $(this).parent().siblings().eq(1).text());
    $('#myModal').modal('show');
    $('#delete').val($(this).attr('href').split('-')[1]);
  });

  $('#delete').on('click', function(e) {
    $('#myModal').block();
    $.post('/cpanel/users/sqRules/delUser', {
      uid: $(this).val(),
      token: $('#token').text()
    }, function() {
      location.reload(true);
    });
  })

  $('#paginator').on('click', 'a[href^="#p-"]', function(e) {
    e.preventDefault();
    var t = $(this);
    if (t.parent().hasClass('active')) {
      return;
    }
    $('.pagination ul li').removeClass('active');
    t.parent().addClass('active');
    t.html('<img src="/slashquery/core/templates/cpanel/img/ajax-loading.gif" alt="loading..." />');
    $.post('/cpanel/users/sqRules/pagination', {
      page: $(this).attr('href').split('-')[1]
    }, function(data) {
      if (data) {
        t.html(data.page);
        $('#cPage').text(data.page);
        var html2insert = [];
        var i = 0;
        $.each(data.rows, function() {
          html2insert[i++] = '<tr>';
          for (var a = 0; a < this.cell.length; a += 1) {
            html2insert[i++] = '<td>' + this.cell[a] + '</td>';
          }
          html2insert[i++] = '</tr>';
        });
        $('tbody').html(html2insert.join(''));
      }
    });
  });

  $('#paginator').on('click', 'a[href=#next]', function(e) {
    e.preventDefault();
    var last = parseInt($(this).closest('li').prev().text());
    if (!last) {
      return;
    }
    if ((parseInt($('#Tpages').text()) - last) > 7) {
      var html2insert = [];
      var i = 0;
      html2insert[i++] = '<li><a href="#prev">« Previous</a></li>';
      for (var a = 1; a <= 7; a += 1) {
        var v = parseInt(a) + last;
        html2insert[i++] = '<li><a href="#p-' + v + '">' + v + '</a></li>';
      }
      html2insert[i++] = '<li><a href="#next">Next »</a></li>';
      $('#paginator').html(html2insert.join(''));
    } else {
      var html2insert = [];
      var i = 0;
      html2insert[i++] = '<li><a href="#prev">« Previous</a></li>';
      for (var a = 1; a <= (parseInt($('#Tpages').text()) - last); a += 1) {
        var v = parseInt(a) + last;
        html2insert[i++] = '<li><a href="#p-' + v + '">' + v + '</a></li>';
      }
      $('#paginator').html(html2insert.join(''));
    }
  });

  $('#paginator').on('click', 'a[href=#prev]', function(e) {
    e.preventDefault();
    var last = parseInt($(this).closest('li').next().text());
    if (!last) {
      return;
    }
    if ((last - 7) > 7) {
      var html2insert = [];
      var i = 0;
      html2insert[i++] = '<li><a href="#prev">« Previous</a></li>';
      for (var a = 7; a >= 1; a -= 1) {
        var v = last - parseInt(a);
        html2insert[i++] = '<li><a href="#p-' + v + '">' + v + '</a></li>';
      }
      html2insert[i++] = '<li><a href="#next">Next »</a></li>';
      $('#paginator').html(html2insert.join(''));
    } else {
      var html2insert = [];
      var i = 0;
      for (var a = last; a >= 1; a -= 1) {
        var v = last - parseInt(a);
        if (v) {
          html2insert[i++] = '<li><a href="#p-' + v + '">' + v + '</a></li>';
        }
      }
      html2insert[i++] = '<li><a href="#next">Next »</a></li>';
      $('#paginator').html(html2insert.join(''));
    }
  });

});
