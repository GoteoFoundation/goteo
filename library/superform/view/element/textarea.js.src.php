$(function () {

    var li= $('li.element#<?php echo $this['id'] ?>');

    var input = li.children('div.contents').find('textarea');

    if (input.length) {

       var lastVal = input.val();

       var updating = null;

       var update = function (val) {

           clearTimeout(updating);

           if (val != lastVal) {

               lastVal = val;

               li.addClass('busy');

               updating = setTimeout(function () {
                   li.superform();
               });

           } else {
                li.removeClass('busy');
           }

       };

       input.focus(function () {

           input.keydown(function () {

               var val = input.val();

               clearTimeout(updating);

               updating = setTimeout(function () {
                   update(input.val());
               }, 700);

           });

          input.one('blur', function () {
              update(input.val());
          });

          input.bind('paste', function () {
              updating = setTimeout(function () {
                  update(input.val());
              }, 100);
          });


       });

    }

});

