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

$(
    function () {
        // Send the form via AJAX
        $('.autoform').on(
            'click', '.add-question', function (e) {
                e.preventDefault();
                var $form = $(this).closest('form');
                var $list = $form.find('.question-list');
                var serial = $form.serialize() + '&' + encodeURIComponent($(this).attr('name')) + '=';
                // console.log('add question', serial);

                $but = $(this).hide();
                $list.find('>.text-danger').remove();
                $list.append('<div class="loading"></div>');
                $.ajax(
                    {
                        type: $form.attr('method'),
                        url: $form.attr('action'),
                        data: serial
                    }
                ).done(
                    function (data) {
                        var $data = $(data);
                        $list.append($data.hide());
                        $data.slideDown();
                    }
                ).fail(
                    function (data) {
                        $list.append('<p class="text-danger">' + data.responseText + '</p>');
                    }
                ).always(
                    function () {
                        $but.show();
                        $list.find('>.loading').remove();
                    }
                );
            }
        );

        $('form.autoform').on(
            'click', '.remove-question', function (e) {
                if(e.isPropagationStopped()) { return false;
                }
                e.preventDefault();
                var $but = $(this);
                var $form = $but.closest('form');
                var $list = $form.find('.question-list');
                var serial = $form.serialize() + '&' + encodeURIComponent($but.attr('name')) + '=';
                var $item = $but.closest('.panel');
                $but.hide().after('<div class="loading"></div>');
                $item.find(':input').attr('disabled', true);
                $.ajax(
                    {
                        type: $form.attr('method'),
                        url: $form.attr('action'),
                        data: serial
                    }
                ).done(
                    function () {
                        $item.slideUp(
                            function () {
                                $(this).remove();
                            }
                        );
                    }
                ).fail(
                    function (data) {
                        console.log('An error occurred.', data);
                    }
                ).always(
                    function () {
                        $but.show().next('.loading').remove();
                    }
                );
            }
        );

        var list = document.getElementById("question-list");
        new Sortable(list);
    
    }
);
