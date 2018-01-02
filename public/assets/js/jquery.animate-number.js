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

(function($){

    var formatNumber = function(n, settings){
        var s = n < 0 ? "-" : "",
        c = isNaN(c = Math.abs(settings.rest)) ? 2 : settings.rest,
        d = settings.decimal === undefined ? "." : settings.decimal,
        t = settings.thousand === undefined ? "," : settings.thousand,
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;
       return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };

    var toNumber = function (c, settings) {
        d = settings.decimal === undefined ? "." : settings.decimal;
        t = settings.thousand === undefined ? "," : settings.thousand;
        // remove thousand char and convert decimal to point
        var r1 = new RegExp('\\' + t, 'g');
        var r2 = new RegExp('\\' + d, 'g');
        return +c.replace(r1, '').replace(r2, '.');
    };

    var writeNumber = function($el, number, settings, n) {
        n = n || 0;
        var prefix = (settings && settings.prefix) || '';
        var suffix = (settings && settings.suffix) || '';
        $el.html(prefix + formatNumber(n, settings) + suffix);
        if(n < number) {
            setTimeout(function() {
                writeNumber($el, number, settings, n + (number / settings.steps));
            }, settings.velocity);
        } else {
            $el.html(prefix + formatNumber(number, settings) + suffix);
        }

    };

    var isInViewport = function($el) {
        var elementTop = $el.offset().top;
        var elementBottom = elementTop + $el.outerHeight();

        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();

        return elementBottom > viewportTop && elementTop < viewportBottom;
    };

    $.fn.extend({

        animateNumber: function (options) {
            this.each(function() {
                // This is the easiest way to have default options.
                var settings = $.extend({
                    // These are the defaults.
                    decimal: ".",
                    thousand: ",",
                    rest: 'auto', // number of decimals
                    steps: 50,
                    velocity: 100, // ms
                    suffix: null,
                    prefix: null,
                    start: 'onViewport' // onLoad, onViewport
                }, options );

                var $el = $(this);
                var text = $el.html();

                if(settings.prefix === null) {
                    settings.prefix = text.match(/^[^0-9\.\,]*/);
                    settings.prefix = settings.prefix ? settings.prefix.join('') : '';
                }
                var number = text.match(/[0-9\.\,]+/);
                number = number ? number.join('') : '';

                if(settings.rest === 'auto') {
                    var p = ('' + number).split(settings.decimal);
                    settings.rest = p.length > 1 ? p[1].length : 0;
                }
                if(settings.suffix === null) {
                    settings.suffix = text.match(/[^0-9\.\,]*$/);
                    settings.suffix = settings.suffix ? settings.suffix.join('') : '';
                }

                number = toNumber(number, settings);
                // console.log('animate number', settings, this, text, number);
                var started = false;
                if(settings.start === 'onViewport') {
                    // Wait until element is visible
                    var writeOnViewport = function() {
                        if(!started && isInViewport($el)) {
                            started = true;
                            writeNumber($el, number, settings);
                            $(window).off('resize scroll', writeOnViewport);
                        }
                    };
                    $(window).on('resize scroll', writeOnViewport);
                    writeOnViewport();
                } else {
                    started = true;
                    writeNumber($el, number, settings);
                }
            });
            return this;
        }
    });

})(jQuery);

