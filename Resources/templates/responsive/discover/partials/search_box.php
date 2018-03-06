    <?php if($this->is_admin()): ?>
    <div class="checkbox text-danger"><label><input type="checkbox" id="include-in-review" name="review"<?= $this->get_query('review') === '0' ? '' : ' checked="checked"' ?>> <?= $this->text('discover-include-review-projects') ?></label></div>
    <?php endif ?>

    <div class="row">
      <div class="col-xs-12 col-sm-4">
        <form class="form-search" method="get" action="/discover">
          <div class="form-group">
            <label for="search-q"><?= $this->text('discover-by-keyword') ?></label>
            <div class="input-group">
                <input id="search-q" type="text" name="q" class="search-query form-control" placeholder="<?= $this->text('discover-searcher-button') ?>" value="<?= $this->get_query('q') ?>">
                <div class="input-group-btn"><button type="submit" class="btn btn-cyan" title="<?= $this->text('regular-search') ?>"><i class="fa fa-search"></i></button></div>
            </div>
          </div>
        </form>
      </div>

      <div class="col-xs-12 col-sm-4">
        <form class="form-search" method="get" action="/discover">
          <div class="form-group">
            <label for="search-location"><?= $this->text('discover-near-by') ?></label>
            <div class="input-group">
                <input id="search-location" type="text" name="location" class="search-query form-control geo-autocomplete" data-geocoder-filter="(regions)" data-geocoder-populate-latitude="#search-latitude" data-geocoder-populate-longitude="#search-longitude" placeholder="<?= $this->text('discover-any-city') ?>" value="<?= $this->get_query('location') ?>">
                <div class="input-group-btn"><button type="submit" class="btn btn-cyan" title="<?= $this->text('regular-search') ?>"><i class="fa fa-search"></i></button>
                </div>
          </div>
          </div>
          <input type="hidden" id="search-latitude" name="latitude" value="<?= $this->get_query('latitude') ?>">
          <input type="hidden" id="search-longitude" name="longitude" value="<?= $this->get_query('longitude') ?>">
        </form>
      </div>

      <div class="col-xs-12 col-sm-4">
        <form class="form-search" method="get" action="/discover">
          <div class="form-group">
            <label for="search-category"><?= $this->text('discover-searcher-bycategory-header') ?></label>
            <?= $this->html('select', ['name' => 'category', 'value' => $this->get_query('category'), 'options' => ['' => ''] + $this->raw('categories'), 'attribs' => ['id' => 'search-category', 'class' => 'form-control']]) ?>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <?= $this->insert('discover/partials/projects_nav') ?>
        </div>
    </div>

