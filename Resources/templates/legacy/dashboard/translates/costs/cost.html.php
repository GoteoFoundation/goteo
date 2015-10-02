<?php

use Goteo\Library\Text;

$cost = $vars['data']['cost'] ?>

<div class="cost <?php echo $cost->type ?>">


    <div class="title"><strong><?php echo htmlspecialchars($cost->cost) ?></strong></div>
    <input type="submit" class="edit" name="cost-<?php echo $cost->id ?>-edit" value="<?php echo Text::get('regular-edit') ?>" />

    <div class="description">
        <?php echo htmlspecialchars($cost->description) ?>

    </div>


</div>




