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
    // Menu behaviour
    var toggleMenu = function(e) {
        e.stopPropagation();
        e.preventDefault();
        var button = this;
        var $button = $(this);
        // console.log($button.attr('class'));
        var target = $button.data('target');
        var $t = $('#' + target);
        var $show = $button.find('.show-menu');
        var $close = $button.find('.close-menu');
        // xs devices has css position to fixed
        var isDesktop = $t.css('position') === 'absolute';
        if(!isDesktop && $close.length) {
            $button.addClass('active');
        }
        var inAnimation = 'slideInRight';
        var outAnimation = 'slideOutRight';
        if(isDesktop) {
            inAnimation = 'foldInUp';
            outAnimation = 'foldOutUp';
        }

        // Close other opened
        $('.top-menu.active:not([id="' + target + '"])').removeClass('active');
        $button.siblings('.active').removeClass('active');
        $('#main-content').off();

        if($t.hasClass('active')) {
            if(isDesktop) {
                $close.animateCss('flipOutX', function() {
                    $button.removeClass('active');
                    $show.animateCss('flipInX');
                });
            }
            $t.animateCss(outAnimation, function(){
                $t.removeClass('active');
                $t.find('.submenu.active').removeClass('active');
            });
        } else {
            if(isDesktop) {
                $show.animateCss('flipOutX', function() {
                    $button.addClass('active');
                    $close.animateCss('flipInX');
                });
            }
            $t.addClass('active').animateCss(inAnimation, function(){
                $('#main-content').on('click', function(e){
                    toggleMenu.call(button, e);
                    $(this).off();
                });
            });
        }
    };

    var toggleSubMenu = function(e) {
        e.stopPropagation();
        e.preventDefault();
        var that = this;
        var $s = $(this).next('.submenu');
        var isDesktop = $s.css('position') === 'absolute';
        var inAnimation = 'slideInRight';
        var outAnimation = 'slideOutRight';
        if(isDesktop) {
            // inAnimation = 'flipInY';
            // outAnimation = 'flipOutY';
            // inAnimation = 'fadeInDown';
            // outAnimation = 'fadeOutUp';
            // inAnimation = 'fadeInRight';
            // outAnimation = 'fadeOutLeft';
            inAnimation = 'foldInUp';
            outAnimation = 'foldOutUp';
        }
        $('.top-menu.active').find('.submenu.active').not($s).removeClass('active');

        if($s.hasClass('active')) {
            $s.animateCss(outAnimation, function() {
                $s.removeClass('active');
            });
        } else {
            $s.find('li a.back').on('click', function(e) {
                toggleSubMenu.call(that, e);
            });
            $s.addClass('active').animateCss(inAnimation);
        }
    };

    $('.toggle-menu').on('click', toggleMenu);
    $('.top-menu .toggle-submenu').on('click', toggleSubMenu);

});
