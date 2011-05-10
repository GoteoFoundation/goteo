<script type="text/javascript">
    
$(function () {
    
   var input = $('#<?php echo $this['id'] ?> > div.contents > input');
   
   input.focus(function () {       
       
       /*
       var keyup = function () {
           
           var cancel = false;

           input.keydown(function () {               
               cancel = true; 
           });
           
           setTimeout(function () {
               if (!cancel) {     
                   Superform.update(input);
               }
           }, 700);
           
      };
      
      input.keyup(keyup);
      */
      
      input.blur(function () {
           
           if (!('__value' in input) || input['__value'] !== this.value) {
               Superform.update(input);
           }
           
           //Superform.update(input);
           
           input.unbind('blur', arguments.callee);
           //input.unbind('keyup', keyup);
           
      });
      
   });
   
});

</script>