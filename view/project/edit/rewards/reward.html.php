<?php

use Goteo\Library\Text;

$reward = $this['data']['reward'] ?>

<div class="reward <?php echo $reward->icon ?>">
    
    <div class="title"><strong><?php echo htmlspecialchars($reward->icon_name) . ': ' . htmlspecialchars($reward->reward) ?></strong></div>
    
    <div class="description">
        <p><?php echo htmlspecialchars($reward->description) ?></p>
        <?php if (!empty($reward->units)) : ?>
                <?php echo "{$reward->units} u. x {$reward->amount} &euro; = " . ($reward->units * $reward->amount) ." &euro;<br />"; ?>
                <strong><?php echo Text::get('project-rewards-individual_reward-limited'); ?></strong>
                <?php $units = $reward->units;
                echo Text::html('project-rewards-individual_reward-units_left', $units); ?><br />
            <?php endif; ?>
        <div class="license license_<?php echo $reward->license ?>"><?php echo htmlspecialchars($this['data']['licenses'][$reward->license]) ?></div>
    </div>

    
    <input type="submit" class="edit" name="<?php echo $reward->type ?>_reward-<?php echo $reward->id ?>-edit" value="<?php echo Text::get('form-edit-button') ?>" />
    <input type="submit" class="remove weak" name="<?php echo $reward->type ?>_reward-<?php echo $reward->id ?>-remove" value="<?php echo Text::get('form-remove-button') ?>" />
    
</div>

    

    