<?php
$all = ['call','channel','project','user'];
$defaults = ['call','channel'];
?>
<div class="admin-typeahead" data-sources="<?= implode(',', $defaults) ?>">
  <div class="form-group has-feedback<?= ($this->value ? ' has-warning' : '') ?>">

    <input class="typeahead form-control" autocomplete="off" type="text" placeholder="<?= $this->placeholder ?: $this->text('admin-search-global') ?>" value="<?= $this->value ?>">
    <span class="fa fa-search form-control-feedback" aria-hidden="true"></span>
    <span class="help-block pull-left">
      <?php foreach($all as $k): ?>
        <label><input type="checkbox" autocomplete="off" name="<?= $k ?>"<?= in_array($k, $defaults) ? ' checked' : '' ?>> <?= $this->text('admin-' . $k . 's') ?></label>
      <?php endforeach ?>
    </span>

    <?php if($this->value) {
      echo '<span class="help-block text-right"><a href="' . $this->get_pathinfo() . '" class="pronto text-danger"><i class="fa fa-close"></i> ' . $this->text('admin-remove-filters') . '</a> &nbsp;</span>';
    } ?>
  </div>
</div>
