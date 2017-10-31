<?php $method = strtoupper($method) ?>
<?php $form_method = $method === 'GET' || $method === 'POST' ? $method : 'POST' ?>
<form name="<?php echo $name ?>" data-confirm="<?php echo $view->escape(false !== $translation_domain ? $view['translator']->trans('form-confirm-exit', array(), $translation_domain) : 'form-confirm-exit') ?>" method="<?php echo strtolower($form_method) ?>"<?php if ($action !== ''): ?> action="<?php echo $action ?>"<?php endif ?><?php foreach ($attr as $k => $v) { printf(' %s="%s"', $view->escape($k), $view->escape($v)); } ?><?php if ($multipart): ?> enctype="multipart/form-data"<?php endif ?>>
<?php if ($form_method !== $method): ?>
    <input type="hidden" name="_method" value="<?php echo $method ?>" />
<?php endif ?>

<script class="dropfile_item_template" type="text/template">
<?php
// Template for dropfilesjs (javascript use)
echo $view->render('bootstrap/dropfiles_item.html.php', ['file_name' => '{NAME}']);
?>
</script>

<div class="modal modal-map fade" id="modal-map-<?= $name?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Map</h4>
        <div><?php echo $view->escape(false !== $translation_domain ? $view['translator']->trans('form-editor-fine-locate', array(), $translation_domain) : 'form-editor-fine-locate') ?></div>
        <div id="geo-autocomplete-error-<?= $name ?>" class="text-danger error-msg"><?php echo $view->escape(false !== $translation_domain ? $view['translator']->trans('form-editor-error-locating', array(), $translation_domain) : 'form-editor-error-locating') ?></div>
        <div id="geo-autocomplete-success-<?= $name ?>" class="text-success success-msg"><?php echo $view->escape(false !== $translation_domain ? $view['translator']->trans('form-editor-success-locating', array(), $translation_domain) : 'form-editor-success-locating') ?></div>
      </div>
      <div class="modal-body">
        <div class="input-block">
          <div class="form-group form-group-search">
            <input type="text" id="geo-autocomplete-<?= $name ?>" onfocus="this.select()" class="form-control geo-autocomplete" name="address" value="">
          </div>
          <div class="form-group form-group-radius">R: <input type="number" id="geo-autocomplete-radius-<?= $name ?>" onfocus="this.select()" class="form-control geo-autocomplete-radius" name="radius" value="">Km</div>
        </div>
        <div id="geo-map-<?= $name ?>" data-autocomplete-target="#geo-autocomplete-<?= $name ?>" data-autocomplete-error="#geo-autocomplete-error-<?= $name ?>" data-autocomplete-success="#geo-autocomplete-success-<?= $name ?>" class="map"></div>

      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
