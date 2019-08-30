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
    // Send the form via AJAX
    $('#autoform_social_commitment').on('click', 'label', function(e){
        var $label = $(this);
        var $item = $label.find('input');
        var social_commitment=$item.val();

        if(social_commitment){

            $.ajax({
                url: '/api/social-commitment/ods-suggestion',
                type: 'POST',
                data: {
                    'social_commitment' : social_commitment
                }
            }).done(function(data) {
                $("#sdgs_suggestion_label").show();
                $("#sdgs_suggestion").html(data);
                $('[data-toggle="tooltip"]').tooltip();
            }); 
        }

        else
            $("#sdgs_suggestion_label").hide();
            $("#sdgs_suggestion").html('');
    });

    $('.pre-help').on('click', '#sdgs_suggestion img', function(e){
        var $ods = $(this);
        var $ods_id=$ods.data('value');

        var $item=$('#autoform_sdgs').find('input[value="' + $ods_id + '"]');
        $item.prop('checked', true);
        $item.parent().animateCss('wobble');

    });

    $('[data-toggle="tooltip"]').tooltip(); 


});


