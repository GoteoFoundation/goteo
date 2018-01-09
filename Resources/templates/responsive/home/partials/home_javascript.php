<script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

    $(function(){

      // Animate numbers
      $('.animate-number').animateNumber({
        decimal: '<?= $this->get_currency('dec') ?>',
        thousand: '<?= $this->get_currency('thou') ?>',
        steps: 30
      });

      // Trigger resize on change tab to animate numbers (if needed)
      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $(window).trigger('resize');
      })

      $('.slider-main').slick({
        dots: true,
        infinite: true,
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
        autoplaySpeed: 3500,
        fade: true,
        arrows: false,
        cssEase: 'linear'
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

      initSlickProjects();

      initSlickChannels();

      $(".auto-update-projects").on('click', ".filters li", function (e) {
        e.preventDefault();
        $(".auto-update-projects .filters li").removeClass('active');

        $(this).addClass('active');

        var filter = $(this).data('status');
        var $div = $(this).closest('.section');

        var drawProjects = function(lat, lng) {
          var params = { filter: filter, latitude: lat, longitude: lng };
          // $div.addClass('loading-container');
          $('#projects-container').addClass('loading-container');
          // console.log('drawProjects', params)
          $.post('/home/ajax/projects/filtered', params, function(result) {
             destroySlickProjects();
             $('#projects-container').html(result.html);
             initSlickProjects();
             // $div.removeClass('loading-container');
             $('#projects-container').removeClass('loading-container');
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

    });

    // @license-end
</script>
