<?php
use Goteo\Library\Text;

$project = $this['project'];
$step = $this['step'];
$step_errors = count($project->errors[$step]);
$total_errors = 0;
foreach ($project->errors as $st => $errors) {
    $total_errors += count($errors);
}

// Para que salte al campo
/*<!-- <a href="#<?php echo $id ?>" onclick="document.getElementById('<?php echo $id ?>').focus(); return false;"> -->*/

?>
<div>

    <?php if ($step != 'preview') : ?>
        <p>Total: <?php echo $total_errors ?> | En este paso: <?php echo $step_errors ?></p>

        <?php if (!empty($project->errors[$step])) : ?>
        <ul class="sf-footer-errors">
            <?php foreach ($project->errors[$step] as $id => $error) : ?>
            <li>
                <?php echo $error ?>
            </li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>
    <?php else : ?>
        <p>Total: <?php echo $total_errors; ?></p>
        <?php foreach ($project->errors as $st => $errors)  :
            if (!empty($errors)) : ?>
            <h4 class="title"><?php echo Text::get('step-'.$st); ?></h4>
            <ul class="sf-footer-errors">
            <?php foreach ($errors as $id => $error) : ?>
                <li><?php echo $error ?></li>
            <?php endforeach; ?>
            </ul>
        <?php endif;
            endforeach; ?>
    <?php endif; ?>

</div>

<script type="text/javascript">
$(function () {    
    $('div.superform').one('sfafterupdate', function (ev, el, html) {
        Superform.updateElement($('li#errors'), null, html);
    });
});
</script>
    