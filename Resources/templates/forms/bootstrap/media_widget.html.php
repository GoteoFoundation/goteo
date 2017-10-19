<?php
$id = $id ? $id : 'video_holder';
?>

<div class="media-container">
    <div class="generic-media video-holder" id="<?= $id ?>">
        <div class="embed-responsive embed-responsive-16by9"></div>
        <img class="cover-image img-responsive">
        <div class="video-button"><img src="<?= SRC_URL ?>/assets/img/project/play.png" class="img-responsive"></div>
    </div>


    <?php echo $view['form']->block($form, 'form_widget_simple', ['type' => 'text']); ?>
</div>
