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

    var toggleSidebar = function(e) {
        var $body = $('body');
        if($body.hasClass('animating')) return;
        // console.log('sidebar toggle', e);
        e.stopPropagation();
        e.preventDefault();
        $('body').toggleClass('animating sidebar-opened').one(transitionEnd, function(e) {
            $(this).removeClass('animating');
            // console.log('end animation', e);
        });
    };


    // Sidebar toggle
    $('.toggle-sidebar').on('click', toggleSidebar);
    // Swipper detector
    if('.toggle-sidebar') {
        $('body').hammer().bind("swiperight", function(e){
            if(!$('body').hasClass('sidebar-opened'))
                toggleSidebar(e);
        });
        $('body').hammer().bind("swipeleft", function(e){
            if($('body').hasClass('sidebar-opened'))
                toggleSidebar(e);
        });
    }
});
