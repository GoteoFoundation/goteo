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

$(function() {
    //goteo value sliders
    $('.slider-footprint-data, .slider-footprint-projects').slick({
        dots: false,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 7000,
        speed: 1500,
        fade: true,
        arrows: true,
        cssEase: 'linear',
        prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
        nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
    },function(e){
        console.log("test");
    });

// add navigation at tabs on Goteo Values
    $(".goteo-values .footprint-tabs").on("click","a",function(e){
        e.preventDefault();
        var footprint =$(this).attr("data-footprint");
        $(".goteo-values div.container > div.row").addClass('hidden');
        $(".goteo-values .footprint-tabs a").removeClass("active");
        $(".goteo-values div.container > div#goteo-values-"+footprint).removeClass('hidden');
        $(".goteo-values .footprint-tabs a[data-footprint='"+footprint+"']").addClass("active");
        //workaround to fix slick initiation on hiden elements
        $('.slider-footprint-data, .slider-footprint-projects').slick('setPosition');
    });
});
