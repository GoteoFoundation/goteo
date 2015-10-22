<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Project;

$project = $vars['project'];

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

    <div id="project-status" style="width: 280px;">
        <h3><?php echo Text::get('form-project-status-title'); ?></h3>
        <ul>
            <?php foreach (Project::status() as $i => $s): ?>
            <li><?php if ($i == $project->status) echo '<strong>' ?><?php echo htmlspecialchars($s) ?><?php if ($i == $project->status) echo '</strong>' ?></li>
            <?php endforeach ?>
        </ul>
    </div>

</div>

<div id="meter-big" class="widget collapsable">

    <?php echo View::get('project/meter_hor_big.html.php', $vars) ?>

</div>

<br clear="both" />
<br />

<?php if (in_array($project->status, array(3, 4, 5))) : ?>
<!-- librerias externas -->
    <script language="javascript" type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.js"></script>
    <script language="javascript" type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js"></script>
    <script language="javascript" type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/datatables/1.9.4/jquery.dataTables.min.js"></script>

    <!-- funciones para la visualización -->
    <script language="javascript" type="text/javascript" src="<?php echo SRC_URL; ?>/view/js/project/chart.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo SRC_URL; ?>/view/js/project/visualizers.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo SRC_URL; ?>/view/js/project/display.js"></script>

    <!-- estilos para la visualización -->
    <link rel="stylesheet" type="text/css" href="<?php echo SRC_URL; ?>/view/css/dashboard/projects/graph.css"/>


    <div class="widget chart">
            <div id="project_selection" style="margin-bottom: 10px"></div>
            <div class="titles">
                <div>
                    <h2><?php echo Text::get('dashboard-chart-invested'); ?></h2>
                <?php if (empty($vars['data']['invests'])) : ?>
                    <div id="funded" class="obtenido number"><?php echo Text::get('dashboard-chart-no-invested'); ?></div>
                <?php else : ?>
                    <div id="funded" class="obtenido number"><?php echo \amount_format($project->invested); ?> &euro;</div>
                    <div id="de" class="de"><?php echo Text::get('regular-of'); ?></div>
                    <div id="minimum" class="minimum number"><?php echo \amount_format($project->mincost); ?> &euro;</div>
                    <div id="euros" class="euros">
                         (<div style='color: #bb70b6; display: inline'><?php echo \amount_format($project->maxcost); ?> &euro;</div> <?php echo Text::get('dashboard-chart-optimum'); ?>)
                    </div>
                <?php endif; ?>
                </div>
                <?php if ($project->status == 3) : ?>
                <div class="quedan">
                    <div style="font-weight: normal; font-size: 12px"><?php echo \vsprintf(Text::get('dashboard-chart-days') , array('<h2 id="dias" style="display:inline; margin:0px 5px">'.$project->days.'</h2>')); ?></div>
                </div>
                <?php endif; ?>
            </div>
            <div id="funds" class="chart_div"></div>
            <div>
                <h2><?php echo Text::get('dashboard-chart-investors'); ?></h2>
            </div>
            <div id="cofund" class="chart_div"></div>
    </div>

<script type="text/javascript">
    /* función para cargar los datos del gáfico, sacado de graphA.js */
jQuery(document).ready(function(){
        GOTEO.initializeGraph(<?php echo json_encode($vars['data']); ?>);
    });

</script>
<?php elseif (($project->status == 2) && (!empty($project->published))): ?>
        <div class="widget chart">
            <div id="project_selection" style="margin-bottom: 10px"></div>
            <div class="titles">
                <h2 style="color:#20b3b2;"><?php echo 'Este proyecto se publicará el día ' .  date('d-m-Y', strtotime($project->published)) ; ?></h2>
            </div>
        </div>
<?php endif; ?>
<div class="widget projects">
    <h2 class="widget-title title"><?php echo Text::get('project-spread-widget_title'); ?></h2>
    <?php echo View::get('project/widget/embed.html.php', array('project'=>$project)) ?>
</div>
