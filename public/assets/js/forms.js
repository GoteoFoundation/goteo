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
    $('.autoform .material-switch').on('click', function(e){
        e.preventDefault();
        var $checkbox = $(this).find('input[type="checkbox"]');
        $checkbox.prop('checked', !$checkbox.prop('checked'));
    });

    // Create datepickers on date input types
    $('.autoform .datepicker, .autoform .datepicker > input').datetimepicker({
            format: 'DD/MM/YYYY',
            extraFormats: ['YYYY-MM-DD'],
            locale: goteo.locale,
        });
        // .on('dp.change', function (e) {
        //         _activate_calendar();
        //         $('#publishing-date').val(e.date.format('YYYY/MM/DD'));
        // });

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
        var $error = $dz.next();
        var $list = $(this).find('.image-list-sortable');
        var $dnd = $(this).find('.dragndrop');
        var $form = $dz.closest('form');
        var multiple = !!$dz.data('multiple');
        var $template = $form.find('script.dropfile_item_template');
        // ALlow drag&drop reorder of existing files
        if(multiple) {
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
        // Create the FILE upload

        var drop = new Dropzone($dnd.contents('div')[0], {
            url: $dz.data('url') ? $dz.data('url') : null,
            uploadMultiple: multiple,
            createImageThumbnails: true,
            maxFiles: $dz.data('limit'),
            autoProcessQueue: !!$dz.data('auto-process'), // no ajax post
            dictDefaultMessage: $dz.data('text-upload')
        })
        .on('error', function(file, error) {
            $error.html(error.error);
            $error.removeClass('hidden');
            // console.log('error', error);
        })
        .on('thumbnail', function(file, dataURL) {
            // Add to list
            var $img = $form.find('li[data-name="' + file.name + '"] .image');
            $img.css({
                backgroundImage:  'url(' + dataURL + ')',
                backgroundSize: 'cover'
            });
            // console.log('thumbnail', file);
        })
        .on('addedfile', function(file) {
            // console.log('added', file);
            var $li = $($template.html().replace('{NAME}', file.name));
            $li.find('.image').css({backgroundSize: '25%'});
            $list.append($li);
            // TODO put filetypes as default in URL
            $error.addClass('hidden');

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
        var $zone = $(this).closest('.image-zone');
        var $form = $(this).closest('form');
        var $error = $zone.next();
        $li.remove();
        $error.addClass('hidden');
        $form.find('.dragndrop').show();
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
            md.value(md.value().replace(/\s+$/g, '') + "\n\n![](" + SRC_URL + '/image/600x600/' + name + ")");
        }
    });

});
