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

