<div class="impact-discover-sdg-list">
    <div class="container">
        <h2 id="search-projects" class="active"><?= $this->t('impact-discover-search-projects-sdgs')?></h2>
        <h2 id="list-projects" class=""><?= $this->t('impact-discover-projects-by-sdg') ?></h2>

        <div class="row" id="sdg-icons">
            <div class="col col-xs-12 col-sm12">
                <?php foreach($this->sdgSelected as $sdg): ?>
                    <div class="sdgicon" data-sdg="<?= $sdg ?>">
                        <img src="/assets/img/sdg/sdg<?= $sdg ?>.svg">
                        <a class="close flip" href="#">
                            <i class="icon icon-close"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
