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
    var $sidebar = $('#sidebar-menu');
    var $main = $('#main');
    var $footer = $('#footer');

    var isMobile = function() {
        // xs devices has css position to absolute or fixed
        return $sidebar.css('position') === 'absolute' || $sidebar.css('position') === 'fixed';
    };

    var dynamicFooterSidebar = function() {

        // Footer position to the bottom
        var docHeight = $window.height();
        var footerHeight = $footer.outerHeight(true);
        var footerTop = $footer.position() && $footer.position().top;

        // Sidebar margin bottom adjust
        var mainTop = $main.position() && $main.position().top;
        var mainPaddingBottom = parseInt($main.css('padding-bottom'), 10);
        var mainBottom = mainTop + $main.outerHeight(true);
        var gap = Math.max(0, docHeight - mainBottom - footerHeight);
        // increment main div
        $main.css('padding-bottom', (mainPaddingBottom + gap) + 'px');
        if ($sidebar.length > 0 && $sidebar.width() > 0) {
            var originalHeight = $sidebar.data('height') || $sidebar.outerHeight();
            $sidebar.data('height', originalHeight);
            // Sidebar, increment both
            var sidebarTop = $sidebar.position() && $sidebar.position().top;
            var newHeight = docHeight - sidebarTop - footerHeight;
            if(newHeight > originalHeight) {
                $sidebar.outerHeight(newHeight);
            }

            if(isMobile()) {
                $('.page-wrap').outerHeight($sidebar.outerHeight());
            }
        }
    };

    if ($sidebar.length > 0) {
        var lastScrollTop = $window.scrollTop();
        var wasScrollingDown = true;
        var initialSidebarTop = $sidebar.position().top;
        var scrollSidebar = function(event) {

            if(isMobile()) {
                var windowHeight = $window.height();
                var sidebarHeight = $sidebar.outerHeight();

                var scrollTop = $window.scrollTop();
                var scrollBottom = scrollTop + windowHeight;

                var sidebarTop = $sidebar.position().top;
                var sidebarBottom = sidebarTop + sidebarHeight;

                var heightDelta = Math.abs(windowHeight - sidebarHeight);
                var scrollDelta = lastScrollTop - scrollTop;

                var isScrollingDown = (scrollTop > lastScrollTop);
                var isWindowLarger = (windowHeight > sidebarHeight);

                if ((isWindowLarger && scrollTop > initialSidebarTop) || (!isWindowLarger && scrollTop > initialSidebarTop + heightDelta)) {
                    $sidebar.addClass('fixed');
                } else if (!isScrollingDown && scrollTop <= initialSidebarTop) {
                    $sidebar.removeClass('fixed');
                }

                var dragBottomDown = (sidebarBottom <= scrollBottom && isScrollingDown);
                var dragTopUp = (sidebarTop >= scrollTop && !isScrollingDown);

                if (dragBottomDown) {
                    if (isWindowLarger) {
                        $sidebar.css('top', 0);
                    } else {
                        $sidebar.css('top', -heightDelta);
                    }
                } else if (dragTopUp) {
                    $sidebar.css('top', 0);
                } else if ($sidebar.hasClass('fixed')) {
                    var currentTop = parseInt($sidebar.css('top'), 10);

                    var minTop = -heightDelta;
                    var scrolledTop = currentTop + scrollDelta;

                    var isPageAtBottom = (scrollTop + windowHeight >= $(document).height());
                    var newTop = (isPageAtBottom) ? minTop : scrolledTop;

                    $sidebar.css('top', newTop);
                }

                lastScrollTop = scrollTop;
                wasScrollingDown = isScrollingDown;
            }
        };

        // $window.scroll(scrollSidebar);
    }

    var toggleSidebar = function(e) {
        var $body = $('body.has-sidebar');
        if($body.hasClass('animating')) return;
        // console.log('sidebar toggle', e);
        e.stopPropagation();
        e.preventDefault();
        $body.toggleClass('animating sidebar-opened').one(transitionEnd, function(e) {
            $(this).removeClass('animating');
            // console.log('end animation', e);
            // dynamicFooterSidebar();
            // scrollSidebar();
        });
    };

    // Sidebar toggle
    $('.toggle-sidebar').on('click', toggleSidebar);
    // Swipper detector
    if('.toggle-sidebar') {
        // Enable text selection
        delete Hammer.defaults.cssProps.userSelect;
        var $body = $('body.has-sidebar');
        $body.hammer().bind("swiperight", function(e){
            if(!$body.hasClass('sidebar-opened'))
                toggleSidebar(e);
        });
        $body.hammer().bind("swipeleft", function(e){
            if($body.hasClass('sidebar-opened'))
                toggleSidebar(e);
        });
    }

    // dynamicFooterSidebar();
    // $(window).on('resize', dynamicFooterSidebar);

});
