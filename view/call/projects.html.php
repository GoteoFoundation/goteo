<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Project,
    Goteo\Model\Image;

$bodyClass = 'projects';

$call = $this['call'];
$bgimage = $call->image->getLink(5000, 5000);

// reordenar proyectos: random pero si ya no está en campaña sale al final
$final = array();

foreach ($call->projects as $key=>$proj) {

    if ($proj->status < 3 || $proj->status > 5) {
        unset($call->projects[$key]);
    }

    if ($proj->status == 4 || $proj->status == 5) {
        $final[$proj->id] = $proj;
        unset($call->projects[$key]);
    }
}

shuffle($call->projects);
shuffle($final);

$call->projects = array_merge($call->projects, $final);

include 'view/call/prologue.html.php';
include 'view/call/header.html.php';
?>

	<div id="main">

		<?php echo new View('view/call/side.html.php', $this); ?>
	
        <div id="content">
            <div id="campaign-desc">
                <?php echo new View('view/call/widget/title.html.php', $this); ?>

                <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info" class="button long"><?php echo Text::get('call-splash-more_info-button') ?></a>

                <h2 class="title"><?php echo Text::get('call-splash-selected_projects-header') ?></h2>
            </div>
        
		<ul id="project-list">

            <?php
            foreach ($call->projects as $proj) :

                $project = Project::getMedium($proj->id);
                $categories = Project\Category::getNames($proj->id, 2);
                $project->per_amount = round(($project->amount / $project->mincost) * 100);

                ?>
			<li>
				<a href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>" class="expand" target="_blank"></a>
                <div class="image">
                    <?php switch ($project->tagmark) {
                        case 'onrun': // "en marcha"
                            echo '<div class="tagmark green">' . Text::get('regular-onrun_mark') . '</div>';
                            break;
                        case 'keepiton': // "aun puedes"
                            echo '<div class="tagmark green">' . Text::get('regular-keepiton_mark') . '</div>';
                            break;
                        case 'onrun-keepiton': // "en marcha" y "aun puedes"
            //                echo '<div class="tagmark green">' . Text::get('regular-onrun_mark') . '</div>';
                              echo '<div class="tagmark green twolines"><span class="small"><strong>' . Text::get('regular-onrun_mark') . '</strong><br />' . Text::get('regular-keepiton_mark') . '</span></div>';
                            break;
                        case 'gotit': // "financiado"
                            echo '<div class="tagmark violet">' . Text::get('regular-gotit_mark') . '</div>';
                            break;
                        case 'success': // "exitoso"
                            echo '<div class="tagmark red">' . Text::get('regular-success_mark') . '</div>';
                            break;
                        case 'fail': // "caducado"
                            echo '<div class="tagmark grey">' . Text::get('regular-fail_mark') . '</div>';
                            break;
                    } ?>

                    <?php if (!empty($project->gallery) && (current($project->gallery) instanceof Image)): ?>
                    <a href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>"><img src="<?php echo current($project->gallery)->getLink(150, 98, true) ?>" alt="<?php echo $project->name ?>"/></a>
                    <?php endif ?>
                    <?php if (!empty($categories)): ?>
                    <div class="categories"><?php $sep = ''; foreach ($categories as $key=>$value) :
                        echo $sep.htmlspecialchars($value);
                    $sep = ', '; endforeach; ?></div>
                    <?php endif ?>
                </div>
                <h3 class="title"><a href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><?php echo htmlspecialchars(Text::recorta($project->name,50)) ?></a></h3>
                <div class="description"><?php echo empty($project->subtitle) ? Text::recorta($project->description, 100) : Text::recorta($project->subtitle, 100); ?></div>
                <h4 class="author"><?php echo Text::get('regular-by')?> <a href="<?php echo SITE_URL ?>/user/profile/<?php echo htmlspecialchars($project->user->id) ?>" target="_blank"><?php echo htmlspecialchars(Text::recorta($project->user->name,40)) ?></a></h4>
				<span class="obtained"><?php echo Text::get('project-view-metter-got'); ?></span>
				<div class="obtained">
		            <strong><?php echo \amount_format($project->amount) ?> <span class="euro">&euro;</span></strong>
		            <span class="percent"><?php echo $project->per_amount ?> &#37;</span>
		        </div>
				<div class="days"><span><?php echo Text::get('project-view-metter-days'); ?></span> <?php echo $project->days ?> <?php echo Text::get('regular-days'); ?></div>
			</li>
            <?php
            endforeach; ?>
		</ul>

        </div>

    </div>

<?php 

include 'view/call/footer.html.php';

include 'view/epilogue.html.php';

 ?>