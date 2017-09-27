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
    <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.css" />
    <link rel="stylesheet" href="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.css" type="text/css" />
    <link rel="stylesheet" href="<?= SRC_URL ?>/assets/vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" type="text/css" />
    <link href="<?= SRC_URL ?>/assets/css/typeahead.css" rel="stylesheet">
<?php $this->append() ?>

<?php $this->section('footer') ?>

    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/Sortable/Sortable.min.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/js/forms.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/js/dashboard/ajax-utils.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/typeahead.js/dist/typeahead.bundle.min.js"></script>
    <script>
        $(document).ready(function(){

          $('.fade').slick({
          dots: true,
          infinite: true,
          speed: 1500,
          fade: true,
          arrows: true,
          cssEase: 'linear',
            });


          // Projects carrousel
          var $carousel = $('.slider-projects');

          var settings = {
            dots: false,
            arrows: true,
            slide: '.widget-slide',
            slidesToShow: 3,
            slidesToScroll: 0,
            centerMode: true,
            centerPadding: '150px',
            infinite: false,
            responsive: [
              {
                breakpoint: 768,
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

          function setSlideVisibility() {
            //Find the visible slides i.e. where aria-hidden="false"
            var visibleSlides = $carousel.find('.slick-slideshow__slide[aria-hidden="false"]');
            //Make sure all of the visible slides have an opacity of 1
            $(visibleSlides).each(function() {
              $(this).css('opacity', 1);
            });

            //Set the opacity of the first and last partial slides.
            $(visibleSlides).first().prev().css('opacity', 0);
          }

          $carousel.slick(settings);
          $carousel.slick('slickGoTo', 1);
          setSlideVisibility();

          $carousel.on('afterChange', function() {
            setSlideVisibility();
          });

          $('.slider-calls').slick({
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            arrows: true,
            dots: true,
            responsive: [
              {
                breakpoint: 768,
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




          /*$('.slider-projects').slick({
            slidesToShow: 3,
            slidesToScroll: 0,
            dots: false,
            centerMode: true,
            focusOnSelect: true,
            infinite: false,
            responsive: [
              {
                breakpoint: 768,
                settings: {
                  arrows: false,
                  centerMode: true,
                  slidesToShow: 2
                }
              },
              {
                breakpoint: 480,
                settings: {
                  arrows: false,
                  centerMode: true,
                  slidesToShow: 1
                }
              }
            ]
          });*/

          $('a[href="#search"]').on('click', function(event) {
              event.preventDefault();
              $('#search').addClass('open');
              $('#search > form > input[type="search"]').focus();
              $('body').removeClass('sidebar-opened');
          });
          
          $('#search, #search button.close').on('click keyup', function(event) {
              if (event.target == this || event.target.className == 'close' || event.keyCode == 27) {
                  $(this).removeClass('open');
              }
          });

          $(".auto-update-calls").on('click', ".filters li", function (e) {
            if($(this).hasClass('active')){
              $(this).removeClass('active');
            }
            else
            {
              $(this).addClass('active');
            }

            $filters.find('.auto-update-calls li .active').each(function(){
              filters.push($(this).data('status'));
            });
            /*var value = $(this).is(":checked") ? 1 : 0;
            var id = $(this).attr('id');
            var $parent = $(this).closest('.auto-update-projects');
            var $button = $parent.find('.more-projects-button');
            var url = $parent.data('url');
            var limit = $parent.data('limit') || 6;

            $.post(url + '?' + $.param({ limit: limit }), { 'id' : id, 'value' : value  }, function(result) {
                if((result.offset + result.limit) >= result.total) {
                    $button.addClass('hidden');
                } else {
                    $button.removeClass('hidden');
                }
                $parent.contents('.elements-container').html(result.html);
            });*/
          });
          
          
        });
    </script>

<?php $this->append() ?>
