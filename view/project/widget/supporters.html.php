<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Worth;

$project = $this['project'];

$level = (int) $this['level'] ?: 3;

$reached    = \amount_format($project->invested);
$supporters = count($project->investors);

$worthcracy = Worth::getAll();

$investors = array();

foreach ($project->investors as $user=>$investor) {
    $investors[] = $investor;
}

// en la página de cofinanciadores, paginación de 20 en 20
require_once 'library/pagination/pagination.php';

$pagedResults = new \Paginated($investors, 20, isset($_GET['page']) ? $_GET['page'] : 1);


?>
<div class="widget project-supporters">
    
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-menu-supporters'); ?></h<?php echo $level ?>>
    
    <dl class="summary<?php if (isset($this['droped'])) echo ' drop'; ?>">
        <dt class="supporters"><?php echo Text::get('project-menu-supporters'); ?></dt>
        <dd class="supporters"><?php echo $supporters ?></dd>
        
        <dt class="reached"><?php echo Text::get('project-invest-total'); ?></dt>
        <dd class="reached"><?php echo $reached ?></dd>

        <?php if (isset($this['droped'])) : ?>
        <dt class="droped">Aportaciones Capital riego</dt>
        <dd class="droped"><a href="/call/<?php echo $project->called->id ?>" class="tipsy" title="<?php echo Text::get('call-splash-campaign_title').': '.$project->called->name ?>"><img src="<?php echo SRC_URL ?>/view/css/call/logo-capital_small.png" alt="CAMPAÑA" /></a><?php echo \amount_format($this['droped']) ?></dd>
        <?php endif; ?>
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