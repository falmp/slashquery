(function($) {

  $.fn.myPaginator = function(options) {
    var defaults = {
      url: '/',
      page: 1,
      rp: 10,
      total: '',
      sname: 'id',
      sorder: 'DESC',
      search: 0,
      query: 0,
      searchitems: 0,
      nodatamsg: '',
      addTableTrID : 0,
      rowIDs: 0,
      rowExtras: 0,
      zebra: 0,
      mover: 0,
      rclick: 0,
      toogle: 0,
      waitId: 0,
      noTable: 0,
      noTableId: 0,
      showPaginator: 0, // 1 or 2, 1 for always display the paginator, 2 for show paginator if pages > total
      paginatorHTML: '<div class="myPaginator hidden"><div class="table"><div class="pSearchForm">Find <input size="30" class="pQuery" name="pQuery" type="text">&nbsp;<select class="pSelect" name="pSelect"></select>&nbsp;<input class="pSearchButton" type="button" value="Search"></div></div><div class="line"></div><div class="table"><div class="paginator"><ul class="pControl"><li class="pButton"><a class="iconR sprite16 pSearch" href="#"></a></li><li><span class="pSeparator"></span></li><li><select class="pRP" name="rp"><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="75">75</option><option value="100">100</option></select></li><li><span class="pSeparator"></span></li><li class="pButton"><a class="iconR sprite16 pFirst" href="#"></a></li><li class="pButton"><a class="iconR sprite16 pPrev" href="#"></a></li><li><span class="pSeparator"></span></li><li class="pPage">Page <span id="pCurrent">&nbsp;</span> of <span id="pTotal">&nbsp;</span></li><li><span class="pSeparator"></span></li><li class="pButton"><a class="iconR sprite16 pNext" href="#"></a></li><li class="pButton"><a class="iconR sprite16 pLast" href="#"></a></li><li><span class="pSeparator"></span></li><li class="pButton"><a href="#" class="iconR sprite16 pReload"></a></li></ul></div></div></div></div></div></div></div>'
    };
    options = $.extend({},
    defaults, options);

    // iterate passed elements
    return this.each(function() {
      var $this = $(this),
      obj = $.data(this, "myPaginator-instance");
      if (!obj) {
        $.data(this, "myPaginator-instance", new $.fn.myPaginator.instance(this, options)._init(arguments));
      } else {
        obj.settings(options).refresh();
      }
    }).data("myPaginator-instance");

  };

  // plugin instance constructor
  $.extend($.fn.myPaginator, {
    instance: function(element, options) {
      this.element = $(element);
      this.options = options;
    }
  });

  $.extend($.fn.myPaginator.instance.prototype, {
    end: function() {
      return this.element;
    },
    option: function(option, value) {
      if (value) {
        this.element.data({option: value});
      } else {
        return this.element.data().option;
      }
      return this;
    },
    settings: function(options) {
      if (options) {
        this.element.data($.extend({},this.options, options));
      } else {
        return this.options;
      }
      return this;
    },
    common: function() {
      var self = this;
      if (this.element.data('showPaginator') && $('.myPaginator', this.element).length==0) {
        this.element.append(this.element.data('paginatorHTML'));
      }

      if ($('.myPaginator .pRP option[value=' + this.element.data('rp') + ']', this.element).length === 0) {
        $('.myPaginator .pRP', this.element).prepend('<option value=' + this.element.data('rp') + '>' + this.element.data('rp') + '</option>');
      }
      $('.myPaginator .pRP', this.element).val(this.element.data('rp')).attr('selected', true);

      if (this.element.data('searchitems')) {
        var searchItems = '';
        $.each(this.element.data('searchitems'),
        function(key, value) {
          searchItems += '<option value="' + value + '">' + key + '</option>';
        });
        $('.myPaginator .pSelect').append(searchItems);
      }

      this.element.find('.myPaginator .pControl a').click(function(e) {
        e.preventDefault();
        var m = $(this).attr('class').split(' ');
        self.paginate(m[2]);
      });

      this.element.find('.myPaginator .pRP').change(function() {
        self.element.data({
          rp: $("select[name='rp'] option:selected").val()
        });
        self.element.data({
          page: 1
        });
        self.element.data({
          search: 0
        });
        self.refresh();
      });

      this.element.find('.myPaginator .pSearchButton').click(function(e) {
        e.preventDefault();
        /* hide previous selected */
        if ($('.myPaginator .pSort-' + self.element.data('sname') + ' > span').is(':visible')) {
          $('.myPaginator .pSort-' + self.element.data('sname') + ' > span').toggle();
        }
        self.element.data({
          search: 1
        });
        self.element.data({
          page: 1
        });
        self.element.data({
          query: $('.myPaginator .pQuery').val()
        });
        self.element.data({
          sname: $('.myPaginator .pSelect :selected').val()
        });
        var targetOffset = self.element.offset().top - 30;
        $('html, body').animate( { scrollTop: targetOffset }, 'slow' );
        self.refresh();
      });

      $('a[id^=pSort-]', this.element).click(function(e) {
        e.preventDefault();
        var nSname = $(this).attr('id').split('-');
        var oldId = self.element.data('sname');
        self.element.data({
          sname: nSname[1]
        });
        /* hide previous selected */
        if ($('#' + nSname[0] + '-' + oldId + ' > span').is(':visible')) {
          $('#' + nSname[0] + '-' + oldId + ' > span').toggle();
        }
        if (oldId == self.element.data('sname')) {
          if ($(this).children().is(':hidden')) {
            $(this).children().toggle();
          }
          self.element.data({
            'sorder': (self.element.data('sorder') == 'DESC') ? 'ASC': 'DESC'
          });
          if (self.element.data('sorder') == 'DESC') {
            $(this).children().removeClass('desc').addClass('asc');
          } else {
            $(this).children().removeClass('asc').addClass('desc');
          }
        } else {
          $(this).children().toggle();
          self.element.data({
            sorder: 'ASC'
          });
        }
        self.refresh();
      });
      return this;
    },
    _init: function() {
      this.element.data(this.options);
      this.common();
      this.ajaxCall();
      return this;
    },
    ajaxSuccessHandler: function(response) {
      if (response.rows) {

        if (this.element.data('showPaginator')) {
          if (this.element.data('showPaginator') > 1)  {
            if (this.element.data('page') < response.total || this.element.data('page') > 1 || this.element.data('search'))  {
              $('.myPaginator', this.element).show();
            } else {
              $('.myPaginator', this.element).hide();
            }
          } else {
            $('.myPaginator', this.element).show();
          }
        }

        this.element.data({
          total: response.total
        });
        $('#pCurrent').html(response.page);
        $('#pTotal').html(response.total);
        if (this.element.data('noTable')) {
          this.drawNoTable(response);
        } else {
          this.drawTbody(response);
          if (this.element.data('mover')) {
            $('tr', this.element).mouseover(function() {
              $(this).addClass('over');
            }).mouseout(function() {
              $(this).removeClass('over');
            });
          }
          if (this.element.data('rclick')) {
            $('tr', this.element).mousedown(function(e) {
              if (e.which > 1) {
                e.preventDefault();
                $(this)[0].oncontextmenu = function() {
                  return false;
                };
                $(this).toggleClass('click');
              }
            });
          }
          if (this.element.data('zebra')) {
            $('tr:even', this.element).addClass('alt');
          }
        }
      } else {
        if (this.element.data('noTable')) {
          $(this.element.data('noTableId'), this.element).html(this.element.data('nodatamsg'));
        } else {
          $('tbody', this.element).html(this.element.data('nodatamsg'));
        }
      }
      this.toggleLoading();
      this.element.data({
        search: 0
      });
    },
    ajaxErrorHandler: function(xhr) {
      console.log(xhr);
    },
    ajaxCall: function() {
      var self = this;
      var data = {};
      data.page = this.element.data('page');
      data.rp = this.element.data('rp');
      data.sorder = this.element.data('sorder');
      data.sname = this.element.data('sname');
      if (this.element.data('search')) {
        data.query = this.element.data('query');
        data.rp = 100;
      }
      $.ajax({
        type: "POST",
        url: this.element.data('url'),
        data: data,
        dataType: 'json',
        beforeSend: function() {
          self.toggleLoading();
        },
        success: function(response) {
          self.ajaxSuccessHandler(response);
        },
        error: function(xhr) {
          self.ajaxErrorHandler(xhr);
        }
      });
    },
    drawNoTable: function(data) {
      var html2insert = [];
      var i = 0;
      $.each(data.rows,
      function() {
        for (var a = 0; a < this.cell.length; a += 1) {
          html2insert[i++] = this.cell[a];
        }
      });
      if (this.element.data('noTableId')) {
        $(this.element.data('noTableId'), this.element).html(html2insert.join(''));
      } else {
        this.element.html(html2insert.join(''));
      }
    },
    drawTbody: function(data) {
      var self = this;
      var html2insert = [];
      var i = 0;
      $.each(data.rows, function() {
        if(self.element.data('addTableTrID')) {
          html2insert[i++] = '<tr id="'+ self.element.data('addTableTrID')+this.id+'">';
        } else {
          html2insert[i++] = '<tr>';
        }
        for (var a = 0; a < this.cell.length; a += 1) {
          if (self.element.data().rowExtras[a]) {
            if (self.element.data().rowIDs[a]) {
              html2insert[i++] = '<td id="'+ self.element.data().rowIDs[a] + this.id +'" ' + self.element.data().rowExtras[a] + '>' + this.cell[a] + '</td>';
            } else {
              html2insert[i++] = '<td ' + self.element.data().rowExtras[a] + '>' + this.cell[a] + '</td>';
            }
          } else {
            if (self.element.data().rowIDs[a]) {
              html2insert[i++] = '<td id="'+ self.element.data().rowIDs[a] + this.id +'">' + this.cell[a] + '</td>';
            } else {
              html2insert[i++] = '<td>' + this.cell[a] + '</td>';
            }
          }
        }
        html2insert[i++] = '</tr>';
      });
      $('tbody', this.element).html(html2insert.join(''));
    },
    toggleLoading: function() {
      if (this.element.data('toggle')) {
        $(this.element.data('toggle')).toggle();
      }
      if (this.element.data('waitId')) {
        $(this.element.data('waitId')).toggleClass('wait5');
      } else {
        this.element.toggleClass('wait5');
      }
      $('.myPaginator .pReload', this.element).toggleClass('loading');
    },
    paginate: function(ptype) {
      var r = 0,
      page = this.element.data('page'),
      total = this.element.data('total');
      switch (ptype) {
      case 'pNext':
        if (page < total) {
          page = (page) + 1;
          r++;
        }
        break;
      case 'pLast':
        if (page != total) {
          page = total;
          r++;
        }
        break;
      case 'pPrev':
        if (page != 1) {
          page = (page) - 1;
          r++;
        }
        break;
      case 'pFirst':
        if (page > 1) {
          page = 1;
          r++;
        }
        break;
      case 'pReload':
        r++;
        break;
      case 'pSearch':
        $('.myPaginator .table .pSearchForm').slideToggle('fast');
        break;
      }
      this.element.data({
        page: page
      });
      if (r) {
        this.refresh();
      }
    },
    refresh: function() {
      this.ajaxCall();
      return this;
    }

  });

})(jQuery);
