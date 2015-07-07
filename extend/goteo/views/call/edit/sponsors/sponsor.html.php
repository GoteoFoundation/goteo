<?php

use Goteo\Library\Text;

$sponsor = $vars['data']['sponsor'] ?>

<div class="sponsor">


    <div class="title"><strong><?php echo htmlspecialchars($sponsor->name) ?></strong></div>

    <?php if (is_object($sponsor->image)) : ?>
    <div class="thumb"><img src="<?php echo $sponsor->image->getLink(100, 100) ?>" alt="Imagen" /></div>
    <?php endif; ?>

    <!--
    <div class="position">UP | DOWN</div>
    -->

    <input type="submit" class="edit" name="sponsor-<?php echo $sponsor->id ?>-edit" value="<?php echo Text::get('regular-edit') ?>" />
    <input type="submit" class="remove weak" name="sponsor-<?php echo $sponsor->id ?>-remove" value="<?php echo Text::get('form-remove-button') ?>" />
</div>




