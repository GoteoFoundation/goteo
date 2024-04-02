<?php $announcement = $this->announcement; ?>

<a href="<?= $announcement->getCtaUrl() ?>" class="btn btn-white btn-lg btn-block"><?= $announcement->getCtaText()?></a>