<?php

use Goteo\Library\Text;

$errors = $vars['errors'];
$steps = $vars['steps'];

/**
 *  Estilos
 * ---------
 *
 * first: no tiene linea antes del círculo
 * off: linea/circulo gris
 * on: linea/circulo aqua
 * last: no tiene linea después del circulo
 *
 */
$line = array(
    'userProfile' => ' first-off off',
    'overview' => ' on-on',
    'supports' => ' on-on',
    'preview' => ' off-last off'
);

// URL
$url = "/call/edit/{$vars['id_call']}/";

?>
<div id="project-steps">

            <fieldset>

                <legend><h3><?php echo Text::get('form-navigation_bar-header'); ?></h3></legend>

                <div class="steps">

                    <?php foreach ($steps as $stepId => $stepData) {

                        // circulito
                        $active = ($vars['step'] == $stepId) ? ' active' : ' activable';

                        echo '<a href="' . $url . $stepId . '" title="' . $stepData['title'] . '">
                            <span class="step' . $line[$stepId] . $active . '">
                                <button type="button" name="view-step-' . $stepId . '" value="' . $stepId . '">' . $stepData['name'] . '</button>
                            </span>
                        </a>
                        ';

                    } ?>

                    <?php /**************************************************************************
                    <a href="/call/edit/<?php echo $vars['id_call']; ?>/userProfile">
                    <span class="step first-off off<?php if ($vars['step'] === 'userProfile') echo ' active'; else echo ' activable'; ?>">
                        <button type="button" name="view-step-userProfile" value="userProfile"><?php echo Text::get('step-1'); ?>
                        <strong class="number"></strong></button>
                    </span>
                    </a>

                    <a href="/call/edit/<?php echo $vars['id_call']; ?>/overview">
                    <span class="step on-on<?php if ($vars['step'] === 'overview') echo ' active'; else echo ' activable'; ?>">
                        <button type="button" name="view-step-overview" value="overview"><?php echo Text::get('step-3'); ?>
                        <strong class="number"></strong></button>
                    </span>
                    </a>

                    <a href="/call/edit/<?php echo $vars['id_call']; ?>/supports">
                    <span class="step on-on<?php if ($vars['step'] === 'supports') echo ' active'; else echo ' activable'; ?>">
                        <button type="button" name="view-step-supports" value="supports"><?php echo Text::get('call-step-3'); ?>
                        <strong class="number"></strong></button>
                    </span>
                    </a>

                    <a href="/call/edit/<?php echo $vars['id_call']; ?>/preview">
                    <span class="step off-last off<?php if ($vars['step'] === 'preview') echo ' active'; else echo ' activable'; ?>">
                        <button type="button" name="view-step-preview" value="preview"><?php echo Text::get('step-7'); ?>
                        <strong class="number"></strong></button>
                    </span>
                    </a>

                    **********************/ ?>

                </div>

            </fieldset>
        </div>
