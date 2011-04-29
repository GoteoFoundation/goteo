<?php use Goteo\Core\View ?>

<div class="superform<?php if (isset($this['class'])) echo ' '. htmlspecialchars($this['class']) ?>"<?php if (isset($this['id'])) echo 'id="'. htmlspecialchars($this['id']) . '"' ?>>
    
    <?php if (isset($this['title'])): ?>
    <h<?php echo $this['level'] ?>><?php echo htmlspecialchars($this['title']) ?></h<?php echo $this['level'] ?>>
    <?php endif ?>
    
    <?php if (isset($this['hint'])): ?>
    <div class="hint">                    
        <h4>Guía</h4>
        <blockquote><?php echo $this['hint'] ?></blockquote>
    </div>
    <?php endif ?>
    
    <?php echo new View('library/superform/view/elements.html.php', $this['elements']) ?>
    
    <?php if(!empty($this['footer'])): ?>
    <div class="footer">
        <div class="elements">
            <?php foreach ($this['footer'] as $element): ?>
            <div class="element">
                <?php echo $element->getInnerHTML() ?>
            </div>
            <?php endforeach ?>
        </div>
    </div>
    <?php endif ?>
    
    <script type="text/javascript">

    jQuery(document).ready(function($) {
        
        var frm = $('#<?php echo $this['id'] ?>');
        var cFb = null;
        var speed = 200;
        
        var handler = function (event) {
            
           var id = $(this).attr('id');
           
           if (cFb !== null && cFb !== id) {
               
               setTimeout(function () {                      
                    frm.find('div.feedback#superform-feedback-for-' + cFb).fadeOut(speed);
               });
               
           }
                   
           setTimeout(function () {               
               frm.find('div.feedback#superform-feedback-for-' + id).fadeIn(speed);
               cFb = id;
           });
               
           event.stopPropagation();

        };
        
        frm.find('li.element').bind('click', handler);
                
        frm.find(':input').bind('focus', function (event) {
           
           console.log(this);
           
           var p = $(this).parents('li.element');

           if (p.length >= 1) {
                handler.apply(p[0], [event]);
           }
           
           return false;          
           
        });

    });                
    </script>
    
</div>