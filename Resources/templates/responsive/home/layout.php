<?php

$this->layout('layout', [
    'bodyClass' => 'home'
    ]);

$this->section('content');

// We include alert messages in this layout, so it will be processed before the
// main layout. Therefore the main layout won't repeat them
?>

<div class="home">

    <?= $this->supply('home-content') ?>

</div>

<?php $this->replace() ?>


<?php $this->section('head') ?>
    <?= $this->insert('home/partials/styles') ?>
<?php $this->append() ?>

<?php $this->section('footer') ?>

    <?= $this->insert('home/partials/javascript') ?>

    <script type="text/javascript">
        $(document).ready(function(){

          // Animate numbers
          $('.animate-number').animateNumber({
            decimal: '<?= $this->get_currency('dec') ?>',
            thousand: '<?= $this->get_currency('thou') ?>',
            steps: 30
          });

          $('.slider-fade').slick({
            dots: true,
            infinite: true,
            speed: 1500,
            fade: true,
            arrows: true,
            cssEase: 'linear',
            prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
            nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
          });


          function setSlideVisibility() {
            //Find the visible slides i.e. where aria-hidden="false"
            var visibleSlides = $('.slider-projects').find('.slick-slideshow__slide[aria-hidden="false"]');
            //Make sure all of the visible slides have an opacity of 1
            $(visibleSlides).each(function() {
              $(this).css('opacity', 1);
            });

            //Set the opacity of the first and last partial slides.
            $(visibleSlides).first().prev().css('opacity', 0);
          }

          function initSlickProjects(){
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

              $('.slider-projects').slick(settings);
              $('.slider-projects').slick('slickGoTo', 1);
              setSlideVisibility();
          }

          function destroySlickProjects() {
            if ($('.slider-projects').hasClass('slick-initialized')) {
              $('.slider-projects').slick('destroy');
            }
          }

          $('.slider-projects').on('afterChange', function() {
            setSlideVisibility();
          });

          function initSlickCalls() {
            $('.slider-calls').slick({
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


          function destroySlickCalls() {
            if ($('.slider-calls').hasClass('slick-initialized')) {
              $('.slider-calls').slick('destroy');
            }
          }

          initSlickCalls();

          initSlickProjects();

          initSlickChannels();

          $('a[href="#search"]').on('click', function(event) {
              event.preventDefault();
              $('#search').addClass('open');
              $('#search > form > input[type="search"]').focus();
          });

          $('#search, #search button.close').on('click keyup', function(event) {
              if (event.target == this || event.target.className == 'close' || event.keyCode == 27) {
                  $(this).removeClass('open');
              }
          });

          $(".auto-update-calls").on('click', ".filters li", function (e) {

              $(this).toggleClass('active');

            var $filters = $('.auto-update-calls .filters');
            var filters = [];

            $filters.find('li.active').each(function(){
              filters.push($(this).data('status'));
            });

            var url = '/home/ajax/calls/filtered';

          $('#calls-container').animateCss('fadeOut');
            $.post(url, { filters: filters }, function(result) {
                destroySlickCalls();
                $('#calls-container').html(result.html);
                initSlickCalls();
                //$('#calls-container').removeClass('fadeOut').animateCss('fadeIn');
            });

          });

          $(".auto-update-projects").on('click', ".filters li", function (e) {
            e.preventDefault();
            $(".auto-update-projects .filters li").removeClass('active');

            $(this).addClass('active');

            var filter = $(this).data('status');

            var drawProjects = function(lat, lng) {
              var params = { filter: filter, latitude: lat, longitude: lng };
              // console.log('drawProjects', params)
              $.post('/home/ajax/projects/filtered', params, function(result) {
                 destroySlickProjects();
                 $('#projects-container').html(result.html);
                 initSlickProjects();
                 //$('#projects-container').removeClass('fadeOut').animateCss('fadeIn');
             });
            };

            if(filter === 'near') {
              var fallbackUserLocation = function() {
                // Fallback to existing user location or ip based
                locator.getUserLocation(function(position, error) {
                  locator.trace('fallback user location', position, error);
                  if(position) {
                    drawProjects(position.latitude, position.longitude);
                  } else {
                    console.error('Location not found', error);
                  }
                });
              };

              // Try browser first
              if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                  function(position) {
                    locator.trace('browser info:', position.coords);
                    drawProjects(position.coords.latitude, position.coords.longitude);
                  },
                  function(error) {
                    locator.trace('browser locator error', error);
                    fallbackUserLocation();

                  });
              } else {
                  fallbackUserLocation();
              }

            } else {
                drawProjects();
            }

          });


          $(".sidebar-nav").on('click', "a", function (e) {
            if(/^#/.test(href) === true) {
                $('body').removeClass('sidebar-opened');
            } else {
                alert("no anchor")
            }
          });


        });
    </script>

<?php $this->append() ?>
