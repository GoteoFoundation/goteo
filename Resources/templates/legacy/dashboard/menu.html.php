<?php
//piñonaco que te crio
$vars['option'] = str_replace(array('call_overview', 'node_overview'), array('overview', 'overview'), $vars['option']);
?>
<div id="dashboard-menu">
    <ul>
    <?php foreach ($vars['menu'] as $section=>$item) : ?>
        <li class="section<?php if ($section == $vars['section']) echo ' current'; ?>">
            <a class="section" href="/dashboard/<?php echo $section; ?>"><?php echo $item['label']; ?></a>
            <ul>
            <?php foreach ($item['options'] as $option=>$label) : ?>
                <li class="option<?php if ($section == $vars['section'] && $option == $vars['option']) echo ' current'; ?>">
                    <a href="/dashboard/<?php echo $section; ?>/<?php echo $option; ?>"<?php if ($option == 'public') echo ' target="_blank"' ?>><?php echo $label; ?></a>
                </li>
            <?php endforeach; ?>
            </ul>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
