<?php

$defaults = [];
$engines = $this->a('engines');
if(empty($engines)) $engines = ['channel', 'call', 'matcher', 'project', 'user', 'consultant'];
foreach($engines as $q) {
    if($this->has_query($q)) $defaults[] = $q;
}

if(empty($defaults)) $defaults = $this->a('defaults');
if(empty($defaults)) $defaults = ['call', 'channel', 'matcher'];
$defaults = array_intersect($defaults, $engines);
$value = $this->text;

?>
<div class="admin-typeahead" data-sources="<?= implode(',', $defaults) ?>">
  <div class="form-group has-feedback<?= ($value ? ' has-error' : '') ?>">

    <input class="typeahead form-control" autocomplete="off" type="text" placeholder="<?= $this->placeholder ?: $this->text('admin-search-global') ?>" value="<?= $value ?>">
    <span class="fa fa-search form-control-feedback" aria-hidden="true"></span>
    <span class="help-block pull-left">
      <?php foreach($engines as $k): ?>
        <label><input type="checkbox" autocomplete="off" name="<?= $k ?>"<?= in_array($k, $defaults) ? ' checked' : '' ?>> <?= $this->text('admin-' . $k . 's') ?></label>
      <?php endforeach ?>
    </span>

    <?php if($value) {
      echo '<span class="help-block text-right"><a href="' . $this->get_pathinfo() . '" class="pronto text-danger"><i class="fa fa-close"></i> ' . $this->text('admin-remove-filters') . '</a> &nbsp;</span>';
    } ?>
  </div>
</div>
