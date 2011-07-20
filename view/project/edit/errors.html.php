<?php
use Goteo\Library\Text;

$project = $this['project'];
$step = $this['step'];

$step_errors = count($project->errors[$step]);
$total_errors = count($project->errors);
?>
<div>
    <?php if ($step != 'preview') : ?>
        <?php if (!empty($project->errors[$step])) : ?>
        <ul class="sf-footer-errors">
            <?php foreach ($project->errors[$step] as $id => $error) : ?>
            <li><a href="#<?php echo $id ?>" onclick="document.getElementById('<?php echo $id ?>').focus(); return false;"><?php echo $error ?></a></li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>
    <?php else : ?>
        <?php foreach ($project->errors as $st => $errors)  :
            if (!empty($errors)) : ?>
            <h4 class="title"><?php echo Text::get('step-'.$st); ?></h4>
            <ul class="sf-footer-errors">
            <?php foreach ($errors as $id => $error) : ?>
                <li><a href="#<?php echo $id ?>" onclick="document.getElementById('<?php echo $id ?>').focus(); return false;"><?php echo $error ?></a></li>
            <?php endforeach; ?>
            </ul>
        <?php endif;
            endforeach; ?>
    <?php endif; ?>

    <p>
    Errores en este Paso: <?php echo $step_errors ?><br />
    Erores en Total: <?php echo $total_errors ?>
    </p>

</div>

<script type="text/javascript">
$(function () {    
    $('div.superform').one('sfafterupdate', function (ev, el, html) {
        Superform.updateElement($('li#errors'), null, html);
    });
});
</script>
    