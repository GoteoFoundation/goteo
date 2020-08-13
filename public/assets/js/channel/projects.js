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

    var $container = $('#projects-container');
    var $slider = $container.contents('.slider-projects');
    
    function initSlickProjects() {
      $slider.slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
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
  
    initSlickProjects();
  
  });
  