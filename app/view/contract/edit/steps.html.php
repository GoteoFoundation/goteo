<?php

use Goteo\Library\Text;

?>
<div id="project-steps">

    <fieldset>

        <legend><h3><?php echo Text::get('form-navigation_bar-header'); ?></h3></legend>

        <div class="steps">

        <?php foreach ($vars['steps'] as $step => $stepData) : ?>
            <span class="step <?php echo $stepData['class']; ?><?php if ($vars['step'] === $step) echo ' active'; else echo ' activable'; ?>">
                <button type="submit" name="view-step-<?php echo $step ?>" value="<?php echo $step; ?>"><?php echo $stepData['name']; ?>
                <strong class="number"><?php echo $stepData['num']; ?></strong></button>
            </span>
        <?php endforeach; ?>

        </div>

    </fieldset>
</div>
