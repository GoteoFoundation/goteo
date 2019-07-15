<?php

$this->layout('admin/communication/layout');

$this->section('admin-container-head');

$langs = array_diff_key($this->languages, $this->translations);

?>

<!-- <div class="input_wrap">
    <div class="row">
      <div class="form-group col-xs-12 col-md-6">
        <select id="filter-select" class="form-control">
          <option value="0" selected disabled hidden><?= $this->text('admin-filters') ?></option>
          <?php foreach ($this->filters as $filter) : ?>
                  <option value=<?= $filter->id?> > <?= $filter->name?>  </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-xs-4 col-md-2">
        <button id="filter-edit" class="btn btn-cyan"><?= $this->text('admin-filters-edit') ?></button>
      </div>
      <div class="col-xs-4 col-md-2">
        <button id="filter-create" class="btn btn-cyan"> <?= $this->text('admin-filters-create') ?></button>
      </div>
    </div>
</div> -->

<?php $this->append() ?>


<?php $this->section('admin-container-body') ?>

<form class="autoform" method="post">

<div class="form-group">
  <label for="filters"> <?= $this->text('admin-filters') ?> </label>
  <div class="input-wrap">
    <div class="row">
      <div class="form-group col-xs-8 col-md-10">
        <select id="filter-select" class="form-control" name="autoform[filter]" required>
          <option value="0" selected disabled hidden><?= $this->text('admin-filters') ?></option>
          <?php foreach ($this->filters as $filter) : ?>
                  <option value=<?= $filter->id?> > <?= $filter->name?>  </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-xs-2 col-md-1">
        <a id="filter-edit" class="btn btn-cyan fa fa-pencil"></a>
      </div>
      
      <div class="col-xs-2 col-md-1">
        <a id="filter-create" href="filter/add" class="btn btn-cyan fa fa-plus"></a>
      </div>
    </div>
  </div>
</div>

<div class="form-group">
  <label for="templates"> <?= $this->text('regular-template') ?> </label>
  <div class="input-wrap">
    <select id="templates" class="form-control" name="autoform[template]" required>
      <option selected disabled hidden></option>
      <?php foreach($this->templates as $id => $name) : ?>
        <option value="<?= $id ?>" > <?= $name ?> </option>
      <?php endforeach ?>
  </select>
</div>

<div class="form-group">
  <label for="langs"> <?= $this->text('overview-field-lang') ?> </label>
  <div class="input-wrap">
    <select id="lang" class="form-control" name="autoform[original-lang]" required>
      <?php foreach($this->translations as $lang => $name) : ?>
        <option value="<?= $lang ?>" > <?= $name ?> </option>
      <?php endforeach ?>
    </select>
  </div>
</div>

<div class="form-group">
  <label for="text"> <?= $this->text('admin-text-type') ?> </label>
  <div class="input-wrap">
    <select id="text" class="form-control" name="autoform[data-editor-type]" required>
      <?php foreach($this->editor_types as $id => $name) : ?>
        <option value="<?= $id ?>" > <?= $name ?> </option>
      <?php endforeach ?>
    </select>
  </div>
</div>

<div id="dropzone-image" class="form-group hidden">
  <label for="image"> <?= $this->text('admin-title-header-image') ?> </label>
  <div class="image-zone">
    <div class="dragndrop"><div class="dropzone"></div></div>
  </div>
</div>

<div class="form-group">
<section role="list" class="input-wrap">
  <?php foreach($this->variables as $var => $content) : ?>
    <div role="listitem" class="raw"> <?= $this->text('admin-communications-'.$var.'-label') . $content ?> </div>
  <?php endforeach ?>
</section>
</div>

<ul class="nav nav-tabs">
<?php
    foreach($this->translations as $lang => $name):
      if(!$default_lang) $default_lang = $lang;
?>
  <li role="tab" <?= $default_lang == $lang ? ' class="active"' : '' ?>><a class="text-success" title="<?= $this->text('translator-original-text') ?>" href="?hl=<?= $lang ?>" data-target="#t-<?= $lang ?>" data-toggle="tab"><?= $name ?></a></li>
<?php endforeach ?>
  <li>
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?= $this->text('translator-more-langs') ?> <span class="caret"></span></a>
        <ul class="dropdown-menu">
<?php
  foreach($langs as $lang => $name):
?>
        <li role="tab" <?= $default_lang == $lang ? ' class="active"' : '' ?>><a class="text-muted" title="<?= $this->text('translator-not-translated') ?>" href="?hl=<?= $lang ?>" data-target="#t-<?= $lang ?>" data-toggle="tab"><?= $name ?></a></li>
<?php endforeach ?>
        </ul>
  </li>
</ul>
<br>

<div class="tab-content">
<?php foreach($this->languages as $lang => $name): 
  ?>
  <div role="tabpanel" class="tab-pane<?= $default_lang == $lang ? ' active' : '' ?>" id="t-<?= $lang ?>">

    <div class="form-group">
      <label for="i-<?= $lang ?>-subject"> <?= $this->text('admin-mailing-subject') ?></label>
      <input class="form-control editor" id="i-<?= $lang ?>-subject" name="t[<?= $lang ?>][subject]">
    </div>
    <div class="form-group">
      <label for="i-<?= $lang ?>-body"> <?= $this->text('admin-mail-body') ?></label>
        <textarea rows="10" class="form-control editor" id="i-<?= $lang ?>-body" name="t[<?= $lang ?>][body]" <?= $default_lang != $lang ? ' class="hidden"' : '' ?> ></textarea>
    </div>

    
  </div>
<?php endforeach ?>

<button type="submit" class="btn btn-cyan" name="save"><?= $this->text('form-next-button') ?></button>
<button type="button" class="btn btn-cyan" name="preview"><?= $this->text('regular-preview') ?></button>

</div>
</form>


  </div>
</div>

<?php $this->replace() ?>