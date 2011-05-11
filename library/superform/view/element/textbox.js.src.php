$(function () {

    var li= $('#<?php echo $this['id'] ?>');

    var input = li.children('div.contents').find('input');

    if (input.length) {

       var lastVal = input.val();
       
       var updating = null;

       var update = function (val) {              
       
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
       
       input.one('focus', function () {
                  
           input.keypress(function () {
           
               li.addClass('busy');
           
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

