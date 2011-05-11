$(function () {
    
    var li = $('#<?php echo $this['id'] ?>').closest('li.element');
    
    var checkboxes = li.children('div.contents').find('input[type="checkbox"]');
    
    if (checkboxes.length) {
    
       // Thanks, jQuery
       li[0].__updating = null;

       checkboxes.unbind('change');

       checkboxes.change(function () {
       
           li.addClass('busy');                             
           
           clearTimeout(li[0].__updating);   

           li[0].__updating = setTimeout(function () {
               window.Superform.update(li, function () {
                   li.removeClass('busy');
               });
           }, 1000);
           
       });

    }
   
});


