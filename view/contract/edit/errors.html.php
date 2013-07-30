<?php
use Goteo\Library\Text;

$contract = $this['contract'];
$step = $this['step'];
$step_errors = count($contract->errors[$step]);
$total_errors = 0;
foreach ($contract->errors as $st => $errors) {
    $total_errors += count($errors);
}

// Para que salte al campo
/*<!-- <a href="#<?php echo $id ?>" onclick="document.getElementById('<?php echo $id ?>').focus(); return false;"> -->*/

?>
<div>

    <p><?php echo Text::get('form-errors-info', $total_errors, $step_errors) ?></p>

        <?php if (!empty($contract->errors[$step])) : ?>
        <ul class="sf-footer-errors">
            <?php foreach ($contract->errors[$step] as $id => $error) : ?>
            <li>
                <?php echo $error ?>
            </li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>

</div>

<script type="text/javascript">
$(function () {    
    $('div.superform').one('sfafterupdate', function (ev, el, html) {
        Superform.updateElement($('li#errors'), null, html);
    });
});
</script>
    