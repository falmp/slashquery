$(document).ready(function() {

  $("#usersTable a[id^='uid-']").on('click', function(e) {
    e.preventDefault();
    uid = $(this).attr('id').split('-');
    $('#dialog-confirm').dialog({
      resizable: false,
      draggable: false,
      closeOnEscape: true,
      modal: true,
      width: '400',
      title: 'Delete: '+$(this).parent().siblings().eq(1).text(),
      buttons: {
        Delete: function() {
          $.post('/cpanel/users/delete', {
            uid: uid[1]
          },
          function(data) {
            if (data) {
              location.reload(true);
            }
          },
          'json');
        },
        Cancel: function() {
          $(this).dialog("close");
        }
      }
    });
  });

  $('#usersContainer').myPaginator({
    url: '/cpanel/users/getusers',
    rp: 10,
    sname: 'uid',
    sorder: 'DESC',
    searchitems: {
      'Name': 'name',
      'Email': 'email',
      'Joined': 'cdate'
    },
    rowExtras: {
      0 : 'class="nbl"',
      4 : 'class="center"'
    },
    nodatamsg: '<tr><td id="tableBarLoader" class="nbl" colspan="5"><div class="notification info"><span class="icon sprite32 info"></span><p class="strong">No Results</p></div></td></tr>',
    zebra: 1,
    mover: 0,
    rclick: 1,
    waitId: '#usersTable',
    showPaginator: 1
  });

});