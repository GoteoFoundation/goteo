<?php

use Goteo\Library\Text;

$cost = $vars['data']['cost'];
?>

<div class="cost <?php echo $cost->type ?>">


    <div class="title"><strong><?php echo htmlspecialchars($cost->cost) ?></strong></div>
    <input type="submit" class="edit" name="cost-<?php echo $cost->id ?>-edit" value="<?php echo Text::get('regular-edit') ?>" />
    <input type="submit" class="remove weak" name="cost-<?php echo $cost->id ?>-remove" value="<?php echo Text::get('form-remove-button') ?>" />

    <div class="description">
        <?php echo htmlspecialchars($cost->description) ?>
        <p><?php echo $cost->amount_format ?>
            <strong><?php echo $cost->required ? Text::get('costs-field-required_cost-yes') : Text::get('costs-field-required_cost-no') ?></strong>
        </p>

    </div>


</div>




