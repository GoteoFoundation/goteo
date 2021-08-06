<div class="section impact-discover-filters active">
  <div class="container">
      <div class="row">
        <div class="col-xs-12 col-sm-9" id="filters-mobile">
          <a href="" class="filter"><img src="./assets/img/icons/funnel.svg" /> FILTRAR</a>
          <a class="close flip" href="#"><i class="icon icon-close"></i></a>
        </div>
        <div class="col-xs-12 col-sm-4" id="filters-footprints">
          <p>Filtra por Huellas de Goteo</p>
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
        <div class="col-xs-12 col-sm-3" id="filters-ods-list">
          <p>Filtra por Objetivos de Desarrollo Sostenible</p>
          <ul>
            <?php foreach($this->sdgs as $sdg): ?>
            <li>
              <input type="checkbox" name="<?= $sdg->name ?>" />
              <!-- <img src="./assets/img/ods/ods1.svg" /> -->
              <img src="./assets/img/ods/ods<?= $sdg->id ?>.svg" alt="<?= $sdg->name ?>"/>
              <?= $sdg->id . $sdg->name ?>
            </li>
          <?php endforeach; ?>
          </ul>
          <p>
            <a href="" id="reset-ods">Borrar todo</a>
            <button>Aplicar filtros</button>
          </p>
        </div>
        <div class="col-xs-12 col-sm-5 text-center" id="filters-ods-select">
          <select>
            <option>Filtra por Objetivos de Desarrollo sostenible</option>
            <?= foreach($this->sdgs as $sdg?>
            <option data-footprints="eco">ODS1</option>
            <option data-footprints="soc">ODS2</option>
            <option data-footprints="dem">ODS3</option>
            <option data-footprints="eco,soc">ODS4</option>
            <option data-footprints="soc,dem">ODS5</option>
            <option data-footprints="eco,dem">ODS6</option>
            <option data-footprints="eco,doc,dem">ODS7</option>
          </select>
        </div>
        <div class="col-xs-12 col-sm-3 text-right" id="filters-view-as">
          <a id="activate-mosaic" onclick="activateMosaic()"><img src="./assets/img/icons/mosaic.svg"></a>
          <a id="activate-projects" onclick="activateProjects()" class="active"><img src="./assets/img/icons/lists.svg"></a>
          <a id="activate-map" onclick="activateMap()"><img src="./assets/img/icons/map.svg"></a>
        </div>
      </div>
  </div>
</div>
