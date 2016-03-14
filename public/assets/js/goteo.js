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

var transitionDeferred;

function getTransitionOutDeferred() {
    // Reject active deferred
    if (transitionDeferred) {
        transitionDeferred.reject();
    }

    // Create new timing deferred
    transitionDeferred = $.Deferred();

    // Animate content out
    $("#main-content").animate({ opacity: 0 }, 500, function() {
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
    $("#main-content").animate({ opacity: 1 }, 500);
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

    $.pronto({
        selector: "a.pronto",
        transitionOut: getTransitionOutDeferred,
        target: { title: 'title', content: '#main-content' }
    });

    pageRendered();

});
