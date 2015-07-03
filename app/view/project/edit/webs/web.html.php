<?php

use Goteo\Library\Text;

$web = $vars['data']['web'] ?>

<div class="web">

    <div class="title"><strong><?php echo htmlspecialchars($web->url) ?></strong></div>

    <input type="submit" class="edit" name="web-<?php echo $web->id ?>-edit" value="<?php echo Text::get('regular-edit') ?>" />
    <input type="submit" class="remove weak" name="web-<?php echo $web->id ?>-remove" value="<?php echo Text::get('form-remove-button') ?>" />

</div>




