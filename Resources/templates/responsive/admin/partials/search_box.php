<?php

$filter = $this->a('filter');

$class = $filter['_class'];
if(!$class) $class = 'pronto';
$action_prefix = $filter['_action_prefix'] ? $filter['_action_prefix'] : '/admin';
$method = $filter['_method'] ? $filter['_method'] : 'get';

?><form class="form <?= $class ?>" action="<?= $action_prefix . $filter['_action'] ?>" method="<?= $method ?>">
    <div class="row">
      <?php if($filter): ?>
        <div class="form-group col-md-6 col-xs-11">
            <?php foreach($filter as $key => $value):
                if($key[0] === '_') continue;
            ?>
                <input type="text" class="form-control" name="<?= $key ?>" placeholder="<?= $value ?>" value="<?= $this->ee($method === 'get' ? $this->get_query($key) : $this->get_post($key)) ?>">
            <?php endforeach ?>
        </div>

          <div class="form-group col-xs-1" style="padding-left:0">
            <button type="submit" class="btn btn-cyan" title="<?= $this->text('regular-search') ?>"><i class="fa fa-search"></i></button>
        </div>
      <?php endif ?>

    <div class="col-md-5 col-xs-12">
        <?= $this->supply('admin-search-box-addons') ?>
    </div>
    </div>
</form>
