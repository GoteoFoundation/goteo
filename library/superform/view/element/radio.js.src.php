$(function () {
    
    var li = $('#<?php echo $this['id'] ?>').closest('li.element');
    
    var radios = li.children('div.contents').find('input[type="radio"]');        
    
    if (radios.length) {

       radios.unbind('change');

       radios.change(function () {
       
           li.addClass('busy');
           
           window.Superform.update(li, null, function () {
               li.removeClass('busy');
           });           

       });
    }   
});


