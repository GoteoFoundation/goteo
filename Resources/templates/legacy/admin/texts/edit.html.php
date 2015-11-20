<?php

use Goteo\Library\Text;


?>
<div class="widget board">
    <form action="<?php echo $vars['form']['action']; ?>" method="post" enctype="multipart/form-data">
        <dl>
            <?php foreach ($vars['form']['fields'] as $Id=>$field) : ?>
                <dt><label for="<?php echo $Id; ?>"><?php echo $field['label']; ?></label></dt>
                <dd><?php switch ($field['type']) {
                    case 'text': ?>
                        <input type="text" id="<?php echo $Id; ?>" name="<?php echo $field['name']; ?>" <?php echo $field['properties']; ?> value="<?php $name = $field['name']; echo $vars['data']->$name; ?>" />
                    <?php break;
                    case 'hidden': ?>
                        <input type="hidden" name="<?php echo $field['name']; ?>" <?php echo $field['properties']; ?> value="<?php $name = $field['name']; echo $vars['data']->$name; ?>" />
                    <?php break;
                    case 'textarea': ?>
                        <textarea id="<?php echo $Id; ?>" name="<?php echo $field['name']; ?>" <?php echo $field['properties']; ?>><?php $name = $field['name']; echo $vars['data']->$name; ?></textarea>
                    <?php break;
                } ?></dd>

            <?php endforeach; ?>
        </dl>
        <input type="submit" name="<?php echo $vars['form']['submit']['name']; ?>" value="<?php echo $vars['form']['submit']['label']; ?>" />
        <a href="/translate/texts/edit/<?= $vars['data']->id ?>" target="_blank" class="button" style="color: #FFF; font-size:1.2em;">Traducir</a>
        <p>
            <label for="mark-pending">Marcar como pendiente de traducir</label>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </p>

    </form>
</div>
