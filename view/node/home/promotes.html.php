<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$promotes = $this['promotes'];
?>
<div class="content_widget node-projects">

    <h2><?php echo Text::get('home-promotes-header'); ?></h2>
    ----------------<br />

    <ul>

        <?php
        foreach ($promotes as $promo) :

            $project = $promo->projectData;
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
                <?php if (!empty($project->categories)): ?>
                <div class="categories"><?php $sep = ''; foreach ($project->categories as $key=>$value) :
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