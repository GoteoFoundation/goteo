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
  var total = $slider.data('total');
  var limit_add = $slider.data('limit-add') || $slider.data('limit');
  var params = {
    filter: $(".auto-update-projects .project-filters li.active").data('status'),
    latitude: 0,
    longitude: 0,
    pag: 0,
    limit: $slider.data('limit')
  };
  // console.log('projects slides, params: ', params);

  function setSlideVisibility() {
    //Find the visible slides i.e. where aria-hidden="false"
    var $visibleSlides = $slider.find('.widget-slide[aria-hidden="false"]');
    //Make sure all of the visible slides have an opacity of 1
    $visibleSlides.css('opacity', 1);
    // hide the first (which is the last in a infinite loop) partial slides to avoid a the rendering of the last
    // slide in the inifinite loop
    $visibleSlides.first().prev().css('opacity', 0);
    // in case there's a partial on the left this should be uncommented
    // $visibleSlides.last().next().css('opacity', 0);
  }

  function destroySlickProjects() {
    if ($slider.hasClass('slick-initialized')) {
      $slider.slick('destroy');
    }
  }


  function initSlickProjects() {
    // console.log('projects slides, total: ', total, 'limit', params.limit);

    var settings = {
      dots: false,
      arrows: true,
      slide: '.widget-slide',
      slidesToShow: 3,
      slidesToScroll: 0,
      centerMode: true,
      centerPadding: '150px',
      infinite: true,
      prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
      nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
      responsive: [
        {
          breakpoint: 769,
          settings: {
            arrows: false,
            centerMode: true,
            slidesToShow: 2,
            centerPadding: '100px',
          }
        },
        {
          breakpoint: 500,
          settings: {
            arrows: false,
            centerMode: true,
            slidesToShow: 1,
            centerPadding: '75px',

          }
        }
      ]
    };

    $slider.slick(settings);

    // Load more slides if end of the carrousle reached
    $slider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {

      //Re-apply the visibility of the slides
      $slider.find('.widget-slide').css('opacity', 1);

      if($container.hasClass('loading-container')) return;
      var size = $slider.find('.widget-slide:not(.slick-cloned)').length;
      var visible = $slider.find('.widget-slide[aria-hidden="false"]').length;
      // console.log('slide about to load, currentSlide', currentSlide, 'nextSlide', nextSlide,'visible',visible);
      if(currentSlide + visible + 1 === size) {
        // console.log('right edge reached, currentSlide', currentSlide,'size', size, 'visible slides', visible, 'page', pag);
        if( currentSlide < total && size < total) {
          params.pag++;
          // We change some defaults here
          var query = jQuery.extend({}, params);
          query.limit = limit_add; // smaller limit than the initial slide load
          query.strict = true; // do not give default projects if current filter returns empty
          $container.addClass('loading-container');
          $.getJSON('/discover/ajax', query, function(result) {
            total = result.total;
            var items = '';
            result.items.forEach(function(html, index) {
              // $slider.slick('slickAdd', '<div class="item widget-slide">' + html + '</div>');
              items += '<div class="item widget-slide">' + html + '</div>';
            });
            // Add all items at once, seems to be more efficient for slick
            $slider.slick('slickAdd', items);
            $container.removeClass('loading-container');
            // $slider.slick('slickGoTo', currentSlide + 1);
            // console.log('new loaded with params', params, 'result', result);
          });
        }
      }
      // if(currentSlide < nextSlide && currentSlide === 0) {
      //   console.log('left edge reached');
      // }
    });

    $slider.slick('slickGoTo', 1);

    setSlideVisibility();
    //After the slide change has completed, call the setSlideVisibility to hide the partial slides.
    $slider.on('afterChange', function(event, slick, currentSlide) {
      setSlideVisibility();
    });
  }


  function drawProjects(filter, lat, lng) {

    $container.addClass('loading-container');

    // rebuild the status global var
    params.filter = filter;
    params.latitude = lat;
    params.longitude = lng;
    // reset page
    params.pag = 0;

    // console.log('drawProjects', params)
    $.getJSON('/discover/ajax', params, function(result) {
      // console.log(result);
      destroySlickProjects();
      total = result.total;
      $slider.contents('.widget-slide').remove();
      result.items.forEach(function(html, index) {
        $slider.append('<div class="item widget-slide">' + html + '</div>');
      });
      $container.removeClass('loading-container');
      initSlickProjects();
   });
  }

  // Ajax reload when nav clicked
  $(".auto-update-projects").on('click', ".project-filters li", function (e) {
    e.preventDefault();

    $(".auto-update-projects .project-filters li").removeClass('active');
    $(this).addClass('active');

    var filter = $(this).data('status');
    var $div = $(this).closest('.section');

    if(filter === 'near') {
      var fallbackUserLocation = function() {
        // Fallback to existing user location or ip based
        locator.getUserLocation(function(position, error) {
          locator.trace('fallback user location', position, error);
          if(position) {
            drawProjects(filter,position.latitude, position.longitude);
          } else {
            // console.error('Location not found', error);
          }
        });
      };

      // Try browser first
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(position) {
            locator.trace('browser info:', position.coords);
            drawProjects(filter, position.coords.latitude, position.coords.longitude);
          },
          function(error) {
            locator.trace('browser locator error', error);
            fallbackUserLocation();

          });
      } else {
          fallbackUserLocation();
      }

    } else {
        drawProjects(filter);
    }

  });

  initSlickProjects();

});
