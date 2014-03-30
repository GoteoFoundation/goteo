<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $this['project'];
$status = $this['statuses'];
?>
<a class="button" href="/admin/commons">Volver a la lista</a>
<div class="widget board">
    <h3><?php echo $project->name; ?> (<?php echo $status[$project->status]; ?>)</h3>
    <?php echo new View('view/project/edit/rewards/commons.html.php', array('project'=>$project, 'icons'=>$this)); ?>
</div>
<?php echo new View('view/project/edit/rewards/commons.js.php'); ?>