<?php

use Goteo\Library\Text;

$banner = $vars['data']['banner'] ?>

<div class="support">

    <?php if (is_object($banner->image)) : ?>
    <div class="thumb"><img src="<?php echo $banner->image->getLink(270, 100) ?>" alt="Imagen" /></div>
    <?php endif; ?>

    <p><?php echo htmlspecialchars($banner->name) ?></p>

    <input type="submit" class="edit" name="banner-<?php echo $banner->id ?>-edit" value="<?php echo Text::get('regular-edit') ?>" />
</div>

