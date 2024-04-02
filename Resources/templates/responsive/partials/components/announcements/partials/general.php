<?php $announcement = $this->announcement; ?>

<a href="<?= $announcement->getCtaUrl() ?>" class="btn btn-white btn-lg pull-center"><?= $announcement->getCtaText()?></a>