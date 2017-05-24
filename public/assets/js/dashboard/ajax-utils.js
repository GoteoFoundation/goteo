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

    $(".auto-update-projects-interests").on('change', ".interest", function (e) {
        var value = $(this).is(":checked") ? 1 : 0;
        var id = $(this).attr('id');
        var $parent = $(this).closest('.auto-update-projects-interests');
        $.ajax({
            url: "/dashboard/ajax/projects-suggestion",
            data: { 'id' : id, 'value' : value  },
            type: 'post',
            success: function(html) {
                $parent.html(html);
            }
        });
    });

    $(".auto-update-projects-interests").on('click', ".more-projects-button", function (e) {
        e.preventDefault();
        var $parent = $(this).closest('.auto-update-projects-interests');
        var total = $parent.find('.widget-element').length;
        $(this).remove();
        $.get('/dashboard/ajax/projects-suggestion', {offset:total, 'show': 'projects'}, function(html) {
            $parent.contents('.projects-container').append(html);
        });
    });

});
