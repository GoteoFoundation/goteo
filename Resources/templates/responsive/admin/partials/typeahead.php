<?php

$defaults = [];
$extra = [];
$engines = $this->a('engines');
if(empty($engines)) $engines = ['channel', 'call', 'matcher', 'project', 'user', 'consultant'];
foreach($engines as $q) {
    if($this->has_query($q)) $defaults[] = $q;
}

if(empty($defaults)) $defaults = $this->a('defaults');
if(empty($defaults)) $defaults = ['call', 'channel', 'matcher'];
$defaults = array_intersect($defaults, $engines);
$extra = $this->a('extra');
$value = $this->text;
$data = $this->data;
$id = $this->id;
$field = ($this->field) ? $this->field : 'id';
$type = ($this->type == "multiple") ? $this->type : 'simple';
$hidden = ($this->hidden) ? $this->hidden : '';
?>

<div class="admin-typeahead" data-sources="<?= implode(',', $defaults) ?>" data-extra-params="<?= $this->ee(json_encode($extra)) ?>" data-value-field="<?= $field ?>">
  <div class="form-group has-feedback<?= ($value ? ' has-error' : '') ?>">

    <?php if ($type == "multiple") : ?>
      <input class="typeahead form-control"  data-type="<?= $type ?>" data-real-id="<?= $id ?>" autocomplete="off"  type="text" placeholder="<?= $this->placeholder ?: $this->text('admin-search-global') ?>" value="">
      <input id="<?= $id ?>" name="<?= $id ?>[]" type="hidden">
      <?php if ($data) : ?>
        <?php foreach($data as $obj) : ?>
          <input id="<?= $id ?>" name="<?= $id ?>[]" type="hidden" value="<?= $obj->id ?>">
        <?php endforeach ?> 
      <?php endif ?>

    <?php else : ?>
      <input class="typeahead form-control" data-type="<?= $type ?>" autocomplete="off" type="text" placeholder="<?= $this->placeholder ?: $this->text('admin-search-global') ?>" value="<?= $value ?>">
    <?php endif ?>

    <span class="fa fa-search form-control-feedback" aria-hidden="true"></span>
    <span class="help-block pull-left">
      <?php foreach($engines as $k): ?>
        <label <?= $hidden ?>><input type="checkbox" autocomplete="off"  name="<?= $k ?>"<?= in_array($k, $defaults) ? ' checked' : '' ?>> <?= $this->text('admin-' . $k . 's') ?></label>
      <?php endforeach ?>
    </span>
  
    <?php if ($type == "multiple") : ?>

      <div id="<?= $id ?>" class="bootstrap-tagsinput help-text">
        <?php if ($data && is_array($data)) : ?>
            <?php foreach($data as $obj) : ?>
            <div>
            <span class="tag label label-lilac"> <?= $obj->name ?>
                <span id="remove-<?= $obj->id ?>-<?=$id?>" data-real-id="<?= $id ?>" data-value="<?= $obj->id ?>" data-role="remove"></span>
            </span></div>
            <?php endforeach ?>
        <?php endif ?>
      </div>
      
    <?php endif ?>

    <?php if($value) {
      echo '<span class="help-block text-right"><a href="' . $this->get_pathinfo() . '" class="pronto text-danger"><i class="fa fa-close"></i> ' . $this->text('admin-remove-filters') . '</a> &nbsp;</span>';
    } ?>
  </div>
</div>