<?php

use Goteo\Library\Text;

$reward = $this['data']['reward'] ?>

<div class="reward <?php echo $reward->icon ?>">
    
    <div class="title"><strong><?php echo htmlspecialchars($reward->icon_name) . ': ' . htmlspecialchars($reward->reward) ?></strong></div>
    
    <div class="description">
        <p><?php echo htmlspecialchars($reward->description) ?></p>
        <div class="license license_<?php echo $reward->license ?>">
            <?php echo htmlspecialchars($this['data']['licenses'][$reward->license]) ?>
        </div>
    </div>

    
    <input type="submit" class="edit" name="<?php echo $reward->type ?>_reward-<?php echo $reward->id ?>-edit" value="<?php echo Text::get('form-edit-button') ?>" />
    <input type="submit" class="remove red" name="<?php echo $reward->type ?>_reward-<?php echo $reward->id ?>-remove" value="<?php echo Text::get('form-remove-button') ?>" />
    
</div>

    

    