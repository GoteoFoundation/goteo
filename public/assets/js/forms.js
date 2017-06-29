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

$(function(){
    //material switch checkbox
    $('.autoform .material-switch').on('click', function(e){
        e.preventDefault();
        var $checkbox = $(this).find('input[type="checkbox"]');
        console.log('before',$checkbox[0].checked, $checkbox.attr('id'));
        $checkbox.prop('checked', !$checkbox.prop('checked'));
        console.log('after',$checkbox[0].checked);
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

    // MarkdownType initialization
    $('.autoform .markdown > textarea').each(function() {
        var simplemde = new SimpleMDE({
            element: this,
            spellChecker: false,
            promptURLs: true,
            forceSync: true
         });
    });

    var dropzones = [];
    // Dropfiles initialization
    $('.autoform .dropfiles').each(function() {
        var $dz = $(this);
        var element = $dz.find('.dragndrop>div').get(0);
        var $form = $dz.closest('form');
        dropzones.push(
            new Dropzone(element, {
                url: $dz.data('url') ? $dz.data('url') : null,
                uploadMultiple: !!$dz.data('multiple'),
                createImageThumbnails: true,
                parallelUploads: 100,
                maxFiles: $dz.data('limit'),
                autoProcessQueue: !!$dz.data('auto-process'),
                dictDefaultMessage: $dz.data('text-upload')
            })
            .on('addedfile', function(file) {
                // Input node with selected files. It will be removed from document shortly in order to
                // give user ability to choose another set of files.
                var inputFile = this.hiddenFileInput;
                // Append it to form after stack become empty, because if you append it earlier
                // it will be removed from its parent node by Dropzone.js.
                setTimeout(function(){
                    // Set some unique name in order to submit data.
                    inputFile.name = $dz.data('name');
                    $form[0].appendChild(inputFile);
                }, 0);
            })
        );

    });

    // $('.autoform').on('submit', function(e){
    //     e.preventDefault();
    //     e.stopPropagation();

    //     console.log('submit');
    //     if(dropzones.length) {
    //         console.log('submit process');
    //         // Process the first (will submit everything)
    //         dropzones[0].processQueue();
    //     }
    // });
});
