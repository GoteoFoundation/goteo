$(function () {
    
    var li = $('#<?php echo $this['id'] ?>');
    
    var select = li.children('div.contents').find('select');
    
    if (select.length) {

       select.unbind('change');

       select.change(function () {
       
           li.addClass('busy');
           
           window.Superform.update(li, null, function () {
               li.removeClass('busy');
           });           

       });
    }   
});


