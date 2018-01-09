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

// This file requires jquery.animate-css.js

$(function(){

   // Project widget flip backside
    var toggleFlipWidgetBackside = function(e) {
        e.stopPropagation();
        e.preventDefault();
        // console.log('flip', e);
        var $that = $(this);
        $widget = $(this).closest('.flip-widget');
        $target = $($that.attr('href'));
        // if($target.length === 0)
        //     $target = $widget.find('.backside');
        var inAnimation = 'flipInY';
        var outAnimation = 'flipOutY';

        if($target.hasClass('active')) {
            $target.animateCss(outAnimation, function() {
                $target.removeClass('active');
            });
        } else {
            $('.flip-widget .backside.active').animateCss(outAnimation, function(t) {
                $(this).removeClass('active');
            });
            $target.addClass('active').animateCss(inAnimation);
        }
    };


    $('body').on('click', '.flip-widget .flip', toggleFlipWidgetBackside);

});
