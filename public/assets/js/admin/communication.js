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

$(function () {

    if ($('.autoform').length) {

        if (document.getElementById('filter-select').value > 0) {
            document.getElementById('filter-edit').href = "/admin/filter/edit/" + document.getElementById('filter-select').value;
        }

        document.getElementById('filter-select').onchange = function(){
            document.getElementById('filter-edit').href = "/admin/filter/edit/" + this.value;
        }

        document.getElementById('templates').onchange = function() {
            if (this.value == 33) document.getElementById('dropzone-image').classList.remove('hidden');
            else document.getElementById('dropzone-image').classList.add('hidden');
        }

        $('#templates').change();

        var mdeditor = [];
        var summernote = [];
        var summernote_config = {
            toolbar: [
                ['tag', ['style']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                // ['font', ['strikethrough', 'superscript', 'subscript']],
                // ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                // ['height', ['height']],
                ['insert', ['link', 'picture', 'video', 'hr', 'table']],
                ['misc', ['fullscreen', 'codeview', 'help']]
            ],
            popatmouse: false,
            callbacks: {
                onFocus: function() {
                // console.log('Editable area is focused');
                $(this).closest('.summernote').addClass('focused');
                },
                onBlur: function() {
                // console.log('Editable area loses focus');
                $(this).closest('.summernote').removeClass('focused');
                }
            }
        };

        $('#text').change(function(){
            if (this.value === "html") {
                $('textarea.editor').each(function(i) {
                    mdeditor[i].toTextArea();
                    summernote[i] = $(this).summernote(summernote_config);
                });
            }
            else if (this.value === "md"){
                $('textarea.editor').each(function(i) {
                    summernote[i].summernote('destroy');
                    mdeditor[i] = new SimpleMDE({
                        element: this,
                        spellChecker: false,
                        promptURLs: true,
                        forceSync: true
                    });
                  });
            }
        });

        $('textarea.editor').each(function(i) {
            var el = this;
            mdeditor[i] = new SimpleMDE({
                element: this,
                spellChecker: false,
                promptURLs: true,
                forceSync: true
            });

            // Tweak codemirror to accept drag&drop any file
            mdeditor[i].codemirror.setOption("allowDropFileTypes", null);

            mdeditor[i].codemirror.on('drop', function(codemirror, event) {
                // console.log('codemirror',codemirror,'event',event);

                var loading_text = '![](loading image...)';

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
                        var $cm = $(el).closest('.form-group').find('.CodeMirror.CodeMirror-wrap');
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

                            if(status === 'error')
                                alert('ERROR: ' + data);
                        });
                    }
                }

            });
            mdeditor[i].render();
          });
    }

    function refreshMailValues() {
        $("tr[id^='tr-']").each(function(index) {

            if (document.getElementsByClassName('model-communication').length > 0) {
                $.ajax({
                   url: '/api/communication/' + $(this).find('.td-id').find('.id')[0].innerHTML + '/success',
                   'method': 'GET',
               })
               .done(function(data){
                   $("tr[id^='tr-"+ data.id +"']").find('.td-success').find('.text').children()[0].innerHTML = data.success + " %";
               });
            }
            else if (document.getElementsByClassName('model-mail').length > 0) {
                $(this).find('.td-id').addClass('loading');
                $.ajax({
                    url: '/api/communication/' + document.getElementById('communication-id').innerHTML + '/mail/' + $(this).find('.td-id').find('.id')[0].innerHTML,
                    'method': 'GET'
                })
                .done(function(data){
                    if (data.status !== "sent") {
                        $("tr[id^='tr-"+ data.id +"']").find('.td-sent').find('.text')[0].innerHTML = data.sent;
                        $("tr[id^='tr-"+ data.id +"']").find('.td-failed').find('.text')[0].innerHTML = data.failed;
                        $("tr[id^='tr-"+ data.id +"']").find('.td-pending').find('.text')[0].innerHTML = data.pending;
                        $("tr[id^='tr-"+ data.id +"']").find('.td-status').find('.text')[0].innerHTML = data.status;
                        $("tr[id^='tr-"+ data.id +"']").find('.td-percent').find('.text').children()[0].style.backgroundColor = 'hsl(' + 120 * data.percent/100 +',45%,50%)';
                        if (data.status == "sent") $("tr[id^='tr-"+ data.id +"']").find('.td-id').removeClass('loading');
                        $("tr[id^='tr-"+ data.id +"']").find('.td-percent').find('.text').children()[0].innerHTML = data.percent + " %";
                    }

                    $("tr[id^='tr-"+ data.id +"']").find('.td-success').find('.text').children()[0].style.backgroundColor = 'hsl(' + 120 * data.success/100 +',45%,50%)';
                    $("tr[id^='tr-"+ data.id +"']").find('.td-success').find('.text').children()[0].innerHTML = data.success + " %";
                });
            }
         });
    }

    setInterval(function() {
        refreshMailValues();
    }, 10000);
});
