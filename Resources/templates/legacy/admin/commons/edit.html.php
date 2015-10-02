<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $vars['project'];
$status = $vars['statuses'];
?>
<a class="button" href="/admin/commons/view/<?php echo $project->id; ?>">Volver (sin guardar)</a>
<br /><br />
<div class="widget board">
    <h3><?php echo $project->name; ?> (<?php echo $status[$project->status]; ?>)</h3>
    <?php echo View::get('project/edit/rewards/edit_commons.html.php', $vars); ?>
</div>
