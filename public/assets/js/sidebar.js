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


// // Function from David Walsh: http://davidwalsh.name/css-animation-callback
function whichTransitionEvent(){
  var t,
      el = document.createElement("fakeelement");

  var transitions = {
    "transition"      : "transitionend",
    "OTransition"     : "oTransitionEnd",
    "MozTransition"   : "transitionend",
    "WebkitTransition": "webkitTransitionEnd"
  };

  for (t in transitions){
    if (el.style[t] !== undefined){
      return transitions[t];
    }
  }
}

var transitionEnd = whichTransitionEvent();

$(function(){

    var $window = $(window);
    var $body = $('body');
    var $sidebar = $('#sidebar-menu');
    var $wrap = $('#sidebar-menu .sidebar-wrap');
    var $toggle = $('#sidebar-menu-toggle');
    var $main = $('#main');
    var $footer = $('#footer');
    // $body.addClass('loaded');
    setTimeout(function(){$body.addClass('loaded');}, 500);

    var isMobile = function() {
        // xs devices has css position to absolute or fixed
        return $sidebar.css('position') === 'absolute' || $sidebar.css('position') === 'fixed';
    };

    var toggleSidebar = function() {
        // console.log('toggle');
        if($body.hasClass('has-sidebar')) {
            // console.log($body.attr('class'));
            if($body.hasClass('sidebar-opened')) {
                // console.log('close');
                $sidebar.animateCss('fadeOut');
                $toggle.css('opacity', 0);
                $wrap.animateCss('slideOutLeft', function() {
                    // console.log('end');
                    // $toggle.css('opacity', 1).animateCss('fadeIn');
                    $toggle.css({opacity: 1});
                    $body.removeClass('sidebar-opened');
                });
            } else {
                $body.addClass('sidebar-opened');
                $sidebar.animateCss('fadeIn');
                $toggle.css('opacity', 0);
                $wrap.animateCss('slideInLeft', function() {
                    // console.log('end');
                    $toggle.css({opacity: 1});
                    // $toggle.css('opacity', 1).animateCss('fadeIn');
                });
            }
        }
    };

    var toggleSubMenu = function(e) {
        e.stopPropagation();
        e.preventDefault();
        var that = this;
        var $li = $(this).closest('li');
        var $s = $(this).next('.submenu');

        var $others = $sidebar.find('li.active .submenu').not($s);
            $others.slideUp(function() {
                $others.closest('li.active').removeClass('active');
            });


        if($li.hasClass('active')) {
            $s.slideUp(function() {
                $li.removeClass('active');
            });
        } else {
            $li.addClass('active');
            $s.hide().slideDown();
        }
        // if($li.hasClass('active')) {
        //     $s.animateCss('foldOutUp', function() {
        //         $li.removeClass('active');
        //     });
        // } else {
        //     $li.addClass('active');
        //     $s.animateCss('foldInUp');
        // }
    };

    // Sidebar toggle
    $('body.has-sidebar').on('click', '.toggle-sidebar', toggleSidebar);
    // Swipper detector
    if($body.hasClass('has-sidebar')) {
        // Enable text selection
        delete Hammer.defaults.cssProps.userSelect;
        // var $slider = $('body.has-sidebar');
        var prevented = false;
        $body.on('mouseover', '.slider,.material-switch', function(){
            // console.log('mouseover swipe');
            prevented = true;
        });
        $body.on('mouseout', '.slider,.material-switch', function(){
            // console.log('mouseout swipe');
            prevented = false;
        });
        $body.on('touchstart', '.slider,.material-switch', function(e){
            // console.log('touchstart swipe', e);
            prevented = true;
        });
        $body.on('touchend', '.slider,.material-switch', function(){
            // console.log('touchend swipe');
            setTimeout(function(){prevented = false;});
        });
        $body.hammer().bind("swiperight", function(){
            // console.log('body swipe right, prevented: ', prevented);
            if(prevented) return;
            if(!isMobile()) return;
            if(!$body.hasClass('sidebar-opened'))
                toggleSidebar();
        });
        $body.hammer().bind("swipeleft", function(){
            if(prevented) return;
            if(!isMobile()) return;
            if($body.hasClass('sidebar-opened'))
                toggleSidebar();
        });

        $('#sidebar-menu .toggle-submenu').on('click', toggleSubMenu);
        // Hide sidebar for anchor links
        $("#sidebar-menu li > a[href^='#']:not(.toggle-submenu)").on('click', function (e) {
          toggleSidebar();
        });

        if($toggle.affix) {
            $toggle.affix({
                    offset: {
                top: parseInt($toggle.css('top'), 10)
                // bottom: function () {
                    //   return (this.bottom = $('.footer').outerHeight(true))
                    // }
                }
            });
        }

    }

});
