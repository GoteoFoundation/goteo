<?php

use Goteo\Library\Text;

$reward = $this['data']['reward'] ?>

<div class="reward <?php echo $reward->icon ?>">
    
    <div class="title"><strong><?php echo htmlspecialchars($reward->reward) ?></strong></div>
    
    <div class="description"><?php echo htmlspecialchars($reward->description) ?></div>

    <div class="license"><?php echo htmlspecialchars($this['license']) ?></div>
    
    <input type="submit" class="edit" name="<?php echo $reward->type ?>_reward-<?php echo $reward->id ?>-edit" value="<?php echo Text::get('form-edit-button') ?>" />
    <input type="submit" class="remove" name="<?php echo $reward->type ?>_reward-<?php echo $reward->id ?>-remove" value="<?php echo Text::get('form-remove-button') ?>" />
    
</div>

    

    