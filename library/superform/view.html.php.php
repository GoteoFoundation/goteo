<div class="superform<?php if (isset($this['class'])) echo ' '. htmlspecialchars($this['class']) ?>"<?php if (isset($this['id'])) echo 'id="'. htmlspecialchars($this['id']) . '"' ?>>
    
    <?php if (isset($this['title'])): ?>
    <h3><?php echo htmlspecialchars($this['title']) ?></h3>
    <?php endif ?>
    
    <?php if (isset($this['hint'])): ?>
    <div class="guide">                    
        <h4>Guía</h4>
        <blockquote><?php echo $this['hint'] ?></blockquote>
    </div>
    <?php endif ?>
    
    <?php if (!empty($this['elements'])): ?>
    <div class="elements">
        <ol>
            <?php foreach ($this['elements'] as $element): ?>
            <li class="element">
                <?php echo (string) $element ?>
            </li>
            <?php endforeach ?>
        </ol>
    </div>
    <?php endif ?>
    
    <script type="text/javascript">

    jQuery(document).ready(function($) {

        var frm = $('#<?php echo $this['id'] ?>');

        frm.__currentTooltip = null;

        frm.find('li.field[id]').each(function (i, li) {

            li = $(li);

            var id = li.attr('id').substring(6,99999);

            var handler = function (event) {

                if (frm.__currentTooltip !== id) {                                                        
                    frm.find('div.tooltip').hide();
                    frm.find('div.tooltip#tooltip-' + id).fadeIn(300);
                    frm.__currentTooltip = id;                            
                }
                event.stopPropagation();
            };

            li.bind('click', handler);

            li.find(':input').each(function (j, el) {

                var el = $(el);
                var p = el.parents('li.field');

                if (p.length >= 1 && ($(p[0]).attr('id') === 'field-' + id)) {                            
                    el.bind('focus', handler);                                                        
                }
            });

        });

    });                
    </script>
    
</div>