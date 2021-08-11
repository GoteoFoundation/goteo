<div class="section impact-discover-filters active">
  <div class="container">
      <div class="row">
        <div class="col-xs-12 col-sm-9" id="filters-mobile">
          <a href="" class="filter"><img src="./assets/img/icons/funnel.svg" /> FILTRAR</a>
          <a class="close flip" href="#"><i class="icon icon-close"></i></a>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6" id="filters-footprints">
          <p><?= $this->t('impact-discover-filter-by-footprints') ?></p>
          <ul>
            <li><a href="" data-footprint="all" class="active">Todas</a></li>
            <?php foreach($this->footprints as $footprint): ?>
              <li>
                <a href="" data-footprint="<?= $footprint->id ?>">
                  <img src="/assets/img/<?= $footprint->getIcon() ?>" alt="<?= $footprint->name ?>">
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="col-xs-12 col-sm-3" id="filters-sdg-list">
          <p><?= $this->t('impact-discover-filter-by-sdg') ?></p>
          <ul>
            <?php foreach($this->sdgs as $sdg): ?>
              <li>
                <input type="checkbox" name="<?= $sdg->name ?>" />
                <img src="./assets/img/sdg/sdg<?= $sdg->id ?>.svg" alt="<?= $sdg->name ?>"/>
                <?= $sdg->id . $sdg->name ?>
              </li>
            <?php endforeach; ?>
          </ul>
          <p>
            <a href="" id="reset-sdg"><?= $this->t('regular-delete') ?></a>
            <button><?= $this->t('regular-apply') ?></button>
          </p>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 text-center" id="filters-sdg-select">
          <select>
            <option><?= $this->t('impact-discover-filter-by-sdg') ?></option>

            <?php foreach($this->sdgs as $sdg): ?>
              <option data-footprints="<?= implode(',',array_column($sdg->getFootprints(), 'id'))?>" value="<?= $sdg->id ?>"> <?= $sdg->name ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 text-right" id="filters-view-as">
          <a id="activate-mosaic"><img src="./assets/img/icons/mosaic.svg"></a>
          <a id="activate-projects" class="active"><img src="./assets/img/icons/lists.svg"></a>
          <a id="activate-map"><img src="./assets/img/icons/map.svg"></a>
        </div>
      </div>
  </div>
</div>
