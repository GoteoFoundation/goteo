<?php
$project = $this['project'];
$step = $this['step'];

$step_errors = 0;
$total_errors = 0;

foreach ($project->errors as $st => $errors) {
    if ($st == $step) {
        $step_errors = count($errors);
    }
    $total_errors += count($errors);
}
?>
<div>
    Total: <?php echo $total_errors ?><br />
    Este paso: <?php echo $step_errors ?><br />
    
</div>

<script type="text/javascript">
$(function () {    
    $('div.superform').one('sfafterupdate', function (ev, el, html) {
        Superform.updateElement($('li#errors'), null, html);
    });
});
</script>
    