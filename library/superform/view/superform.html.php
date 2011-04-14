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
        frm.__chint= null;
        
        var els = frm.children('div.elements');
        
        var speed = 200;
        
        frm.find('li.element').each(function (i, li) {

            li = $(li);
                        
            var id = li.attr('id');

            var handler = function (event) {
                                

                if (frm.__chint !== id) {                    
                    
                    if (frm.__chint !== null) {
                        setTimeout(function() {
                            frm.find('div.feedback#superform-feedback-for-' + frm.__chint).fadeOut(speed);
                            frm.__chint = null;
                        }, 0);
                    }
                    
                    setTimeout(function() {
                            frm.find('div.feedback#superform-feedback-for-' + id).fadeIn(speed);
                            frm.__chint = id;
                    }, 0);
                    
                    
                }
                
                event.stopPropagation();
                
            };

            li.bind('click', handler);

            li.find(':input').each(function (j, el) {                
                
                el = $(el);
                
                var p = el.parents('li.element');

                if (p.length >= 1 && ($(p[0]).attr('id') === id)) {
                    el.bind('focus', handler);
                }
                
            });

        });

    });                
    </script>
    
</div>