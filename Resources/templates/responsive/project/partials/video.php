<?php
$cover = $this->cover;
$embed = $this->raw('embed');
$id = $this->id ? $this->id : 'video_holder';

if($cover):
?>
    <div class="project-media video-holder embed-responsive embed-responsive-16by9" style="position:relative;" id="<?= $id ?>">
        <img src="<?= $cover ?>" class="img-responsive">
        <div onclick="document.getElementById('<?= $this->ee($id) ?>').innerHTML='<?= $this->ee($embed, 'js') ?>'" class="video-button"><img src="<?= SRC_URL ?>/assets/img/project/play.png" class="img-responsive"></div>
    </div>

<?php else: ?>

    <div class="project-media embed-responsive embed-responsive-16by9">
        <?= $embed ?>
    </div>

<?php endif ?>
