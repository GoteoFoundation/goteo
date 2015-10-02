<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $vars['project'];
$status  = $vars['status'];

?>
<a class="button" href="/admin/commons">Volver a la lista</a>
<div class="widget board">
    <h3><?php echo $project->name; ?> (<?php echo $status[$project->status]; ?>)</h3>

    <?php include __DIR__ . '/contact.html.php' ?>

</div>
