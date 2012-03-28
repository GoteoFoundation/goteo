<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$patrons = $this['patrons'];
?>
<div class="widget project-patrons">
    <h4>Proyecto recomendado por:</h4>
    <?php foreach ($patrons as $patron) : ?>
    <div class="patron">
        <div class="patron-name">
            <a href="/user/profile/<?php echo $patron->id ?>" title="<?php echo $patron->name ?>" target="_blank"><?php echo $patron->name ?></a>
        </div>
        <div class="patron-avatar">
            <a href="/user/profile/<?php echo $patron->id ?>" title="<?php echo $patron->name ?>" target="_blank"><img src="<?php echo $patron->avatar->getLink(112, 75) ?>" alt="<?php echo $patron->name ?>" /></a>
        </div>
        <div class="patron-reco">
            <span><a href="<?php echo $patron->link ?>" target="_blank"><?php echo $patron->title; ?></a></span>
            <blockquote><a href="<?php echo $patron->link ?>" target="_blank"><?php echo $patron->description; ?></a></blockquote>
        </div>
    </div>
    <?php endforeach; ?>
</div>