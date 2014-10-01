<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Worth;

$project = $this['project'];

$level = (int) $this['level'] ?: 3;

$reached    = \amount_format($project->invested);

$worthcracy = Worth::getAll();

// en la página de cofinanciadores, paginación de 20 en 20
require_once 'library/pagination/pagination.php';

$pagedResults = new \Paginated($project->investors, 20, isset($_GET['page']) ? $_GET['page'] : 1);


?>
<div class="widget project-supporters">
    
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-menu-supporters'); ?></h<?php echo $level ?>>
    
    <dl class="summary<?php if (!empty($project->amount_call)) echo ' drop'; ?>">
        <dt class="supporters"><?php echo Text::get('project-menu-supporters'); ?></dt>
        <dd class="supporters"><?php echo $project->num_investors ?></dd>
        
        <?php if (!empty($project->amount_call)) : ?>
        <dt class="droped"><?php echo Text::get('call_header_riego'); ?></dt>
        <dd class="droped"><a href="/call/<?php echo $project->called->id ?>" class="tipsy" title="<?php echo Text::get('call-splash-campaign_title').': '.$project->called->name ?>"><img src="<?php echo SRC_URL ?>/view/css/call/logo-capital_small.png" alt="CAMPAÑA" /></a><?php echo \amount_format($project->amount_call) ?></dd>
        <?php endif; ?>
        
        <dt class="reached"><?php echo Text::get('project-invest-total'); ?></dt>
        <dd class="reached"><?php echo $reached ?></dd>
    </dl>   

    <div class="supporters">
        <ul>
        <?php while ($investor = $pagedResults->fetchPagedRow()) : ?>
            <li><?php echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy)) ?></li>
        <?php endwhile ?>
        </ul>            
    </div>        

    <ul id="pagination">
        <?php   $pagedResults->setLayout(new DoubleBarLayout());
                echo $pagedResults->fetchPagedNavigation(); ?>
    </ul>

</div>