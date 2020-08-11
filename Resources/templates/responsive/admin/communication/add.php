<?php

$this->layout('admin/communication/layout');

$this->section('admin-container-head');

$data = $this->data;
$translator = $this->translator;
$langs = array_diff_key($this->languages, $this->translations);
$langs_available = $this->langs_available;
$languages = [$translator->original => $translator->original_name]+ array_diff_key($this->languages, [$translator->original => $translator->original_name]);
if($data) $image = $data->getImage();
$dataProjects = ($data)? $data->getCommunicationProjects($data->id) : '';
?>

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
                  <option value=<?= $filter->id?> <?= ($data->filter == $filter->id) ? 'selected="selected"' : "" ?>  > <?= $filter->name?>  </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-xs-2 col-md-1">
        <a id="filter-edit" class="btn btn-cyan fa fa-pencil"></a>
      </div>
      
      <div class="col-xs-2 col-md-1">
        <a id="filter-create" href="/admin/filter/add" class="btn btn-cyan fa fa-plus"></a>
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
        <option value="<?= $id ?>"  <?= ($data->template == $id) ? 'selected="selected"' : "" ?> > <?= $name ?> </option>
      <?php endforeach ?>
  </select>
</div>

<div class="form-group">
  <label for="text"> <?= $this->text('admin-text-type') ?> </label>
  <div class="input-wrap">
    <select id="text" class="form-control" name="autoform[data-editor-type]" required>
      <?php foreach($this->editor_types as $id => $name) : ?>
        <option value="<?= $id ?>"  <?= ($data->type == $id) ? 'selected="selected"' : "" ?> > <?= $name ?> </option>
      <?php endforeach ?>
    </select>
  </div>
</div>

<div id="dropzone-image" class="form-group hidden">
  <label for="image"> <?= $this->text('admin-title-header-image') ?> </label>
  <div class="input-wrap">
    <div class="image-zone image">
    <ul class="list-inline image-list-sortable">
      <li data-name="">
        <?php if($image): ?>
          <div class="image <?=$image ? ' file-type-' .$image->getType() : '' ?>" <?= $image->getLink() ? 'style="background-image:url(' . $image->getLink(300,300,true) . ');background-size:cover"' : '' ?>></div>
          <div class="options">
          <a class="delete-image btn btn-default"><i class="fa fa-trash" title="<?= $this->text('dashboard-project-delete-image') ?>"></i></a>
        </div>
        <?php endif ?>
      </li>
    </ul>
    <input id="image-upload" type="hidden" name="autoform[image]" value="<?= ($image)? $image->getName() : null ?>" required>
      <div class="dragndrop <?=($image)? 'hidden' : '' ?>"><div class="dropzone">
      </div></div>
    </div>
  </div>
</div>

<div class="form-group">
  <label for="promotes"> <?= $this->text('translator-promote') ?> </label>
  <div class="input-wrap">
    <?= $this->insert('admin/partials/typeahead', ['id' => 'communication_add', 'field' => 'name', 'type' => 'multiple', 'engines' => ['project'], 'defaults' => ['project'], 'extra' => [], 'hidden' => 'hidden', 'data' => $dataProjects]) ?>
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
  <li role="tab" <?= $default_lang == $lang ? ' class="active"' : '' ?>><a class="text-success" title="<?= $this->text('translator-text') ?>" href="?hl=<?= $lang ?>" data-target="#t-<?= $lang ?>" data-toggle="tab"><?= $name ?></a></li>
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
      <input class="form-control editor" id="i-<?= $lang ?>-subject" name="t[<?= $lang ?>][subject]" <?php if($data->id): ?> value="<?= $this->ee($translator->getTranslation($lang, 'subject', true)) ?> <?php endif ?>"> 
    </div>
    <div class="form-group">
      <label for="i-<?= $lang ?>-body"> <?= $this->text('admin-mail-body') ?></label>
        <textarea rows="10" class="form-control editor" id="i-<?= $lang ?>-body" name="t[<?= $lang ?>][body]" <?= $default_lang != $lang ? ' class="hidden"' : '' ?> ><?php if($data->id): ?><?= $this->ee($translator->getTranslation($lang, 'content', true)) ?><?php endif ?></textarea>
    </div>

    
  </div>
<?php endforeach ?>

<?php if ($data->isActive() || $data->isSent()): ?> 
  <a class="btn btn-cyan" href="/admin/communication/detail/<?= $data->id ?>"> <?= $this->text('form-next-button') ?> </a>
<?php else: ?>
  <button type="submit" class="btn btn-cyan" name="save"><?= $this->text('regular-save') ?></button>
<?php endif; ?>

</div>
</form>


  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
  Dropzone.autoDiscover = false;

  $('.image-zone').each(function(){
    var $zone = $(this);
    var $error = $zone.next();
    var $list = $(this).find('.image-list-sortable');
    var element = $zone.find('.dragndrop>div').get(0);

    var dropzone = new Dropzone(element, {
        url: '/api/communication/images',
        uploadMultiple: false,
        createImageThumbnails: true,
        maxFiles:10,
        maxFilesize: MAX_FILE_SIZE,
        autoProcessQueue: true,
        dictDefaultMessage: '<i style="font-size:2em" class="fa fa-plus"></i><br><br><?= $this->ee($this->text('dashboard-project-dnd-image'), 'js') ?>'
    });
    dropzone.on('error', function(file, error) {
        $error.html(error.error);
        $error.removeClass('hidden');
        console.log('error', error);
    });
    dropzone.on("queuecomplete", function (progress) {
      document.querySelector("#total-progress").style.opacity = "0.0";
    });
    dropzone.on('addedfile', function(file, response) {
        console.log(this.hiddenFileInput.files);
    });
    dropzone.on('success', function(file, response) {
        $error.addClass('hidden');
        // see if all files are uploaded ok in response
        if(response && !response.success) {
            $error.html(response.msg);
            $error.removeClass('hidden');
            for(var i in response.files) {
                if(!response.files[i].success)
                    $error.append('<br>' + response.files[i].msg);
            }
            return;
        }
        // Add to list
        var li = '<?= $this->ee($this->insert('admin/communication/partials/image_list_item', ['image_url' => '{URL}', 'image_name' => '{NAME}']), 'js') ?>';
        var name = file.name;
        for(var i in response.files) {
            if(response.files[i].originalName == name) {
                name = response.files[i].name;
            }
        }

        var img = '/img/300x300c/' + name;
        document.getElementById('image-upload').value = name;
        li = li.replace('{URL}', img);
        li = li.replace('{NAME}', name);
        $list.append(li);
        if(response.cover) {
            $('#menu-item-images').removeClass('ko').addClass('ok');
        }
        console.log('success', file, response, li);
      });
      dropzone.on("complete", function(file) {
          dropzone.removeFile(file);
          $zone.find('.dragndrop').addClass('hidden');
      });
      dropzone.on("sending", function(file, xhr, formData) {
        // Will send the section value along with the file as POST data.
        // formData.append("section", $zone.data('section'));
        // formData.append("add_to_gallery", 'project_image');
      });
  });

  // Delete actions
  $('.image-list-sortable').on( 'click', '.delete-image', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var $li = $(this).closest('li');
      // console.log('remove', $li);
      var $drop = $(this).closest('.dropfiles');
      var $zone = $(this).closest('.image-zone');
      var $list = $(this).closest('.image-list-sortable');
      var $error = $zone.next();
      $li.remove();
      $error.addClass('hidden');
      var total = $list.find('li').length;
      document.getElementById('image-upload').value = "";
      $zone.find('.dragndrop').removeClass('hidden');
  });
});


// @license-end
</script>

<?php $this->append() ?>
