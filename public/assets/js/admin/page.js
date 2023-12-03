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

    let mdeditor;
    let summernote;
    const summernote_config = {
        toolbar: [
            ['tag', ['style']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video', 'hr', 'table']],
            ['misc', ['fullscreen', 'codeview', 'help']]
        ],
        popatmouse: false,
        callbacks: {
            onFocus: function () {
                // console.log('Editable area is focused');
                $(this).closest('.summernote').addClass('focused');
            },
            onBlur: function () {
                // console.log('Editable area loses focus');
                $(this).closest('.summernote').removeClass('focused');
            }
        }
    };

    const $textarea = $('textarea#autoform_content');

    function activateMD() {
        mdeditor = new SimpleMDE({
            element: $textarea[0],
            spellChecker: false,
            promptURLs: true,
            forceSync: true
        });

        mdeditor.render();
    }

    function activateHTML() {
        summernote = $textarea.summernote(summernote_config);
    }

    const $type = $('#autoform_type');

    $type.change(function () {
        if (this.value === "html") {
            mdeditor.toTextArea();
            activateHTML();
        } else if (this.value === "md") {
            summernote.summernote('destroy');
            activateMD();
        }
    });

    if ($type.val() === "html")
        activateHTML();
    else if ($type.val() === "md")
        activateMD();

});
