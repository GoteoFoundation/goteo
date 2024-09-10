<?php
$announcements = $this->announcements;
if ($announcements) :


    $id = md5(serialize($announcements));
?>
    <div class="announcement" id="<?= $id ?>">
        <button id="announcement-close" class="close" aria-label="Close" onclick="this.parentNode.style.display='none'"><i class="fa fa-close"></i></button>

        <div class="container">
            <div class="slider-announcements">
                <?php foreach ($announcements as $announcement) : ?>
                    <div class="row">
                        <div class="col-lg-8 col-md-7 col-md-offset-1 col-sm-offset-2 col-sm-6 col-xs-offset-3 col-xs-9">
                            <h2 class="announcement-title"><?= $announcement->title ?></h2>
                            <p class="announcement-description"><?= $announcement->description ?></p>
                        </div>
                        <div class="cta col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <?php $type = $announcement->type ?>
                            <?= $this->insert("partials/components/announcements/partials/$type", ['announcement' => $announcement]) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
