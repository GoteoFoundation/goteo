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
var prontoScroll = '#main-content';
var transitionDeferred;

function getTransitionOutDeferred() {
    // Reject active deferred
    if (transitionDeferred) {
        goteo.trace('rejecting current transition');
        transitionDeferred.reject();
    }

    // Create new timing deferred
    transitionDeferred = $.Deferred();

    // Animate content out
    $(prontoTarget).addClass('pronto-loading');
    if($(prontoTarget).contents('*').length) {
        $(prontoTarget).contents('*').animate({ opacity: 0 }, 200, function() {
            // Resolve active deferred
            transitionDeferred.resolve();
        });
    } else {
        transitionDeferred.resolve();
    }

    // Return active deferred
    return transitionDeferred;
}

function pageRequested(e) {
    // Update state to reflect loading
    goteo.trace("Request new page", e, 'prontoTarget:', prontoTarget);
}

function pageLoadProgress(e, percent) {
    // Update progress to reflect loading
    goteo.trace("New page load progress", percent, e, 'prontoTarget:', prontoTarget);
}

function pageLoaded(e) {
    // Unbind old events and remove plugins
    goteo.trace("Destroy old page", e, 'prontoTarget:', prontoTarget);
}

function prontoLoad(href, target) {
    target = target || '#main-content';
    prontoTarget = target;
    prontoScroll = target;

    $.pronto('defaults', {
        target: { title: 'title', content: prontoTarget }
    });
    $.pronto('load', href);
}

function pageRendered(e) {
    if(e === undefined) return;
    // Bind new events and initialize plugins
    goteo.trace("Render new page", e, 'prontoTarget:', prontoTarget);

    if($(prontoTarget).contents('*').length) {
        // Animate content in
        $(prontoTarget).contents('*').animate({opacity: 1}, 200, function() {
            $(prontoTarget).removeClass('pronto-loading');
        });
    } else {
        $(prontoTarget).removeClass('pronto-loading');
    }

    if($('html, body').scrollTop() > $(prontoScroll).offset().top) {
        $('html, body').animate({scrollTop: $(prontoScroll).offset().top}, 800);
    }
}

function pageLoadError(e, error) {
    // Watch for load errors
    goteo.error("Error loading page", error);
    $(prontoTarget).html('<div class="alert alert-danger" style="margin: 1em;">' + goteo.texts['ajax-load-error'].replace('%ERROR%', error) + '</div>');
    $(prontoTarget).removeClass('pronto-loading');
}


/**
 * Document ready
 */
$(function(){

    // Bind pronto events
    $(window).on("pronto.request", pageRequested)
             .on("pronto.progress", pageLoadProgress)
             .on("pronto.load", pageLoaded)
             .on("pronto.render", pageRendered)
             .on("pronto.error", pageLoadError);

    $('#main').on('click', 'a.pronto', function(e){
        if($(this).data('pronto-target')) {
            prontoTarget = $(this).data('pronto-target');
        } else if(prontoTarget !== '#main-content') {
            prontoTarget = '#main-content';
        }
        if($(this).data('pronto-scroll-to')) {
            prontoScroll = $(this).data('pronto-scroll-to');
        } else if(prontoScroll !== prontoTarget) {
            prontoScroll = prontoTarget;
        }
        goteo.trace('pronto click',$(this).data('pronto-target'), prontoTarget, $(this).data('pronto-scroll-to'), prontoScroll);
        $.pronto('defaults', {
            target: { title: 'title', content: prontoTarget }
        });
    });
    $.pronto({
        selector: "a.pronto",
        transitionOut: getTransitionOutDeferred,
        jump: false,
        target: { title: 'title', content: prontoTarget }
    });

    pageRendered();

    // Responsive tables initialization
    if($('table.footable').length) $('table.footable').footable();

});

