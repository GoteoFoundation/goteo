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
function parseVideoURL (url) {
    // - Supported YouTube URL formats:
    //   - http://www.youtube.com/watch?v=My2FRPA3Gf8
    //   - http://youtu.be/My2FRPA3Gf8
    //   - https://youtube.googleapis.com/v/My2FRPA3Gf8
    //   - https://m.youtube.com/watch?v=My2FRPA3Gf8
    // - Supported Vimeo URL formats:
    //   - http://vimeo.com/25451551
    //   - http://player.vimeo.com/video/25451551
    // - Also supports relative URLs:
    //   - //player.vimeo.com/video/25451551

    url.match(/(http:|https:|)\/\/(player.|www.|m.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/);

    var type, src;
    if (RegExp.$3.indexOf('youtu') > -1) {
        type = 'youtube';
        src = '//youtube.com/embed/' +  RegExp.$6 + '?wmode=Opaque&autoplay=1';
    } else if (RegExp.$3.indexOf('vimeo') > -1) {
        type = 'vimeo';
        src = '//player.vimeo.com/video/' + RegExp.$6 + '?title=0&byline=0&portrait=0&autoplay=1';
    }

    return {
        type: type,
        src: src,
        id: RegExp.$6
    };
}

$(function(){
    //material switch checkbox
    $('.material-switch').on('click', function(e){
        e.preventDefault();
        var text_yes = $(this).data('confirm-yes');
        var text_no = $(this).data('confirm-no');
        var $checkbox = $(this).find('input[type="checkbox"]');
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
            // save previous value
            // $input.on('focus', function(e) {
            //     original = _getValue.call($input[0]);
            // });
            $input.on('change', function(e) {
                var val = _getValue.call($input[0]);
                // console.log('change', val);
                $.ajax({
                    url: url,
                    type: 'PUT',
                    data: {value: val}
                }).success(function(data) {
                    original = _setValue.call($input[0], data);
                    $(document).trigger('form-boolean-changed', [$input[0]]);
                    // console.log('saved', data);
                }).fail(function(error) {
                    var json = JSON.parse(error.responseText);
                    var txt = json && json.error;
                    _setValue.call($input[0], original);
                    // console.log('fail', json, txt, error);
                    alert(txt ? txt : error.responseText ? error.responseText : error);
                });
            });
        }

    });

    //////////////////////////////////////////////
    /// FORM with class "autoform"
    ///////////////////////////////////////

    // Create datepickers on date input types
    $('.autoform .datepicker, .autoform .datepicker > input').datetimepicker({
            format: 'DD/MM/YYYY',
            extraFormats: ['YYYY-MM-DD'],
            locale: goteo.locale
            // ,debug: true
        });
        // .on('dp.change', function (e) {
        //         _activate_calendar();
        //         $('#publishing-date').val(e.date.format('YYYY/MM/DD'));
        // });
    // Year only datepickers
    $('.autoform .datepicker-year, .autoform .datepicker-year > input').datetimepicker({
            format: 'YYYY',
            locale: goteo.locale,
            viewMode: 'years'
            // ,debug: true
        });

    $('.autoform .tagsinput').each(function(){
      // Tags with autocomplete (optional)
      var $this = $(this);
      var url = $this.data('url');
      var displayKey = $this.data('display-key') || 'tag';
      var displayValue = $this.data('display-value') || 'tag';
      var wildcard = $this.data('wilcard') || '%QUERY';
      var ops = { tagClass: 'label label-lilac label-big' };
      if(url) {
        var tags = new Bloodhound({
          datumTokenizer: function(datum) {
            return Bloodhound.tokenizers.whitespace(datum.tag);
          },
          // datumTokenizer: Bloodhound.tokenizers.whitespace,
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          remote: {
            wildcard: wildcard,
            url: url
          }
        });
        tags.initialize();
        ops.typeaheadjs = [
          {
            highlight: true,
            //other options
          },
          {
            name: 'tags',
            displayKey: displayKey,
            valueKey: displayValue,
            source: tags.ttAdapter()
          }];
        // console.log('tags', tags, tags.ttAdapter());
      }
      $this.tagsinput(ops);

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

        console.log('adding video', val, video,e);
        // Add thumb
        $container.removeClass('loaded').removeClass('playing').addClass('loading');

        var putVideo = function(thumb) {
            console.log('putting thumb');
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
        }
        else if (video.type === 'vimeo') {
            $.getJSON("https://vimeo.com/api/v2/video/"+ video.id + ".json")
             .success(function(res) {
                console.log('videmo ok', res);
                putVideo(res[0].thumbnail_large);
             })
             .fail(function(e){
                console.log('error vimeo', e.responseText);
             });
        }
    };
    $('.autoform input.online-video').on('paste', function(e){
        var that = this;
        setTimeout(function() {
            _addVideo.call(that, e);
        }, 100);
    });
    $('.autoform input.online-video').each(_addVideo);


    // MarkdownType initialization
    var markdowns = {};
    $('.autoform .markdown > textarea').each(function() {
        var el = this;
        // console.log('found textarea', el);
        var simplemde = new SimpleMDE({
            element: el,
            forceSync: true,
            autosave: false,
            promptURLs: true,
            spellChecker: false
        });
        // simplemde.codemirror.on('change', function() {
        //     console.log(simplemde.value());
        //     $(el).html(simplemde.value());
        //     console.log(document.getElementById('autoform_text').innerHTML);
        // });

        markdowns[$(this).attr('id')] = simplemde;
    });

    var dropzones = {};
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
                onChoose: function(evt) {
                    // console.log('choose');
                    $dnd.hide();
                },
                onEnd: function (evt) {
                    // console.log('end');
                    $dnd.show();
                    $list.removeClass('over');
                },
                onMove: function (evt) {
                    // console.log('move');
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
            autoProcessQueue: !!url, // no ajax post if no url
            dictDefaultMessage: $dz.data('text-upload'),
            acceptedFiles: accepted_files
        })
        .on('error', function(file, error) {
            $error.html(error.error ? error.error : error);
            $error.removeClass('hidden');
            drop.removeFile(file);
            // console.log('error', error);
        })
        .on('thumbnail', function(file, dataURL) {
            // Add to list
            // console.log('thumbnail', file, dataURL);
            var $img = $form.find('li[data-name="' + file.name + '"] .image');
            _addImageCss($img, file.name, dataURL);
        })
        .on(url ? 'success' : 'addedfile', function(file, response) {
            var total = $list.find('li').length;
            // console.log(response ? 'success' : 'added', file, 'total', total, 'limit', limit, 'response', response);
            if(total >= limit) {
                $error.html($dz.data('text-max-files-reached'));
                $error.removeClass('hidden');
                drop.removeFile(file);
                // console.log($dz.data('text-max-files-reached'), $error.html());
                return false;
            }
            if(!Dropzone.isValidFile(file, accepted_files)) {
                // console.log('not accepted file', file, accepted_files);
                $error.html($dz.data('text-file-type-error'));
                drop.removeFile(file);
                return false;
            }
            var name = file.name;
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
                    }
                }
            }
            var $li = $($template.html().replace('{NAME}', name));
            var $img = $li.find('.image');

            var re = /(?:\.([^.]+))?$/;
            var ext = re.exec(name)[1];
            $img.addClass('file-type-' + ext);
            console.log('extension',ext,$img.attr('class'));

            if(response) {
                $li.append('<input type="hidden" name="' + $dz.data('current') + '" value="' + name + '">');
                if($dz.data('markdown-link')) {
                    $li.find('.add-to-markdown').data('target', $dz.data('markdown-link'));
                    $li.find('.add-to-markdown').removeClass('hidden');
                }
                // AJAX does not create thumbnail
                _addImageCss($img, name);
            }
            else {
                $img.css({backgroundSize: '25%'});
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
                // console.log('adding file', $dz.data('name'), inputFile);

                $li.append(inputFile);
                drop.removeFile(file);
            }, 0);
        });
        dropzones[$(this).attr('id')] = drop;

    });

    // Delete actions
    $('.autoform').on( 'click', '.image-list-sortable .delete-image', function(e) {
        e.preventDefault();
        e.stopPropagation();
        // console.log('remove');
        var $li = $(this).closest('li');
        var $drop = $(this).closest('.dropfiles');
        var $zone = $(this).closest('.image-zone');
        var $list = $(this).closest('.image-list-sortable');
        var $form = $(this).closest('form');
        var $error = $zone.next();
        $li.remove();
        $error.addClass('hidden');
        var limit = parseInt($drop.data('limit'));
        var total = $list.find('li').length;
        if(total < limit) {
            $form.find('.dragndrop').show();
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
            md.value(md.value().replace(/\s+$/g, '') + "\n\n![](" + SRC_URL + '/img/600x600/' + name + ")");
        }
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
        console.log('changed', formChanged);
         return formChanged;
       }
    });
});
