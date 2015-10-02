<?php

use Goteo\Library\Text;

$support = $vars['data']['support'] ?>

<div class="support <?php echo $support->type ?>">


    <div class="title"><strong><?php echo htmlspecialchars($support->support) ?></strong></div>

    <div class="description"><?php echo htmlspecialchars($support->description) ?></div>

    <input type="submit" class="edit" name="support-<?php echo $support->id ?>-edit" value="<?php echo Text::get('regular-edit') ?>" />
</div>




