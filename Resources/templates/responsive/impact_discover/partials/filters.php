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
                <input type="checkbox" name="<?= $sdg->id ?>" />
                <img src="/assets/img/sdg/sdg<?= $sdg->id ?>.svg" alt="<?= $sdg->name ?>"/>
                <?= $sdg->id . $sdg->name ?>
              </li>
            <?php endforeach; ?>
          </ul>
          <p>
            <a href="" id="reset-sdg"><?= $this->t('regular-delete') ?></a>
            <button><?= $this->t('regular-apply') ?></button>
          </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-4 text-center" id="filters-channel">
          <?php if ($this->selectedChannel) : ?>
            <select class="form-control" name="channel" disabled>
              <option value="<?= $this->selectedChannel ?>" selected> <?= $this->channels[$this->selectedChannel] ?> </option>
            </select>
          <?php else: ?>
            <select class="form-control" name="channel" >
              <option value="" selected disabled hidden><?= $this->t('regular-channel') ?></option>

              <?php foreach($this->channels as $cid => $cname): ?>
                <option value="<?= $cid ?>"> <?= $cname ?></option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-4 text-center" id="filters-sdg-select">
          <select class="form-control" name="footprints">
            <option><?= $this->t('impact-discover-filter-by-sdg') ?></option>

            <?php foreach($this->sdgs as $sdg): ?>
              <option data-footprints="<?= implode(',',array_column($sdg->getFootprints(), 'id'))?>" value="<?= $sdg->id ?>"> <?= $sdg->name ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 text-right" id="filters-view-as">
          <ul>
          <li>
            <a id="activate-mosaic" class="<?= ($this->view == 'mosaic') ? 'active' : '' ?>" href="/impact-discover/mosaic"> <img src="/assets/img/icons/mosaic.svg"> </a>
          </li>
          <li>
            <a id="activate-projects" class="<?= ($this->view == 'list_projects')? 'active' : '' ?>" href="/impact-discover"><img src="/assets/img/icons/lists.svg"></a>
          </li>
          <li>
            <a id="activate-map" class="<?= ($this->view == 'map')? 'active' : '' ?>" href="/impact-discover/map"><img src="/assets/img/icons/map.svg"></a>
          </li>
          </ul>
        </div>
      </div>
  </div>
</div>
