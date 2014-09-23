//obsoleto
$(function () {

    var li= $('li.element#<?php echo $this['id'] ?>');

    var input = li.children('div.contents').find('input');

    if (input.length) {

       var lastVal = input.val();

       var updating = null;

       var update = function () {

           var val = input.val();

           clearTimeout(updating);

           if (val != lastVal) {

               lastVal = val;

               updating = setTimeout(function () {
                   li.superform();
               });

           } else {
                li.removeClass('busy');
           }

       };

       input.keydown(function () {

           if (!updating) {
               li.addClass('busy');
           } else {
               clearTimeout(updating);
           }

           updating = setTimeout(function () {
               update();
           }, 700);
       });

      input.bind('paste', function () {
          update();
      });

       input.focus(function () {

          updating = null;

          input.one('blur', function () {
              updating = update();
          });

       });

    }

});

