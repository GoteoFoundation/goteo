<?php

use Goteo\Library\Text;

$reward = $vars['data']['reward'] ?>

<div class="reward <?php echo $reward->icon ?>">

    <div class="title"><strong><?php echo htmlspecialchars($reward->icon_name) . ': ' . htmlspecialchars($reward->reward) ?></strong></div>

    <div class="description">
        <p><?php echo htmlspecialchars($reward->description) ?></p>
    </div>


    <input type="submit" class="edit" name="<?php echo $reward->type ?>_reward-<?php echo $reward->id ?>-edit" value="<?php echo Text::get('regular-edit') ?>" />
</div>




