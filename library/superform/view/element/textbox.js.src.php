$(function () {

    var li= $('#<?php echo $this['id'] ?>');

    var input = li.children('div.contents').find('input');

    if (input.length) {

       var lastVal = input.val();      
       
       var updating = null;

       var update = function () {
       
           var val = input.val();
       
           clearTimeout(updating);

           if (val != lastVal) {    
           
               lastVal = val;
               
               li.addClass('busy');
                                             
               updating = setTimeout(function () {               
                   window.Superform.update(input, function () {
                       li.removeClass('busy');
                   });
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

