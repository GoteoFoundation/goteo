/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Goteo Foundation

The JavaScript code in this page is free software: you can
redistribute it and/or modify it under the terms of the GNU
General Public License (GNU GPL) as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.  The code is distributed WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

As additional permission under GNU GPL version 3 section 7, you
may distribute non-source (e.g., minimized or compacted) forms of
that code without the copy of the GNU GPL normally required by
section 4, provided you include this license notice and a URL
through which recipients can access the Corresponding Source.


@licend  The above is the entire license notice
for the JavaScript code in this page.
*/

var form = {};

function parseVideoURL (url) {
    // - Supported YouTube URL formats:
    //   - http://www.youtube.com/watch?v=My2FRPA3Gf8
    //   - http://youtu.be/My2FRPA3Gf8
    //   - https://youtube.googleapis.com/v/My2FRPA3Gf8
    //   - https://m.youtube.com/watch?v=My2FRPA3Gf8
    // - Supported Vimeo URL formats:
    //   - http://vimeo.com/25451551
    //   - http://player.vimeo.com/video/25451551
    // - Supported PeerTube URL formats:
    //   - http://framatube.org/w/25451551
    //   - http://framatube.org/video/watch/25451551
    // - Also supports relative URLs:
    //   - //player.vimeo.com/video/25451551

    url.match(/(http:|https:|)\/\/(player.|www.|m.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com)|framatube\.org)\/(video\/watch\/|video\/|embed\/|watch\?v=|v\/|w\/)?([A-Za-z0-9._%-]*)(\&\S+)?/);

    var type, src;

    if (RegExp.$3.indexOf('youtu') > -1) {
        type = 'youtube';
        src = '//youtube.com/embed/' +  RegExp.$6 + '?wmode=Opaque&autoplay=1';
    } else if (RegExp.$3.indexOf('vimeo') > -1) {
        type = 'vimeo';
        src = '//player.vimeo.com/video/' + RegExp.$6 + '?title=0&byline=0&portrait=0&autoplay=1';
    } else if (RegExp.$3.indexOf('framatube') > -1) {
        type = 'framatube';
        src = '//framatube.org/videos/embed/' + RegExp.$6 + '?warningTitle=0&autoplay=1';
    }

    return {
        type: type,
        src: src,
        id: RegExp.$6
    };
}

var _uploadImage = function(files, url, callback) {
  callback = $.isFunction(callback) ? callback : function(){};
  var data = new FormData();
  if(!files.length) files = [files];
  $.each(files, function(index, file){
    // TODO: configurable input.file name
    data.append('file[]', file);
  });
  var _progress = function(e) {
      if(e.lengthComputable){
          callback('progress', e.loaded / e.total);
      }
  };
  $.ajax({
      url: url,
      cache: false,
      contentType: false,
      processData: false,
      data: data,
      type: 'POST',
      xhr: function() {
        var myXhr = $.ajaxSettings.xhr();
        if (myXhr.upload) myXhr.upload.addEventListener('progress',_progress, false);
        return myXhr;
      },
      success: function(result) {
        if(result && result.files) {
          var files = $.map(result.files, function(file) {
            return IMG_URL + '/700x0/' + file.name;
          });
          callback('success', files);
        } else {
          callback('error', 'No files uploaded!');
        }
      },
      error: function(data) {
        callback('error', data);
      }
  });
};

var default_md_toolbar = [
  {
      name: "close",
      // action: SimpleMDE.toggleFullScreen,
      action: function customFunction(editor){
          var cm = editor.codemirror;
          if(cm.getOption('fullScreen')) {
              SimpleMDE.toggleFullScreen(editor);
          }
      },
      className: "fa fa-close exit-fullscreen",
      title: goteo.texts['form-editor-close'] ? goteo.texts['form-editor-close'] : "Close"
  },
  {
      name: "bold",
      action: SimpleMDE.toggleBold,
      className: "fa fa-bold",
      title: goteo.texts['form-editor-bold'] ? goteo.texts['form-editor-bold'] : 'Bold'
  },
  {
      name: "italic",
      action: SimpleMDE.toggleItalic,
      className: "fa fa-italic",
      title: goteo.texts['form-editor-italic'] ? goteo.texts['form-editor-italic'] : "Italic"
  },
  {
      name: "strikethrough",
      action: SimpleMDE.toggleStrikethrough,
      className: "fa fa-strikethrough",
      title: goteo.texts['form-editor-strikethrough'] ? goteo.texts['form-editor-strikethrough'] : "Strikethrough"
  },
  {
      name: "heading",
      action: SimpleMDE.toggleHeadingSmaller,
      className: "fa fa-header",
      title: goteo.texts['form-editor-heading'] ? goteo.texts['form-editor-heading'] : "Heading"
  },
  {
      name: "heading-smaller",
      action: SimpleMDE.toggleHeadingSmaller,
      className: "fa fa-header fa-header-x fa-header-smaller",
      title: goteo.texts['form-editor-smaller_heading'] ? goteo.texts['form-editor-smaller_heading'] : "Smaller Heading"
  },
  {
      name: "heading-bigger",
      action: SimpleMDE.toggleHeadingBigger,
      className: "fa fa-header fa-header-x fa-header-bigger",
      title: goteo.texts['form-editor-bigger_heading'] ? goteo.texts['form-editor-bigger_heading'] : "Bigger Heading"
  },
  '|',
  {
      name: "code",
      action: SimpleMDE.toggleCodeBlock,
      className: "fa fa-code",
      title: goteo.texts['form-editor-code'] ? goteo.texts['form-editor-code'] : "Code"
  },
  {
      name: "quote",
      action: SimpleMDE.toggleBlockquote,
      className: "fa fa-quote-left",
      title: goteo.texts['form-editor-quote'] ? goteo.texts['form-editor-quote'] : "Quote"
  },
  {
      name: "unordered-list",
      action: SimpleMDE.toggleUnorderedList,
      className: "fa fa-list-ul",
      title: goteo.texts['form-editor-generic_list'] ? goteo.texts['form-editor-generic_list'] : "Generic List"
  },
  {
      name: "ordered-list",
      action: SimpleMDE.toggleOrderedList,
      className: "fa fa-list-ol",
      title: goteo.texts['form-editor-numbered_list'] ? goteo.texts['form-editor-numbered_list'] : "Numbered List"
  },
  '|',
  {
      name: "link",
      action: SimpleMDE.drawLink,
      className: "fa fa-link",
      title: goteo.texts['form-editor-create_link'] ? goteo.texts['form-editor-create_link'] : "Create Link"
  },
  {
      name: "image",
      action: SimpleMDE.drawImage,
      className: "fa fa-picture-o",
      title: goteo.texts['form-editor-insert_image'] ? goteo.texts['form-editor-insert_image'] : "Insert Image"
  },
  {
      name: "table",
      action: SimpleMDE.drawTable,
      className: "fa fa-table",
      title: goteo.texts['form-editor-insert_table'] ? goteo.texts['form-editor-insert_table'] : "Insert Table"
  },
  {
      name: "horizontal-rule",
      action: SimpleMDE.drawHorizontalRule,
      className: "fa fa-minus",
      title: goteo.texts['form-editor-insert_horizontal_line'] ? goteo.texts['form-editor-insert_horizontal_line'] : "Insert Horizontal Line"
  },
  '|',
  {
      name: "preview",
      action: SimpleMDE.togglePreview,
      className: "fa fa-eye no-disable",
      title: goteo.texts['form-editor-toggle_preview'] ? goteo.texts['form-editor-toggle_preview'] : "Toggle Preview"
  },
  {
      name: "side-by-side",
      action: SimpleMDE.toggleSideBySide,
      className: "fa fa-columns no-disable no-mobile",
      title: goteo.texts['form-editor-toggle_side_by_side'] ? goteo.texts['form-editor-toggle_side_by_side'] : "Toggle Side by Side"
  },
  {
      name: "fullscreen",
      action: SimpleMDE.toggleFullScreen,
      className: "fa fa-arrows-alt no-disable no-mobile",
      title: goteo.texts['form-editor-toggle_fullscreen'] ? goteo.texts['form-editor-toggle_fullscreen'] : "Toggle Fullscreen"
  },
  '|',
  {
      name: "guide",
      action: "https://simplemde.com/markdown-guide",
      className: "fa fa-question-circle",
      title: goteo.texts['form-editor-markdown_guide'] ? goteo.texts['form-editor-markdown_guide'] : "Markdown Guide"
  },
  '|',
  {
      name: "undo",
      action: SimpleMDE.undo,
      className: "fa fa-undo no-disable",
      title: goteo.texts['form-editor-undo'] ? goteo.texts['form-editor-undo'] : "Undo"
  },
  {
      name: "redo",
      action: SimpleMDE.redo,
      className: "fa fa-repeat no-disable",
      title: goteo.texts['form-editor-redo'] ? goteo.texts['form-editor-redo'] : "Redo"
  }
];

var markdowns = form.markdowns = {};
var _createMarkdownEditor = function() {
    var el = this;
    var toolbar = default_md_toolbar;
    if($(el).data('toolbar')) {
        var telements = $(el).data('toolbar').split(',');
        toolbar = default_md_toolbar.filter(function(v){
            return telements.indexOf(v.name) !== -1;
        });
    }
    var simplemde = new SimpleMDE({
        element: el,
        forceSync: true,
        autosave: false,
        promptURLs: true,
        spellChecker: false,
        toolbar: toolbar
    });

    // Tweak codemirror to accept drag&drop any file
    simplemde.codemirror.setOption("allowDropFileTypes", null);

    simplemde.codemirror.on('drop', function(codemirror, event) {
        if(!$(el).data('image-upload')) return;

        var loading_text = $(el).data('image-loading-text') || '![](loading image...)';

        if(event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length) {
          var images = $.grep(event.dataTransfer.files, function(file,i) {
            if(file && file.type && file.type.startsWith('image/')) {
              return true;
            }
            return false;
          });

          if(images.length) {
            // Do not allow predefined codemirror behaviour if are images
            event.preventDefault();
            event.stopPropagation();
            var $cm = $(el).closest('.markdown').find('.CodeMirror.CodeMirror-wrap');
            var $up = $('<div class="uploading">');
            $cm.prepend($up);

            var coords = codemirror.coordsChar({
              left: event.pageX,
              top: event.pageY
            });

            codemirror.replaceRange("\n" + loading_text + "\n", coords);
            coords.line++;
            coords.ch = 0;
            codemirror.setCursor(coords);

            _uploadImage(images, $(el).data('image-upload'), function(status, data) {
              if(status === 'progress') {
                $up.css('width',  (data * 100) + '%');
              } else {
                $up.remove();
              }
              if(status === 'success') {
                if(!data.length) data = [data];
                $.each(data, function(i,name){
                  codemirror.replaceRange("![](" + name + ")", coords, {line:coords.line, ch:loading_text.length});
                });
              }
              if(status === 'error') {
                alert('ERROR: ' + data);
              }
            });
          }
        }
    });

    markdowns[$(this).attr('id')] = simplemde;
};

var summernotes = form.summernotes = {};
var _createHtmlEditor = function() {
  var el = this;
  var summernote;
  var callbacks = {
    onFocus: function() {
      $(this).closest('.summernote').addClass('focused');
    },
    onBlur: function() {
      $(this).closest('.summernote').removeClass('focused');
    }
  };
  if($(el).data('image-upload')) {
    callbacks.onImageUpload = function(files) {
      var $sm = $(this).closest('.summernote');
      var $up = $('<div class="uploading">');
      $sm.prepend($up);
      _uploadImage(files, $(el).data('image-upload'), function(status, data) {
        if(status === 'progress') {
          $up.css('width',  (data * 100) + '%');
        } else {
          $up.remove();
        }
        if(status === 'success') {
          if(!data.length) data = [data];
          $.each(data, function(i,name){
            var image = $('<img>').attr('src', name);
            summernote.summernote('insertNode', image[0]);
          });
        }
        if(status === 'error') {
          alert('ERROR: ' + data);
        }
      });
    };
  }
  summernote = $(el).summernote({
      toolbar: [
          ['tag', ['style']],
          ['style', ['bold', 'italic', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['insert', ['link', 'picture', 'video', 'hr', 'table']],
          ['misc', ['fullscreen', 'codeview', 'help']]
        ],
      popatmouse: false,
      callbacks: callbacks
  });
  summernotes[$(this).attr('id')] = summernote;
};

$(function(){
    var initBindings = function() {
        //material switch checkbox
        $('.material-switch').on('click', function(e){
            e.preventDefault();
            var $checkbox = $(this).find('input[type="checkbox"]');
            if($checkbox.prop('disabled')) return;
            var text_yes = $(this).data('confirm-yes');
            var text_no = $(this).data('confirm-no');
            var current = $checkbox.prop('checked');
            if(current && text_no) {
                if(!confirm(text_no)) {
                    return false;
                }
            }
            if(!current && text_yes) {
                if(!confirm(text_yes)) {
                    return false;
                }
            }
            $checkbox.prop('checked', !current);
            $checkbox.change();
        });

        $('.material-switch').hammer().bind('swiperight', function() {
            $(this).find('input[type="checkbox"]').prop('checked', true);
        });

        $('.material-switch').hammer().bind('swipeleft', function() {
            $(this).find('input[type="checkbox"]').prop('checked', false);
        });

        /// AJAX auto updates fields
        $('.auto-save-property').each(function() {
            var $input = $(this);
            if(!$input.is('input')) {
                $input = $input.find('input');
                if($input.length) {
                    $(this).contents('label').css('cursor', 'pointer');
                }
            }
            var type = $(this).data('type');
            var _getValue = function() {
                if(type === 'boolean') {
                    return $(this).prop('checked');
                } else {
                    return $(this).val();
                }
            };
            var _setValue = function(val) {
                if(type === 'boolean') {
                    $(this).prop('checked', val);
                } else {
                    $(this).val(val);
                }
            };
            var url = $(this).data('url');
            var original = _getValue.call($input[0]);
            if(url && $input.is('input')) {
                $input.on('change', function(e) {
                    var val = _getValue.call($input[0]);
                    $.ajax({
                        url: url,
                        type: 'PUT',
                        data: {value: val}
                    }).done(function(data) {
                        original = _setValue.call($input[0], data.value);
                        $(document).trigger('form-boolean-changed', [$input[0]]);
                        if(data.message) alert(data.message);
                    }).fail(function(error) {
                        var json = JSON.parse(error.responseText);
                        var txt = json && json.error;
                        _setValue.call($input[0], original);
                        alert(txt ? txt : error.responseText ? error.responseText : error);
                    });
                });
            }

        });

        //////////////////////////////////////////////
        /// FORM with class "autoform"
        /// TODO: check how this works in ajax loading (after pronto)
        ///////////////////////////////////////

        // Create datepickers on date input types
        $('.autoform .datetimepicker, .autoform .datetimepicker > input').datetimepicker({
                format: 'DD/MM/YYYY',
                extraFormats: ['YYYY-MM-DD'],
                locale: goteo.locale
            }).on('dp.change', function (e) {
                // $('#publishing-date').val(e.date.format('YYYY/MM/DD'));
            });
        // Full time datetimepicker
        $('.autoform .datetimepicker-full, .autoform .datetimepicker-full > input').datetimepicker({
                locale: goteo.locale
            }).on('dp.change', function(e) {
              // TODO: some kind of solution to transform locale dates:
              // https://github.com/Eonasdan/bootstrap-datetimepicker/issues/1869
            });
        // Year only datetimepickers
        $('.autoform .datetimepicker-year, .autoform .datetimepicker-year > input').datetimepicker({
                format: 'YYYY',
                locale: goteo.locale,
                viewMode: 'years'
        });

        // Typeahead fields
        $('.autoform .input-typeahead .typeahead').typeahead('destroy');
        $('.autoform .input-typeahead').each(function () {
            var $this = $(this);
            var sources = $this.data('sources').split(',');
            var id_field = $this.data('value-field') ? $this.data('value-field') : 'id';
            var engines = [{
                minLength: 0,
                hint: true,
                highlight: true,
                classNames: {
                    hint: ''
                }
            }];
            sources.forEach(function(source) {
                if(goteo.typeahead_engines[source]) {
                    engines.push(goteo.typeahead_engines[source]({
                        defaults: true // Show a list of prefetch projects without typing
                    }));
                }
            });
            $.fn.typeahead.apply($this.find('.typeahead'), engines)
                .on('typeahead:active', function (event) {
                    $(event.target).select();
                })
                .on('typeahead:asyncrequest', function (event) {
                    $(event.target).addClass('loading');
                })
                .on('typeahead:asynccancel typeahead:asyncreceive', function (event) {
                    $(event.target).removeClass('loading');
                })
                // typeahead:select event is done when needed.
                // For example: assets/js/admin/stats.js
                .on('typeahead:select', function (event, datum, name) {
                    if ($(this).data('type') === "simple" ) {

                      $('#' + $(this).data('real-id')).val(datum[id_field]);

                    } else if ($(this).data('type') === "multiple") {

                      if ($('[id="'+$(this).data('real-id')+'"][value="'+datum['id']+'"]').length === 0) {

                        $('.bootstrap-tagsinput.help-text#'+$(this).data('real-id'))
                          .append('<span class="tag label label-lilac">'+ datum[id_field] +'<span id="remove-'+datum['id']+'-'+$(this).data('real-id')+'" data-real-id="'+ $(this).data('real-id')+ '" data-value="'+ datum['id'] + '"data-role="remove"></span></span>');

                        $('#remove-'+datum['id'].replace(/\./g, '\\.')+'-'+$(this).data('real-id')).click(function(){
                          if ($('input[id="'+$(this).data('real-id')+'"]').length > 1) {
                            $('input[id="'+$(this).data('real-id')+'"][value="'+datum['id']+'"]').remove();
                          } else {
                            $('input[id="'+$(this).data('real-id')+'"][value="'+datum['id']+'"]').value = "";
                          }
                          $(this).parent().remove();
                        });

                          $('input[id="' + $(this).data('real-id') + '"]').last().clone().appendTo($('#' + $(this).data('real-id')).last().parent()).val(datum['id']);
                      }
                      $('.typeahead').typeahead('close');
                    }
                })
                .on('typeahead:close', function(event) {
                  if ($(this).data('type') === "multiple" ) {
                    $(this).typeahead('val', '');
                  }
                })
                .on('typeahead:change', function (event) {
                    if($(this).val().trim() === '') $('#' + $(this).data('real-id')).val('');
                });

                if ($('.typeahead').find('[data-type="multiple"]')) {
                  if ($('span').find('[data-role="remove"]').length) {
                    $('span').find('[data-role="remove"]').click(function(){
                      if ($('input[id="'+$(this).data('real-id')+'"]').length > 1) {
                        $('input[id="'+$(this).data('real-id')+'"][value="'+$(this).data('value')+'"]').remove();
                      } else {
                        $('input[id="'+$(this).data('real-id')+'"][value="'+$(this).data('value')+'"]').value = "";
                      }
                      $(this).parent().remove();
                    });
                  }
                }
        });

        // Tags input fields
        $('.autoform .tagsinput').each(function(){
          // Tags with autocomplete (optional)
          var $this = $(this);
          // Typeahead remote url to fecth
          var url = $this.data('url');
          // Id and Text fields for typeahead
          var keyValue = $this.data('key-value') || 'tag';
          var keyText = $this.data('key-text') || 'tag';
          // Id and Text fields for tagsinput
          var itemValue = $this.data('item-value') || 'tag';
          var itemText = $this.data('item-text') || 'tag';
          var limit = $this.data('limit') || 5;
          var maxTags = $this.data('max-tags') || 10;
          var minLength = $this.data('min') || 0;
          // Optional values via json object
          var values = $this.data('values') || [];
          // if present in the url, will be replace by the query in typeahead
          var wildcard = $this.data('wilcard') || '%QUERY';
          // TODO: add a template via data attributes

          // Tagsinput options object
          var ops = {
            itemValue: itemValue,
            itemText: itemText,
            tagClass: 'label label-lilac',
            maxTags: maxTags
          };
          // Convert default values to object if non data-values defined
          if(!values || !values.length) {
            values = [];
            $this.val().split(',').forEach(function(val) {
                var o = {};
                o[itemValue] = val;
                o[itemText] = val;
                values.push(o);
            });
          }

          if(url) {
            var tags = new Bloodhound({
              datumTokenizer: Bloodhound.tokenizers.obj.whitespace(keyText),
              identify: function (o) { return o[keyValue]; },
              dupDetector: function (a, b) { return a[keyValue] === b[keyValue]; },
              queryTokenizer: Bloodhound.tokenizers.whitespace,
              prefetch: { // TODO: make it optional
                url: url,
                filter: function (response) {
                    return response;
                }
                // ,cache: false
              },
              remote: {
                wildcard: wildcard,
                url: url
                // ,cache: false
              }
            });
            tags.initialize();
            var engineWithDefaults = function (q, sync, async) {
                if (q === '') {
                    sync(tags.index.all());
                    async([]);
                } else {
                    tags.search(q, sync, async);
                }
            };

            ops.typeaheadjs = [{
                minLength: minLength,
                hint: true,
                highlight: true,
                classNames: {
                    hint: '',
                    menu: 'tt-menu tt-menu-clip'
                }
              },
              {
                name: 'tags',
                limit: limit,
                displayKey: keyText,
                source: engineWithDefaults
              }];
          }

          $this.tagsinput(ops);
          values.forEach(function(tag) {
            $this.tagsinput('add', tag);
          });
        });

        // Video
        var _addVideo = function(e) {
            var val = $(this).val();
            if(!val) return;
            var video = parseVideoURL(val);
            var input = this;
            var $container = $(this).closest('.media-container');
            var $holder = $container.find('.video-holder');
            var $embed = $container.find('.embed-responsive');

            // Add thumb
            $container.removeClass('loaded').removeClass('playing').addClass('loading');

            var putVideo = function(thumb) {
                $container.find('.cover-image').attr('src', thumb);
                $container.removeClass('loading').addClass('loaded');
                var iframe = $('<iframe>', {
                    src: video.src,
                    frameborder: 0,
                    allowfullscreen: true,
                    width: '100%',
                    height: '100%',
                });
                $container.find('.video-button').one('click', function() {
                    $embed.html(iframe);
                    $container.addClass('playing');
                });
            };

            if (video.type === 'youtube') {
                putVideo('https://img.youtube.com/vi/' + video.id + '/maxresdefault.jpg');
            } else if (video.type === 'vimeo') {
                $.getJSON("https://vimeo.com/api/v2/video/"+ video.id + ".json")
                 .done(function(res) {
                    putVideo(res[0].thumbnail_large);
                 })
                 .fail(function(e) { });
            } else if (video.type === 'framatube') {
                $.getJSON("https://peertube2.cpy.re/api/v1/videos/" + video.id)
                    .done(function(res) {
                        putVideo("https://framatube.org/lazy-static/previews/" + res.uuid + ".jpg");
                    })
                    .fail(function(e) { });
            }
        };

        $('.autoform input.online-video').on('paste', function(e){
            var that = this;
            setTimeout(function() {
                _addVideo.call(that, e);
            }, 100);
        });

        $('.autoform input.online-video').each(_addVideo);

        // HTML editors initializations
        $('.autoform .summernote > textarea').each(_createHtmlEditor);

        // MarkdownType initialization
        $('.autoform .markdown > textarea').each(_createMarkdownEditor);

        // Type editor chooser
        $('.autoform').on( 'change', '.form-control[data-editor-type]', function(e){
          e.preventDefault();
          e.stopPropagation();
          var el = this;
          var parts = $(el).attr('id').split('_');
          var target = parts[0] + '_' + $(el).data('editor-type');
          var $target = $('#' + target);
          var to = $(el).val();
          var from;

          if(markdowns[target]) from = 'md';
          if(summernotes[target]) from = 'html';

          if(from === to) return;
          if(from === 'html' && to === 'md') {
            // destroy summernote, initialize markdown
            summernotes[target].summernote('destroy');
            delete summernotes[target];
            // convert to markdown if possible
            if(TurndownService) {
              var service = new TurndownService();
              service.keep(['iframe', 'object']);
              $target.val(service.turndown($target.val()));
            }
            _createMarkdownEditor.call($target[0]);
          }
          if(from === 'md' && to === 'html') {
            // destroy markdown, initialize summernote
            markdowns[target].toTextArea();
            $target.val(markdowns[target].markdown($target.val()));
            delete markdowns[target];
            _createHtmlEditor.call($target[0]);
          }

        });

        var dropzones = form.dropzones = {};
        // Dropfiles initialization
        $('.autoform .dropfiles').each(function() {
            var $dz = $(this);
            var $error = $dz.find('.error-msg');
            var $list = $(this).find('.image-list-sortable');
            var $dnd = $(this).find('.dragndrop');
            var $form = $dz.closest('form');
            var multiple = !!$dz.data('multiple');
            var limit = parseInt($dz.data('limit'));
            var url = $dz.data('url') || null;
            var accepted_files = $dz.data('accepted-files') ? $dz.data('accepted-files') : null;
            var $template = $form.find('script.dropfile_item_template');
            // ALlow drag&drop reorder of existing files
            if(multiple && limit > 1) {
               Sortable.create($list[0], {
                    // group: '',
                    // , forceFallback: true
                    // Reorder actions
                    onStart: function(evt) {
                        $dnd.hide();
                    },
                    onEnd: function (evt) {
                        $dnd.show();
                        $list.removeClass('over');
                    },
                    onMove: function (evt) {
                        $list.removeClass('over');
                        $(evt.to).addClass('over');
                    }
                });
            }
            if($list.find('li').length >= limit) {
                $dnd.hide();
            }

            var _addImageCss = function($img, name, dataURL) {
              var url = dataURL ? dataURL : '/img/300x300c/' + name;
              $img.css({
                  backgroundImage:  'url(' + url + ')',
                  backgroundSize: 'cover'
              });
            };

            // Create the FILE upload
            var drop = new Dropzone($dnd.contents('div')[0], {
              url: url ? url : $form.attr('action'),
              uploadMultiple: multiple,
              createImageThumbnails: true,
              maxFiles: limit,
              maxFilesize: MAX_FILE_SIZE,
              autoProcessQueue: !!url, // no ajax post if no url
              dictDefaultMessage: $dz.data('text-upload'),
              acceptedFiles: accepted_files
            })
            .on('error', function(file, error) {
              $error.html(error.error ? error.error : error);
              $error.removeClass('hidden');
              drop.removeFile(file);
            })
            .on('thumbnail', function(file, dataURL) {
              // Add to list
              var $img = $form.find('li[data-name="' + file.name + '"] .image');
              _addImageCss($img, file.name, dataURL);
            })
            .on(url ? 'success' : 'addedfile', function(file, response) {
              var total = $list.find('li').length;
              if(total >= limit) {
                $error.html($dz.data('text-max-files-reached'));
                $error.removeClass('hidden');
                drop.removeFile(file);
                return false;
              }
              if(!Dropzone.isValidFile(file, accepted_files)) {
                $error.html($dz.data('text-file-type-error'));
                drop.removeFile(file);
                return false;
              }

              var name = file.name;
              var type = '';
              var download_url = '';
              var i;
              $error.addClass('hidden');
              // AJAX upload case, a response is defined
              if(response) {
                if(!response.success) {
                  $error.html(response.msg);
                  $error.removeClass('hidden');
                  for(i in response.files) {
                    if(!response.files[i].success)
                      $error.append('<br>' + response.files[i].msg);
                  }
                }
                for(i in response.files) {
                  if(response.files[i].originalName === name) {
                    name = response.files[i].name;
                    type = response.files[i].regularFile && response.files[i].type;
                    download_url = response.files[i].downloadUrl;
                  }
                }
              }
              var $li = $($template.html().replace('{NAME}', name));
              var $img = $li.find('.image');

              var re = /(?:\.([^.]+))?$/;
              var ext = re.exec(name)[1];
              $img.addClass('file-type-' + ext);

              if(response) {
                $li.append('<input type="file" display:"none" name="' + $dz.data('current') + '" value="' + name + '">');
                if($dz.data('markdown-link')) {
                  $li.find('.add-to-markdown').data('target', $dz.data('markdown-link'));
                  $li.find('.add-to-markdown').removeClass('hidden');
                }
                if(download_url) {
                  $li.find('.download-url').attr('href', download_url);
                  $li.find('.download-url').removeClass('hidden');
                }
                if(type) {
                  $img.addClass('file-type-' + type);
                } else {
                  // AJAX does not create thumbnail
                  _addImageCss($img, name);
                }
              }
              else {
                  // $img.css({backgroundSize: '25%'});
              }
              $list.append($li);

              if(total >= limit - 1) {
                  $dnd.hide();
              }
              // On response, input[type=file] is already uploaded
              if(response) {
                  drop.removeFile(file);
                  return;
              }

              // Input node with selected files. It will be removed from document shortly in order to
              // give user ability to choose another set of files.
              var inputFile = this.hiddenFileInput;
              // Append it to form after stack become empty, because if you append it earlier
              // it will be removed from its parent node by Dropzone.js.
              setTimeout(function(){
                // Set some unique name in order to submit data.
                inputFile.name = $dz.data('name');
                if(inputFile.files && inputFile.files.length) {
                  $li.append(inputFile);
                } else {
                  alert(goteo.texts['form-dragndrop-unsupported']);
                  $li.remove();
                  $dnd.show();
                }
                drop.removeFile(file);
              }, 0);
            });
            dropzones[$(this).attr('id')] = drop;

        });

        // Delete actions
        $('.autoform').on( 'click', '.image-list-sortable .delete-image', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $li = $(this).closest('li');
            var $drop = $(this).closest('.dropfiles');
            var $zone = $(this).closest('.image-zone');
            var $list = $(this).closest('.image-list-sortable');
            var $form = $(this).closest('form');
            var $error = $zone.next();
            var image = $li[0].getAttribute('data-name');
            $li.remove();
            $error.addClass('hidden');
            var limit = parseInt($drop.data('limit'));
            var total = $list.find('li').length;
            if(total < limit) {
                $zone.find('.dragndrop').show();
            }
            if ($(this).parent().siblings('input').attr('name').includes("current")) {
              $removed_input = $("input[name='" + $drop.data('removed') + "[]']");
              $cloned_input = $removed_input.get(0).cloneNode();
              $cloned_input.setAttribute('value', image);
              $removed_input.after($cloned_input);
            }
        });

        // Add to markdown
        $('.autoform').on( 'click', '.image-list-sortable .add-to-markdown', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $li = $(this).closest('li');
            var name = $li.data('name');
            var $form = $(this).closest('form');
            $form.find('.dragndrop').show();
            var target = $form.attr('name') + '_' + $(this).data('target');
            var md = markdowns[target];
            if(md) {
                md.value(md.value().replace(/\s+$/g, '') + "\n\n![](" + IMG_URL + '/600x600/' + name + ")");
            } else {
                sm = summernotes[target];
                if(sm) {
                    sm.summernote('insertImage', IMG_URL + '/600x600/' + name, name);
                }
            }
        });

        // handle exact geolocation
        $('.autoform').on('click', '.exact-location', function(e) {
            e.preventDefault();
            var lat,lng,formatted_address,radius;
            var $form = $(this).closest('form');
            var $modal = $('#modal-map-' + $form.attr('name'));
            var $map = $modal.find('.map');
            var $wrap = $modal.find('.input-block');
            var $search = $modal.find('.geo-autocomplete');
            var $radius = $modal.find('.geo-autocomplete-radius');
            var $input = $($(this).attr('href'));
            var title = $input.closest('.form-group').find('label:first').text();
            $modal.find('.modal-title').text(title);

            $search.data('geocoder-type', $input.data('geocoder-type'));
            $search.data('geocoder-item', $input.data('geocoder-item'));
            $(['address', 'city', 'region', 'zipcode', 'country_code', 'country', 'latitude', 'longitude', 'formatted_address', 'radius']).each(function(i, el){
                var el_dest = $input.data('geocoder-populate-' +  el);
                var $val = $(el_dest);
                var val = $val.text();
                if($val.is(':input')) val = $val.val();

                if(el === 'radius') {
                    radius = parseInt(val, 10) || 0;
                    $radius.data('geocoder-populate-' +  el, el_dest);
                } else {
                    $search.data('geocoder-populate-' +  el, el_dest);
                }
                if(el === 'latitude') {
                    lat = parseFloat(val) || 0;
                }
                if(el === 'longitude') {
                    lng = parseFloat(val) || 0;
                }
                if(el === 'formatted_address') {
                    formatted_address = val;
                }
            });
            $map.data('map-latitude', lat);
            $map.data('map-longitude', lng);
            if(!lat || !lng) {
                $map.data('map-address', $input.val());
            }
            if(radius) {
                $map.data('map-radius', radius);
                $radius.val(radius);
                $wrap.addClass('show-radius');
            }
            $search.val((lat && lng) ? formatted_address : $input.val());

            $modal.modal('show');
            locator.setGoogleMapPoint($map[0]);
            locator.setGoogleAutocomplete('#' + $search.attr('id'));
        });
    };

    initBindings();
    $(window).on("pronto.render", function(e){
        initBindings();
    });

    $(".autoform .modal-map").on("shown.bs.modal", function () {
        goteo.trace('shown locator map', locator);
        google.maps.event.trigger(locator.map, "resize");
        locator.map.setCenter(locator.marker.getPosition());
    });

    // Handle buttons with confirmation
    $('form.autoform').on( 'click', 'button[data-confirm]', function(e) {
        if(!confirm($(this).data('confirm'))) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });

    // Asks user before leave unsaved
    var formChanged = false;
    $('form[data-confirm]').on('change', ':input', function(){
     //':input' selector get all form fields even textarea, input, or select
      formChanged = $(this).closest('form').data('confirm');
    });

    $('form[data-confirm]').on('submit', function(){
        formChanged = false;
    });

    $(window).on('beforeunload', function() {
      if(formChanged){
         return formChanged;
       }
    });
});
