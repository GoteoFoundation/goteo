<?php

use Goteo\Library\Text;

$banner = $this['data']['banner'] ?>

<div class="support">
    
    <div class="title"><strong><?php echo htmlspecialchars($banner->name) ?></strong></div>
    
    <?php if (is_object($banner->image)) : ?>
    <div class="thumb"><img src="<?php echo $banner->image->getLink(270, 100) ?>" alt="Imagen" /></div>
    <?php endif; ?>

    <!--
    <div class="position">UP | DOWN</div>
    -->

    <input type="submit" class="edit" name="banner-<?php echo $banner->id ?>-edit" value="<?php echo Text::get('regular-edit') ?>" />
    <input type="submit" class="remove weak" name="banner-<?php echo $banner->id ?>-remove" value="<?php echo Text::get('form-remove-button') ?>" />
</div>

    

    