<?php
use Goteo\Library\Text,
    Goteo\Core\View;
?>
<div class="side_widget">
    <?php echo \trace($this['summary']); ?>
    <div class="line">
        <?php echo $this['summary']['projects'] ?>
    </div>
    <div class="half">
        <?php echo $this['summary']['active'] ?>
    </div>
    <div class="half">
        <?php echo $this['summary']['success'] ?>
    </div>
    <div class="half">
        <?php echo $this['summary']['investors'] ?>
    </div>
    <div class="half">
        <?php echo $this['summary']['supporters'] ?>
    </div>
    <div class="line">
        <?php echo $this['summary']['amount'] ?>
    </div>
</div>