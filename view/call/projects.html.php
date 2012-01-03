<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Project,
    Goteo\Model\Image;

$bodyClass = 'projects';

$call = $this['call'];
$bgimage = $call->image->getLink(2000, 2000);
include 'view/call/prologue.html.php';
include 'view/call/header.html.php';
?>

	<div id="main">

		<?php echo new View('view/call/side.html.php', $this); ?>
	
        <div id="content">
		<div id="campaign-desc">

            <h2 class="title"><?php echo Text::get('call-splash-campaign_title') ?><br /><?php echo $call->name ?></h2>
            <p class="subtitle"><?php echo Text::get('call-splash-invest_explain', $call->user->name) ?></p>

            <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info" class="button long"><?php echo Text::get('call-splash-more_info-button') ?></a>

            <h2 class="title"><?php echo Text::get('call-splash-selected_projects-header') ?></h2>
		</div>
		<ul id="project-list">

            <?php
            foreach ($call->projects as $proj) :
                $project = Project::getMedium($proj->id);
                $categories = Project\Category::getNames($proj->id, 2);
                $project->per_amount = round(($project->amount / $project->minimum) * 100);

                ?>
			<li>
                <div class="image">
                    <?php if (!empty($project->gallery) && (current($project->gallery) instanceof Image)): ?>
                    <a href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>"><img src="<?php echo current($project->gallery)->getLink(150, 98, true) ?>" alt="<?php echo $project->name ?>"/></a>
                    <?php endif ?>
                    <?php if (!empty($categories)): ?>
                    <div class="categories">
                    <?php $sep = ''; foreach ($categories as $key=>$value) :
                        echo $sep.htmlspecialchars($value);
                    $sep = ', '; endforeach; ?>
                    </div>
                    <?php endif ?>
                </div>

                <h3 class="title">
                    <a href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><?php echo htmlspecialchars(Text::recorta($project->name,50)) ?></a>
                </h3>

                <h4 class="author">
                    <?php echo Text::get('regular-by')?> <a href="<?php echo SITE_URL ?>/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"<?php echo $blank; ?>><?php echo htmlspecialchars(Text::recorta($project->user->name,40)) ?></a>
                </h4>
                <div class="description"><?php echo $project->subtitle; ?></div>


                <div class="">
                    <span class="">Obtenido</span>
                    <div class=""><?php echo $project->amount ?> <span class="euro">&euro;</span></div>&nbsp;&nbsp;|&nbsp;&nbsp;<div class=""><?php echo $project->per_amount ?> &#37;</div>
                </div>

                <div class="days">Quedan <?php echo $project->days ?> d&iacute;as</div>

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