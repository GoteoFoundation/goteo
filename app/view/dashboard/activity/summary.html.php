<?php
use Goteo\Core\View,
    Goteo\Model,
    Goteo\Library\Worth,
    Goteo\Library\Text;

$waitfor = Model\Project::waitfor();
$worthcracy = Worth::getAll();

$user = $_SESSION['user'];

$support = $user->support;

$lsuf = (LANG != 'es') ? '?lang='.LANG : '';
?>
<script type="text/javascript">

    jQuery(document).ready(function ($) {

        /* todo esto para cada lista de proyectos (flechitas navegacion) */
            $("#discover-group-my_projects-1").show();
            $("#navi-discover-group-my_projects-1").addClass('active');
            $("#discover-group-invest_on-1").show();
            $("#navi-discover-group-invest_on-1").addClass('active');

        $(".discover-arrow").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-discover-group-"+this.rev).removeClass('active');
            $(".discover-group-"+this.rev).hide();
            /* Poner acctive a este, mostrar este */
            $("#navi-discover-group-"+this.rel).addClass('active');
            $("#discover-group-"+this.rel).show();
        });

        $(".navi-discover-group").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-discover-group-"+this.rev).removeClass('active');
            $(".discover-group-"+this.rev).hide();
            /* Poner acctive a este, mostrar este */
            $("#navi-discover-group-"+this.rel).addClass('active');
            $("#discover-group-"+this.rel).show();
        });

    });
</script>

<!-- monedero credito goteo -->
<?php if ($this['pool']->amount) : ?>
<div class="widget">
    <div class="crowd-credit">
        <h3 class="title">Crédito Crowd</h3>
        <div class="credit-amount">
            <?php echo \amount_format($this['pool']->amount); ?>
        </div>
    </div>
    <div class="crowd-credit-help">
    Dispones de crédito Crowd para utilizar en Goteo. A continuación te proponemos proyectos que creemos pueden ser de tu interés para apoyar.
    </div>
</div>
<?php endif; ?>

<!-- Proyectos que cofinancio -->
<?php if (!empty($this['lists']['invest_on'])) : ?>
    <div class="widget projects">
        <h2 class="title"><?php echo Text::get('profile-invest_on-header'); ?></h2>
        <?php foreach ($this['lists']['invest_on'] as $group=>$projects) : ?>
            <div class="discover-group discover-group-invest_on" id="discover-group-invest_on-<?php echo $group ?>">

                <div class="discover-arrow-left">
                    <a class="discover-arrow" href="#invest_on" rev="invest_on" rel="<?php echo 'invest_on-'.$projects['prev'] ?>">&nbsp;</a>
                </div>

                <?php foreach ($projects['items'] as $project) :

                    $url = SITE_URL . '/widget/project/' . $project->id;
                    $widget_code = Text::widget($url . $lsuf);
                    $widget_code_investor = Text::widget($url.'/invested/'.$user->id.'/'.$lsuf);
                    ?>
                <div style="float:left;">
                      <?php  echo View::get('project/widget/project.html.php', array(
                            'project' => $project,
                            'investor'  => $user
                        )); ?>
                <br clear="both"/>
                <?php if ($project->status > 2) : ?>
                      <div id="widget-code" style="float:none;width:250px;margin-left:25px;">
                          <div class="wc-embed" onclick="$('#widget_code').focus();$('#widget_code').select()"><?php echo Text::get('dashboard-embed_code'); ?></div>
                        <textarea id="widget_code" style="width:230px;margin:0 0 10px;" onclick="this.focus();this.select()" readonly="readonly"><?php echo htmlentities($widget_code); ?></textarea>
                      </div>

                      <div id="widget-code" style="float:none;width:250px;margin-left:25px;">
                        <div class="wc-embed" onclick="$('#investor_code').focus();$('#investor_code').select()"><?php echo Text::get('dashboard-embed_code_investor'); ?></div>
                        <textarea id="investor_code" style="width:230px;margin:0 0 10px;" onclick="this.focus();this.select()" readonly="readonly"><?php echo htmlentities($widget_code_investor); ?></textarea>
                      </div>
                <?php endif; ?>
                </div>
                <?php endforeach; ?>

                <div class="discover-arrow-right">
                    <a class="discover-arrow" href="#invest_on" rev="invest_on" rel="<?php echo 'invest_on-'.$projects['next'] ?>">&nbsp;</a>
                </div>

            </div>
        <?php endforeach; ?>


        <!-- carrusel de cuadritos -->
        <div class="navi-bar">
            <ul class="navi">
                <?php foreach (array_keys($list) as $group) : ?>
                <li><a id="navi-discover-group-<?php echo 'invest_on-'.$group ?>" href="#invest_on" rev="invest_on" rel="<?php echo "invest_on-{$group}" ?>" class="navi-discover-group navi-discover-group-invest_on"><?php echo $group ?></a></li>
                <?php endforeach ?>
            </ul>
        </div>

    </div>
<?php endif; ?>

<!-- nivel de meritocracia -->
<?php echo View::get('user/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth, 'amount' => $support['amount'])) ?>
