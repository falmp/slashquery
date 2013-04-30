/**
 * upload files to nginx with upload_module supporting resumable uploads
 * @see http://www.grid.net.ru/nginx/resumable_uploads.en.html
 */

var fo = {} // our file object
var pause = 0;
var startTime = new Date();

$(document).ready(function() {

  /**
   * when uploading huge files we need to ping php to maitain the session
   * the idea is to ping php (call ping.php) every 10 min
   * if have a better idea let me know nbari@slashquery.com
   */
  ping();

  $.blockUI.defaults.message = '<img src="/slashquery/core/templates/cpanel/img/ajax-mac.gif" alt="loading..." />';
  $.blockUI.defaults.css.border = 'none';
  $.blockUI.defaults.css.backgroundColor = 'transparent';
  $.blockUI.defaults.overlayCSS.backgroundColor = '#fff';

  $('#pause').click(function() {
    pause = 1;
    $('.progress').removeClass('active')
    return false;
  });

  $('#resume').click(function() {
    if (pause) {
      pause = 0;
      $('.progress').addClass('active')
      retryUpload(fo);
    }
    return false;
  });

  $(':file').change(function() {
    var file = this.files[0];
    if (file) {
      var fileSize = 0;
      fileSize = (file.size > 1024 * 1024) ? (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB' : (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
      $('#fsize').text('File size: ' + fileSize).show();
      console.log(fileSize);
    } else {
      $('#fsize').hide();
    }
  });

  $.validator.addMethod('onlyImages', function(value, element) {
    var file = $(element).get(0).files[0];
    return true;
    return file.type.match('image.*')
  }, 'invalid mimetype');

  $('#uploadForm').validate({
    highlight: function(element, errorClass, validClass) {
      $(element).closest('.control-group').removeClass('success').addClass('error');
    },
    unhighlight: function(element, errorClass, validClass) {
      $(element).closest('.control-group').removeClass('error').addClass('success');
    },
    rules: {
      name: {
        required: true,
        minlength: 3
      },
      file: {
        required: true,
        onlyImages: true
      },
      tags: {
        required: true,
        minlength: 3
      }
    },
    messages: {
      file: {
        onlyImages: 'Only images'
      }
    },
    submitHandler: function(form) {
      $(form).slideUp();

      /**
       * get the file and increment the fo object with the url (form action)
       * and the sessionID
       */
      fo = $('#file').get(0).files[0];
      fo.url = $(form).attr('action');
      fo.sessionID = $('#uuid').val();

      /**
       * Based on the data collected, it seems our most optimal configuration is
       * to use a maxChunkSize of 256 KB and a client_body_buffer_size of 256 KB
       * because it performed the best out of all the other configurations.
       * @see https://gist.github.com/JAndritsch/3920385
       */
      fo.bytes_per_chunk = 1024 * 256;
      fo.currentChunkStartPos = 0
      fo.currentChunkEndPos = fo.bytes_per_chunk;
      if (fo.currentChunkEndPos > fo.size) {
        fo.currentChunkEndPos = fo.size;
      }

      fo.retries = 3;

      $('.status').show();

      uploadFile(fo);
    }
  });

  $('.close').click(function() {
    $(this).closest('div').hide()
    $('#uploadForm').slideDown();
  });

  function uploadFile(fo) {
    /**
     * split the files into segments
     * depending on the browser found the proper 'slice'
     * slicing a file and uploading each portion (File APIs)
     * @see http://www.html5rocks.com/en/tutorials/file/xhr2/
     */
    var slice;
    (slice = 'slice') in fo || (slice = 'mozSlice') in fo || (slice = 'webkitSlice') in fo;

    slice = fo[slice](fo.currentChunkStartPos, fo.currentChunkEndPos);
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
      if (this.readyState == 4) {
        try {
          if (this.status == 201) { // chunk was uploaded succesfully
            var range = this.responseText;
            try { // getResponseHeader throws exception during cross-domain upload, but this is most reliable variant
              range = this.getResponseHeader('Range');
            } catch (e) {};
            if (!range) {
              throw new Error('No range in 201 answer');
            }
            /**
             * parse the current range from the response
             */
            fo.currentChunkStartPos = parseInt(range.split('-')[1].split('/')[0]) + 1;

            if (fo.currentChunkStartPos > fo.currentChunkEndPos) {
              fo.currentChunkEndPos = fo.currentChunkStartPos + fo.bytes_per_chunk;
            } else {
              fo.currentChunkEndPos = fo.currentChunkEndPos + fo.bytes_per_chunk;
            }

            if (fo.currentChunkEndPos > fo.size) {
              fo.currentChunkEndPos = fo.size;
            }

            fo.retries = 3 // restore retry counter
            //console.log(range, fo.currentChunkStartPos, fo.currentChunkEndPos)
            uploadProgress(range)

            if (!pause) {
              uploadFile(fo);
            }
          } else if (this.status == 200) {
            $('.controls').hide();
            $('.bar').css('width', 100 + '%').text(100 + '%');
            $('.progress').removeClass('active')
            var dl = $('<dl/>').addClass('dl-horizontal');
            $.each(JSON.parse(this.responseText), function(k, v) {
              console.log(k, v);
              $(dl).append('<dt>' + k + '</dt><dd>' + v + '</dd>');
            });
            $('form').replaceWith(dl);
          } else {
            throw new Error('Bad http answer code');
          }
        } catch (e) { // any exception means that we need to retry upload
          console.log(this)
          retryUpload(fo);
        };
      }
    };

    xhr.open('POST', fo.url, true);
    // xhr.upload.addEventListener('progress', uploadProgress, false);
    xhr.setRequestHeader('Content-Disposition', 'attachment; filename="' + encodeURI(fo.name) + '"');
    xhr.setRequestHeader('Content-Type', fo.type || 'application/octet-stream');
    xhr.setRequestHeader('Content-Range', 'bytes ' + fo.currentChunkStartPos + '-' + (fo.currentChunkEndPos - 1) + '/' + fo.size);
    xhr.setRequestHeader('Session-ID', fo.sessionID);
    xhr.withCredentials = true; // allow cookies to be sent
    xhr.send(slice);
    slice = null;
  }

  function retryUpload(fo) {
    fo.retries--;
    if (fo.retries) {
      setTimeout(function() {
        uploadFile(fo)
      }, 1000);
      console.log('retrying...');
    } else {
      console.log('no more retries');
    }
  }

  function uploadProgress(range) {
    var status = range.split('-')[1].split('/');
    var loaded = parseInt(status[0]);
    var total = parseInt(status[1])
    var percent = parseInt(loaded * 100 / total, 10) || 0;
    $('.bar').css('width', percent + '%').text(percent + '%');
    //console.log(loaded, total, percent);
  }

  function ping() {
    var thisTime = parseFloat((new Date() - startTime) / 1000 / 60);
    if (thisTime > 10) { // after 10 min
      $.get('/cpanel/upload/sqRules/ping');
      startTime = new Date()
    }
    setTimeout(function() {
      ping()
    }, 600000);
  }

});
