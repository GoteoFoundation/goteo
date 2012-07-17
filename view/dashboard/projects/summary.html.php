<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Project;

$project = $this['project'];

if (!$project instanceof  Goteo\Model\Project) {
    return;
}
?>
<div class="widget">
    <p><strong><?php echo $project->name ?></strong></p>
    <a class="button red" href="/project/edit/<?php echo $project->id ?>"><?php echo Text::get('regular-edit') ?></a>
    <a class="button" href="/project/<?php echo $project->id ?>" target="_blank"><?php echo Text::get('dashboard-menu-projects-preview') ?></a>
    <?php if ($project->status <= 1) : ?>
    <a class="button weak" href="/project/delete/<?php echo $project->id ?>" onclick="return confirm('<?php echo Text::get('dashboard-project-delete_alert') ?>')"><?php echo Text::get('regular-delete') ?></a>
    <?php endif ?>
</div>

<div class="status">

    <div id="project-status">
        <h3><?php echo Text::get('form-project-status-title'); ?></h3>
        <ul>
            <?php foreach (Project::status() as $i => $s): ?>
            <li><?php if ($i == $project->status) echo '<strong>' ?><?php echo htmlspecialchars($s) ?><?php if ($i == $project->status) echo '</strong>' ?></li>
            <?php endforeach ?>
        </ul>
    </div>

</div>

<div id="meter-big" class="widget collapsable">

    <?php echo new View('view/project/meter_hor_big.html.php', $this) ?>
    
</div>

<br clear="both" />
<br />

<?php if (isset($this['Data']) && !empty($project->passed) && empty($project->success)) : ?>
<p style="font-weight: bold; color: black;">Las cantidades que aparecen en este informe no son definitivas hasta que termine la segunda ronda</p>
<?php endif; ?>
<?php if (isset($this['Data']) && !empty($project->passed)) {
    echo new View('view/project/report.html.php', array('project'=>$project, 'Data'=>$this['Data']));
} ?>
