<?php

$this->layout('admin/communication/layout');

$this->section('admin-container-head');

$langs = array_diff_key($this->languages, $this->translations);

?>
<div id="alert-success" class="new-material-success alert alert-success" style='display: none' >
  <strong class="msg"><?= $this->text('form-sent-success') ?></strong>
</div>

<div class="input_wrap">
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
</div>

<?php $this->append() ?>


<?php $this->section('admin-container-body') ?>


<div id="filter-form" class="hidden form-group">

  <?= $this->form_form($this->raw('form_filter')) ?>

  <button id="form_submit" type="submit" class="btn btn-cyan btn-lg" name="send">
    <i class="fa fa-save"></i> <?= $this->text('regular-submit') ?> 
  </button>

  <button id="form_close" type="submit" class="btn btn-cyan btn-lg">
    <i class="fa fa-ban"></i> <?= $this->text('regular-cancel') ?> 
  </button>
</div>


<form action="/communication" class="autoform" method="post">

<div class="form-group">
  <label for="templates"> <?= $this->text('regular-template') ?> </label>
  <div class="input-wrap">
    <select id="templates" class="form-control">
      <option selected disabled hidden></option>
      <?php foreach($this->templates as $id => $name) : ?>
        <option value="<?= $id ?>" > <?= $name ?> </option>
      <?php endforeach ?>
  </select>
</div>

<div class="form-group">
  <label for="text"> <?= $this->text('admin-text-type') ?> </label>
  <div class="input-wrap">
    <select id="text" class="form-control" name="autoform[data-editor-type]">
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

</div>
</form>


  </div>
</div>

<?php $this->replace() ?>