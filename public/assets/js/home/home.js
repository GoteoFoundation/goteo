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

$(function(){

  // Animate numbers
  $('.animate-number').animateNumber({
    decimal: goteo && goteo.decimal || '.',
    thousand: goteo && goteo.thousands || ',',
    steps: 30
  });

  // Trigger resize on change tab to animate numbers (if needed)
  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    $(window).trigger('resize');
  });

  $('.slider-main').slick({
    dots: true,
    infinite: true,
    autoplay: false,
    autoplaySpeed: 7000,
    speed: 1500,
    fade: true,
    arrows: true,
    cssEase: 'linear',
    prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
    nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
  });

  $('.slider-stories').slick({
    dots: true,
    infinite: true,
    speed: 1000,
    fade: true,
    arrows: true,
    cssEase: 'linear',
    prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
    nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
  });

  $('.slider-team').slick({
    dots: false,
    autoplay: true,
    infinite: true,
    speed: 2000,
    autoplaySpeed: 3000,
    fade: true,
    arrows: false,
    cssEase: 'linear'
  });

  function initSlickChannels() {
    $('.slider-channels').slick({
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1,
      arrows: true,
      dots: true,
      prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
      nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
      responsive: [
        {
          breakpoint: 769,
          settings: {
            slidesToShow: 2,
            arrows:false
          }
        },
        {
          breakpoint: 500,
          settings: {
            slidesToShow: 1,
          }
        }]
    });
  }

  initSlickChannels();

});
