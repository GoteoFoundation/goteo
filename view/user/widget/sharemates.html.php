<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$user = $this['user'];

$categories = Interest::getAll($user->id);

$shares = array();
foreach ($categories as $catId => $catName) {
    $shares[$catId] = Interest::share($user->id, $catId);
}


?>
<div class="widget user-mates">
	<!-- categorias -->
    <h3 class="supertitle"><?php echo Text::get('profile-sharing_interests-header'); ?></h3>
    <div class="categories">
    <ul>
        <?php foreach ($categories as $catId=>$catName) : ?>
        <li><a href="#"><?php echo $catName ?></a></li>
        <?php endforeach; ?>
    </ul>
    </div>
    
    
    <!-- usuarios sociales -->
    <?php
    // mostramos 2
    $muestra = 1;
    foreach ($shares as $catId => $sharemates) :
        if (count($sharemates) == 0) continue;
        ?>
    <div class="users" id="mates-<?php echo $catId ?>" <?php if ($muestra >= 2) {echo ' display="none"';} else {$muestra++;} ?>>
	    <h3 class="supertitle"><?php echo $categories[$catId] ?></h3>
        <ul>
        <?php $c=1; // limitado a 6 sharemates en el lateral
        foreach ($sharemates as $mate): ?>
            <li class="activable">            	
                <div class="user">
                	<a href="/user/<?php echo htmlspecialchars($mate->user) ?>" class="expand">&nbsp;</a>
                    <div class="avatar">
                        <a href="/user/<?php echo htmlspecialchars($mate->user) ?>">
                            <img src="/image/<?php echo $mate->avatar->id ?>/43/43/1" />
                        </a>
                    </div>
                    <h4>
                    	<a href="/user/<?php echo htmlspecialchars($mate->user) ?>">
						<?php echo htmlspecialchars($mate->user) ?>
                        </a>
                    </h4>
                    <span class="projects">
						<?php echo Text::get('regular-projects'); ?> (<?php echo $mate->projects ?>)
                    </span>
                    <span class="invests">
						<?php echo Text::get('regular-investing'); ?> (<?php echo $mate->invests ?>)
                    </span>
                </div>
            </li>
        <?php if ($c>5) break; else $c++;
        endforeach ?>
        </ul>
        <a class="more" href="/user/profile/<?php echo $this['user']->id ?>/sharemates/<?php echo $catId ?>"><?php echo Text::get('regular-see_more'); ?></a>
    </div>
    <?php endforeach; ?>
    
</div>
