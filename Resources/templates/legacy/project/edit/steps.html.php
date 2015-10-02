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
 * - Fieldset: contiene pasos en una linea roja, reza 'proyecto nuevo'.
 */
$line = array(
    'userProfile' => ' first-off off',
    'userPersonal' => ' off-off off',
    'overview' => ' off-on',
    'images' => ' on-on',
    'costs' => ' on-on',
    'rewards' => ' on-on',
    'supports' => ' on-off',
    'preview' => ' off-last off'
);

// URL
$url = "/project/edit/{$vars['id_project']}/";
?>

<div id="project-steps">

            <fieldset>

                <legend><h3><?php echo Text::get('form-navigation_bar-header'); ?></h3></legend>

                <div class="steps">

                    <?php foreach ($steps as $stepId => $stepData) {

                        // fieldset start/end
                        if ($stepId == 'overview') echo '<fieldset style="display: inline"><legend>'.Text::get('regular-new_project').'</legend>';
                        if ($stepId == 'preview') echo '</fieldset>';

                        // circulito
                        $active = ($vars['step'] == $stepId) ? ' active' : ' activable';

                        echo '<a href="' . $url . $stepId . '" title="' . $stepData['title'] . '">
                            <span class="step' . $line[$stepId] . $active . '">
                                <button type="button" name="view-step-' . $stepId . '" value="' . $stepId . '">' . $stepData['name'] . '</button>
                            </span>
                        </a>
                        ';

                    } ?>

                </div>

            </fieldset>
        </div>
