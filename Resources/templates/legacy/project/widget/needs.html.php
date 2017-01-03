<?php
use Goteo\Library\Text;

$project = $vars['project'];
$types   = $vars['types'];
$level = (int) $vars['level'] ?: 3;

$minimum    = \amount_format($project->mincost);
$optimum    = \amount_format($project->maxcost);

// separar los costes por tipo
$costs = array();

foreach ($project->costs as $cost) {
    $costs[$cost->type][] = (object) array(
        'name' => $cost->cost,
        'description' => $cost->description,
        'min' => $cost->required == 1 ? \amount_format($cost->amount) : '',
        'opt' => \amount_format($cost->amount),
        'req' => $cost->required
    );
}


?>
<div class="widget project-needs">

    <h<?php echo $level+1 ?> class="title"><?php echo Text::get('project-view-metter-investment'); ?></h<?php echo $level+1 ?>>

    <script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(document).ready(function() {
       $("div.click").click(function() {
           $(this).children("blockquote").toggle();
           $(this).children("span.icon").toggleClass("closed");
        });
     });
    // @license-end
	</script>
    <table width="100%">

        <?php foreach ($costs as $type => $list):

            // usort($list, function ($a, $b) {if ($a->req == $b->req) return 0; if ($a->req && !$b->req) return -1; if ($b->req && !$a->req) return 1;});
            ?>

        <thead class="<?php echo htmlspecialchars($type)?>">
            <tr>
                <th class="summary"><?php echo htmlspecialchars($types[$type]) ?></th>
                <th class="min"><?php echo Text::get('project-view-metter-minimum'); ?></th>
                <th class="max"><?php echo Text::get('project-view-metter-optimum'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($list as $cost): ?>
            <tr<?php echo ($cost->req == 1) ? ' class="req"' : ' class="noreq"' ?>>
                <th class="summary">
	                <div class="click">
                    	<span class="icon">&nbsp;</span>
                        <span><strong><?php echo htmlspecialchars($cost->name) ?></strong></span>
                        <blockquote><?php echo $cost->description ?></blockquote>
                    </div>
                </th>
                <td class="min"><?php echo $cost->min ?></td>
                <td class="max"><?php echo $cost->opt ?></td>
            </tr>
            <?php endforeach ?>
        </tbody>

        <?php endforeach ?>

        <tfoot>
            <tr>
                <th class="total"><?php echo Text::get('regular-total'); ?></th>
                <th class="min"><?php echo $minimum ?></th>
                <th class="max"><?php echo $optimum ?></th>
            </tr>
        </tfoot>

    </table>

    <div id="legend">
    	<div class="min"><span>&nbsp;</span><?php echo Text::get('costs-field-required_cost-yes') ?></div>
        <div class="max"><span>&nbsp;</span><?php echo Text::get('costs-field-required_cost-no') ?></div>
    </div>

</div>
