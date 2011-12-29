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

            <h2 class="title"><?php echo Text::get('call-splash-campaign_title') ?><br /><?php echo $call->name ?></h2>
            <p class="subtitle"><?php echo Text::get('call-splash-invest_explain', $call->user->name) ?></p>

            <h2 class="title"><?php echo Text::get('call-splash-selected_projects-header') ?></h2>

            <?php
            $c = 0;
            foreach ($call->projects as $proj) :
                $project = Project::getMedium($proj->id);
                $categories = Project\Category::getNames($proj->id, 2);
                $project->per_amount = round(($project->amount / $project->minimum) * 100);

                if ($c > 0) {
                    $c = 0;
//                    echo '<br style="clear:all;" /><br />';
                    echo '<br />';
                }
                ?>
            <div style="height:260px;width:160px;background-color:#EBE9E9;margin:0px 10px 10px 0px;">
                <div class="image" style="max-width: 150px; margin: 5px;">
                    <?php if (!empty($project->gallery) && (current($project->gallery) instanceof Image)): ?>
                    <a href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>"><img src="<?php echo current($project->gallery)->getLink(150, 98, true) ?>" alt="<?php echo $project->name ?>"/></a>
                    <?php endif ?>
                    <?php if (!empty($categories)): ?>
                    <div style="max-width: 150px;overflow:hidden;">
                    <?php $sep = ''; foreach ($categories as $key=>$value) :
                        echo $sep.htmlspecialchars($value);
                    $sep = ', '; endforeach; ?>
                    </div>
                    <?php endif ?>
                </div>

                <h5 style="max-width: 150px; margin: 5px; padding: 0px; max-height:35px;">
                    <a href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><?php echo htmlspecialchars(Text::recorta($project->name,50)) ?></a>
                </h5>
                <p style="max-width: 150px; margin: 5px; padding: 0px; height:35px;"><?php echo $project->subtitle; ?></p>

                <h6 style="max-width: 150px; margin: 5px; padding: 0px;">
                    <?php echo Text::get('regular-by')?> <a href="<?php echo SITE_URL ?>/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"<?php echo $blank; ?>><?php echo htmlspecialchars(Text::recorta($project->user->name,40)) ?></a>
                </h6>

                <fieldset style="max-width: 150px; margin: 5px; padding: 0px;">
                    <legend>Obtenido</legend>
                    <span><?php echo $project->amount ?> &euro;</span>&nbsp;&nbsp;|&nbsp;&nbsp;<span><?php echo $project->per_amount ?> &#37;</span>
                </fieldset>

                <span style="max-width: 150px; margin: 5px; padding: 0px;">Quedan <?php echo $project->days ?> d&iacute;as</span>

            </div>
            <?php
            $c++;
            endforeach; ?>
            </div>

        </div>

    </div>

<?php 

include 'view/call/footer.html.php';

include 'view/epilogue.html.php';

 ?>