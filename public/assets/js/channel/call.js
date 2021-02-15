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
  // $('.tabs').click( function() {
  //   $('.tabs').removeClass('slick-current');
  //   $(this).addClass('slick-current');
  // });

  $("div.languages div.active").click(function(){
    $("ul.languages-list").slideToggle();
  });
  

  var $slider_program = $('.slider-programs');
  var $slider_post = $('.slider-post');
  var $slider_workshop = $('.slider-workshops');
  var $slider_stories = $('.slider-stories');

  function initSlickSliders() {
    var initial_slide = $slider_program.data('initial-slide');
    $slider_program.slick({
      slidesToShow: 1,
      initialSlide: parseInt(initial_slide),
      slidesToScroll: 1,
      infinite: false,
      arrows: true,
      dots: false,
      variableWidth: true,
      centerMode: false,
      focusOnSelect: true,
      prevArrow: '<div class="custom-left-arrow"><span class="icon icon-2x icon-arrow"></span><span class="sr-only">Prev</span></div>',
      nextArrow: '<div class="custom-right-arrow"><span class="icon icon-2x icon-arrow"></span><span class="sr-only">Prev</span></div>',
      responsive: [
        {
          breakpoint: 992,
          settings: {
            prevArrow: '<div class="custom-left-arrow"><span class="icon icon-4x icon-arrow"></span><span class="sr-only">Prev</span></div>',
            nextArrow: '<div class="custom-right-arrow"><span class="icon icon-4x icon-arrow"></span><span class="sr-only">Prev</span></div>',
          }
        }
      ]
    });

    $slider_post.slick({
      slidesToShow: 3.5,
      infinite: false,
      slidesToScroll: 1,
      arrows: true,
      dots: false,
      focusOnSelect: true,
      prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
      nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
      responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2.5,
            arrows: false
          }
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1.2,
            arrows: false
          }
        }]

    });
    
      var workshop_initial_slide = $slider_workshop.data('initial-slide');
      $slider_workshop.slick({
      slidesToShow: 3.5,
      initialSlide: parseInt(workshop_initial_slide),
      infinite: false,
      slidesToScroll: 1,
      arrows: true,
      dots: false,
      focusOnSelect: true,
      prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
      nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
      responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2.5,
            arrows: false
          }
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1.2,
            arrows: false
          }
        }]

    });

    var $container = $('#projects-container');
    var $slider_projects = $container.contents('.slider-projects');
    
    $slider_projects.slick({
      infinite: false,
      slidesToShow: 3.5,
      slidesToScroll: 1,
      arrows: true,
      dots: false,
      prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
      nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
      responsive: [
        {
          breakpoint: 769,
          settings: {
            slidesToShow: 2.5,
            arrows:false
          }
        },
        {
          breakpoint: 500,
          settings: {
            slidesToShow: 1.2,
            arrows: false
          }
        }]
    });

    $slider_stories.slick({
      slidesToShow: 1,
      infinite: false,
      slidesToScroll: 1,
      arrows: true,
      dots: false,
      focusOnSelect: true,
      prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
      nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
      responsive: [
        {
          breakpoint: 769,
          settings: {
            slidesToShow: 1,
            arrows:true
          }
        },
        {
          breakpoint: 500,
          settings: {
            slidesToShow: 1,
            arrows: true
          }
        }]

    });

    

  }


  initSlickSliders();

   $(".info.intro").on('click', ".filters li", function (e) {

        e.preventDefault();

        $(".info.intro .filters li").removeClass('selected');

        $(this).addClass('selected');

        var rol = $(this).data('rol');

        //$('#rol-container').animateCss('fadeOut');

        $('#rol-container .row').hide()
        $('.row#'+rol).show();


        /*var url = '/home/ajax/calls/filtered';
        $('#calls-container').addClass('loading-container');

        // $('#calls-container').animateCss('fadeOut');
        $.post(url, { filters: filters }, function(result) {
            $('#calls-container').html(result.html);
            initSlickCalls();
            $('#calls-container').removeClass('loading-container');
            //$('#calls-container').removeClass('fadeOut').animateCss('fadeIn');
        });*/

      });

});
