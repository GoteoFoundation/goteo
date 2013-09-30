<?php
use Goteo\Core\View,
    Goteo\Library\Text;
?>
<div class="widget projects">
    <h2 class="widget-title title"><?php echo Text::get('rewards-fields-social_reward-title'); ?></h2>
    <?php echo new View('view/project/edit/rewards/commons.html.php', $this); ?>
</div>
<?php echo new View('view/project/edit/rewards/commons.js.php'); ?>