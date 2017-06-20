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

//Main goteo object
var goteo = { debug : false };

/**
 * Console debug function on non LIVE sites
 * @param {string} msg description
 */
goteo.trace = function () {
    try {
        if(goteo.debug) {
            console.log([].slice.apply(arguments));
        }
    }catch(e){}
};
goteo.error = function () {
    try {
        if(goteo.debug) {
            console.error([].slice.apply(arguments));
        }
    }catch(e){}
};

var prontoTarget = '#main-content';
var transitionDeferred;

function getTransitionOutDeferred() {
    // Reject active deferred
    if (transitionDeferred) {
        transitionDeferred.reject();
    }

    // Create new timing deferred
    transitionDeferred = $.Deferred();

    // Animate content out
    $(prontoTarget).animate({ opacity: 0 }, 500, function() {
        // Resolve active deferred
        transitionDeferred.resolve();
    });

    // Return active deferred
    return transitionDeferred;
}

function pageRequested(e) {
    // Update state to reflect loading
    goteo.trace("Request new page");
}

function pageLoadProgress(e, percent) {
    // Update progress to reflect loading
    goteo.trace("New page load progress", percent);
}

function pageLoaded(e) {
    // Unbind old events and remove plugins
    goteo.trace("Destroy old page");
}

function pageRendered(e) {
    // Bind new events and initialize plugins
    goteo.trace("Render new page");

    // Animate content in
    $(prontoTarget).animate({ opacity: 1 }, 500);
}

function pageLoadError(e, error) {
    // Watch for load errors
    goteo.error("Error loading page", error);
}

// Function from David Walsh: http://davidwalsh.name/css-animation-callback
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

var animationEnd = whichTransitionEvent();

$.fn.extend({
    animateCss: function (animationName, callback) {
        this.off();
        this.addClass('animated ' + animationName).on(animationEnd, function() {
            console.log(this, $(this).attr('class'));
            $(this).removeClass('animated ' + animationName);
            if($.isFunction(callback)) {
                callback.call(this);
            }
            $(this).off();
        });
    }
});

/**
 * Document ready
 */
$(function(){
    // Menu behaviour
    var toggleMenu = function(e) {
        e.stopPropagation();
        e.preventDefault();
        var that = this;
        var target = $(this).data('target');
        var $t = $('#' + target);
        var $show = $(this).find('.show-menu');
        var $hide = $(this).find('.hide-menu');
        // xs devices has css position to fixed
        var isDesktop = $t.css('position') === 'absolute';
        if(!isDesktop && $hide.length) {
            $hide.css('display', 'none');
            $show.css('display', 'block');
        }
        var inAnimation = 'flipInY';
        var outAnimation = 'flipOutY';
        // Close other opened
        $('.top-menu.active:not([id="' + target + '"])').removeClass('active');
        $('#main-content').off();

        if($t.hasClass('active')) {
            if(isDesktop) {
                $show.css('display', 'none');
                $hide.css('display', 'block').animateCss('flipOutX', function() {
                    $hide.css('display', 'none');
                    $show.css('display', 'block').animateCss('flipInX');
                });
            }
            $t.animateCss(outAnimation, function(){
                $t.removeClass('active');
                $t.find('.submenu.active').removeClass('active');
            });
        } else {
            if(isDesktop) {
                $hide.css('display', 'none');
                $show.css('display', 'block').animateCss('flipOutX', function() {
                    $show.css('display', 'none');
                    $hide.css('display', 'block').animateCss('flipInX');
                });
            }
            $t.addClass('active').animateCss(inAnimation, function(){
                $('#main-content').on('click', function(e){
                    toggleMenu.call(that, e);
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
            inAnimation = 'flipInY';
            outAnimation = 'flipOutY';
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

    var toggleSidebar = function(e) {
        console.log('sidebar toggle', e);
        e.stopPropagation();
        e.preventDefault();
        $('body').toggleClass('sidebar-opened').one(animationEnd, function(e) {
            console.log('end animation', e);
        });
    };

    $('.toggle-menu').on('click', toggleMenu);
    $('.top-menu .toggle-submenu').on('click', toggleSubMenu);
    // Sidebar toggle
    $('.toggle-sidebar').on('click', toggleSidebar);
    // Swipper detector
    if('.toggle-sidebar') {
        $('body').hammer({}).bind("swiperight", toggleSidebar);
    }



    // Bind pronto events
    $(window).on("pronto.request", pageRequested)
             .on("pronto.progress", pageLoadProgress)
             .on("pronto.load", pageLoaded)
             .on("pronto.render", pageRendered)
             .on("pronto.error", pageLoadError);

    $('#main-content').on('click', 'a.pronto', function(e){
        if($(this).data('pronto-target')) {
            prontoTarget = $(this).data('pronto-target');
            $.pronto('defaults',{
                target: { title: 'title', content: prontoTarget }
            });
        } else {
            if(prontoTarget !== '#main-content') {
                prontoTarget = '#main-content';
                $.pronto('defaults',{
                    target: { title: 'title', content: prontoTarget }
                });
            }
        }
    });
    $.pronto({
        selector: "a.pronto",
        transitionOut: getTransitionOutDeferred,
        jump: false,
        target: { title: 'title', content: prontoTarget }
    });

    pageRendered();

});

